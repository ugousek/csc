<?php
/**
 * Ecomail integration – subscribe contacts after free registration.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * AJAX handler: subscribe contact to Ecomail list.
 *
 * Expected POST fields: jmeno, prijmeni, email, telefon, firma, skoleni_id
 * The Ecomail list ID comes from ACF field 'ecomail_list_id' on the skoleni post.
 */
add_action( 'wp_ajax_nopriv_xevos_ecomail_register', 'xevos_ecomail_register' );
add_action( 'wp_ajax_xevos_ecomail_register', 'xevos_ecomail_register' );

function xevos_ecomail_register(): void {
	// Verify nonce.
	if ( ! isset( $_POST['xevos_order_nonce'] ) || ! wp_verify_nonce( $_POST['xevos_order_nonce'], 'xevos_order' ) ) {
		wp_send_json_error( [ 'message' => 'Neplatný bezpečnostní token.' ], 403 );
	}

	// Honeypot check.
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success( [ 'message' => 'Registrace proběhla úspěšně.' ] ); // Silent.
	}

	// Time-based check — bot submits instantly.
	if ( function_exists( 'xevos_check_form_time' ) && ! xevos_check_form_time( 3 ) ) {
		wp_send_json_error( [ 'message' => 'Příliš rychlé odeslání. Zkuste to znovu.' ], 403 );
	}

	// Rate limit — max 5 submissions per IP per 10 minutes.
	if ( function_exists( 'xevos_check_rate_limit' ) && ! xevos_check_rate_limit( 'register' ) ) {
		wp_send_json_error( [ 'message' => 'Příliš mnoho pokusů. Zkuste to za chvíli.' ], 429 );
	}

	// Turnstile (only when enabled in settings).
	if ( function_exists( 'xevos_turnstile_verify' ) && ! xevos_turnstile_verify() ) {
		wp_send_json_error( [ 'message' => 'Ověření selhalo. Zkuste to znovu.' ], 403 );
	}


	$skoleni_id = (int) ( $_POST['skoleni_id'] ?? 0 );

	$jmeno    = sanitize_text_field( $_POST['jmeno'] ?? '' );
	$prijmeni = sanitize_text_field( $_POST['prijmeni'] ?? '' );
	$email    = sanitize_email( $_POST['email'] ?? '' );
	$telefon  = sanitize_text_field( $_POST['telefon'] ?? '' );
	$firma    = sanitize_text_field( $_POST['firma'] ?? '' );
	$pocet      = max( 1, (int) ( $_POST['pocet'] ?? 1 ) );
	$forma      = sanitize_text_field( $_POST['forma'] ?? '' );
	$platce_dph = ! empty( $_POST['platce_dph'] );

	if ( ! $email ) {
		wp_send_json_error( [ 'message' => 'E-mail je povinný.' ], 400 );
	}

	// Duplicate check — same email + same školení (exclude cancelled).
	$existing = get_posts( [
		'post_type'      => 'objednavka',
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'meta_query'     => [
			'relation' => 'AND',
			[
				'key'   => 'email',
				'value' => $email,
			],
			[
				'key'   => 'skoleni',
				'value' => $skoleni_id,
			],
			[
				'key'     => 'stav_platby',
				'value'   => 'cancelled',
				'compare' => '!=',
			],
		],
	] );

	if ( ! empty( $existing ) ) {
		wp_send_json_error( [ 'message' => 'Tento e-mail je na toto školení již registrován.' ], 409 );
	}

	// Capacity check — count real active orders for this term.
	$termin_val = sanitize_text_field( wp_unslash( $_POST['termin'] ?? '' ) );
	if ( $termin_val ) {
		$terminy = get_field( 'terminy', $skoleni_id );
		if ( is_array( $terminy ) ) {
			foreach ( $terminy as $t ) {
				if ( xevos_termin_key( $t ) === $termin_val ) {
					$kapacita   = (int) ( $t['kapacita'] ?? 0 );
					$registrace = xevos_count_active_registrations( $skoleni_id, $termin_val );
					if ( $kapacita > 0 && $registrace >= $kapacita ) {
						wp_send_json_error( [ 'message' => 'Kapacita tohoto termínu je bohužel naplněna.' ], 409 );
					}
					break;
				}
			}
		}
	}

	// Ecomail subscribe — odesílá všechna vyplněná pole + custom_fields.
	$typ = sanitize_text_field( $_POST['typ_prihlaseni'] ?? 'zdarma' );
	xevos_ecomail_subscribe_order( $skoleni_id, [
		'jmeno'          => $jmeno,
		'prijmeni'       => $prijmeni,
		'email'          => $email,
		'telefon'        => $telefon,
		'firma'          => $firma,
		'skoleni_title'  => get_the_title( $skoleni_id ),
		'termin'         => function_exists( 'xevos_format_termin_display' )
			? xevos_format_termin_display( (string) ( $_POST['termin'] ?? '' ), (int) $skoleni_id )
			: sanitize_text_field( $_POST['termin'] ?? '' ),
		'typ_registrace' => 'free',
		'typ_prihlaseni' => $typ,
		'castka'         => '0',
		'pocet'          => $pocet,
		'forma'          => $forma,
		'platce_dph'     => $platce_dph,
	] );

	// Create an order CPT record for tracking.
	$order_title = $typ === 'pozvanka' ? 'Žádost o pozvánku' : 'Registrace zdarma';

	// Pozvánky i volné registrace se uloží jako publish — odlišuje je stav_platby
	// (pozvánka = "pending", volná registrace = "registered"). Díky tomu jsou viditelné
	// v seznamu objednávek i v metaboxu Registrace na detailu školení.
	$order_id = wp_insert_post( [
		'post_type'   => 'objednavka',
		'post_status' => 'publish',
		'post_title'  => $order_title,
	] );

	$termin_val = sanitize_text_field( wp_unslash( $_POST['termin'] ?? '' ) );

	if ( $order_id && ! is_wp_error( $order_id ) ) {
		update_field( 'jmeno', $jmeno, $order_id );
		update_field( 'prijmeni', $prijmeni, $order_id );
		update_field( 'email', $email, $order_id );
		update_field( 'telefon', $telefon, $order_id );
		update_field( 'firma', $firma, $order_id );
		update_field( 'skoleni', $skoleni_id, $order_id );
		update_field( 'termin', $termin_val, $order_id );
		update_field( 'pocet', $pocet, $order_id );
		update_field( 'forma', $forma, $order_id );
		update_field( 'platce_dph', $platce_dph, $order_id );
		update_field( 'castka', 0, $order_id );
		update_field( 'typ_registrace', 'free', $order_id );
		update_field( 'stav_platby', $typ === 'pozvanka' ? 'invitation_pending' : 'registered', $order_id );
		update_field( 'datum_objednavky', date( 'd.m.Y' ), $order_id );

		// Check capacity thresholds and send admin notifications.
		if ( ! empty( $termin_val ) ) {
			$terminy = get_field( 'terminy', $skoleni_id );
			if ( is_array( $terminy ) ) {
				foreach ( $terminy as $i => $t ) {
					if ( xevos_termin_key( $t ) === $termin_val ) {
						xevos_increment_registration( $skoleni_id, $i );
						break;
					}
				}
			}
		}
	}

	// Send confirmation email to the customer.
	if ( $email && function_exists( 'xevos_send_email' ) ) {
		$skoleni_title     = get_the_title( $skoleni_id );
		$termin_str        = function_exists( 'xevos_format_termin_display' )
			? xevos_format_termin_display( (string) $termin_val, (int) $skoleni_id )
			: $termin_val;
		$firma_nazev       = function_exists( 'xevos_get_option' ) ? xevos_get_option( 'nazev_firmy', 'XEVOS' ) : 'XEVOS';
		$skoleni_url       = get_permalink( $skoleni_id ) ?: '';
		$skoleni_admin_url = get_edit_post_link( $skoleni_id, 'raw' ) ?: '';

		if ( $typ === 'pozvanka' ) {
			xevos_send_email( $email, 'Žádost o pozvánku – ' . $skoleni_title, 'invitation-request', [
				'jmeno'         => $jmeno,
				'nazev_skoleni' => $skoleni_title,
				'termin'        => $termin_str,
				'skoleni_url'   => $skoleni_url,
				'kontakt_email' => get_option( 'admin_email' ),
				'firma'         => $firma_nazev,
			] );
		} else {
			xevos_send_email( $email, 'Potvrzení registrace – ' . $skoleni_title, 'registration-confirmation', [
				'jmeno'         => $jmeno,
				'nazev_skoleni' => $skoleni_title,
				'termin'        => $termin_str,
				'misto'         => '',
				'skoleni_url'   => $skoleni_url,
				'kontakt_email' => get_option( 'admin_email' ),
				'firma'         => $firma_nazev,
			] );
		}

		// Notify admin about free registration / invitation request.
		xevos_send_email( get_option( 'admin_email' ), ( $typ === 'pozvanka' ? 'Nová žádost o pozvánku: ' : 'Nová registrace: ' ) . $skoleni_title, 'admin-notification-free', [
			'typ'              => $typ === 'pozvanka' ? 'Žádost o pozvánku' : 'Registrace zdarma',
			'jmeno'            => $jmeno,
			'prijmeni'         => $prijmeni,
			'email'            => $email,
			'telefon'          => $telefon,
			'firma_nazev'      => $firma,
			'nazev_skoleni'    => $skoleni_title,
			'termin'           => $termin_str,
			'admin_url'        => $order_id ? admin_url( 'post.php?post=' . $order_id . '&action=edit' ) : '',
			'skoleni_url'      => $skoleni_url,
			'skoleni_admin_url' => $skoleni_admin_url,
		] );
	}

	$success_msg = $typ === 'pozvanka'
		? 'Žádost o pozvánku byla odeslána. Ozveme se vám.'
		: 'Registrace proběhla úspěšně.';

	wp_send_json_success( [ 'message' => $success_msg ] );
}

/**
 * Subscribe a contact to an Ecomail list via API.
 *
 * @param string $api_key Ecomail API key.
 * @param string $list_id Ecomail list ID.
 * @param array  $contact Subscriber data (email, name, surname, phone, company,
 *                        street, city, zip, country, custom_fields [key=>value]).
 * @return string|false API response or false on failure.
 */
function xevos_ecomail_subscribe( string $api_key, string $list_id, array $contact ) {
	$url = 'https://api2.ecomailapp.cz/lists/' . urlencode( $list_id ) . '/subscribe';

	$body = [
		'subscriber_data'        => $contact,
		'trigger_autoresponders' => true,
		'update_existing'        => true,
		'resubscribe'            => true,
	];

	$response = wp_remote_post( $url, [
		'headers' => [
			'Content-Type' => 'application/json',
			'key'          => $api_key,
		],
		'body'    => wp_json_encode( $body ),
		'timeout' => 15,
	] );

	if ( is_wp_error( $response ) ) {
		error_log( 'Ecomail API error: ' . $response->get_error_message() );
		return false;
	}

	$code = wp_remote_retrieve_response_code( $response );
	if ( $code < 200 || $code >= 300 ) {
		error_log( 'Ecomail API HTTP ' . $code . ': ' . wp_remote_retrieve_body( $response ) );
		return false;
	}

	return wp_remote_retrieve_body( $response );
}

/**
 * Build an Ecomail subscriber_data array from flat form data.
 *
 * Maps known order/registration fields to Ecomail standard fields and
 * categorizes the subscriber via tags (auto-created by Ecomail — no manual
 * setup required). Empty values are skipped so we don't overwrite existing
 * Ecomail data.
 *
 * @param array $data Flat form/order data — expected keys:
 *                    jmeno, prijmeni, email, telefon, firma, ico, dic,
 *                    ulice, mesto, psc, skoleni_title, termin, typ_registrace,
 *                    cislo_objednavky, castka, typ_prihlaseni.
 * @return array      Ecomail-ready subscriber_data.
 */
function xevos_ecomail_build_contact( array $data ): array {
	$contact = [];

	// Standardní Ecomail pole.
	$map_standard = [
		'email'    => 'email',
		'jmeno'    => 'name',
		'prijmeni' => 'surname',
		'telefon'  => 'phone',
		'firma'    => 'company',
		'ulice'    => 'street',
		'mesto'    => 'city',
		'psc'      => 'zip',
	];

	foreach ( $map_standard as $src => $dst ) {
		if ( ! empty( $data[ $src ] ) ) {
			$contact[ $dst ] = (string) $data[ $src ];
		}
	}

	if ( ! empty( $contact['zip'] ) ) {
		$contact['country'] = 'CZ';
	}

	// Tagy — auto-vytvoří se v Ecomailu, používáme pro kategorizaci / segmentaci.
	$tags = [];

	if ( ! empty( $data['typ_registrace'] ) ) {
		$tags[] = 'typ-registrace:' . sanitize_title( $data['typ_registrace'] );
	}

	if ( ! empty( $data['typ_prihlaseni'] ) ) {
		$tags[] = 'typ-platby:' . sanitize_title( $data['typ_prihlaseni'] );
	}

	if ( ! empty( $data['skoleni_title'] ) ) {
		$tags[] = 'skoleni:' . sanitize_title( $data['skoleni_title'] );
	}

	if ( ! empty( $data['forma'] ) ) {
		$tags[] = 'forma:' . sanitize_title( $data['forma'] );
	}

	if ( ! empty( $data['ico'] ) ) {
		$tags[] = 'b2b';
	}

	if ( ! empty( $data['platce_dph'] ) ) {
		$tags[] = 'platce-dph';
	}

	if ( isset( $data['pocet'] ) && (int) $data['pocet'] > 1 ) {
		$tags[] = 'pocet-vice';
	}

	if ( ! empty( $tags ) ) {
		$contact['tags'] = array_values( array_unique( $tags ) );
	}

	// Custom fields — strukturovaná data specifická pro objednávku.
	// Ecomail neznámá pole ignoruje, takže pokud je v listu vytvoříš,
	// naplní se; pokud ne, subscribe i tak proběhne bez chyby.
	$custom_map = [
		'ico'              => 'ico',
		'dic'              => 'dic',
		'skoleni_title'    => 'skoleni',
		'termin'           => 'termin',
		'cislo_objednavky' => 'cislo_objednavky',
		'castka'           => 'castka',
		'pocet'            => 'pocet',
		'forma'            => 'forma',
	];

	$custom = [];
	foreach ( $custom_map as $src => $dst ) {
		if ( isset( $data[ $src ] ) && $data[ $src ] !== '' && $data[ $src ] !== null ) {
			if ( is_scalar( $data[ $src ] ) ) {
				$custom[ $dst ] = (string) $data[ $src ];
			}
		}
	}

	if ( isset( $data['platce_dph'] ) ) {
		$custom['platce_dph'] = ! empty( $data['platce_dph'] ) ? '1' : '0';
	}

	if ( ! empty( $custom ) ) {
		$contact['custom_fields'] = $custom;
	}

	return $contact;
}

/**
 * Subscribe a contact to Ecomail from an order/registration flow.
 *
 * No-op when the skoleni has no ecomail_list_id, when the API key is not
 * configured, or when the email is missing. Always fails silently — the
 * order itself must not fail because Ecomail is down or misconfigured.
 *
 * @param int   $skoleni_id  Training post ID (list ID is on the skoleni post).
 * @param array $form_data   Flat form/order data (see xevos_ecomail_build_contact).
 * @return bool              True when subscribe was attempted, false when skipped.
 */
function xevos_ecomail_subscribe_order( int $skoleni_id, array $form_data ): bool {
	if ( empty( $form_data['email'] ) ) {
		return false;
	}

	$list_id = $skoleni_id ? get_field( 'ecomail_list_id', $skoleni_id ) : '';
	$api_key = function_exists( 'xevos_get_option' ) ? xevos_get_option( 'ecomail_api_key' ) : '';

	if ( ! $list_id || ! $api_key ) {
		return false;
	}

	$contact = xevos_ecomail_build_contact( $form_data );

	if ( empty( $contact['email'] ) ) {
		return false;
	}

	xevos_ecomail_subscribe( $api_key, $list_id, $contact );

	return true;
}

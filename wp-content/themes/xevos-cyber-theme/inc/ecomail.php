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
		wp_send_json_error( [ 'message' => 'Spam detekován.' ], 403 );
	}

	$skoleni_id = (int) ( $_POST['skoleni_id'] ?? 0 );
	$list_id    = get_field( 'ecomail_list_id', $skoleni_id );
	$api_key    = xevos_get_option( 'ecomail_api_key' );

	if ( ! $list_id ) {
		wp_send_json_error( [ 'message' => 'Ecomail List ID není vyplněno u tohoto školení.' ], 500 );
	}
	if ( ! $api_key ) {
		wp_send_json_error( [ 'message' => 'Ecomail API klíč není nastaven (Nastavení webu → Ecomail).' ], 500 );
	}

	$jmeno    = sanitize_text_field( $_POST['jmeno'] ?? '' );
	$prijmeni = sanitize_text_field( $_POST['prijmeni'] ?? '' );
	$email    = sanitize_email( $_POST['email'] ?? '' );
	$telefon  = sanitize_text_field( $_POST['telefon'] ?? '' );
	$firma    = sanitize_text_field( $_POST['firma'] ?? '' );

	if ( ! $email ) {
		wp_send_json_error( [ 'message' => 'E-mail je povinný.' ], 400 );
	}

	$result = xevos_ecomail_subscribe( $api_key, $list_id, [
		'name'    => $jmeno,
		'surname' => $prijmeni,
		'email'   => $email,
		'phone'   => $telefon,
		'company' => $firma,
	] );

	if ( $result === false ) {
		wp_send_json_error( [ 'message' => 'Nepodařilo se odeslat registraci.' ], 500 );
	}

	// Also create an order CPT record for tracking.
	$typ = sanitize_text_field( $_POST['typ_prihlaseni'] ?? 'zdarma' );
	$order_title = $typ === 'pozvanka' ? 'Žádost o pozvánku' : 'Registrace zdarma';
	$order_status = $typ === 'pozvanka' ? 'draft' : 'publish';

	$order_id = wp_insert_post( [
		'post_type'   => 'objednavka',
		'post_status' => $order_status,
		'post_title'  => $order_title,
	] );

	if ( $order_id && ! is_wp_error( $order_id ) ) {
		update_field( 'jmeno', $jmeno, $order_id );
		update_field( 'prijmeni', $prijmeni, $order_id );
		update_field( 'email', $email, $order_id );
		update_field( 'telefon', $telefon, $order_id );
		update_field( 'firma', $firma, $order_id );
		update_field( 'skoleni', $skoleni_id, $order_id );
		update_field( 'termin', sanitize_text_field( $_POST['termin'] ?? '' ), $order_id );
		update_field( 'castka', 0, $order_id );
		update_field( 'stav_platby', $typ === 'pozvanka' ? 'pending' : 'paid', $order_id );
		update_field( 'datum_objednavky', date( 'd.m.Y' ), $order_id );

		// Increment registration count for the chosen term.
		if ( ! empty( $_POST['termin'] ) ) {
			$terminy = get_field( 'terminy', $skoleni_id );
			if ( is_array( $terminy ) ) {
				foreach ( $terminy as $i => $t ) {
					if ( ( $t['datum'] ?? '' ) === $_POST['termin'] ) {
						xevos_increment_registration( $skoleni_id, $i );
						break;
					}
				}
			}
		}
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
 * @param array  $contact Contact data (name, surname, email, phone, company).
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

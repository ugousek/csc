<?php
/**
 * AJAX handlers: live search, archive filtering.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

// Live search.
add_action( 'wp_ajax_xevos_live_search', 'xevos_live_search_handler' );
add_action( 'wp_ajax_nopriv_xevos_live_search', 'xevos_live_search_handler' );

function xevos_live_search_handler(): void {
	check_ajax_referer( 'xevos_nonce', 'nonce' );

	$query = sanitize_text_field( wp_unslash( $_GET['query'] ?? '' ) );

	if ( strlen( $query ) < 3 ) {
		wp_send_json_success( [ 'results' => [] ] );
	}

	$results = [];

	$post_types = [
		'skoleni'   => __( 'Školení', 'xevos-cyber' ),
		'aktualita' => __( 'Aktuality', 'xevos-cyber' ),
		'page'      => __( 'Stránky', 'xevos-cyber' ),
	];

	foreach ( $post_types as $type => $label ) {
		$search_query = new WP_Query( [
			'post_type'      => $type,
			'post_status'    => 'publish',
			's'              => $query,
			'posts_per_page' => 5,
		] );

		if ( $search_query->have_posts() ) {
			$group = [
				'type'  => $label,
				'items' => [],
			];

			while ( $search_query->have_posts() ) {
				$search_query->the_post();
				$group['items'][] = [
					'title'   => get_the_title(),
					'url'     => get_the_permalink(),
					'excerpt' => wp_trim_words( get_the_excerpt(), 15 ),
				];
			}

			$results[] = $group;
		}

		wp_reset_postdata();
	}

	wp_send_json_success( [ 'results' => $results ] );
}

// Contact form.
add_action( 'wp_ajax_xevos_contact_form', 'xevos_contact_form_handler' );
add_action( 'wp_ajax_nopriv_xevos_contact_form', 'xevos_contact_form_handler' );

function xevos_contact_form_handler(): void {
	if ( ! isset( $_POST['xevos_contact_nonce'] ) || ! wp_verify_nonce( $_POST['xevos_contact_nonce'], 'xevos_contact' ) ) {
		wp_send_json_error( [ 'message' => 'Neplatný bezpečnostní token.' ], 403 );
	}

	// Honeypot.
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_error( [ 'message' => 'Spam detekován.' ], 403 );
	}

	$jmeno    = sanitize_text_field( $_POST['jmeno'] ?? '' );
	$prijmeni = sanitize_text_field( $_POST['prijmeni'] ?? '' );
	$email    = sanitize_email( $_POST['email'] ?? '' );
	$telefon  = sanitize_text_field( $_POST['telefon'] ?? '' );
	$zprava   = sanitize_textarea_field( $_POST['zprava'] ?? '' );

	if ( ! $jmeno || ! $email ) {
		wp_send_json_error( [ 'message' => 'Jméno a e-mail jsou povinné.' ] );
	}

	if ( ! is_email( $email ) ) {
		wp_send_json_error( [ 'message' => 'Zadejte prosím platný e-mail.' ] );
	}

	$firma_nazev = function_exists( 'xevos_get_option' ) ? xevos_get_option( 'nazev_firmy', 'XEVOS' ) : 'XEVOS';

	// Send notification to admin.
	if ( function_exists( 'xevos_send_email' ) ) {
		xevos_send_email( get_option( 'admin_email' ), 'Nová zpráva z kontaktního formuláře – ' . $jmeno . ' ' . $prijmeni, 'admin-notification-contact', [
			'jmeno'    => $jmeno,
			'prijmeni' => $prijmeni,
			'email'    => $email,
			'telefon'  => $telefon,
			'zprava'   => $zprava,
		] );

		// Confirm to the customer.
		xevos_send_email( $email, 'Děkujeme za Vaši zprávu – ' . $firma_nazev, 'contact-confirmation', [
			'jmeno'         => $jmeno,
			'kontakt_email' => get_option( 'admin_email' ),
			'firma'         => $firma_nazev,
		] );
	}

	wp_send_json_success( [ 'message' => 'Zpráva byla úspěšně odeslána. Děkujeme!' ] );
}

// Inquiry form (kybernetické testování).
add_action( 'wp_ajax_xevos_inquiry_form', 'xevos_inquiry_form_handler' );
add_action( 'wp_ajax_nopriv_xevos_inquiry_form', 'xevos_inquiry_form_handler' );

function xevos_inquiry_form_handler(): void {
	if ( ! isset( $_POST['xevos_inquiry_nonce'] ) || ! wp_verify_nonce( $_POST['xevos_inquiry_nonce'], 'xevos_inquiry' ) ) {
		wp_send_json_error( [ 'message' => 'Neplatný bezpečnostní token.' ], 403 );
	}

	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_error( [ 'message' => 'Spam detekován.' ], 403 );
	}

	$jmeno      = sanitize_text_field( $_POST['jmeno'] ?? '' );
	$prijmeni   = sanitize_text_field( $_POST['prijmeni'] ?? '' );
	$email      = sanitize_email( $_POST['email'] ?? '' );
	$telefon    = sanitize_text_field( $_POST['telefon'] ?? '' );
	$firma      = sanitize_text_field( $_POST['firma'] ?? '' );
	$druh_testu = sanitize_text_field( $_POST['druh_testu'] ?? '' );
	$zprava     = sanitize_textarea_field( $_POST['zprava'] ?? '' );

	if ( ! $jmeno || ! $prijmeni || ! $email || ! $telefon || ! $druh_testu ) {
		wp_send_json_error( [ 'message' => 'Vyplňte prosím všechna povinná pole.' ] );
	}

	if ( ! is_email( $email ) ) {
		wp_send_json_error( [ 'message' => 'Zadejte prosím platný e-mail.' ] );
	}

	$firma_nazev = function_exists( 'xevos_get_option' ) ? xevos_get_option( 'nazev_firmy', 'XEVOS' ) : 'XEVOS';

	if ( function_exists( 'xevos_send_email' ) ) {
		// Notify admin.
		xevos_send_email( get_option( 'admin_email' ), 'Nová poptávka testování – ' . $jmeno . ' ' . $prijmeni, 'admin-notification-inquiry', [
			'jmeno'      => $jmeno,
			'prijmeni'   => $prijmeni,
			'email'      => $email,
			'telefon'    => $telefon,
			'firma'      => $firma,
			'druh_testu' => $druh_testu,
			'zprava'     => $zprava,
		] );

		// Confirm to customer.
		xevos_send_email( $email, 'Potvrzení poptávky – ' . $firma_nazev, 'inquiry-confirmation', [
			'jmeno'         => $jmeno,
			'druh_testu'    => $druh_testu,
			'kontakt_email' => get_option( 'admin_email' ),
			'firma'         => $firma_nazev,
		] );
	}

	wp_send_json_success( [ 'message' => 'Poptávka byla úspěšně odeslána. Budeme Vás kontaktovat.' ] );
}

// Archive filter (aktuality / skoleni).
add_action( 'wp_ajax_xevos_filter_archive', 'xevos_filter_archive_handler' );
add_action( 'wp_ajax_nopriv_xevos_filter_archive', 'xevos_filter_archive_handler' );

function xevos_filter_archive_handler(): void {
	check_ajax_referer( 'xevos_nonce', 'nonce' );

	$post_type = sanitize_text_field( wp_unslash( $_POST['post_type'] ?? 'aktualita' ) );
	$taxonomy  = sanitize_text_field( wp_unslash( $_POST['taxonomy'] ?? '' ) );
	$term      = sanitize_text_field( wp_unslash( $_POST['term'] ?? '' ) );
	$order     = sanitize_text_field( wp_unslash( $_POST['order'] ?? 'DESC' ) );
	$paged     = absint( $_POST['paged'] ?? 1 );

	// Whitelist allowed post types.
	if ( ! in_array( $post_type, [ 'aktualita', 'skoleni' ], true ) ) {
		wp_send_json_error( [ 'message' => 'Invalid post type.' ] );
	}

	$args = [
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => 12,
		'paged'          => $paged,
		'orderby'        => 'date',
		'order'          => in_array( $order, [ 'ASC', 'DESC' ], true ) ? $order : 'DESC',
	];

	if ( $taxonomy && $term ) {
		$args['tax_query'] = [
			[
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $term,
			],
		];
	}

	$query = new WP_Query( $args );

	ob_start();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();

			$variant = sanitize_text_field( wp_unslash( $_POST['card_variant'] ?? '' ) );
			$template = "template-parts/components/card-{$post_type}";
			if ( $variant ) {
				$template .= '-' . $variant;
			}
			get_template_part( $template );
		}
	} else {
		echo '<p class="xevos-no-results">' . esc_html__( 'Žádné výsledky.', 'xevos-cyber' ) . '</p>';
	}

	$html = ob_get_clean();

	wp_send_json_success( [
		'html'       => $html,
		'found'      => $query->found_posts,
		'max_pages'  => $query->max_num_pages,
	] );

	wp_reset_postdata();
}

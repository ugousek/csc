<?php
/**
 * CSV export for Objednávky CPT.
 * Adds "Exportovat CSV" button to the orders list + handles the export.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add export button next to filters.
 */
add_action( 'restrict_manage_posts', 'xevos_objednavka_export_button', 20 );

function xevos_objednavka_export_button( string $post_type ): void {
	if ( 'objednavka' !== $post_type || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$url = add_query_arg( [
		'post_type'       => 'objednavka',
		'xevos_export'    => 'csv',
		'stav_platby'     => sanitize_text_field( wp_unslash( $_GET['stav_platby'] ?? '' ) ),
		'typ_registrace'  => sanitize_text_field( wp_unslash( $_GET['typ_registrace'] ?? '' ) ),
		'filter_skoleni'  => absint( $_GET['filter_skoleni'] ?? 0 ),
	], admin_url( 'edit.php' ) );

	printf(
		'<a href="%s" class="button" style="margin-left:8px;">%s</a>',
		esc_url( wp_nonce_url( $url, 'xevos_export_csv' ) ),
		esc_html__( 'Exportovat CSV', 'xevos-cyber' )
	);
}

/**
 * Handle CSV export.
 */
add_action( 'admin_init', 'xevos_objednavka_handle_export' );

function xevos_objednavka_handle_export(): void {
	if (
		empty( $_GET['xevos_export'] ) ||
		$_GET['xevos_export'] !== 'csv' ||
		empty( $_GET['post_type'] ) ||
		$_GET['post_type'] !== 'objednavka' ||
		! current_user_can( 'manage_options' ) ||
		! wp_verify_nonce( $_GET['_wpnonce'] ?? '', 'xevos_export_csv' )
	) {
		return;
	}

	$args = [
		'post_type'      => 'objednavka',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
		'meta_query'     => [],
	];

	// Apply same filters as list view.
	$stav = sanitize_text_field( wp_unslash( $_GET['stav_platby'] ?? '' ) );
	if ( $stav ) {
		$args['meta_query'][] = [ 'key' => 'stav_platby', 'value' => $stav ];
	}

	$typ = sanitize_text_field( wp_unslash( $_GET['typ_registrace'] ?? '' ) );
	if ( $typ ) {
		$args['meta_query'][] = [ 'key' => 'typ_registrace', 'value' => $typ ];
	}

	$skoleni_id = absint( $_GET['filter_skoleni'] ?? 0 );
	if ( $skoleni_id ) {
		$args['meta_query'][] = [ 'key' => 'skoleni', 'value' => $skoleni_id ];
	}

	$orders = get_posts( $args );

	// CSV headers.
	$filename = 'objednavky-' . gmdate( 'Y-m-d' ) . '.csv';
	header( 'Content-Type: text/csv; charset=UTF-8' );
	header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
	header( 'Pragma: no-cache' );

	$output = fopen( 'php://output', 'w' );

	// BOM for Excel UTF-8.
	fprintf( $output, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );

	// Header row.
	fputcsv( $output, [
		'Číslo',
		'Datum',
		'Jméno',
		'Příjmení',
		'Email',
		'Telefon',
		'Firma',
		'IČO',
		'DIČ',
		'Školení',
		'Termín',
		'Typ registrace',
		'Částka',
		'Stav',
		'Comgate ID',
	], ';' );

	$stav_labels = [
		'pending'            => 'Čeká na platbu',
		'invoice'            => 'Čeká na úhradu faktury',
		'invitation_pending' => 'Čeká na pozvánku',
		'paid'               => 'Zaplaceno',
		'registered'         => 'Registrováno (zdarma)',
		'cancelled'          => 'Zrušeno',
		'refunded'           => 'Refundováno',
	];

	$typ_labels = [
		'paid' => 'Placená',
		'free' => 'Zdarma',
	];

	foreach ( $orders as $order ) {
		$id        = $order->ID;
		$skoleni   = get_field( 'skoleni', $id );
		$stav_val  = get_field( 'stav_platby', $id ) ?: 'pending';
		$typ_val   = get_field( 'typ_registrace', $id ) ?: 'paid';

		fputcsv( $output, [
			$order->post_title,
			get_the_date( 'd.m.Y H:i', $id ),
			get_field( 'jmeno', $id ),
			get_field( 'prijmeni', $id ),
			get_field( 'email', $id ),
			get_field( 'telefon', $id ),
			get_field( 'firma', $id ),
			get_field( 'ico', $id ),
			get_field( 'dic', $id ),
			$skoleni ? $skoleni->post_title : '',
			get_field( 'termin', $id ),
			$typ_labels[ $typ_val ] ?? $typ_val,
			get_field( 'castka', $id ) ?: '0',
			$stav_labels[ $stav_val ] ?? $stav_val,
			get_field( 'comgate_transaction_id', $id ),
		], ';' );
	}

	fclose( $output );
	exit;
}

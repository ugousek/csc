<?php
/**
 * Admin filters for Objednávky CPT list.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

add_action( 'restrict_manage_posts', 'xevos_objednavka_filters' );
add_action( 'pre_get_posts', 'xevos_objednavka_filter_query' );

function xevos_objednavka_filters( string $post_type ): void {
	if ( 'objednavka' !== $post_type ) {
		return;
	}

	// Payment status filter.
	$current_stav = sanitize_text_field( wp_unslash( $_GET['stav_platby'] ?? '' ) );
	$stavy = [
		''                   => __( 'Všechny stavy', 'xevos-cyber' ),
		'pending'            => __( 'Čeká na platbu', 'xevos-cyber' ),
		'invoice'            => __( 'Čeká na úhradu faktury', 'xevos-cyber' ),
		'invitation_pending' => __( 'Čeká na pozvánku', 'xevos-cyber' ),
		'paid'               => __( 'Zaplaceno', 'xevos-cyber' ),
		'registered'         => __( 'Registrováno (zdarma)', 'xevos-cyber' ),
		'cancelled'          => __( 'Zrušeno', 'xevos-cyber' ),
		'refunded'           => __( 'Refundováno', 'xevos-cyber' ),
	];

	echo '<select name="stav_platby">';
	foreach ( $stavy as $value => $label ) {
		printf(
			'<option value="%s"%s>%s</option>',
			esc_attr( $value ),
			selected( $current_stav, $value, false ),
			esc_html( $label )
		);
	}
	echo '</select>';

	// Registration type filter.
	$current_typ = sanitize_text_field( wp_unslash( $_GET['typ_registrace'] ?? '' ) );
	$typy = [
		''     => __( 'Všechny typy', 'xevos-cyber' ),
		'paid' => __( 'Placená', 'xevos-cyber' ),
		'free' => __( 'Zdarma', 'xevos-cyber' ),
	];

	echo '<select name="typ_registrace">';
	foreach ( $typy as $value => $label ) {
		printf(
			'<option value="%s"%s>%s</option>',
			esc_attr( $value ),
			selected( $current_typ, $value, false ),
			esc_html( $label )
		);
	}
	echo '</select>';

	// Training filter.
	$current_skoleni = absint( $_GET['filter_skoleni'] ?? 0 );
	$skoleni_posts = get_posts( [
		'post_type'      => 'skoleni',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	] );

	echo '<select name="filter_skoleni">';
	echo '<option value="">' . esc_html__( 'Všechna školení', 'xevos-cyber' ) . '</option>';
	foreach ( $skoleni_posts as $sk ) {
		printf(
			'<option value="%d"%s>%s</option>',
			$sk->ID,
			selected( $current_skoleni, $sk->ID, false ),
			esc_html( $sk->post_title )
		);
	}
	echo '</select>';
}

function xevos_objednavka_filter_query( WP_Query $query ): void {
	if ( ! is_admin() || ! $query->is_main_query() || 'objednavka' !== $query->get( 'post_type' ) ) {
		return;
	}

	$meta_query = $query->get( 'meta_query' ) ?: [];

	// Filter by payment status.
	$stav = sanitize_text_field( wp_unslash( $_GET['stav_platby'] ?? '' ) );
	if ( $stav ) {
		$meta_query[] = [
			'key'   => 'stav_platby',
			'value' => $stav,
		];
	}

	// Filter by registration type.
	$typ = sanitize_text_field( wp_unslash( $_GET['typ_registrace'] ?? '' ) );
	if ( $typ ) {
		$meta_query[] = [
			'key'   => 'typ_registrace',
			'value' => $typ,
		];
	}

	// Filter by training.
	$skoleni_id = absint( $_GET['filter_skoleni'] ?? 0 );
	if ( $skoleni_id ) {
		$meta_query[] = [
			'key'   => 'skoleni',
			'value' => $skoleni_id,
		];
	}

	if ( $meta_query ) {
		$query->set( 'meta_query', $meta_query );
	}

	// Default sort: newest first.
	if ( ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', 'date' );
		$query->set( 'order', 'DESC' );
	}
}

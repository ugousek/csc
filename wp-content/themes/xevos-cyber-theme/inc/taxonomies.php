<?php
/**
 * Custom taxonomies registration.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'xevos_register_taxonomies' );

function xevos_register_taxonomies(): void {
	// Kategorie aktualit.
	register_taxonomy( 'kategorie-aktualit', 'aktualita', [
		'labels'       => [
			'name'          => __( 'Kategorie aktualit', 'xevos-cyber' ),
			'singular_name' => __( 'Kategorie aktuality', 'xevos-cyber' ),
			'add_new_item'  => __( 'Přidat kategorii', 'xevos-cyber' ),
			'search_items'  => __( 'Hledat kategorie', 'xevos-cyber' ),
			'all_items'     => __( 'Všechny kategorie', 'xevos-cyber' ),
		],
		'hierarchical' => true,
		'public'       => true,
		'show_in_rest' => true,
		'rewrite'      => [ 'slug' => 'kategorie-aktualit', 'with_front' => false ],
	] );

	// Kategorie školení.
	register_taxonomy( 'kategorie-skoleni', 'skoleni', [
		'labels'       => [
			'name'          => __( 'Kategorie školení', 'xevos-cyber' ),
			'singular_name' => __( 'Kategorie školení', 'xevos-cyber' ),
			'add_new_item'  => __( 'Přidat kategorii', 'xevos-cyber' ),
			'search_items'  => __( 'Hledat kategorie', 'xevos-cyber' ),
			'all_items'     => __( 'Všechny kategorie', 'xevos-cyber' ),
		],
		'hierarchical' => true,
		'public'       => true,
		'show_in_rest' => true,
		'rewrite'      => [ 'slug' => 'kategorie-skoleni', 'with_front' => false ],
	] );
}

// Pre-populate terms on theme activation + every init (idempotent).
add_action( 'after_switch_theme', 'xevos_populate_default_terms' );
add_action( 'init', 'xevos_populate_default_terms', 20 );

function xevos_populate_default_terms(): void {
	$aktuality_terms = [ 'Azure', 'Cloud', 'NIS2', 'Kybernetická bezpečnost', 'GDPR', 'Školení' ];
	foreach ( $aktuality_terms as $term ) {
		if ( ! term_exists( $term, 'kategorie-aktualit' ) ) {
			wp_insert_term( $term, 'kategorie-aktualit' );
		}
	}

	$skoleni_terms = [ 'Eventy', 'Webináře', 'Školení', 'Kybernetická bezpečnost', 'NIS2', 'Cloud Security', 'GDPR', 'Awareness' ];
	foreach ( $skoleni_terms as $term ) {
		if ( ! term_exists( $term, 'kategorie-skoleni' ) ) {
			wp_insert_term( $term, 'kategorie-skoleni' );
		}
	}
}

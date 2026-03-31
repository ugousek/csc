<?php
/**
 * Custom Post Types registration.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'xevos_register_post_types' );

function xevos_register_post_types(): void {
	// CPT: Aktuality.
	register_post_type( 'aktualita', [
		'labels'       => [
			'name'               => __( 'Aktuality', 'xevos-cyber' ),
			'singular_name'      => __( 'Aktualita', 'xevos-cyber' ),
			'add_new'            => __( 'Přidat aktualitu', 'xevos-cyber' ),
			'add_new_item'       => __( 'Přidat novou aktualitu', 'xevos-cyber' ),
			'edit_item'          => __( 'Upravit aktualitu', 'xevos-cyber' ),
			'view_item'          => __( 'Zobrazit aktualitu', 'xevos-cyber' ),
			'all_items'          => __( 'Všechny aktuality', 'xevos-cyber' ),
			'search_items'       => __( 'Hledat aktuality', 'xevos-cyber' ),
			'not_found'          => __( 'Žádné aktuality nenalezeny', 'xevos-cyber' ),
			'not_found_in_trash' => __( 'Žádné aktuality v koši', 'xevos-cyber' ),
		],
		'public'       => true,
		'has_archive'  => true,
		'rewrite'      => [ 'slug' => 'aktuality', 'with_front' => false ],
		'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-megaphone',
		'menu_position' => 5,
	] );

	// CPT: Školení.
	register_post_type( 'skoleni', [
		'labels'       => [
			'name'               => __( 'Školení', 'xevos-cyber' ),
			'singular_name'      => __( 'Školení', 'xevos-cyber' ),
			'add_new'            => __( 'Přidat školení', 'xevos-cyber' ),
			'add_new_item'       => __( 'Přidat nové školení', 'xevos-cyber' ),
			'edit_item'          => __( 'Upravit školení', 'xevos-cyber' ),
			'view_item'          => __( 'Zobrazit školení', 'xevos-cyber' ),
			'all_items'          => __( 'Všechna školení', 'xevos-cyber' ),
			'search_items'       => __( 'Hledat školení', 'xevos-cyber' ),
			'not_found'          => __( 'Žádná školení nenalezena', 'xevos-cyber' ),
			'not_found_in_trash' => __( 'Žádná školení v koši', 'xevos-cyber' ),
		],
		'public'       => true,
		'has_archive'  => true,
		'rewrite'      => [ 'slug' => 'skoleni', 'with_front' => false ],
		'supports'     => [ 'title', 'thumbnail' ],
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-welcome-learn-more',
		'menu_position' => 6,
	] );

	// CPT: Recenze.
	register_post_type( 'recenze', [
		'labels'       => [
			'name'               => __( 'Recenze', 'xevos-cyber' ),
			'singular_name'      => __( 'Recenze', 'xevos-cyber' ),
			'add_new'            => __( 'Přidat recenzi', 'xevos-cyber' ),
			'add_new_item'       => __( 'Přidat novou recenzi', 'xevos-cyber' ),
			'edit_item'          => __( 'Upravit recenzi', 'xevos-cyber' ),
			'all_items'          => __( 'Všechny recenze', 'xevos-cyber' ),
		],
		'public'       => false,
		'show_ui'      => true,
		'supports'     => [ 'title' ],
		'show_in_rest' => false,
		'menu_icon'    => 'dashicons-star-filled',
		'menu_position' => 7,
	] );

	// CPT: Objednávky.
	register_post_type( 'objednavka', [
		'labels'       => [
			'name'               => __( 'Objednávky', 'xevos-cyber' ),
			'singular_name'      => __( 'Objednávka', 'xevos-cyber' ),
			'edit_item'          => __( 'Detail objednávky', 'xevos-cyber' ),
			'all_items'          => __( 'Všechny objednávky', 'xevos-cyber' ),
			'search_items'       => __( 'Hledat objednávky', 'xevos-cyber' ),
			'not_found'          => __( 'Žádné objednávky', 'xevos-cyber' ),
		],
		'public'       => false,
		'show_ui'      => true,
		'supports'     => [ 'title' ],
		'show_in_rest' => false,
		'menu_icon'    => 'dashicons-cart',
		'menu_position' => 8,
		'capabilities' => [
			'create_posts' => 'manage_options',
		],
		'map_meta_cap' => true,
	] );
}

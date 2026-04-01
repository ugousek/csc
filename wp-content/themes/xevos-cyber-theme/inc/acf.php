<?php
/**
 * ACF Pro configuration: options pages, helpers.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

// ACF JSON save/load paths.
add_filter( 'acf/settings/save_json', 'xevos_acf_json_save_path' );
add_filter( 'acf/settings/load_json', 'xevos_acf_json_load_path' );

function xevos_acf_json_save_path( string $path ): string {
	return XEVOS_THEME_DIR . '/acf-json';
}

function xevos_acf_json_load_path( array $paths ): array {
	unset( $paths[0] );
	$paths[] = XEVOS_THEME_DIR . '/acf-json';
	return $paths;
}

// Register ACF Options Pages.
add_action( 'acf/init', 'xevos_acf_options_pages' );

function xevos_acf_options_pages(): void {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page( [
		'page_title'  => __( 'Nastavení webu', 'xevos-cyber' ),
		'menu_title'  => __( 'Nastavení webu', 'xevos-cyber' ),
		'menu_slug'   => 'xevos-settings',
		'capability'  => 'manage_options',
		'redirect'    => false,
		'icon_url'    => 'dashicons-admin-generic',
		'position'    => 2,
	] );
}

/**
 * Get site option from ACF Options page.
 *
 * @param string $field_name ACF field name.
 * @param mixed  $default    Default value.
 * @return mixed
 */
function xevos_get_option( string $field_name, mixed $default = '' ): mixed {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$cache_key = 'xevos_opt_' . $field_name;
	$cached    = wp_cache_get( $cache_key, 'xevos_options' );

	if ( false !== $cached ) {
		return $cached ?: $default;
	}

	$value = get_field( $field_name, 'option' );
	wp_cache_set( $cache_key, $value, 'xevos_options' );

	return $value ?: $default;
}

/**
 * Clear options cache when ACF options are saved.
 */
add_action( 'acf/save_post', function ( $post_id ): void {
	if ( $post_id !== 'options' ) {
		return;
	}
	if ( function_exists( 'wp_cache_delete_group' ) ) {
		wp_cache_delete_group( 'xevos_options' );
	} else {
		wp_cache_flush();
	}
}, 20 );

/**
 * Register Školení field group via PHP (overrides DB version).
 */
add_action( 'acf/init', 'xevos_register_skoleni_fields' );

function xevos_register_skoleni_fields(): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$json = file_get_contents( XEVOS_THEME_DIR . '/acf-json/group_xevos_skoleni.json' );
	if ( ! $json ) {
		return;
	}

	$group = json_decode( $json, true );
	if ( ! $group || empty( $group['key'] ) ) {
		return;
	}

	acf_add_local_field_group( $group );
}

/**
 * Get training availability info.
 *
 * @param int $skoleni_id Post ID.
 * @param int $termin_index Index in repeater.
 * @return array{kapacita: int, registrace: int, volna_mista: int, plne: bool}
 */
function xevos_get_termin_dostupnost( int $skoleni_id, int $termin_index ): array {
	$terminy = get_field( 'terminy', $skoleni_id );

	if ( ! $terminy || ! isset( $terminy[ $termin_index ] ) ) {
		return [
			'kapacita'    => 0,
			'registrace'  => 0,
			'volna_mista' => 0,
			'plne'        => true,
		];
	}

	$termin    = $terminy[ $termin_index ];
	$kapacita  = (int) ( $termin['kapacita'] ?? 0 );
	$registrace = (int) ( $termin['pocet_registraci'] ?? 0 );

	return [
		'kapacita'    => $kapacita,
		'registrace'  => $registrace,
		'volna_mista' => max( 0, $kapacita - $registrace ),
		'plne'        => $registrace >= $kapacita,
	];
}

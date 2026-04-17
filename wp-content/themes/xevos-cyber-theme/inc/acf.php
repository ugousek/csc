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

/**
 * Auto-sync ACF field groups from JSON when modified timestamp is newer than DB.
 * Runs in admin only — prevents stale DB copies from overriding JSON changes.
 */
add_action( 'admin_init', 'xevos_acf_auto_sync_json' );

function xevos_acf_auto_sync_json(): void {
	if ( ! function_exists( 'acf_get_field_group' ) || ! function_exists( 'acf_import_field_group' ) ) {
		return;
	}

	$dir = XEVOS_THEME_DIR . '/acf-json';
	if ( ! is_dir( $dir ) ) {
		return;
	}

	foreach ( glob( $dir . '/*.json' ) as $file ) {
		$json = json_decode( file_get_contents( $file ), true );
		if ( empty( $json['key'] ) ) {
			continue;
		}

		$db_group = acf_get_field_group( $json['key'] );
		$json_mod = (int) ( $json['modified'] ?? 0 );
		$db_mod   = (int) ( $db_group['modified'] ?? 0 );

		// Import only when JSON is strictly newer than DB (or DB entry is missing).
		if ( ! $db_group || $json_mod > $db_mod ) {
			acf_import_field_group( $json );
		}
	}
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

	// Archiv školení – options subpage under Školení menu.
	acf_add_options_sub_page( [
		'page_title'  => __( 'Nastavení archivu', 'xevos-cyber' ),
		'menu_title'  => __( 'Nastavení archivu', 'xevos-cyber' ),
		'menu_slug'   => 'xevos-skoleni-archive',
		'parent_slug' => 'edit.php?post_type=skoleni',
		'capability'  => 'manage_options',
	] );

	// Archiv aktualit – options subpage under Aktuality menu.
	acf_add_options_sub_page( [
		'page_title'  => __( 'Nastavení archivu', 'xevos-cyber' ),
		'menu_title'  => __( 'Nastavení archivu', 'xevos-cyber' ),
		'menu_slug'   => 'xevos-aktuality-archive',
		'parent_slug' => 'edit.php?post_type=aktualita',
		'capability'  => 'manage_options',
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

// Školení field group is loaded automatically via acf/settings/load_json path above.
// No manual acf_add_local_field_group() call needed — duplicate registration caused ACF conflicts.

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

	$termin     = $terminy[ $termin_index ];
	$kapacita   = (int) ( $termin['kapacita'] ?? 0 );
	$termin_key = function_exists( 'xevos_termin_key' ) ? xevos_termin_key( $termin ) : ( $termin['datum'] ?? '' );
	$registrace = function_exists( 'xevos_count_active_registrations' )
		? xevos_count_active_registrations( $skoleni_id, $termin_key )
		: 0;

	$volna  = $kapacita > 0 ? max( 0, $kapacita - $registrace ) : PHP_INT_MAX;
	$plne   = $kapacita > 0 && $registrace >= $kapacita;
	$procent = $kapacita > 0 ? ( $registrace / $kapacita ) * 100 : 0;

	// Label logic:
	// 0-50% obsazeno → "K dispozici"
	// 51-89% obsazeno → "K dispozici - X/Y" (s počtem míst)
	// 90%+ obsazeno → "Poslední místa - X/Y"
	// 100% → "Obsazeno"
	if ( $plne ) {
		$label = 'Obsazeno';
		$cislo = '';
		$stav  = 'full';
	} elseif ( $procent >= 90 ) {
		$label = 'Poslední místa';
		$cislo = $registrace . '/' . $kapacita;
		$stav  = 'warning';
	} elseif ( $procent > 50 ) {
		$label = 'K dispozici';
		$cislo = $registrace . '/' . $kapacita;
		$stav  = 'available';
	} else {
		$label = 'K dispozici';
		$cislo = '';
		$stav  = 'available';
	}

	return [
		'kapacita'    => $kapacita,
		'registrace'  => $registrace,
		'volna_mista' => $volna,
		'plne'        => $plne,
		'label'       => $label,
		'cislo'       => $cislo,
		'stav'        => $stav,
	];
}

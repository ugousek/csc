<?php

/**
 * WordPress hardening — vypnutí běžně zneužívaných endpointů a recon vektorů.
 * Doplňuje inc/security-headers.php (ten řeší HTTP hlavičky a REST users).
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * 1) XML-RPC — kompletně vypnout. Reálně používá jen Jetpack a starý Windows
 *    Live Writer, nic z toho zde neběží. Aktivně zneužíváno k brute-force
 *    (system.multicall) a pingback DDoS.
 * --------------------------------------------------------------------- */
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'wp_xmlrpc_server_class', '__return_false' );

// Odřízne i pingback methods (kdyby xmlrpc bylo přesto povoleno přes plugin).
add_filter( 'xmlrpc_methods', static function ( $methods ) {
	unset( $methods['pingback.ping'], $methods['pingback.extensions.getPingbacks'] );
	return $methods;
} );

// Pingback X-Pingback header — odebrat.
add_filter( 'wp_headers', static function ( $headers ) {
	unset( $headers['X-Pingback'] );
	return $headers;
}, 10, 1 );

/* -------------------------------------------------------------------------
 * 2) Odstranit recon meta z <head> — RSD, WLW, shortlink, REST link, oEmbed.
 * --------------------------------------------------------------------- */
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'template_redirect', 'rest_output_link_header', 11 );

/* -------------------------------------------------------------------------
 * 3) Application Passwords — vypnout (málo komu se hodí, široký útočný povrch
 *    pro přihlašovací automaty).
 * --------------------------------------------------------------------- */
add_filter( 'wp_is_application_passwords_available', '__return_false' );

/* -------------------------------------------------------------------------
 * 4) DISALLOW_FILE_EDIT — vypne editor témat a pluginů v wp-adminu, takže
 *    útočník s admin-level kompromitací nemůže přepsat PHP přes UI.
 * --------------------------------------------------------------------- */
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

/* -------------------------------------------------------------------------
 * 5) Skrýt informativní soubory v rootu (readme.html, license.txt) a
 *    debug log, pokud někdo zapomněl.
 * --------------------------------------------------------------------- */
add_action( 'init', static function () {
	if ( ! isset( $_SERVER['REQUEST_URI'] ) ) return;
	$uri = strtolower( (string) $_SERVER['REQUEST_URI'] );
	$blocked = [
		'/readme.html',
		'/license.txt',
		'/wp-config-sample.php',
		'/wp-content/debug.log',
	];
	foreach ( $blocked as $path ) {
		if ( strpos( $uri, $path ) !== false ) {
			status_header( 404 );
			nocache_headers();
			exit;
		}
	}
} );

/* -------------------------------------------------------------------------
 * 6) Vypnout REST endpointy, které prozrazují strukturu obsahu, pokud volá
 *    neautentizovaný uživatel. Necháváme jen public posts/pages potřebné
 *    pro frontend (theme to nevolá, ale necháme pro kompatibilitu).
 * --------------------------------------------------------------------- */
add_filter( 'rest_endpoints', static function ( array $endpoints ): array {
	if ( is_user_logged_in() ) {
		return $endpoints;
	}
	// Skrýt endpointy které by jinak prozradily plugin/theme listy nebo settings.
	$drop = [
		'/wp/v2/plugins',
		'/wp/v2/themes',
		'/wp/v2/settings',
		'/wp/v2/users/me',
	];
	foreach ( $drop as $route ) {
		if ( isset( $endpoints[ $route ] ) ) {
			unset( $endpoints[ $route ] );
		}
	}
	return $endpoints;
} );

/* -------------------------------------------------------------------------
 * 7) Trackbacky — vypnout úplně (legacy mechanism, dnes jen spam vektor).
 * --------------------------------------------------------------------- */
add_action( 'init', static function () {
	if ( isset( $_SERVER['REQUEST_URI'] ) && stripos( (string) $_SERVER['REQUEST_URI'], '/trackback' ) !== false ) {
		status_header( 403 );
		nocache_headers();
		exit;
	}
} );

/* -------------------------------------------------------------------------
 * 8) Oslabit informativnost feedů (nezveřejňovat user_login).
 * --------------------------------------------------------------------- */
add_filter( 'the_author', static function ( $author ) {
	// V RSS feedu ukázat display_name, ne username.
	if ( is_feed() ) {
		$user_id = get_the_author_meta( 'ID' );
		if ( $user_id ) {
			$display = get_the_author_meta( 'display_name', $user_id );
			return $display ?: $author;
		}
	}
	return $author;
} );

/* -------------------------------------------------------------------------
 * 9) Strikter cookies pro WP login — HttpOnly + Secure + SameSite=Lax.
 *    WP defaultně neposílá Secure flag i na HTTPS instalaci (závisí na configu).
 * --------------------------------------------------------------------- */
add_filter( 'secure_signon_cookie', '__return_true' );

add_action( 'set_logged_in_cookie', static function ( $cookie, $expire, $expiration, $user_id, $scheme, $token ) {
	if ( ! headers_sent() && is_ssl() ) {
		// Re-emit cookie s SameSite=Lax (WP core do 6.6 nenastavuje SameSite).
		$cookie_name = ( SECURE_AUTH_COOKIE && 'secure_auth' === $scheme ) ? SECURE_AUTH_COOKIE : LOGGED_IN_COOKIE;
		setcookie(
			$cookie_name,
			$cookie,
			[
				'expires'  => $expire,
				'path'     => COOKIEPATH ? COOKIEPATH : '/',
				'domain'   => COOKIE_DOMAIN,
				'secure'   => true,
				'httponly' => true,
				'samesite' => 'Lax',
			]
		);
	}
}, 10, 6 );

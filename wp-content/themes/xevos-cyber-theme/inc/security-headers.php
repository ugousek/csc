<?php

/**
 * Bezpečnostní HTTP hlavičky + REST API hardening (zákaz enumerace uživatelů).
 *
 * Headers se posílají pro frontend response. Adminu se vyhneme, aby se nepokazil
 * editor / nahrávání médií.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * 1) Zablokovat anonymní přístup k /wp-json/wp/v2/users — to by jinak vrátilo
 *    seznam usernames (vektor pro brute-force).
 * 2) Zablokovat ?author=N redirect na /author/<slug>/, který také prozradí slug.
 * 3) Skrýt loginové chyby (přesný popis zda chyba je v jménu nebo heslu).
 */
add_filter( 'rest_authentication_errors', 'xevos_block_users_endpoint' );
function xevos_block_users_endpoint( $result ) {
	// Pokud už dřívější filter vrátil chybu, neměnit.
	if ( true === $result || is_wp_error( $result ) ) {
		return $result;
	}
	$route = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
	if ( strpos( $route, '/wp-json/wp/v2/users' ) !== false || strpos( $route, 'rest_route=/wp/v2/users' ) !== false ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_user_cannot_view',
				'Sorry, you are not allowed to list users.',
				[ 'status' => 401 ]
			);
		}
	}
	return $result;
}

add_filter( 'rest_endpoints', 'xevos_remove_users_rest_endpoints' );
function xevos_remove_users_rest_endpoints( array $endpoints ): array {
	if ( is_user_logged_in() && current_user_can( 'list_users' ) ) {
		return $endpoints;
	}
	if ( isset( $endpoints['/wp/v2/users'] ) ) {
		unset( $endpoints['/wp/v2/users'] );
	}
	if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
		unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
	}
	return $endpoints;
}

// ?author=N → 404 (jinak WP přesměruje na /author/<slug>/ a slug se objeví v URL).
add_action( 'template_redirect', 'xevos_block_author_enumeration' );
function xevos_block_author_enumeration(): void {
	if ( is_admin() ) return;
	if ( ! empty( $_GET['author'] ) ) {
		status_header( 404 );
		nocache_headers();
		exit;
	}
}

// Generic login error — neprozradíme, jestli chyba je v jméně nebo heslu.
add_filter( 'login_errors', static function () {
	return 'Neplatné přihlašovací údaje.';
} );

// Skrýt WP verzi z hlavičky (drobný recon prevent).
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

add_action( 'send_headers', 'xevos_send_security_headers' );

function xevos_send_security_headers(): void {
	if ( is_admin() ) {
		return;
	}
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		return;
	}

	// HSTS — vyžadovat HTTPS po dobu 1 roku, vč. subdomén, přípustné pro preload list.
	header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains; preload' );

	// Zákaz framování (clickjacking).
	header( 'X-Frame-Options: SAMEORIGIN' );

	// Zákaz MIME-sniffing.
	header( 'X-Content-Type-Options: nosniff' );

	// Referrer policy — bezpečný kompromis (posílá origin u cross-origin GET, plnou URL u same-origin).
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );

	// Permissions-Policy — vypnutí citlivých browser API, která web nepoužívá.
	header(
		'Permissions-Policy: '
		. 'accelerometer=(), '
		. 'autoplay=(), '
		. 'camera=(), '
		. 'display-capture=(), '
		. 'fullscreen=(self), '
		. 'geolocation=(), '
		. 'gyroscope=(), '
		. 'magnetometer=(), '
		. 'microphone=(), '
		. 'midi=(), '
		. 'payment=(), '
		. 'usb=()'
	);

	/*
	 * Content-Security-Policy — enforcing režim, deny-by-default.
	 *
	 * 'unsafe-inline' a 'unsafe-eval' zůstávají v script-src/style-src kvůli kompatibilitě
	 * s WP core, jQuery, Complianz cookie bannerem a GTM/GA. Pro přepnutí na nonce-based
	 * strict CSP by bylo nutné přepatchovat každý inline <script>/<style> v projektu.
	 *
	 * Povolené externí origin pro skripty/styly: jsdelivr (Swiper), cdnjs (Lottie),
	 * Google (Tag Manager + Analytics), Cloudflare Turnstile, fonts.gstatic.com.
	 */
	$google_script  = 'https://www.googletagmanager.com https://www.google-analytics.com https://ssl.google-analytics.com https://*.googletagmanager.com';
	$google_connect = 'https://*.google-analytics.com https://*.analytics.google.com https://stats.g.doubleclick.net https://*.g.doubleclick.net https://*.googletagmanager.com';
	$cdn_script     = 'https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com';
	$marketing      = 'https://connect.facebook.net https://www.facebook.com https://c.seznam.cz https://h.seznam.cz https://ssp.seznam.cz https://h.imedia.cz https://ssp.imedia.cz https://www.googleadservices.com https://googleads.g.doubleclick.net';
	$turnstile      = 'https://challenges.cloudflare.com';

	$csp = [
		"default-src 'none'",
		"script-src 'self' 'unsafe-inline' 'unsafe-eval' " . $cdn_script . ' ' . $google_script . ' ' . $turnstile . ' ' . $marketing,
		"style-src 'self' 'unsafe-inline' " . $cdn_script . ' https://fonts.googleapis.com',
		"img-src 'self' data: blob: https:",
		"font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net",
		"connect-src 'self' " . $cdn_script . ' ' . $google_connect . ' ' . $marketing,
		"frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com https://player.vimeo.com https://www.facebook.com " . $turnstile,
		"media-src 'self' https: data:",
		"manifest-src 'self'",
		"worker-src 'self' blob:",
		"child-src 'self' blob:",
		"frame-ancestors 'self'",
		"object-src 'none'",
		"base-uri 'self'",
		"form-action 'self' https://www.facebook.com https://www.googletagmanager.com https://td.doubleclick.net",
		'upgrade-insecure-requests',
	];
	header( 'Content-Security-Policy: ' . implode( '; ', $csp ) );
}

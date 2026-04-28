<?php

/**
 * Bezpečnostní HTTP hlavičky.
 *
 * Headers se posílají pro frontend response. Adminu se vyhneme, aby se nepokazil
 * editor / nahrávání médií. CSP je v Report-Only režimu — nic neblokuje, jen sbírá
 * případné porušení (lze později přepnout na enforcing).
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

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
		"form-action 'self'",
		'upgrade-insecure-requests',
	];
	header( 'Content-Security-Policy: ' . implode( '; ', $csp ) );
}

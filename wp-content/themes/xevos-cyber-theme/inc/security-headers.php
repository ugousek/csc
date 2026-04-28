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
	 * Content-Security-Policy v Report-Only režimu — aplikace neblokuje, jen by
	 * případně logovala porušení. WP admin se v tomto handleru přeskakuje.
	 *
	 * Povoleno:
	 *  - 'self' a HTTPS všech zdrojů pro skripty, styly, písma
	 *  - inline <script>/<style> (kvůli WP, GTM/GA, GDPR cookie banneru)
	 *  - data: pro obrázky (lazy SVG / inlined ikony)
	 *  - blob: pro images (Swiper / lazy loading)
	 */
	$csp = [
		"default-src 'self' https:",
		"script-src 'self' 'unsafe-inline' 'unsafe-eval' https:",
		"style-src 'self' 'unsafe-inline' https:",
		"img-src 'self' data: blob: https:",
		"font-src 'self' data: https:",
		"connect-src 'self' https:",
		"frame-src 'self' https:",
		"frame-ancestors 'self'",
		"object-src 'none'",
		"base-uri 'self'",
		"form-action 'self'",
	];
	header( 'Content-Security-Policy-Report-Only: ' . implode( '; ', $csp ) );
}

<?php
/**
 * Spam protection helpers:
 *  - Rate limiting per IP (max 5 submissions / 10 minutes)
 *  - Time-based check (form must be open at least 3 seconds before submit)
 *  - Cloudflare Turnstile (optional, enabled via Nastavení webu → Turnstile)
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

// ── Turnstile ────────────────────────────────────────────────────────────────

/**
 * Returns true if Turnstile is configured and enabled.
 */
function xevos_turnstile_enabled(): bool {
	return (bool) xevos_get_option( 'turnstile_enabled' )
		&& (bool) xevos_get_option( 'turnstile_site_key' )
		&& (bool) xevos_get_option( 'turnstile_secret_key' );
}

/**
 * Enqueue Turnstile JS on single-skoleni pages (only when enabled).
 */
add_action( 'wp_enqueue_scripts', 'xevos_turnstile_enqueue' );

function xevos_turnstile_enqueue(): void {
	if ( ! xevos_turnstile_enabled() ) {
		return;
	}
	if ( ! is_singular( 'skoleni' ) ) {
		return;
	}
	wp_enqueue_script(
		'cf-turnstile',
		'https://challenges.cloudflare.com/turnstile/v0/api.js',
		[],
		null,
		true
	);
}

/**
 * Verify Turnstile token server-side.
 * Call this in AJAX handlers when xevos_turnstile_enabled() === true.
 *
 * @return bool  True = human, false = bot / missing token.
 */
function xevos_turnstile_verify(): bool {
	if ( ! xevos_turnstile_enabled() ) {
		return true; // Not enabled — pass through.
	}

	$token = sanitize_text_field( $_POST['cf-turnstile-response'] ?? '' );
	if ( ! $token ) {
		return false;
	}

	$secret   = xevos_get_option( 'turnstile_secret_key' );
	$response = wp_remote_post( 'https://challenges.cloudflare.com/turnstile/v0/siteverify', [
		'body' => [
			'secret'   => $secret,
			'response' => $token,
			'remoteip' => xevos_get_client_ip(),
		],
	] );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	return ! empty( $data['success'] );
}

/**
 * Check rate limit for a given IP.
 * Returns true if OK to proceed, false if rate limit exceeded.
 *
 * @param string $action  Unique key, e.g. 'register' or 'payment'.
 * @param int    $max     Max submissions allowed in the window.
 * @param int    $window  Time window in seconds.
 */
function xevos_check_rate_limit( string $action, int $max = 5, int $window = 600 ): bool {
	$ip      = xevos_get_client_ip();
	$key     = 'xevos_rl_' . $action . '_' . md5( $ip );
	$entries = get_transient( $key );

	if ( $entries === false ) {
		$entries = [];
	}

	// Remove entries outside the window.
	$now     = time();
	$entries = array_filter( $entries, fn( $t ) => ( $now - $t ) < $window );

	if ( count( $entries ) >= $max ) {
		return false;
	}

	$entries[] = $now;
	set_transient( $key, array_values( $entries ), $window );

	return true;
}

/**
 * Verify that the form was open for at least $min_seconds before submitting.
 * Expects a hidden field `_form_time` with a signed timestamp.
 *
 * @param int $min_seconds Minimum seconds the form must be open.
 */
function xevos_check_form_time( int $min_seconds = 3 ): bool {
	$token = sanitize_text_field( $_POST['_form_time'] ?? '' );
	if ( ! $token ) {
		return false;
	}

	// Token is base64( timestamp . '|' . hmac )
	$decoded = base64_decode( $token, true );
	if ( ! $decoded || substr_count( $decoded, '|' ) < 1 ) {
		return false;
	}

	[ $timestamp, $hmac ] = explode( '|', $decoded, 2 );
	$expected = hash_hmac( 'sha256', $timestamp, wp_salt( 'auth' ) );

	if ( ! hash_equals( $expected, $hmac ) ) {
		return false;
	}

	$elapsed = time() - (int) $timestamp;
	return $elapsed >= $min_seconds;
}

/**
 * Generate the hidden form time token.
 */
function xevos_form_time_token(): string {
	$ts    = (string) time();
	$hmac  = hash_hmac( 'sha256', $ts, wp_salt( 'auth' ) );
	return base64_encode( $ts . '|' . $hmac );
}

/**
 * Get real client IP, accounting for proxies.
 */
function xevos_get_client_ip(): string {
	foreach ( [ 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR' ] as $key ) {
		if ( ! empty( $_SERVER[ $key ] ) ) {
			$ip = trim( explode( ',', $_SERVER[ $key ] )[0] );
			if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
				return $ip;
			}
		}
	}
	return '0.0.0.0';
}

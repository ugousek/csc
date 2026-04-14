<?php

/**
 * Restricted mode — pouze jedno školení je veřejně dostupné.
 * Nastavení se spravuje přes Administrace → Restricted mode.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

const XEVOS_RESTRICTED_BYPASS_COOKIE = 'xevos_rs_bypass';

/**
 * True = restricted mode je aktivní.
 */
function xevos_restricted_is_active(): bool {
	return (bool) get_option( 'xevos_restricted_enabled', 0 );
}

/**
 * True = tento návštěvník má cookie pro obcházení restricted modu.
 */
function xevos_restricted_has_bypass(): bool {
	return ! empty( $_COOKIE[ XEVOS_RESTRICTED_BYPASS_COOKIE ] );
}

/**
 * Pokud je v URL ?rs=1, ulož cookie (30 dní) a přesměruj bez parametru.
 * Cookie musí být nastavena dřív, než se pošle jakýkoliv výstup.
 */
add_action( 'init', function () {
	if ( ! isset( $_GET['rs'] ) ) return;

	if ( $_GET['rs'] === '1' ) {
		setcookie(
			XEVOS_RESTRICTED_BYPASS_COOKIE,
			'1',
			time() + 30 * DAY_IN_SECONDS,
			COOKIEPATH ?: '/',
			COOKIE_DOMAIN,
			is_ssl(),
			true
		);
		$_COOKIE[ XEVOS_RESTRICTED_BYPASS_COOKIE ] = '1';
	} elseif ( $_GET['rs'] === '0' ) {
		setcookie( XEVOS_RESTRICTED_BYPASS_COOKIE, '', time() - 3600, COOKIEPATH ?: '/', COOKIE_DOMAIN );
		unset( $_COOKIE[ XEVOS_RESTRICTED_BYPASS_COOKIE ] );
	}

	// Přesměruj bez ?rs= parametru
	$url = remove_query_arg( 'rs' );
	wp_safe_redirect( $url );
	exit;
}, 1 );

/**
 * Slug povoleného školení.
 */
function xevos_restricted_slug(): string {
	return (string) get_option( 'xevos_restricted_slug', '' );
}

/**
 * True = tento request smí projít bez přesměrování.
 */
function xevos_restricted_allow(): bool {
	if ( ! xevos_restricted_is_active() ) return true;

	$slug = xevos_restricted_slug();
	if ( ! $slug ) return true;

	// Admin, AJAX, cron, REST — vždy pustit
	if ( is_admin() )        return true;
	if ( wp_doing_ajax() )   return true;
	if ( wp_doing_cron() )   return true;
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) return true;

	// Přihlášení uživatelé nebo bypass cookie — plný přístup
	if ( is_user_logged_in() )        return true;
	if ( xevos_restricted_has_bypass() ) return true;

	// Povolené školení
	if ( is_singular( 'skoleni' ) && get_post_field( 'post_name', get_the_ID() ) === $slug ) {
		return true;
	}

	// Platební výsledkové stránky
	if ( is_page( [ 'platba-ok', 'platba-chyba' ] ) ) return true;

	return false;
}

/**
 * Přesměruj vše ostatní na povolené školení.
 */
add_action( 'template_redirect', function () {
	if ( xevos_restricted_allow() ) return;

	$slug = xevos_restricted_slug();
	$post = get_page_by_path( $slug, OBJECT, 'skoleni' );

	// Nenašli jsme cílové školení — neredigerujeme (zabráníme nekonečné smyčce)
	if ( ! $post ) return;

	wp_safe_redirect( get_permalink( $post ), 302 );
	exit;
} );

/**
 * Vyprázdni nav menu (header i footer) — pouze nepřihlášeným.
 */
add_filter( 'pre_wp_nav_menu', function ( $output ) {
	if ( ! xevos_restricted_is_active() )  return $output;
	if ( is_admin() )                      return $output;
	if ( is_user_logged_in() )             return $output;
	if ( xevos_restricted_has_bypass() )   return $output;
	return '';
} );

/**
 * Body class pro CSS.
 */
add_filter( 'body_class', function ( $classes ) {
	if ( xevos_restricted_is_active() && ! is_admin() && ! is_user_logged_in() && ! xevos_restricted_has_bypass() ) {
		$classes[] = 'xevos-restricted-mode';
	}
	return $classes;
} );

/**
 * Inline CSS — schová navigaci, search, hamburger, partners, footer nav/social.
 */
add_action( 'wp_head', function () {
	if ( ! xevos_restricted_is_active() ) return;
	?>
	<style id="xevos-restricted-mode">
		.xevos-restricted-mode .xevos-header__nav,
		.xevos-restricted-mode .xevos-header__search-btn,
		.xevos-restricted-mode .xevos-header__cta,
		.xevos-restricted-mode .xevos-header__hamburger,
		.xevos-restricted-mode .xevos-mobile-menu,
		.xevos-restricted-mode .xevos-mobile-menu__overlay,
		.xevos-restricted-mode .xevos-search-overlay,
		.xevos-restricted-mode .xevos-partners,
		.xevos-restricted-mode .xevos-footer__nav-social,
		.xevos-restricted-mode .xevos-footer__nav,
		.xevos-restricted-mode .xevos-footer__social { display: none !important; }
	</style>
	<?php
} );

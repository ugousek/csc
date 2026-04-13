<?php

/**
 * Restricted mode — pouze jedno školení je veřejně dostupné.
 * Nastavení se spravuje přes Administrace → Restricted mode.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * True = restricted mode je aktivní.
 */
function xevos_restricted_is_active(): bool {
	return (bool) get_option( 'xevos_restricted_enabled', 0 );
}

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
	if ( is_admin() )      return true;
	if ( wp_doing_ajax() ) return true;
	if ( wp_doing_cron() ) return true;
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) return true;

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
	$url  = $post ? get_permalink( $post ) : home_url( '/' );

	wp_safe_redirect( $url, 302 );
	exit;
} );

/**
 * Vyprázdni nav menu (header i footer).
 */
add_filter( 'pre_wp_nav_menu', function ( $output ) {
	if ( ! xevos_restricted_is_active() ) return $output;
	if ( is_admin() ) return $output;
	return '';
} );

/**
 * Body class pro CSS.
 */
add_filter( 'body_class', function ( $classes ) {
	if ( xevos_restricted_is_active() && ! is_admin() ) {
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

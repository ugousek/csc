<?php
/**
 * Utility / helper functions.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Fallback menu when no WP menu is assigned.
 */
function xevos_fallback_menu(): void {
	$items = [
		'Služby'      => '/sluzby/',
		'NIS 2'       => '/nis2/',
		'Eventy'      => '/skoleni/',
		'Partnerství' => '/partnerstvi/',
		'O nás'       => '/o-nas/',
		'Blog'        => '/aktuality/',
		'Kontakt'     => '/kontakt/',
	];
	echo '<ul class="xevos-header__nav-list">';
	foreach ( $items as $label => $url ) {
		printf(
			'<li class="menu-item"><a href="%s">%s</a></li>',
			esc_url( home_url( $url ) ),
			esc_html( $label )
		);
	}
	echo '</ul>';
}

/**
 * Render a template-part component with arguments.
 *
 * @param string $component Component name (e.g. 'button', 'card-aktualita').
 * @param array  $args      Arguments passed to the template.
 */
function xevos_component( string $component, array $args = [] ): void {
	get_template_part( 'template-parts/components/' . $component, null, $args );
}

/**
 * Render a homepage section partial.
 *
 * @param string $section Section name (e.g. 'hero', 'sluzby').
 */
function xevos_homepage_section( string $section ): void {
	get_template_part( 'template-parts/homepage/' . $section );
}

/**
 * Get ACF image with fallback. Returns img tag.
 *
 * @param array|false $image ACF image array (return format: array).
 * @param string      $size  WordPress image size.
 * @param string      $class CSS class.
 * @return string HTML img tag or empty string.
 */
function xevos_get_image( array|false $image, string $size = 'large', string $class = '' ): string {
	if ( ! $image || empty( $image['url'] ) ) {
		return '';
	}

	$url = $image['sizes'][ $size ] ?? $image['url'];
	$alt = esc_attr( $image['alt'] ?? '' );
	$cls = $class ? ' class="' . esc_attr( $class ) . '"' : '';

	return sprintf( '<img src="%s" alt="%s"%s loading="lazy">', esc_url( $url ), $alt, $cls );
}

/**
 * Generate breadcrumbs HTML.
 *
 * @return string Breadcrumbs HTML.
 */
function xevos_breadcrumbs(): string {
	if ( is_front_page() ) {
		return '';
	}

	$items = [];
	$items[] = '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Úvod', 'xevos-cyber' ) . '</a>';

	if ( is_singular( 'aktualita' ) ) {
		$items[] = '<a href="' . esc_url( get_post_type_archive_link( 'aktualita' ) ) . '">' . esc_html__( 'Aktuality', 'xevos-cyber' ) . '</a>';
		$items[] = '<span>' . esc_html( get_the_title() ) . '</span>';
	} elseif ( is_singular( 'skoleni' ) ) {
		$items[] = '<a href="' . esc_url( get_post_type_archive_link( 'skoleni' ) ) . '">' . esc_html__( 'Školení', 'xevos-cyber' ) . '</a>';
		$items[] = '<span>' . esc_html( get_the_title() ) . '</span>';
	} elseif ( is_post_type_archive( 'aktualita' ) ) {
		$items[] = '<span>' . esc_html__( 'Aktuality', 'xevos-cyber' ) . '</span>';
	} elseif ( is_post_type_archive( 'skoleni' ) ) {
		$items[] = '<span>' . esc_html__( 'Školení', 'xevos-cyber' ) . '</span>';
	} elseif ( is_page() ) {
		$items[] = '<span>' . esc_html( get_the_title() ) . '</span>';
	} elseif ( is_search() ) {
		$items[] = '<span>' . esc_html__( 'Výsledky vyhledávání', 'xevos-cyber' ) . '</span>';
	} elseif ( is_404() ) {
		$items[] = '<span>' . esc_html__( 'Stránka nenalezena', 'xevos-cyber' ) . '</span>';
	}

	return '<nav class="xevos-breadcrumbs" aria-label="breadcrumb">' . implode( ' <span class="xevos-breadcrumbs__sep">/</span> ', $items ) . '</nav>';
}

/**
 * Trim HTML text to N words while preserving <strong>, <em>, <br> tags.
 *
 * @param string $html  Input HTML.
 * @param int    $words Max word count.
 * @return string Trimmed and sanitized HTML.
 */
function xevos_trim_html( string $html, int $words = 30 ): string {
	$allowed = [ 'strong' => [], 'em' => [], 'br' => [] ];
	$clean   = wp_kses( $html, $allowed );
	$clean   = preg_replace( '/\s+/', ' ', strip_tags( str_replace( '>', '> ', $clean ), '<strong><em><br>' ) );

	$all   = preg_split( '/\s+/', trim( $clean ), -1, PREG_SPLIT_NO_EMPTY );
	$plain = preg_split( '/\s+/', trim( strip_tags( $clean ) ), -1, PREG_SPLIT_NO_EMPTY );

	if ( count( $plain ) <= $words ) {
		return wp_kses( $clean, $allowed );
	}

	// Walk through tokens, count only real words (not tags).
	$word_count = 0;
	$result     = '';
	$in_tag     = false;

	for ( $i = 0, $len = strlen( $clean ); $i < $len; $i++ ) {
		$char = $clean[ $i ];

		if ( $char === '<' ) {
			$in_tag = true;
			$result .= $char;
			continue;
		}
		if ( $char === '>' ) {
			$in_tag = false;
			$result .= $char;
			continue;
		}
		if ( $in_tag ) {
			$result .= $char;
			continue;
		}

		if ( $char === ' ' && $i > 0 && $clean[ $i - 1 ] !== '>' ) {
			$word_count++;
			if ( $word_count >= $words ) {
				break;
			}
		}
		$result .= $char;
	}

	// Close any open tags.
	$result = force_balance_tags( trim( $result ) . '&hellip;' );

	return wp_kses( $result, $allowed );
}

<?php
/**
 * Utility / helper functions.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Czech typography: non-breaking space after single-char prepositions/conjunctions.
 * Prevents orphaned v, k, s, z, i, a, o, u etc. at end of line.
 */
function xevos_fix_czech_typo( string $text ): string {
	// Match single-char Czech prepositions/conjunctions followed by a space (outside HTML tags).
	// Pattern: word boundary + single letter (v,k,s,z,i,a,o,u,V,K,S,Z,I,A,O,U) + space
	return preg_replace(
		'/(?<=\s|^)([vkszVKSZ]|[iaoIAO]|[uU])\s+(?=[^\s<])/u',
		'$1&nbsp;',
		$text
	);
}

// Apply to all text output.
add_filter( 'the_content', 'xevos_fix_czech_typo', 99 );
add_filter( 'the_title', 'xevos_fix_czech_typo', 99 );
add_filter( 'the_excerpt', 'xevos_fix_czech_typo', 99 );
add_filter( 'widget_text', 'xevos_fix_czech_typo', 99 );
add_filter( 'acf/format_value/type=wysiwyg', 'xevos_fix_czech_typo', 99 );
add_filter( 'acf/format_value/type=text', 'xevos_fix_czech_typo', 99 );
add_filter( 'acf/format_value/type=textarea', 'xevos_fix_czech_typo', 99 );

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

/* Breadcrumbs removed — not used in design. */

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

/**
 * Responsive image output from ACF image array or attachment ID.
 * Uses wp_get_attachment_image() which generates srcset + sizes automatically.
 * EWWW hooks into this for lazy load / WebP.
 *
 * @param array|int $image  ACF image array (['ID'=>…]) or attachment ID.
 * @param string    $size   WP image size name (default 'full').
 * @param array     $attrs  Extra <img> attributes (alt, class, loading…).
 * @return string   HTML <img> tag with srcset.
 */
function xevos_img( $image, string $size = 'full', array $attrs = [] ): string {
	$id = 0;
	if ( is_array( $image ) && ! empty( $image['ID'] ) ) {
		$id = (int) $image['ID'];
		if ( empty( $attrs['alt'] ) && ! empty( $image['alt'] ) ) {
			$attrs['alt'] = $image['alt'];
		}
	} elseif ( is_numeric( $image ) ) {
		$id = (int) $image;
	}
	if ( ! $id ) return '';

	return wp_get_attachment_image( $id, $size, false, $attrs );
}

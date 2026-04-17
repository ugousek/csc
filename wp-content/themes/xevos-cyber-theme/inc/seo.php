<?php
/**
 * SEO meta tags: description, canonical, Open Graph, Twitter Card.
 * Skips output when a dedicated SEO plugin (Yoast, RankMath) is active.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns true when a dedicated SEO plugin is active and owns meta output.
 */
function xevos_seo_plugin_active(): bool {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'AIOSEO_VERSION' );
}

/**
 * Resolve the best description for the current context.
 */
function xevos_seo_get_description(): string {
	if ( is_singular() ) {
		$excerpt = get_the_excerpt();
		if ( $excerpt ) {
			return wp_trim_words( wp_strip_all_tags( $excerpt ), 30, '…' );
		}

		$content = get_post_field( 'post_content', get_the_ID() );
		if ( $content ) {
			return wp_trim_words( wp_strip_all_tags( strip_shortcodes( $content ) ), 30, '…' );
		}
	}

	if ( is_post_type_archive( 'aktualita' ) ) {
		return 'Aktuality ze světa kybernetické bezpečnosti – novinky, hrozby a doporučení.';
	}

	if ( is_post_type_archive( 'skoleni' ) ) {
		return 'Nabídka školení v oblasti kybernetické bezpečnosti, NIS2, GDPR a compliance.';
	}

	if ( is_tax() || is_category() ) {
		$desc = term_description();
		if ( $desc ) {
			return wp_trim_words( wp_strip_all_tags( $desc ), 30, '…' );
		}
	}

	return (string) get_bloginfo( 'description' );
}

/**
 * Resolve canonical URL for the current context.
 */
function xevos_seo_get_canonical(): string {
	if ( is_singular() ) {
		return (string) get_permalink();
	}
	if ( is_post_type_archive() ) {
		return (string) get_post_type_archive_link( get_query_var( 'post_type' ) );
	}
	if ( is_tax() || is_category() || is_tag() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			return (string) get_term_link( $term );
		}
	}
	if ( is_front_page() || is_home() ) {
		return home_url( '/' );
	}
	if ( is_search() ) {
		return home_url( '/?s=' . rawurlencode( get_search_query() ) );
	}

	return home_url( add_query_arg( null, null ) );
}

/**
 * Resolve the best image URL for social sharing.
 */
function xevos_seo_get_share_image(): string {
	if ( is_singular() && has_post_thumbnail() ) {
		$url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
		if ( $url ) {
			return $url;
		}
	}

	$og = xevos_get_option( 'og_image' );
	if ( is_array( $og ) && ! empty( $og['url'] ) ) {
		return (string) $og['url'];
	}

	return '';
}

/**
 * Resolve og:type — 'article' for singles, 'website' otherwise.
 */
function xevos_seo_get_og_type(): string {
	return is_singular() && ! is_front_page() ? 'article' : 'website';
}

/**
 * Output all SEO meta tags (description, canonical, OG, Twitter).
 */
add_action( 'wp_head', 'xevos_seo_meta_output', 5 );

function xevos_seo_meta_output(): void {
	if ( xevos_seo_plugin_active() ) {
		return;
	}

	$title       = wp_get_document_title();
	$description = xevos_seo_get_description();
	$canonical   = xevos_seo_get_canonical();
	$image       = xevos_seo_get_share_image();
	$og_type     = xevos_seo_get_og_type();
	$site_name   = xevos_get_option( 'nazev_firmy', get_bloginfo( 'name' ) );
	$locale      = get_locale() ?: 'cs_CZ';

	// Description + robots.
	if ( $description ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
	}

	// Canonical.
	if ( $canonical ) {
		echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";
	}

	// Open Graph.
	echo '<meta property="og:type" content="' . esc_attr( $og_type ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">' . "\n";
	echo '<meta property="og:locale" content="' . esc_attr( $locale ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $description ) {
		echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
	}
	if ( $canonical ) {
		echo '<meta property="og:url" content="' . esc_url( $canonical ) . '">' . "\n";
	}
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
		echo '<meta property="og:image:width" content="1200">' . "\n";
		echo '<meta property="og:image:height" content="630">' . "\n";
	}

	// Article-specific OG.
	if ( $og_type === 'article' ) {
		echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c' ) ) . '">' . "\n";
		echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . "\n";
		$author = get_the_author();
		if ( $author ) {
			echo '<meta property="article:author" content="' . esc_attr( $author ) . '">' . "\n";
		}
	}

	// Twitter Card.
	echo '<meta name="twitter:card" content="' . esc_attr( $image ? 'summary_large_image' : 'summary' ) . '">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $description ) {
		echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
	}
	if ( $image ) {
		echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
	}
}

<?php
/**
 * Theme setup: supports, menus, image sizes.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'xevos_theme_setup' );

function xevos_theme_setup(): void {
	// Translation support.
	load_theme_textdomain( 'xevos-cyber', XEVOS_THEME_DIR . '/languages' );

	// Theme supports.
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [
		'search-form',
		'comment-form',
		'gallery',
		'caption',
		'style',
		'script',
	] );
	add_theme_support( 'custom-logo' );

	// Navigation menus.
	register_nav_menus( [
		'primary'  => __( 'Primary Navigation', 'xevos-cyber' ),
		'footer'   => __( 'Footer Navigation', 'xevos-cyber' ),
	] );

	// Custom image sizes.
	add_image_size( 'xevos-hero', 1920, 800, true );       // Hero full-width (subpages)
	add_image_size( 'xevos-hero-half', 910, 770, true );    // Hero two-col right image
	add_image_size( 'xevos-card', 507, 293, true );         // Blog/aktuality card (Figma: 507x480, image 475x274 @retina)
	add_image_size( 'xevos-card-sm', 448, 260, true );      // HP aktuality card (Figma: 448x480, image 416x248)
	add_image_size( 'xevos-thumbnail', 400, 300, true );    // General thumbnail
	add_image_size( 'xevos-lektor', 240, 240, true );       // Lektor photo circle
	add_image_size( 'xevos-article', 1200, 660, true );     // Article inline image
}

/**
 * Disable Gutenberg editor on front page — all content via ACF.
 */
add_action( 'init', function (): void {
	$front_page_id = (int) get_option( 'page_on_front' );
	if ( $front_page_id ) {
		add_filter( 'use_block_editor_for_post', function ( bool $use, \WP_Post $post ) use ( $front_page_id ): bool {
			return $post->ID === $front_page_id ? false : $use;
		}, 10, 2 );
	}
} );

add_action( 'admin_init', function (): void {
	$front_page_id = (int) get_option( 'page_on_front' );
	if ( $front_page_id ) {
		remove_post_type_support( 'page', 'editor' );
		// Re-add for non-front pages.
		add_action( 'load-post.php', function () use ( $front_page_id ): void {
			$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0;
			if ( $post_id !== $front_page_id ) {
				add_post_type_support( 'page', 'editor' );
			}
		} );
	}
} );

/**
 * Make custom sizes available in WP media picker.
 */
add_filter( 'image_size_names_choose', function ( array $sizes ): array {
	return array_merge( $sizes, [
		'xevos-hero'      => 'XEVOS Hero (1920×800)',
		'xevos-hero-half' => 'XEVOS Hero Half (910×770)',
		'xevos-card'      => 'XEVOS Card (507×293)',
		'xevos-card-sm'   => 'XEVOS Card SM (448×260)',
		'xevos-thumbnail' => 'XEVOS Thumbnail (400×300)',
		'xevos-lektor'    => 'XEVOS Lektor (240×240)',
		'xevos-article'   => 'XEVOS Article (1200×660)',
	] );
} );

/**
 * Custom robots.txt.
 */
add_filter( 'robots_txt', 'xevos_robots_txt', 10, 2 );

function xevos_robots_txt( string $output, bool $public ): string {
	if ( ! $public ) {
		return $output;
	}

	$output  = "User-agent: *\n";
	$output .= "Allow: /\n";
	$output .= "Disallow: /wp-admin/\n";
	$output .= "Allow: /wp-admin/admin-ajax.php\n\n";
	$output .= "Sitemap: " . home_url( '/sitemap_index.xml' ) . "\n";
	$output .= "Sitemap: " . home_url( '/sitemap.xml' ) . "\n";

	return $output;
}

/**
 * Archive posts per page — 12 for aktuality, 12 for skoleni.
 */
add_action( 'pre_get_posts', function ( WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() ) return;

	if ( $query->is_post_type_archive( 'aktualita' ) || $query->is_tax( 'kategorie-aktualit' ) ) {
		$query->set( 'posts_per_page', 12 );
	}
	if ( $query->is_post_type_archive( 'skoleni' ) || $query->is_tax( 'kategorie-skoleni' ) ) {
		$query->set( 'posts_per_page', 12 );
	}
} );


/**
 * Favicon meta tags from ACF Options.
 */
add_action( 'wp_head', 'xevos_favicon_meta', 1 );

function xevos_favicon_meta(): void {
	$favicon = xevos_get_option( 'favicon' );
	if ( ! $favicon ) {
		return;
	}

	$url = $favicon['url'];
	$mime = $favicon['mime_type'] ?? 'image/png';

	echo '<link rel="icon" type="' . esc_attr( $mime ) . '" href="' . esc_url( $url ) . '">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url( $url ) . '">' . "\n";
}

/**
 * OG meta tags fallback (when no SEO plugin).
 */
add_action( 'wp_head', 'xevos_og_meta', 5 );

function xevos_og_meta(): void {
	// Skip if Yoast or other SEO plugin is active.
	if ( defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) ) {
		return;
	}

	$title       = wp_get_document_title();
	$description = get_bloginfo( 'description' );
	$url         = is_singular() ? get_the_permalink() : home_url( '/' );
	$image       = '';

	if ( is_singular() && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( get_the_ID(), 'large' );
	}

	if ( ! $image ) {
		$og_img = xevos_get_option( 'og_image' );
		if ( $og_img ) {
			$image = $og_img['url'];
		}
	}

	if ( is_singular() ) {
		$description = get_the_excerpt() ?: $description;
	}

	echo '<meta property="og:type" content="website">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( wp_trim_words( $description, 30 ) ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
}

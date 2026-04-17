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
/**
 * Disable Gutenberg globally — all content is managed via ACF.
 */
add_filter( 'use_block_editor_for_post', '__return_false' );
add_filter( 'use_block_editor_for_post_type', '__return_false' );

/**
 * Hide default "Posts" from admin — we use CPT "aktualita" instead.
 */
add_action( 'admin_menu', function () {
	remove_menu_page( 'edit.php' );
	remove_menu_page( 'edit-comments.php' );
} );

add_action( 'admin_bar_menu', function ( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'new-post' );
	$wp_admin_bar->remove_node( 'comments' );
}, 999 );

// Hide from nav menus picker + disable comments.
add_action( 'init', function () {
	global $wp_post_types;
	if ( isset( $wp_post_types['post'] ) ) {
		$wp_post_types['post']->show_in_nav_menus = false;
	}
	// Remove comments support from all post types.
	foreach ( get_post_types( [], 'names' ) as $pt ) {
		remove_post_type_support( $pt, 'comments' );
		remove_post_type_support( $pt, 'trackbacks' );
	}

	// Hide classic editor on pages — all content managed via ACF.
	remove_post_type_support( 'page', 'editor' );
}, 99 );

// Completely disable comments everywhere.
add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );
add_filter( 'comments_array', '__return_empty_array', 10, 2 );

// Redirect comments admin page.
add_action( 'admin_init', function () {
	global $pagenow;
	if ( $pagenow === 'edit-comments.php' || $pagenow === 'options-discussion.php' ) {
		wp_safe_redirect( admin_url() );
		exit;
	}
} );

// Remove comments from admin bar "New" dropdown.
add_action( 'wp_before_admin_bar_render', function () {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu( 'comments' );
} );

// Remove comments widget.
add_action( 'widgets_init', function () {
	unregister_widget( 'WP_Widget_Recent_Comments' );
} );

// Remove comments from REST API.
add_filter( 'rest_endpoints', function ( $endpoints ) {
	unset( $endpoints['/wp/v2/comments'] );
	unset( $endpoints['/wp/v2/comments/(?P<id>[\\d]+)'] );
	return $endpoints;
} );

// Remove discussion settings from admin menu.
add_action( 'admin_menu', function () {
	remove_submenu_page( 'options-general.php', 'options-discussion.php' );
}, 999 );

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
		$default = get_theme_file_uri( 'assets/img/global/favicon.png' );
		if ( file_exists( get_theme_file_path( 'assets/img/global/favicon.png' ) ) ) {
			echo '<link rel="icon" type="image/png" href="' . esc_url( $default ) . '">' . "\n";
		}
		return;
	}

	$mime = $favicon['mime_type'] ?? 'image/png';
	$id   = $favicon['ID'] ?? 0;

	// SVG — one tag is enough, scales to any size
	if ( $mime === 'image/svg+xml' ) {
		echo '<link rel="icon" type="image/svg+xml" href="' . esc_url( $favicon['url'] ) . '">' . "\n";
		echo '<link rel="apple-touch-icon" href="' . esc_url( $favicon['url'] ) . '">' . "\n";
		remove_action( 'wp_head', 'wp_site_icon', 99 );
		return;
	}

	// Raster — use WP generated sizes from attachment
	$sizes = [
		[ 'rel' => 'icon',             'size' => '16x16',   'wp_size' => [ 16, 16 ] ],
		[ 'rel' => 'icon',             'size' => '32x32',   'wp_size' => [ 32, 32 ] ],
		[ 'rel' => 'icon',             'size' => '192x192', 'wp_size' => [ 192, 192 ] ],
		[ 'rel' => 'apple-touch-icon', 'size' => '180x180', 'wp_size' => [ 180, 180 ] ],
	];

	foreach ( $sizes as $s ) {
		$src = $id ? wp_get_attachment_image_url( $id, $s['wp_size'] ) : $favicon['url'];
		if ( $src ) {
			echo '<link rel="' . esc_attr( $s['rel'] ) . '" type="' . esc_attr( $mime ) . '" sizes="' . esc_attr( $s['size'] ) . '" href="' . esc_url( $src ) . '">' . "\n";
		}
	}

	remove_action( 'wp_head', 'wp_site_icon', 99 );
}

// SEO meta tags (description, canonical, OG, Twitter) live in inc/seo.php.

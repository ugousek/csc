<?php
/**
 * XEVOS Cyber Security Center theme functions.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

define( 'XEVOS_THEME_VERSION', '1.0.2' );
define( 'XEVOS_THEME_DIR', get_template_directory() );
define( 'XEVOS_THEME_URI', get_template_directory_uri() );

// Composer autoload.
if ( file_exists( XEVOS_THEME_DIR . '/vendor/autoload.php' ) ) {
	require_once XEVOS_THEME_DIR . '/vendor/autoload.php';
}

// Theme includes.
$xevos_includes = [
	'/inc/setup.php',
	'/inc/enqueue.php',
	'/inc/cpt.php',
	'/inc/taxonomies.php',
	'/inc/acf.php',
	'/inc/ajax-handlers.php',
	'/inc/helpers.php',
	'/inc/email.php',
	'/inc/admin/order-columns.php',
	'/inc/admin/order-filters.php',
	'/inc/admin/order-export.php',
	'/inc/admin/skoleni-registrace.php',
	'/inc/admin/order-paid-hook.php',
	'/inc/admin/order-detail.php',
	'/inc/admin/email-templates.php',
	'/inc/admin/poptavky-columns.php',
	'/inc/admin/lektori-columns.php',
	'/inc/admin/premium-features.php',
	'/inc/order-numbering.php',
	'/inc/spam-protection.php',
	'/inc/ecomail.php',
	'/inc/cron.php',
	'/inc/schema.php',
	'/inc/seo.php',
	'/inc/demo-content.php',
	// '/inc/cookie-consent.php', // Replaced by Complianz plugin
	'/inc/optimization.php',
	'/inc/admin/restricted-mode-settings.php',
	'/inc/restricted-mode.php',
];

/**
 * Fix WP dashboard "Quick Draft" error when get_default_post_to_edit returns null.
 */
add_action( 'wp_dashboard_setup', function () {
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
}, 20 );

foreach ( $xevos_includes as $file ) {
	$filepath = XEVOS_THEME_DIR . $file;
	if ( file_exists( $filepath ) ) {
		require_once $filepath;
	}
}

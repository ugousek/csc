<?php
/**
 * Custom email wrappers and templates.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Send HTML email using a template.
 *
 * @param string $to          Recipient email.
 * @param string $subject     Email subject.
 * @param string $template    Template filename (without .php) from email-templates/.
 * @param array  $data        Data passed to the template (accessible as $data['key']).
 * @param array  $attachments File paths to attach.
 * @return bool
 */
function xevos_send_email( string $to, string $subject, string $template, array $data = [], array $attachments = [] ): bool {
	$template_path = XEVOS_THEME_DIR . '/email-templates/' . $template . '.php';

	if ( ! file_exists( $template_path ) ) {
		return false;
	}

	// Sanitize all data values.
	$email_data = array_map( function ( $value ) {
		if ( is_string( $value ) ) {
			return sanitize_text_field( $value );
		}
		return $value;
	}, $data );

	// Render template – $email_data is available inside.
	ob_start();
	include $template_path;
	$html = ob_get_clean();

	$headers = [
		'Content-Type: text/html; charset=UTF-8',
		'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>',
	];

	return wp_mail( $to, $subject, $html, $headers, $attachments );
}

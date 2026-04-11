<?php
/**
 * Hook: When an order is manually saved as "paid" in admin,
 * generate invoice PDF and send payment confirmation email.
 *
 * Also makes the pdf_faktura ACF field read-only (display only).
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Capture old status from DB before ACF writes new data.
 * Must use save_post at priority 1 — ACF writes at priority 10.
 */
add_action( 'save_post', 'xevos_capture_old_order_status', 1, 1 );

function xevos_capture_old_order_status( int $post_id ): void {
	if ( get_post_type( $post_id ) !== 'objednavka' ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// At this point ACF hasn't written yet — DB still has the old value.
	$current = get_field( 'stav_platby', $post_id ) ?: 'pending';
	xevos_order_old_status( $post_id, $current );
}

/**
 * Static storage for old status (avoids globals).
 */
function xevos_order_old_status( int $post_id = 0, string $set = '' ): string {
	static $map = [];
	if ( $post_id && $set ) {
		$map[ $post_id ] = $set;
	}
	return $map[ $post_id ] ?? '';
}

/**
 * After ACF saves, check for paid transition and trigger invoice generation.
 */
add_action( 'acf/save_post', 'xevos_handle_manual_paid_status', 20 );

function xevos_handle_manual_paid_status( $post_id ): void {
	if ( get_post_type( $post_id ) !== 'objednavka' ) {
		return;
	}

	$new_status = get_field( 'stav_platby', $post_id ) ?: 'pending';
	$old_status = xevos_order_old_status( $post_id );

	if ( $new_status !== 'paid' || $old_status === 'paid' ) {
		return;
	}

	// Log the manual status change.
	$log = get_post_meta( $post_id, '_xevos_status_log', true ) ?: [];
	$log[] = [
		'from'    => $old_status,
		'to'      => 'paid',
		'date'    => current_time( 'mysql' ),
		'user_id' => get_current_user_id(),
		'note'    => 'Ručně nastaveno adminem.',
	];
	update_post_meta( $post_id, '_xevos_status_log', $log );

	// Capacity notifications.
	$skoleni_obj = get_field( 'skoleni', $post_id );
	$skoleni_id  = is_object( $skoleni_obj ) ? $skoleni_obj->ID : (int) $skoleni_obj;
	$termin_str  = get_field( 'termin', $post_id ) ?: '';

	if ( $skoleni_id && $termin_str && function_exists( 'xevos_increment_registration' ) ) {
		$terminy = get_field( 'terminy', $skoleni_id );
		if ( is_array( $terminy ) ) {
			foreach ( $terminy as $i => $t ) {
				$t_key = function_exists( 'xevos_termin_key' ) ? xevos_termin_key( $t ) : ( $t['datum'] ?? '' );
				if ( $t_key === $termin_str ) {
					xevos_increment_registration( $skoleni_id, $i );
					break;
				}
			}
		}
	}

	// Generate PDF invoice (class provided by xevos-comgate-payment plugin).
	// Guard: skip if invoice already generated (e.g. ComGate callback already ran).
	$attachments = [];
	$existing_invoice = get_post_meta( $post_id, '_xevos_invoice_number', true );
	if ( ! $existing_invoice && class_exists( 'Xevos_Invoice_Generator' ) ) {
		$invoice_path = Xevos_Invoice_Generator::generate( $post_id );
		if ( $invoice_path ) {
			$attachments[] = $invoice_path;
		}
	} elseif ( $existing_invoice ) {
		// Invoice already exists – attach it to the confirmation email.
		$existing_path = get_post_meta( $post_id, '_xevos_invoice_path', true );
		if ( $existing_path && file_exists( $existing_path ) ) {
			$attachments[] = $existing_path;
		}
	}

	// Send payment confirmation email.
	$email_addr = get_field( 'email', $post_id );
	if ( $email_addr && function_exists( 'xevos_send_email' ) ) {
		xevos_send_email(
			$email_addr,
			'Platba přijata – ' . ( $skoleni_id ? get_the_title( $skoleni_id ) : '' ),
			'payment-confirmation',
			[
				'jmeno'         => get_field( 'jmeno', $post_id ) ?: '',
				'nazev_skoleni' => $skoleni_id ? get_the_title( $skoleni_id ) : '',
				'cena'          => number_format( (float) ( get_field( 'castka', $post_id ) ?: 0 ), 0, ',', ' ' ),
				'termin'        => $termin_str,
				'misto'         => '',
				'typ'           => $skoleni_id ? ( get_field( 'typ', $skoleni_id ) ?: '' ) : '',
				'kontakt_email' => get_option( 'admin_email' ),
				'firma'         => function_exists( 'xevos_get_option' ) ? xevos_get_option( 'nazev_firmy', 'XEVOS' ) : 'XEVOS',
			],
			$attachments
		);
	}
}

/**
 * Make the pdf_faktura field read-only — show download link instead of file picker.
 */
add_filter( 'acf/prepare_field/name=pdf_faktura', 'xevos_pdf_faktura_readonly' );

function xevos_pdf_faktura_readonly( array $field ): array {
	$post_id      = acf_get_valid_post_id();
	$invoice_url  = get_post_meta( $post_id, '_xevos_invoice_url', true );
	$invoice_num  = get_post_meta( $post_id, '_xevos_invoice_number', true );

	// Replace the field with a read-only message.
	$field['type'] = 'message';
	if ( $invoice_url ) {
		$label = $invoice_num ? esc_html( $invoice_num ) : 'Stáhnout fakturu';
		$field['message'] = sprintf(
			'<a href="%s" target="_blank" class="button button-secondary">⬇ %s</a>',
			esc_url( $invoice_url ),
			$label
		);
	} else {
		$field['message'] = '<em style="color:#999;">Faktura bude vygenerována po nastavení stavu na Zaplaceno nebo po připsání částky na účet.</em>';
	}

	return $field;
}

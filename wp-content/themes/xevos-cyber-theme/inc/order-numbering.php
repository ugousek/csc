<?php
/**
 * Auto-numbering for Objednávky: OBJ-{YEAR}-{SEQUENCE}.
 * Capacity notifications at 80% and 100%.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Generate order number on post creation.
 */
add_action( 'wp_insert_post', 'xevos_auto_number_order', 10, 2 );

function xevos_auto_number_order( int $post_id, ?WP_Post $post ): void {
	if ( ! $post || 'objednavka' !== $post->post_type ) {
		return;
	}

	// Skip if order number already assigned.
	if ( get_post_meta( $post_id, '_xevos_order_number', true ) ) {
		return;
	}

	$year        = date( 'Y' );
	$option_key  = '_xevos_order_seq_' . $year;

	// Atomically increment the counter stored as a WP option.
	$seq = (int) get_option( $option_key, 0 ) + 1;
	update_option( $option_key, $seq, false );

	$order_number = sprintf( 'OBJ-%s-%04d', $year, $seq );

	// Store meta.
	update_post_meta( $post_id, '_xevos_order_year', $year );
	update_post_meta( $post_id, '_xevos_order_seq', $seq );
	update_post_meta( $post_id, '_xevos_order_number', $order_number );

	// Update title.
	wp_update_post( [
		'ID'         => $post_id,
		'post_title' => $order_number,
	] );
}

/**
 * Build a unique key for a term from its datum + cas_od.
 * Used as the stored value in orders to distinguish same-date terms.
 *
 * @param array $t Term row from ACF repeater.
 * @return string  e.g. "26.05.2026|08:00" or just "26.05.2026"
 */
function xevos_termin_key( array $t ): string {
	$key = $t['datum'] ?? '';
	if ( ! empty( $t['cas_od'] ) ) {
		$key .= '|' . $t['cas_od'];
	}
	return $key;
}

/**
 * Convert a stored termin key ("17.04.2026|14:00") into a human-readable display string
 * ("17.04.2026 | 14:00 – 15:30"). If $skoleni_id is provided, looks up cas_do from the
 * ACF terminy repeater on that post.
 *
 * @param string $termin_key  Stored key format "datum|cas_od" or just "datum".
 * @param int    $skoleni_id  Optional ID of the linked skoleni post to resolve cas_do.
 * @return string             Human-readable termin for emails / admin.
 */
function xevos_format_termin_display( string $termin_key, int $skoleni_id = 0 ): string {
	if ( $termin_key === '' ) {
		return '';
	}

	$parts  = explode( '|', $termin_key, 2 );
	$datum  = trim( $parts[0] ?? '' );
	$cas_od = isset( $parts[1] ) ? trim( $parts[1] ) : '';
	$cas_do = '';

	if ( $skoleni_id > 0 && function_exists( 'get_field' ) ) {
		$terminy = get_field( 'terminy', $skoleni_id );
		if ( is_array( $terminy ) ) {
			foreach ( $terminy as $t ) {
				$t_datum  = $t['datum']  ?? '';
				$t_cas_od = $t['cas_od'] ?? '';
				if ( $t_datum === $datum && ( $cas_od === '' || $t_cas_od === $cas_od ) ) {
					$cas_do = $t['cas_do'] ?? '';
					if ( $cas_od === '' ) {
						$cas_od = $t_cas_od;
					}
					break;
				}
			}
		}
	}

	if ( $datum !== '' && $cas_od !== '' && $cas_do !== '' ) {
		return $datum . ' | ' . $cas_od . ' – ' . $cas_do;
	}
	if ( $datum !== '' && $cas_od !== '' ) {
		return $datum . ' | ' . $cas_od;
	}
	return $datum;
}

/**
 * Count active (non-cancelled, non-refunded) registrations for a training term.
 *
 * @param int    $skoleni_id  Training post ID.
 * @param string $termin_datum Date string matching the term's 'datum' field.
 * @return int
 */
function xevos_count_active_registrations( int $skoleni_id, string $termin_datum ): int {
	$orders = get_posts( [
		'post_type'      => 'objednavka',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'fields'         => 'ids',
		'meta_query'     => [
			'relation' => 'AND',
			[
				'key'   => 'skoleni',
				'value' => $skoleni_id,
			],
			[
				'key'   => 'termin',
				'value' => $termin_datum,
			],
		],
	] );

	$count = 0;
	foreach ( $orders as $order_id ) {
		$stav = get_field( 'stav_platby', $order_id ) ?: 'pending';
		if ( ! in_array( $stav, [ 'cancelled', 'refunded' ], true ) ) {
			$count++;
		}
	}

	return $count;
}

/**
 * Check capacity thresholds and send admin notifications.
 * Does NOT store anything — count is derived from real orders.
 *
 * Call this after a successful order is created.
 *
 * @param int $skoleni_id Training post ID.
 * @param int $termin_index Index of the term in repeater.
 */
function xevos_increment_registration( int $skoleni_id, int $termin_index ): void {
	$terminy = get_field( 'terminy', $skoleni_id );
	if ( ! $terminy || ! isset( $terminy[ $termin_index ] ) ) {
		return;
	}

	$kapacita     = (int) ( $terminy[ $termin_index ]['kapacita'] ?? 0 );
	$termin_datum = $terminy[ $termin_index ]['datum'] ?? '';
	$new_count    = xevos_count_active_registrations( $skoleni_id, $termin_datum );

	if ( $kapacita <= 0 || ! $termin_datum ) {
		return;
	}

	$percent       = ( $new_count / $kapacita ) * 100;
	$prev_count    = $new_count - 1;
	$prev_percent  = $kapacita > 0 ? ( $prev_count / $kapacita ) * 100 : 0;
	$skoleni_title = get_the_title( $skoleni_id );
	$admin_email   = get_option( 'admin_email' );

	// 100% – full.
	if ( $new_count >= $kapacita && $prev_count < $kapacita ) {
		wp_mail(
			$admin_email,
			sprintf( '[XEVOS] Kapacita naplněna: %s (%s)', $skoleni_title, $termin_datum ),
			sprintf(
				"Školení \"%s\" – termín %s má naplněnou kapacitu (%d/%d).\n\nNové registrace nebudou možné.\n\nZkontrolovat: %s",
				$skoleni_title,
				$termin_datum,
				$new_count,
				$kapacita,
				admin_url( 'post.php?post=' . $skoleni_id . '&action=edit' )
			)
		);
	}
	// 80% – warning.
	elseif ( $percent >= 80 && $prev_percent < 80 ) {
		wp_mail(
			$admin_email,
			sprintf( '[XEVOS] 80%% kapacity: %s (%s)', $skoleni_title, $termin_datum ),
			sprintf(
				"Školení \"%s\" – termín %s dosáhlo 80%% kapacity (%d/%d).\n\nZbývá %d míst.\n\nZkontrolovat: %s",
				$skoleni_title,
				$termin_datum,
				$new_count,
				$kapacita,
				$kapacita - $new_count,
				admin_url( 'post.php?post=' . $skoleni_id . '&action=edit' )
			)
		);
	}
}

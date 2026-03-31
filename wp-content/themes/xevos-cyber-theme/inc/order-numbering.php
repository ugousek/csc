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
add_action( 'wp_insert_post', 'xevos_auto_number_order', 10, 3 );

function xevos_auto_number_order( int $post_id, WP_Post $post, bool $update ): void {
	if ( $update || 'objednavka' !== $post->post_type ) {
		return;
	}

	$year = date( 'Y' );

	// Get next sequence number for this year.
	$last = get_posts( [
		'post_type'      => 'objednavka',
		'posts_per_page' => 1,
		'post_status'    => 'any',
		'meta_key'       => '_xevos_order_year',
		'meta_value'     => $year,
		'orderby'        => 'meta_value_num',
		'meta_query'     => [
			[
				'key'     => '_xevos_order_seq',
				'compare' => 'EXISTS',
			],
		],
		'order'          => 'DESC',
		'fields'         => 'ids',
		'exclude'        => [ $post_id ],
	] );

	$seq = 1;
	if ( ! empty( $last ) ) {
		$last_seq = (int) get_post_meta( $last[0], '_xevos_order_seq', true );
		$seq      = $last_seq + 1;
	}

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
 * Increment registration count and check capacity.
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

	$current = (int) ( $terminy[ $termin_index ]['pocet_registraci'] ?? 0 );
	$kapacita = (int) ( $terminy[ $termin_index ]['kapacita'] ?? 0 );
	$new_count = $current + 1;

	// Update the repeater sub-field.
	update_sub_field( [ 'terminy', $termin_index + 1, 'pocet_registraci' ], $new_count, $skoleni_id );

	// Check capacity thresholds.
	if ( $kapacita > 0 ) {
		$percent = ( $new_count / $kapacita ) * 100;
		$skoleni_title = get_the_title( $skoleni_id );
		$termin_datum  = $terminy[ $termin_index ]['datum'] ?? '';
		$admin_email   = get_option( 'admin_email' );

		// 100% – full.
		if ( $new_count >= $kapacita ) {
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
		elseif ( $percent >= 80 && ( ( $current / $kapacita ) * 100 ) < 80 ) {
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
}

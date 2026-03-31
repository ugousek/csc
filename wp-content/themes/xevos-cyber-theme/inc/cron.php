<?php
/**
 * WP CRON: Scheduled email reminders for trainings.
 * Sends reminders 3 days and 1 day before training.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Schedule daily cron event on theme activation.
 */
add_action( 'after_switch_theme', 'xevos_schedule_reminder_cron' );

function xevos_schedule_reminder_cron(): void {
	if ( ! wp_next_scheduled( 'xevos_daily_training_reminders' ) ) {
		wp_schedule_event( strtotime( 'today 08:00' ), 'daily', 'xevos_daily_training_reminders' );
	}
}

/**
 * Clear cron on theme deactivation.
 */
add_action( 'switch_theme', 'xevos_clear_reminder_cron' );

function xevos_clear_reminder_cron(): void {
	wp_clear_scheduled_hook( 'xevos_daily_training_reminders' );
}

/**
 * Daily cron callback: check for trainings happening in 3 days or 1 day.
 * Optimized: pre-fetches all paid orders in one query, then matches in PHP.
 */
add_action( 'xevos_daily_training_reminders', 'xevos_send_training_reminders' );

function xevos_send_training_reminders(): void {
	$target_dates = [
		date( 'd.m.Y', strtotime( '+3 days' ) ) => 3,
		date( 'd.m.Y', strtotime( '+1 day' ) )  => 1,
	];

	// Pre-fetch ALL paid orders in one query (avoids N+1).
	$all_paid_orders = get_posts( [
		'post_type'      => 'objednavka',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'meta_query'     => [
			[
				'key'   => 'stav_platby',
				'value' => 'paid',
			],
		],
	] );

	// Index orders by skoleni_id + termin for fast lookup.
	$orders_index = [];
	foreach ( $all_paid_orders as $order ) {
		$sk  = get_field( 'skoleni', $order->ID );
		$sk_id = is_object( $sk ) ? $sk->ID : (int) $sk;
		$trm = get_field( 'termin', $order->ID ) ?: '';

		if ( $sk_id && $trm ) {
			$key = $sk_id . '|' . $trm;
			$orders_index[ $key ][] = $order;
		}
	}

	// Get all published školení.
	$skoleni_posts = get_posts( [
		'post_type'      => 'skoleni',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
	] );

	foreach ( $skoleni_posts as $skoleni ) {
		$terminy = get_field( 'terminy', $skoleni->ID );
		if ( ! is_array( $terminy ) || empty( $terminy ) ) {
			continue;
		}

		foreach ( $terminy as $termin ) {
			$datum = $termin['datum'] ?? '';
			if ( empty( $datum ) ) {
				continue;
			}

			// Check if this date matches any reminder target.
			if ( ! isset( $target_dates[ $datum ] ) ) {
				continue;
			}

			$days_before = $target_dates[ $datum ];

			// Lookup matching orders from pre-fetched index.
			$key = $skoleni->ID . '|' . $datum;
			$matching_orders = $orders_index[ $key ] ?? [];

			foreach ( $matching_orders as $order ) {
				$email_addr = get_field( 'email', $order->ID );
				$jmeno      = get_field( 'jmeno', $order->ID );

				if ( ! $email_addr ) {
					continue;
				}

				// Check if reminder already sent.
				$sent_key = "_reminder_sent_{$days_before}d_{$datum}";
				if ( get_post_meta( $order->ID, $sent_key, true ) ) {
					continue;
				}

				// Prepare data.
				$typ        = get_field( 'typ', $skoleni->ID ) ?: '';
				$misto_text = $termin['misto'] ?? '';
				$cas_text   = ( $termin['cas_od'] ?? '' ) . ' – ' . ( $termin['cas_do'] ?? '' );

				$poznamky = '';
				if ( $typ === 'online' ) {
					$poznamky = 'Připojte se přes odkaz, který vám bude zaslán na e-mail v den školení.';
				} else {
					$poznamky = 'Vezměte si s sebou notebook a poznámkový blok.';
				}

				xevos_send_email(
					$email_addr,
					sprintf( 'Připomenutí: %s za %d %s', $skoleni->post_title, $days_before, $days_before === 1 ? 'den' : 'dny' ),
					'reminder',
					[
						'jmeno'         => $jmeno ?: '',
						'nazev_skoleni' => $skoleni->post_title,
						'termin'        => $datum,
						'cas'           => $cas_text,
						'misto'         => $misto_text,
						'poznamky'      => $poznamky,
						'kontakt_email' => get_option( 'admin_email' ),
						'firma'         => xevos_get_option( 'nazev_firmy', 'XEVOS' ),
					]
				);

				// Mark as sent.
				update_post_meta( $order->ID, $sent_key, time() );
			}
		}
	}
}

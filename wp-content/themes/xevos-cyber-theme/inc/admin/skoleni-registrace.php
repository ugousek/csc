<?php
/**
 * Metabox: Registrace / objednávky u školení.
 * Zobrazuje tabulku registrovaných uživatelů přímo na editaci školení,
 * rozdělenou podle termínů.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

add_action( 'add_meta_boxes', 'xevos_skoleni_registrace_metabox' );
add_action( 'wp_ajax_xevos_remove_registration', 'xevos_remove_registration_ajax' );

/**
 * AJAX handler: Remove a user from a training registration.
 * Sets order status to 'cancelled' and decrements the registration count.
 */
function xevos_remove_registration_ajax(): void {
	check_ajax_referer( 'xevos_remove_registration', '_nonce' );

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( [ 'message' => 'Nedostatečná oprávnění.' ] );
	}

	$order_id = (int) ( $_POST['order_id'] ?? 0 );
	if ( ! $order_id || get_post_type( $order_id ) !== 'objednavka' ) {
		wp_send_json_error( [ 'message' => 'Neplatná objednávka.' ] );
	}

	$current_status = get_field( 'stav_platby', $order_id ) ?: 'pending';
	if ( $current_status === 'cancelled' ) {
		wp_send_json_error( [ 'message' => 'Registrace je již zrušena.' ] );
	}

	// Decrement registration count for the matching term.
	$skoleni_id = get_field( 'skoleni', $order_id );
	$termin_val = get_field( 'termin', $order_id );

	// Count is derived from real orders — no field to update.

	// Set status to cancelled.
	update_field( 'stav_platby', 'cancelled', $order_id );

	// Log the status change.
	$log = get_post_meta( $order_id, '_xevos_status_log', true ) ?: [];
	$log[] = [
		'from'    => $current_status,
		'to'      => 'cancelled',
		'date'    => current_time( 'mysql' ),
		'user_id' => get_current_user_id(),
		'note'    => 'Odstraněno z registrace adminem.',
	];
	update_post_meta( $order_id, '_xevos_status_log', $log );

	// Send cancellation email to the participant.
	$participant_email = get_field( 'email', $order_id );
	if ( $participant_email && function_exists( 'xevos_send_email' ) ) {
		$skoleni_title = $skoleni_id ? get_the_title( $skoleni_id ) : '';
		$castka        = (float) ( get_field( 'castka', $order_id ) ?: 0 );
		$typ_platby    = get_field( 'typ_registrace', $order_id ) ?: 'paid';
		xevos_send_email(
			$participant_email,
			'Zrušení účasti – ' . $skoleni_title,
			'cancellation',
			[
				'jmeno'         => get_field( 'jmeno', $order_id ) ?: '',
				'nazev_skoleni' => $skoleni_title,
				'termin'        => function_exists( 'xevos_format_termin_display' )
					? xevos_format_termin_display( (string) ( $termin_val ?: '' ), (int) $skoleni_id )
					: ( $termin_val ?: '' ),
				'cena'          => $castka > 0 ? number_format( $castka, 0, ',', ' ' ) : '',
				'refundace'     => ( $typ_platby === 'paid' && $current_status === 'paid' && $castka > 0 ),
				'skoleni_url'   => $skoleni_id ? ( get_permalink( $skoleni_id ) ?: '' ) : '',
				'kontakt_email' => get_option( 'admin_email' ),
				'firma'         => function_exists( 'xevos_get_option' ) ? xevos_get_option( 'nazev_firmy', 'XEVOS' ) : 'XEVOS',
			]
		);
	}

	wp_send_json_success( [ 'message' => 'Registrace byla zrušena.' ] );
}

function xevos_skoleni_registrace_metabox(): void {
	add_meta_box(
		'xevos_skoleni_registrace',
		__( 'Registrace', 'xevos-cyber' ),
		'xevos_skoleni_registrace_render',
		'skoleni',
		'normal',
		'low'
	);
}

function xevos_skoleni_registrace_render( WP_Post $post ): void {
	$orders = get_posts( [
		'post_type'      => 'objednavka',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'meta_query'     => [
			[
				'key'   => 'skoleni',
				'value' => $post->ID,
			],
		],
		'orderby'        => 'date',
		'order'          => 'DESC',
	] );

	$terminy = get_field( 'terminy', $post->ID );
	$uroven_labels = [
		'zakladni'  => 'Základní',
		'pokrocily' => 'Pokročilý',
		'expert'    => 'Expert',
	];

	if ( empty( $orders ) ) {
		echo '<p style="color:#999;">' . esc_html__( 'Zatím žádné registrace.', 'xevos-cyber' ) . '</p>';
		return;
	}

	$stav_labels = [
		'pending'            => [ 'label' => 'Čeká na platbu',   'color' => '#f59e0b' ],
		'invoice'            => [ 'label' => 'Čeká na fakturu',  'color' => '#8b5cf6' ],
		'invitation_pending' => [ 'label' => 'Čeká na pozvánku', 'color' => '#0ea5e9' ],
		'paid'               => [ 'label' => 'Zaplaceno',        'color' => '#10b981' ],
		'registered'         => [ 'label' => 'Registrováno',     'color' => '#8b5cf6' ],
		'cancelled'          => [ 'label' => 'Zrušeno',          'color' => '#ef4444' ],
		'refunded'           => [ 'label' => 'Refundováno',      'color' => '#6b7280' ],
	];

	$typ_labels = [
		'paid' => 'Placená',
		'free' => 'Zdarma',
	];

	// Group orders by termin date.
	$grouped = [];
	$no_termin = [];
	foreach ( $orders as $order ) {
		$termin = get_field( 'termin', $order->ID ) ?: '';
		if ( $termin ) {
			$grouped[ $termin ][] = $order;
		} else {
			$no_termin[] = $order;
		}
	}

	// Global stats — exclude cancelled and refunded.
	$total      = 0;
	$paid_count = 0;
	$free_count = 0;
	$revenue    = 0;

	foreach ( $orders as $o ) {
		$typ  = get_field( 'typ_registrace', $o->ID ) ?: 'paid';
		$stav = get_field( 'stav_platby', $o->ID ) ?: 'pending';
		if ( in_array( $stav, [ 'cancelled', 'refunded' ], true ) ) {
			continue;
		}
		$total++;
		if ( $typ === 'free' || $stav === 'registered' ) {
			$free_count++;
		} elseif ( $stav === 'paid' ) {
			$paid_count++;
			$revenue += (float) ( get_field( 'castka', $o->ID ) ?: 0 );
		}
	}

	// Summary bar.
	printf(
		'<div style="display:flex;gap:2rem;margin-bottom:1rem;padding:12px 16px;background:#f0f0f1;border-radius:4px;flex-wrap:wrap;">
			<strong>Celkem: %d</strong>
			<span style="color:#10b981;">Zaplaceno: %d</span>
			<span style="color:#8b5cf6;">Zdarma: %d</span>
			<span>Tržba: %s Kč</span>
		</div>',
		$total,
		$paid_count,
		$free_count,
		number_format( $revenue, 0, ',', ' ' )
	);

	// Export link.
	$export_url = add_query_arg( [
		'post_type'      => 'objednavka',
		'xevos_export'   => 'csv',
		'filter_skoleni' => $post->ID,
	], admin_url( 'edit.php' ) );

	printf(
		'<a href="%s" class="button button-secondary" style="margin-bottom:16px;">%s</a>',
		esc_url( wp_nonce_url( $export_url, 'xevos_export_csv' ) ),
		esc_html__( 'Exportovat CSV', 'xevos-cyber' )
	);

	// Render by termin.
	if ( is_array( $terminy ) ) {
		foreach ( $terminy as $ti => $t ) {
			$datum   = $t['datum'] ?? '';
			$cas_od  = $t['cas_od'] ?? '';
			$cas_do  = $t['cas_do'] ?? '';
			$misto   = $t['misto'] ?? '';
			$uroven  = $t['uroven'] ?? '';
			$termin_key_lookup = function_exists( 'xevos_termin_key' ) ? xevos_termin_key( $t ) : $datum;
			// Support both new format ("datum|cas_od") and old format ("datum") stored in orders.
			$termin_orders = $grouped[ $termin_key_lookup ] ?? $grouped[ $datum ] ?? [];

			$time_str = '';
			if ( $cas_od && $cas_do ) {
				$time_str = $cas_od . ' – ' . $cas_do;
			}

			// Termin heading.
			printf(
				'<div style="background:#23232d;color:#fff;padding:12px 16px;margin-top:16px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
					<strong style="font-size:15px;">%s</strong>',
				esc_html( $datum )
			);

			if ( $time_str ) {
				printf( '<span style="color:#94a3b8;">%s</span>', esc_html( $time_str ) );
			}
			if ( $misto ) {
				printf( '<span style="color:#94a3b8;">📍 %s</span>', esc_html( $misto ) );
			}
			if ( $uroven && isset( $uroven_labels[ $uroven ] ) ) {
				printf( '<span style="background:#3b82f6;color:#fff;padding:2px 8px;border-radius:3px;font-size:11px;">%s</span>', esc_html( $uroven_labels[ $uroven ] ) );
			}

			// Capacity badge — count active (non-cancelled/refunded) orders for this term.
			$kap = (int) ( $t['kapacita'] ?? 0 );
			$reg = 0;
			foreach ( $termin_orders as $to ) {
				$to_stav = get_field( 'stav_platby', $to->ID ) ?: 'pending';
				if ( ! in_array( $to_stav, [ 'cancelled', 'refunded' ], true ) ) {
					$reg++;
				}
			}
			$cap_color = ( $kap > 0 && $reg >= $kap ) ? '#ef4444' : '#10b981';
			printf(
				'<span style="margin-left:auto;background:%s;color:#fff;padding:2px 10px;border-radius:3px;font-size:12px;">%d / %d</span>',
				$cap_color,
				$reg,
				$kap
			);

			echo '</div>';

			if ( empty( $termin_orders ) ) {
				echo '<p style="padding:8px 16px;color:#999;font-style:italic;">Žádné registrace pro tento termín.</p>';
				continue;
			}

			xevos_render_orders_table( $termin_orders, $stav_labels, $typ_labels );

			// Remove from grouped so we can detect orphans (unset both formats).
			unset( $grouped[ $termin_key_lookup ], $grouped[ $datum ] );
		}
	}

	// Orders with unknown/unmatched termíny.
	$remaining = [];
	foreach ( $grouped as $orders_list ) {
		$remaining = array_merge( $remaining, $orders_list );
	}
	$remaining = array_merge( $remaining, $no_termin );

	if ( ! empty( $remaining ) ) {
		echo '<div style="background:#3d2a00;color:#fff;padding:12px 16px;margin-top:16px;"><strong>Bez přiřazeného termínu</strong></div>';
		xevos_render_orders_table( $remaining, $stav_labels, $typ_labels );
	}

	// Link to all orders.
	$all_url = add_query_arg( [
		'post_type'      => 'objednavka',
		'filter_skoleni' => $post->ID,
	], admin_url( 'edit.php' ) );

	printf(
		'<p style="margin-top:12px;"><a href="%s">%s &rarr;</a></p>',
		esc_url( $all_url ),
		esc_html__( 'Zobrazit v Objednávkách', 'xevos-cyber' )
	);

	// Inline JS for remove registration.
	$nonce = wp_create_nonce( 'xevos_remove_registration' );
	?>
	<script>
	(function(){
		document.querySelectorAll('.xevos-remove-registration').forEach(function(btn){
			btn.addEventListener('click', function(e){
				e.preventDefault();
				if (!confirm('Opravdu chcete odstranit tuto registraci? Stav bude změněn na "Zrušeno" a počet registrací bude snížen.')) {
					return;
				}
				var orderId = this.dataset.orderId;
				var row = this.closest('tr');
				var button = this;
				button.disabled = true;
				button.textContent = 'Ruším…';

				var data = new FormData();
				data.append('action', 'xevos_remove_registration');
				data.append('order_id', orderId);
				data.append('_nonce', '<?php echo esc_js( $nonce ); ?>');

				fetch(ajaxurl, { method: 'POST', body: data })
					.then(function(r){ return r.json(); })
					.then(function(res){
						if (res.success) {
							row.style.opacity = '0.5';
							button.textContent = 'Zrušeno';
							button.style.color = '#6b7280';
							button.style.borderColor = '#6b7280';
							// Update the status badge in the row.
							var statusCell = row.querySelectorAll('td')[6];
							if (statusCell) {
								statusCell.innerHTML = '<span style="background:#ef4444;color:#fff;padding:2px 8px;border-radius:3px;font-size:11px;">Zrušeno</span>';
							}
						} else {
							alert(res.data && res.data.message ? res.data.message : 'Chyba při rušení registrace.');
							button.disabled = false;
							button.textContent = 'Odstranit';
						}
					})
					.catch(function(){
						alert('Chyba při komunikaci se serverem.');
						button.disabled = false;
						button.textContent = 'Odstranit';
					});
			});
		});
	})();
	</script>
	<?php
}

/**
 * Render orders table (reusable for each termin group).
 */
function xevos_render_orders_table( array $orders, array $stav_labels, array $typ_labels ): void {
	echo '<table class="widefat striped" style="margin:0;">';
	echo '<thead><tr>';
	echo '<th>Datum</th><th>Jméno</th><th>Email</th><th>Firma</th><th>Typ</th><th>Částka</th><th>Stav</th><th></th>';
	echo '</tr></thead><tbody>';

	foreach ( $orders as $order ) {
		$id       = $order->ID;
		$jmeno    = get_field( 'jmeno', $id );
		$prijmeni = get_field( 'prijmeni', $id );
		$email    = get_field( 'email', $id );
		$firma    = get_field( 'firma', $id );
		$castka   = get_field( 'castka', $id );
		$stav     = get_field( 'stav_platby', $id ) ?: 'pending';
		$typ      = get_field( 'typ_registrace', $id ) ?: 'paid';
		$badge    = $stav_labels[ $stav ] ?? $stav_labels['pending'];

		echo '<tr>';
		printf( '<td>%s</td>', esc_html( get_the_date( 'd.m.Y', $id ) ) );
		printf( '<td><strong>%s</strong></td>', esc_html( trim( "$jmeno $prijmeni" ) ) );
		printf( '<td><a href="mailto:%s">%s</a></td>', esc_attr( $email ), esc_html( $email ) );
		printf( '<td>%s</td>', esc_html( $firma ?: '—' ) );
		printf(
			'<td><span style="background:%s;color:#fff;padding:2px 8px;border-radius:3px;font-size:11px;">%s</span></td>',
			$typ === 'free' ? '#8b5cf6' : '#3b82f6',
			esc_html( $typ_labels[ $typ ] ?? $typ )
		);
		if ( $typ === 'free' ) {
			echo '<td><em style="color:#999;">Zdarma</em></td>';
		} else {
			printf( '<td>%s</td>', $castka ? esc_html( number_format( (float) $castka, 0, ',', ' ' ) . ' Kč' ) : '—' );
		}
		printf(
			'<td><span style="background:%s;color:#fff;padding:2px 8px;border-radius:3px;font-size:11px;">%s</span></td>',
			esc_attr( $badge['color'] ),
			esc_html( $badge['label'] )
		);
		printf(
			'<td style="white-space:nowrap;"><a href="%s" class="button button-small">Detail</a> ',
			esc_url( get_edit_post_link( $id ) )
		);
		if ( $stav !== 'cancelled' && $stav !== 'refunded' ) {
			printf(
				'<button type="button" class="button button-small xevos-remove-registration" data-order-id="%d" style="color:#ef4444;border-color:#ef4444;">Odstranit</button>',
				$id
			);
		}
		echo '</td>';
		echo '</tr>';
	}

	echo '</tbody></table>';
}

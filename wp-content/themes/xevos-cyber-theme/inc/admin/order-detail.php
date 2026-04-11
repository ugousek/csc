<?php
/**
 * Custom order detail metabox — styled summary for objednavka edit page.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

add_action( 'add_meta_boxes', 'xevos_order_detail_metabox' );
add_action( 'admin_head', 'xevos_order_detail_css' );

function xevos_order_detail_metabox(): void {
	add_meta_box(
		'xevos_order_detail',
		'Detail objednávky',
		'xevos_order_detail_render',
		'objednavka',
		'normal',
		'high'
	);
}

function xevos_order_detail_css(): void {
	global $post;
	if ( ! $post || get_post_type( $post ) !== 'objednavka' ) {
		return;
	}
	?>
	<style>
	/* Hide default ACF metabox title */
	#acf-group_xevos_objednavka > h2 { display: none; }

	/* ── ACF fields: 2-column grid ── */
	#acf-group_xevos_objednavka .acf-fields {
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 0;
	}
	#acf-group_xevos_objednavka .acf-field {
		padding: 14px 18px;
		border-bottom: 1px solid #f0f0f1;
		border-right: 1px solid #f0f0f1;
		margin: 0;
	}
	/* Right column fields — no right border */
	#acf-group_xevos_objednavka .acf-field[data-name="prijmeni"],
	#acf-group_xevos_objednavka .acf-field[data-name="telefon"],
	#acf-group_xevos_objednavka .acf-field[data-name="ico"],
	#acf-group_xevos_objednavka .acf-field[data-name="castka"],
	#acf-group_xevos_objednavka .acf-field[data-name="stav_platby"] {
		border-right: none;
	}

	/* Full-width fields */
	#acf-group_xevos_objednavka .acf-field[data-name="dic"],
	#acf-group_xevos_objednavka .acf-field[data-name="fakturacni_adresa"],
	#acf-group_xevos_objednavka .acf-field[data-name="skoleni"],
	#acf-group_xevos_objednavka .acf-field[data-name="comgate_transaction_id"],
	#acf-group_xevos_objednavka .acf-field[data-name="datum_objednavky"],
	#acf-group_xevos_objednavka .acf-field[data-name="pdf_faktura"] {
		grid-column: 1 / -1;
		border-right: none;
	}

	/* Labels */
	#acf-group_xevos_objednavka .acf-label label {
		font-size: 11px;
		font-weight: 700;
		text-transform: uppercase;
		letter-spacing: .07em;
		color: #8c8f94;
	}
	#acf-group_xevos_objednavka .acf-label .acf-tip,
	#acf-group_xevos_objednavka .acf-label p { display: none; }

	/* Inputs */
	#acf-group_xevos_objednavka .acf-input input[type="text"],
	#acf-group_xevos_objednavka .acf-input input[type="email"],
	#acf-group_xevos_objednavka .acf-input input[type="number"],
	#acf-group_xevos_objednavka .acf-input select {
		border: 1px solid #dde1e7;
		border-radius: 4px;
		padding: 7px 10px;
		font-size: 13px;
		color: #1e293b;
		background: #fff;
		box-shadow: none;
		width: 100%;
		transition: border-color .15s;
	}
	#acf-group_xevos_objednavka .acf-input input:focus,
	#acf-group_xevos_objednavka .acf-input select:focus {
		border-color: #0073aa;
		outline: none;
		box-shadow: 0 0 0 1px #0073aa;
	}

	/* Stav select */
	#acf-group_xevos_objednavka .acf-field[data-name="stav_platby"] select {
		font-weight: 600;
	}

	/* Částka with prepend — flexbox místo ACF float layoutu */
	#acf-group_xevos_objednavka .acf-field[data-name="castka"] .acf-input {
		display: flex;
		align-items: stretch;
	}
	#acf-group_xevos_objednavka .acf-field[data-name="castka"] .acf-input-prepend {
		float: none;
		flex-shrink: 0;
		display: flex;
		align-items: center;
		padding: 0 10px;
		margin: 0;
		background: #f6f7f7;
		border: 1px solid #dde1e7;
		border-right: none;
		border-radius: 4px 0 0 4px;
		font-size: 13px;
		color: #64748b;
		min-height: unset;
		line-height: 1;
	}
	#acf-group_xevos_objednavka .acf-field[data-name="castka"] .acf-input-wrap {
		flex: 1;
		overflow: visible;
	}
	#acf-group_xevos_objednavka .acf-field[data-name="castka"] .acf-input-wrap input {
		border-radius: 0 4px 4px 0;
		width: 100%;
		height: 100%;
		border-left: none;
	}

	/* Termín — readonly appearance */
	#acf-group_xevos_objednavka .acf-field[data-name="termin"] input {
		pointer-events: none;
		background: #f6f7f7;
		color: #444;
	}

	/* Fakturační adresa — nested 3-column grid */
	#acf-group_xevos_objednavka .acf-field[data-name="fakturacni_adresa"] {
		border-bottom: 1px solid #f0f0f1;
	}
	#acf-group_xevos_objednavka .acf-field[data-name="fakturacni_adresa"] .acf-fields {
		display: grid;
		grid-template-columns: 2fr 2fr 1fr;
		gap: 12px;
		padding: 6px 0 0;
	}
	#acf-group_xevos_objednavka .acf-field[data-name="fakturacni_adresa"] .acf-fields .acf-field {
		padding: 0;
		border: none !important;
		grid-column: auto;
	}

	/* ── Summary card ── */
	.xevos-od { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; margin-bottom: 4px; }

	.xevos-od__header {
		display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
		padding: 16px 20px; background: #1e1e2e; border-radius: 6px 6px 0 0;
	}
	.xevos-od__number { font-size: 20px; font-weight: 700; color: #fff; }
	.xevos-od__date { color: #94a3b8; font-size: 13px; }
	.xevos-od__badge {
		display: inline-block; padding: 3px 12px; border-radius: 20px;
		font-size: 12px; font-weight: 600; letter-spacing: .04em; margin-left: auto;
	}

	.xevos-od__body {
		display: grid; grid-template-columns: 1fr 1fr;
		border: 1px solid #e0e0e0; border-top: none; border-radius: 0 0 6px 6px; overflow: hidden;
	}
	.xevos-od__section {
		padding: 18px 22px; border-right: 1px solid #e0e0e0; border-bottom: 1px solid #e0e0e0;
	}
	.xevos-od__section:nth-child(even) { border-right: none; }
	.xevos-od__section:nth-last-child(-n+2) { border-bottom: none; }
	.xevos-od__section--full { grid-column: 1 / -1; border-right: none; border-bottom: none; }
	.xevos-od__section-title {
		font-size: 10px; font-weight: 700; text-transform: uppercase;
		letter-spacing: .1em; color: #94a3b8; margin: 0 0 10px;
	}
	.xevos-od__row { display: flex; gap: 8px; margin-bottom: 5px; font-size: 13px; }
	.xevos-od__row:last-child { margin-bottom: 0; }
	.xevos-od__label { color: #64748b; min-width: 75px; flex-shrink: 0; }
	.xevos-od__value { color: #1e293b; font-weight: 500; }
	.xevos-od__value a { color: #0073aa; text-decoration: none; }
	.xevos-od__value a:hover { text-decoration: underline; }

	.xevos-od__amount { font-size: 26px; font-weight: 700; color: #1e293b; }
	.xevos-od__amount-sub { font-size: 12px; color: #94a3b8; margin-top: 2px; }

	.xevos-od__invoice-btn {
		display: inline-flex; align-items: center; gap: 8px; padding: 8px 18px;
		border-radius: 4px; background: #1e1e2e; color: #fff !important;
		font-size: 13px; font-weight: 600; text-decoration: none !important; transition: background .15s;
	}
	.xevos-od__invoice-btn:hover { background: #2d2d44; }
	.xevos-od__invoice-none { color: #94a3b8; font-size: 13px; font-style: italic; }

	.xevos-od__log { list-style: none; margin: 0; padding: 0; }
	.xevos-od__log li {
		display: flex; gap: 12px; align-items: flex-start;
		padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size: 12px;
	}
	.xevos-od__log li:last-child { border-bottom: none; }
	.xevos-od__log-date { color: #94a3b8; min-width: 130px; }
	.xevos-od__log-arrow { color: #cbd5e1; }
	</style>
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Replace pipe in termín input with space for display
		var terminInput = document.querySelector('#acf-group_xevos_objednavka .acf-field[data-name="termin"] input');
		if (terminInput) {
			terminInput.value = terminInput.value.replace('|', '  ');
		}
	});
	</script>
	<?php
}

function xevos_order_detail_render( WP_Post $post ): void {
	$id = $post->ID;

	$jmeno    = get_field( 'jmeno', $id ) ?: '';
	$prijmeni = get_field( 'prijmeni', $id ) ?: '';
	$email    = get_field( 'email', $id ) ?: '';
	$telefon  = get_field( 'telefon', $id ) ?: '';
	$firma    = get_field( 'firma', $id ) ?: '';
	$ico      = get_field( 'ico', $id ) ?: '';
	$dic      = get_field( 'dic', $id ) ?: '';
	$fa       = get_field( 'fakturacni_adresa', $id ) ?: [];
	$skoleni  = get_field( 'skoleni', $id );
	$termin   = get_field( 'termin', $id ) ?: '';
	$castka   = (float) ( get_field( 'castka', $id ) ?: 0 );
	$typ      = get_field( 'typ_registrace', $id ) ?: 'paid';
	$stav     = get_field( 'stav_platby', $id ) ?: 'pending';
	$datum    = get_field( 'datum_objednavky', $id ) ?: get_the_date( 'd.m.Y', $id );
	$order_number = get_post_meta( $id, '_xevos_order_number', true ) ?: '—';
	$comgate  = get_field( 'comgate_transaction_id', $id ) ?: '';
	$inv_url  = get_post_meta( $id, '_xevos_invoice_url', true );
	$inv_num  = get_post_meta( $id, '_xevos_invoice_number', true );
	$log      = get_post_meta( $id, '_xevos_status_log', true ) ?: [];

	$stav_map = [
		'pending'    => [ 'label' => 'Čeká na platbu',          'bg' => '#f59e0b', 'fg' => '#fff' ],
		'invoice'    => [ 'label' => 'Čeká na úhradu faktury',  'bg' => '#8b5cf6', 'fg' => '#fff' ],
		'paid'       => [ 'label' => 'Zaplaceno',               'bg' => '#10b981', 'fg' => '#fff' ],
		'registered' => [ 'label' => 'Registrováno (zdarma)',   'bg' => '#6366f1', 'fg' => '#fff' ],
		'cancelled'  => [ 'label' => 'Zrušeno',                 'bg' => '#ef4444', 'fg' => '#fff' ],
		'refunded'   => [ 'label' => 'Refundováno',             'bg' => '#6b7280', 'fg' => '#fff' ],
	];
	$stav_info    = $stav_map[ $stav ] ?? $stav_map['pending'];
	$skoleni_title = $skoleni ? get_the_title( $skoleni ) : '—';
	$skoleni_link  = $skoleni ? get_edit_post_link( is_object( $skoleni ) ? $skoleni->ID : $skoleni ) : '';
	$termin_display = str_replace( '|', ' ', $termin );

	echo '<div class="xevos-od">';

	// Header.
	printf(
		'<div class="xevos-od__header">
			<span class="xevos-od__number">%s</span>
			<span class="xevos-od__date">%s</span>
			<span class="xevos-od__badge" style="background:%s;color:%s;">%s</span>
		</div>',
		esc_html( $order_number ),
		esc_html( $datum ),
		esc_attr( $stav_info['bg'] ),
		esc_attr( $stav_info['fg'] ),
		esc_html( $stav_info['label'] )
	);

	echo '<div class="xevos-od__body">';

	// Kontakt.
	echo '<div class="xevos-od__section">';
	echo '<p class="xevos-od__section-title">Kontakt</p>';
	xevos_od_row( 'Jméno', trim( "$jmeno $prijmeni" ) );
	xevos_od_row( 'E-mail', $email ? '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>' : '', true );
	xevos_od_row( 'Telefon', $telefon ? '<a href="tel:' . esc_attr( $telefon ) . '">' . esc_html( $telefon ) . '</a>' : '', true );
	echo '</div>';

	// Fakturace.
	echo '<div class="xevos-od__section">';
	echo '<p class="xevos-od__section-title">Fakturace</p>';
	xevos_od_row( 'Firma', $firma );
	xevos_od_row( 'IČO', $ico );
	xevos_od_row( 'DIČ', $dic );
	$adresa = trim( implode( ', ', array_filter( [ $fa['ulice'] ?? '', $fa['mesto'] ?? '', $fa['psc'] ?? '' ] ) ) );
	xevos_od_row( 'Adresa', $adresa );
	echo '</div>';

	// Školení.
	echo '<div class="xevos-od__section">';
	echo '<p class="xevos-od__section-title">Školení</p>';
	xevos_od_row( 'Název', $skoleni_link ? '<a href="' . esc_url( $skoleni_link ) . '">' . esc_html( $skoleni_title ) . '</a>' : esc_html( $skoleni_title ), true );
	xevos_od_row( 'Termín', $termin_display );
	xevos_od_row( 'Typ', $typ === 'free' ? 'Zdarma' : 'Placená' );
	if ( $comgate ) {
		xevos_od_row( 'Comgate', $comgate );
	}
	echo '</div>';

	// Platba.
	echo '<div class="xevos-od__section">';
	echo '<p class="xevos-od__section-title">Platba</p>';
	if ( $typ === 'free' ) {
		echo '<div class="xevos-od__amount">Zdarma</div>';
	} else {
		printf( '<div class="xevos-od__amount">%s Kč</div>', esc_html( number_format( $castka, 0, ',', ' ' ) ) );
		echo '<div class="xevos-od__amount-sub">včetně DPH</div>';
	}
	echo '</div>';

	// Faktura.
	echo '<div class="xevos-od__section xevos-od__section--full">';
	echo '<p class="xevos-od__section-title">Faktura</p>';
	if ( $inv_url ) {
		printf(
			'<a href="%s" target="_blank" class="xevos-od__invoice-btn">
				<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
				%s
			</a>',
			esc_url( $inv_url ),
			esc_html( $inv_num ?: 'Stáhnout fakturu' )
		);
	} else {
		echo '<span class="xevos-od__invoice-none">Faktura bude vygenerována po nastavení stavu na Zaplaceno nebo po připsání částky na účet.</span>';
	}
	echo '</div>';

	// Historie stavů.
	if ( ! empty( $log ) ) {
		echo '<div class="xevos-od__section xevos-od__section--full" style="border-top:1px solid #e0e0e0;">';
		echo '<p class="xevos-od__section-title">Historie stavů</p>';
		echo '<ul class="xevos-od__log">';
		foreach ( array_reverse( $log ) as $entry ) {
			$from     = $stav_map[ $entry['from'] ?? '' ]['label'] ?? ( $entry['from'] ?? '?' );
			$to       = $stav_map[ $entry['to']   ?? '' ]['label'] ?? ( $entry['to']   ?? '?' );
			$date     = $entry['date'] ?? '';
			$note     = $entry['note'] ?? '';
			$user     = ! empty( $entry['user_id'] ) ? get_userdata( $entry['user_id'] ) : null;
			$user_str = $user ? $user->display_name : '';
			printf(
				'<li>
					<span class="xevos-od__log-date">%s</span>
					<span>%s <span class="xevos-od__log-arrow">→</span> <strong>%s</strong>%s%s</span>
				</li>',
				esc_html( $date ),
				esc_html( $from ),
				esc_html( $to ),
				$note ? ' — ' . esc_html( $note ) : '',
				$user_str ? ' <span style="color:#94a3b8;">(' . esc_html( $user_str ) . ')</span>' : ''
			);
		}
		echo '</ul></div>';
	}

	echo '</div></div>';
}

function xevos_od_row( string $label, string $value, bool $raw = false ): void {
	if ( $value === '' ) {
		return;
	}
	printf(
		'<div class="xevos-od__row"><span class="xevos-od__label">%s</span><span class="xevos-od__value">%s</span></div>',
		esc_html( $label ),
		$raw ? $value : esc_html( $value )
	);
}

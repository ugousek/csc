<?php
/**
 * Admin: Poptávky – sloupce, filtry, detail metabox, označení jako přečteno.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'xevos_is_feature_enabled' ) || ! xevos_is_feature_enabled( 'inquiries_admin' ) ) {
	return;
}

// ─── Sloupce v seznamu ────────────────────────────────────────────────────────
add_filter( 'manage_poptavka_posts_columns', function ( array $cols ): array {
	return [
		'cb'              => $cols['cb'],
		'popt_stav'       => '',
		'popt_typ'        => 'Typ',
		'popt_jmeno'      => 'Jméno',
		'popt_email'      => 'E-mail',
		'popt_telefon'    => 'Telefon',
		'popt_firma'      => 'Firma / Druh testu',
		'popt_zprava'     => 'Zpráva',
		'popt_datum'      => 'Přijato',
	];
} );

add_action( 'manage_poptavka_posts_custom_column', function ( string $col, int $post_id ): void {
	$typ        = get_post_meta( $post_id, '_poptavka_typ', true );
	$stav       = get_post_meta( $post_id, '_poptavka_stav', true ) ?: 'nova';
	$jmeno      = get_post_meta( $post_id, '_poptavka_jmeno', true );
	$prijmeni   = get_post_meta( $post_id, '_poptavka_prijmeni', true );
	$email      = get_post_meta( $post_id, '_poptavka_email', true );
	$telefon    = get_post_meta( $post_id, '_poptavka_telefon', true );
	$firma      = get_post_meta( $post_id, '_poptavka_firma', true );
	$druh       = get_post_meta( $post_id, '_poptavka_druh_testu', true );
	$zprava     = get_post_meta( $post_id, '_poptavka_zprava', true );
	$datum      = get_post_meta( $post_id, '_poptavka_datum', true );

	$typ_labels = [
		'kontakt'  => 'Kontakt',
		'poptavka' => 'Poptávka testování',
		'pozvanka' => 'Žádost o pozvánku',
	];
	$typ_colors = [
		'kontakt'  => '#2271b1',
		'poptavka' => '#d63638',
		'pozvanka' => '#8c3491',
	];

	switch ( $col ) {
		case 'popt_stav':
			$dot_color = $stav === 'nova' ? '#d63638' : '#646970';
			$title     = $stav === 'nova' ? 'Nová' : 'Přečteno';
			echo '<span title="' . esc_attr( $title ) . '" style="display:inline-block;width:10px;height:10px;border-radius:50%;background:' . esc_attr( $dot_color ) . ';"></span>';
			break;

		case 'popt_typ':
			$label = $typ_labels[ $typ ] ?? $typ;
			$color = $typ_colors[ $typ ] ?? '#50575e';
			echo '<span style="display:inline-block;padding:2px 8px;border-radius:3px;font-size:11px;font-weight:600;background:' . esc_attr( $color ) . '22;color:' . esc_attr( $color ) . ';border:1px solid ' . esc_attr( $color ) . '44;">' . esc_html( $label ) . '</span>';
			break;

		case 'popt_jmeno':
			echo '<strong>' . esc_html( trim( $jmeno . ' ' . $prijmeni ) ) . '</strong>';
			break;

		case 'popt_email':
			if ( $email ) {
				echo '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
			}
			break;

		case 'popt_telefon':
			echo esc_html( $telefon );
			break;

		case 'popt_firma':
			$val = $druh ?: $firma;
			echo esc_html( $val );
			break;

		case 'popt_zprava':
			echo '<span title="' . esc_attr( $zprava ) . '">' . esc_html( wp_trim_words( $zprava, 8, '…' ) ) . '</span>';
			break;

		case 'popt_datum':
			echo $datum ? esc_html( date_i18n( 'd.m.Y H:i', strtotime( $datum ) ) ) : '—';
			break;
	}
}, 10, 2 );

// Seřaditelný sloupec datum.
add_filter( 'manage_edit-poptavka_sortable_columns', function ( array $cols ): array {
	$cols['popt_datum'] = 'date';
	return $cols;
} );

// Výchozí řazení – nejnovější první.
add_action( 'pre_get_posts', function ( WP_Query $q ): void {
	if ( ! is_admin() || $q->get( 'post_type' ) !== 'poptavka' || ! $q->is_main_query() ) {
		return;
	}
	if ( ! $q->get( 'orderby' ) ) {
		$q->set( 'orderby', 'date' );
		$q->set( 'order', 'DESC' );
	}
} );

// ─── Filtr podle typu ─────────────────────────────────────────────────────────
add_action( 'restrict_manage_posts', function ( string $post_type ): void {
	if ( $post_type !== 'poptavka' ) {
		return;
	}
	$current = sanitize_text_field( $_GET['poptavka_typ'] ?? '' );
	$typy    = [
		''         => 'Všechny typy',
		'kontakt'  => 'Kontakt',
		'poptavka' => 'Poptávka testování',
		'pozvanka' => 'Žádost o pozvánku',
	];
	echo '<select name="poptavka_typ">';
	foreach ( $typy as $val => $label ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $val ), selected( $current, $val, false ), esc_html( $label ) );
	}
	echo '</select>';

	$cur_stav = sanitize_text_field( $_GET['poptavka_stav'] ?? '' );
	echo '<select name="poptavka_stav" style="margin-left:4px;">';
	foreach ( [ '' => 'Všechny stavy', 'nova' => 'Nové', 'prectena' => 'Přečteno', 'vyrizena' => 'Vyřízena' ] as $val => $label ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $val ), selected( $cur_stav, $val, false ), esc_html( $label ) );
	}
	echo '</select>';

	// Export button – passes current filters.
	$export_url = add_query_arg( [
		'post_type'      => 'poptavka',
		'xevos_export'   => 'poptavky_csv',
		'poptavka_typ'   => sanitize_text_field( wp_unslash( $_GET['poptavka_typ'] ?? '' ) ),
		'poptavka_stav'  => sanitize_text_field( wp_unslash( $_GET['poptavka_stav'] ?? '' ) ),
	], admin_url( 'edit.php' ) );
	printf(
		'<a href="%s" class="button" style="margin-left:8px;">Exportovat CSV</a>',
		esc_url( wp_nonce_url( $export_url, 'xevos_export_poptavky_csv' ) )
	);
} );

add_action( 'pre_get_posts', function ( WP_Query $q ): void {
	if ( ! is_admin() || $q->get( 'post_type' ) !== 'poptavka' || ! $q->is_main_query() ) {
		return;
	}
	$typ  = sanitize_text_field( $_GET['poptavka_typ'] ?? '' );
	$stav = sanitize_text_field( $_GET['poptavka_stav'] ?? '' );

	$meta = [];
	if ( $typ ) {
		$meta[] = [ 'key' => '_poptavka_typ', 'value' => $typ ];
	}
	if ( $stav ) {
		$meta[] = [ 'key' => '_poptavka_stav', 'value' => $stav ];
	}
	if ( $meta ) {
		$meta['relation'] = 'AND';
		$q->set( 'meta_query', $meta );
	}
} );

// ─── Detail metabox ───────────────────────────────────────────────────────────
add_action( 'add_meta_boxes', function (): void {
	add_meta_box(
		'poptavka_detail',
		'Detail poptávky',
		'xevos_poptavka_detail_metabox',
		'poptavka',
		'normal',
		'high'
	);
	add_meta_box(
		'poptavka_stav_box',
		'Stav',
		'xevos_poptavka_stav_metabox',
		'poptavka',
		'side',
		'high'
	);
} );

function xevos_poptavka_detail_metabox( WP_Post $post ): void {
	$typ      = get_post_meta( $post->ID, '_poptavka_typ', true );
	$jmeno    = get_post_meta( $post->ID, '_poptavka_jmeno', true );
	$prijmeni = get_post_meta( $post->ID, '_poptavka_prijmeni', true );
	$email    = get_post_meta( $post->ID, '_poptavka_email', true );
	$telefon  = get_post_meta( $post->ID, '_poptavka_telefon', true );
	$firma    = get_post_meta( $post->ID, '_poptavka_firma', true );
	$druh     = get_post_meta( $post->ID, '_poptavka_druh_testu', true );
	$zprava   = get_post_meta( $post->ID, '_poptavka_zprava', true );
	$datum    = get_post_meta( $post->ID, '_poptavka_datum', true );

	$rows = array_filter( [
		'Typ formuláře' => [
			'kontakt'  => 'Kontaktní formulář',
			'poptavka' => 'Poptávka kybernetického testování',
			'pozvanka' => 'Žádost o pozvánku na školení',
		][ $typ ] ?? $typ,
		'Přijato'      => $datum ? date_i18n( 'd.m.Y H:i', strtotime( $datum ) ) : '',
		'Jméno'        => trim( $jmeno . ' ' . $prijmeni ),
		'E-mail'       => $email ? '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>' : '',
		'Telefon'      => $telefon,
		'Firma'        => $firma,
		'Druh testu'   => $druh,
		'Zpráva'       => $zprava ? nl2br( esc_html( $zprava ) ) : '',
	] );

	echo '<table class="form-table" style="margin:0;">';
	foreach ( $rows as $label => $value ) {
		if ( $value === '' || $value === null ) {
			continue;
		}
		echo '<tr>';
		echo '<th style="width:160px;padding:8px 10px;font-weight:600;">' . esc_html( $label ) . '</th>';
		echo '<td style="padding:8px 10px;">' . wp_kses_post( $value ) . '</td>';
		echo '</tr>';
	}
	echo '</table>';

	// Označit jako přečteno automaticky při otevření.
	if ( get_post_meta( $post->ID, '_poptavka_stav', true ) === 'nova' ) {
		update_post_meta( $post->ID, '_poptavka_stav', 'prectena' );
	}
}

function xevos_poptavka_stav_metabox( WP_Post $post ): void {
	$stav = get_post_meta( $post->ID, '_poptavka_stav', true ) ?: 'nova';
	wp_nonce_field( 'xevos_poptavka_stav', 'xevos_poptavka_stav_nonce' );
	echo '<select name="poptavka_stav_select" style="width:100%;">';
	foreach ( [ 'nova' => 'Nová', 'prectena' => 'Přečteno', 'vyrízena' => 'Vyřízena' ] as $val => $label ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $val ), selected( $stav, $val, false ), esc_html( $label ) );
	}
	echo '</select>';
}

// Uložit stav ze side metaboxu.
add_action( 'save_post_poptavka', function ( int $post_id ): void {
	if (
		! isset( $_POST['xevos_poptavka_stav_nonce'] ) ||
		! wp_verify_nonce( $_POST['xevos_poptavka_stav_nonce'], 'xevos_poptavka_stav' ) ||
		( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
	) {
		return;
	}
	$stav = sanitize_text_field( $_POST['poptavka_stav_select'] ?? 'nova' );
	update_post_meta( $post_id, '_poptavka_stav', $stav );
} );

// ─── CSV export ───────────────────────────────────────────────────────────────
add_action( 'admin_init', function (): void {
	if (
		empty( $_GET['xevos_export'] ) ||
		$_GET['xevos_export'] !== 'poptavky_csv' ||
		( $_GET['post_type'] ?? '' ) !== 'poptavka' ||
		! current_user_can( 'manage_options' ) ||
		! wp_verify_nonce( $_GET['_wpnonce'] ?? '', 'xevos_export_poptavky_csv' )
	) {
		return;
	}

	$meta_query = [];
	$typ  = sanitize_text_field( wp_unslash( $_GET['poptavka_typ'] ?? '' ) );
	$stav = sanitize_text_field( wp_unslash( $_GET['poptavka_stav'] ?? '' ) );
	if ( $typ )  { $meta_query[] = [ 'key' => '_poptavka_typ',  'value' => $typ ]; }
	if ( $stav ) { $meta_query[] = [ 'key' => '_poptavka_stav', 'value' => $stav ]; }
	if ( count( $meta_query ) > 1 ) { $meta_query['relation'] = 'AND'; }

	$posts = get_posts( [
		'post_type'      => 'poptavka',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'meta_query'     => $meta_query ?: [],
	] );

	$typ_labels = [
		'kontakt'  => 'Kontakt',
		'poptavka' => 'Poptávka testování',
		'pozvanka' => 'Žádost o pozvánku',
	];
	$stav_labels = [
		'nova'     => 'Nová',
		'prectena' => 'Přečteno',
		'vyrizena' => 'Vyřízena',
	];

	$filename = 'poptavky-' . gmdate( 'Y-m-d' ) . '.csv';
	header( 'Content-Type: text/csv; charset=UTF-8' );
	header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
	header( 'Pragma: no-cache' );

	$output = fopen( 'php://output', 'w' );
	fprintf( $output, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) ); // BOM pro Excel.

	fputcsv( $output, [
		'Datum',
		'Typ',
		'Stav',
		'Jméno',
		'Příjmení',
		'E-mail',
		'Telefon',
		'Firma / Druh testu',
		'Zpráva',
	], ';' );

	foreach ( $posts as $post ) {
		$id       = $post->ID;
		$typ_val  = get_post_meta( $id, '_poptavka_typ', true );
		$stav_val = get_post_meta( $id, '_poptavka_stav', true ) ?: 'nova';
		$firma    = get_post_meta( $id, '_poptavka_firma', true );
		$druh     = get_post_meta( $id, '_poptavka_druh_testu', true );
		$datum    = get_post_meta( $id, '_poptavka_datum', true );

		fputcsv( $output, [
			$datum ? date_i18n( 'd.m.Y H:i', strtotime( $datum ) ) : get_the_date( 'd.m.Y H:i', $id ),
			$typ_labels[ $typ_val ] ?? $typ_val,
			$stav_labels[ $stav_val ] ?? $stav_val,
			get_post_meta( $id, '_poptavka_jmeno', true ),
			get_post_meta( $id, '_poptavka_prijmeni', true ),
			get_post_meta( $id, '_poptavka_email', true ),
			get_post_meta( $id, '_poptavka_telefon', true ),
			$druh ?: $firma,
			get_post_meta( $id, '_poptavka_zprava', true ),
		], ';' );
	}

	fclose( $output );
	exit;
} );

// ─── Počet nových poptávek v menu (badge) ─────────────────────────────────────
add_action( 'admin_menu', function (): void {
	global $menu;
	$nova = (int) ( new WP_Query( [
		'post_type'      => 'poptavka',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'meta_query'     => [ [ 'key' => '_poptavka_stav', 'value' => 'nova' ] ],
		'fields'         => 'ids',
	] ) )->found_posts;

	if ( $nova < 1 ) {
		return;
	}
	foreach ( $menu as &$item ) {
		if ( isset( $item[2] ) && $item[2] === 'edit.php?post_type=poptavka' ) {
			$item[0] .= ' <span class="awaiting-mod">' . $nova . '</span>';
			break;
		}
	}
}, 999 );

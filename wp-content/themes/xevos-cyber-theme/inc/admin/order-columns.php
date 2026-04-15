<?php
/**
 * Custom admin columns for Objednávky CPT.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'manage_objednavka_posts_columns', 'xevos_objednavka_columns' );
add_action( 'manage_objednavka_posts_custom_column', 'xevos_objednavka_column_content', 10, 2 );
add_filter( 'manage_edit-objednavka_sortable_columns', 'xevos_objednavka_sortable_columns' );

function xevos_objednavka_columns( array $columns ): array {
	return [
		'cb'         => $columns['cb'],
		'title'      => __( 'Číslo objednávky', 'xevos-cyber' ),
		'ob_jmeno'   => __( 'Jméno', 'xevos-cyber' ),
		'ob_email'   => __( 'Email', 'xevos-cyber' ),
		'ob_firma'   => __( 'Firma', 'xevos-cyber' ),
		'ob_skoleni' => __( 'Školení', 'xevos-cyber' ),
		'ob_termin'  => __( 'Termín', 'xevos-cyber' ),
		'ob_pocet'   => __( 'Počet', 'xevos-cyber' ),
		'ob_typ'     => __( 'Typ', 'xevos-cyber' ),
		'ob_castka'  => __( 'Částka', 'xevos-cyber' ),
		'ob_stav'    => __( 'Stav', 'xevos-cyber' ),
		'date'       => __( 'Datum', 'xevos-cyber' ),
	];
}

function xevos_objednavka_sortable_columns( array $columns ): array {
	$columns['ob_castka'] = 'ob_castka';
	$columns['ob_stav']   = 'ob_stav';
	$columns['ob_typ']    = 'ob_typ';
	return $columns;
}

function xevos_objednavka_column_content( string $column, int $post_id ): void {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	switch ( $column ) {
		case 'ob_jmeno':
			$jmeno    = get_field( 'jmeno', $post_id );
			$prijmeni = get_field( 'prijmeni', $post_id );
			echo esc_html( trim( "$jmeno $prijmeni" ) );
			break;

		case 'ob_email':
			$email = get_field( 'email', $post_id );
			echo $email ? '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>' : '—';
			break;

		case 'ob_firma':
			echo esc_html( get_field( 'firma', $post_id ) ?: '—' );
			break;

		case 'ob_skoleni':
			$skoleni = get_field( 'skoleni', $post_id );
			if ( $skoleni ) {
				printf(
					'<a href="%s">%s</a>',
					esc_url( get_edit_post_link( $skoleni->ID ) ),
					esc_html( $skoleni->post_title )
				);
			} else {
				echo '—';
			}
			break;

		case 'ob_termin':
			$termin_raw  = (string) ( get_field( 'termin', $post_id ) ?: '' );
			$skoleni_rel = get_field( 'skoleni', $post_id );
			$skoleni_id  = is_object( $skoleni_rel ) ? (int) $skoleni_rel->ID : (int) $skoleni_rel;
			$formatted   = function_exists( 'xevos_format_termin_display' )
				? xevos_format_termin_display( $termin_raw, $skoleni_id )
				: $termin_raw;
			echo $formatted !== '' ? esc_html( $formatted ) : '—';
			break;

		case 'ob_pocet':
			$pocet = (int) ( get_field( 'pocet', $post_id ) ?: 1 );
			echo '<strong>' . esc_html( (string) $pocet ) . '</strong>';
			break;

		case 'ob_typ':
			$typ = get_field( 'typ_registrace', $post_id ) ?: 'paid';
			$badges = [
				'paid' => [ 'label' => 'Placená', 'color' => '#3b82f6' ],
				'free' => [ 'label' => 'Zdarma', 'color' => '#8b5cf6' ],
			];
			$badge = $badges[ $typ ] ?? $badges['paid'];
			printf(
				'<span style="background:%s;color:#fff;padding:2px 8px;border-radius:3px;font-size:12px;">%s</span>',
				esc_attr( $badge['color'] ),
				esc_html( $badge['label'] )
			);
			break;

		case 'ob_castka':
			$castka = get_field( 'castka', $post_id );
			$typ    = get_field( 'typ_registrace', $post_id );
			if ( $typ === 'free' ) {
				echo '<em style="color:#94a3b8;">Zdarma</em>';
			} else {
				echo $castka ? esc_html( number_format( (float) $castka, 0, ',', ' ' ) . ' Kč' ) : '—';
			}
			break;

		case 'ob_stav':
			$stav = get_field( 'stav_platby', $post_id ) ?: 'pending';
			$badges = [
				'pending'            => [ 'label' => 'Čeká na platbu',   'color' => '#f59e0b' ],
				'invoice'            => [ 'label' => 'Čeká na fakturu',  'color' => '#8b5cf6' ],
				'invitation_pending' => [ 'label' => 'Čeká na pozvánku', 'color' => '#0ea5e9' ],
				'paid'               => [ 'label' => 'Zaplaceno',        'color' => '#10b981' ],
				'registered'         => [ 'label' => 'Registrováno',     'color' => '#8b5cf6' ],
				'cancelled'          => [ 'label' => 'Zrušeno',          'color' => '#ef4444' ],
				'refunded'           => [ 'label' => 'Refundováno',      'color' => '#6b7280' ],
			];
			$badge = $badges[ $stav ] ?? $badges['pending'];
			printf(
				'<span style="background:%s;color:#fff;padding:2px 8px;border-radius:3px;font-size:12px;">%s</span>',
				esc_attr( $badge['color'] ),
				esc_html( $badge['label'] )
			);
			break;
	}
}

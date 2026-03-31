<?php
/**
 * Custom admin columns for Objednávky CPT.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'manage_objednavka_posts_columns', 'xevos_objednavka_columns' );
add_action( 'manage_objednavka_posts_custom_column', 'xevos_objednavka_column_content', 10, 2 );

function xevos_objednavka_columns( array $columns ): array {
	$new = [
		'cb'         => $columns['cb'],
		'title'      => __( 'Číslo objednávky', 'xevos-cyber' ),
		'ob_jmeno'   => __( 'Jméno', 'xevos-cyber' ),
		'ob_email'   => __( 'Email', 'xevos-cyber' ),
		'ob_firma'   => __( 'Firma', 'xevos-cyber' ),
		'ob_skoleni' => __( 'Školení', 'xevos-cyber' ),
		'ob_termin'  => __( 'Termín', 'xevos-cyber' ),
		'ob_castka'  => __( 'Částka', 'xevos-cyber' ),
		'ob_stav'    => __( 'Stav platby', 'xevos-cyber' ),
		'date'       => __( 'Datum', 'xevos-cyber' ),
	];

	return $new;
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
			echo esc_html( get_field( 'email', $post_id ) ?: '—' );
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
			echo esc_html( get_field( 'termin', $post_id ) ?: '—' );
			break;

		case 'ob_castka':
			$castka = get_field( 'castka', $post_id );
			echo $castka ? esc_html( number_format( (float) $castka, 0, ',', ' ' ) . ' Kč' ) : '—';
			break;

		case 'ob_stav':
			$stav = get_field( 'stav_platby', $post_id ) ?: 'pending';
			$badges = [
				'pending'   => [ 'label' => 'Čeká na platbu', 'color' => '#f59e0b' ],
				'paid'      => [ 'label' => 'Zaplaceno', 'color' => '#10b981' ],
				'cancelled' => [ 'label' => 'Zrušeno', 'color' => '#ef4444' ],
				'refunded'  => [ 'label' => 'Refundováno', 'color' => '#6b7280' ],
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

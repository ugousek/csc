<?php
/**
 * Admin: Lektoři – sloupce a inline styly.
 * Načítá se vždy, ale render logic projde jen když je feature aktivní.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'xevos_is_feature_enabled' ) || ! xevos_is_feature_enabled( 'speaker_database' ) ) {
	return;
}

// ─── Sloupce v seznamu ────────────────────────────────────────────────────────
add_filter( 'manage_lektor_posts_columns', function ( array $cols ): array {
	return [
		'cb'            => $cols['cb'],
		'lekt_foto'     => '',
		'title'         => 'Jméno',
		'lekt_pozice'   => 'Pozice',
		'lekt_email'    => 'E-mail',
		'lekt_linkedin' => 'LinkedIn',
	];
} );

add_action( 'manage_lektor_posts_custom_column', function ( string $col, int $post_id ): void {
	switch ( $col ) {
		case 'lekt_foto':
			$foto_id = get_field( 'lektor_foto', $post_id );
			if ( $foto_id ) {
				$src = wp_get_attachment_image_url( (int) $foto_id, 'thumbnail' );
				if ( $src ) {
					echo '<img src="' . esc_url( $src ) . '" width="40" height="40" style="border-radius:50%;object-fit:cover;display:block;" alt="">';
				}
			} else {
				echo '<span class="dashicons dashicons-admin-users" style="font-size:32px;color:#ccc;width:40px;height:40px;line-height:40px;"></span>';
			}
			break;

		case 'lekt_pozice':
			$pozice = get_field( 'lektor_pozice', $post_id );
			echo esc_html( $pozice ?: '—' );
			break;

		case 'lekt_email':
			$email = get_field( 'lektor_email', $post_id );
			if ( $email ) {
				echo '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
			} else {
				echo '—';
			}
			break;

		case 'lekt_linkedin':
			$url = get_field( 'lektor_linkedin', $post_id );
			if ( $url ) {
				echo '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">LinkedIn ↗</a>';
			} else {
				echo '—';
			}
			break;
	}
}, 10, 2 );

// Seřaditelný sloupec jméno (title).
add_filter( 'manage_edit-lektor_sortable_columns', function ( array $cols ): array {
	$cols['title'] = 'title';
	return $cols;
} );

// ─── Inline styly pro list stránku ───────────────────────────────────────────
add_action( 'admin_head', function (): void {
	$screen = get_current_screen();
	if ( ! $screen || $screen->post_type !== 'lektor' ) {
		return;
	}
	?>
	<style>
		.column-lekt_foto   { width: 52px; }
		.column-lekt_pozice { width: 220px; }
		.column-lekt_email  { width: 220px; }
		.column-lekt_linkedin { width: 100px; }
	</style>
	<?php
} );

<?php

/**
 * Admin stránka: Restricted mode.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

// ─── Zpracování formuláře ─────────────────────────────────────────────────────

add_action( 'admin_init', function () {
	if ( empty( $_POST['xevos_restricted_nonce'] ) ) return;
	if ( ! wp_verify_nonce( $_POST['xevos_restricted_nonce'], 'xevos_restricted_save' ) ) return;
	if ( ! current_user_can( 'manage_options' ) ) return;

	update_option( 'xevos_restricted_enabled', isset( $_POST['xevos_restricted_enabled'] ) ? 1 : 0 );
	update_option( 'xevos_restricted_slug',    sanitize_text_field( $_POST['xevos_restricted_slug'] ?? '' ) );

	wp_safe_redirect( admin_url( 'admin.php?page=xevos-restricted-mode&updated=1' ) );
	exit;
} );

// ─── Registrace menu ──────────────────────────────────────────────────────────

add_action( 'admin_menu', function () {
	add_menu_page(
		'Restricted mode',
		'Restricted mode',
		'manage_options',
		'xevos-restricted-mode',
		'xevos_render_restricted_mode_page',
		'dashicons-lock',
		4
	);
} );

// ─── Inline styly ─────────────────────────────────────────────────────────────

add_action( 'admin_head', function () {
	$screen = get_current_screen();
	if ( ! $screen || $screen->id !== 'toplevel_page_xevos-restricted-mode' ) return;
	?>
	<style>
		.xevos-rm-wrap { max-width: 680px; padding: 24px 0; }
		.xevos-rm-wrap h1 { display: flex; align-items: center; gap: 10px; font-size: 22px; margin-bottom: 6px; }
		.xevos-rm-intro { color: #646970; font-size: 14px; margin-bottom: 32px; line-height: 1.6; }

		.xevos-rm-card {
			background: #fff;
			border: 1px solid #dcdcde;
			border-radius: 6px;
			padding: 28px 28px 24px;
		}
		.xevos-rm-card.active { border-color: #f0b849; }

		.xevos-rm-status {
			display: inline-flex; align-items: center; gap: 6px;
			font-size: 11px; font-weight: 600; text-transform: uppercase;
			letter-spacing: 0.5px; padding: 3px 10px; border-radius: 20px;
			margin-bottom: 20px;
		}
		.xevos-rm-status.on  { background: #fef9ec; color: #996800; border: 1px solid #f0d063; }
		.xevos-rm-status.off { background: #f0f6ff; color: #2271b1; border: 1px solid #b3d3ee; }

		.xevos-rm-field { margin-bottom: 20px; }
		.xevos-rm-field label { display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; }
		.xevos-rm-field .description { font-size: 12px; color: #646970; margin-top: 5px; }

		.xevos-rm-toggle-row {
			display: flex; align-items: center; gap: 10px;
			padding: 14px 16px;
			background: #f6f7f7; border-radius: 4px; margin-bottom: 24px;
		}
		.xevos-rm-toggle-row label { font-size: 13px; font-weight: 600; cursor: pointer; margin: 0; }

		.xevos-rm-select { width: 100%; max-width: 420px; font-size: 14px; }

		.xevos-rm-footer {
			display: flex; align-items: center; gap: 14px;
			padding-top: 20px; border-top: 1px solid #f0f0f1; margin-top: 4px;
		}
		.xevos-rm-warning {
			display: flex; align-items: flex-start; gap: 8px;
			background: #fff8e5; border: 1px solid #f0d063; border-radius: 4px;
			padding: 10px 14px; margin-bottom: 24px; font-size: 13px; color: #7a5800;
		}
		.xevos-rm-warning .dashicons { flex-shrink: 0; font-size: 18px; width: 18px; height: 18px; margin-top: 1px; }
	</style>
	<?php
} );

// ─── Render ───────────────────────────────────────────────────────────────────

function xevos_render_restricted_mode_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) return;

	$enabled      = (bool) get_option( 'xevos_restricted_enabled', 0 );
	$saved_slug   = (string) get_option( 'xevos_restricted_slug', '' );
	$updated      = isset( $_GET['updated'] );

	/* Načti všechna školení pro dropdown */
	$skoleni_posts = get_posts( [
		'post_type'      => 'skoleni',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	] );
	?>
	<div class="wrap xevos-rm-wrap">

		<h1>
			<span class="dashicons dashicons-lock"></span>
			Restricted mode
		</h1>
		<p class="xevos-rm-intro">
			Když je režim zapnutý, návštěvníci mohou přistoupit pouze na vybrané školení.<br>
			Vše ostatní je přesměrováno. Navigace a footer jsou automaticky skryty.
		</p>

		<?php if ( $updated ) : ?>
			<div class="notice notice-success is-dismissible"><p><strong>Nastavení bylo uloženo.</strong></p></div>
		<?php endif; ?>

		<form method="post">
			<?php wp_nonce_field( 'xevos_restricted_save', 'xevos_restricted_nonce' ); ?>

			<div class="xevos-rm-card <?php echo $enabled ? 'active' : ''; ?>">

				<span class="xevos-rm-status <?php echo $enabled ? 'on' : 'off'; ?>">
					<span class="dashicons <?php echo $enabled ? 'dashicons-lock' : 'dashicons-unlock'; ?>"></span>
					<?php echo $enabled ? 'Zapnuto' : 'Vypnuto'; ?>
				</span>

				<?php if ( $enabled && ! $saved_slug ) : ?>
					<div class="xevos-rm-warning">
						<span class="dashicons dashicons-warning"></span>
						Restricted mode je zapnutý, ale není vybráno žádné školení — všichni jsou přesměrováni na homepage.
					</div>
				<?php endif; ?>

				<!-- Toggle zapnout/vypnout -->
				<div class="xevos-rm-toggle-row">
					<input type="checkbox" id="xevos_restricted_enabled" name="xevos_restricted_enabled" value="1" <?php checked( $enabled ); ?>>
					<label for="xevos_restricted_enabled">Zapnout Restricted mode</label>
				</div>

				<!-- Výběr školení -->
				<div class="xevos-rm-field">
					<label for="xevos_restricted_slug">Povolené školení</label>
					<?php if ( $skoleni_posts ) : ?>
						<select name="xevos_restricted_slug" id="xevos_restricted_slug" class="xevos-rm-select">
							<option value="">— vyberte školení —</option>
							<?php foreach ( $skoleni_posts as $post ) : ?>
								<option value="<?php echo esc_attr( $post->post_name ); ?>" <?php selected( $saved_slug, $post->post_name ); ?>>
									<?php echo esc_html( $post->post_title ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					<?php else : ?>
						<p style="color:#d63638; font-size:13px;">Žádná publikovaná školení nenalezena.</p>
						<input type="hidden" name="xevos_restricted_slug" value="">
					<?php endif; ?>
					<?php if ( $saved_slug ) :
						$post = get_page_by_path( $saved_slug, OBJECT, 'skoleni' );
						if ( $post ) : ?>
							<p class="description">
								Aktuálně: <a href="<?php echo esc_url( get_permalink( $post ) ); ?>" target="_blank"><?php echo esc_html( get_permalink( $post ) ); ?></a>
							</p>
						<?php endif;
					endif; ?>
				</div>

				<div class="xevos-rm-footer">
					<?php submit_button( 'Uložit', 'primary', 'submit', false ); ?>
				</div>

			</div>
		</form>
	</div>
	<?php
}

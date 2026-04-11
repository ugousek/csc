<?php
/**
 * Prémiové funkce – přehled rozšíření s možností aktivace.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

// ─── Helper: je daná prémiová funkce aktivní? ────────────────────────────────
function xevos_is_feature_enabled( string $key ): bool {
	return (bool) get_option( 'xevos_feature_' . $key . '_enabled', 0 );
}

// ─── Zpracování POST (toggle menu + toggle funkcí) ───────────────────────────
add_action( 'admin_init', function () {
	if ( empty( $_POST['xevos_premium_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['xevos_premium_nonce'], 'xevos_premium_save' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Viditelnost menu.
	update_option( 'xevos_premium_menu_visible', isset( $_POST['xevos_premium_visible'] ) ? 1 : 0 );

	// Stav jednotlivých funkcí.
	foreach ( xevos_get_premium_features() as $feature ) {
		$key = $feature['key'];
		update_option( 'xevos_feature_' . $key . '_enabled', isset( $_POST[ 'feature_' . $key ] ) ? 1 : 0 );
	}

	wp_safe_redirect( admin_url( 'admin.php?page=xevos-premium-features&updated=1' ) );
	exit;
} );

// ─── Registrace menu ─────────────────────────────────────────────────────────
add_action( 'admin_menu', function () {
	$visible = (bool) get_option( 'xevos_premium_menu_visible', 1 );

	if ( $visible ) {
		add_menu_page(
			'Prémiové funkce',
			'Prémiové funkce',
			'manage_options',
			'xevos-premium-features',
			'xevos_render_premium_features_page',
			'dashicons-star-filled',
			9
		);
	} else {
		add_submenu_page(
			null,
			'Prémiové funkce',
			'Prémiové funkce',
			'manage_options',
			'xevos-premium-features',
			'xevos_render_premium_features_page'
		);
	}
} );

// ─── Inline styly ────────────────────────────────────────────────────────────
add_action( 'admin_head', function () {
	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}
	// Stránka může mít ID toplevel_page_… nebo admin_page_… podle toho, zda je v menu.
	if ( $screen->id !== 'toplevel_page_xevos-premium-features' && $screen->id !== 'admin_page_xevos-premium-features' ) {
		return;
	}
	?>
	<style>
		.xevos-premium-wrap { max-width: 900px; padding: 24px 0; }
		.xevos-premium-wrap h1 { display: flex; align-items: center; gap: 10px; font-size: 22px; margin-bottom: 6px; }
		.xevos-premium-intro { color: #646970; font-size: 14px; margin-bottom: 32px; }

		/* Lišta viditelnosti menu */
		.xevos-visibility-bar {
			display: flex; align-items: center; gap: 14px;
			background: #fff; border: 1px solid #dcdcde; border-radius: 6px;
			padding: 14px 18px; margin-bottom: 32px; font-size: 13px;
		}
		.xevos-visibility-bar .description { color: #646970; margin: 0; }

		/* Checkbox label */
		.xevos-toggle-label { display: inline-flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; }

		/* Mřížka karet */
		.xevos-feature-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 20px; }
		.xevos-feature-card {
			background: #fff; border: 1px solid #dcdcde; border-radius: 6px;
			padding: 24px; position: relative;
		}
		.xevos-feature-card.active { border-color: #68de7c; }

		/* Badge stavu */
		.xevos-feature-badge {
			display: inline-flex; align-items: center; gap: 5px; font-size: 11px;
			font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
			padding: 3px 10px; border-radius: 20px; margin-bottom: 14px;
		}
		.badge-locked  { background: #fef9ec; color: #996800; border: 1px solid #f0d063; }
		.badge-active  { background: #edfaef; color: #1a7a2e; border: 1px solid #68de7c; }

		.xevos-feature-card h2 { font-size: 16px; margin: 0 0 10px; color: #1d2327; }
		.xevos-feature-card p  { font-size: 13px; color: #50575e; margin: 0 0 16px; line-height: 1.6; }
		.xevos-feature-card ul { font-size: 13px; color: #50575e; margin: 0 0 20px; padding-left: 18px; line-height: 1.8; }
		.xevos-feature-card .dashicons { font-size: 16px; width: 16px; height: 16px; vertical-align: middle; }

		/* Toggle funkce v kartě */
		.xevos-feature-footer {
			display: flex; align-items: center; justify-content: space-between;
			padding-top: 16px; border-top: 1px solid #f0f0f1; margin-top: 4px;
		}
		.xevos-feature-footer .xevos-toggle-label strong { font-size: 13px; }
		.xevos-feature-toggle-active { color: #1a7a2e; font-size: 13px; font-weight: 600; }
		.xevos-feature-toggle-locked { color: #996800; font-size: 13px; font-weight: 600; }

		/* Tlačítko Uložit */
		.xevos-premium-save { margin-top: 28px; }
	</style>
	<?php
} );

// ─── Definice funkcí ─────────────────────────────────────────────────────────
function xevos_get_premium_features(): array {
	return [
		[
			'key'         => 'invoice_payment',
			'label'       => 'Platba na fakturu',
			'description' => 'Účastník si při objednávce může zvolit platbu na fakturu místo okamžité online platby. Systém vygeneruje zálohovou fakturu, odešle ji e-mailem a objednávka čeká na ruční potvrzení platby.',
			'benefits'    => [
				'Volba způsobu platby přímo v objednávkovém formuláři',
				'Automatické odeslání potvrzovacího e-mailu s platebními instrukcemi',
				'Oddělený stav objednávky „čeká na úhradu faktury"',
				'Ruční potvrzení zaplacení administrátorem',
				'Vhodné pro firemní objednávky a B2B klienty',
			],
		],
		[
			'key'         => 'inquiries_admin',
			'label'       => 'Přehled poptávek a kontaktů',
			'description' => 'Všechny odeslané formuláře (kontaktní, poptávka testování, žádosti o pozvánku) se ukládají do databáze a jsou dostupné v přehledné adminovské sekci – s filtrací, označením stavu a počtem nových zpráv v menu.',
			'benefits'    => [
				'Přehled všech příchozích zpráv na jednom místě',
				'Filtrace podle typu formuláře a stavu (nová / přečteno / vyřízena)',
				'Červená tečka u nových, badge s počtem v menu',
				'Automatické označení jako přečteno při otevření detailu',
				'Žádná zpráva se neztratí – záloha mimo e-mail',
			],
		],
		[
			'key'         => 'speaker_database',
			'label'       => 'Databáze speakrů',
			'description' => 'Centrální správa všech řečníků a lektorů. Místo vyplňování jména, fotky a bija u každého školení zvlášť stačí školitele jednou přidat do databáze a pak ho u libovolného školení jednoduše vybrat.',
			'benefits'    => [
				'Jeden profil – neomezené použití na více školeních',
				'Editace profilu na jednom místě se automaticky promítne všude',
				'Foto, pozice, bio a sociální sítě uložené trvale',
				'Rychlý výběr školitele při tvorbě školení (relation field)',
			],
		],
		// Další prémiové funkce budou přidány sem.
	];
}

// ─── Render stránky ──────────────────────────────────────────────────────────
function xevos_render_premium_features_page(): void {
	$features     = xevos_get_premium_features();
	$menu_visible = (bool) get_option( 'xevos_premium_menu_visible', 1 );
	?>
	<div class="wrap xevos-premium-wrap">
		<h1>
			<span class="dashicons dashicons-star-filled" style="color:#f0c33c;font-size:24px;width:24px;height:24px;"></span>
			Prémiové funkce
		</h1>

		<?php if ( isset( $_GET['updated'] ) ) : ?>
			<div class="notice notice-success is-dismissible"><p>Nastavení uloženo.</p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'xevos_premium_save', 'xevos_premium_nonce' ); ?>
			<input type="hidden" name="action" value="xevos_premium_save">

			<!-- Viditelnost menu -->
			<div class="xevos-visibility-bar">
				<label class="xevos-toggle-label">
					<input type="checkbox" name="xevos_premium_visible" value="1" <?php checked( $menu_visible ); ?> onchange="this.form.submit()">
					<strong>Zobrazit záložku v menu</strong>
				</label>
				<p class="description">
					Zaškrtnuto = položka "Prémiové funkce" je vidět v levém menu WordPressu.
				</p>
			</div>

			<p class="xevos-premium-intro">
				Níže jsou rozšíření připravená k aktivaci. Přepínačem na kartě funkci zapneš / vypneš.
			</p>

			<!-- Karty funkcí -->
			<div class="xevos-feature-grid">
				<?php foreach ( $features as $feature ) :
					$enabled = xevos_is_feature_enabled( $feature['key'] );
					$status  = $enabled ? 'active' : 'locked';
				?>
					<div class="xevos-feature-card <?php echo esc_attr( $status ); ?>">

						<div class="xevos-feature-badge badge-<?php echo esc_attr( $status ); ?>">
							<?php if ( $enabled ) : ?>
								<span class="dashicons dashicons-yes-alt"></span> Aktivní
							<?php else : ?>
								<span class="dashicons dashicons-lock"></span> Neaktivní
							<?php endif; ?>
						</div>

						<h2><?php echo esc_html( $feature['label'] ); ?></h2>
						<p><?php echo esc_html( $feature['description'] ); ?></p>

						<?php if ( ! empty( $feature['benefits'] ) ) : ?>
							<ul>
								<?php foreach ( $feature['benefits'] as $benefit ) : ?>
									<li><?php echo esc_html( $benefit ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<div class="xevos-feature-footer">
							<label class="xevos-toggle-label">
								<input
									type="checkbox"
									name="feature_<?php echo esc_attr( $feature['key'] ); ?>"
									value="1"
									<?php checked( $enabled ); ?>
									onchange="this.form.submit()"
								>
								<?php if ( $enabled ) : ?>
									<span class="xevos-feature-toggle-active">Zapnuto</span>
								<?php else : ?>
									<span class="xevos-feature-toggle-locked">Vypnout</span>
								<?php endif; ?>
							</label>
						</div>

					</div>
				<?php endforeach; ?>
			</div>

			</form>
	</div>
	<?php
}

// ─── admin-post.php handler (zpracuje POST z formuláře) ──────────────────────
add_action( 'admin_post_xevos_premium_save', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Nedostatečná oprávnění.' );
	}
	if ( ! isset( $_POST['xevos_premium_nonce'] ) || ! wp_verify_nonce( $_POST['xevos_premium_nonce'], 'xevos_premium_save' ) ) {
		wp_die( 'Neplatný token.' );
	}

	update_option( 'xevos_premium_menu_visible', isset( $_POST['xevos_premium_visible'] ) ? 1 : 0 );

	foreach ( xevos_get_premium_features() as $feature ) {
		$key = $feature['key'];
		update_option( 'xevos_feature_' . $key . '_enabled', isset( $_POST[ 'feature_' . $key ] ) ? 1 : 0 );
	}

	wp_safe_redirect( admin_url( 'admin.php?page=xevos-premium-features&updated=1' ) );
	exit;
} );

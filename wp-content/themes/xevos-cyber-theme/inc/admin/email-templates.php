<?php
/**
 * Admin page: Email templates overview with preview.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', 'xevos_email_templates_menu', 20 );

function xevos_email_templates_menu(): void {
	add_menu_page(
		'E-mailové šablony',
		'E-maily',
		'manage_options',
		'xevos-email-templates',
		'xevos_email_templates_page',
		'dashicons-email-alt',
		31
	);
}

/**
 * Get all registered email templates with metadata and sample data.
 */
function xevos_get_email_templates(): array {
	$firma = function_exists( 'xevos_get_option' ) ? xevos_get_option( 'nazev_firmy', 'XEVOS s.r.o.' ) : 'XEVOS s.r.o.';

	return [
		'order-confirmation' => [
			'name'        => 'Potvrzení objednávky',
			'description' => 'Odesílá se zákazníkovi po vytvoření placené objednávky.',
			'trigger'     => 'Odeslání objednávkového formuláře (placené školení)',
			'recipient'   => 'Zákazník',
			'sample_data' => [
				'jmeno'            => 'Jan',
				'cislo_objednavky' => 'OBJ-2026-0001',
				'nazev_skoleni'    => 'Penetrační testování pro pokročilé',
				'termin'           => '26.05.2026',
				'cena'             => '10 000',
				'payment_url'      => '#',
				'kontakt_email'    => get_option( 'admin_email' ),
				'firma'            => $firma,
			],
		],
		'payment-confirmation' => [
			'name'        => 'Platba přijata',
			'description' => 'Odesílá se zákazníkovi po úspěšné platbě přes Comgate. V příloze je PDF faktura.',
			'trigger'     => 'Comgate callback – stav PAID',
			'recipient'   => 'Zákazník',
			'sample_data' => [
				'jmeno'         => 'Jan',
				'nazev_skoleni' => 'Penetrační testování pro pokročilé',
				'cena'          => '10 000',
				'termin'        => '26.05.2026',
				'misto'         => 'Praha, Karlovo náměstí 5',
				'typ'           => 'Prezenční',
				'kontakt_email' => get_option( 'admin_email' ),
				'firma'         => $firma,
			],
		],
		'admin-notification' => [
			'name'        => 'Notifikace pro admina (placená objednávka)',
			'description' => 'Odesílá se adminovi po vytvoření nové placené objednávky.',
			'trigger'     => 'Odeslání objednávkového formuláře (placené školení)',
			'recipient'   => 'Admin',
			'sample_data' => [
				'cislo_objednavky' => 'OBJ-2026-0001',
				'jmeno'            => 'Jan',
				'prijmeni'         => 'Novák',
				'email'            => 'jan.novak@example.com',
				'telefon'          => '+420 777 123 456',
				'firma_nazev'      => 'ACME s.r.o.',
				'nazev_skoleni'    => 'Penetrační testování pro pokročilé',
				'termin'           => '26.05.2026',
				'cena'             => '10 000',
				'admin_url'        => admin_url( 'post.php?post=1&action=edit' ),
			],
		],
		'registration-confirmation' => [
			'name'        => 'Potvrzení registrace (zdarma)',
			'description' => 'Odesílá se zákazníkovi po registraci na bezplatné školení.',
			'trigger'     => 'Odeslání registračního formuláře (školení zdarma)',
			'recipient'   => 'Zákazník',
			'sample_data' => [
				'jmeno'         => 'Jan',
				'nazev_skoleni' => 'Úvod do kybernetické bezpečnosti',
				'termin'        => '15.06.2026',
				'misto'         => 'Online',
				'kontakt_email' => get_option( 'admin_email' ),
				'firma'         => $firma,
			],
		],
		'invitation-request' => [
			'name'        => 'Žádost o pozvánku přijata',
			'description' => 'Odesílá se zákazníkovi po odeslání žádosti o pozvánku na akci s omezenou kapacitou.',
			'trigger'     => 'Odeslání formuláře žádosti o pozvánku',
			'recipient'   => 'Zákazník',
			'sample_data' => [
				'jmeno'         => 'Jan',
				'nazev_skoleni' => 'VIP Workshop: Red Teaming',
				'termin'        => '10.07.2026',
				'kontakt_email' => get_option( 'admin_email' ),
				'firma'         => $firma,
			],
		],
		'admin-notification-free' => [
			'name'        => 'Notifikace pro admina (registrace / pozvánka)',
			'description' => 'Odesílá se adminovi po bezplatné registraci nebo žádosti o pozvánku.',
			'trigger'     => 'Odeslání registračního formuláře nebo žádosti o pozvánku',
			'recipient'   => 'Admin',
			'sample_data' => [
				'typ'           => 'Registrace zdarma',
				'jmeno'         => 'Jan',
				'prijmeni'      => 'Novák',
				'email'         => 'jan.novak@example.com',
				'telefon'       => '+420 777 123 456',
				'firma_nazev'   => 'ACME s.r.o.',
				'nazev_skoleni' => 'Úvod do kybernetické bezpečnosti',
				'termin'        => '15.06.2026',
				'admin_url'     => admin_url( 'post.php?post=1&action=edit' ),
			],
		],
		'reminder' => [
			'name'        => 'Připomenutí školení',
			'description' => 'Odesílá se automaticky 3 dny a 1 den před termínem školení.',
			'trigger'     => 'WP Cron (denně v 08:00)',
			'recipient'   => 'Zákazník',
			'sample_data' => [
				'jmeno'         => 'Jan',
				'nazev_skoleni' => 'Penetrační testování pro pokročilé',
				'termin'        => '26.05.2026',
				'cas'           => '9:00 – 17:00',
				'misto'         => 'Praha, Karlovo náměstí 5',
				'poznamky'      => 'Vezměte si s sebou notebook a poznámkový blok.',
				'kontakt_email' => get_option( 'admin_email' ),
				'firma'         => $firma,
			],
		],
		'contact-confirmation' => [
			'name'        => 'Potvrzení kontaktní zprávy',
			'description' => 'Odesílá se zákazníkovi po odeslání kontaktního formuláře.',
			'trigger'     => 'Odeslání kontaktního formuláře',
			'recipient'   => 'Zákazník',
			'sample_data' => [
				'jmeno'         => 'Jan',
				'kontakt_email' => get_option( 'admin_email' ),
				'firma'         => $firma,
			],
		],
		'admin-notification-contact' => [
			'name'        => 'Notifikace pro admina (kontaktní formulář)',
			'description' => 'Odesílá se adminovi po odeslání kontaktního formuláře. Obsahuje text zprávy a tlačítko pro odpověď.',
			'trigger'     => 'Odeslání kontaktního formuláře',
			'recipient'   => 'Admin',
			'sample_data' => [
				'jmeno'    => 'Jan',
				'prijmeni' => 'Novák',
				'email'    => 'jan.novak@example.com',
				'telefon'  => '+420 777 123 456',
				'zprava'   => 'Dobrý den, mám zájem o školení penetračního testování pro náš tým 5 lidí. Mohli byste nám poslat nabídku? Děkuji.',
			],
		],
		'cancellation' => [
			'name'        => 'Zrušení účasti',
			'description' => 'Připraveno pro budoucí použití – odesílá se zákazníkovi při zrušení objednávky.',
			'trigger'     => 'Zatím neaktivní',
			'recipient'   => 'Zákazník',
			'sample_data' => [
				'jmeno'         => 'Jan',
				'nazev_skoleni' => 'Penetrační testování pro pokročilé',
				'termin'        => '26.05.2026',
				'cena'          => '10 000',
				'refundace'     => true,
				'kontakt_email' => get_option( 'admin_email' ),
				'firma'         => $firma,
			],
		],
	];
}

/**
 * AJAX: Render email template preview.
 */
add_action( 'wp_ajax_xevos_preview_email_template', 'xevos_preview_email_template' );

function xevos_preview_email_template(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Nedostatečná oprávnění.' );
	}

	check_ajax_referer( 'xevos_email_preview', 'nonce' );

	$template = sanitize_file_name( $_GET['template'] ?? '' );
	$templates = xevos_get_email_templates();

	if ( ! isset( $templates[ $template ] ) ) {
		wp_die( 'Šablona nenalezena.' );
	}

	$template_path = XEVOS_THEME_DIR . '/email-templates/' . $template . '.php';
	if ( ! file_exists( $template_path ) ) {
		wp_die( 'Soubor šablony neexistuje.' );
	}

	$email_data = $templates[ $template ]['sample_data'];

	ob_start();
	include $template_path;
	$html = ob_get_clean();

	echo $html;
	wp_die();
}

/**
 * Render the admin page.
 */
function xevos_email_templates_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$templates = xevos_get_email_templates();
	$nonce = wp_create_nonce( 'xevos_email_preview' );
	$preview_url = admin_url( 'admin-ajax.php?action=xevos_preview_email_template&nonce=' . $nonce );
	?>
	<div class="wrap">
		<h1>E-mailové šablony</h1>
		<p>Přehled všech e-mailových šablon používaných na webu. Kliknutím na náhled zobrazíte šablonu s ukázkovými daty.</p>

		<table class="wp-list-table widefat fixed striped" style="margin-top:20px;">
			<thead>
				<tr>
					<th style="width:22%;">Název</th>
					<th style="width:30%;">Popis</th>
					<th style="width:18%;">Kdy se odesílá</th>
					<th style="width:10%;">Příjemce</th>
					<th style="width:12%;">Soubor</th>
					<th style="width:8%;">Akce</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $templates as $slug => $tpl ) :
					$file_exists = file_exists( XEVOS_THEME_DIR . '/email-templates/' . $slug . '.php' );
					$is_inactive = str_contains( $tpl['trigger'], 'neaktivní' );
				?>
				<tr<?php echo $is_inactive ? ' style="opacity:0.6;"' : ''; ?>>
					<td><strong><?php echo esc_html( $tpl['name'] ); ?></strong></td>
					<td><?php echo esc_html( $tpl['description'] ); ?></td>
					<td><?php echo esc_html( $tpl['trigger'] ); ?></td>
					<td>
						<?php if ( $tpl['recipient'] === 'Admin' ) : ?>
							<span style="background:#dbeafe;color:#1e40af;padding:2px 8px;border-radius:3px;font-size:12px;">Admin</span>
						<?php else : ?>
							<span style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:3px;font-size:12px;">Zákazník</span>
						<?php endif; ?>
					</td>
					<td>
						<code style="font-size:12px;"><?php echo esc_html( $slug . '.php' ); ?></code>
						<?php if ( ! $file_exists ) : ?>
							<br><span style="color:#dc2626;font-size:11px;">Soubor chybí!</span>
						<?php endif; ?>
					</td>
					<td>
						<?php if ( $file_exists ) : ?>
							<a href="#" class="button button-small xevos-preview-email" data-template="<?php echo esc_attr( $slug ); ?>">Náhled</a>
						<?php else : ?>
							<span style="color:#999;">—</span>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<div style="margin-top:20px;padding:12px 16px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:6px;">
			<strong>Soubory šablon:</strong>
			<code><?php echo esc_html( XEVOS_THEME_DIR . '/email-templates/' ); ?></code>
			<br><small style="color:#666;">Šablony jsou PHP soubory s inline CSS (kompatibilní se všemi e-mailovými klienty). Proměnná <code>$email_data</code> obsahuje data předaná šabloně.</small>
		</div>
	</div>

	<!-- Preview modal -->
	<div id="xevos-email-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.7);z-index:100000;">
		<div style="position:absolute;top:30px;left:50%;transform:translateX(-50%);width:700px;max-width:90vw;max-height:calc(100vh - 60px);background:#fff;border-radius:8px;overflow:hidden;display:flex;flex-direction:column;">
			<div style="display:flex;justify-content:space-between;align-items:center;padding:12px 20px;border-bottom:1px solid #e2e8f0;flex-shrink:0;">
				<strong id="xevos-email-modal-title">Náhled šablony</strong>
				<button id="xevos-email-modal-close" class="button" style="min-height:30px;">&times; Zavřít</button>
			</div>
			<div style="flex:1;overflow:auto;">
				<iframe id="xevos-email-preview-frame" style="width:100%;height:600px;border:0;"></iframe>
			</div>
		</div>
	</div>

	<script>
	(function() {
		var previewUrl = <?php echo wp_json_encode( $preview_url ); ?>;
		var templates = <?php echo wp_json_encode( wp_list_pluck( $templates, 'name' ) ); ?>;
		var modal = document.getElementById('xevos-email-modal');
		var frame = document.getElementById('xevos-email-preview-frame');
		var title = document.getElementById('xevos-email-modal-title');

		document.querySelectorAll('.xevos-preview-email').forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.preventDefault();
				var tpl = this.dataset.template;
				title.textContent = 'Náhled: ' + (templates[tpl] || tpl);
				frame.src = previewUrl + '&template=' + encodeURIComponent(tpl);
				modal.style.display = 'block';
			});
		});

		document.getElementById('xevos-email-modal-close').addEventListener('click', function() {
			modal.style.display = 'none';
			frame.src = '';
		});

		modal.addEventListener('click', function(e) {
			if (e.target === modal) {
				modal.style.display = 'none';
				frame.src = '';
			}
		});
	})();
	</script>
	<?php
}

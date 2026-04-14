<?php
/**
 * Admin page: Email templates overview with preview and test send.
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
				'termin'           => '26.05.2026 | 9:00 – 17:00',
				'cena'             => '10 000',
				'payment_url'      => home_url( '/skoleni/penetracni-testovani/' ),
				'skoleni_url'      => home_url( '/skoleni/penetracni-testovani/' ),
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
				'termin'        => '26.05.2026 | 9:00 – 17:00',
				'misto'         => 'Praha, Karlovo náměstí 5',
				'typ'           => 'Prezenční',
				'skoleni_url'   => home_url( '/skoleni/penetracni-testovani/' ),
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
				'cislo_objednavky'  => 'OBJ-2026-0001',
				'jmeno'             => 'Jan',
				'prijmeni'          => 'Novák',
				'email'             => 'jan.novak@example.com',
				'telefon'           => '+420 777 123 456',
				'firma_nazev'       => 'ACME s.r.o.',
				'nazev_skoleni'     => 'Penetrační testování pro pokročilé',
				'termin'            => '26.05.2026 | 9:00 – 17:00',
				'cena'              => '10 000',
				'admin_url'         => admin_url( 'post.php?post=1&action=edit' ),
				'skoleni_url'       => home_url( '/skoleni/penetracni-testovani/' ),
				'skoleni_admin_url' => admin_url( 'post.php?post=2&action=edit' ),
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
				'termin'        => '15.06.2026 | 9:00 – 17:00',
				'misto'         => 'Online',
				'skoleni_url'   => home_url( '/skoleni/uvod-do-kyberneticke-bezpecnosti/' ),
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
				'skoleni_url'   => home_url( '/skoleni/vip-workshop-red-teaming/' ),
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
				'typ'               => 'Registrace zdarma',
				'jmeno'             => 'Jan',
				'prijmeni'          => 'Novák',
				'email'             => 'jan.novak@example.com',
				'telefon'           => '+420 777 123 456',
				'firma_nazev'       => 'ACME s.r.o.',
				'nazev_skoleni'     => 'Úvod do kybernetické bezpečnosti',
				'termin'            => '15.06.2026 | 9:00 – 17:00',
				'admin_url'         => admin_url( 'post.php?post=1&action=edit' ),
				'skoleni_url'       => home_url( '/skoleni/uvod-do-kyberneticke-bezpecnosti/' ),
				'skoleni_admin_url' => admin_url( 'post.php?post=2&action=edit' ),
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
				'skoleni_url'   => home_url( '/skoleni/penetracni-testovani/' ),
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
		'inquiry-confirmation' => [
			'name'        => 'Potvrzení poptávky testování',
			'description' => 'Odesílá se zákazníkovi po odeslání poptávkového formuláře na stránce kybernetického testování.',
			'trigger'     => 'Odeslání poptávkového formuláře',
			'recipient'   => 'Zákazník',
			'sample_data' => [
				'jmeno'         => 'Jan',
				'druh_testu'    => 'Penetrační testy',
				'kontakt_email' => get_option( 'admin_email' ),
				'firma'         => $firma,
			],
		],
		'admin-notification-inquiry' => [
			'name'        => 'Notifikace pro admina (poptávka testování)',
			'description' => 'Odesílá se adminovi po odeslání poptávky kybernetického testování.',
			'trigger'     => 'Odeslání poptávkového formuláře',
			'recipient'   => 'Admin',
			'sample_data' => [
				'jmeno'      => 'Jan',
				'prijmeni'   => 'Novák',
				'email'      => 'jan.novak@example.com',
				'telefon'    => '+420 777 123 456',
				'firma'      => 'ACME s.r.o.',
				'druh_testu' => 'Penetrační testy',
				'zprava'     => 'Potřebujeme otestovat naši infrastrukturu před ISO 27001 auditem. Máme 3 servery a 2 webové aplikace.',
			],
		],
		'invoice-order-confirmation' => [
			'name'        => 'Potvrzení objednávky na fakturu',
			'description' => 'Odesílá se zákazníkovi po objednávce se způsobem platby „na fakturu". Obsahuje platební instrukce (číslo účtu, VS, částka).',
			'trigger'     => 'Odeslání objednávkového formuláře (platba na fakturu)',
			'recipient'   => 'Zákazník',
			'sample_data' => [
				'jmeno'            => 'Jan',
				'cislo_objednavky' => 'OBJ-2026-0001',
				'nazev_skoleni'    => 'Penetrační testování pro pokročilé',
				'termin'           => '26.05.2026 | 9:00 – 17:00',
				'cena'             => '10 000',
				'vs'               => 'OBJ-2026-0001',
				'cislo_uctu'       => '123456789/0100',
				'kontakt_email'    => get_option( 'admin_email' ),
				'firma'            => $firma,
			],
		],
		'cancellation' => [
			'name'        => 'Zrušení účasti',
			'description' => 'Odesílá se účastníkovi při odebrání z registrace adminem. Pokud byla objednávka zaplacena a školení probíhá refundace, zobrazí se info o vrácení peněz.',
			'trigger'     => 'Tlačítko "Odstranit" v metaboxu Registrace na školení',
			'recipient'   => 'Zákazník',
			'sample_data' => [
				'jmeno'         => 'Jan',
				'nazev_skoleni' => 'Penetrační testování pro pokročilé',
				'termin'        => '26.05.2026 | 9:00 – 17:00',
				'cena'          => '10 000',
				'refundace'     => true,
				'skoleni_url'   => home_url( '/skoleni/penetracni-testovani/' ),
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
 * AJAX: Send test email.
 */
add_action( 'wp_ajax_xevos_send_test_email', 'xevos_send_test_email_ajax' );

function xevos_send_test_email_ajax(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( [ 'message' => 'Nedostatečná oprávnění.' ] );
	}

	check_ajax_referer( 'xevos_email_preview', 'nonce' );

	$template  = sanitize_file_name( $_POST['template'] ?? '' );
	$recipient = sanitize_email( $_POST['recipient'] ?? '' );

	if ( ! $recipient ) {
		wp_send_json_error( [ 'message' => 'Zadejte platnou e-mailovou adresu.' ] );
	}

	$templates = xevos_get_email_templates();

	if ( ! isset( $templates[ $template ] ) ) {
		wp_send_json_error( [ 'message' => 'Šablona nenalezena.' ] );
	}

	$template_path = XEVOS_THEME_DIR . '/email-templates/' . $template . '.php';
	if ( ! file_exists( $template_path ) ) {
		wp_send_json_error( [ 'message' => 'Soubor šablony neexistuje.' ] );
	}

	$email_data = $templates[ $template ]['sample_data'];

	ob_start();
	include $template_path;
	$html = ob_get_clean();

	$subject = '[TEST] ' . $templates[ $template ]['name'];
	$headers = [
		'Content-Type: text/html; charset=UTF-8',
		'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>',
	];

	$sent = wp_mail( $recipient, $subject, $html, $headers );

	if ( $sent ) {
		wp_send_json_success( [ 'message' => 'E-mail byl odeslán na ' . $recipient . '.' ] );
	} else {
		wp_send_json_error( [ 'message' => 'Odeslání selhalo. Zkontrolujte nastavení SMTP.' ] );
	}
}

/**
 * Render the admin page.
 */
function xevos_email_templates_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$templates   = xevos_get_email_templates();
	$nonce       = wp_create_nonce( 'xevos_email_preview' );
	$preview_url = admin_url( 'admin-ajax.php?action=xevos_preview_email_template&nonce=' . $nonce );
	$current_user_email = wp_get_current_user()->user_email;
	?>
	<div class="wrap">
		<h1>E-mailové šablony</h1>
		<p>Přehled všech e-mailových šablon používaných na webu. Kliknutím na <strong>Náhled</strong> zobrazíte šablonu s ukázkovými daty, tlačítkem <strong>Odeslat ukázku</strong> ji zašlete na libovolný e-mail.</p>

		<table class="wp-list-table widefat fixed striped" style="margin-top:20px;">
			<thead>
				<tr>
					<th style="width:22%;">Název</th>
					<th style="width:28%;">Popis</th>
					<th style="width:18%;">Kdy se odesílá</th>
					<th style="width:9%;">Příjemce</th>
					<th style="width:11%;">Soubor</th>
					<th style="width:12%;">Akce</th>
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
					<td style="white-space:nowrap;">
						<?php if ( $file_exists ) : ?>
							<a href="#" class="button button-small xevos-preview-email" data-template="<?php echo esc_attr( $slug ); ?>" data-name="<?php echo esc_attr( $tpl['name'] ); ?>">Náhled</a>
							<a href="#" class="button button-small xevos-send-test-email" data-template="<?php echo esc_attr( $slug ); ?>" data-name="<?php echo esc_attr( $tpl['name'] ); ?>" style="margin-left:4px;">Odeslat</a>
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
	<div id="xevos-email-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.75);z-index:100000;">
		<div style="position:absolute;top:30px;left:50%;transform:translateX(-50%);width:720px;max-width:92vw;max-height:calc(100vh - 60px);background:#fff;border-radius:8px;overflow:hidden;display:flex;flex-direction:column;">
			<div style="display:flex;justify-content:space-between;align-items:center;padding:12px 20px;border-bottom:1px solid #e2e8f0;flex-shrink:0;gap:12px;">
				<strong id="xevos-email-modal-title" style="font-size:14px;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">Náhled šablony</strong>
				<div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
					<input type="email" id="xevos-test-email-input" placeholder="vas@email.cz" value="<?php echo esc_attr( $current_user_email ); ?>" style="padding:5px 10px;border:1px solid #ccc;border-radius:4px;font-size:13px;width:200px;">
					<button id="xevos-send-test-btn" class="button button-primary" style="white-space:nowrap;">&#9993; Odeslat ukázku</button>
					<button id="xevos-email-modal-close" class="button" style="white-space:nowrap;">&times; Zavřít</button>
				</div>
			</div>
			<div id="xevos-send-status" style="display:none;padding:8px 20px;font-size:13px;flex-shrink:0;"></div>
			<div style="flex:1;overflow:auto;">
				<iframe id="xevos-email-preview-frame" style="width:100%;height:600px;border:0;display:block;"></iframe>
			</div>
		</div>
	</div>

	<!-- Send test modal (from row button) -->
	<div id="xevos-send-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.75);z-index:100000;">
		<div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:440px;max-width:92vw;background:#fff;border-radius:8px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
			<h3 style="margin:0 0 6px;font-size:16px;" id="xevos-send-modal-title">Odeslat ukázku</h3>
			<p style="margin:0 0 20px;font-size:13px;color:#666;" id="xevos-send-modal-tpl"></p>
			<label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;">E-mailová adresa příjemce</label>
			<input type="email" id="xevos-send-modal-email" value="<?php echo esc_attr( $current_user_email ); ?>" style="width:100%;box-sizing:border-box;padding:8px 12px;border:1px solid #ccc;border-radius:4px;font-size:14px;margin-bottom:16px;">
			<div id="xevos-send-modal-status" style="display:none;padding:8px 12px;border-radius:4px;font-size:13px;margin-bottom:16px;"></div>
			<div style="display:flex;justify-content:flex-end;gap:8px;">
				<button id="xevos-send-modal-cancel" class="button">Zrušit</button>
				<button id="xevos-send-modal-send" class="button button-primary">&#9993; Odeslat</button>
			</div>
		</div>
	</div>

	<script>
	(function() {
		var previewUrl   = <?php echo wp_json_encode( $preview_url ); ?>;
		var ajaxUrl      = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
		var nonce        = <?php echo wp_json_encode( $nonce ); ?>;

		var modal        = document.getElementById('xevos-email-modal');
		var frame        = document.getElementById('xevos-email-preview-frame');
		var title        = document.getElementById('xevos-email-modal-title');
		var sendStatus   = document.getElementById('xevos-send-status');
		var testInput    = document.getElementById('xevos-test-email-input');
		var sendTestBtn  = document.getElementById('xevos-send-test-btn');
		var activeTpl    = '';

		// Send modal
		var sendModal     = document.getElementById('xevos-send-modal');
		var sendModalTitle = document.getElementById('xevos-send-modal-title');
		var sendModalTpl  = document.getElementById('xevos-send-modal-tpl');
		var sendModalEmail = document.getElementById('xevos-send-modal-email');
		var sendModalStatus = document.getElementById('xevos-send-modal-status');
		var sendModalSend  = document.getElementById('xevos-send-modal-send');
		var sendModalCancel = document.getElementById('xevos-send-modal-cancel');
		var sendModalTplSlug = '';

		function showStatus(el, msg, ok) {
			el.textContent = msg;
			el.style.display = 'block';
			el.style.background = ok ? '#dcfce7' : '#fee2e2';
			el.style.color      = ok ? '#166534' : '#991b1b';
			el.style.border     = '1px solid ' + (ok ? '#86efac' : '#fca5a5');
		}

		function sendTest(template, recipient, btn, statusEl) {
			btn.disabled = true;
			btn.textContent = 'Odesílám…';
			statusEl.style.display = 'none';

			var fd = new FormData();
			fd.append('action',    'xevos_send_test_email');
			fd.append('nonce',     nonce);
			fd.append('template',  template);
			fd.append('recipient', recipient);

			fetch(ajaxUrl, { method: 'POST', body: fd })
				.then(function(r){ return r.json(); })
				.then(function(res){
					showStatus(statusEl, res.data && res.data.message ? res.data.message : (res.success ? 'Odesláno.' : 'Chyba.'), res.success);
					btn.disabled = false;
					btn.innerHTML = '&#9993; Odeslat ukázku';
				})
				.catch(function(){
					showStatus(statusEl, 'Chyba při komunikaci se serverem.', false);
					btn.disabled = false;
					btn.innerHTML = '&#9993; Odeslat ukázku';
				});
		}

		// Open preview modal
		document.querySelectorAll('.xevos-preview-email').forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.preventDefault();
				activeTpl = this.dataset.template;
				title.textContent = this.dataset.name;
				sendStatus.style.display = 'none';
				frame.src = previewUrl + '&template=' + encodeURIComponent(activeTpl);
				modal.style.display = 'block';
			});
		});

		// Send from preview modal toolbar
		sendTestBtn.addEventListener('click', function() {
			if (!activeTpl) return;
			sendTest(activeTpl, testInput.value, sendTestBtn, sendStatus);
		});

		// Close preview modal
		document.getElementById('xevos-email-modal-close').addEventListener('click', function() {
			modal.style.display = 'none';
			frame.src = '';
			sendStatus.style.display = 'none';
		});
		modal.addEventListener('click', function(e) {
			if (e.target === modal) {
				modal.style.display = 'none';
				frame.src = '';
				sendStatus.style.display = 'none';
			}
		});

		// Open send modal directly from row button
		document.querySelectorAll('.xevos-send-test-email').forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.preventDefault();
				sendModalTplSlug = this.dataset.template;
				sendModalTitle.textContent = 'Odeslat ukázku';
				sendModalTpl.textContent   = this.dataset.name;
				sendModalStatus.style.display = 'none';
				sendModalSend.disabled = false;
				sendModalSend.innerHTML = '&#9993; Odeslat';
				sendModal.style.display = 'block';
				sendModalEmail.focus();
			});
		});

		sendModalSend.addEventListener('click', function() {
			sendTest(sendModalTplSlug, sendModalEmail.value, sendModalSend, sendModalStatus);
		});

		sendModalEmail.addEventListener('keydown', function(e) {
			if (e.key === 'Enter') sendModalSend.click();
		});

		function closeSendModal() {
			sendModal.style.display = 'none';
			sendModalStatus.style.display = 'none';
		}
		sendModalCancel.addEventListener('click', closeSendModal);
		sendModal.addEventListener('click', function(e) { if (e.target === sendModal) closeSendModal(); });

		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape') {
				if (modal.style.display !== 'none') { modal.style.display = 'none'; frame.src = ''; }
				if (sendModal.style.display !== 'none') closeSendModal();
			}
		});
	})();
	</script>
	<?php
}

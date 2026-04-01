<?php
/**
 * Demo content importer.
 *
 * Run once via WP Admin: Tools → Import Demo Content
 * or visit: /wp-admin/admin.php?page=xevos-demo-import
 *
 * Creates sample pages, posts, školení, recenze and fills ACF fields.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Check if a post with given title exists in a post type.
 */
function xevos_post_exists_by_title( string $title, string $post_type ): bool {
	$q = new WP_Query( [
		'post_type'      => $post_type,
		'title'          => $title,
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'post_status'    => 'any',
	] );
	return $q->have_posts();
}

add_action( 'admin_menu', 'xevos_demo_import_menu' );

function xevos_demo_import_menu(): void {
	add_management_page(
		'Import Demo obsahu',
		'Demo obsah XEVOS',
		'manage_options',
		'xevos-demo-import',
		'xevos_demo_import_page'
	);
}

function xevos_demo_import_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) return;

	$done = false;
	if ( isset( $_POST['xevos_import_demo'] ) && check_admin_referer( 'xevos_demo_import' ) ) {
		xevos_import_demo_content();
		$done = true;
	}
	?>
	<div class="wrap">
		<h1>Import demo obsahu – XEVOS Cyber Security</h1>
		<?php if ( $done ) : ?>
			<div class="notice notice-success"><p><strong>Demo obsah byl úspěšně naimportován!</strong> Zkontrolujte stránky a nastavení.</p></div>
		<?php else : ?>
			<p>Tato akce vytvoří ukázkové stránky, aktuality, školení, recenze a vyplní ACF pole na homepage a v nastavení webu.</p>
			<p><strong>Doporučeno spustit jen jednou na čisté instalaci.</strong></p>
			<form method="post">
				<?php wp_nonce_field( 'xevos_demo_import' ); ?>
				<input type="hidden" name="xevos_import_demo" value="1">
				<?php submit_button( 'Importovat demo obsah', 'primary', 'submit', true ); ?>
			</form>
		<?php endif; ?>
	</div>
	<?php
}

function xevos_import_demo_content(): void {

	// =========================================================================
	// 1. ACF Options (Site Settings)
	// =========================================================================
	if ( function_exists( 'update_field' ) ) {
		update_field( 'telefon', '+420 591 140 315', 'option' );
		update_field( 'email', 'hello@xevos.eu', 'option' );
		update_field( 'adresa', 'Mostárenská 1156/38, 703 00 Ostrava', 'option' );
		update_field( 'nazev_firmy', 'XEVOS s.r.o.', 'option' );
		update_field( 'ico', '12345678', 'option' );
		update_field( 'dic', 'CZ12345678', 'option' );
		update_field( 'header_cta_text', 'Nezávazná konzultace', 'option' );
		update_field( 'header_cta_url', home_url( '/kontakt/' ), 'option' );
		update_field( 'footer_popis', '<p>Pomáháme firmám budovat reálnou kybernetickou odolnost. Bez strašení. S jistotou.</p>', 'option' );

		// Social networks.
		update_field( 'socialni_site', [
			[ 'nazev' => 'Facebook', 'url' => 'https://facebook.com/xevos', 'ikona' => '' ],
			[ 'nazev' => 'LinkedIn', 'url' => 'https://linkedin.com/company/xevos', 'ikona' => '' ],
			[ 'nazev' => 'Instagram', 'url' => 'https://instagram.com/xevos', 'ikona' => '' ],
		], 'option' );

		// Partners.
		update_field( 'partneri', [
			[ 'nazev' => 'Cisco', 'logo' => '', 'url' => 'https://cisco.com' ],
			[ 'nazev' => 'ESET', 'logo' => '', 'url' => 'https://eset.com' ],
			[ 'nazev' => 'Apple', 'logo' => '', 'url' => 'https://apple.com' ],
			[ 'nazev' => 'Palo Alto Networks', 'logo' => '', 'url' => 'https://paloaltonetworks.com' ],
			[ 'nazev' => 'Pentera', 'logo' => '', 'url' => 'https://pentera.io' ],
		], 'option' );
	}

	// =========================================================================
	// 2. Pages
	// =========================================================================
	$pages = [
		'Úvod'                     => [ 'slug' => 'uvod', 'template' => '' ],
		'Služby'                   => [ 'slug' => 'sluzby', 'template' => 'page-sluzby.php' ],
		'NIS 2'                    => [ 'slug' => 'nis2', 'template' => 'page-nis2.php' ],
		'Kybernetické testování'   => [ 'slug' => 'kyberneticke-testovani', 'template' => 'page-kyberneticke-testovani.php' ],
		'Eventy'                   => [ 'slug' => 'eventy', 'template' => 'page-eventy.php' ],
		'Přehled školení'          => [ 'slug' => 'prehled-skoleni', 'template' => 'page-prehled-skoleni.php' ],
		'Partnerství'              => [ 'slug' => 'partnerstvi', 'template' => 'page-partnerstvi.php' ],
		'O nás'                    => [ 'slug' => 'o-nas', 'template' => 'page-o-nas.php' ],
		'Kontakt'                  => [ 'slug' => 'kontakt', 'template' => 'page-kontakt.php' ],
		'Obchodní podmínky'        => [ 'slug' => 'obchodni-podminky', 'template' => 'page-obchodni-podminky.php', 'content' => '<h2>Úvodní ustanovení</h2>
<p>Tyto obchodní podmínky společnosti XEVOS Solutions s.r.o., IČO: 14184290, se sídlem Mostárenská 1156/38, 703 00 Ostrava (dále jen „Poskytovatel"), upravují vzájemná práva a povinnosti smluvních stran vzniklé v souvislosti s objednávkou služeb.</p>

<h2>Předmět smlouvy</h2>
<p>Předmětem smlouvy je poskytování služeb v oblasti kybernetické bezpečnosti, včetně školení, penetračních testů, auditů a poradenství dle aktuální nabídky Poskytovatele.</p>

<h2>Objednávka a uzavření smlouvy</h2>
<p>Objednávka je závazná okamžikem jejího odeslání prostřednictvím objednávkového formuláře. Smlouva je uzavřena potvrzením objednávky ze strany Poskytovatele.</p>

<h2>Cena a platební podmínky</h2>
<p>Ceny jsou uvedeny včetně DPH, není-li výslovně uvedeno jinak. Platba probíhá na základě faktury se splatností 14 dní, případně online platbou při objednávce.</p>

<h2>Storno podmínky</h2>
<p>Objednavatel může bezplatně zrušit objednávku školení nejpozději 7 pracovních dnů před termínem konání. Při pozdějším zrušení může být účtován storno poplatek ve výši 50 % ceny.</p>

<h2>Reklamace</h2>
<p>V případě nespokojenosti s poskytnutou službou je Objednavatel oprávněn uplatnit reklamaci písemně do 14 dnů od poskytnutí služby na e-mail hello@xevos.eu.</p>

<h2>Závěrečná ustanovení</h2>
<p>Tyto obchodní podmínky nabývají účinnosti dnem jejich zveřejnění. Poskytovatel si vyhrazuje právo na jejich změnu. Vztahy těmito podmínkami výslovně neupravené se řídí právním řádem České republiky.</p>' ],
		'Zásady ochrany os. údajů' => [ 'slug' => 'zasady-ochrany-osobnich-udaju', 'template' => 'page-zasady-ochrany-osobnich-udaju.php', 'content' => '<h2>Správce osobních údajů</h2>
<p>Správcem osobních údajů je společnost XEVOS Solutions s.r.o., IČO: 14184290, se sídlem Mostárenská 1156/38, 703 00 Ostrava (dále jen „Správce"). Kontaktní e-mail: hello@xevos.eu.</p>

<h2>Rozsah zpracování osobních údajů</h2>
<p>Zpracováváme osobní údaje, které nám poskytnete v rámci objednávky služeb, registrace na školení nebo kontaktního formuláře. Jedná se zejména o: jméno a příjmení, e-mailovou adresu, telefonní číslo, fakturační údaje (název firmy, IČO, DIČ, adresa).</p>

<h2>Účel zpracování</h2>
<p>Osobní údaje zpracováváme za účelem: plnění smlouvy a poskytování objednaných služeb, zasílání informací o objednávce a fakturace, komunikace s klienty, plnění zákonných povinností (účetnictví, daňová evidence), zasílání obchodních sdělení (pouze s vaším souhlasem).</p>

<h3>Právní základ zpracování</h3>
<p>Zpracování je založeno na: plnění smlouvy (čl. 6 odst. 1 písm. b) GDPR), oprávněném zájmu správce (čl. 6 odst. 1 písm. f) GDPR), plnění právní povinnosti (čl. 6 odst. 1 písm. c) GDPR), souhlasu subjektu údajů (čl. 6 odst. 1 písm. a) GDPR).</p>

<h2>Doba uchovávání údajů</h2>
<p>Osobní údaje uchováváme po dobu nezbytnou k naplnění účelu zpracování, nejdéle však po dobu stanovenou příslušnými právními předpisy (typicky 10 let pro účetní doklady).</p>

<h2>Příjemci osobních údajů</h2>
<p>Vaše údaje mohou být předány poskytovatelům služeb, kteří pro nás zajišťují zpracování dat (hosting, fakturace, e-mailové služby). Všichni příjemci jsou vázáni povinností mlčenlivosti a zpracovávají údaje pouze dle našich pokynů.</p>

<h2>Vaše práva</h2>
<p>Máte právo na přístup k údajům, opravu, výmaz, omezení zpracování, přenositelnost údajů a právo vznést námitku. Svá práva můžete uplatnit na e-mailu hello@xevos.eu. Máte rovněž právo podat stížnost u Úřadu pro ochranu osobních údajů (www.uoou.cz).</p>

<h2>Zabezpečení údajů</h2>
<p>Přijali jsme vhodná technická a organizační opatření k ochraně vašich osobních údajů před neoprávněným přístupem, změnou, ztrátou nebo zničením.</p>

<h2>Cookies</h2>
<p>Informace o používání cookies naleznete v samostatném dokumentu <a href="/zasady-cookies/">Zásady cookies</a>.</p>' ],
		'Zásady cookies'           => [ 'slug' => 'zasady-cookies', 'template' => 'page-zasady-cookies.php', 'content' => '<h2>Co jsou cookies</h2>
<p>Cookies jsou malé textové soubory, které se ukládají do vašeho prohlížeče při návštěvě webových stránek. Slouží k zajištění správného fungování webu, analýze návštěvnosti a personalizaci obsahu.</p>

<h2>Jaké cookies používáme</h2>

<h3>Nezbytné cookies</h3>
<p>Tyto cookies jsou nutné pro správné fungování webu. Nelze je vypnout. Zahrnují cookies pro správu relace, bezpečnostní tokeny a preference souhlasu s cookies.</p>

<h3>Analytické cookies</h3>
<p>Pomáhají nám porozumět, jak návštěvníci používají naše stránky. Data jsou anonymizována. Používáme Google Analytics 4.</p>

<h3>Marketingové cookies</h3>
<p>Slouží k zobrazování relevantních reklam a měření účinnosti reklamních kampaní. Používají se pouze s vaším výslovným souhlasem.</p>

<h2>Správa cookies</h2>
<p>Při první návštěvě webu vás požádáme o souhlas s používáním volitelných cookies. Svůj souhlas můžete kdykoliv odvolat nebo změnit v nastavení cookies na našem webu nebo v nastavení vašeho prohlížeče.</p>

<h2>Doba uchovávání</h2>
<p>Nezbytné cookies: po dobu relace nebo max. 1 rok. Analytické cookies: max. 26 měsíců. Marketingové cookies: max. 12 měsíců.</p>

<h2>Další informace</h2>
<p>Podrobnosti o zpracování osobních údajů naleznete v <a href="/zasady-ochrany-osobnich-udaju/">Zásadách ochrany osobních údajů</a>. V případě dotazů nás kontaktujte na hello@xevos.eu.</p>' ],
		'Platba OK'                => [ 'slug' => 'platba-ok', 'template' => 'page-platba-ok.php' ],
		'Platba chyba'             => [ 'slug' => 'platba-chyba', 'template' => 'page-platba-chyba.php' ],
	];

	$front_page_id = 0;

	foreach ( $pages as $title => $data ) {
		if ( get_page_by_path( $data['slug'] ) ) continue;

		$id = wp_insert_post( [
			'post_type'    => 'page',
			'post_title'   => $title,
			'post_name'    => $data['slug'],
			'post_status'  => 'publish',
			'post_content' => $data['content'] ?? '',
		] );

		if ( $id && $data['template'] ) {
			update_post_meta( $id, '_wp_page_template', $data['template'] );
		}

		// Set last update date for legal pages.
		if ( $id && function_exists( 'update_field' ) && in_array( $data['slug'], [ 'obchodni-podminky', 'zasady-ochrany-osobnich-udaju', 'zasady-cookies' ], true ) ) {
			update_field( 'datum_posledni_aktualizace', '01.01.2026', $id );
		}

		if ( $data['slug'] === 'uvod' ) {
			$front_page_id = $id;
		}
	}

	// Set front page.
	if ( $front_page_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page_id );
	}

	// =========================================================================
	// 3. Homepage ACF fields
	// =========================================================================
	if ( $front_page_id && function_exists( 'update_field' ) ) {
		// Hero.
		update_field( 'hero_zobrazit_sekci', true, $front_page_id );
		update_field( 'hero_heading', 'GARANCE <span>KYBERNETICKÉ ODOLNOSTI</span>', $front_page_id );
		update_field( 'hero_subheading', 'Bez strašení. S jistotou', $front_page_id );
		update_field( 'hero_popis', 'Pomáháme firmám detekovat hrozby, testujeme systémy a připravujeme týmy na skutečné útoky.', $front_page_id );
		update_field( 'hero_cta_text', 'Chci otestovat zabezpečení', $front_page_id );
		update_field( 'hero_cta_url', home_url( '/kontakt/' ), $front_page_id );

		// Služby.
		update_field( 'sluzby_zobrazit_sekci', true, $front_page_id );
		update_field( 'sluzby_heading', 'Naše služby', $front_page_id );
		update_field( 'sluzby', [
			[ 'nazev' => 'Dohledové centrum', 'popis' => 'Security Operations Center – 24/7 monitoring, detekce incidentů a okamžitá reakce na bezpečnostní události.', 'ikona' => '', 'url' => home_url( '/sluzby/' ) ],
			[ 'nazev' => 'Penetrační testy', 'popis' => 'Simulace útoku na vaše systémy s cílem identifikovat zranitelná místa a navrhnout účinná opatření k jejich eliminaci.', 'ikona' => '', 'url' => home_url( '/kyberneticke-testovani/' ) ],
			[ 'nazev' => 'Edukace', 'popis' => 'Praktická školení a workshopy, jejichž cílem je zvýšení bezpečnostního povědomí zaměstnanců a celé organizace.', 'ikona' => '', 'url' => home_url( '/skoleni/' ) ],
			[ 'nazev' => 'Kyber politika', 'popis' => 'Nastavení bezpečnostních politik, NIS2/NIS, směrnic, firemní postupy k dosažení kybernetické odolnosti.', 'ikona' => '', 'url' => home_url( '/nis2/' ) ],
		], $front_page_id );

		// Kyber testování.
		update_field( 'kyber_test_zobrazit_sekci', true, $front_page_id );
		update_field( 'kyber_test_heading', 'Kybernetické testování', $front_page_id );
		update_field( 'kyber_test_text', '<p>Ověření bezpečnostních systémů prostřednictvím penetračních útoků a technických analýz, které odhalují reálné zranitelnosti.</p>', $front_page_id );
		update_field( 'kyber_test_cta_text', 'Chci test', $front_page_id );
		update_field( 'kyber_test_cta_url', home_url( '/kyberneticke-testovani/' ), $front_page_id );

		// Statistiky.
		update_field( 'statistiky_zobrazit_sekci', true, $front_page_id );
		update_field( 'statistiky', [
			[ 'cislo' => '40%', 'popis' => 'Počet vysokých nebo kritických zranitelností' ],
			[ 'cislo' => '87%', 'popis' => 'Firem zasažených phishingovým útokem' ],
			[ 'cislo' => '3,5M', 'popis' => 'Průměrná škoda kybernetického incidentu v ČR' ],
		], $front_page_id );

		// Kyber politika.
		update_field( 'kyber_politika_zobrazit_sekci', true, $front_page_id );
		update_field( 'kyber_politika_heading', 'Kybernetická politika', $front_page_id );
		update_field( 'kyber_politika_text', '<p>Soubor pravidel a postupů, které určují, jak organizace řídí bezpečnost, přístup, rizika a provozní standardy.</p>', $front_page_id );
		update_field( 'kyber_politika_seznam', [
			[ 'bod' => 'NIS 2 compliance' ],
			[ 'bod' => 'DORA compliance' ],
			[ 'bod' => 'Příprava na ISO certifikace' ],
		], $front_page_id );

		// Eventy.
		update_field( 'eventy_zobrazit_sekci', true, $front_page_id );
		update_field( 'eventy_heading', 'Aktuální eventy', $front_page_id );
		update_field( 'eventy_popis', 'Workshopy, školení a odborné akce zaměřené na praktickou kybernetickou bezpečnost, aktuální hrozby a reálné scénáře z praxe.', $front_page_id );
		update_field( 'eventy_pocet', 3, $front_page_id );

		// Aktuality.
		update_field( 'aktuality_zobrazit_sekci', true, $front_page_id );
		update_field( 'aktuality_heading', 'Kyber aktuality', $front_page_id );
		update_field( 'aktuality_pocet', 9, $front_page_id );

		// Recenze.
		update_field( 'recenze_zobrazit_sekci', true, $front_page_id );
		update_field( 'recenze_heading', 'Řekli o nás', $front_page_id );

		// CTA.
		update_field( 'cta_zobrazit_sekci', false, $front_page_id );
	}

	// =========================================================================
	// 4. Aktuality (sample blog posts)
	// =========================================================================
	$aktuality = [
		[
			'title'    => 'NIS2: Co přináší nová směrnice a jak se připravit',
			'excerpt'  => 'Směrnice NIS2 rozšiřuje povinnosti v oblasti kybernetické bezpečnosti na širší okruh organizací. Přinášíme přehled klíčových změn.',
			'content'  => "<h2>Co je NIS2?</h2>\n<p>Směrnice NIS2 je nová evropská legislativa zaměřená na posílení kybernetické bezpečnosti v celé EU. Vstoupila v platnost v lednu 2023 a členské státy ji musí implementovat do října 2024.</p>\n\n<h2>Koho se týká?</h2>\n<p>NIS2 výrazně rozšiřuje okruh subjektů, které spadají pod regulaci. Nově se týká i středních podniků v klíčových sektorech jako energetika, doprava, zdravotnictví, digitální infrastruktura a výrobní průmysl.</p>\n\n<h2>Klíčové požadavky</h2>\n<ul>\n<li>Řízení kybernetických rizik</li>\n<li>Povinné hlášení incidentů (24h / 72h)</li>\n<li>Odpovědnost vedení organizace</li>\n<li>Supply chain security</li>\n<li>Business continuity plánování</li>\n</ul>\n\n<h2>Jak se připravit?</h2>\n<p>Doporučujeme začít gap analýzou stávajícího stavu bezpečnosti a identifikovat oblasti, kde nesplňujete požadavky NIS2. XEVOS vám s tímto procesem rádi pomůže.</p>",
			'category' => 'Legislativa & NIS2',
		],
		[
			'title'    => 'Dohledové centrum: Jak funguje SOC a proč ho potřebujete',
			'excerpt'  => 'Security Operations Center je klíčovým prvkem moderní kybernetické obrany. Vysvětlujeme, jak funguje a co přináší.',
			'content'  => "<h2>Co je SOC?</h2>\n<p>Security Operations Center (SOC) je centralizované pracoviště, které nepřetržitě monitoruje, detekuje a reaguje na bezpečnostní incidenty v IT infrastruktuře organizace.</p>\n\n<h2>Proč potřebujete SOC?</h2>\n<p>Kybernetické útoky probíhají 24/7. Bez nepřetržitého dohledu mohou útočníci zůstat v síti nepozorováni dny až měsíce. SOC zajišťuje, že každá anomálie je okamžitě vyhodnocena a řešena.</p>\n\n<h2>Jak funguje náš SOC</h2>\n<p>Náš dohledový tým využívá kombinaci SIEM nástrojů, threat intelligence a automatizovaných pravidel pro detekci. Každý alert je posouzen analytikem a v případě potvrzeného incidentu je zahájena response procedura.</p>",
			'category' => 'Cloud & infrastruktura',
		],
		[
			'title'    => 'Penetrační testy: Průvodce pro firmy',
			'excerpt'  => 'Co jsou penetrační testy, proč je provádět a co očekávat? Kompletní průvodce pro manažery a IT vedoucí.',
			'content'  => "<h2>Co je penetrační test?</h2>\n<p>Penetrační test (pentest) je autorizovaná simulace kybernetického útoku na IT systémy organizace. Cílem je identifikovat zranitelnosti dříve, než je zneužije skutečný útočník.</p>\n\n<h2>Typy penetračních testů</h2>\n<ul>\n<li><strong>Black box</strong> – tester nemá žádné informace o systému</li>\n<li><strong>White box</strong> – tester má kompletní přístup ke zdrojovým kódům a dokumentaci</li>\n<li><strong>Gray box</strong> – kombinace obou přístupů</li>\n</ul>\n\n<h2>Co získáte?</h2>\n<p>Detailní report s popisem nalezených zranitelností, jejich závažností a doporučením pro nápravu. Součástí je i executive summary pro management.</p>",
			'category' => 'Analýzy hrozeb',
		],
		[
			'title'    => 'Kybernetická bezpečnost v cloudu: Best practices 2026',
			'excerpt'  => 'S přechodem do cloudu přibývají nové výzvy. Přinášíme přehled best practices pro bezpečný cloud.',
			'content'  => "<h2>Cloud security v roce 2026</h2>\n<p>Stále více organizací přesouvá svou infrastrukturu do cloudu. S tím přicházejí nové bezpečnostní výzvy, které vyžadují odlišný přístup než tradiční on-premise řešení.</p>\n\n<h2>Klíčové principy</h2>\n<ul>\n<li>Zero Trust architektura</li>\n<li>Šifrování dat at rest i in transit</li>\n<li>Identity and Access Management (IAM)</li>\n<li>Pravidelné audity a compliance checks</li>\n<li>Monitoring a logging</li>\n</ul>",
			'category' => 'Cloud',
		],
		[
			'title'    => 'GDPR a kybernetická bezpečnost: Propojení povinností',
			'excerpt'  => 'GDPR a kybernetická bezpečnost jsou úzce propojeny. Jak zajistit soulad s oběma regulacemi současně?',
			'content'  => "<h2>GDPR a bezpečnost</h2>\n<p>GDPR vyžaduje implementaci \"přiměřených technických a organizačních opatření\" pro ochranu osobních údajů. To přímo souvisí s kybernetickou bezpečností organizace.</p>\n\n<h2>Společné požadavky s NIS2</h2>\n<p>Mnoho požadavků GDPR a NIS2 se překrývá. Správná implementace kybernetické bezpečnosti vám pomůže splnit obě regulace současně.</p>",
			'category' => 'GDPR',
		],
		[
			'title'    => 'Phishing v roce 2026: Nové trendy a jak se bránit',
			'excerpt'  => 'Phishingové útoky jsou stále sofistikovanější. AI generovaný obsah a deep fake technologie zvyšují úspěšnost útoků.',
			'content'  => "<h2>Evoluce phishingu</h2>\n<p>Phishing v roce 2026 už dávno není jen e-mail od \"nigerijského prince\". Útočníci využívají AI pro generování personalizovaných zpráv, deep fake technologie pro vishing (voice phishing) a sofistikované sociální inženýrství.</p>\n\n<h2>Jak se bránit</h2>\n<ul>\n<li>Pravidelné awareness školení zaměstnanců</li>\n<li>Simulované phishingové kampaně</li>\n<li>Multi-factor authentication (MFA)</li>\n<li>Email security gateway</li>\n<li>Zero Trust přístup</li>\n</ul>",
			'category' => 'Analýzy hrozeb',
		],
		[
			'title'    => 'Azure Sentinel: Nasazení SIEM v cloudu',
			'excerpt'  => 'Microsoft Azure Sentinel nabízí cloud-native SIEM řešení. Jak ho efektivně nasadit a co od něj očekávat?',
			'content'  => "<h2>Co je Azure Sentinel?</h2>\n<p>Azure Sentinel je cloudová platforma pro správu bezpečnostních informací a událostí (SIEM) a orchestraci bezpečnostních odpovědí (SOAR). Umožňuje sběr dat z celé infrastruktury a jejich analýzu pomocí AI.</p>\n\n<h2>Výhody nasazení</h2>\n<ul>\n<li>Škálovatelnost bez nutnosti správy HW</li>\n<li>Integrace s Microsoft 365 a Azure AD</li>\n<li>Automatizované playbooks pro response</li>\n<li>Machine learning detekce anomálií</li>\n</ul>",
			'category' => 'Azure',
		],
		[
			'title'    => 'Azure Active Directory: Zabezpečení identit v hybridním prostředí',
			'excerpt'  => 'Správa identit je klíčem k bezpečnosti. Jak využít Azure AD pro ochranu přístupu v organizaci?',
			'content'  => "<h2>Azure AD a Zero Trust</h2>\n<p>Azure Active Directory je základní stavební kámen Zero Trust strategie. Conditional Access políčky, MFA a Privileged Identity Management jsou nástroje, které dramaticky snižují riziko kompromitace účtů.</p>\n\n<h2>Best practices</h2>\n<ul>\n<li>Povinné MFA pro všechny uživatele</li>\n<li>Conditional Access na bázi rizika</li>\n<li>Pravidelná revize oprávnění</li>\n<li>Monitoring sign-in logů</li>\n</ul>",
			'category' => 'Azure',
		],
		[
			'title'    => 'Školení kybernetické bezpečnosti: Proč je povinné od roku 2025',
			'excerpt'  => 'Nový zákon o kybernetické bezpečnosti ukládá povinnost pravidelného školení zaměstnanců. Co to znamená pro vaši firmu?',
			'content'  => "<h2>Legislativní požadavky</h2>\n<p>S transpozicí NIS2 do českého práva vzniká povinnost pravidelného školení zaměstnanců v oblasti kybernetické bezpečnosti. Školení musí být dokumentované a jeho obsah musí odpovídat aktuálním hrozbám.</p>\n\n<h2>Co musí školení obsahovat</h2>\n<ul>\n<li>Rozpoznání phishingu a sociálního inženýrství</li>\n<li>Bezpečná práce s hesly a MFA</li>\n<li>Hlášení bezpečnostních incidentů</li>\n<li>Ochrana firemních dat</li>\n<li>Bezpečnost při práci z domova</li>\n</ul>",
			'category' => 'Školení',
		],
		[
			'title'    => 'Awareness programy: Jak efektivně vzdělávat zaměstnance',
			'excerpt'  => 'Technologie nestačí — lidský faktor je nejslabší článek. Jak vybudovat efektivní awareness program?',
			'content'  => "<h2>Proč awareness?</h2>\n<p>95 % bezpečnostních incidentů má svůj původ v lidské chybě. Pravidelné awareness programy výrazně snižují riziko úspěšného útoku na organizaci.</p>\n\n<h2>Efektivní program zahrnuje</h2>\n<ul>\n<li>Simulované phishingové kampaně</li>\n<li>Interaktivní e-learningové moduly</li>\n<li>Gamifikace a soutěže</li>\n<li>Pravidelné testování a měření pokroku</li>\n</ul>",
			'category' => 'Školení',
		],
		[
			'title'    => 'Případová studie: Jak jsme zabezpečili výrobní podnik proti ransomwaru',
			'excerpt'  => 'Reálný příběh zabezpečení výrobního podniku s 500 zaměstnanci. Od gap analýzy po implementaci SOC.',
			'content'  => "<h2>Výchozí stav</h2>\n<p>Středně velký výrobní podnik s 500 zaměstnanci čelil opakovaným pokusům o ransomware útok. IT oddělení nemělo kapacity na nepřetržitý monitoring a response.</p>\n\n<h2>Naše řešení</h2>\n<ul>\n<li>Gap analýza a risk assessment</li>\n<li>Nasazení EDR na všechny endpointy</li>\n<li>Napojení na SOC 24/7</li>\n<li>Segmentace OT a IT sítí</li>\n<li>Awareness školení pro zaměstnance</li>\n</ul>\n\n<h2>Výsledek</h2>\n<p>Za 6 měsíců od implementace bylo detekováno a neutralizováno 12 bezpečnostních incidentů, včetně 2 pokusů o ransomware. Žádný z nich nevedl k výpadku výroby.</p>",
			'category' => 'Případové studie',
		],
		[
			'title'    => 'Případová studie: NIS2 compliance pro energetickou společnost',
			'excerpt'  => 'Jak jsme pomohli energetické společnosti splnit požadavky NIS2 za 4 měsíce.',
			'content'  => "<h2>Zadání</h2>\n<p>Energetická společnost spadající pod NIS2 jako essential entity potřebovala v krátkém čase dosáhnout souladu s novou legislativou. Deadline se blížil a interní kapacity nestačily.</p>\n\n<h2>Postup</h2>\n<ul>\n<li>Maturity assessment dle NIS2 požadavků</li>\n<li>Tvorba bezpečnostní dokumentace</li>\n<li>Implementace incident response procesu</li>\n<li>Nastavení supply chain security</li>\n<li>Školení managementu a klíčových zaměstnanců</li>\n</ul>\n\n<h2>Výsledek</h2>\n<p>Za 4 měsíce byla společnost připravena na audit. Všechny klíčové požadavky NIS2 byly splněny a organizace získala strukturovaný přístup k řízení kybernetické bezpečnosti.</p>",
			'category' => 'Případové studie',
		],
		[
			'title'    => 'Zero Trust architektura: Kompletní průvodce implementací',
			'excerpt'  => 'Zero Trust není jen buzzword. Praktický průvodce nasazením Zero Trust modelu ve vaší organizaci.',
			'content'  => "<h2>Principy Zero Trust</h2>\n<p>Nikdy neduveruj, vzdy overuj - Zero Trust meni paradigma sitove bezpecnosti. Kazdy pozadavek je overovan bez ohledu na to, zda pochazi z vnitrni nebo vnejsi site.</p>",
			'category' => 'Cloud & infrastruktura',
		],
		[
			'title'    => 'DORA regulace: Co znamená pro finanční sektor',
			'excerpt'  => 'Digital Operational Resilience Act přináší nové povinnosti pro finanční instituce v oblasti ICT rizik.',
			'content'  => "<h2>Co je DORA?</h2>\n<p>DORA je evropská regulace zaměřená na digitální provozní odolnost finančního sektoru. Stanovuje požadavky na řízení ICT rizik, hlášení incidentů a testování odolnosti.</p>",
			'category' => 'Legislativa & NIS2',
		],
		[
			'title'    => 'Ransomware 2026: Nejnovější taktiky útočníků',
			'excerpt'  => 'Ransomware gangy mění strategie. Double extortion, supply chain útoky a cílení na kritickou infrastrukturu.',
			'content'  => "<h2>Evoluce ransomwaru</h2>\n<p>Ransomware útoky v roce 2026 jsou sofistikovanější než kdykoli předtím. Útočníci kombinují šifrování dat s hrozbou jejich zveřejnění (double extortion).</p>",
			'category' => 'Analýzy hrozeb',
		],
		[
			'title'    => 'Cloud migrace: Bezpečnostní checklist před přesunem',
			'excerpt'  => 'Plánujete migraci do cloudu? Tady je bezpečnostní checklist, který byste neměli ignorovat.',
			'content'  => "<h2>Před migrací</h2>\n<p>Migrace do cloudu bez bezpečnostního plánu je recept na katastrofu. Připravili jsme checklist kritických kroků, které musíte provést ještě před samotným přesunem.</p>",
			'category' => 'Cloud',
		],
		[
			'title'    => 'Azure Security Center: Monitoring a ochrana cloudových zdrojů',
			'excerpt'  => 'Jak využít Azure Security Center pro nepřetržitou ochranu vašich cloudových workloadů.',
			'content'  => "<h2>Azure Security Center</h2>\n<p>Azure Security Center (nyní Microsoft Defender for Cloud) poskytuje jednotný pohled na bezpečnostní stav vašich cloudových a hybridních prostředí.</p>",
			'category' => 'Azure',
		],
		[
			'title'    => 'ISO 27001: Cesta k certifikaci krok za krokem',
			'excerpt'  => 'Certifikace ISO 27001 posiluje důvěru klientů. Provádíme vás celým procesem od gap analýzy po úspěšný audit.',
			'content'  => "<h2>Proč ISO 27001?</h2>\n<p>ISO 27001 je mezinárodně uznávaný standard pro systémy řízení informační bezpečnosti (ISMS). Certifikace prokazuje, že organizace systematicky chrání svá informační aktiva.</p>",
			'category' => 'Školení',
		],
		[
			'title'    => 'Incident Response: Jak reagovat na kybernetický útok',
			'excerpt'  => 'Když dojde k incidentu, počítá se každá minuta. Jak by měl vypadat váš incident response plán?',
			'content'  => "<h2>Fáze incident response</h2>\n<ul>\n<li>Příprava – plány, nástroje, školení týmu</li>\n<li>Identifikace – detekce a potvrzení incidentu</li>\n<li>Containment – izolace zasažených systémů</li>\n<li>Eradikace – odstranění hrozby</li>\n<li>Recovery – obnova provozu</li>\n<li>Lessons learned – poučení a zlepšení</li>\n</ul>",
			'category' => 'Cloud & infrastruktura',
		],
		[
			'title'    => 'Případová studie: Detekce APT útoku v bankovním sektoru',
			'excerpt'  => 'Jak náš SOC tým detekoval pokročilý perzistentní útok na systémy české banky.',
			'content'  => "<h2>Situace</h2>\n<p>Anomální síťový provoz detekovaný naším SOC vedl k odhalení sofistikovaného APT útoku, který byl v síti banky přítomen již 3 týdny.</p>\n\n<h2>Řešení</h2>\n<p>Okamžitý containment, forenzní analýza a koordinovaná response vedly k úplné eradikaci hrozby bez úniku dat.</p>",
			'category' => 'Případové studie',
		],
		[
			'title'    => 'Supply Chain Security: Slabé místo vaší bezpečnosti',
			'excerpt'  => 'Útočníci cílí na dodavatelský řetězec. Jak ověřit bezpečnost svých dodavatelů?',
			'content'  => "<h2>Hrozba z dodavatelského řetězce</h2>\n<p>SolarWinds, Kaseya, MOVEit – útoky přes dodavatelský řetězec jsou jednou z největších hrozeb současnosti. Organizace musí aktivně řešit bezpečnost svých dodavatelů.</p>",
			'category' => 'Analýzy hrozeb',
		],
		[
			'title'    => 'Multi-cloud strategie: Bezpečnostní výzvy a řešení',
			'excerpt'  => 'Používáte AWS, Azure i GCP? Multi-cloud přináší flexibilitu, ale i komplexní bezpečnostní výzvy.',
			'content'  => "<h2>Multi-cloud realita</h2>\n<p>Většina velkých organizací dnes provozuje workloady u více cloudových poskytovatelů. To přináší potřebu jednotného bezpečnostního frameworku napříč platformami.</p>",
			'category' => 'Cloud',
		],
		[
			'title'    => 'Vulnerability Management: Systematický přístup k zranitelnostem',
			'excerpt'  => 'Skenování zranitelností je jen začátek. Jak vybudovat efektivní vulnerability management program?',
			'content'  => "<h2>Více než skenování</h2>\n<p>Vulnerability management není jen o spouštění skeneru jednou za měsíc. Je to kontinuální proces identifikace, hodnocení, prioritizace a nápravy zranitelností.</p>",
			'category' => 'Analýzy hrozeb',
		],
		[
			'title'    => 'Případová studie: Implementace SOC pro logistickou firmu',
			'excerpt'  => 'Jak jsme za 8 týdnů nasadili plně funkční SOC pro mezinárodní logistickou společnost.',
			'content'  => "<h2>Zadání</h2>\n<p>Logistická firma s operacemi v 5 zemích EU potřebovala centralizovaný bezpečnostní dohled. Požadavek: plný provoz do 2 měsíců.</p>\n\n<h2>Výsledek</h2>\n<p>SOC nasazen za 8 týdnů, 400+ endpointů pod dohledem, průměrná doba detekce incidentu snížena z dnů na minuty.</p>",
			'category' => 'Případové studie',
		],
		[
			'title'    => 'GDPR pokuty v ČR: Přehled a poučení z praxe',
			'excerpt'  => 'ÚOOÚ udělil rekordní pokuty za porušení GDPR. Co z toho plyne pro vaši organizaci?',
			'content'  => "<h2>Přehled pokut</h2>\n<p>Český Úřad pro ochranu osobních údajů zpřísnil dohled nad dodržováním GDPR. V posledním roce uložil pokuty v celkové výši přes 50 milionů korun.</p>",
			'category' => 'GDPR',
		],
	];

	// Create taxonomy terms first.
	$cat_terms = [ 'Azure', 'Cloud', 'Legislativa & NIS2', 'Cloud & infrastruktura', 'Školení', 'Analýzy hrozeb', 'Případové studie', 'GDPR' ];
	foreach ( $cat_terms as $term_name ) {
		if ( ! term_exists( $term_name, 'kategorie-aktualit' ) ) {
			wp_insert_term( $term_name, 'kategorie-aktualit' );
		}
	}

	foreach ( $aktuality as $a ) {
		if ( xevos_post_exists_by_title( $a['title'], 'aktualita' ) ) continue;

		$post_id = wp_insert_post( [
			'post_type'    => 'aktualita',
			'post_title'   => $a['title'],
			'post_excerpt' => $a['excerpt'],
			'post_content' => $a['content'],
			'post_status'  => 'publish',
		] );

		if ( $post_id && $a['category'] ) {
			wp_set_object_terms( $post_id, $a['category'], 'kategorie-aktualit' );
		}
	}

	// =========================================================================
	// 5. Školení (sample trainings)
	// =========================================================================
	$skoleni_data = [
		[
			'title'  => 'Kybernetická bezpečnost – OBECNÁ',
			'typ'    => 'prezencni',
			'cena'   => 4132,
			'cena_dph' => 5000,
			'popis'  => '<p>S novým ZoKB je školení kyberbezpečnosti povinné pro zaměstnance pracující se systémy či daty. Povinné označit odolnost firmy a podporuje plnění ZoKB a NIS2.</p><p>Všichni zaměstnanci pracující s počítačem, e-mailem nebo firemními daty. Noví nastupující pracovníci, kteří musí být proškoleni při nástupu do organizace. Zaměstnanci subjektů spadajících pod povinnosti ZoKB/NIS2, u nichž musí být školení pravidelné a dokumentované.</p>',
			'terminy' => [
				[ 'datum' => '25.01.2026', 'cas_od' => '08:00', 'cas_do' => '16:00', 'misto' => 'Mostárenská 1156/38, 703 00 Ostrava', 'kapacita' => 20, 'pocet_registraci' => 5 ],
				[ 'datum' => '02.02.2026', 'cas_od' => '08:00', 'cas_do' => '16:00', 'misto' => 'Mostárenská 1156/38, 703 00 Ostrava', 'kapacita' => 20, 'pocet_registraci' => 3 ],
				[ 'datum' => '05.02.2026', 'cas_od' => '08:00', 'cas_do' => '16:00', 'misto' => 'Mostárenská 1156/38, 703 00 Ostrava', 'kapacita' => 20, 'pocet_registraci' => 0 ],
				[ 'datum' => '08.02.2026', 'cas_od' => '08:00', 'cas_do' => '16:00', 'misto' => 'Mostárenská 1156/38, 703 00 Ostrava', 'kapacita' => 20, 'pocet_registraci' => 12 ],
				[ 'datum' => '16.02.2026', 'cas_od' => '08:00', 'cas_do' => '16:00', 'misto' => 'Mostárenská 1156/38, 703 00 Ostrava', 'kapacita' => 20, 'pocet_registraci' => 0 ],
				[ 'datum' => '25.02.2026', 'cas_od' => '08:00', 'cas_do' => '16:00', 'misto' => 'Mostárenská 1156/38, 703 00 Ostrava', 'kapacita' => 20, 'pocet_registraci' => 0 ],
			],
			'lektori' => [
				[ 'jmeno' => 'Petr Letocha', 'pozice' => 'Cyber Security Center Manager', 'bio' => 'Petr je odborník na kybernetickou bezpečnost se specializací na implementaci legislativních požadavků. Zaměřuje se na řízení bezpečnostních rizik, nastavování bezpečnostních procesů a zajištění souladu organizací s regulatorními normami.', 'foto' => '' ],
				[ 'jmeno' => 'Václav Herec', 'pozice' => 'Cyber Security Specialist', 'bio' => 'Václav je odborný specialista na penetrační testování a odhalování bezpečnostních slabých míst v IT systémech. Spolupracuje na projektech zaměřených na zvyšování bezpečnosti a dodržování certifikačních požadavků.', 'foto' => '' ],
			],
			'harmonogram' => [
				[ 'cas' => '08:00 – Registrace a snídaně', 'aktivita' => '' ],
				[ 'cas' => '10:30–10:45 – Coffee break', 'aktivita' => '' ],
				[ 'cas' => '10:45–12:00 – 2. blok (1,25 h)', 'aktivita' => '' ],
				[ 'cas' => '12:00–13:00 – Oběd', 'aktivita' => '' ],
				[ 'cas' => '13:00–15:00 – 3. blok (2 h)', 'aktivita' => '' ],
				[ 'cas' => '15:45–16:00 – Diskuse / Závěr', 'aktivita' => '' ],
			],
			'osnova' => [
				[ 'bod' => 'Legislativní minimum: požadavky ZoKB, NIS2 a povinnosti zaměstnanců' ],
				[ 'bod' => 'Hlášení kybernetických incidentů: jak poznám incident a komu ho hlásit' ],
				[ 'bod' => 'Nejčastější hrozby: phishing, sociální inženýrství, ransomware, malware' ],
				[ 'bod' => 'Bezpečné chování: heslová politika, práce s daty, bezpečný internet, mobilní zařízení' ],
				[ 'bod' => 'Fyzická bezpečnost, home office, vzdálený přístup a zásady práce na služebních zařízeních' ],
				[ 'bod' => 'Bezpečnost dodavatelského řetězce a externích služeb' ],
			],
			'category' => 'Kybernetická bezpečnost',
		],
		[
			'title'  => 'Manažer kybernetické bezpečnosti',
			'typ'    => 'prezencni',
			'cena'   => 4132,
			'cena_dph' => 5000,
			'popis'  => '<p>Školení pro manažery a vedoucí pracovníky zaměřené na strategické řízení kybernetické bezpečnosti v organizaci. Pokrývá legislativní povinnosti, risk management a budování bezpečnostní kultury.</p>',
			'terminy' => [
				[ 'datum' => '03.05.2026', 'cas_od' => '08:00', 'cas_do' => '16:00', 'misto' => 'Mostárenská 1156/38, 703 00 Ostrava', 'kapacita' => 15, 'pocet_registraci' => 2 ],
			],
			'lektori' => [
				[ 'jmeno' => 'Petr Letocha', 'bio' => 'Cyber Security Center Manager', 'foto' => '' ],
			],
			'harmonogram' => [],
			'osnova' => [
				[ 'bod' => 'Strategické řízení kybernetické bezpečnosti' ],
				[ 'bod' => 'Odpovědnost vedení dle NIS2 a ZoKB' ],
				[ 'bod' => 'Risk management framework' ],
				[ 'bod' => 'Incident response plánování' ],
				[ 'bod' => 'Budování bezpečnostní kultury v organizaci' ],
			],
			'category' => 'Kybernetická bezpečnost',
		],
		[
			'title'  => 'Pověřená osoba kybernetické bezpečnosti',
			'typ'    => 'online',
			'cena'   => 4132,
			'cena_dph' => 5000,
			'popis'  => '<p>Specializované školení pro pověřené osoby odpovědné za kybernetickou bezpečnost v organizacích spadajících pod NIS2 a ZoKB.</p>',
			'terminy' => [
				[ 'datum' => '05.03.2026', 'cas_od' => '09:00', 'cas_do' => '15:00', 'misto' => 'Online (Teams)', 'kapacita' => 30, 'pocet_registraci' => 8 ],
			],
			'lektori' => [],
			'harmonogram' => [],
			'osnova' => [
				[ 'bod' => 'Role pověřené osoby v organizaci' ],
				[ 'bod' => 'Legislativní rámec (ZoKB, NIS2)' ],
				[ 'bod' => 'Řízení bezpečnostních incidentů' ],
				[ 'bod' => 'Komunikace s regulátory' ],
			],
			'category' => 'NIS2',
		],
		[
			'title'  => 'Webinar: Jak na NIS2 za 90 minut',
			'typ'    => 'online',
			'cena'   => 0,
			'cena_dph' => 0,
			'popis'  => '<p>Bezplatny webinar zamereny na prakticke kroky pro splneni pozadavku NIS2. Urceno pro management a IT vedouci.</p>',
			'terminy' => [
				[ 'datum' => '15.04.2026', 'cas_od' => '10:00', 'cas_do' => '11:30', 'misto' => 'Online (MS Teams)', 'kapacita' => 100, 'pocet_registraci' => 42 ],
				[ 'datum' => '22.04.2026', 'cas_od' => '10:00', 'cas_do' => '11:30', 'misto' => 'Online (MS Teams)', 'kapacita' => 100, 'pocet_registraci' => 15 ],
			],
			'lektori' => [
				[ 'jmeno' => 'Petr Letocha', 'pozice' => 'Cyber Security Center Manager', 'bio' => '', 'foto' => '' ],
			],
			'harmonogram' => [
				[ 'cas' => '10:00 - Uvod a prehled NIS2', 'aktivita' => '' ],
				[ 'cas' => '10:30 - Prakticke kroky implementace', 'aktivita' => '' ],
				[ 'cas' => '11:00 - Q&A', 'aktivita' => '' ],
			],
			'osnova' => [
				[ 'bod' => 'Zakladni pozadavky NIS2' ],
				[ 'bod' => 'Kdo spada pod regulaci' ],
				[ 'bod' => 'Implementacni plan' ],
			],
			'category' => 'Webináře',
		],
		[
			'title'  => 'Event: Cyber Security Summit 2026',
			'typ'    => 'prezencni',
			'cena'   => 2479,
			'cena_dph' => 3000,
			'popis'  => '<p>Celodenni konference zamerena na aktualni hrozby, legislativu a best practices v kyberneticke bezpecnosti. Networking, workshopy a panelove diskuze.</p>',
			'terminy' => [
				[ 'datum' => '20.05.2026', 'cas_od' => '09:00', 'cas_do' => '17:00', 'misto' => 'Clarion Congress Hotel, Ostrava', 'kapacita' => 200, 'pocet_registraci' => 87 ],
			],
			'lektori' => [
				[ 'jmeno' => 'Petr Letocha', 'pozice' => 'Cyber Security Center Manager', 'bio' => '', 'foto' => '' ],
				[ 'jmeno' => 'Vaclav Herec', 'pozice' => 'Cyber Security Specialist', 'bio' => '', 'foto' => '' ],
			],
			'harmonogram' => [
				[ 'cas' => '09:00 - Registrace', 'aktivita' => '' ],
				[ 'cas' => '09:30 - Keynote: Stav kyberbezpecnosti v CR', 'aktivita' => '' ],
				[ 'cas' => '11:00 - Panel: NIS2 v praxi', 'aktivita' => '' ],
				[ 'cas' => '12:30 - Obed a networking', 'aktivita' => '' ],
				[ 'cas' => '14:00 - Workshopy (paralelne)', 'aktivita' => '' ],
				[ 'cas' => '16:00 - Zaver a networking', 'aktivita' => '' ],
			],
			'osnova' => [
				[ 'bod' => 'Aktualni hrozby a trendy' ],
				[ 'bod' => 'Legislativni novinky' ],
				[ 'bod' => 'Prakticke workshopy' ],
			],
			'category' => 'Eventy',
		],
		[
			'title'  => 'Webinar: Phishing awareness pro firmy',
			'typ'    => 'online',
			'cena'   => 826,
			'cena_dph' => 999,
			'popis'  => '<p>Interaktivni webinar s realnym phishing testem. Zamestnanci si vyzkousi rozpoznavani podvodnych emailu a nauci se spravne reagovat.</p>',
			'terminy' => [
				[ 'datum' => '08.04.2026', 'cas_od' => '14:00', 'cas_do' => '15:30', 'misto' => 'Online (Zoom)', 'kapacita' => 50, 'pocet_registraci' => 23 ],
				[ 'datum' => '29.04.2026', 'cas_od' => '14:00', 'cas_do' => '15:30', 'misto' => 'Online (Zoom)', 'kapacita' => 50, 'pocet_registraci' => 8 ],
			],
			'lektori' => [
				[ 'jmeno' => 'Vaclav Herec', 'pozice' => 'Cyber Security Specialist', 'bio' => '', 'foto' => '' ],
			],
			'harmonogram' => [
				[ 'cas' => '14:00 - Teorie phishingu', 'aktivita' => '' ],
				[ 'cas' => '14:30 - Realny phishing test', 'aktivita' => '' ],
				[ 'cas' => '15:00 - Rozbor vysledku a doporuceni', 'aktivita' => '' ],
			],
			'osnova' => [
				[ 'bod' => 'Typy phishingovych utoku' ],
				[ 'bod' => 'Jak rozpoznat podvod' ],
				[ 'bod' => 'Spravna reakce a hlaseni' ],
			],
			'category' => 'Webináře',
		],
		[
			'title'  => 'Event: Hackathon - Red vs Blue Team',
			'typ'    => 'hybrid',
			'cena'   => 4132,
			'cena_dph' => 5000,
			'popis'  => '<p>Soutezni akce kde se ucastnici rozdeli do Red a Blue tymu. Red tym utoci, Blue tym brani. Prakticky si vyzkouste realne scenare kybernetickych utoku.</p>',
			'terminy' => [
				[ 'datum' => '12.06.2026', 'cas_od' => '09:00', 'cas_do' => '18:00', 'misto' => 'XEVOS HQ, Ostrava + online stream', 'kapacita' => 40, 'pocet_registraci' => 28 ],
			],
			'lektori' => [
				[ 'jmeno' => 'Petr Letocha', 'pozice' => 'Cyber Security Center Manager', 'bio' => '', 'foto' => '' ],
				[ 'jmeno' => 'Vaclav Herec', 'pozice' => 'Cyber Security Specialist', 'bio' => '', 'foto' => '' ],
			],
			'harmonogram' => [
				[ 'cas' => '09:00 - Briefing a rozdeleni tymu', 'aktivita' => '' ],
				[ 'cas' => '10:00 - 1. kolo utoku', 'aktivita' => '' ],
				[ 'cas' => '12:00 - Obed', 'aktivita' => '' ],
				[ 'cas' => '13:00 - 2. kolo utoku', 'aktivita' => '' ],
				[ 'cas' => '16:00 - Vyhodnoceni a ceny', 'aktivita' => '' ],
			],
			'osnova' => [
				[ 'bod' => 'Penetracni testovani v praxi' ],
				[ 'bod' => 'Incident response' ],
				[ 'bod' => 'Tymova spoluprace' ],
			],
			'category' => 'Eventy',
		],
		[
			'title'  => 'GDPR pro IT oddleni',
			'typ'    => 'prezencni',
			'cena'   => 3306,
			'cena_dph' => 4000,
			'popis'  => '<p>Specializovane skoleni GDPR zamerene na technicke aspekty ochrany osobnich udaju. Sifrovani, pseudonymizace, data retention a prava subjektu udaju z pohledu IT.</p>',
			'terminy' => [
				[ 'datum' => '18.04.2026', 'cas_od' => '09:00', 'cas_do' => '15:00', 'misto' => 'Mostarenska 1156/38, 703 00 Ostrava', 'kapacita' => 15, 'pocet_registraci' => 11 ],
			],
			'lektori' => [
				[ 'jmeno' => 'Petr Letocha', 'pozice' => 'Cyber Security Center Manager', 'bio' => '', 'foto' => '' ],
			],
			'harmonogram' => [
				[ 'cas' => '09:00 - Registrace', 'aktivita' => '' ],
				[ 'cas' => '09:30 - GDPR technicke pozadavky', 'aktivita' => '' ],
				[ 'cas' => '12:00 - Obed', 'aktivita' => '' ],
				[ 'cas' => '13:00 - Prakticke cviceni', 'aktivita' => '' ],
			],
			'osnova' => [
				[ 'bod' => 'Sifrovani a pseudonymizace' ],
				[ 'bod' => 'Data retention politiky' ],
				[ 'bod' => 'Prava subjektu udaju' ],
				[ 'bod' => 'Data breach notifikace' ],
			],
			'category' => 'Školení',
		],
		[
			'title'  => 'Cloud Security Fundamentals',
			'typ'    => 'online',
			'cena'   => 2479,
			'cena_dph' => 3000,
			'popis'  => '<p>Zakladni kurz bezpecnosti v cloudu. AWS, Azure, GCP - spolecne principy a specificka rizika kazde platformy.</p>',
			'terminy' => [
				[ 'datum' => '05.05.2026', 'cas_od' => '09:00', 'cas_do' => '13:00', 'misto' => 'Online (MS Teams)', 'kapacita' => 30, 'pocet_registraci' => 12 ],
				[ 'datum' => '19.05.2026', 'cas_od' => '09:00', 'cas_do' => '13:00', 'misto' => 'Online (MS Teams)', 'kapacita' => 30, 'pocet_registraci' => 3 ],
			],
			'lektori' => [
				[ 'jmeno' => 'Vaclav Herec', 'pozice' => 'Cyber Security Specialist', 'bio' => '', 'foto' => '' ],
			],
			'harmonogram' => [
				[ 'cas' => '09:00 - Uvod do cloud security', 'aktivita' => '' ],
				[ 'cas' => '10:30 - Prestavka', 'aktivita' => '' ],
				[ 'cas' => '10:45 - Prakticka cast', 'aktivita' => '' ],
			],
			'osnova' => [
				[ 'bod' => 'Shared responsibility model' ],
				[ 'bod' => 'IAM a pristupova prava' ],
				[ 'bod' => 'Sifrovani v cloudu' ],
				[ 'bod' => 'Monitoring a logging' ],
			],
			'category' => 'Školení',
		],
		[
			'title' => 'Event: NIS2 Compliance Workshop', 'typ' => 'prezencni', 'cena' => 3306, 'cena_dph' => 4000,
			'popis' => '<p>Celodenni workshop zamereny na prakticke kroky k dosazeni souladu s NIS2.</p>',
			'terminy' => [['datum' => '25.04.2026', 'cas_od' => '09:00', 'cas_do' => '16:00', 'misto' => 'Clarion Congress Hotel, Ostrava', 'kapacita' => 30, 'pocet_registraci' => 18]],
			'lektori' => [['jmeno' => 'Petr Letocha', 'pozice' => 'CSC Manager', 'bio' => '', 'foto' => '']],
			'harmonogram' => [['cas' => '09:00 - Gap analyza', 'aktivita' => '']],
			'osnova' => [['bod' => 'NIS2 pozadavky'], ['bod' => 'Gap analyza']],
			'category' => 'Eventy',
		],
		[
			'title' => 'Webinar: DORA pro financni sektor', 'typ' => 'online', 'cena' => 0, 'cena_dph' => 0,
			'popis' => '<p>Bezplatny webinar o pozadavcich DORA regulace pro financni instituce.</p>',
			'terminy' => [['datum' => '10.04.2026', 'cas_od' => '10:00', 'cas_do' => '11:30', 'misto' => 'Online (MS Teams)', 'kapacita' => 100, 'pocet_registraci' => 55]],
			'lektori' => [['jmeno' => 'Petr Letocha', 'pozice' => 'CSC Manager', 'bio' => '', 'foto' => '']],
			'harmonogram' => [['cas' => '10:00 - DORA prehled', 'aktivita' => '']],
			'osnova' => [['bod' => 'DORA zakladni pozadavky']],
			'category' => 'Webináře',
		],
		[
			'title' => 'Incident Response Training', 'typ' => 'prezencni', 'cena' => 5785, 'cena_dph' => 7000,
			'popis' => '<p>Pokrocile skoleni zamerene na reakci na bezpecnostni incidenty. Simulace scenaru, forenzni analyza.</p>',
			'terminy' => [['datum' => '02.05.2026', 'cas_od' => '08:00', 'cas_do' => '17:00', 'misto' => 'XEVOS HQ, Ostrava', 'kapacita' => 15, 'pocet_registraci' => 9]],
			'lektori' => [['jmeno' => 'Vaclav Herec', 'pozice' => 'Security Specialist', 'bio' => '', 'foto' => '']],
			'harmonogram' => [['cas' => '08:00 - Registrace', 'aktivita' => ''], ['cas' => '09:00 - Teorie IR', 'aktivita' => '']],
			'osnova' => [['bod' => 'Faze incident response'], ['bod' => 'Forenzni analyza']],
			'category' => 'Školení',
		],
		[
			'title' => 'Event: CTF soutez pro studenty', 'typ' => 'hybrid', 'cena' => 0, 'cena_dph' => 0,
			'popis' => '<p>Capture The Flag soutez pro studenty. Prakticky hackovaniv kontrolovanem prostredi.</p>',
			'terminy' => [['datum' => '15.06.2026', 'cas_od' => '10:00', 'cas_do' => '18:00', 'misto' => 'VSB-TUO, Ostrava + online', 'kapacita' => 100, 'pocet_registraci' => 67]],
			'lektori' => [['jmeno' => 'Vaclav Herec', 'pozice' => 'Security Specialist', 'bio' => '', 'foto' => '']],
			'harmonogram' => [['cas' => '10:00 - Start', 'aktivita' => '']],
			'osnova' => [['bod' => 'Web exploitation'], ['bod' => 'Kryptografie']],
			'category' => 'Eventy',
		],
		[
			'title' => 'Webinar: Zero Trust architektura', 'typ' => 'online', 'cena' => 826, 'cena_dph' => 999,
			'popis' => '<p>Uvod do Zero Trust modelu. Implementace ve vasi organizaci.</p>',
			'terminy' => [['datum' => '17.04.2026', 'cas_od' => '14:00', 'cas_do' => '15:30', 'misto' => 'Online (Zoom)', 'kapacita' => 50, 'pocet_registraci' => 31]],
			'lektori' => [['jmeno' => 'Petr Letocha', 'pozice' => 'CSC Manager', 'bio' => '', 'foto' => '']],
			'harmonogram' => [['cas' => '14:00 - Zero Trust principy', 'aktivita' => '']],
			'osnova' => [['bod' => 'Zero Trust principy'], ['bod' => 'Case studies']],
			'category' => 'Webináře',
		],
		[
			'title' => 'Awareness skoleni pro management', 'typ' => 'prezencni', 'cena' => 4959, 'cena_dph' => 6000,
			'popis' => '<p>Skoleni pro vedeni firmy. Odpovednost managementu dle NIS2, krizova komunikace.</p>',
			'terminy' => [['datum' => '28.04.2026', 'cas_od' => '09:00', 'cas_do' => '14:00', 'misto' => 'XEVOS HQ, Ostrava', 'kapacita' => 12, 'pocet_registraci' => 7]],
			'lektori' => [['jmeno' => 'Petr Letocha', 'pozice' => 'CSC Manager', 'bio' => '', 'foto' => '']],
			'harmonogram' => [['cas' => '09:00 - Uvod', 'aktivita' => '']],
			'osnova' => [['bod' => 'Odpovednost managementu'], ['bod' => 'Bezpecnostni kultura']],
			'category' => 'Awareness',
		],
		[
			'title' => 'Webinar: Bezpecnost v Microsoft 365', 'typ' => 'online', 'cena' => 826, 'cena_dph' => 999,
			'popis' => '<p>Jak zabezpecit Microsoft 365. Conditional Access, MFA, Defender.</p>',
			'terminy' => [['datum' => '24.04.2026', 'cas_od' => '10:00', 'cas_do' => '12:00', 'misto' => 'Online (MS Teams)', 'kapacita' => 60, 'pocet_registraci' => 22]],
			'lektori' => [['jmeno' => 'Vaclav Herec', 'pozice' => 'Security Specialist', 'bio' => '', 'foto' => '']],
			'harmonogram' => [['cas' => '10:00 - M365 security', 'aktivita' => '']],
			'osnova' => [['bod' => 'Conditional Access'], ['bod' => 'MFA']],
			'category' => 'Cloud Security',
		],
		[
			'title' => 'Event: Kyberneticka bezpecnost pro obce', 'typ' => 'prezencni', 'cena' => 1653, 'cena_dph' => 2000,
			'popis' => '<p>Workshop pro zastupitele obci. Povinnosti samosprav v oblasti kyberbezpecnosti.</p>',
			'terminy' => [['datum' => '08.05.2026', 'cas_od' => '09:00', 'cas_do' => '13:00', 'misto' => 'Mestsky urad Frydek-Mistek', 'kapacita' => 40, 'pocet_registraci' => 25]],
			'lektori' => [['jmeno' => 'Petr Letocha', 'pozice' => 'CSC Manager', 'bio' => '', 'foto' => '']],
			'harmonogram' => [['cas' => '09:00 - Legislativa', 'aktivita' => '']],
			'osnova' => [['bod' => 'ZoKB pro samospravy'], ['bod' => 'Dotacni moznosti']],
			'category' => 'Eventy',
		],
		[
			'title' => 'GDPR audit - jak na to', 'typ' => 'online', 'cena' => 2479, 'cena_dph' => 3000,
			'popis' => '<p>Skoleni o provedeni interniho GDPR auditu. Metodika, checklisty, dokumentace.</p>',
			'terminy' => [['datum' => '12.05.2026', 'cas_od' => '09:00', 'cas_do' => '12:00', 'misto' => 'Online (Zoom)', 'kapacita' => 25, 'pocet_registraci' => 14]],
			'lektori' => [['jmeno' => 'Petr Letocha', 'pozice' => 'CSC Manager', 'bio' => '', 'foto' => '']],
			'harmonogram' => [['cas' => '09:00 - Metodika', 'aktivita' => '']],
			'osnova' => [['bod' => 'Audit metodika'], ['bod' => 'Nejcastejsi chyby']],
			'category' => 'GDPR',
		],
		[
			'title' => 'Penetracni testovani pro pokrocile', 'typ' => 'prezencni', 'cena' => 8264, 'cena_dph' => 10000,
			'popis' => '<p>Dvoudenni intenzivni kurz penetracniho testovani. AD exploitation, pivoting, privilege escalation.</p>',
			'terminy' => [['datum' => '26.05.2026', 'cas_od' => '08:00', 'cas_do' => '17:00', 'misto' => 'XEVOS HQ, Ostrava', 'kapacita' => 10, 'pocet_registraci' => 8]],
			'lektori' => [['jmeno' => 'Vaclav Herec', 'pozice' => 'Security Specialist', 'bio' => '', 'foto' => '']],
			'harmonogram' => [['cas' => '08:00 - Den 1', 'aktivita' => ''], ['cas' => '08:00 - Den 2', 'aktivita' => '']],
			'osnova' => [['bod' => 'AD exploitation'], ['bod' => 'Privilege escalation']],
			'category' => 'Školení',
		],
	];

	// Create školení taxonomy terms.
	$sk_terms = [ 'Eventy', 'Webináře', 'Školení', 'Kybernetická bezpečnost', 'NIS2', 'Cloud Security', 'GDPR', 'Awareness' ];
	foreach ( $sk_terms as $term_name ) {
		if ( ! term_exists( $term_name, 'kategorie-skoleni' ) ) {
			wp_insert_term( $term_name, 'kategorie-skoleni' );
		}
	}

	foreach ( $skoleni_data as $s ) {
		if ( xevos_post_exists_by_title( $s['title'], 'skoleni' ) ) continue;

		$post_id = wp_insert_post( [
			'post_type'   => 'skoleni',
			'post_title'  => $s['title'],
			'post_status' => 'publish',
		] );

		if ( ! $post_id ) continue;

		if ( function_exists( 'update_field' ) ) {
			update_field( 'popis', $s['popis'], $post_id );
			update_field( 'cena', $s['cena'], $post_id );
			update_field( 'cena_s_dph', $s['cena_dph'], $post_id );
			update_field( 'typ', $s['typ'], $post_id );
			update_field( 'terminy', $s['terminy'], $post_id );
			update_field( 'lektori', $s['lektori'], $post_id );
			update_field( 'harmonogram', $s['harmonogram'], $post_id );
			update_field( 'osnova', $s['osnova'], $post_id );
			update_field( 'objednavkovy_formular', true, $post_id );
		}

		if ( $s['category'] ) {
			wp_set_object_terms( $post_id, $s['category'], 'kategorie-skoleni' );
		}
	}

	// =========================================================================
	// 6. Recenze (sample reviews)
	// =========================================================================
	$recenze = [
		[
			'title'  => 'Recenze 1',
			'jmeno'  => 'Jan Novák',
			'pozice' => 'IT Manager, ABC s.r.o.',
			'text'   => 'Nechtěl jsem marketing, chtěl jsem důkaz. Dostali jsme jasný report, re-test a hlavně klid, že víme, kde jsme slabí. Rychlý, věcný, profesionální.',
			'hodnoceni' => 5,
		],
		[
			'title'  => 'Recenze 2',
			'jmeno'  => 'Marie Svobodová',
			'pozice' => 'CEO, XYZ a.s.',
			'text'   => 'Nechtěl jsem marketing, chtěl jsem důkaz. Dostali jsme jasný report, re-test a hlavně klid, že víme, kde jsme slabí. Rychlý, věcný, profesionální.',
			'hodnoceni' => 5,
		],
		[
			'title'  => 'Recenze 3',
			'jmeno'  => 'Tomáš Dvořák',
			'pozice' => 'CISO, DEF Group',
			'text'   => 'Nechtěl jsem marketing, chtěl jsem důkaz. Dostali jsme jasný report, re-test a hlavně klid, že víme, kde jsme slabí. Rychlý, věcný, profesionální.',
			'hodnoceni' => 5,
		],
	];

	foreach ( $recenze as $r ) {
		if ( xevos_post_exists_by_title( $r['title'], 'recenze' ) ) continue;

		$post_id = wp_insert_post( [
			'post_type'   => 'recenze',
			'post_title'  => $r['title'],
			'post_status' => 'publish',
		] );

		if ( $post_id && function_exists( 'update_field' ) ) {
			update_field( 'jmeno', $r['jmeno'], $post_id );
			update_field( 'pozice_firma', $r['pozice'], $post_id );
			update_field( 'text_recenze', $r['text'], $post_id );
			update_field( 'hodnoceni', $r['hodnoceni'], $post_id );
		}
	}

	// =========================================================================
	// 7. Navigation Menus
	// =========================================================================
	$menu_name = 'Hlavní navigace';
	$menu_exists = wp_get_nav_menu_object( $menu_name );
	if ( ! $menu_exists ) {
		$menu_id = wp_create_nav_menu( $menu_name );
		$menu_items = [
			'Služby'      => '/sluzby/',
			'NIS 2'       => '/nis2/',
			'Eventy'      => '/skoleni/',
			'Partnerství' => '/partnerstvi/',
			'O nás'       => '/o-nas/',
			'Blog'        => '/aktuality/',
			'Kontakt'     => '/kontakt/',
		];

		$i = 1;
		foreach ( $menu_items as $label => $url ) {
			wp_update_nav_menu_item( $menu_id, 0, [
				'menu-item-title'  => $label,
				'menu-item-url'    => home_url( $url ),
				'menu-item-status' => 'publish',
				'menu-item-type'   => 'custom',
				'menu-item-position' => $i++,
			] );
		}

		$locations = get_theme_mod( 'nav_menu_locations' );
		$locations['primary'] = $menu_id;
		$locations['footer']  = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	// =========================================================================
	// 8. Permalink structure
	// =========================================================================
	update_option( 'permalink_structure', '/%postname%/' );
	flush_rewrite_rules();
}

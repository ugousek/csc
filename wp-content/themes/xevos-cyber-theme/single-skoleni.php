<?php

/**
 * Single: Školení.
 * Figma node 319-56: Hero + price, pro koho, termíny, lektoři carousel,
 *        harmonogram, osnova, co si odnesete, kde parkovat, recenze, objednávkový formulář.
 *
 * @package Xevos\CyberTheme
 */

get_header();

while (have_posts()) : the_post();

	$popis       = get_field('popis');
	$cena        = get_field('cena');
	$cena_s_dph  = get_field('cena_s_dph');
	$typ         = get_field('typ');
	$lektori     = get_field('lektori');
	$harmonogram = get_field('harmonogram');
	$osnova      = get_field('osnova');
	$terminy     = get_field('terminy');
	$formular    = get_field('objednavkovy_formular');
	$telefon = xevos_get_option('telefon');
	$email   = xevos_get_option('email');
	$adresa  = xevos_get_option('adresa', 'Mostárenská 1156/38, 703 00 Ostrava');
?>

	<main id="main" class="xevos-main xevos-main--glows xevos-skoleni-detail">
		<div class="xevos-skoleni-bg-bottom" aria-hidden="true"></div>
		<div class="xevos-glow-blob xevos-glow-blob--left xevos-glow-blob--lg" style="top:-400px;"></div>
		<div class="xevos-glow-blob xevos-glow-blob--top xevos-glow-blob--lg"></div>
		<div class="xevos-glow-blob xevos-glow-blob--right xevos-glow-blob--lg" style="top:800px;"></div>
		<div class="xevos-glow-blob xevos-glow-blob--left-second xevos-glow-blob--lg" style="bottom:0;"></div>

		<!-- 1. Hero -->
		<?php
		$hero_acf = get_field('hero_obrazek');
		if ($hero_acf && !empty($hero_acf['url'])) {
			$hero_img = $hero_acf['url'];
		} elseif (has_post_thumbnail()) {
			$hero_img = get_the_post_thumbnail_url(get_the_ID(), 'xevos-hero');
		} else {
			$hero_img = get_theme_file_uri('assets/img/detail-skoleni/hero-shield.png');
		}

		$typ_prihlaseni = get_field('typ_prihlaseni') ?: 'platba';
		$is_free = in_array($typ_prihlaseni, ['zdarma', 'pozvanka'], true);

		if ($typ_prihlaseni === 'pozvanka') {
			$cta_label = 'Požádat o pozvánku';
		} elseif ($typ_prihlaseni === 'zdarma') {
			$cta_label = 'Registrovat se zdarma';
		} elseif ($cena_s_dph) {
			$cta_label = 'Cena: ' . number_format((float) $cena_s_dph, 0, ',', ' ') . ' Kč';
		} else {
			$cta_label = 'Objednat';
		}

		$hero_nadpis = get_field('hero_nadpis') ?: get_the_title();
		$hero_popis  = get_field('hero_popis');

		get_template_part('template-parts/components/hero-page', null, [
			'heading'     => $hero_nadpis,
			'description' => $hero_popis ?: '',
			'cta_text'    => $cta_label,
			'cta_url'     => '#objednavka',
			'image_url'   => $hero_img,
			'css_class'   => 'xevos-skoleni-hero',
			'loading'     => 'eager',
		]);
		?>

		<!-- 2. Pro koho -->
		<?php
		$pro_koho_nadpis = get_field('pro_koho_nadpis') ?: 'Pro koho je školení určeno';
		$pro_koho_text   = get_field('pro_koho_text');
		if ($pro_koho_text) : ?>
			<section class="xevos-section xevos-skoleni-pro-koho">
				<div class="xevos-section__container">
					<h2><?php echo esc_html($pro_koho_nadpis); ?></h2>
					<div class="xevos-article-content__body"><?php echo wp_kses_post($pro_koho_text); ?></div>
				</div>
			</section>
		<?php endif; ?>

		<!-- 3. Termíny -->
		<?php if ($terminy) : ?>
			<section class="xevos-section xevos-skoleni-terminy">
				<div class="xevos-section__container">
					<h2>Termíny školení</h2>
					<div class="xevos-termin-cards">
						<?php foreach ($terminy as $i => $t) :
							$dost = xevos_get_termin_dostupnost(get_the_ID(), $i);
						?>
							<div class="xevos-termin-card<?php echo $dost['plne'] ? ' xevos-termin-card--full' : ''; ?>">
								<div class="xevos-termin-card__date"><?php echo esc_html($t['datum'] ?? ''); ?></div>
								<div class="xevos-termin-card__location"><?php echo esc_html($t['misto'] ?? ''); ?></div>
								<div class="xevos-termin-card__time"><?php echo esc_html(($t['cas_od'] ?? '') . ' – ' . ($t['cas_do'] ?? '')); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<!-- 4. Lektoři -->
		<?php
		$has_lektori = false;
		if (is_array($lektori)) {
			foreach ($lektori as $l) {
				if (! empty($l['jmeno'])) {
					$has_lektori = true;
					break;
				}
			}
		}
		if (! $has_lektori) {
			$lektori = [
				['jmeno' => 'Petr Letocha', 'pozice' => 'Cyber Security Center Manager', 'bio' => 'Petr je odborník na kybernetickou bezpečnost se specializací na implementaci legislativních požadavků. Zaměřuje se na řízení bezpečnostních rizik, nastavování bezpečnostních procesů a zajištění souladu organizací s regulatorními normami.', 'foto' => ''],
				['jmeno' => 'Václav Herec', 'pozice' => 'Cyber Security Specialist', 'bio' => 'Václav je odborný specialista na penetrační testování a odhalování bezpečnostních slabých míst v IT systémech. Spolupracuje na projektech zaměřených na zvyšování bezpečnosti a dodržování certifikačních požadavků.', 'foto' => ''],
				['jmeno' => 'Václav Herec', 'pozice' => 'Cyber Security Specialist', 'bio' => 'Václav je odborný specialista na penetrační testování a odhalování bezpečnostních slabých míst v IT systémech. Spolupracuje na projektech zaměřených na zvyšování bezpečnosti a dodržování certifikačních požadavků.', 'foto' => ''],
				['jmeno' => 'Václav Herec', 'pozice' => 'Cyber Security Specialist', 'bio' => 'Václav je odborný specialista na penetrační testování a odhalování bezpečnostních slabých míst v IT systémech. Spolupracuje na projektech zaměřených na zvyšování bezpečnosti a dodržování certifikačních požadavků.', 'foto' => ''],
			];
		}

		$lektor_count = is_array($lektori) ? count($lektori) : 0;
		$use_swiper   = $lektor_count >= 3;
		$lektor_fallbacks = [
			get_theme_file_uri('assets/img/detail-skoleni/petr.png'),
			get_theme_file_uri('assets/img/detail-skoleni/vaclav.png'),
		];
		?>
		<section class="xevos-section xevos-skoleni-lektori-section">
			<div class="xevos-section__container">
				<h2><?php echo esc_html(get_field('lektori_nadpis') ?: 'Obsahem vás provede:'); ?></h2>
				<div class="xevos-skoleni-lektori__carousel">
					<?php if ($use_swiper) : ?>
						<button class="xevos-nav-arrow xevos-nav-arrow--prev" aria-label="Předchozí" type="button">
							<svg width="12" height="20" viewBox="0 0 12 20" fill="none">
								<path d="M10 2L2 10l8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</button>
					<?php endif; ?>

					<div class="<?php echo $use_swiper ? 'swiper xevos-lektori-swiper' : 'xevos-skoleni-lektori__cards'; ?>" <?php echo $use_swiper ? 'id="lektori-swiper"' : ''; ?>>
						<div class="<?php echo $use_swiper ? 'swiper-wrapper' : 'xevos-skoleni-lektori__static'; ?>">
							<?php foreach ($lektori as $li => $l) :
								$foto_url = '';
								if (! empty($l['foto']) && is_array($l['foto'])) {
									$foto_url = $l['foto']['sizes']['xevos-thumbnail'] ?? $l['foto']['url'] ?? '';
								}
								if (! $foto_url) {
									$foto_url = $lektor_fallbacks[$li % count($lektor_fallbacks)];
								}
							?>
								<div class="<?php echo $use_swiper ? 'swiper-slide' : ''; ?> xevos-lektor-card">
									<div class="xevos-lektor-card__foto-wrap">
										<img src="<?php echo esc_url($foto_url); ?>" alt="<?php echo esc_attr($l['jmeno'] ?? ''); ?>" class="xevos-lektor-card__foto">
									</div>
									<div class="xevos-lektor-card__info">
										<div class="xevos-lektor-card__name-wrap">
											<div class="xevos-lektor-card__name"><?php echo esc_html($l['jmeno'] ?? ''); ?></div>
											<?php if (! empty($l['pozice'])) : ?>
												<div class="xevos-lektor-card__role"><?php echo esc_html($l['pozice']); ?></div>
											<?php endif; ?>
										</div>
										<?php if (! empty($l['bio'])) : ?>
											<p class="xevos-lektor-card__bio"><?php echo esc_html($l['bio']); ?></p>
										<?php endif; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>

					<?php if ($use_swiper) : ?>
						<button class="xevos-nav-arrow xevos-nav-arrow--next" aria-label="Další" type="button">
							<svg width="12" height="20" viewBox="0 0 12 20" fill="none">
								<path d="M2 2l8 8-8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</button>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<!-- 5. Harmonogram + Osnova -->
		<?php
		$has_harmonogram = false;
		if (is_array($harmonogram)) {
			foreach ($harmonogram as $h) {
				if (! empty($h['cas'])) {
					$has_harmonogram = true;
					break;
				}
			}
		}
		if (! $has_harmonogram) {
			$harmonogram = [
				['cas' => '08:00', 'aktivita' => 'Registrace a snídaně'],
				['cas' => '10:30–10:45', 'aktivita' => 'Coffee break'],
				['cas' => '10:45–12:00', 'aktivita' => '2. blok (1,25 h)'],
				['cas' => '12:00–13:00', 'aktivita' => 'Oběd'],
				['cas' => '13:00–15:00', 'aktivita' => '3. blok (2 h)'],
				['cas' => '15:45–16:00', 'aktivita' => 'Diskuse / Závěr'],
			];
		}
		$has_osnova = false;
		if (is_array($osnova)) {
			foreach ($osnova as $o) {
				if (! empty($o['bod'])) {
					$has_osnova = true;
					break;
				}
			}
		}
		if (! $has_osnova) {
			$osnova = [
				['bod' => 'Legislativní minimum: požadavky ZoKB, NIS2 a povinnosti zaměstnanců'],
				['bod' => 'Hlášení kybernetických incidentů: jak poznat incident a komu ho předat'],
				['bod' => 'Nejčastější hrozby: phishing, sociální inženýrství, ransomware, malware'],
				['bod' => 'Bezpečné chování: heslová politika, práce s daty, bezpečný internet, mobilní zařízení'],
				['bod' => 'Fyzická bezpečnost, home office, vzdálený přístup a zásady práce na služebních zařízeních'],
				['bod' => 'Bezpečnost dodavatelského řetězce a externích služeb'],
			];
		}
		?>
		<section class="xevos-section">
			<div class="xevos-section__container">
				<div class="xevos-skoleni-content-cols">
					<?php if ($harmonogram) : ?>
						<div class="xevos-skoleni-content-cols__col">
							<h2><?php echo esc_html(get_field('harmonogram_nadpis') ?: 'Harmonogram školení'); ?></h2>
							<div class="xevos-harmonogram-list">
								<?php foreach ($harmonogram as $h) : ?>
									<div class="xevos-harmonogram-item">
										<span class="xevos-harmonogram-item__cas"><?php echo esc_html($h['cas'] ?? ''); ?></span><?php if (! empty($h['aktivita'])) : ?><span class="xevos-harmonogram-item__text">&nbsp;–&nbsp;<?php echo esc_html($h['aktivita']); ?></span><?php endif; ?>
									</div>
								<?php endforeach; ?>
							</div>

							<?php
							$co_odnesete = get_field('co_odnesete');
							if (! $co_odnesete) {
								$co_odnesete = [
									['bod' => 'Schopnost rozpoznat kybernetické hrozby a správně na ně reagovat'],
									['bod' => 'Praktické dovednosti pro bezpečnou práci s daty, hesly a firemními systémy'],
									['bod' => 'Znalost povinností vyplývajících ze ZoKB/NIS2 a role zaměstnance v ochraně organizace'],
									['bod' => 'Zvýšené bezpečnostní povědomí, které posiluje kulturu bezpečnosti celé firmy'],
								];
							}
							?>
							<div class="xevos-skoleni-benefits">
								<h2><?php echo esc_html(get_field('co_odnesete_nadpis') ?: 'Co si odnesete'); ?></h2>
								<?php xevos_component('checklist', ['items' => $co_odnesete]); ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if ($osnova) : ?>
						<div class="xevos-skoleni-content-cols__col">
							<h2><?php echo esc_html(get_field('osnova_nadpis') ?: 'Osnova školení'); ?></h2>
							<ul class="xevos-osnova-list">
								<?php foreach ($osnova as $o) : ?>
									<li><?php echo wp_kses(strip_tags($o['bod'] ?? '', '<strong><b>'), ['strong' => [], 'b' => []]); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<!-- 6. Kde parkovat -->
		<?php
		$kde_parkovat  = get_field('kde_parkovat');
		$kde_park_img  = get_field('kde_parkovat_obrazek');
		if (! $kde_parkovat) {
			$kde_parkovat = 'Parkování máte přímo u nás ve dvoře na adrese firmy. Po příjezdu stačí vjet do dvora a zaparkovat na vyhrazených místech.';
		}
		$kde_park_img_url = $kde_park_img
			? $kde_park_img['url']
			: get_theme_file_uri('assets/img/detail-skoleni/kde-parkovat.png');
		?>
		<section class="xevos-section xevos-skoleni-kde-parkovat">
			<div class="xevos-section__container">
				<div class="xevos-skoleni-parking">
					<div class="xevos-skoleni-parking__image">
						<img src="<?php echo esc_url($kde_park_img_url); ?>" alt="Kde parkovat" loading="lazy">
					</div>
					<div class="xevos-skoleni-parking__text">
						<h2>Kde parkovat</h2>
						<?php echo wp_kses_post($kde_parkovat); ?>
					</div>
				</div>
			</div>
		</section>

		<!-- 7. Recenze -->
		<?php get_template_part('template-parts/components/recenze'); ?>

		<!-- 8. Objednávkový formulář -->
		<?php
		if ($formular !== '0') :
			$form_action = $is_free ? 'xevos_ecomail_register' : '';

			if ($typ_prihlaseni === 'pozvanka') {
				$form_heading = 'Požádat o pozvánku';
				$form_desc    = 'Kapacita akce je omezená. Vyplňte formulář a my vám dáme vědět, zda pro vás máme místo.';
				$submit_label = 'Odeslat žádost o pozvánku';
			} elseif ($typ_prihlaseni === 'zdarma') {
				$form_heading = 'Registrace zdarma';
				$form_desc    = 'Spolupracujeme s firmami, které chtějí mít v kybernetické bezpečnosti jasno. Zpětná vazba od klientů je pro nás potvrzením práce v reálném provozu.';
				$submit_label = 'Registrovat se zdarma';
			} else {
				$form_heading = 'Objednat školení';
				$form_desc    = 'Spolupracujeme s firmami, které chtějí mít v kybernetické bezpečnosti jasno. Zpětná vazba od klientů je pro nás potvrzením práce v reálném provozu.';
				$submit_label = 'Závazně objednat';
			}
		?>
			<section class="xevos-section" id="objednavka">
				<div class="xevos-section__container">
					<div class="xevos-skoleni-section-header">
						<h2><?php echo esc_html($form_heading); ?></h2>
						<p><?php echo esc_html($form_desc); ?></p>
					</div>

					<form class="xevos-order-section" method="post" id="xevos-order-form" data-free="<?php echo $is_free ? '1' : '0'; ?>" data-typ="<?php echo esc_attr($typ_prihlaseni); ?>">
						<input type="hidden" name="action" value="<?php echo esc_attr($form_action); ?>">
						<input type="hidden" name="skoleni_id" value="<?php echo esc_attr(get_the_ID()); ?>">
						<input type="hidden" name="skoleni_nazev" value="<?php echo esc_attr(get_the_title()); ?>">
						<input type="hidden" name="skoleni_cena" value="<?php echo esc_attr($is_free ? 0 : $cena_s_dph); ?>">
						<input type="hidden" name="typ_prihlaseni" value="<?php echo esc_attr($typ_prihlaseni); ?>">
						<?php wp_nonce_field('xevos_order', 'xevos_order_nonce'); ?>

						<div class="xevos-order-form">
							<div class="xevos-form-row">
								<div class="xevos-form__group">
									<label class="xevos-form__label">Jméno <span class="xevos-form__required">*</span></label>
									<input type="text" class="xevos-form__input" name="jmeno" required>
								</div>
								<div class="xevos-form__group">
									<label class="xevos-form__label">Příjmení <span class="xevos-form__required">*</span></label>
									<input type="text" class="xevos-form__input" name="prijmeni" required>
								</div>
							</div>
							<div class="xevos-form-row">
								<div class="xevos-form__group">
									<label class="xevos-form__label">Telefon <span class="xevos-form__required">*</span></label>
									<input type="tel" class="xevos-form__input" name="telefon" required>
								</div>
								<div class="xevos-form__group">
									<label class="xevos-form__label">E-mail <span class="xevos-form__required">*</span></label>
									<input type="email" class="xevos-form__input" name="email" required>
								</div>
							</div>
							<div class="xevos-form-row">
								<div class="xevos-form__group">
									<label class="xevos-form__label">Školení <span class="xevos-form__required">*</span></label>
									<select class="xevos-form__input" name="skoleni">
										<option><?php the_title(); ?></option>
									</select>
								</div>
								<div class="xevos-form__group">
									<label class="xevos-form__label">Termín školení <span class="xevos-form__required">*</span></label>
									<select class="xevos-form__input" name="termin">
										<?php if ($terminy) : foreach ($terminy as $t) : ?>
												<option value="<?php echo esc_attr($t['datum'] ?? ''); ?>"><?php echo esc_html($t['datum'] ?? ''); ?></option>
										<?php endforeach;
										endif; ?>
									</select>
								</div>
							</div>
							<div class="xevos-form-row">
								<?php if ($typ === 'hybrid') : ?>
									<div class="xevos-form__group">
										<label class="xevos-form__label">Forma účasti <span class="xevos-form__required">*</span></label>
										<select class="xevos-form__input" name="forma" required>
											<option value="">– vyberte formu –</option>
											<option value="prezencni">Prezenčně (na místě)</option>
											<option value="online">Online (stream)</option>
										</select>
									</div>
								<?php endif; ?>
								<div class="xevos-form__group">
									<label class="xevos-form__label">Počet účastníků <span class="xevos-form__required">*</span></label>
									<input type="number" class="xevos-form__input" name="pocet" min="1" value="1">
								</div>
								<?php if ($typ !== 'hybrid') : ?>
									<div class="xevos-form__group">
										<label class="xevos-form__label">Název firmy <span class="xevos-form__required">*</span></label>
										<input type="text" class="xevos-form__input" name="firma">
									</div>
								<?php endif; ?>
							</div>
							<?php if ($typ === 'hybrid') : ?>
								<div class="xevos-form-row">
									<div class="xevos-form__group">
										<label class="xevos-form__label">Název firmy <span class="xevos-form__required">*</span></label>
										<input type="text" class="xevos-form__input" name="firma">
									</div>
								</div>
							<?php endif; ?>
							<?php if (! $is_free) : ?>
								<div class="xevos-form-row">
									<div class="xevos-form__group">
										<label class="xevos-form__label">IČ <span class="xevos-form__required">*</span></label>
										<input type="text" class="xevos-form__input" name="ico">
									</div>
									<div class="xevos-form__group">
										<label class="xevos-form__label">DIČ</label>
										<input type="text" class="xevos-form__input" name="dic">
									</div>
								</div>
								<div class="xevos-form-row">
									<div class="xevos-form__group">
										<label class="xevos-form__label">Ulice <span class="xevos-form__required">*</span></label>
										<input type="text" class="xevos-form__input" name="ulice">
									</div>
									<div class="xevos-form__group">
										<label class="xevos-form__label">Město <span class="xevos-form__required">*</span></label>
										<input type="text" class="xevos-form__input" name="mesto">
									</div>
								</div>
								<div class="xevos-form-row">
									<div class="xevos-form__group">
										<label class="xevos-form__label">PSČ <span class="xevos-form__required">*</span></label>
										<input type="text" class="xevos-form__input" name="psc">
									</div>
									<div class="xevos-form__group">
										<div class="xevos-form__checkbox-wrap">
											<input type="checkbox" class="xevos-form__checkbox" name="platce_dph" id="platce_dph">
											<label class="xevos-form__checkbox-label" for="platce_dph">Jsem plátce DPH</label>
										</div>
									</div>
								</div>
							<?php endif; ?>
							<div class="xevos-form__hp"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
						</div>

						<div class="xevos-order-summary">
							<?php xevos_component('contact-info'); ?>

							<div class="xevos-order-summary__price-block">
								<?php if ($is_free) : ?>
									<div class="xevos-order-summary__price-main">Zdarma</div>
								<?php else : ?>
									<div class="xevos-order-summary__price-label">Celková cena:</div>
									<div class="xevos-order-summary__price-main">
										<?php echo esc_html($cena_s_dph ? number_format((float) $cena_s_dph, 0, ',', ' ') . ' Kč' : '—'); ?>
										<small>s DPH</small>
									</div>
									<?php if ($cena) : ?>
										<div class="xevos-order-summary__price-secondary">
											<?php echo esc_html(number_format((float) $cena, 0, ',', ' ')); ?> Kč bez DPH
										</div>
									<?php endif; ?>
								<?php endif; ?>
							</div>

							<button type="submit" class="xevos-btn xevos-btn--primary" id="xevos-order-submit">
								<span class="xevos-btn__arrow">
									<svg width="39" height="39" viewBox="0 0 20 20" fill="none">
										<path d="M5 15L15 5M15 5H7M15 5v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
									</svg>
								</span>
								<?php echo esc_html(strtoupper($submit_label)); ?>
							</button>
							<div class="xevos-order-message" id="xevos-order-message" style="display:none;"></div>
						</div>
					</form>
				</div>
			</section>
		<?php endif; ?>

	<?php endwhile;
get_footer(); ?>
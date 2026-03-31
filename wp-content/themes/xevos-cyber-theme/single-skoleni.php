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

	<main id="main" class="xevos-main">

		<!-- Hero – shared component with price CTA -->
		<?php
		$hero_img = has_post_thumbnail()
			? get_the_post_thumbnail_url(get_the_ID(), 'xevos-hero')
			: get_theme_file_uri('assets/img/detail-skoleni/hero-shield.png');

		$cta_label = $cena_s_dph
			? 'Cena: ' . number_format((float) $cena_s_dph, 0, ',', ' ') . ' Kč'
			: 'Objednat';

		get_template_part('template-parts/components/hero-page', null, [
			'heading'     => get_the_title(),
			'description' => $popis ? wp_trim_words(wp_strip_all_tags($popis), 30) : '',
			'cta_text'    => $cta_label,
			'cta_url'     => '#objednavka',
			'image_url'   => $hero_img,
			'css_class'   => 'xevos-skoleni-hero',
			'loading'     => 'eager',
		]);
		?>

		<!-- ============================================================
	     Pro koho je školení určeno
	     Figma: H3 Montserrat/900/32px + Body Sora/400/24px
	     ============================================================ -->
		<div class="xevos-glow-wrap">
			<div class="xevos-glow-blob xevos-glow-blob--left xevos-glow-blob--lg"></div>
			<?php if ($popis) : ?>
				<section class="xevos-section xevos-skoleni-pro-koho">
					<div class="xevos-section__container">
						<h2>Pro koho je školení určeno</h2>
						<div class="xevos-article-content__body"><?php echo wp_kses_post($popis); ?></div>
					</div>
				</section>
			<?php endif; ?>

			<!-- ============================================================
	     Termíny školení
	     Figma: 3-col grid, cyan cards 480x191, date Montserrat/700/52px
	     ============================================================ -->
			<?php if ($terminy) : ?>
				<section class="xevos-section xevos-skoleni-terminy xevos-glow">
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

			<div class="xevos-glow first"></div>

			<!-- ============================================================
	     Lektoři – carousel
	     Figma: 240x240 photo circles, cyan border, arrows left/right
	     ============================================================ -->
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
				];
			}
			?>
			<section class="xevos-section xevos-skoleni-lektori-section">
				<div class="xevos-section__container">
					<h2>Obsahem vás provede:</h2>
					<div class="xevos-skoleni-lektori__carousel">
						<button class="xevos-lektor-nav xevos-lektor-nav--prev" aria-label="Předchozí" type="button">
							<svg width="12" height="20" viewBox="0 0 12 20" fill="none">
								<path d="M10 2L2 10l8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</button>
						<div class="xevos-skoleni-lektori__cards">
							<?php
							$lektor_fallbacks = [
								get_theme_file_uri('assets/img/detail-skoleni/petr.png'),
								get_theme_file_uri('assets/img/detail-skoleni/vaclav.png'),
							];
							foreach ($lektori as $li => $l) :
								$foto_url = '';
								if (! empty($l['foto']) && is_array($l['foto'])) {
									$foto_url = $l['foto']['sizes']['xevos-thumbnail'] ?? $l['foto']['url'] ?? '';
								}
								if (! $foto_url) {
									$foto_url = $lektor_fallbacks[$li % count($lektor_fallbacks)];
								}
							?>
								<div class="xevos-lektor-card">
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
						<button class="xevos-lektor-nav xevos-lektor-nav--next" aria-label="Další" type="button">
							<svg width="12" height="20" viewBox="0 0 12 20" fill="none">
								<path d="M2 2l8 8-8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</button>
					</div>
				</div>
			</section>

		</div>

		<!-- ============================================================
	     Harmonogram + Osnova – two-column layout
	     Figma: H3 Montserrat/900/52px, two equal columns, gap 80px
	     ============================================================ -->
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
				['cas' => '08:00 – Registrace a snídaně', 'aktivita' => ''],
				['cas' => '10:30–10:45 – Coffee break', 'aktivita' => ''],
				['cas' => '10:45–12:00 – 2. blok (1,25 h)', 'aktivita' => ''],
				['cas' => '12:00–13:00 – Oběd', 'aktivita' => ''],
				['cas' => '13:00–15:00 – 3. blok (2 h)', 'aktivita' => ''],
				['cas' => '15:45–16:00 – Diskuse / Závěr', 'aktivita' => ''],
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
							<h2>Harmonogram školení</h2>
							<div class="xevos-harmonogram-list">
								<?php foreach ($harmonogram as $h) : ?>
									<div class="xevos-harmonogram-item">
										<span class="xevos-harmonogram-item__text"><?php echo esc_html(($h['cas'] ?? '') . ' – ' . ($h['aktivita'] ?? '')); ?></span>
									</div>
								<?php endforeach; ?>
							</div>

							<?php
							/* Co si odnesete – below Harmonogram in left column per Figma */
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
							<div class="xevos-skoleni-benefits" style="margin-top:5rem;">
								<h2>Co si odnesete?</h2>
								<?php xevos_component('checklist', ['items' => $co_odnesete]); ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if ($osnova) : ?>
						<div class="xevos-skoleni-content-cols__col">
							<h2>Osnova školení</h2>
							<ul class="xevos-osnova-list">
								<?php foreach ($osnova as $o) : ?>
									<li><?php echo esc_html($o['bod'] ?? ''); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<!-- ============================================================
	     Kde parkovat – image left + text right
	     Figma: image (799x542) with border, text block right
	     ============================================================ -->
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
		<section class="xevos-section">
			<div class="xevos-section__container">
				<div class="xevos-skoleni-parking">
					<div class="xevos-skoleni-parking__image">
						<img src="<?php echo esc_url($kde_park_img_url); ?>" alt="Kde parkovat" loading="lazy">
					</div>
					<div class="xevos-skoleni-parking__text">
						<h2>Kde parkovat</h2>
						<p><?php echo esc_html($kde_parkovat); ?></p>
					</div>
				</div>
			</div>
		</section>

		<!-- ============================================================
	     Recenze – shared component
	     ============================================================ -->
		<?php get_template_part('template-parts/homepage/recenze'); ?>

		<!-- ============================================================
	     Objednávkový formulář
	     Figma: form left (7 rows x 2 inputs) + sidebar right (contact + price + submit)
	     ============================================================ -->
		<?php if ($formular !== '0') : ?>
			<section class="xevos-section" id="objednavka">
				<div class="xevos-section__container">
					<div class="xevos-skoleni-section-header">
						<h2>Objednat školení</h2>
						<p>Spolupracujeme s firmami, které chtějí mít v kybernetické bezpečnosti jasno. Zpětná vazba od klientů je pro nás potvrzením práce v reálném provozu.</p>
					</div>

					<form class="xevos-order-section" method="post" action="">
						<input type="hidden" name="skoleni_id" value="<?php echo esc_attr(get_the_ID()); ?>">
						<input type="hidden" name="skoleni_nazev" value="<?php echo esc_attr(get_the_title()); ?>">
						<input type="hidden" name="skoleni_cena" value="<?php echo esc_attr($cena_s_dph); ?>">
						<?php wp_nonce_field('xevos_order', 'xevos_order_nonce'); ?>

						<!-- Form fields -->
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
								<div class="xevos-form__group">
									<label class="xevos-form__label">Počet účastníků <span class="xevos-form__required">*</span></label>
									<input type="number" class="xevos-form__input" name="pocet" min="1" value="1">
								</div>
								<div class="xevos-form__group">
									<label class="xevos-form__label">Název firmy <span class="xevos-form__required">*</span></label>
									<input type="text" class="xevos-form__input" name="firma">
								</div>
							</div>
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
							<div class="xevos-form__hp"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
						</div>

						<!-- Sidebar: Contact + Price + Submit -->
						<div class="xevos-order-summary">
							<?php xevos_component('contact-info'); ?>

							<div class="xevos-order-summary__price-block">
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
							</div>

							<button type="submit" class="xevos-btn xevos-btn--primary">
								<span class="xevos-btn__arrow">
									<svg width="39" height="39" viewBox="0 0 20 20" fill="none">
										<path d="M5 15L15 5M15 5H7M15 5v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
									</svg>
								</span>
								Závazně objednat
							</button>
						</div>
					</form>
				</div>
			</section>
		<?php endif; ?>

	</main>

<?php endwhile;
get_footer(); ?>
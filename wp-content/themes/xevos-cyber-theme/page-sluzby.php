<?php

/**
 * Template Name: Služby
 *
 * Sections (matching ACF tab order):
 * 1. Hero
 * 2. Kyber test slider
 * 3. Postup testování
 * 4. Co získáte / Pro koho
 * 5. Penetrační testy
 * 6. Kyber politika
 * 7. CTA box
 * 8. Aktuality, Recenze, Formulář
 *
 * @package Xevos\CyberTheme
 */

get_header();
$telefon = xevos_get_option('telefon');
$email   = xevos_get_option('email');
$adresa  = xevos_get_option('adresa', 'Mostárenská 1156/38, 703 00 Ostrava');
?>

<main id="main" class="xevos-main xevos-main--glows">

	<!-- Glow blobs – absolute positioned, z-index:0, sections are z-index:1 -->
	<div class="xevos-glow-blob xevos-glow-blob--left xevos-glow-blob--lg" style="top:-400px;"></div>
	<div class="xevos-glow-blob xevos-glow-blob--top xevos-glow-blob--lg"></div>
	<div class="xevos-glow-blob xevos-glow-blob--right xevos-glow-blob--lg" style="top:600px;"></div>
	<div class="xevos-glow-blob xevos-glow-blob--left-second xevos-glow-blob--lg" style="top:2200px;"></div>
	<div class="xevos-glow-blob xevos-glow-blob--right-second xevos-glow-blob--lg" style="top:3800px;"></div>

	<!-- 1. Hero -->
	<?php
	$_show = get_field('sl_hero_zobrazit');
	if ($_show !== '0' && $_show !== 0) :
		$_bg = get_field('sl_hero_background');
		get_template_part('template-parts/components/hero-page', null, [
			'heading'     => get_field('sl_hero_heading') ?: 'Kybernetické testování',
			'description' => get_field('sl_hero_subheading') ?: 'Kybernetické testování je soubor praktických technik, které simulují reálné útoky s cílem odhalit zranitelnosti dříve, než je objeví skutečný útočník a to s využitím nejmodernějšího softwaru, pokročilé automatizace a umělé inteligence.',
			'image_url'   => $_bg ? $_bg['url'] : get_theme_file_uri('assets/img/kyber-testovani-hero.png'),
		]);
	endif;
	?>

	<!-- 2. Kyber test slider -->
	<?php if (get_field('sl_slider_zobrazit') !== false) : ?>
		<?php get_template_part('template-parts/components/kyber-testovani'); ?>
	<?php endif; ?>

		<!-- 3. Postup testování -->
		<?php
		$postup_heading = get_field('sl_postup_heading') ?: 'Jak testování probíhá?';
		$postup_text    = get_field('sl_postup_text') ?: 'Testování probíhá v jasně definovaných krocích – od domluvy rozsahu až po finální zprávu a ověření oprav. Díky tomu <strong>máte průběžný přehled</strong> o tom, co se děje a jaké budou výstupy.';
		$kroky = get_field('sl_postup_kroky');
		if (! $kroky) {
			$kroky = [
				['nazev' => 'Analýza a plán testu', 'popis' => 'Společně definujeme rozsah a cíle testování, identifikujeme klíčové systémy a nastavíme pravidla spolupráce.'],
				['nazev' => 'Aktivní testování a simulace útoků', 'popis' => 'Provádíme řízené simulace reálných útoků na vaše systémy s využitím pokročilých nástrojů a metodik.'],
				['nazev' => 'Vyhodnocení zranitelností', 'popis' => 'Kombinace manuální práce s algoritmy umělé inteligence pro identifikaci i skrytých rizik.'],
				['nazev' => 'Příprava zprávy a doporučení', 'popis' => 'Připravíme srozumitelný report s prioritizací rizik a konkrétními kroky k nápravě.'],
				['nazev' => 'Ověření nápravy (retest)', 'popis' => 'Prověříme, že opravy byly provedeny správně a pokrývají všechny nalezené zranitelnosti.'],
			];
		}
		?>
		<?php if (get_field('sl_postup_zobrazit') !== false) : ?>
			<section class="xevos-section xevos-postup-section">
				<div class="xevos-section__container">
					<div class="xevos-hp-recenze__header">
						<h2><?php echo esc_html($postup_heading); ?></h2>
						<p><?php echo wp_kses_post($postup_text); ?></p>
					</div>
					<!-- Numbered circles row -->
					<div class="xevos-postup-circles">
						<?php foreach ($kroky as $i => $k) : ?>
							<?php if ($i > 0) : ?><span class="xevos-postup-circles__line"></span><?php endif; ?>
							<span class="xevos-postup-step__num"><?php echo esc_html($i + 1); ?>.</span>
						<?php endforeach; ?>
					</div>

					<!-- Descriptions grid (3 left + 2 right) -->
					<div class="xevos-postup-descs">
						<?php foreach ($kroky as $i => $k) : ?>
							<div class="xevos-postup-desc">
								<h4 class="xevos-postup-step__title"><?php echo esc_html(($i + 1) . '. ' . ($k['nazev'] ?? '')); ?></h4>
								<p class="xevos-postup-step__text"><?php echo wp_kses_post($k['popis'] ?? ''); ?></p>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<!-- 4. Co získáte / Pro koho -->
		<?php
		$ziskate_heading = get_field('sl_ziskate_heading') ?: 'Co získáte?';
		$ziskate_text    = get_field('sl_ziskate_text') ?: 'Kybernetická politika vytváří <strong>jasný rámec pro řízení bezpečnosti v organizaci.</strong> Určuje <strong>role, odpovědnosti a pravidla,</strong> podle kterých se chrání data, systémy a provoz.';
		$ziskate_seznam  = get_field('sl_ziskate_seznam');
		$prokoho_heading = get_field('sl_prokoho_heading') ?: 'Pro koho je služba vhodná?';
		$prokoho_text    = get_field('sl_prokoho_text') ?: 'Kybernetická politika vytváří <strong>jasný rámec pro řízení bezpečnosti v organizaci.</strong> Určuje <strong>role, odpovědnosti a pravidla,</strong> podle kterých se chrání data, systémy a provoz.';
		$prokoho_seznam  = get_field('sl_prokoho_seznam');
		$benefity_cta_text = get_field('sl_benefity_cta_text') ?: 'CHCI TEST';
		$benefity_cta_url  = get_field('sl_benefity_cta_url') ?: '#formular';

		if (! $ziskate_seznam) {
			$ziskate_seznam = [
				['bod' => 'Kompletní přehled rizik ohrožujících vaši organizaci'],
				['bod' => 'Výstupy pro audity, certifikace a NIS2'],
				['bod' => 'Odborné doporučení s praktickými kroky k nápravě'],
				['bod' => 'Testování podpořené moderním softwarem a umělou inteligencí'],
				['bod' => 'Vyšší bezpečnost a celkovou kybernetickou odolnost'],
			];
		}
		if (! $prokoho_seznam) {
			$prokoho_seznam = [
				['bod' => 'Firmy a organizace, které chtějí posílit bezpečnost'],
				['bod' => 'Subjekty spadající pod NIS2, DORA, chtějí plnit podmínky certifikace ISO'],
				['bod' => 'IT a vývojové týmy'],
				['bod' => 'Instituce spravující citlivá nebo důvěrná data'],
			];
		}
		?>
		<?php if (get_field('sl_benefity_zobrazit') !== false) : ?>
			<section class="xevos-section xevos-benefity-section">
				<div class="xevos-section__container">
					<div class="xevos-two-col">
						<div>
							<h3><?php echo esc_html($ziskate_heading); ?></h3>
							<div class="xevos-benefity-section__desc"><?php echo wp_kses_post($ziskate_text); ?></div>
							<ul class="xevos-kyber-politika__checklist">
								<?php foreach ($ziskate_seznam as $item) : ?>
									<li>
										<span class="xevos-kyber-test__check-circle" aria-hidden="true">
											<svg xmlns="http://www.w3.org/2000/svg" width="23" height="17" viewBox="0 0 23 17" fill="none">
												<g filter="url(#filter_ziskate_<?php echo esc_attr(sanitize_title($item['bod'] ?? '')); ?>)">
													<path d="M22.6 2.8L8.5 16.9L0 8.4L2.8 5.6L8.5 11.3L19.8 0L22.6 2.8Z" fill="#00BBFF" />
												</g>
												<defs>
													<filter id="filter_ziskate_<?php echo esc_attr(sanitize_title($item['bod'] ?? '')); ?>" x="0" y="0" width="22.6001" height="20.4" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
														<feFlood flood-opacity="0" result="BackgroundImageFix" />
														<feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape" />
														<feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
														<feOffset dy="4" />
														<feGaussianBlur stdDeviation="1.75" />
														<feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1" />
														<feColorMatrix type="matrix" values="0 0 0 0 0.4298 0 0 0 0 0.82894 0 0 0 0 1 0 0 0 1 0" />
														<feBlend mode="normal" in2="shape" result="effect1_innerShadow" />
													</filter>
												</defs>
											</svg>
										</span>
										<?php echo esc_html($item['bod'] ?? ''); ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
						<div>
							<h3><?php echo esc_html($prokoho_heading); ?></h3>
							<div class="xevos-benefity-section__desc"><?php echo wp_kses_post($prokoho_text); ?></div>
							<ul class="xevos-kyber-politika__checklist">
								<?php foreach ($prokoho_seznam as $item) : ?>
									<li>
										<span class="xevos-kyber-test__check-circle" aria-hidden="true">
											<svg xmlns="http://www.w3.org/2000/svg" width="23" height="17" viewBox="0 0 23 17" fill="none">
												<g filter="url(#filter_prokoho_<?php echo esc_attr(sanitize_title($item['bod'] ?? '')); ?>)">
													<path d="M22.6 2.8L8.5 16.9L0 8.4L2.8 5.6L8.5 11.3L19.8 0L22.6 2.8Z" fill="#00BBFF" />
												</g>
												<defs>
													<filter id="filter_prokoho_<?php echo esc_attr(sanitize_title($item['bod'] ?? '')); ?>" x="0" y="0" width="22.6001" height="20.4" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
														<feFlood flood-opacity="0" result="BackgroundImageFix" />
														<feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape" />
														<feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
														<feOffset dy="4" />
														<feGaussianBlur stdDeviation="1.75" />
														<feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1" />
														<feColorMatrix type="matrix" values="0 0 0 0 0.4298 0 0 0 0 0.82894 0 0 0 0 1 0 0 0 1 0" />
														<feBlend mode="normal" in2="shape" result="effect1_innerShadow" />
													</filter>
												</defs>
											</svg>
										</span>
										<?php echo esc_html($item['bod'] ?? ''); ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
					<div class="xevos-benefity-section__cta">
						<a href="<?php echo esc_url($benefity_cta_url); ?>" class="xevos-btn xevos-btn--primary">
							<span class="xevos-btn__arrow"><svg width="18" height="18" viewBox="0 0 20 20" fill="none">
									<path d="M5 15L15 5M15 5H7M15 5v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
								</svg></span>
							<?php echo esc_html($benefity_cta_text); ?>
						</a>
					</div>
				</div>
			</section>
		<?php endif; ?>

	<!-- 5. Banner -->
	<?php if (get_field('sl_banner_zobrazit') !== false) : ?>
		<?php
		$banner_heading  = get_field('sl_banner_heading') ?: 'Chcete ověřit, jak je vaše organizace odolná?';
		$banner_text     = get_field('sl_banner_text') ?: 'Provedeme testování s využitím <strong>nejmodernějších nástrojů,</strong> aby výsledky odpovídaly aktuálním hrozbám i metodikám.';
		$banner_btn_text = get_field('sl_banner_btn_text') ?: 'POTŘEBUJI POMOC!';
		$banner_btn_url  = get_field('sl_banner_btn_url') ?: '#formular';
		$banner_image    = get_field('sl_banner_image');
		$banner_img_url  = $banner_image ? $banner_image['url'] : get_theme_file_uri('assets/img/kyber-testovani/banner.png');
		?>
		<section class="xevos-section xevos-kt-banner">
			<div class="xevos-section__container">
				<div class="xevos-kt-banner__inner">
					<img src="<?php echo esc_url($banner_img_url); ?>" alt="" class="xevos-kt-banner__bg" loading="lazy">
					<div class="xevos-kt-banner__content">
						<h2><?php echo esc_html($banner_heading); ?></h2>
						<p><?php echo wp_kses( strip_tags( $banner_text, '<strong><b><em><a><br>' ), [ 'strong' => [], 'b' => [], 'em' => [], 'a' => [ 'href' => [], 'target' => [], 'rel' => [] ], 'br' => [] ] ); ?></p>
						<a href="<?php echo esc_url($banner_btn_url); ?>" class="xevos-btn xevos-btn--primary">
							<span class="xevos-btn__arrow"><svg width="18" height="18" viewBox="0 0 20 20" fill="none">
									<path d="M5 15L15 5M15 5H7M15 5v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
								</svg></span>
							<?php echo esc_html($banner_btn_text); ?>
						</a>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<!-- 6. Aktuality -->
	<?php if (get_field('sl_aktuality_zobrazit') !== false) : ?>
		<?php get_template_part('template-parts/components/aktuality'); ?>
	<?php endif; ?>

	<!-- 7. Recenze -->
	<?php if (get_field('sl_recenze_zobrazit') !== false) : ?>
		<?php get_template_part('template-parts/components/recenze'); ?>
	<?php endif; ?>

	<!-- 8. Formulář -->
	<?php if (get_field('sl_formular_zobrazit') !== false) : ?>
		<?php
		$form_heading = get_field('sl_formular_heading') ?: 'Poptávka testování';
		$form_text    = get_field('sl_formular_text') ?: 'Spolupracujeme s firmami, které chtějí mít v <strong>kybernetické bezpečnosti jasno</strong>.';
		?>
		<section class="xevos-section" id="formular">
			<div class="xevos-section__container">
				<div class="xevos-hp-recenze__header">
					<h2><?php echo esc_html($form_heading); ?></h2>
					<p><?php echo wp_kses_post($form_text); ?></p>
				</div>

				<form class="xevos-order-section" method="post" id="xevos-inquiry-form">
					<input type="hidden" name="action" value="xevos_inquiry_form">
					<?php wp_nonce_field( 'xevos_inquiry', 'xevos_inquiry_nonce' ); ?>
					<div class="xevos-kontasl__form">
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
								<label class="xevos-form__label">Firma</label>
								<input type="text" class="xevos-form__input" name="firma">
							</div>
							<div class="xevos-form__group">
								<label class="xevos-form__label">Druh testu <span class="xevos-form__required">*</span></label>
								<select class="xevos-form__input" name="druh_testu" required>
									<option value="">– vyberte –</option>
									<option value="Kybernetická bezpečnost – obecná">Kybernetická bezpečnost – obecná</option>
									<option value="Penetrační testy">Penetrační testy</option>
									<option value="Audit infrastruktury">Audit infrastruktury</option>
								</select>
							</div>
						</div>
						<div class="xevos-form__group">
							<label class="xevos-form__label">Zpráva</label>
							<textarea class="xevos-form__textarea" name="zprava" rows="4"></textarea>
						</div>
						<div class="xevos-form__hp"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
						<div id="xevos-inquiry-message" class="xevos-order-message" style="display:none;"></div>
					</div>

					<div class="xevos-order-summary">
						<?php xevos_component('contact-info'); ?>

						<button type="submit" id="xevos-inquiry-submit" class="xevos-btn xevos-btn--primary">
							<span class="xevos-btn__arrow"><svg width="18" height="18" viewBox="0 0 20 20" fill="none">
									<path d="M5 15L15 5M15 5H7M15 5v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
								</svg></span>
							ODESLAT POPTÁVKU
						</button>
					</div>
				</form>
			</div>
		</section>
	<?php endif; ?>



<?php get_footer(); ?>
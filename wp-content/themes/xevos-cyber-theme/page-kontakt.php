<?php

/**
 * Template Name: Kontakt
 * Figma node 586:20: Hero two-col, contact form + info sidebar, reviews, partners.
 *
 * @package Xevos\CyberTheme
 */

get_header();
$telefon = xevos_get_option('telefon', '+420 591 140 315');
$email   = xevos_get_option('email', 'hello@xevos.eu');
$adresa  = xevos_get_option('adresa', 'Mostárenská 1156/38, 703 00 Ostrava');
$hero_img = get_field('kontakt_hero_obrazek');
$hero_img_url = $hero_img ? $hero_img['url'] : get_theme_file_uri('assets/img/kontakt/kontakt-hero.png');
?>

<main id="main" class="xevos-main xevos-main--glows">

	<!-- Glow blobs -->
	<div class="xevos-glow-blob xevos-glow-blob--left xevos-glow-blob--lg" style="top:-400px;"></div>
	<div class="xevos-glow-blob xevos-glow-blob--top xevos-glow-blob--lg"></div>
	<div class="xevos-glow-blob xevos-glow-blob--right xevos-glow-blob--lg" style="top:800px;"></div>
	<div class="xevos-glow-blob xevos-glow-blob--left-second xevos-glow-blob--lg" style="bottom:0;"></div>

	<!-- Hero: shared component -->
	<?php get_template_part('template-parts/components/hero-page', null, [
		'heading'     => get_field('kontakt_heading') ?: 'Kontaktujte naše specialisty',
		'description' => get_field('kontakt_popis') ?: 'Jsme připraveni konzultovat vaše bezpečnostní potřeby a navrhnout řešení na míru vaší organizaci. Ať už řešíte prevenci, aktuální incident nebo dlouhodobou strategii, pomůžeme vám posílit ochranu dat, infrastruktury a uživatelů.',
		'image_url'   => $hero_img_url,
		'image_mask'  => !in_array( get_field('kontakt_hero_maska'), [ false, 0, '0' ], true ),
	]); ?>

	<!-- Form + Contact info — Figma: 902px form | 574px info, gap 124px -->
	<section class="xevos-section" id="formular">
		<div class="xevos-section__container">
			<div class="xevos-kontakt">
				<!-- Form -->
				<div class="xevos-kontakt__form">
						<form class="xevos-kontakt-form" method="post" id="xevos-contact-form">
							<input type="hidden" name="action" value="xevos_contact_form">
							<?php wp_nonce_field( 'xevos_contact', 'xevos_contact_nonce' ); ?>
							<div class="xevos-form-row">
								<div class="xevos-form__group">
									<label class="xevos-form__label">Jméno *</label>
									<input type="text" class="xevos-form__input" name="jmeno" required>
								</div>
								<div class="xevos-form__group">
									<label class="xevos-form__label">Příjmení *</label>
									<input type="text" class="xevos-form__input" name="prijmeni" required>
								</div>
							</div>
							<div class="xevos-form-row">
								<div class="xevos-form__group">
									<label class="xevos-form__label">Telefon</label>
									<input type="tel" class="xevos-form__input" name="telefon">
								</div>
								<div class="xevos-form__group">
									<label class="xevos-form__label">E-mail *</label>
									<input type="email" class="xevos-form__input" name="email" required>
								</div>
							</div>
							<div class="xevos-form__group">
								<label class="xevos-form__label">Zpráva</label>
								<textarea class="xevos-form__textarea" name="zprava" rows="6"></textarea>
							</div>
							<div class="xevos-form__hp"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
							<div id="xevos-contact-message" class="xevos-order-message" style="display:none;"></div>
							<button type="submit" id="xevos-contact-submit" class="xevos-btn xevos-btn--primary">
								<span class="xevos-btn__arrow"><svg width="18" height="18" viewBox="0 0 20 20" fill="none">
										<path d="M5 15L15 5M15 5H7M15 5v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
									</svg></span>
								ODESLAT
							</button>
						</form>
				</div>

				<!-- Contact sidebar — shared component -->
				<?php xevos_component('contact-info'); ?>
			</div>
		</div>
	</section>

	<!-- Recenze -->
	<?php get_template_part('template-parts/components/recenze'); ?>



<?php get_footer(); ?>
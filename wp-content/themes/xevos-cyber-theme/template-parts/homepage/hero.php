<?php

/**
 * Homepage: Hero section.
 * Figma: "GARANCE KYBERNETICKÉ ODOLNOSTI" + subtitle + CTA + emergency badge.
 */

$show = get_field('hero_zobrazit_sekci');
if ($show === false) return; // Show by default if ACF not yet configured.

$heading    = get_field('hero_heading') ?: 'GARANCE <span>KYBERNETICKÉ ODOLNOSTI</span>';
$subtitle   = get_field('hero_subheading') ?: 'Bez strašení. S jistotou';
$desc       = get_field('hero_popis') ?: 'Pomáháme firmám detekovat hrozby, testujeme systémy a připravujeme týmy na skutečné útoky.';
$cta_text   = get_field('hero_cta_text') ?: 'Chci otestovat zabezpečení';
$cta_url    = get_field('hero_cta_url') ?: '/kontakt/';
$bg         = get_field('hero_background');
?>

<section class="xevos-hero">
	<div class="xevos-hero__bg">

		<video autoplay muted loop playsinline>
			<source src="<?php echo esc_url(get_theme_file_uri('assets/video/hp-hero.mp4')); ?>" type="video/mp4">
		</video>
		<div class="xevos-hero__eclipse"></div>
		<div class="xevos-hero__eclipse"></div>
		<div class="xevos-hero__eclipse"></div>
		<div class="xevos-hero__grid"></div>
	</div>

	<div class="xevos-hero__content">
		<h1 class="xevos-hero__title"><?php echo wp_kses_post($heading); ?></h1>
		<p class="xevos-hero__subtitle"><strong><?php echo esc_html($subtitle); ?></strong></p>
		<p class="xevos-hero__desc"><?php echo esc_html($desc); ?></p>

		<a href="<?php echo esc_url($cta_url); ?>" class="xevos-btn xevos-btn--primary xevos-btn--lg">
			<span class="xevos-btn__arrow">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
					<path d="M5 15L15 5M15 5H7M15 5v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</span>
			<?php echo esc_html(strtoupper($cta_text)); ?>
		</a>
	</div>

	<!-- Emergency badge (from Figma – floating) -->
	<div class="xevos-section__container">
		<div class="xevos-hero__emergency">
			<a href="<?php echo esc_url(home_url('/kontakt/')); ?>" class="xevos-emergency-badge">
				<img src="<?php echo esc_url(get_theme_file_uri('assets/img/homepage/hero-terc.png')); ?>" alt="" class="xevos-emergency-badge__icon">
				<div>
					<span class="xevos-emergency-badge__title">Jsem terčem <strong class="xevos-accent">ÚTOKU!</strong></span>
					<span class="xevos-emergency-badge__sub">Jak postupovat?</span>
				</div>
			</a>
		</div>
	</div>
</section>
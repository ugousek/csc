<?php

/**
 * Template Name: Eventy
 * Figma: Hero two-col + events list + CTA banner.
 *
 * @package Xevos\CyberTheme
 */

get_header();
?>

<main id="main" class="xevos-main">

	<?php get_template_part('template-parts/eventy/hero'); ?>

	<?php get_template_part('template-parts/components/eventy'); ?>

	<?php xevos_component('cta-banner', [
		'title'    => 'Chcete privátní školení pro váš tým?',
		'text'     => 'Uspořádáme workshop přímo ve vaší firmě, přizpůsobený vašim potřebám.',
		'cta_text' => 'Domluvit školení',
		'cta_url'  => home_url('/kontakt/'),
	]); ?>



<?php get_footer(); ?>
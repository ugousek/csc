<?php
/**
 * Template Name: Přehled školení
 * Figma node 593:8230: Training overview — hero, upcoming events list, card grid, CTA.
 *
 * @package Xevos\CyberTheme
 */

get_header();
?>

<main id="main" class="xevos-main">

	<?php get_template_part( 'template-parts/prehled-skoleni/hero' ); ?>

	<?php get_template_part( 'template-parts/homepage/eventy' ); ?>

	<?php get_template_part( 'template-parts/prehled-skoleni/grid' ); ?>

	<?php xevos_component( 'cta-banner', [
		'title'    => 'Potřebujete školení na míru?',
		'text'     => 'Připravíme individuální program přesně podle potřeb vaší organizace.',
		'cta_text' => 'Kontaktujte nás',
		'cta_url'  => home_url( '/kontakt/' ),
	] ); ?>

</main>

<?php get_footer(); ?>

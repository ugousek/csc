<?php
/**
 * Eventy: Hero section.
 * Reads ACF fields and delegates to reusable hero-page component.
 *
 * @package Xevos\CyberTheme
 */

$hero_img = get_field( 'eventy_hero_obrazek' );

get_template_part( 'template-parts/components/hero-page', null, [
	'heading'     => get_field( 'eventy_hero_heading' ) ?: 'Aktuální eventy a školení',
	'description' => get_field( 'eventy_hero_popis' ) ?: 'Workshopy, školení a odborné akce zaměřené na praktickou kybernetickou bezpečnost, aktuální hrozby a reálné scénáře z praxe.',
	'image_url'   => $hero_img ? $hero_img['url'] : get_theme_file_uri( 'assets/img/homepage/eventy-cta.png' ),
	'image_mask'  => !in_array( get_field( 'eventy_hero_maska' ), [ false, 0, '0' ], true ),
] );

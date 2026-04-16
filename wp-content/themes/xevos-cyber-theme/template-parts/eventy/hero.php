<?php
/**
 * Eventy: Hero section.
 * Reads ACF fields and delegates to reusable hero-page component.
 *
 * @package Xevos\CyberTheme
 */

$hero_img = get_field( 'eventy_hero_obrazek' );

get_template_part( 'template-parts/components/hero-page', null, [
	'heading'     => get_field( 'eventy_hero_heading' ) ?: '',
	'description' => get_field( 'eventy_hero_popis' ) ?: '',
	'image_id'    => $hero_img ? (int) ($hero_img['ID'] ?? 0) : 0,
	'image_url'   => '',
	'image_mask'  => !in_array( get_field( 'eventy_hero_maska' ), [ false, 0, '0' ], true ),
] );

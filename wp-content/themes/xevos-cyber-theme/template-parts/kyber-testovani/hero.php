<?php
/**
 * Kyber testování: Hero – delegates to shared hero-page component.
 */

$show = get_field('kt_hero_zobrazit');
if ( $show === '0' || $show === 0 ) return;

$bg = get_field('kt_hero_background');

get_template_part('template-parts/components/hero-page', null, [
	'heading'     => get_field('kt_hero_heading') ?: '',
	'description' => get_field('kt_hero_subheading') ?: '',
	'image_id'    => $bg ? (int) ($bg['ID'] ?? 0) : 0,
	'image_url'   => '',
	'image_mask'  => !in_array( get_field('kt_hero_maska'), [ false, 0, '0' ], true ),
]);

<?php
/**
 * Přehled školení: Hero section.
 * Reads ACF fields and delegates to reusable hero-page component.
 *
 * @package Xevos\CyberTheme
 */

$hero_img = get_field( 'prehled_hero_obrazek' );

get_template_part( 'template-parts/components/hero-page', null, [
	'heading'     => get_field( 'prehled_heading' ) ?: 'Školení, která posilují bezpečnost',
	'description' => get_field( 'prehled_popis' ) ?: 'Praktická a odborně vedená školení zaměřená na kybernetickou bezpečnost, legislativní požadavky i každodenní bezpečnostní návyky.',
	'image_url'   => $hero_img ? $hero_img['url'] : get_theme_file_uri( 'assets/img/prehled-skoleni/hero.png' ),
] );

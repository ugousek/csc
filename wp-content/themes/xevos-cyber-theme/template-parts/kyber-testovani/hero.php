<?php
/**
 * Kyber testování: Hero – delegates to shared hero-page component.
 */

$show = get_field('kt_hero_zobrazit');
if ( $show === '0' || $show === 0 ) return;

$bg = get_field('kt_hero_background');

get_template_part('template-parts/components/hero-page', null, [
	'heading'     => get_field('kt_hero_heading') ?: 'Kybernetické testování',
	'description' => get_field('kt_hero_subheading') ?: 'Kybernetické testování je soubor praktických technik, které simulují reálné útoky s cílem odhalit zranitelnosti dříve, než je objeví skutečný útočník a to s využitím nejmodernějšího softwaru, pokročilé automatizace a umělé inteligence.',
	'image_url'   => $bg ? $bg['url'] : get_theme_file_uri('assets/img/kyber-testovani-hero.png'),
	'image_mask'  => !in_array( get_field('kt_hero_maska'), [ false, 0, '0' ], true ),
]);

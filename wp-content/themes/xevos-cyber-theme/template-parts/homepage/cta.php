<?php
/**
 * Homepage section: CTA.
 *
 * @package Xevos\CyberTheme
 */

if ( ! get_field( 'cta_zobrazit_sekci' ) ) return;

xevos_component( 'cta-banner', [
	'title'    => get_field( 'cta_heading' ),
	'text'     => get_field( 'cta_text' ),
	'cta_text' => get_field( 'cta_button_text' ),
	'cta_url'  => get_field( 'cta_button_url' ),
] );

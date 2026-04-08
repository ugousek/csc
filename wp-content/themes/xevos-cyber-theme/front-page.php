<?php

/**
 * Homepage template – matches Figma design.
 *
 * @package Xevos\CyberTheme
 */

get_header();
?>

<main id="main" class="xevos-main xevos-main--glows">

	<!-- Glow blobs -->
	<div class="xevos-glow-blob xevos-glow-blob--right" style="top:577px;"></div>
	<div class="xevos-glow-blob xevos-glow-blob--left" style="top:1800px;"></div>
	<div class="xevos-glow-blob xevos-glow-blob--right-second" style="top:3500px;"></div>

	<?php
	xevos_homepage_section('hero');
	xevos_homepage_section('sluzby');
	get_template_part('template-parts/components/eventy');
	get_template_part('template-parts/components/kyber-testovani');
	xevos_homepage_section('statistiky');
	xevos_homepage_section('kyber-politika');
	get_template_part('template-parts/components/aktuality');
	get_template_part('template-parts/components/recenze');
	?>


<?php get_footer(); ?>

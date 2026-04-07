<?php

/**
 * Homepage template – matches Figma design.
 *
 * @package Xevos\CyberTheme
 */

get_header();
?>

<main id="main" class="xevos-main">
	<?php
	// Each partial checks its own ACF 'zobrazit_sekci' toggle.

	?>

	<!-- Figma Ellipse 22: right glow spanning hero → services -->
	<div class="xevos-glow-wrap xevos-glow-wrap--hero">
		<div class="xevos-glow-blob xevos-glow-blob--right" style="top:577px;"></div>
		<?php
		xevos_homepage_section('hero');
		xevos_homepage_section('sluzby');
		?>
	</div>

	<!-- Figma Ellipse 23: left glow from mid-eventy → kyber testování -->
	<div class="xevos-glow-wrap">
		<div class="xevos-glow-blob xevos-glow-blob--left"></div>
		<?php
		xevos_homepage_section('eventy');
		get_template_part('template-parts/components/kyber-testovani');
		?>
	</div>

	<?php  ?>

	<!-- Figma Ellipse 25: right glow spanning kyber-politika → aktuality → recenze -->
	<div class="xevos-glow-wrap">
		<div class="xevos-glow-blob xevos-glow-blob--right-second" style="top:100px;"></div>
		<?php
		xevos_homepage_section('statistiky');
		xevos_homepage_section('kyber-politika');
		get_template_part('template-parts/components/aktuality');
		get_template_part('template-parts/components/recenze');
		?>
	</div>


<?php
get_footer();

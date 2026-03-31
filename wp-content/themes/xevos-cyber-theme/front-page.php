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
	xevos_homepage_section( 'hero' );
	xevos_homepage_section( 'sluzby' );
	?>

	<!-- Glow: right blob between eventy & kyber testování -->
	<div class="xevos-glow-wrap">
		<div class="xevos-glow-blob xevos-glow-blob--right" style="top:-200px;"></div>
		<?php
		xevos_homepage_section( 'eventy' );
		get_template_part( 'template-parts/components/kyber-testovani' );
		?>
	</div>

	<!-- Glow: left blob on statistiky & politika -->
	<div class="xevos-glow-wrap">
		<div class="xevos-glow-blob xevos-glow-blob--left xevos-glow-blob--lg" style="top:0;"></div>
		<?php
		xevos_homepage_section( 'statistiky' );
		xevos_homepage_section( 'kyber-politika' );
		?>
	</div>

	<!-- Glow: right blob on aktuality & recenze -->
	<div class="xevos-glow-wrap">
		<div class="xevos-glow-blob xevos-glow-blob--right" style="top:100px;"></div>
		<?php
		get_template_part( 'template-parts/components/aktuality' );
		xevos_homepage_section( 'recenze' );
		?>
	</div>
	<?php ?>
</main>

<?php
get_footer();

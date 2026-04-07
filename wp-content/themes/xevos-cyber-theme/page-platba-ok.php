<?php
/**
 * Template Name: Platba OK
 * Payment success confirmation page.
 *
 * @package Xevos\CyberTheme
 */

get_header();
?>

<main id="main" class="xevos-main">
	<div class="xevos-platba-result xevos-platba-result--ok">
		<svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
			<circle cx="40" cy="40" r="38" stroke="#10b981" stroke-width="4"/>
			<path d="M24 40l12 12 20-20" stroke="#10b981" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
		<h1>Platba proběhla úspěšně</h1>
		<p>Děkujeme za vaši objednávku. Na zadaný e-mail jsme odeslali potvrzení s podrobnostmi.</p>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="xevos-btn xevos-btn--primary">
			<span class="xevos-btn__arrow"><svg width="18" height="18" viewBox="0 0 20 20" fill="none"><path d="M5 15L15 5M15 5H7M15 5v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
			ZPĚT NA HLAVNÍ STRÁNKU
		</a>
	</div>


<?php get_footer(); ?>

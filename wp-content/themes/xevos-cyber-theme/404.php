<?php
/**
 * 404 template – dark cyber theme.
 *
 * @package Xevos\CyberTheme
 */

get_header();
?>

<main id="main" class="xevos-main">
	<div class="xevos-404">
		<h1 class="xevos-404__title">404</h1>
		<p class="xevos-404__text">Stránka, kterou hledáte, neexistuje nebo byla přesunuta.</p>
		<div class="xevos-404__search">
			<?php get_search_form(); ?>
		</div>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="xevos-btn xevos-btn--primary">
			<span class="xevos-btn__arrow"><svg width="18" height="18" viewBox="0 0 20 20" fill="none"><path d="M5 15L15 5M15 5H7M15 5v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
			ZPĚT NA HLAVNÍ STRÁNKU
		</a>
	</div>


<?php get_footer(); ?>

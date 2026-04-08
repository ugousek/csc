<?php
/**
 * 404 template – dark cyber theme.
 *
 * @package Xevos\CyberTheme
 */

get_header();
?>

<main id="main" class="xevos-main xevos-main--glows">

	<!-- Glow blobs -->
	<div class="xevos-glow-blob xevos-glow-blob--left xevos-glow-blob--lg" style="top:-400px;"></div>
	<div class="xevos-glow-blob xevos-glow-blob--right xevos-glow-blob--lg" style="top:200px;"></div>

	<div class="xevos-404">
		<div class="xevos-404__badge">
			<svg class="xevos-404__shield" width="80" height="80" viewBox="0 0 24 24" fill="none">
				<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="var(--color-accent)" stroke-width="1.5" fill="none"/>
				<path d="M15 9l-6 6M9 9l6 6" stroke="var(--color-accent)" stroke-width="1.5" stroke-linecap="round"/>
			</svg>
		</div>

		<h1 class="xevos-404__title">404</h1>
		<h2 class="xevos-404__heading">Stránka nenalezena</h2>
		<p class="xevos-404__text">Stránka, kterou hledáte, neexistuje nebo byla přesunuta.<br>Zkuste vyhledat obsah nebo se vraťte na hlavní stránku.</p>

		<div class="xevos-404__search">
			<form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="xevos-404__search-form">
				<svg class="xevos-404__search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
				<input type="search" name="s" placeholder="Hledat na webu..." class="xevos-404__search-input" value="">
			</form>
		</div>

		<a href="<?php echo esc_url(home_url('/')); ?>" class="xevos-btn xevos-btn--primary">
			<span class="xevos-btn__arrow"><svg width="18" height="18" viewBox="0 0 20 20" fill="none"><path d="M5 15L15 5M15 5H7M15 5v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
			ZPĚT NA HLAVNÍ STRÁNKU
		</a>
	</div>

<?php get_footer(); ?>

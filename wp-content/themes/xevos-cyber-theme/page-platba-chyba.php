<?php
/**
 * Template Name: Platba chyba
 * Payment error page.
 *
 * @package Xevos\CyberTheme
 */

get_header();
?>

<main id="main" class="xevos-main">
	<div class="xevos-platba-result xevos-platba-result--error">
		<svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
			<circle cx="40" cy="40" r="38" stroke="#ef4444" stroke-width="4"/>
			<path d="M28 28l24 24M52 28L28 52" stroke="#ef4444" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
		<h1>Platba se nezdařila</h1>
		<p>Při zpracování platby došlo k chybě. Zkuste to prosím znovu nebo nás kontaktujte.</p>
		<div class="xevos-platba-result__buttons">
			<a href="javascript:history.back()" class="xevos-btn xevos-btn--primary">
				<span class="xevos-btn__arrow"><svg width="18" height="18" viewBox="0 0 20 20" fill="none"><path d="M5 15L15 5M15 5H7M15 5v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
				ZKUSIT ZNOVU
			</a>
			<a href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>" class="xevos-btn xevos-btn--outline">
				KONTAKTOVAT NÁS
			</a>
		</div>
	</div>
</main>

<?php get_footer(); ?>

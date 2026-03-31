<?php
/**
 * Custom search form.
 *
 * @package Xevos\CyberTheme
 */
?>

<form role="search" method="get" class="xevos-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="xevos-search-form__label" for="xevos-search">
		<span class="screen-reader-text"><?php esc_html_e( 'Hledat:', 'xevos-cyber' ); ?></span>
	</label>
	<div class="xevos-search-form__wrap">
		<input type="search"
			id="xevos-search"
			class="xevos-search-form__input xevos-form__input"
			placeholder="<?php esc_attr_e( 'Hledat…', 'xevos-cyber' ); ?>"
			value="<?php echo get_search_query(); ?>"
			name="s"
			autocomplete="off">
		<button type="submit" class="xevos-search-form__submit xevos-btn xevos-btn--primary">
			<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="2"/><path d="M14 14l4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
			<span class="screen-reader-text"><?php esc_html_e( 'Hledat', 'xevos-cyber' ); ?></span>
		</button>
	</div>
	<div class="xevos-search-form__results" id="live-search-results" aria-live="polite" hidden></div>
</form>

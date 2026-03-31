<?php
/**
 * Component: Recenze Card — Figma node 231:14242
 * 417x303, cyan border, big quote mark, text only.
 *
 * @package Xevos\CyberTheme
 */

$text = get_field( 'text_recenze' ) ?: '';
if ( ! $text ) return;
?>

<div class="xevos-recenze-card swiper-slide">
	<div class="xevos-recenze-card__inner">
		<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/global/anotace.svg' ) ); ?>" alt="" class="xevos-recenze-card__quote" aria-hidden="true">
		<blockquote class="xevos-recenze-card__text"><?php echo esc_html( $text ); ?></blockquote>
	</div>
</div>

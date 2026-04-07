<?php
/**
 * Component: Recenze Card
 * Text recenze + jméno kurzívou pod tím.
 *
 * @package Xevos\CyberTheme
 */

$text  = get_field( 'text_recenze' ) ?: '';
$jmeno = get_field( 'jmeno' ) ?: '';
if ( ! $text ) return;
?>

<div class="xevos-recenze-card swiper-slide">
	<div class="xevos-recenze-card__inner">
		<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/global/anotace.svg' ) ); ?>" alt="" class="xevos-recenze-card__quote" aria-hidden="true">
		<blockquote class="xevos-recenze-card__text"><?php echo esc_html( $text ); ?></blockquote>
		<?php if ( $jmeno ) : ?>
			<cite class="xevos-recenze-card__author"><?php echo esc_html( $jmeno ); ?></cite>
		<?php endif; ?>
	</div>
</div>

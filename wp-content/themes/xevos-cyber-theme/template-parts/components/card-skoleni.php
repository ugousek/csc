<?php
/**
 * Component: Školení Card.
 *
 * @package Xevos\CyberTheme
 */

$cena    = get_field( 'cena_s_dph' );
$typ     = get_field( 'typ' );
$terminy = get_field( 'terminy' );

// Find nearest future date.
$nejblizsi = '';
if ( $terminy ) {
	$now = time();
	foreach ( $terminy as $t ) {
		if ( ! empty( $t['datum'] ) && strtotime( $t['datum'] ) >= $now ) {
			$nejblizsi = $t['datum'];
			break;
		}
	}
}
?>

<article class="xevos-card">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="xevos-card__image">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'xevos-card' ); ?>
			</a>
		</div>
	<?php endif; ?>
	<div class="xevos-card__body">
		<?php if ( $typ ) : ?>
			<span class="xevos-card__badge"><?php echo esc_html( ucfirst( $typ ) ); ?></span>
		<?php endif; ?>

		<h3 class="xevos-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<?php if ( $nejblizsi ) : ?>
			<p class="xevos-card__meta"><?php printf( esc_html__( 'Nejbližší termín: %s', 'xevos-cyber' ), esc_html( $nejblizsi ) ); ?></p>
		<?php endif; ?>

		<?php if ( $cena ) : ?>
			<p class="xevos-card__price"><?php echo esc_html( number_format( (float) $cena, 0, ',', ' ' ) . ' Kč' ); ?></p>
		<?php endif; ?>

		<?php xevos_component( 'button', [
			'url'     => get_the_permalink(),
			'text'    => __( 'Detail školení', 'xevos-cyber' ),
			'variant' => 'primary',
			'size'    => 'sm',
		] ); ?>
	</div>
</article>

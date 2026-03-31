<?php
/**
 * Kyber testování: Slider section.
 */

if ( ! get_field( 'kt_slider_zobrazit' ) ) return;

$slides = get_field( 'kt_slider_items' );
if ( ! $slides ) return;
?>

<section class="xevos-section" style="background:#f8fafc;">
	<div class="xevos-section__container">
		<div class="swiper" id="kt-swiper">
			<div class="swiper-wrapper">
				<?php foreach ( $slides as $slide ) : ?>
					<div class="swiper-slide">
						<?php if ( ! empty( $slide['obrazek'] ) ) : ?>
							<?php echo xevos_get_image( $slide['obrazek'], 'large' ); ?>
						<?php endif; ?>
						<?php if ( ! empty( $slide['popis'] ) ) : ?>
							<p style="margin-top:1rem;text-align:center;"><?php echo esc_html( $slide['popis'] ); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="swiper-pagination"></div>
		</div>
	</div>
</section>

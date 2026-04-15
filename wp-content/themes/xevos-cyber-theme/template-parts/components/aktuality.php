<?php

/**
 * Component: Kyber aktuality – latest articles.
 * Shared between homepage and kyberneticke-testovani page.
 * Figma node 271:16949: Centered heading, 3 article cards with nav arrows, CTA button.
 */

$show = get_field('aktuality_zobrazit_sekci');
if ($show === false && $show !== null) return;

$heading = get_field('aktuality_heading') ?: ( get_field('kt_aktuality_heading') ?: '' );
$desc    = get_field('aktuality_text') ?: ( get_field('kt_aktuality_text') ?: '' );
$count   = (int) (get_field('aktuality_pocet') ?: 9);

$query = new WP_Query([
	'post_type'      => 'aktualita',
	'post_status'    => 'publish',
	'posts_per_page' => $count,
]);

$has_aktuality = $query->have_posts();

/* Skrýt celou sekci, když nejsou žádné publikované aktuality. */
if ( ! $has_aktuality ) {
	wp_reset_postdata();
	return;
}
?>

<section class="xevos-section xevos-hp-aktuality">
	<div class="xevos-section__container">

		<?php if ( $heading || $desc ) : ?>
			<!-- Centered heading -->
			<div class="xevos-hp-aktuality__header">
				<?php if ( $heading ) : ?>
					<h2><?php echo esc_html($heading); ?></h2>
				<?php endif; ?>
				<?php if ( $desc ) : ?>
					<p class="xevos-hp-aktuality__desc--mixed"><?php echo wp_kses_post(strip_tags($desc, '<strong><b><em><br>')); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<!-- Cards carousel -->
		<div class="xevos-hp-aktuality__carousel">
			<button class="xevos-nav-arrow xevos-nav-arrow--prev xevos-aktuality-prev" aria-label="Předchozí">
				<svg width="12" height="20" viewBox="0 0 12 20" fill="none">
					<path d="M10 2L2 10l8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</button>

			<div class="swiper xevos-aktuality-swiper" id="aktuality-swiper">
				<div class="swiper-wrapper" style="align-items:stretch;">
					<?php while ($query->have_posts()) : $query->the_post(); ?>
						<div class="swiper-slide">
							<?php get_template_part('template-parts/components/card-aktualita'); ?>
						</div>
					<?php endwhile;
					wp_reset_postdata(); ?>
				</div>
				<div class="swiper-pagination xevos-aktuality-pagination"></div>
			</div>

			<button class="xevos-nav-arrow xevos-nav-arrow--next xevos-aktuality-next" aria-label="Další">
				<svg width="12" height="20" viewBox="0 0 12 20" fill="none">
					<path d="M2 2l8 8-8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</button>
		</div>

		<!-- CTA button -->
		<div class="xevos-hp-aktuality__footer">
			<a href="<?php echo esc_url(get_post_type_archive_link('aktualita')); ?>" class="xevos-btn xevos-btn--outline">ZOBRAZIT VŠECHNY ČLÁNKY</a>
		</div>

	</div>
</section>
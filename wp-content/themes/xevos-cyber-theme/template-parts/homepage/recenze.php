<?php
/**
 * Homepage: Řekli o nás – reviews slider.
 * Figma node 271:17056: Centered heading, 3 review cards with big quote mark + nav arrows.
 */

$show = get_field( 'recenze_zobrazit_sekci' );
if ( $show === false ) return;

$heading  = get_field( 'recenze_heading' ) ?: 'Řekli o nás';
$subtitle = 'Spolupracujeme s firmami, které chtějí mít v kybernetické bezpečnosti jasno. Zpětná vazba od klientů je pro nás potvrzením práce v reálném provozu.';

$query = new WP_Query( [
	'post_type'      => 'recenze',
	'posts_per_page' => -1,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
] );

if ( ! $query->have_posts() ) return;
?>

<section class="xevos-section xevos-hp-recenze">
	<div class="xevos-section__container">

		<!-- Centered heading -->
		<div class="xevos-hp-recenze__header">
			<h2><?php echo esc_html( $heading ); ?></h2>
			<p><?php echo esc_html( $subtitle ); ?></p>
		</div>

		<!-- Reviews carousel with nav arrows -->
		<div class="xevos-hp-recenze__carousel">
			<button class="xevos-nav-arrow xevos-nav-arrow--prev" aria-label="Předchozí">
				<svg width="12" height="20" viewBox="0 0 12 20" fill="none"><path d="M10 2L2 10l8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>

			<div class="swiper xevos-recenze-swiper" id="recenze-swiper">
				<div class="swiper-wrapper">
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<?php get_template_part( 'template-parts/components/card-recenze' ); ?>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			</div>

			<button class="xevos-nav-arrow xevos-nav-arrow--next" aria-label="Další">
				<svg width="12" height="20" viewBox="0 0 12 20" fill="none"><path d="M2 2l8 8-8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
		</div>

	</div>
</section>

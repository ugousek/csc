<?php
/**
 * Homepage: Řekli o nás – reviews slider.
 * Figma node 271:17056: Centered heading, 3 review cards with big quote mark + nav arrows.
 */

$show = get_field( 'recenze_zobrazit_sekci' );
if ( $show === false && $show !== null ) return;

$heading  = $args['heading']  ?? ( get_field( 'recenze_heading' ) ?: '' );
$subtitle = $args['subtitle'] ?? ( get_field( 'recenze_popis' ) ?: '' );

$count = (int) ( get_field( 'recenze_pocet' ) ?: 9 );

$query = new WP_Query( [
	'post_type'      => 'recenze',
	'posts_per_page' => $count,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
] );

$has_recenze = $query->have_posts();

/* Skrýt celou sekci, když nejsou žádné recenze. */
if ( ! $has_recenze ) {
	wp_reset_postdata();
	return;
}
?>

<section class="xevos-section xevos-hp-recenze">
	<div class="xevos-hp-recenze__wave" id="recenze-wave-bg"></div>
	<div class="xevos-section__container">

		<!-- Centered heading -->
		<div class="xevos-hp-recenze__header">
			<?php if ( $heading ) : ?>
				<h2><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $subtitle ) : ?>
				<p class="xevos-hp-recenze__desc-mixed"><?php echo wp_kses( $subtitle, [ 'strong' => [] ] ); ?></p>
			<?php endif; ?>
		</div>

		<!-- Reviews carousel with nav arrows -->
		<div class="xevos-hp-recenze__carousel">
			<button class="xevos-nav-arrow xevos-nav-arrow--prev" aria-label="Předchozí">
				<svg width="12" height="20" viewBox="0 0 12 20" fill="none"><path d="M10 2L2 10l8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>

			<div class="swiper xevos-recenze-swiper" id="recenze-swiper">
				<div class="swiper-wrapper">
					<?php while ( $query->have_posts() ) : $query->the_post();
						get_template_part( 'template-parts/components/card-recenze' );
					endwhile;
					wp_reset_postdata(); ?>
				</div>
				<div class="swiper-pagination xevos-recenze-pagination"></div>
			</div>

			<button class="xevos-nav-arrow xevos-nav-arrow--next" aria-label="Další">
				<svg width="12" height="20" viewBox="0 0 12 20" fill="none"><path d="M2 2l8 8-8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
		</div>

	</div>

	<!-- Wave background animation -->
	<script type="importmap">
	{ "imports": {
		"three": "https://unpkg.com/three@0.162.0/build/three.module.js",
		"three/addons/": "https://unpkg.com/three@0.162.0/examples/jsm/"
	} }
	</script>
	<script type="module">
	import { initWaveBg } from '<?php echo esc_url( get_theme_file_uri( 'assets/js/wave-bg.js' ) ); ?>';
	initWaveBg(document.getElementById('recenze-wave-bg'));
	</script>
</section>

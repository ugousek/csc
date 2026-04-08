<?php
/**
 * Search results template.
 *
 * @package Xevos\CyberTheme
 */

get_header();
?>

<main id="main" class="xevos-main xevos-main--glows">

	<!-- Glow blobs -->
	<div class="xevos-glow-blob xevos-glow-blob--left xevos-glow-blob--lg" style="top:-400px;"></div>
	<div class="xevos-glow-blob xevos-glow-blob--top xevos-glow-blob--lg"></div>
	<div class="xevos-glow-blob xevos-glow-blob--right xevos-glow-blob--lg" style="top:800px;"></div>
	<div class="xevos-glow-blob xevos-glow-blob--left-second xevos-glow-blob--lg" style="bottom:0;"></div>

	<!-- Hero -->
	<?php get_template_part( 'template-parts/components/hero-page', null, [
		'heading'     => __( 'Výsledky <span style="color:#F527AA">vyhledávání</span>', 'xevos-cyber' ),
		'description' => sprintf( __( 'Hledaný výraz: „<strong>%s</strong>" — nalezeno <strong>%s</strong> výsledků', 'xevos-cyber' ), esc_html( get_search_query() ), $wp_query->found_posts ),
	] ); ?>

	<!-- Results -->
	<section class="xevos-section xevos-search-results">
		<div class="xevos-section__container">

			<?php if ( have_posts() ) : ?>

				<div class="xevos-search-results__grid">
					<?php while ( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'template-parts/components/card-aktualita' ); ?>
					<?php endwhile; ?>
				</div>

				<div class="xevos-archive-bottom">
					<?php the_posts_pagination( [ 'class' => 'xevos-pagination' ] ); ?>
				</div>

			<?php else : ?>
				<div class="xevos-search-results__empty">
					<svg width="64" height="64" viewBox="0 0 24 24" fill="none" class="xevos-search-results__empty-icon">
						<circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="1.5"/>
						<path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
					</svg>
					<h2><?php esc_html_e( 'Žádné výsledky', 'xevos-cyber' ); ?></h2>
					<p><?php printf( __( 'Pro výraz „%s" jsme nic nenašli. Zkuste jiný dotaz.', 'xevos-cyber' ), '<strong>' . esc_html( get_search_query() ) . '</strong>' ); ?></p>
					<div class="xevos-search-results__form">
						<?php get_search_form(); ?>
					</div>
				</div>
			<?php endif; ?>

		</div>
	</section>



<?php get_footer(); ?>

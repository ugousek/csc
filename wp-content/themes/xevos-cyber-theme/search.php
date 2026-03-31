<?php
/**
 * Search results template.
 *
 * @package Xevos\CyberTheme
 */

get_header();
?>

<main id="main" class="xevos-main">

	<!-- Hero: two-column — same pattern as blog-hero -->
	<section class="xevos-page-hero xevos-blog-hero">
		<div class="xevos-page-hero__bg xevos-page-hero__bg--gradient"></div>
		<div class="xevos-section__container">
			<div class="xevos-blog-hero__grid">
				<div class="xevos-blog-hero__content">
					<h1>Výsledky vyhledávání</h1>
					<p class="xevos-blog-hero__desc"><?php printf( esc_html__( 'Hledaný výraz: „%s"', 'xevos-cyber' ), get_search_query() ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<section class="xevos-section xevos-blog-archive">
		<div class="xevos-section__container">

			<!-- Cards grid -->
			<div class="xevos-aktuality-archive__grid" id="search-results-grid">
				<?php if ( have_posts() ) : ?>
					<?php while ( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'template-parts/components/card-aktualita' ); ?>
					<?php endwhile; ?>
				<?php else : ?>
					<div class="xevos-no-results">
						<p><?php esc_html_e( 'Žádné výsledky pro zadaný výraz.', 'xevos-cyber' ); ?></p>
						<?php get_search_form(); ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- Pagination -->
			<?php if ( have_posts() ) : ?>
				<div class="xevos-archive-bottom">
					<?php the_posts_pagination( [ 'class' => 'xevos-pagination' ] ); ?>
				</div>
			<?php endif; ?>

		</div>
	</section>

</main>

<?php get_footer(); ?>

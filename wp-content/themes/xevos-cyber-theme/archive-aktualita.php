<?php

/**
 * Archive: Aktuality (Blog).
 * Figma node 588:4168: Two-col hero, filter pills, 3-col card grid (4 rows), pagination + load more.
 *
 * @package Xevos\CyberTheme
 */

get_header();

$terms = get_terms(['taxonomy' => 'kategorie-aktualit', 'hide_empty' => true]);
?>

<main id="main" class="xevos-main">

	<!-- Hero: two-column — Figma: Claim 690px left + image 910px right -->
	<section class="xevos-page-hero xevos-blog-hero">
		<div class="xevos-page-hero__bg xevos-page-hero__bg--gradient"></div>
		<div class="xevos-section__container">
			<div class="xevos-blog-hero__grid">
				<div class="xevos-blog-hero__content">
					<h1>Znalosti, které posilují bezpečnost</h1>
					<p class="xevos-blog-hero__desc">Odborné články, analýzy a aktuální pohledy na kybernetickou bezpečnost, legislativu i moderní technologie. Sdílíme zkušenosti z praxe, vysvětlujeme nové hrozby a přinášíme doporučení, která vám pomohou lépe chránit data, infrastrukturu i celou organizaci v dynamicky se měnícím digitálním prostředí.</p>
				</div>
				<div class="xevos-blog-hero__image">
					<img src="<?php echo esc_url(get_theme_file_uri('assets/img/blog/blog-hero.png')); ?>" alt="Blog" loading="lazy">
				</div>
			</div>
		</div>
	</section>

	<section class="xevos-section xevos-blog-archive">
		<div class="xevos-section__container">

			<div class="xevos-archive-toolbar">
				<div class="xevos-aktuality-archive__filters" id="aktuality-filters">
					<button class="xevos-filter-pill is-active" data-term=""><?php esc_html_e('Vše', 'xevos-cyber'); ?></button>
					<?php if (! empty($terms) && ! is_wp_error($terms)) : ?>
						<?php foreach ($terms as $term) : ?>
							<button class="xevos-filter-pill" data-term="<?php echo esc_attr($term->slug); ?>">
								<?php echo esc_html($term->name); ?>
							</button>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
				<div class="xevos-archive-toolbar__sort">
					<label for="sort-order">Řadit dle</label>
					<select id="sort-order">
						<option value="DESC">Nejnovější</option>
						<option value="ASC">Nejstarší</option>
					</select>
				</div>
			</div>

			<div class="xevos-grid-wrap">
				<!-- Loader overlay -->
				<div class="xevos-loader" id="aktuality-loader">
					<div class="xevos-loader__spinner"></div>
				</div>

				<div class="xevos-aktuality-archive__grid" id="aktuality-grid">
				<?php if (have_posts()) : ?>
					<?php while (have_posts()) : the_post(); ?>
						<?php get_template_part('template-parts/components/card-aktualita'); ?>
					<?php endwhile; ?>
				<?php else : ?>
					<p class="xevos-no-results"><?php esc_html_e('Zatím žádné aktuality.', 'xevos-cyber'); ?></p>
				<?php endif; ?>
			</div>
			</div><!-- /.xevos-grid-wrap -->

			<!-- Bottom bar: load more vlevo + pagination vpravo -->
			<div class="xevos-archive-bottom">
				<button class="xevos-btn xevos-btn--outline" id="load-more-btn">ZOBRAZIT DALŠÍ ČLÁNKY</button>
				<div class="xevos-pagination"></div>
			</div>

		</div>
	</section>

</main>

<?php get_footer(); ?>
<?php
/**
 * Archive: Školení.
 * Figma node 593-8231: Hero, filter pills, list cards with meta, pagination.
 *
 * @package Xevos\CyberTheme
 */

get_header();
$terms = get_terms(['taxonomy' => 'kategorie-skoleni', 'hide_empty' => false]);
?>

<main id="main" class="xevos-main">

	<!-- Hero -->
	<?php get_template_part('template-parts/components/hero-page', null, [
		'heading'     => 'Školení, která posilují bezpečnost',
		'description' => 'Praktická a odborně vedená školení zaměřená na kybernetickou bezpečnost, legislativní požadavky i každodenní bezpečnostní návyky. Pomáháme managementu i zaměstnancům budovat reálnou odolnost organizace vůči moderním hrozbám.',
		'image_url'   => get_theme_file_uri('assets/img/prehled-skoleni/hero.png'),
	]); ?>

	<section class="xevos-section xevos-skoleni-archive">
		<div class="xevos-section__container">

			<!-- Filters + sort -->
			<div class="xevos-archive-toolbar">
				<div class="xevos-aktuality-archive__filters" id="skoleni-filters">
					<button class="xevos-filter-pill is-active" data-term="">Vše</button>
					<?php if (!empty($terms) && !is_wp_error($terms)) : ?>
						<?php foreach ($terms as $term) : ?>
							<button class="xevos-filter-pill" data-term="<?php echo esc_attr($term->slug); ?>">
								<?php echo esc_html($term->name); ?>
							</button>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
				<div class="xevos-archive-toolbar__sort">
					<label for="skoleni-sort">Řadit dle</label>
					<select id="skoleni-sort">
						<option value="date-DESC">Nejbližší</option>
						<option value="date-ASC">Nejstarší</option>
						<option value="price-ASC">Cena (nejnižší)</option>
						<option value="price-DESC">Cena (nejvyšší)</option>
					</select>
				</div>
			</div>

			<!-- Loader overlay -->
			<div class="xevos-grid-wrap">
				<div class="xevos-loader" id="skoleni-loader">
					<div class="xevos-loader__spinner"></div>
				</div>

				<!-- List cards -->
				<div class="xevos-skoleni-archive__list" id="skoleni-grid">
					<?php if (have_posts()) : ?>
						<?php while (have_posts()) : the_post(); ?>
							<?php get_template_part('template-parts/components/card-skoleni-list'); ?>
						<?php endwhile; ?>
					<?php else : ?>
						<p class="xevos-no-results">Zatím žádná školení.</p>
					<?php endif; ?>
				</div>
			</div>

			<!-- Bottom: load more center + pagination right -->
			<div class="xevos-archive-bottom">
				<button class="xevos-btn xevos-btn--outline" id="skoleni-load-more">ZOBRAZIT DALŠÍ ŠKOLENÍ</button>
				<div class="xevos-pagination" id="skoleni-pagination"></div>
			</div>

		</div>
	</section>

</main>

<?php get_footer(); ?>

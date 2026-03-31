<?php

/**
 * Component: Kyber aktuality – latest articles.
 * Shared between homepage and kyberneticke-testovani page.
 * Figma node 271:16949: Centered heading, 3 article cards with nav arrows, CTA button.
 */

$show = get_field('aktuality_zobrazit_sekci');
if ($show === false && $show !== null) return;

$heading = get_field('aktuality_heading') ?: (get_field('kt_aktuality_heading') ?: 'Kyber aktuality');
$desc    = get_field('aktuality_text') ?: (get_field('kt_aktuality_text') ?: 'Aktuality ze světa kybernetické bezpečnosti, legislativy a praxe. Sdílíme novinky k NIS2, ISO normám, reálným incidentům i změnám, které mají dopad na firmy a jejich bezpečnost.');
$count   = (int) (get_field('aktuality_pocet') ?: 3);

$query = new WP_Query([
	'post_type'      => 'aktualita',
	'post_status'    => 'publish',
	'posts_per_page' => $count,
]);

if (! $query->have_posts()) return;
?>

<section class="xevos-section xevos-hp-aktuality">
	<div class="xevos-section__container">

		<!-- Centered heading -->
		<div class="xevos-hp-aktuality__header">
			<h2><?php echo esc_html($heading); ?></h2>
			<p><?php echo esc_html($desc); ?></p>
		</div>

		<!-- Cards with nav arrows -->
		<div class="xevos-hp-aktuality__carousel">
			<button class="xevos-nav-arrow xevos-nav-arrow--prev" aria-label="Předchozí">
				<svg width="12" height="20" viewBox="0 0 12 20" fill="none">
					<path d="M10 2L2 10l8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</button>

			<div class="xevos-aktuality-archive__grid">
				<?php while ($query->have_posts()) : $query->the_post(); ?>
					<?php get_template_part('template-parts/components/card-aktualita'); ?>
				<?php endwhile;
				wp_reset_postdata(); ?>
			</div>

			<button class="xevos-nav-arrow xevos-nav-arrow--next" aria-label="Další">
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
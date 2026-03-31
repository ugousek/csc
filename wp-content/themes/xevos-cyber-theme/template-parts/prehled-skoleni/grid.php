<?php
/**
 * Přehled školení: Card grid with filters.
 * Figma: 3-column grid of training cards with category pills + sort.
 *
 * @package Xevos\CyberTheme
 */

$terms = get_terms( [ 'taxonomy' => 'kategorie-skoleni', 'hide_empty' => true ] );
?>

<section class="xevos-section xevos-prehled-grid-section">
	<div class="xevos-section__container">

		<div class="xevos-prehled-grid-section__header">
			<h2>Všechna školení</h2>
			<p>Vyberte si z naší nabídky kurzů a workshopů podle vašich potřeb.</p>
		</div>

		<!-- Filters -->
		<div class="xevos-archive-toolbar">
			<div class="xevos-aktuality-archive__filters" id="skoleni-filters">
				<button class="xevos-filter-pill is-active" data-term="">Vše</button>
				<?php if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) : ?>
					<?php foreach ( $terms as $term ) : ?>
						<button class="xevos-filter-pill" data-term="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></button>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			<div class="xevos-archive-toolbar__sort">
				<span>Řadit dle</span>
				<select><option>Nejbližší</option><option>Cena</option></select>
			</div>
		</div>

		<!-- Cards grid — Figma: 3 columns -->
		<div class="xevos-prehled-cards" id="skoleni-grid">
			<?php
			$skoleni_query = new WP_Query( [
				'post_type'      => 'skoleni',
				'posts_per_page' => 12,
				'orderby'        => 'date',
				'order'          => 'DESC',
			] );

			if ( $skoleni_query->have_posts() ) :
				while ( $skoleni_query->have_posts() ) : $skoleni_query->the_post();
					get_template_part( 'template-parts/components/card-skoleni' );
				endwhile;
				wp_reset_postdata();
			else : ?>
				<p class="xevos-no-results">Zatím žádná školení.</p>
			<?php endif; ?>
		</div>

		<!-- Load more -->
		<div class="xevos-archive-bottom">
			<button class="xevos-btn xevos-btn--outline" id="load-more-skoleni">ZOBRAZIT DALŠÍ ŠKOLENÍ</button>
		</div>

	</div>
</section>

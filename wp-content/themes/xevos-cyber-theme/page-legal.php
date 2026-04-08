<?php
/**
 * Template: Legal pages (Obchodní podmínky, GDPR, Cookies).
 * Template Name: Právní stránka
 *
 * @package Xevos\CyberTheme
 */

get_header();
?>

<main id="main" class="xevos-main xevos-main--glows">
	<div class="xevos-glow-blob xevos-glow-blob--left xevos-glow-blob--lg" style="top:-400px;"></div>
	<div class="xevos-glow-blob xevos-glow-blob--top xevos-glow-blob--lg"></div>
	<div class="xevos-glow-blob xevos-glow-blob--right xevos-glow-blob--lg" style="top:800px;"></div>
	<div class="xevos-glow-blob xevos-glow-blob--left-second xevos-glow-blob--lg" style="bottom:0;"></div>

	<?php while ( have_posts() ) : the_post(); ?>
		<article class="xevos-section">
			<div class="xevos-section__container" style="max-width:1200px;">
				<h1><?php the_title(); ?></h1>

<div class="xevos-legal-content" id="legal-content">
					<?php
					$obsah = get_field('legal_obsah');
					if ( $obsah ) :
						echo wp_kses_post($obsah);
					else :
						the_content();
					endif;
					?>
				</div>
			</div>
		</article>
	<?php endwhile; ?>



<?php
get_footer();

<?php
/**
 * Template Name: Zásady ochrany osobních údajů
 * Figma node 685:85: Legal page with TOC sidebar, content area.
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
		<?php
		$nadpis = get_the_title();
		$obsah  = get_field('legal_obsah');
		?>
		<article class="xevos-section xevos-legal">
			<div class="xevos-section__container xevos-legal__container">
				<h1 class="xevos-legal__title"><?php echo esc_html($nadpis); ?></h1>

				<div class="xevos-legal-content" id="legal-content">
					<?php if ($obsah) :
						echo wp_kses_post($obsah);
					else :
						the_content();
					endif; ?>
				</div>
			</div>
		</article>
	<?php endwhile; ?>



<?php
get_footer();

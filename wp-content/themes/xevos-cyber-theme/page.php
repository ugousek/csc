<?php
/**
 * Default page template.
 *
 * @package Xevos\CyberTheme
 */

get_header();
?>

<main id="main" class="xevos-main">

	<?php while ( have_posts() ) : the_post(); ?>
		<article class="xevos-page">
			<div class="xevos-page-hero">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="xevos-page-hero__bg">
						<?php the_post_thumbnail( 'xevos-hero' ); ?>
					</div>
				<?php endif; ?>
				<div class="xevos-page-hero__content">
					<h1 class="xevos-page-hero__title"><?php the_title(); ?></h1>
				</div>
			</div>

			<div class="xevos-section">
				<div class="xevos-section__container">
					<div class="xevos-page__content">
						<?php the_content(); ?>
					</div>
				</div>
			</div>
		</article>
	<?php endwhile; ?>


<?php
get_footer();

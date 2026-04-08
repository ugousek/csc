<?php
/**
 * Default single post template.
 *
 * @package Xevos\CyberTheme
 */

get_header();
?>

<main id="main" class="xevos-main">

	<?php while ( have_posts() ) : the_post(); ?>
		<article class="xevos-section">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="xevos-aktualita-single__hero">
					<?php the_post_thumbnail( 'xevos-hero' ); ?>
				</div>
			<?php endif; ?>

			<div class="xevos-section__container" style="max-width:860px;">
				<div class="xevos-aktualita-single__meta">
					<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					<span><?php the_author(); ?></span>
				</div>

				<h1><?php the_title(); ?></h1>

				<div class="xevos-aktualita-single__body">
					<?php the_content(); ?>
				</div>
			</div>
		</article>
	<?php endwhile; ?>


<?php
get_footer();

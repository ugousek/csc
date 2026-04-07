<?php
/**
 * Main fallback template.
 *
 * @package Xevos\CyberTheme
 */

get_header();
?>

<main id="main" class="xevos-main">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'xevos-article' ); ?>>
				<h2 class="xevos-article__title">
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h2>
				<div class="xevos-article__excerpt">
					<?php the_excerpt(); ?>
				</div>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'No content found.', 'xevos-cyber' ); ?></p>
	<?php endif; ?>


<?php
get_footer();

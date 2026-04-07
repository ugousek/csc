<?php
/**
 * Single: Aktualita (Blog post).
 * Figma: Hero with category pills, full-width content, partner logos.
 *
 * @package Xevos\CyberTheme
 */

get_header();
$categories = get_the_terms( get_the_ID(), 'kategorie-aktualit' );
?>

<main id="main" class="xevos-main">
	<?php while ( have_posts() ) : the_post(); ?>

		<!-- Hero -->
		<section class="xevos-page-hero xevos-page-hero--article">
			<div class="xevos-page-hero__bg">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail( 'xevos-hero' ); ?>
				<?php endif; ?>
			</div>
			<div class="xevos-page-hero__content">
				<?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
					<div class="xevos-article-hero__cats">
						<?php foreach ( $categories as $cat ) : ?>
							<span class="xevos-filter-pill xevos-filter-pill--small"><?php echo esc_html( $cat->name ); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<h1 class="xevos-page-hero__title"><?php the_title(); ?></h1>
			</div>
		</section>

		<!-- Content -->
		<article class="xevos-section xevos-article-content">
			<div class="xevos-section__container xevos-article-content__inner">
				<div class="xevos-article-content__body">
					<?php the_content(); ?>
				</div>

				<!-- Share -->
				<div class="xevos-aktualita-single__share">
					<?php $url = urlencode( get_the_permalink() ); $title = urlencode( get_the_title() ); ?>
					<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" target="_blank" rel="noopener" aria-label="Facebook"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>
					<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $url; ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2zM4 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/></svg></a>
					<a href="https://twitter.com/intent/tweet?url=<?php echo $url; ?>&text=<?php echo $title; ?>" target="_blank" rel="noopener" aria-label="X"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
					<button class="xevos-copy-url" data-url="<?php echo esc_attr( get_the_permalink() ); ?>" aria-label="Kopírovat URL"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg></button>
				</div>

				<!-- Prev/Next -->
				<nav class="xevos-aktualita-single__nav">
					<?php $prev = get_previous_post(); $next = get_next_post(); ?>
					<div><?php if ( $prev ) : ?><a href="<?php echo esc_url( get_permalink( $prev ) ); ?>">&larr; <?php echo esc_html( wp_trim_words( $prev->post_title, 6 ) ); ?></a><?php endif; ?></div>
					<div><?php if ( $next ) : ?><a href="<?php echo esc_url( get_permalink( $next ) ); ?>"><?php echo esc_html( wp_trim_words( $next->post_title, 6 ) ); ?> &rarr;</a><?php endif; ?></div>
				</nav>
			</div>
		</article>

	<?php endwhile; ?>


<?php get_footer(); ?>

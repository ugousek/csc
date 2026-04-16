<?php

/**
 * Single: Aktualita (Blog post).
 * Figma node 595:12671: Hero with category pills, meta bar, content, share, prev/next.
 *
 * @package Xevos\CyberTheme
 */

get_header();
$categories = get_the_terms(get_the_ID(), 'kategorie-aktualit');


?>

<main id="main" class="xevos-main xevos-main--glows">
	<div class="xevos-glow-blob xevos-glow-blob--left xevos-glow-blob--lg" style="top:-400px;"></div>
	<div class="xevos-glow-blob xevos-glow-blob--top xevos-glow-blob--lg"></div>
	<div class="xevos-glow-blob xevos-glow-blob--right xevos-glow-blob--lg" style="top:800px;"></div>
	<div class="xevos-glow-blob xevos-glow-blob--left-second xevos-glow-blob--lg" style="top:2200px;"></div>
	<div class="xevos-glow-blob xevos-glow-blob--right-second xevos-glow-blob--lg" style="bottom:400px;"></div>

	<?php while (have_posts()) : the_post();
		$hero_layout = get_field('aktualita_hero_layout') ?: 'classic';
		$hero_acf    = get_field('aktualita_hero_obrazek');
		$hero_img_id = 0;
		$hero_url    = '';
		if ($hero_acf && !empty($hero_acf['ID'])) {
			$hero_img_id = (int) $hero_acf['ID'];
		} elseif (has_post_thumbnail()) {
			$hero_img_id = get_post_thumbnail_id(get_the_ID());
		}
	?>

		<?php if ( $hero_layout === 'fullwidth' ) : ?>
			<!-- ============================================================
			     Hero layout: Fullwidth — velký nadpis + tagy + intro s obrázkem
			     ============================================================ -->
			<section class="xevos-article-hero xevos-article-hero--fullwidth">
				<div class="xevos-section__container">
					<div class="xevos-article-hero__content">
						<h1 class="xevos-article-hero__title"><?php the_title(); ?></h1>
						<?php if ($categories && ! is_wp_error($categories)) : ?>
							<div class="xevos-article-hero__tags">
								<?php foreach ($categories as $cat) : ?>
									<a href="<?php echo esc_url(get_post_type_archive_link('aktualita') . '?kategorie=' . $cat->slug); ?>" class="xevos-article-hero__tag-pill"><?php echo esc_html($cat->name); ?></a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</section>

			<!-- Intro: text + obrázek side-by-side -->
			<article class="xevos-section xevos-article-content xevos-article-content--fullwidth">
				<div class="xevos-section__container xevos-article-content__inner">
					<?php if ( $hero_img_id ) : ?>
						<div class="xevos-article-intro">
							<div class="xevos-article-intro__text">
								<?php
								$obsah = get_field('aktualita_obsah');
								$intro = '';
								$rest  = '';
								if ( $obsah ) {
									$first_p_end = strpos( $obsah, '</p>' );
									if ( $first_p_end !== false ) {
										$intro = substr( $obsah, 0, $first_p_end + 4 );
										$rest  = substr( $obsah, $first_p_end + 4 );
									} else {
										$rest = $obsah;
									}
								}
								echo wp_kses_post( $intro );
								?>
							</div>
							<?php $intro_mask = get_field('aktualita_hero_maska'); ?>
							<div class="xevos-article-intro__image<?php echo $intro_mask ? '' : ' xevos-article-intro__image--no-mask'; ?>">
								<?php echo xevos_img($hero_img_id, 'full', ['alt' => get_the_title(), 'loading' => 'eager']); ?>
							</div>
						</div>
						<div class="xevos-article-content__body">
							<?php echo wp_kses_post( $rest ); ?>
						</div>
					<?php else : ?>
						<div class="xevos-article-content__body">
							<?php
							$obsah = get_field('aktualita_obsah');
							if ($obsah) :
								echo wp_kses_post($obsah);
							else :
								the_content();
							endif;
							?>
						</div>
					<?php endif; ?>

		<?php else : ?>
			<!-- ============================================================
			     Hero layout: Classic — obrázek na pozadí + nadpis přes
			     ============================================================ -->
			<section class="xevos-article-hero">
				<div class="xevos-section__container">
					<?php if ( $hero_img_id ) : ?>
						<div class="xevos-article-hero__bg">
							<?php echo xevos_img($hero_img_id, 'full', ['alt' => get_the_title()]); ?>
						</div>
					<?php endif; ?>
					<div class="xevos-article-hero__content">
						<?php if ($categories && ! is_wp_error($categories)) : ?>
							<div class="xevos-article-hero__tags xevos-aktuality-archive__filters">
								<?php foreach ($categories as $cat) : ?>
									<a href="<?php echo esc_url(get_post_type_archive_link('aktualita') . '?kategorie=' . $cat->slug); ?>" class="xevos-filter-pill"><?php echo esc_html($cat->name); ?></a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						<h1 class="xevos-article-hero__title"><?php the_title(); ?></h1>
					</div>
				</div>
			</section>

			<!-- Content -->
			<article class="xevos-section xevos-article-content">
				<div class="xevos-section__container xevos-article-content__inner">
					<div class="xevos-article-content__body">
						<?php
						$obsah = get_field('aktualita_obsah');
						if ($obsah) :
							echo wp_kses_post($obsah);
						else :
							the_content();
						endif;
						?>
		<?php endif; ?>
				</div>

				<!-- Share -->
				<div class="xevos-aktualita-single__share">
					<span class="xevos-aktualita-single__share-label">Sdílet:</span>
					<?php $url = urlencode(get_the_permalink());
					$title = urlencode(get_the_title()); ?>
					<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" target="_blank" rel="noopener" aria-label="Facebook"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
							<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
						</svg></a>
					<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $url; ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
							<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2zM4 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4z" />
						</svg></a>
					<a href="https://twitter.com/intent/tweet?url=<?php echo $url; ?>&text=<?php echo $title; ?>" target="_blank" rel="noopener" aria-label="X"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
							<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
						</svg></a>
					<button class="xevos-copy-url" data-url="<?php echo esc_attr(get_the_permalink()); ?>" aria-label="Kopírovat URL"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<rect x="9" y="9" width="13" height="13" rx="2" />
							<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
						</svg></button>
				</div>

				<!-- Prev/Next -->
				<nav class="xevos-aktualita-single__nav">
					<?php $prev = get_previous_post();
					$next = get_next_post(); ?>
					<div><?php if ($prev) : ?><a href="<?php echo esc_url(get_permalink($prev)); ?>">&larr; <?php echo esc_html(wp_trim_words($prev->post_title, 6)); ?></a><?php endif; ?></div>
					<div><?php if ($next) : ?><a href="<?php echo esc_url(get_permalink($next)); ?>"><?php echo esc_html(wp_trim_words($next->post_title, 6)); ?> &rarr;</a><?php endif; ?></div>
				</nav>
			</div>
		</article>

	<?php endwhile; ?>


	<?php get_footer(); ?>
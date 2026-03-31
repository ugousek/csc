<?php

/**
 * Component: Aktualita Card.
 *
 * @package Xevos\CyberTheme
 */
?>

<a href="<?php the_permalink(); ?>" class="xevos-card">
	<div class="xevos-card__image">
		<?php if (has_post_thumbnail()) : ?>
			<?php the_post_thumbnail('xevos-card', ['loading' => 'lazy', 'decoding' => 'async']); ?>
		<?php else :
			$fallback_imgs = [
				get_theme_file_uri('assets/img/blog/aktualita-img-1.png'),
				get_theme_file_uri('assets/img/blog/aktualita-img-2.png'),
				get_theme_file_uri('assets/img/blog/aktualita-img-3.png'),
			];
			$idx = get_the_ID() % count($fallback_imgs);
		?>
			<img src="<?php echo esc_url($fallback_imgs[$idx]); ?>" alt="<?php the_title_attribute(); ?>" width="507" height="293" loading="lazy" decoding="async">
		<?php endif; ?>
	</div>
	<div class="xevos-card__body">
		<?php
		$categories = get_the_terms(get_the_ID(), 'kategorie-aktualit');
		if ($categories && ! is_wp_error($categories)) : ?>
			<span class="xevos-card__badge"><?php echo esc_html($categories[0]->name); ?></span>
		<?php endif; ?>

		<p class="xevos-card__meta">
			<time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
		</p>

		<h3 class="xevos-card__title"><?php the_title(); ?></h3>

		<p class="xevos-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 20)); ?></p>

		<span class="xevos-card__link">
			Číst více <img src="<?php echo esc_url(get_theme_file_uri('assets/img/global/card-arrow.svg')); ?>" alt="" class="xevos-card__arrow">
		</span>
	</div>
</a>
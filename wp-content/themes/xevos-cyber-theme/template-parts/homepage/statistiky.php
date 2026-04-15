<?php

/**
 * Homepage: Statistiky – Figma nodes 849:25 + 850:141
 * Feature heading row (shield graphic + H2 + desc) + 3 stat cards grid.
 * Plně administrovatelné přes ACF.
 */

$show = get_field('statistiky_zobrazit_sekci');
if ($show === false) return;

$stats          = get_field('statistiky') ?: [];
$heading        = get_field('statistiky_heading') ?: '';
$desc           = get_field('statistiky_popis') ?: '';
$main_image     = get_field('statistiky_obrazek');
$main_img_url   = $main_image ? $main_image['url'] : get_theme_file_uri('assets/img/homepage/stat-graphic.svg');
$fallback_png   = get_theme_file_uri('assets/img/homepage/stat-graphic.png');
$shield_cislo   = get_field('statistiky_shield_cislo') ?: '';
$shield_popis   = get_field('statistiky_shield_popis') ?: '';
?>

<section class="xevos-section xevos-statistiky">
	<div class="xevos-section__container">

		<!-- Feature heading row — shield graphic left + H2 + body right -->
		<div class="xevos-statistiky__feature">
			<div class="xevos-statistiky__feature-graphic">
				<div id="shield-lottie" aria-hidden="true"></div>
				<?php if ($shield_cislo || $shield_popis) : ?>
					<div class="xevos-statistiky__shield-overlay">
						<?php if ($shield_popis) : ?>
							<span class="xevos-statistiky__shield-label"><?php echo esc_html($shield_popis); ?></span>
						<?php endif; ?>
						<?php if ($shield_cislo) : ?>
							<span class="xevos-statistiky__shield-number"><?php echo esc_html($shield_cislo); ?></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( $heading || $desc ) : ?>
				<div class="xevos-statistiky__feature-text">
					<?php if ( $heading ) : ?>
						<h2><?php echo esc_html($heading); ?></h2>
					<?php endif; ?>
					<?php if ( $desc ) : ?>
						<p><?php echo wp_kses_post(strip_tags($desc, '<strong><b><em><br>')); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<!-- Stat cards — each has shield graphic + H3 title below -->
		<div class="xevos-statistiky__grid">
			<?php foreach ($stats as $s) :
				$card_img = ! empty($s['obrazek']['url']) ? $s['obrazek']['url'] : $fallback_png;
			?>
				<div class="xevos-statistiky__card">
					<div class="xevos-statistiky__card-graphic">
						<div class="shield-lottie-card" aria-hidden="true"></div>
						<?php if (! empty($s['cislo'])) : ?>
							<span class="xevos-statistiky__card-number"><?php echo esc_html($s['cislo']); ?></span>
						<?php endif; ?>
					</div>
					<h3 class="xevos-statistiky__card-title"><?php echo esc_html($s['popis'] ?? ''); ?></h3>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
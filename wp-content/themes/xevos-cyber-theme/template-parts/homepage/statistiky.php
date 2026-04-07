<?php

/**
 * Homepage: Statistiky – Figma nodes 849:25 + 850:141
 * Feature heading row (shield graphic + H2 + desc) + 3 stat cards grid.
 * Plně administrovatelné přes ACF.
 */

$show = get_field('statistiky_zobrazit_sekci');
if ($show === false) return;

$stats = get_field('statistiky');

if (! $stats) {
	$stats = [
		['popis' => 'Firem má zranitelnosti'],
		['popis' => 'Stačí kliknout na phishingový e-mail'],
		['popis' => 'Průměrná doba do prvního kompromitování'],
	];
}

$heading    = get_field('statistiky_heading') ?: 'Počet vysokých nebo kritických zranitelností';
$desc       = get_field('statistiky_popis') ?: 'Kybernetická politika vytváří jasný rámec pro řízení bezpečnosti v organizaci. Určuje role, odpovědnosti a pravidla, podle kterých se chrání data, systémy a provoz.';
$main_image = get_field('statistiky_obrazek');
$main_img_url = $main_image ? $main_image['url'] : get_theme_file_uri('assets/img/homepage/stat-graphic.svg');
$fallback_png = get_theme_file_uri('assets/img/homepage/stat-graphic.png');
?>

<section class="xevos-section xevos-statistiky">
	<div class="xevos-section__container">

		<!-- Feature heading row — shield graphic left + H2 + body right -->
		<div class="xevos-statistiky__feature">
			<div class="xevos-statistiky__feature-graphic">
				<img src="<?php echo esc_url($main_img_url); ?>"
					alt="" loading="lazy" aria-hidden="true" />
			</div>
			<div class="xevos-statistiky__feature-text">
				<h2><?php echo esc_html($heading); ?></h2>
				<p><?php echo wp_kses_post(strip_tags($desc, '<strong><b><em><br>')); ?></p>
			</div>
		</div>

		<!-- Stat cards — each has shield graphic + H3 title below -->
		<div class="xevos-statistiky__grid">
			<?php foreach ($stats as $s) :
				$card_img = ! empty($s['obrazek']['url']) ? $s['obrazek']['url'] : $fallback_png;
			?>
				<div class="xevos-statistiky__card">
					<div class="xevos-statistiky__card-graphic">
						<img src="<?php echo esc_url($card_img); ?>"
							alt="" loading="lazy" aria-hidden="true" />
					</div>
					<h3 class="xevos-statistiky__card-title"><?php echo wp_kses_post($s['popis'] ?? ''); ?></h3>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>

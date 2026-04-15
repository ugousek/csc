<?php
/**
 * Component: Partners bar.
 * Figma: 1600px container, space-between; Swiper under 991px.
 */

$partners = xevos_get_option('partneri');

$partner_items = [];

if ($partners) :
	foreach ($partners as $p) :
		$name     = $p['nazev'] ?? '';
		$logo_url = ! empty($p['logo']['url']) ? $p['logo']['url'] : '';
		if ($logo_url) {
			$partner_items[] = ['url' => $logo_url, 'name' => $name];
		}
	endforeach;
endif;

/* Skrýt celou sekci, když nejsou nahraná žádná loga partnerů. */
if (empty($partner_items)) {
	return;
}
?>

<section class="xevos-section xevos-partners">
	<div class="xevos-section__container xevos-partners__inner">
		<div class="swiper xevos-partners__swiper" id="partners-swiper">
			<div class="swiper-wrapper xevos-partners__track">
				<?php foreach ($partner_items as $pi) : ?>
					<div class="swiper-slide xevos-partners__slide">
						<img src="<?php echo esc_url($pi['url']); ?>" alt="<?php echo esc_attr($pi['name']); ?>">
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

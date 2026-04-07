<?php
/**
 * Component: Partners bar.
 * Figma: 1600px container, space-between; Swiper under 991px.
 */

$partners = xevos_get_option('partneri');

$fallback_logos = [
	'cisco'    => 'cisco.png',
	'eset'     => 'eset.png',
	'apple'    => 'apple.png',
	'paloalto' => 'paloalto.png',
	'palo alto' => 'paloalto.png',
	'pentera'  => 'pentera.png',
];

$partner_items = [];

if ($partners) :
	foreach ($partners as $p) :
		$name = $p['nazev'] ?? '';
		$logo_url = '';
		if (! empty($p['logo']['url'])) {
			$logo_url = $p['logo']['url'];
		} else {
			$key = strtolower(trim($name));
			foreach ($fallback_logos as $match => $file) {
				if (str_contains($key, $match)) {
					$logo_url = get_theme_file_uri('assets/img/global/partners/' . $file);
					break;
				}
			}
		}
		if ($logo_url) {
			$partner_items[] = ['url' => $logo_url, 'name' => $name];
		}
	endforeach;
endif;

if (empty($partner_items)) :
	$partner_items = [
		['url' => get_theme_file_uri('assets/img/global/partners/cisco.png'), 'name' => 'Cisco'],
		['url' => get_theme_file_uri('assets/img/global/partners/eset.png'), 'name' => 'ESET Gold Partner'],
		['url' => get_theme_file_uri('assets/img/global/partners/apple.png'), 'name' => 'Apple Technical Partner'],
		['url' => get_theme_file_uri('assets/img/global/partners/paloalto.png'), 'name' => 'Palo Alto Networks'],
		['url' => get_theme_file_uri('assets/img/global/partners/pentera.png'), 'name' => 'Pentera'],
	];
endif;
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

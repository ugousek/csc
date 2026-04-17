<?php

/**
 * Kybernetické testování — slider component.
 * Each page has its own ACF fields — HP and /kyberneticke-testovani/ are independent.
 */

$show = get_field('kyber_test_zobrazit_sekci');
if ($show === false) return;

$heading      = get_field('kyber_test_heading') ?: '';
$heading_url  = get_field('kyber_test_heading_url');
$desc         = get_field('kyber_test_text') ?: '';
$default_cta_text  = '';
$default_cta_url   = '/kyberneticke-testovani/';
$default_image_url = get_theme_file_uri('assets/img/homepage/kyber-testovani.png');

/* Slides — from current page's ACF */
$slides = get_field('kyber_test_slidy') ?: [];

/* Skrýt celou sekci, pokud nejsou nakonfigurované žádné slidy. */
if (empty($slides)) {
	return;
}
?>

<section class="xevos-section xevos-kyber-test-section">
	<div class="xevos-section__container">

		<!-- Centered heading (only on homepage) -->
		<?php if (is_front_page() && ($heading || $desc)) : ?>
			<div class="xevos-kyber-test__header">
				<?php if ($heading) : ?>
					<h2><?php if ($heading_url) : ?><a href="<?php echo esc_url($heading_url); ?>"><?php echo esc_html($heading); ?></a><?php else : echo esc_html($heading);
																																	endif; ?></h2>
				<?php endif; ?>
				<?php if ($desc) : ?>
					<div class="xevos-kyber-test__desc"><?php echo wp_kses_post($desc); ?></div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<!-- Image + slider panel -->
		<div class="xevos-kyber-test__body">
			<!-- Image area (mění se per slide) -->
			<div class="xevos-kyber-test__visual">
				<div class="xevos-kyber-test__glow" aria-hidden="true"></div>
				<?php
				/* Per-slide obrázky — JS přepíná src + srcset + sizes + masku */
				$slide_images = [];
				foreach ($slides as $s) {
					$img_id = ! empty($s['obrazek']['ID']) ? (int) $s['obrazek']['ID'] : 0;
					if ($img_id) {
						$src    = wp_get_attachment_image_url($img_id, 'full');
						$srcset = wp_get_attachment_image_srcset($img_id, 'full') ?: '';
						$sizes  = wp_get_attachment_image_sizes($img_id, 'full') ?: '';
					} else {
						$src    = ! empty($s['obrazek']['url']) ? $s['obrazek']['url'] : $default_image_url;
						$srcset = '';
						$sizes  = '';
					}
					// Maska — default zapnuta pokud pole chybí, jinak podle ACF hodnoty.
					$mask = array_key_exists('maska', $s) ? ! empty($s['maska']) : true;
					$slide_images[] = ['src' => $src, 'srcset' => $srcset, 'sizes' => $sizes, 'mask' => (bool) $mask];
				}
				$first = $slide_images[0] ?? ['src' => $default_image_url, 'srcset' => '', 'sizes' => '', 'mask' => true];
				?>
				<img src="<?php echo esc_url($first['src']); ?>"
					<?php if ($first['srcset']) : ?>srcset="<?php echo esc_attr($first['srcset']); ?>" <?php endif; ?>
					<?php if ($first['sizes']) : ?>sizes="<?php echo esc_attr($first['sizes']); ?>" <?php endif; ?>
					alt="Kybernetické testování"
					id="kyber-test-main-img"
					class="<?php echo $first['mask'] ? '' : 'kyber-test-main-img--no-mask'; ?>"
					loading="lazy" />
				<script type="application/json" id="kyber-test-images">
					<?php echo wp_json_encode($slide_images); ?>
				</script>
			</div>

			<!-- Slider panel (text se mění, 6 snímků) -->
			<div class="xevos-kyber-test__panel swiper" id="kyber-test-panel-swiper">
				<div class="swiper-wrapper">
					<?php foreach ($slides as $slide) :
						$items = is_array($slide['items'] ?? null) ? $slide['items'] : [];
						if (empty($items) && ! empty($slide['polozky'])) {
							$items = array_map(fn($i) => $i['bod'] ?? '', $slide['polozky']);
						}
					?>
						<div class="swiper-slide">
							<h3><?php echo esc_html($slide['title'] ?? $slide['nazev'] ?? ''); ?></h3>
							<p class="xevos-kyber-test__panel-desc"><?php echo wp_kses_post(strip_tags($slide['desc'] ?? $slide['popis'] ?? '', '<strong><b><em><br>')); ?></p>

							<?php if ($items) : ?>
								<ul class="xevos-kyber-test__checklist">
									<?php foreach ($items as $item) : ?>
										<li>
											<span class="xevos-kyber-test__check-circle" aria-hidden="true">
												<svg xmlns="http://www.w3.org/2000/svg" width="23" height="17" viewBox="0 0 23 17" fill="none">
													<g filter="url(#filter0_i_231_13676)">
														<path d="M22.6 2.8L8.5 16.9L0 8.4L2.8 5.6L8.5 11.3L19.8 0L22.6 2.8Z" fill="#00BBFF" />
													</g>
													<defs>
														<filter id="filter0_i_231_13676" x="0" y="0" width="22.6001" height="20.4" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
															<feFlood flood-opacity="0" result="BackgroundImageFix" />
															<feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape" />
															<feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
															<feOffset dy="4" />
															<feGaussianBlur stdDeviation="1.75" />
															<feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1" />
															<feColorMatrix type="matrix" values="0 0 0 0 0.4298 0 0 0 0 0.82894 0 0 0 0 1 0 0 0 1 0" />
															<feBlend mode="normal" in2="shape" result="effect1_innerShadow_231_13676" />
														</filter>
													</defs>
												</svg>
											</span>
											<?php echo esc_html($item); ?>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>

							<?php
							$slide_cta_text = $slide['cta_text'] ?? $default_cta_text;
							$slide_cta_url  = $slide['cta_url'] ?? $default_cta_url;
							?>
							<a href="<?php echo esc_url($slide_cta_url); ?>" class="xevos-btn xevos-btn--primary">
								<span class="xevos-btn__arrow"></span>
								<?php echo esc_html(strtoupper($slide_cta_text)); ?>
							</a>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<!-- Slider pagination (1 2 3 4 5 6) -->
		<div class="xevos-kyber-test__pagination" id="kyber-test-pagination"></div>

	</div>
</section>
<?php

/**
 * Kybernetické testování — slider component.
 * Each page has its own ACF fields — HP and /kyberneticke-testovani/ are independent.
 */

$show = get_field('kyber_test_zobrazit_sekci');
if ($show === false) return;

$heading      = get_field('kyber_test_heading') ?: 'Kybernetické testování';
$heading_url  = get_field('kyber_test_heading_url');
$desc     = get_field('kyber_test_text') ?: 'Ověření bezpečnosti systémů prostřednictvím řízených útoků a technických analýz, které odhalují reálné zranitelnosti.';
$default_cta_text = 'Chci test';
$default_cta_url  = '/kyberneticke-testovani/';
$default_image_url = get_theme_file_uri('assets/img/homepage/kyber-testovani.png');

/* Slides — from current page's ACF */
$slides = get_field('kyber_test_slidy');
if (! $slides) {
	$slides = [
		[
			'title' => 'Penetrační testy',
			'desc'  => 'Simulace útoku na vaše systémy s cílem identifikovat slabá místa a navrhnout účinná opatření pro posílení vaší kybernetické bezpečnosti.',
			'items' => ['Penetrační testování', 'Testování aplikací a webů', 'Kyber odolnost zaměstnanců', 'Testování WiFi sítí'],
		],
		[
			'title' => 'Testování webových aplikací',
			'desc'  => 'Komplexní analýza webových aplikací zaměřená na identifikaci zranitelností v kódu, konfiguraci a autentizaci.',
			'items' => ['OWASP Top 10', 'SQL Injection', 'Cross-site Scripting', 'Autentizační testy'],
		],
		[
			'title' => 'Sociální inženýrství',
			'desc'  => 'Testování odolnosti zaměstnanců vůči phishingovým útokům, podvodným e-mailům a manipulativním technikám.',
			'items' => ['Phishing kampaně', 'Vishing testy', 'USB drop testy', 'Awareness reporting'],
		],
		[
			'title' => 'Testování infrastruktury',
			'desc'  => 'Audit síťové infrastruktury, serverů a cloudového prostředí pro odhalení konfiguračních chyb a zranitelností.',
			'items' => ['Síťový audit', 'Cloud security', 'Active Directory', 'Firewall review'],
		],
		[
			'title' => 'Red Team operace',
			'desc'  => 'Realistická simulace pokročilého útočníka kombinující technické, fyzické a sociální vektory útoku.',
			'items' => ['Cílený útok', 'Lateral movement', 'Data exfiltrace', 'Fyzický průnik'],
		],
		[
			'title' => 'Mobilní aplikace',
			'desc'  => 'Bezpečnostní testování mobilních aplikací pro iOS a Android včetně analýzy API komunikace.',
			'items' => ['iOS testování', 'Android testování', 'API bezpečnost', 'Datové úniky'],
		],
	];
}
?>

<section class="xevos-section xevos-kyber-test-section">
	<div class="xevos-section__container">

		<!-- Centered heading (only on homepage) -->
		<?php if (is_front_page()) : ?>
			<div class="xevos-kyber-test__header">
				<h2><?php if ($heading_url) : ?><a href="<?php echo esc_url($heading_url); ?>"><?php echo esc_html($heading); ?></a><?php else : echo esc_html($heading); endif; ?></h2>
				<div class="xevos-kyber-test__desc"><?php echo wp_kses_post($desc); ?></div>
			</div>
		<?php endif; ?>

		<!-- Image + slider panel -->
		<div class="xevos-kyber-test__body">
			<!-- Image area (mění se per slide) -->
			<div class="xevos-kyber-test__visual">
				<div class="xevos-kyber-test__glow" aria-hidden="true"></div>
				<img src="<?php echo esc_url(get_theme_file_uri('assets/img/homepage/blesk.png')); ?>"
					alt="" class="xevos-kyber-test__blesk" aria-hidden="true" loading="lazy" />
				<?php
				/* Per-slide obrázky (JS přepíná src) */
				$slide_images = [];
				foreach ($slides as $s) {
					$slide_images[] = ! empty($s['obrazek']['url']) ? $s['obrazek']['url'] : $default_image_url;
				}
				$first_image = $slide_images[0] ?? $default_image_url;
				?>
				<img src="<?php echo esc_url($first_image); ?>"
					alt="Kybernetické testování"
					id="kyber-test-main-img"
					loading="lazy" />
				<?php
				?>
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
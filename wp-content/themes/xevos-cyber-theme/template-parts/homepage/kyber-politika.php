<?php
/**
 * Homepage: Kybernetická politika.
 * Figma: heading centrovaný, pod ním flex — TEXT vlevo, OBRÁZEK vpravo.
 */

$show = get_field('kyber_politika_zobrazit_sekci');
if ($show === false) return;

$heading = get_field('kyber_politika_heading') ?: 'Kybernetická politika';
$desc    = get_field('kyber_politika_popis') ?: 'Soubor pravidel a postupů, které určují, jak organizace řídí bezpečnost, přístup, rizika a provozní standardy.';
$text    = get_field('kyber_politika_text') ?: 'Kybernetická politika vytváří jasný rámec pro řízení bezpečnosti v organizaci. Určuje role, odpovědnosti a postupy pro ochranu aktiv, řešení incidentů a splnění regulatorních požadavků.';
$image   = get_field('kyber_politika_obrazek');
$seznam  = get_field('kyber_politika_seznam');

if (! $seznam) {
	$seznam = [
		['bod' => 'NIS 2 compliance'],
		['bod' => 'DORA compliance'],
		['bod' => 'příprava na ISO certifikace'],
	];
}

$image_url = $image ? $image['url'] : get_theme_file_uri('assets/img/homepage/kyber-politika.png');
?>

<section class="xevos-section xevos-kyber-politika">
	<div class="xevos-section__container">

		<!-- Centered heading -->
		<div class="xevos-kyber-politika__header">
			<h2><?php echo esc_html($heading); ?></h2>
			<p class="xevos-kyber-politika__desc"><?php echo esc_html($desc); ?></p>
		</div>

		<!-- Flex: TEXT vlevo + OBRÁZEK vpravo -->
		<div class="xevos-kyber-politika__body">
			<!-- Panel vlevo -->
			<div class="xevos-kyber-politika__panel">
				<h3>Poskytujeme:</h3>
				<div class="xevos-kyber-politika__panel-desc"><?php echo wp_kses_post($text); ?></div>

				<ul class="xevos-kyber-politika__checklist">
					<?php foreach ($seznam as $item) : ?>
						<li>
							<span class="xevos-kyber-test__check-circle" aria-hidden="true">
								<svg xmlns="http://www.w3.org/2000/svg" width="23" height="17" viewBox="0 0 23 17" fill="none">
									<g filter="url(#filter0_i_kp_<?php echo esc_attr(sanitize_title($item['bod'] ?? '')); ?>)">
										<path d="M22.6 2.8L8.5 16.9L0 8.4L2.8 5.6L8.5 11.3L19.8 0L22.6 2.8Z" fill="#00BBFF"/>
									</g>
									<defs>
										<filter id="filter0_i_kp_<?php echo esc_attr(sanitize_title($item['bod'] ?? '')); ?>" x="0" y="0" width="22.6001" height="20.4" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
											<feFlood flood-opacity="0" result="BackgroundImageFix"/>
											<feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
											<feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
											<feOffset dy="4"/>
											<feGaussianBlur stdDeviation="1.75"/>
											<feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1"/>
											<feColorMatrix type="matrix" values="0 0 0 0 0.4298 0 0 0 0 0.82894 0 0 0 0 1 0 0 0 1 0"/>
											<feBlend mode="normal" in2="shape" result="effect1_innerShadow"/>
										</filter>
									</defs>
								</svg>
							</span>
							<?php echo esc_html($item['bod'] ?? ''); ?>
						</li>
					<?php endforeach; ?>
				</ul>

				<a href="<?php echo esc_url(home_url('/nis2/')); ?>" class="xevos-btn xevos-btn--primary">
					<span class="xevos-btn__arrow"></span>
					VYŘEŠIT NIS2
				</a>
			</div>

			<!-- Obrázek vpravo -->
			<div class="xevos-kyber-politika__visual">
				<div class="xevos-kyber-politika__glow" aria-hidden="true"></div>
				<img src="<?php echo esc_url($image_url); ?>"
				     alt="Kybernetická politika"
				     loading="lazy" />
			</div>
		</div>

	</div>
</section>

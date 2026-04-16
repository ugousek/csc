<?php
/**
 * Homepage: Kybernetická politika.
 * Figma: heading centrovaný, pod ním flex — TEXT vlevo, OBRÁZEK vpravo.
 */

$show = get_field('kyber_politika_zobrazit_sekci');
if ($show === false) return;

$heading      = get_field('kyber_politika_heading') ?: '';
$desc         = get_field('kyber_politika_popis') ?: '';
$panel_nadpis = get_field('kyber_politika_panel_nadpis') ?: '';
$text         = get_field('kyber_politika_text') ?: '';
$cta_text     = get_field('kyber_politika_cta_text') ?: '';
$cta_url      = get_field('kyber_politika_cta_url') ?: home_url('/nis2/');
$image        = get_field('kyber_politika_obrazek');
$seznam       = get_field('kyber_politika_seznam') ?: [];

$image_id  = $image ? (int) ($image['ID'] ?? 0) : 0;

/* Skrýt celou sekci, pokud není vyplněný žádný text ani seznam. */
if ( ! $heading && ! $desc && ! $panel_nadpis && ! $text && empty( $seznam ) && ! $image_id ) {
	return;
}
?>

<section class="xevos-section xevos-kyber-politika">
	<div class="xevos-section__container">

		<?php if ( $heading || $desc ) : ?>
			<!-- Centered heading -->
			<div class="xevos-kyber-politika__header">
				<?php if ( $heading ) : ?>
					<h2><?php echo esc_html($heading); ?></h2>
				<?php endif; ?>
				<?php if ( $desc ) : ?>
					<p class="xevos-kyber-politika__desc xevos-kyber-politika__desc--mixed"><?php echo wp_kses_post(strip_tags($desc, '<strong><b><em><br>')); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<!-- Flex: TEXT vlevo + OBRÁZEK vpravo -->
		<div class="xevos-kyber-politika__body">
			<!-- Panel vlevo -->
			<div class="xevos-kyber-politika__panel">
				<?php if ( $panel_nadpis ) : ?>
					<h3><?php echo esc_html($panel_nadpis); ?></h3>
				<?php endif; ?>
				<?php if ( $text ) : ?>
					<p class="xevos-kyber-politika__panel-desc"><?php echo wp_kses_post(strip_tags($text, '<strong><b><em><br>')); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $seznam ) ) : ?>
					<ul class="xevos-kyber-politika__checklist">
						<?php foreach ($seznam as $item) : ?>
							<?php if ( empty( $item['bod'] ) ) continue; ?>
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
				<?php endif; ?>

				<?php if ( $cta_text ) : ?>
					<a href="<?php echo esc_url($cta_url); ?>" class="xevos-btn xevos-btn--primary">
						<span class="xevos-btn__arrow"></span>
						<?php echo esc_html($cta_text); ?>
					</a>
				<?php endif; ?>
			</div>

			<?php if ( $image_id ) : ?>
				<!-- Obrázek vpravo -->
				<div class="xevos-kyber-politika__visual">
					<div class="xevos-kyber-politika__glow" aria-hidden="true"></div>
					<?php echo xevos_img($image_id, 'full', ['alt' => $heading ?: 'Kybernetická politika', 'loading' => 'lazy']); ?>
				</div>
			<?php endif; ?>
		</div>

	</div>
</section>

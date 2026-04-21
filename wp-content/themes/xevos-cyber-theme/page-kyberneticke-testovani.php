<?php

/**
 * Template Name: Kybernetické testování
 *
 * Sections (matching ACF tab order):
 * 1. Hero
 * 2. Kyber test slider
 * 3. Postup testování
 * 4. Co získáte / Pro koho
 * 5. Penetrační testy
 * 6. Kyber politika
 * 7. CTA box
 * 8. Aktuality, Recenze, Formulář
 *
 * @package Xevos\CyberTheme
 */

get_header();
$telefon = xevos_get_option('telefon');
$email   = xevos_get_option('email');
$adresa  = xevos_get_option('adresa', 'Mostárenská 1156/38, 703 00 Ostrava');
?>

<main id="main" class="xevos-main xevos-main--glows">

	<!-- Glow blobs – absolute positioned, z-index:0, sections are z-index:1 -->
	<div class="xevos-glow-blob xevos-glow-blob--left xevos-glow-blob--lg" style="top:-400px;"></div>
	<div class="xevos-glow-blob xevos-glow-blob--top xevos-glow-blob--lg"></div>
	<div class="xevos-glow-blob xevos-glow-blob--right xevos-glow-blob--lg" style="top:600px;"></div>
	<div class="xevos-glow-blob xevos-glow-blob--left-second xevos-glow-blob--lg" style="top:2200px;"></div>
	<div class="xevos-glow-blob xevos-glow-blob--right-second xevos-glow-blob--lg" style="top:3800px;"></div>

	<!-- 1. Hero -->
	<?php get_template_part('template-parts/kyber-testovani/hero'); ?>

	<!-- 2. Kyber test slider -->
	<?php if (get_field('kt_slider_zobrazit') !== false) : ?>
		<?php get_template_part('template-parts/components/kyber-testovani'); ?>
	<?php endif; ?>

		<!-- 3. Postup testování -->
		<?php
		$postup_heading = get_field('kt_postup_heading') ?: '';
		$postup_text    = get_field('kt_postup_text') ?: '';
		$kroky          = get_field('kt_postup_kroky') ?: [];
		?>
		<?php if ( get_field('kt_postup_zobrazit') !== false && ( $postup_heading || $postup_text || ! empty( $kroky ) ) ) : ?>
			<section class="xevos-section xevos-postup-section">
				<div class="xevos-section__container">
					<?php if ( $postup_heading || $postup_text ) : ?>
						<div class="xevos-hp-recenze__header">
							<?php if ( $postup_heading ) : ?>
								<h2><?php echo esc_html($postup_heading); ?></h2>
							<?php endif; ?>
							<?php if ( $postup_text ) : ?>
								<p><?php echo wp_kses_post($postup_text); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $kroky ) ) : ?>
						<!-- Numbered circles row -->
						<div class="xevos-postup-circles">
							<?php foreach ($kroky as $i => $k) : ?>
								<?php if ($i > 0) : ?><span class="xevos-postup-circles__line"></span><?php endif; ?>
								<span class="xevos-postup-step__num"><?php echo esc_html($i + 1); ?>.</span>
							<?php endforeach; ?>
						</div>

						<!-- Descriptions grid (3 left + 2 right) -->
						<div class="xevos-postup-descs">
							<?php foreach ($kroky as $i => $k) : ?>
								<div class="xevos-postup-desc">
									<h4 class="xevos-postup-step__title"><?php echo esc_html(($i + 1) . '. ' . ($k['nazev'] ?? '')); ?></h4>
									<p class="xevos-postup-step__text"><?php echo wp_kses_post($k['popis'] ?? ''); ?></p>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>

		<!-- 4. Co získáte / Pro koho -->
		<?php
		$ziskate_heading   = get_field('kt_ziskate_heading') ?: '';
		$ziskate_text      = get_field('kt_ziskate_text') ?: '';
		$ziskate_seznam    = get_field('kt_ziskate_seznam') ?: [];
		$prokoho_heading   = get_field('kt_prokoho_heading') ?: '';
		$prokoho_text      = get_field('kt_prokoho_text') ?: '';
		$prokoho_seznam    = get_field('kt_prokoho_seznam') ?: [];
		$benefity_cta_text = get_field('kt_benefity_cta_text') ?: '';
		$benefity_cta_url  = get_field('kt_benefity_cta_url') ?: '#formular';
		?>
		<?php
		$has_ziskate  = $ziskate_heading || $ziskate_text || ! empty( $ziskate_seznam );
		$has_prokoho  = $prokoho_heading || $prokoho_text || ! empty( $prokoho_seznam );
		$show_benefity = get_field('kt_benefity_zobrazit') !== false && ( $has_ziskate || $has_prokoho );
		?>
		<?php if ( $show_benefity ) : ?>
			<section class="xevos-section xevos-benefity-section">
				<div class="xevos-section__container">
					<div class="xevos-two-col">
						<?php if ( $has_ziskate ) : ?>
						<div>
							<?php if ( $ziskate_heading ) : ?>
								<h3><?php echo esc_html($ziskate_heading); ?></h3>
							<?php endif; ?>
							<?php if ( $ziskate_text ) : ?>
								<div class="xevos-benefity-section__desc"><?php echo wp_kses_post($ziskate_text); ?></div>
							<?php endif; ?>
							<?php if ( ! empty( $ziskate_seznam ) ) : ?>
							<ul class="xevos-kyber-politika__checklist">
								<?php foreach ($ziskate_seznam as $item) : ?>
									<?php if ( empty( $item['bod'] ) ) continue; ?>
									<li>
										<span class="xevos-kyber-test__check-circle" aria-hidden="true">
											<svg xmlns="http://www.w3.org/2000/svg" width="23" height="17" viewBox="0 0 23 17" fill="none">
												<g filter="url(#filter_ziskate_<?php echo esc_attr(sanitize_title($item['bod'] ?? '')); ?>)">
													<path d="M22.6 2.8L8.5 16.9L0 8.4L2.8 5.6L8.5 11.3L19.8 0L22.6 2.8Z" fill="#00BBFF" />
												</g>
												<defs>
													<filter id="filter_ziskate_<?php echo esc_attr(sanitize_title($item['bod'] ?? '')); ?>" x="0" y="0" width="22.6001" height="20.4" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
														<feFlood flood-opacity="0" result="BackgroundImageFix" />
														<feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape" />
														<feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
														<feOffset dy="4" />
														<feGaussianBlur stdDeviation="1.75" />
														<feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1" />
														<feColorMatrix type="matrix" values="0 0 0 0 0.4298 0 0 0 0 0.82894 0 0 0 0 1 0 0 0 1 0" />
														<feBlend mode="normal" in2="shape" result="effect1_innerShadow" />
													</filter>
												</defs>
											</svg>
										</span>
										<?php echo esc_html($item['bod'] ?? ''); ?>
									</li>
								<?php endforeach; ?>
							</ul>
							<?php endif; ?>
						</div>
						<?php endif; ?>
						<?php if ( $has_prokoho ) : ?>
						<div>
							<?php if ( $prokoho_heading ) : ?>
								<h3><?php echo esc_html($prokoho_heading); ?></h3>
							<?php endif; ?>
							<?php if ( $prokoho_text ) : ?>
								<div class="xevos-benefity-section__desc"><?php echo wp_kses_post($prokoho_text); ?></div>
							<?php endif; ?>
							<?php if ( ! empty( $prokoho_seznam ) ) : ?>
							<ul class="xevos-kyber-politika__checklist">
								<?php foreach ($prokoho_seznam as $item) : ?>
									<?php if ( empty( $item['bod'] ) ) continue; ?>
									<li>
										<span class="xevos-kyber-test__check-circle" aria-hidden="true">
											<svg xmlns="http://www.w3.org/2000/svg" width="23" height="17" viewBox="0 0 23 17" fill="none">
												<g filter="url(#filter_prokoho_<?php echo esc_attr(sanitize_title($item['bod'] ?? '')); ?>)">
													<path d="M22.6 2.8L8.5 16.9L0 8.4L2.8 5.6L8.5 11.3L19.8 0L22.6 2.8Z" fill="#00BBFF" />
												</g>
												<defs>
													<filter id="filter_prokoho_<?php echo esc_attr(sanitize_title($item['bod'] ?? '')); ?>" x="0" y="0" width="22.6001" height="20.4" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
														<feFlood flood-opacity="0" result="BackgroundImageFix" />
														<feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape" />
														<feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
														<feOffset dy="4" />
														<feGaussianBlur stdDeviation="1.75" />
														<feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1" />
														<feColorMatrix type="matrix" values="0 0 0 0 0.4298 0 0 0 0 0.82894 0 0 0 0 1 0 0 0 1 0" />
														<feBlend mode="normal" in2="shape" result="effect1_innerShadow" />
													</filter>
												</defs>
											</svg>
										</span>
										<?php echo esc_html($item['bod'] ?? ''); ?>
									</li>
								<?php endforeach; ?>
							</ul>
							<?php endif; ?>
						</div>
						<?php endif; ?>
					</div>
					<?php if ( $benefity_cta_text ) : ?>
						<div class="xevos-benefity-section__cta">
							<a href="<?php echo esc_url($benefity_cta_url); ?>" class="xevos-btn xevos-btn--primary">
								<span class="xevos-btn__arrow"><svg width="18" height="18" viewBox="0 0 20 20" fill="none">
										<path d="M5 15L15 5M15 5H7M15 5v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
									</svg></span>
								<?php echo esc_html($benefity_cta_text); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>

	<!-- 5. Banner -->
	<?php
	$banner_heading  = get_field('kt_banner_heading') ?: '';
	$banner_text     = get_field('kt_banner_text') ?: '';
	$banner_btn_text = get_field('kt_banner_btn_text') ?: '';
	$banner_btn_url  = get_field('kt_banner_btn_url') ?: '#formular';
	$banner_image    = get_field('kt_banner_image');
	$banner_img_id   = $banner_image ? (int) ($banner_image['ID'] ?? 0) : 0;
	$show_banner     = get_field('kt_banner_zobrazit') !== false && ( $banner_heading || $banner_text || $banner_btn_text || $banner_img_id );
	?>
	<?php if ( $show_banner ) : ?>
		<section class="xevos-section xevos-kt-banner">
			<div class="xevos-section__container">
				<div class="xevos-kt-banner__inner">
					<?php if ( $banner_img_id ) :
						echo xevos_img($banner_img_id, 'full', ['class' => 'xevos-kt-banner__bg', 'loading' => 'lazy', 'alt' => '']);
					endif; ?>
					<div class="xevos-kt-banner__content">
						<?php if ( $banner_heading ) : ?>
							<h2><?php echo wp_kses( $banner_heading, [ 'strong' => [], 'b' => [], 'em' => [], 'br' => [] ] ); ?></h2>
						<?php endif; ?>
						<?php if ( $banner_text ) : ?>
							<p><?php echo wp_kses( strip_tags( $banner_text, '<strong><b><em><a><br>' ), [ 'strong' => [], 'b' => [], 'em' => [], 'a' => [ 'href' => [], 'target' => [], 'rel' => [] ], 'br' => [] ] ); ?></p>
						<?php endif; ?>
						<?php if ( $banner_btn_text ) : ?>
							<a href="<?php echo esc_url($banner_btn_url); ?>" class="xevos-btn xevos-btn--primary">
								<span class="xevos-btn__arrow"><svg width="18" height="18" viewBox="0 0 20 20" fill="none">
										<path d="M5 15L15 5M15 5H7M15 5v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
									</svg></span>
								<?php echo esc_html($banner_btn_text); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<!-- 6. Aktuality -->
	<?php if (get_field('kt_aktuality_zobrazit') !== false) : ?>
		<?php get_template_part('template-parts/components/aktuality'); ?>
	<?php endif; ?>

	<!-- 7. Recenze -->
	<?php if (get_field('kt_recenze_zobrazit') !== false) : ?>
		<?php get_template_part('template-parts/components/recenze'); ?>
	<?php endif; ?>

	<!-- 8. Formulář -->
	<?php if (get_field('kt_formular_zobrazit') !== false) : ?>
		<?php
		$form_heading = get_field('kt_formular_heading') ?: '';
		$form_text    = get_field('kt_formular_text') ?: '';
		?>
		<section class="xevos-section" id="formular">
			<div class="xevos-section__container">
				<?php if ( $form_heading || $form_text ) : ?>
					<div class="xevos-hp-recenze__header">
						<?php if ( $form_heading ) : ?>
							<h2><?php echo esc_html($form_heading); ?></h2>
						<?php endif; ?>
						<?php if ( $form_text ) : ?>
							<p><?php echo wp_kses_post($form_text); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php get_template_part('template-parts/components/inquiry-form', null, ['prefix' => 'kt']); ?>
			</div>
		</section>
	<?php endif; ?>



<?php get_footer(); ?>
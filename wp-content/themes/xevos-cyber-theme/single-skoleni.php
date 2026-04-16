<?php

/**
 * Single: Školení.
 * Figma node 319-56: Hero + price, pro koho, termíny, lektoři carousel,
 *        harmonogram, osnova, co si odnesete, kde parkovat, recenze, objednávkový formulář.
 *
 * @package Xevos\CyberTheme
 */

get_header();

while (have_posts()) : the_post();

	$popis       = get_field('popis');
	$cena        = get_field('cena');
	$cena_s_dph  = get_field('cena_s_dph');
	$typ         = get_field('typ');
	// Lektoři: preferujeme výběr z databáze (lektori_db), fallback = inline repeater.
	$lektori_db_raw = get_field('lektori_db');
	if ( ! empty( $lektori_db_raw ) && is_array( $lektori_db_raw ) ) {
		$lektori = array_map( function( $post ) {
			$foto = get_field( 'lektor_foto', $post->ID );
			return [
				'jmeno'  => get_field( 'lektor_jmeno', $post->ID ) ?: $post->post_title,
				'pozice' => get_field( 'lektor_pozice', $post->ID ) ?: '',
				'foto'   => $foto ?: '',
				'bio'    => get_field( 'lektor_bio', $post->ID ) ?: '',
			];
		}, $lektori_db_raw );
	} else {
		$lektori = get_field('lektori');
	}
	$harmonogram = get_field('harmonogram');
	$osnova      = get_field('osnova');
	$terminy     = get_field('terminy');
	$formular    = get_field('objednavkovy_formular');
	$telefon = xevos_get_option('telefon');
	$email   = xevos_get_option('email');
	$adresa  = xevos_get_option('adresa', 'Mostárenská 1156/38, 703 00 Ostrava');
?>

	<main id="main" class="xevos-main xevos-main--glows xevos-skoleni-detail">
		<div class="xevos-skoleni-bg-bottom" aria-hidden="true"></div>
		<div class="xevos-glow-blob xevos-glow-blob--left xevos-glow-blob--lg" style="top:-400px;"></div>
		<div class="xevos-glow-blob xevos-glow-blob--top xevos-glow-blob--lg"></div>
		<div class="xevos-glow-blob xevos-glow-blob--right xevos-glow-blob--lg" style="top:800px;"></div>
		<div class="xevos-glow-blob xevos-glow-blob--left-second xevos-glow-blob--lg" style="bottom:0;"></div>

		<!-- 1. Hero -->
		<?php
		$hero_acf = get_field('hero_obrazek');
		$hero_img_id  = 0;
		$hero_img_url = '';
		if ($hero_acf && !empty($hero_acf['ID'])) {
			$hero_img_id = (int) $hero_acf['ID'];
		} elseif (has_post_thumbnail()) {
			$hero_img_id = get_post_thumbnail_id(get_the_ID());
		} else {
			$hero_img_url = get_theme_file_uri('assets/img/detail-skoleni/hero-shield.png');
		}

		$typ_prihlaseni = get_field('typ_prihlaseni') ?: 'platba';
		$is_free = in_array($typ_prihlaseni, ['zdarma', 'pozvanka'], true);

		if ($typ_prihlaseni === 'pozvanka') {
			$cta_label = 'Požádat o pozvánku';
		} elseif ($typ_prihlaseni === 'zdarma') {
			$cta_label = 'Registrovat se zdarma';
		} elseif ($cena_s_dph) {
			$cta_label = 'Cena: ' . number_format((float) $cena_s_dph, 0, ',', ' ') . ' Kč';
		} else {
			$cta_label = 'Objednat';
		}

		$hero_nadpis = get_field('hero_nadpis') ?: get_the_title();
		$hero_popis  = get_field('hero_popis');

		get_template_part('template-parts/components/hero-page', null, [
			'heading'     => $hero_nadpis,
			'description' => $hero_popis ?: '',
			'cta_text'    => $cta_label,
			'cta_url'     => '#objednavka',
			'image_id'    => $hero_img_id,
			'image_url'   => $hero_img_url,
			'css_class'   => 'xevos-skoleni-hero',
			'loading'     => 'eager',
			'image_mask'  => !in_array( get_field('hero_maska'), [ false, 0, '0' ], true ),
		]);
		?>

		<!-- 2. Pro koho -->
		<?php
		$pro_koho_nadpis = get_field('pro_koho_nadpis') ?: '';
		$pro_koho_text   = get_field('pro_koho_text');
		if ($pro_koho_text) : ?>
			<section class="xevos-section xevos-skoleni-pro-koho">
				<div class="xevos-section__container">
					<?php if ( $pro_koho_nadpis ) : ?>
						<h2><?php echo esc_html($pro_koho_nadpis); ?></h2>
					<?php endif; ?>
					<div class="xevos-article-content__body"><?php echo wp_kses_post($pro_koho_text); ?></div>
				</div>
			</section>
		<?php endif; ?>

		<!-- 3. Termíny -->
		<?php if ($terminy) :
			$terminy_count   = count($terminy);
			$terminy_obrazek = get_field('terminy_obrazek');
			$is_single_term  = ($terminy_count === 1 && ! empty($terminy_obrazek));
		?>
			<section class="xevos-section xevos-skoleni-terminy<?php echo $is_single_term ? ' xevos-skoleni-terminy--single' : ''; ?>">
				<div class="xevos-section__container">
					<?php $terminy_nadpis = get_field('terminy_nadpis') ?: ''; ?>
					<?php if ( $terminy_nadpis ) : ?>
						<h2><?php echo esc_html( $terminy_nadpis ); ?></h2>
					<?php endif; ?>
					<div class="xevos-termin-cards">
						<?php foreach ($terminy as $i => $t) :
							$dost = xevos_get_termin_dostupnost(get_the_ID(), $i);
							$t_uroven = $t['uroven'] ?? '';
							$t_uroven_label = ['zakladni' => 'Základní', 'pokrocily' => 'Pokročilý', 'expert' => 'Expert'][$t_uroven] ?? '';
						?>
							<div class="xevos-termin-card<?php echo $dost['plne'] ? ' xevos-termin-card--full' : ''; ?>" <?php if (!$dost['plne']) : ?> data-termin="<?php echo esc_attr(xevos_termin_key($t)); ?>" role="button" tabindex="0" <?php endif; ?>>
								<div class="xevos-termin-card__date"><?php echo esc_html($t['datum'] ?? ''); ?></div>
								<div class="xevos-termin-card__location"><?php echo esc_html($t['misto'] ?? ''); ?></div>
								<div class="xevos-termin-card__time"><?php echo esc_html(($t['cas_od'] ?? '') . ' – ' . ($t['cas_do'] ?? '')); ?></div>
								<?php if ($t_uroven_label) : ?>
									<div class="xevos-termin-card__uroven"><?php echo esc_html($t_uroven_label); ?></div>
								<?php endif; ?>
								<?php if ($dost['plne']) : ?>
									<div class="xevos-termin-card__stav">Obsazeno</div>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>

						<?php if ($is_single_term) : ?>
							<div class="xevos-termin-image">
								<?php echo xevos_img($terminy_obrazek, 'full', ['loading' => 'lazy']); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<!-- 4. Lektoři -->
		<?php
		$has_lektori = false;
		if (is_array($lektori)) {
			foreach ($lektori as $l) {
				if (! empty($l['jmeno'])) {
					$has_lektori = true;
					break;
				}
			}
		}

		$lektor_count = is_array($lektori) ? count($lektori) : 0;
		$use_swiper   = $lektor_count >= 3;
		?>
		<?php if ($has_lektori) : ?>
		<section class="xevos-section xevos-skoleni-lektori-section">
			<div class="xevos-section__container">
				<?php $lektori_nadpis = get_field('lektori_nadpis') ?: ''; ?>
				<?php if ( $lektori_nadpis ) : ?>
					<h2><?php echo esc_html( $lektori_nadpis ); ?></h2>
				<?php endif; ?>
				<div class="xevos-skoleni-lektori__carousel">
					<?php if ($use_swiper) : ?>
						<button class="xevos-nav-arrow xevos-nav-arrow--prev" aria-label="Předchozí" type="button">
							<svg width="12" height="20" viewBox="0 0 12 20" fill="none">
								<path d="M10 2L2 10l8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</button>
					<?php endif; ?>

					<div class="<?php echo $use_swiper ? 'swiper xevos-lektori-swiper' : 'xevos-skoleni-lektori__cards'; ?>" <?php echo $use_swiper ? 'id="lektori-swiper"' : ''; ?>>
						<div class="<?php echo $use_swiper ? 'swiper-wrapper' : 'xevos-skoleni-lektori__static'; ?>">
							<?php foreach ($lektori as $li => $l) :
								if ( empty( $l['jmeno'] ) ) continue;
								$foto_id = 0;
								if (! empty($l['foto']) && is_array($l['foto'])) {
									$foto_id = (int) ($l['foto']['ID'] ?? 0);
								} elseif (! empty($l['foto']) && is_numeric($l['foto'])) {
									$foto_id = (int) $l['foto'];
								}
							?>
								<div class="<?php echo $use_swiper ? 'swiper-slide' : ''; ?> xevos-lektor-card">
									<?php if ( $foto_id ) : ?>
										<div class="xevos-lektor-card__foto-wrap">
											<?php echo xevos_img($foto_id, 'xevos-lektor', [
												'alt'   => $l['jmeno'] ?? '',
												'class' => 'xevos-lektor-card__foto',
											]); ?>
										</div>
									<?php endif; ?>
									<div class="xevos-lektor-card__info">
										<div class="xevos-lektor-card__name-wrap">
											<div class="xevos-lektor-card__name"><?php echo esc_html($l['jmeno'] ?? ''); ?></div>
											<?php if (! empty($l['pozice'])) : ?>
												<div class="xevos-lektor-card__role"><?php echo esc_html($l['pozice']); ?></div>
											<?php endif; ?>
										</div>
										<?php if (! empty($l['bio'])) : ?>
											<p class="xevos-lektor-card__bio"><?php echo esc_html($l['bio']); ?></p>
											<button type="button" class="xevos-lektor-card__bio-toggle" hidden>Zobrazit více</button>
										<?php endif; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>

					<?php if ($use_swiper) : ?>
						<button class="xevos-nav-arrow xevos-nav-arrow--next" aria-label="Další" type="button">
							<svg width="12" height="20" viewBox="0 0 12 20" fill="none">
								<path d="M2 2l8 8-8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</button>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php endif; ?>

		<!-- 5. Harmonogram + Osnova -->
		<?php
		$has_harmonogram = false;
		if (is_array($harmonogram)) {
			foreach ($harmonogram as $h) {
				if (! empty($h['cas_od']) || ! empty($h['cas'])) {
					$has_harmonogram = true;
					break;
				}
			}
		}
		$has_osnova = false;
		if (is_array($osnova)) {
			foreach ($osnova as $o) {
				if (! empty($o['bod'])) {
					$has_osnova = true;
					break;
				}
			}
		}
		?>
		<?php if ($has_harmonogram || $has_osnova) : ?>
		<section class="xevos-section">
			<div class="xevos-section__container">
				<div class="xevos-skoleni-content-cols">
					<?php if ($has_harmonogram) : ?>
						<div class="xevos-skoleni-content-cols__col">
							<?php $harmonogram_nadpis = get_field('harmonogram_nadpis') ?: ''; ?>
							<?php if ( $harmonogram_nadpis ) : ?>
								<h2><?php echo esc_html( $harmonogram_nadpis ); ?></h2>
							<?php endif; ?>
							<table class="xevos-harmonogram-table">
								<?php foreach ($harmonogram as $h) :
									$cas_od = $h['cas_od'] ?? '';
									$cas_do = $h['cas_do'] ?? '';
									if (! $cas_od && ! empty($h['cas'])) {
										$cas_od = $h['cas']; // fallback starý formát
									}
									if (! $cas_od) continue;
								?>
									<tr class="xevos-harmonogram-item">
										<td class="xevos-harmonogram-item__cas"><?php echo esc_html($cas_od); ?></td>
										<td class="xevos-harmonogram-item__sep xevos-harmonogram-item__dash">—</td>
										<td class="xevos-harmonogram-item__cas"><?php echo esc_html($cas_do); ?></td>
										<td class="xevos-harmonogram-item__sep">|</td>
										<td class="xevos-harmonogram-item__text"><?php echo esc_html($h['aktivita'] ?? ''); ?></td>
									</tr>
								<?php endforeach; ?>
							</table>

							<?php $co_odnesete = get_field('co_odnesete') ?: []; ?>
							<?php if ( ! empty( $co_odnesete ) ) : ?>
								<div class="xevos-skoleni-benefits">
									<?php $co_odnesete_nadpis = get_field('co_odnesete_nadpis') ?: ''; ?>
									<?php if ( $co_odnesete_nadpis ) : ?>
										<h2><?php echo esc_html( $co_odnesete_nadpis ); ?></h2>
									<?php endif; ?>
									<?php xevos_component('checklist', ['items' => $co_odnesete]); ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ($has_osnova) : ?>
						<div class="xevos-skoleni-content-cols__col">
							<?php $osnova_nadpis = get_field('osnova_nadpis') ?: ''; ?>
							<?php if ( $osnova_nadpis ) : ?>
								<h2><?php echo esc_html( $osnova_nadpis ); ?></h2>
							<?php endif; ?>
							<ul class="xevos-osnova-list">
								<?php foreach ($osnova as $o) : ?>
									<?php if ( empty( $o['bod'] ) ) continue; ?>
									<li><?php echo wp_kses_post($o['bod'] ?? ''); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php endif; ?>

		<!-- 6. Kde parkovat -->
		<?php
		$kde_parkovat = get_field('kde_parkovat');
		$kde_park_img = get_field('kde_parkovat_obrazek');
		?>
		<?php if ( $kde_parkovat ) : ?>
		<section class="xevos-section xevos-skoleni-kde-parkovat">
			<div class="xevos-section__container">
				<div class="xevos-skoleni-parking">
					<?php if ( $kde_park_img ) : ?>
						<div class="xevos-skoleni-parking__image">
							<a href="<?php echo esc_url($kde_park_img['url']); ?>" class="xevos-lightbox-trigger" aria-label="Zvětšit obrázek">
								<?php echo xevos_img($kde_park_img, 'xevos-article', ['loading' => 'lazy', 'alt' => 'Kde parkovat']); ?>
								<span class="xevos-lightbox-zoom" aria-hidden="true">
									<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
								</span>
							</a>
						</div>
					<?php endif; ?>
					<div class="xevos-skoleni-parking__text">
						<h2><?php echo esc_html( get_field('kde_parkovat_nadpis') ?: '' ); ?></h2>
						<?php echo wp_kses_post($kde_parkovat); ?>
					</div>
				</div>
			</div>
		</section>
		<?php endif; ?>

		<!-- 7. Recenze -->
		<?php get_template_part('template-parts/components/recenze'); ?>

		<!-- 8. Formulář -->
		<?php
		if ($formular !== '0') :
			$form_action = $is_free ? 'xevos_ecomail_register' : '';

			// Check if all termíny are full.
			$all_full = true;
			if (is_array($terminy) && !empty($terminy)) {
				foreach ($terminy as $ti => $t) {
					$d = xevos_get_termin_dostupnost(get_the_ID(), $ti);
					if (!$d['plne']) {
						$all_full = false;
						break;
					}
				}
			} else {
				$all_full = false;
			}

			// Default headings by type.
			if ($typ_prihlaseni === 'pozvanka') {
				$default_heading = 'Požádat o pozvánku';
				$default_desc    = 'Kapacita akce je omezená. Vyplňte formulář a my vám dáme vědět, zda pro vás máme místo.';
				$submit_label    = 'Odeslat žádost o pozvánku';
			} elseif ($typ_prihlaseni === 'zdarma') {
				$default_heading = 'Registrace zdarma';
				$default_desc    = '';
				$submit_label    = 'Registrovat se zdarma';
			} else {
				$default_heading = 'Objednat školení';
				$default_desc    = '';
				$submit_label    = 'Závazně objednat';
			}

			// ACF overrides.
			$form_heading = get_field('formular_nadpis') ?: $default_heading;
			$form_desc    = get_field('formular_popis') ?: $default_desc;
		?>
			<section class="xevos-section" id="objednavka">
				<div class="xevos-section__container">

					<?php if ($all_full) : ?>
						<div class="xevos-order-full">
							<h3>Kapacita naplněna</h3>
							<p>Všechny termíny tohoto školení jsou bohužel obsazené. Kontaktujte nás pro informace o dalších termínech.</p>
							<a href="<?php echo esc_url(home_url('/kontakt/')); ?>" class="xevos-btn xevos-btn--outline">Kontaktujte nás</a>
						</div>
					<?php else : ?>
						<div class="xevos-skoleni-section-header">
							<h2><?php echo esc_html($form_heading); ?></h2>
							<?php if ($form_desc) : ?>
								<div class="xevos-skoleni-section-header__desc"><?php echo wp_kses_post($form_desc); ?></div>
							<?php endif; ?>
						</div>

						<?php $invoice_mode = ! $is_free && function_exists('xevos_is_feature_enabled') && xevos_is_feature_enabled('invoice_payment'); ?>
						<form class="xevos-order-section" method="post" id="xevos-order-form" data-free="<?php echo $is_free ? '1' : '0'; ?>" data-typ="<?php echo esc_attr($typ_prihlaseni); ?>" data-invoice="<?php echo $invoice_mode ? '1' : '0'; ?>">
							<input type="hidden" name="action" value="<?php echo esc_attr($form_action); ?>">
							<input type="hidden" name="skoleni_id" value="<?php echo esc_attr(get_the_ID()); ?>">
							<input type="hidden" name="skoleni_nazev" value="<?php echo esc_attr(get_the_title()); ?>">
							<input type="hidden" name="skoleni_cena" value="<?php echo esc_attr($is_free ? 0 : $cena_s_dph); ?>">
							<input type="hidden" name="typ_prihlaseni" value="<?php echo esc_attr($typ_prihlaseni); ?>">
							<?php wp_nonce_field('xevos_order', 'xevos_order_nonce'); ?>
							<input type="hidden" name="_form_time" value="<?php echo esc_attr(function_exists('xevos_form_time_token') ? xevos_form_time_token() : ''); ?>">
							<?php if (function_exists('xevos_turnstile_enabled') && xevos_turnstile_enabled()) : ?>
								<div class="cf-turnstile" data-sitekey="<?php echo esc_attr(xevos_get_option('turnstile_site_key')); ?>" data-theme="dark"></div>
							<?php endif; ?>

							<div class="xevos-order-form">
								<div class="xevos-form-row">
									<div class="xevos-form__group">
										<label class="xevos-form__label">Jméno <span class="xevos-form__required">*</span></label>
										<input type="text" class="xevos-form__input" name="jmeno" required>
									</div>
									<div class="xevos-form__group">
										<label class="xevos-form__label">Příjmení <span class="xevos-form__required">*</span></label>
										<input type="text" class="xevos-form__input" name="prijmeni" required>
									</div>
								</div>
								<div class="xevos-form-row">
									<div class="xevos-form__group">
										<label class="xevos-form__label">Telefon <span class="xevos-form__required">*</span></label>
										<input type="tel" class="xevos-form__input" name="telefon" required>
									</div>
									<div class="xevos-form__group">
										<label class="xevos-form__label">E-mail <span class="xevos-form__required">*</span></label>
										<input type="email" class="xevos-form__input" name="email" required>
									</div>
								</div>
								<div class="xevos-form-row">
									<div class="xevos-form__group">
										<label class="xevos-form__label">Event <span class="xevos-form__required">*</span></label>
										<select class="xevos-form__input" name="skoleni">
											<option><?php the_title(); ?></option>
										</select>
									</div>
									<div class="xevos-form__group">
										<label class="xevos-form__label">Termín <span class="xevos-form__required">*</span></label>
										<select class="xevos-form__input" name="termin" id="xevos-termin-select">
											<?php if ($terminy) : foreach ($terminy as $ti => $t) :
													$td = xevos_get_termin_dostupnost(get_the_ID(), $ti);
											?>
													<?php
													$opt_label = $t['datum'] ?? '';
													if (!empty($t['cas_od']) && !empty($t['cas_do'])) {
														$opt_label .= ' | ' . $t['cas_od'] . ' – ' . $t['cas_do'];
													}
													if ($td['plne']) {
														$opt_label .= ' (Obsazeno)';
													}
													?>
													<option value="<?php echo esc_attr(xevos_termin_key($t)); ?>" <?php echo $td['plne'] ? ' disabled' : ''; ?>>
														<?php echo esc_html($opt_label); ?>
													</option>
											<?php endforeach;
											endif; ?>
										</select>
									</div>
								</div>
								<div class="xevos-form-row">
									<?php if ($typ === 'hybrid') : ?>
										<div class="xevos-form__group">
											<label class="xevos-form__label">Forma účasti <span class="xevos-form__required">*</span></label>
											<select class="xevos-form__input" name="forma" required>
												<option value="">– vyberte formu –</option>
												<option value="prezencni">Prezenčně (na místě)</option>
												<option value="online">Online (stream)</option>
											</select>
										</div>
									<?php endif; ?>
									<div class="xevos-form__group">
										<label class="xevos-form__label">Počet účastníků <span class="xevos-form__required">*</span></label>
										<input type="number" class="xevos-form__input" name="pocet" min="1" value="1">
									</div>
									<?php if ($typ !== 'hybrid') : ?>
										<div class="xevos-form__group">
											<label class="xevos-form__label">Název firmy <span class="xevos-form__required">*</span></label>
											<input type="text" class="xevos-form__input" name="firma">
										</div>
									<?php endif; ?>
								</div>
								<?php if ($typ === 'hybrid') : ?>
									<div class="xevos-form-row">
										<div class="xevos-form__group">
											<label class="xevos-form__label">Název firmy <span class="xevos-form__required">*</span></label>
											<input type="text" class="xevos-form__input" name="firma">
										</div>
									</div>
								<?php endif; ?>
								<?php if (! $is_free) : ?>
									<div class="xevos-form-row">
										<div class="xevos-form__group">
											<label class="xevos-form__label">IČ <span class="xevos-form__required">*</span></label>
											<input type="text" class="xevos-form__input" name="ico">
										</div>
										<div class="xevos-form__group">
											<label class="xevos-form__label">DIČ</label>
											<input type="text" class="xevos-form__input" name="dic">
										</div>
									</div>
									<div class="xevos-form-row">
										<div class="xevos-form__group">
											<label class="xevos-form__label">Ulice <span class="xevos-form__required">*</span></label>
											<input type="text" class="xevos-form__input" name="ulice">
										</div>
										<div class="xevos-form__group">
											<label class="xevos-form__label">Město <span class="xevos-form__required">*</span></label>
											<input type="text" class="xevos-form__input" name="mesto">
										</div>
									</div>
									<div class="xevos-form-row">
										<div class="xevos-form__group">
											<label class="xevos-form__label">PSČ <span class="xevos-form__required">*</span></label>
											<input type="text" class="xevos-form__input" name="psc">
										</div>
										<div class="xevos-form__group">
											<div class="xevos-form__checkbox-wrap">
												<input type="checkbox" class="xevos-form__checkbox" name="platce_dph" id="platce_dph">
												<label class="xevos-form__checkbox-label" for="platce_dph">Jsem plátce DPH</label>
											</div>
										</div>
									</div>
								<?php endif; ?>
								<div class="xevos-form__hp"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
							</div>

							<div class="xevos-order-summary">
								<?php xevos_component('contact-info'); ?>

								<?php if (get_field('formular_zobrazit_cenu') !== false) : ?>
								<div class="xevos-order-summary__price-block">
									<?php if ($is_free) : ?>
										<div class="xevos-order-summary__price-main">Zdarma</div>
									<?php else : ?>
										<div class="xevos-order-summary__price-label">Celková cena:</div>
										<div class="xevos-order-summary__price-main"
											data-price-unit="<?php echo esc_attr((float) $cena_s_dph); ?>"
											data-price-unit-net="<?php echo esc_attr((float) $cena); ?>"
											id="xevos-price-total">
											<?php echo esc_html($cena_s_dph ? number_format((float) $cena_s_dph, 0, ',', ' ') . ' Kč' : '—'); ?>
											<small>s DPH</small>
										</div>
										<?php if ($cena) : ?>
											<div class="xevos-order-summary__price-secondary" id="xevos-price-net">
												<?php echo esc_html(number_format((float) $cena, 0, ',', ' ')); ?> Kč bez DPH
											</div>
										<?php endif; ?>
									<?php endif; ?>
								</div>
								<?php endif; ?>

								<?php if ($invoice_mode) : ?>
									<div class="xevos-payment-method">
										<label class="xevos-payment-method__option">
											<input type="radio" name="payment_method" value="online" checked>
											<span class="xevos-payment-method__label">
												<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
													<rect x="1" y="4" width="22" height="16" rx="2" />
													<line x1="1" y1="10" x2="23" y2="10" />
												</svg>
												Platba kartou online
											</span>
										</label>
										<label class="xevos-payment-method__option">
											<input type="radio" name="payment_method" value="invoice">
											<span class="xevos-payment-method__label">
												<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
													<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
													<polyline points="14 2 14 8 20 8" />
													<line x1="16" y1="13" x2="8" y2="13" />
													<line x1="16" y1="17" x2="8" y2="17" />
												</svg>
												Platba na fakturu
											</span>
										</label>
									</div>
								<?php endif; ?>
								<button type="submit" class="xevos-btn xevos-btn--primary" id="xevos-order-submit">
									<span class="xevos-btn__arrow">
										<svg width="39" height="39" viewBox="0 0 20 20" fill="none">
											<path d="M5 15L15 5M15 5H7M15 5v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
									</span>
									<?php echo esc_html(strtoupper($submit_label)); ?>
								</button>
								<div class="xevos-order-message" id="xevos-order-message" style="display:none;"></div>
							</div>
							<?php if (! $is_free) : ?>
								<script>
								(function(){
									var form   = document.getElementById('xevos-order-form');
									var pocet  = form && form.querySelector('[name="pocet"]');
									var total  = document.getElementById('xevos-price-total');
									var net    = document.getElementById('xevos-price-net');
									if (!form || !pocet || !total) return;

									var unit    = parseFloat(total.dataset.priceUnit) || 0;
									var unitNet = parseFloat(total.dataset.priceUnitNet) || 0;

									function format(n){ return n.toLocaleString('cs-CZ').replace(/\s/g, ' '); }

									function update(){
										var q = Math.max(1, parseInt(pocet.value, 10) || 1);
										total.innerHTML = format(Math.round(unit * q)) + ' Kč <small>s DPH</small>';
										if (net) net.textContent = format(Math.round(unitNet * q)) + ' Kč bez DPH';
									}

									pocet.addEventListener('input', update);
									pocet.addEventListener('change', update);
								})();
								</script>
							<?php endif; ?>
						</form>
					<?php endif; /* !$all_full */ ?>
				</div>
			</section>
		<?php endif; ?>

	<?php endwhile;
get_footer(); ?>
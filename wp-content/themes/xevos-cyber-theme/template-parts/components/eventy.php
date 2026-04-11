<?php

/**
 * Homepage: Aktuální eventy / Školení.
 * Two-column layout – events list | image-bg CTA.
 */

$show = get_field('eventy_zobrazit_sekci');
if ($show === false) return;

$heading = get_field('eventy_heading') ?: 'Aktuální eventy';
$desc    = get_field('eventy_popis') ?: '<strong>Workshopy, školení a odborné akce</strong> zaměřené na praktickou kybernetickou bezpečnost, aktuální hrozby a reálné scénáře z praxe.';
$count   = (int) (get_field('eventy_pocet') ?: 10);

// CTA box (Cyber pohotovost) – ACF editable.
$cta_title     = get_field('eventy_cta_title') ?: 'Cyber pohotovost';
$cta_text      = get_field('eventy_cta_text') ?: 'Okamžitá pomoc a kroky, které pomohou rychle zastavit probíhající útok, omezit škody a zahájit správný postup reakce.';
$cta_btn1_text = get_field('eventy_cta_btn1_text') ?: 'POTŘEBUJI POMOC!';
$cta_btn1_url  = get_field('eventy_cta_btn1_url') ?: home_url('/kontakt/');
$cta_btn2_text = get_field('eventy_cta_btn2_text') ?: 'JAK POSTUPOVAT?';
$cta_btn2_url  = get_field('eventy_cta_btn2_url') ?: home_url('/jak-postupovat/');
$cta_image     = get_field('eventy_cta_image');
$cta_img_url   = $cta_image ? $cta_image['url'] : get_theme_file_uri('assets/img/homepage/xevos-house.png');
$cta_img_url_bg   = $cta_image ? $cta_image['url'] : get_theme_file_uri('assets/img/homepage/xevos-house-bg.png');

$query = new WP_Query([
	'post_type'      => 'skoleni',
	'post_status'    => 'publish',
	'posts_per_page' => $count,
	'orderby'        => 'date',
	'order'          => 'DESC',
]);

/* Map typ values to display labels */
$typ_labels = [
	'online'    => 'Online workshop',
	'prezencni' => 'workshop',
	'hybrid'    => 'Hybridní',
];

/* Map typ to dot color classes */
$typ_colors = [
	'online'    => 'green',
	'prezencni' => 'orange',
	'hybrid'    => 'blue',
];
?>

<section class="xevos-section xevos-eventy">
	<div class="xevos-section__container">
		<div class="xevos-eventy__grid">

			<!-- LEFT COLUMN: heading + event list + scrollbar -->
			<div class="xevos-eventy__left">
				<div class="xevos-eventy__header">
					<h2><?php echo esc_html($heading); ?></h2>
					<div class="xevos-eventy__desc"><?php echo wp_kses_post($desc); ?></div>
				</div>

				<div class="xevos-eventy__list-wrap">
					<!-- Scrollbar vedle listu -->
					<div class="xevos-eventy__scrollbar" aria-hidden="true">
						<div class="xevos-eventy__scrollbar-track"></div>
						<div class="xevos-eventy__scrollbar-thumb"></div>
					</div>
					<div class="xevos-eventy__list">
						<?php if ($query->have_posts()) : ?>
							<?php $i = 0;
							while ($query->have_posts()) : $query->the_post();
								$terminy = get_field('terminy');
								$typ     = get_field('typ') ?: 'prezencni';

								/* Find the nearest future termin */
								$nejblizsi       = null;
								$nejblizsi_index = 0;
								if ($terminy) {
									foreach ($terminy as $idx => $t) {
										if (! empty($t['datum'])) {
											$nejblizsi       = $t;
											$nejblizsi_index = $idx;
											break;
										}
									}
								}

								/* Availability */
								$dostupnost = null;
								if ($nejblizsi) {
									$dostupnost = xevos_get_termin_dostupnost(get_the_ID(), $nejblizsi_index);
								}

								/* Parse date into month / day */
								$month = '';
								$day   = '';
								if ($nejblizsi && ! empty($nejblizsi['datum'])) {
									$ts = strtotime(str_replace('.', '-', $nejblizsi['datum']));
									if ($ts) {
										$month = date('n', $ts);
										$day   = date('d', $ts);
									}
								}

								$typ_label = $typ_labels[$typ] ?? ucfirst($typ);
								$typ_color = $typ_colors[$typ] ?? 'orange';
							?>
								<div class="xevos-eventy__item<?php echo $i === 0 ? ' xevos-eventy__item--first' : ''; ?>">
									<div class="xevos-eventy__date">
										<?php if ($month && $day) : ?>
											<span class="xevos-eventy__date-month"><?php echo esc_html($month); ?></span><span class="xevos-eventy__date-slash">/</span><span class="xevos-eventy__date-day"><?php echo esc_html($day); ?></span>
										<?php endif; ?>
									</div>

									<div class="xevos-eventy__content">
										<h3 class="xevos-eventy__title"><a href="<?php the_permalink(); ?>"><?php echo wp_kses_post(get_field('hero_nadpis') ?: get_the_title()); ?></a></h3>

										<div class="xevos-eventy__meta">
											<?php if ($dostupnost) : ?>
												<span class="xevos-eventy__meta-seats xevos-eventy__meta-seats--<?php echo esc_attr($dostupnost['stav']); ?>">
													<?php echo esc_html($dostupnost['label']); ?>
													<?php if (!empty($dostupnost['cislo'])) : ?>
														<span class="xevos-eventy__meta-capacity"><?php echo esc_html($dostupnost['cislo']); ?></span>
													<?php endif; ?>
												</span>
											<?php endif; ?>
											<span class="xevos-eventy__meta-type xevos-eventy__meta-type--<?php echo esc_attr($typ_color); ?>">
												<span class="xevos-eventy__dot"></span>
												<?php echo esc_html($typ_label); ?>
											</span>
										</div>

										<a href="<?php the_permalink(); ?>" class="xevos-eventy__register-btn">REGISTROVAT SE</a>
									</div>
								</div>
							<?php $i++;
							endwhile;
							wp_reset_postdata(); ?>
							<?php else :
							/* Testovací data — zobrazí se když nejsou žádná školení v DB */
							$demo_events = [
								['month' => '2', 'day' => '28', 'title' => 'Kybernetická bezpečnost - <strong>OBECNÁ</strong>', 'seats' => '1/20', 'type' => 'Online workshop', 'color' => 'green'],
								['month' => '3', 'day' => '05', 'title' => '<strong>MANAŽER</strong> kybernetické bezpečnosti', 'seats' => '11/50', 'type' => 'workshop', 'color' => 'orange'],
								['month' => '3', 'day' => '08', 'title' => '<strong>POVĚŘENÁ OSOBA</strong> kybernetické bezpečnosti', 'seats' => '', 'type' => 'Online workshop', 'color' => 'green'],
								['month' => '2', 'day' => '28', 'title' => 'Kybernetická bezpečnost - <strong>OBECNÁ</strong>', 'seats' => '1/20', 'type' => 'Online workshop', 'color' => 'green'],
								['month' => '3', 'day' => '05', 'title' => '<strong>MANAŽER</strong> kybernetické bezpečnosti', 'seats' => '11/50', 'type' => 'workshop', 'color' => 'orange'],
								['month' => '3', 'day' => '08', 'title' => '<strong>POVĚŘENÁ OSOBA</strong> kybernetické bezpečnosti', 'seats' => '', 'type' => 'Online workshop', 'color' => 'green'],
							];
							foreach ($demo_events as $di => $demo) : ?>
								<div class="xevos-eventy__item<?php echo $di === 0 ? ' xevos-eventy__item--first' : ''; ?>">
									<div class="xevos-eventy__date">
										<span class="xevos-eventy__date-month"><?php echo $demo['month']; ?></span><span class="xevos-eventy__date-slash">/</span><span class="xevos-eventy__date-day"><?php echo $demo['day']; ?></span>
									</div>
									<div class="xevos-eventy__content">
										<h3 class="xevos-eventy__title"><?php echo $demo['title']; ?></h3>
										<div class="xevos-eventy__meta">
											<?php if ($demo['seats']) : ?>
												<span class="xevos-eventy__meta-seats">
													Volná místa <span class="xevos-eventy__meta-capacity">(<?php echo $demo['seats']; ?>)</span>
												</span>
											<?php endif; ?>
											<span class="xevos-eventy__meta-type xevos-eventy__meta-type--<?php echo $demo['color']; ?>">
												<span class="xevos-eventy__dot"></span>
												<?php echo $demo['type']; ?>
											</span>
										</div>
										<a href="#" class="xevos-eventy__register-btn">REGISTROVAT SE</a>
									</div>
								</div>
						<?php endforeach;
						endif; ?>
					</div>


				</div>

				<a href="<?php echo esc_url(get_post_type_archive_link('skoleni')); ?>" class="xevos-eventy__all-link">ZOBRAZIT VŠECHNY ŠKOLENÍ</a>
			</div>

			<!-- RIGHT COLUMN: image + emergency CTA overlay -->
			<div class="xevos-eventy__cta" style="background-image: url('<?php echo esc_url($cta_img_url_bg); ?>'); background-size: cover; background-position: center;">
				<div class="xevos-eventy__cta-img">
					<img src="<?php echo esc_url($cta_img_url); ?>" alt="<?php echo esc_attr($cta_title); ?>" loading="lazy">
				</div>
				<!-- Chevron arrow -->
				<button class="xevos-eventy__cta-arrow" aria-label="Předchozí">
					<svg width="16" height="32" viewBox="0 0 16 32" fill="none">
						<path d="M13 3L3 16l10 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</button>

				<div class="xevos-eventy__cta-content">
					<div class="xevos-eventy__cta-text">
						<h3 class="xevos-eventy__cta-title"><?php echo esc_html($cta_title); ?></h3>
						<p><?php echo wp_kses_post(strip_tags($cta_text, '<strong><b><em><br>')); ?></p>
					</div>
					<div class="xevos-eventy__cta-buttons">
						<a href="<?php echo esc_url($cta_btn1_url); ?>" class="xevos-btn xevos-btn--primary">
							<span class="xevos-btn__arrow"></span>
							<?php echo esc_html($cta_btn1_text); ?>
						</a>
						<?php if ($cta_btn2_text) : ?>
							<a href="<?php echo esc_url($cta_btn2_url); ?>" class="xevos-btn xevos-btn--outline"><?php echo esc_html($cta_btn2_text); ?></a>
						<?php endif; ?>
					</div>
				</div>
			</div>

		</div>
	</div>
</section>
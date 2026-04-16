<?php

/**
 * Homepage: Aktuální eventy / Školení.
 * Two-column layout – events list | image-bg CTA.
 */

$show = get_field('eventy_zobrazit_sekci');
if ($show === false) return;

$heading = get_field('eventy_heading') ?: '';
$desc    = get_field('eventy_popis') ?: '';
$count   = (int) (get_field('eventy_pocet') ?: 10);

// CTA box (Cyber pohotovost) – ACF editable.
$cta_title     = get_field('eventy_cta_title') ?: '';
$cta_text      = get_field('eventy_cta_text') ?: '';
$cta_btn1_text = get_field('eventy_cta_btn1_text') ?: '';
$cta_btn1_url  = get_field('eventy_cta_btn1_url') ?: home_url('/kontakt/');
$cta_btn2_text = get_field('eventy_cta_btn2_text') ?: '';
$cta_btn2_url  = get_field('eventy_cta_btn2_url') ?: home_url('/jak-postupovat/');
$cta_image      = get_field('eventy_cta_image');
$cta_img_id     = $cta_image ? (int) ($cta_image['ID'] ?? 0) : 0;
$cta_img_url    = $cta_image ? $cta_image['url'] : get_theme_file_uri('assets/img/homepage/xevos-house.png');
$cta_img_url_bg = get_theme_file_uri('assets/img/homepage/xevos-house-bg.png');

$query = new WP_Query([
	'post_type'      => 'skoleni',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'orderby'        => 'date',
	'order'          => 'DESC',
]);

/* Sort posts by their nearest future termin date */
$today = strtotime( 'today' );
$posts_with_date = [];
if ( $query->have_posts() ) {
	foreach ( $query->posts as $_ev_post ) {
		$terminy = get_field( 'terminy', $_ev_post->ID );
		$nearest_ts = PHP_INT_MAX;
		if ( $terminy ) {
			foreach ( $terminy as $t ) {
				if ( empty( $t['datum'] ) ) continue;
				$ts = strtotime( str_replace( '.', '-', $t['datum'] ) );
				if ( $ts && $ts >= $today && $ts < $nearest_ts ) {
					$nearest_ts = $ts;
				}
			}
		}
		$posts_with_date[] = [ 'post' => $_ev_post, 'nearest_ts' => $nearest_ts ];
	}
	usort( $posts_with_date, fn( $a, $b ) => $a['nearest_ts'] <=> $b['nearest_ts'] );
	$posts_with_date = array_slice( $posts_with_date, 0, $count );
}

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

/* Skrýt celou sekci, když nejsou žádná nadcházející školení. */
if ( empty( $posts_with_date ) ) {
	return;
}
?>

<section class="xevos-section xevos-eventy">
	<div class="xevos-section__container">
		<div class="xevos-eventy__grid">

			<!-- LEFT COLUMN: heading + event list + scrollbar -->
			<div class="xevos-eventy__left">
				<?php if ( $heading || $desc ) : ?>
					<div class="xevos-eventy__header">
						<?php if ( $heading ) : ?>
							<h2><?php echo esc_html($heading); ?></h2>
						<?php endif; ?>
						<?php if ( $desc ) : ?>
							<div class="xevos-eventy__desc"><?php echo wp_kses_post($desc); ?></div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="xevos-eventy__list-wrap">
					<!-- Scrollbar vedle listu -->
					<div class="xevos-eventy__scrollbar" aria-hidden="true">
						<div class="xevos-eventy__scrollbar-track"></div>
						<div class="xevos-eventy__scrollbar-thumb"></div>
					</div>
					<div class="xevos-eventy__list">
						<?php $i = 0;
							foreach ( $posts_with_date as $pwd ) :
								$post = $pwd['post'];
								setup_postdata( $GLOBALS['post'] = $post );
								$terminy = get_field('terminy');
								$typ     = get_field('typ') ?: 'prezencni';

								/* Find the nearest future termin */
								$nejblizsi       = null;
								$nejblizsi_index = 0;
								if ($terminy) {
									foreach ($terminy as $idx => $t) {
										if ( empty($t['datum']) ) continue;
										$ts = strtotime( str_replace('.', '-', $t['datum']) );
										if ( $ts && $ts >= $today ) {
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
											<span class="xevos-eventy__date-day"><?php echo esc_html($day); ?></span><span class="xevos-eventy__date-slash">/</span><span class="xevos-eventy__date-month"><?php echo esc_html($month); ?></span>
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
							endforeach;
							wp_reset_postdata(); ?>
					</div>


				</div>

				<a href="<?php echo esc_url(get_post_type_archive_link('skoleni')); ?>" class="xevos-eventy__all-link">ZOBRAZIT VŠECHNY ŠKOLENÍ</a>
			</div>

			<!-- RIGHT COLUMN: image + emergency CTA overlay -->
			<div class="xevos-eventy__cta" style="background-image: url('<?php echo esc_url($cta_img_url_bg); ?>'); background-size: cover; background-position: center;">
				<div class="xevos-eventy__cta-img">
					<?php if ($cta_img_id) :
						echo xevos_img($cta_img_id, 'full', ['alt' => $cta_title, 'loading' => 'lazy']);
					else : ?>
						<img src="<?php echo esc_url($cta_img_url); ?>" alt="<?php echo esc_attr($cta_title); ?>" loading="lazy">
					<?php endif; ?>
				</div>
				<!-- Chevron arrow -->
				<button class="xevos-eventy__cta-arrow" aria-label="Předchozí">
					<svg width="16" height="32" viewBox="0 0 16 32" fill="none">
						<path d="M13 3L3 16l10 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</button>

				<div class="xevos-eventy__cta-content">
					<?php if ( $cta_title || $cta_text ) : ?>
						<div class="xevos-eventy__cta-text">
							<?php if ( $cta_title ) : ?>
								<h3 class="xevos-eventy__cta-title"><?php echo esc_html($cta_title); ?></h3>
							<?php endif; ?>
							<?php if ( $cta_text ) : ?>
								<p><?php echo wp_kses_post(strip_tags($cta_text, '<strong><b><em><br>')); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<?php if ( $cta_btn1_text || $cta_btn2_text ) : ?>
						<div class="xevos-eventy__cta-buttons">
							<?php if ( $cta_btn1_text ) : ?>
								<a href="<?php echo esc_url($cta_btn1_url); ?>" class="xevos-btn xevos-btn--primary">
									<span class="xevos-btn__arrow"></span>
									<?php echo esc_html($cta_btn1_text); ?>
								</a>
							<?php endif; ?>
							<?php if ($cta_btn2_text) : ?>
								<a href="<?php echo esc_url($cta_btn2_url); ?>" class="xevos-btn xevos-btn--outline"><?php echo esc_html($cta_btn2_text); ?></a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>

		</div>
	</div>
</section>
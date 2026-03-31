<?php
/**
 * Component: Školení list card (archive view).
 * Figma: Full-width row, title + desc, meta columns (Datum, Úroveň, Délka, Kapacita, Typ, Cena), CTA.
 */

$cena      = get_field('cena_s_dph') ?: 0;
$typ       = get_field('typ') ?: '';
$popis     = get_field('popis');
$terminy   = get_field('terminy');

$typ_labels = [
	'online'    => 'Online',
	'prezencni' => 'Prezenční',
	'hybrid'    => 'Hybridní',
];

// Nearest future date + capacity.
$datum = '';
$kapacita_text = '';
$cas_od = '';
$cas_do = '';
if (is_array($terminy) && !empty($terminy)) {
	foreach ($terminy as $i => $t) {
		if (!empty($t['datum'])) {
			$datum = $t['datum'];
			$cas_od = $t['cas_od'] ?? '';
			$cas_do = $t['cas_do'] ?? '';
			$kap = (int) ($t['kapacita'] ?? 0);
			$reg = (int) ($t['pocet_registraci'] ?? 0);
			$volno = $kap - $reg;
			$kapacita_text = $volno <= 0 ? 'Obsazeno' : $reg . '/' . $kap;
			break;
		}
	}
}

// Délka školení
$delka = '';
if ($cas_od && $cas_do) {
	$delka = $cas_od . ' – ' . $cas_do;
}
?>

<a href="<?php the_permalink(); ?>" class="xevos-skoleni-list-card">
	<!-- Title + description -->
	<div class="xevos-skoleni-list-card__header">
		<h3 class="xevos-skoleni-list-card__title"><?php the_title(); ?></h3>
		<?php if ($popis) : ?>
			<p class="xevos-skoleni-list-card__desc"><?php echo esc_html(wp_trim_words(wp_strip_all_tags($popis), 20)); ?></p>
		<?php endif; ?>
	</div>

	<!-- Meta columns + CTA -->
	<div class="xevos-skoleni-list-card__meta-row">
		<div class="xevos-skoleni-list-card__meta">
			<?php if ($datum) : ?>
				<div class="xevos-skoleni-list-card__meta-col">
					<span class="xevos-skoleni-list-card__meta-label">Datum</span>
					<span class="xevos-skoleni-list-card__meta-value">
						<img src="<?php echo esc_url(get_theme_file_uri('assets/img/prehled-skoleni/datum.svg')); ?>" alt="" width="24" height="24">
						<?php echo esc_html($datum); ?>
					</span>
				</div>
			<?php endif; ?>

			<div class="xevos-skoleni-list-card__meta-col">
				<span class="xevos-skoleni-list-card__meta-label">Úroveň školení</span>
				<span class="xevos-skoleni-list-card__meta-value">
					<img src="<?php echo esc_url(get_theme_file_uri('assets/img/prehled-skoleni/uroven-skoleni.svg')); ?>" alt="" width="24" height="24">
					Základní
				</span>
			</div>

			<?php if ($delka) : ?>
				<div class="xevos-skoleni-list-card__meta-col">
					<span class="xevos-skoleni-list-card__meta-label">Délka školení</span>
					<span class="xevos-skoleni-list-card__meta-value">
						<img src="<?php echo esc_url(get_theme_file_uri('assets/img/prehled-skoleni/delka-skoleni.svg')); ?>" alt="" width="24" height="24">
						<?php echo esc_html($delka); ?>
					</span>
				</div>
			<?php endif; ?>

			<?php if ($kapacita_text) : ?>
				<div class="xevos-skoleni-list-card__meta-col">
					<span class="xevos-skoleni-list-card__meta-label">Kapacita</span>
					<span class="xevos-skoleni-list-card__meta-value">
						<img src="<?php echo esc_url(get_theme_file_uri('assets/img/prehled-skoleni/kapacita.svg')); ?>" alt="" width="24" height="24">
						K dispozici <?php echo esc_html($kapacita_text); ?>
					</span>
				</div>
			<?php endif; ?>

			<?php if ($typ) : ?>
				<div class="xevos-skoleni-list-card__meta-col">
					<span class="xevos-skoleni-list-card__meta-label">Typ školení</span>
					<span class="xevos-skoleni-list-card__meta-value">
						<img src="<?php echo esc_url(get_theme_file_uri('assets/img/prehled-skoleni/typ-skoleni.svg')); ?>" alt="" width="24" height="24">
						<?php echo esc_html($typ_labels[$typ] ?? ucfirst($typ)); ?>
					</span>
				</div>
			<?php endif; ?>

			<?php if ($cena) : ?>
				<div class="xevos-skoleni-list-card__meta-col xevos-skoleni-list-card__meta-col--price">
					<span class="xevos-skoleni-list-card__meta-label">Cena</span>
					<span class="xevos-skoleni-list-card__price"><?php echo esc_html(number_format((float) $cena, 0, ',', ' ')); ?> Kč</span>
				</div>
			<?php endif; ?>
		</div>

		<span class="xevos-btn xevos-btn--outline xevos-btn--sm">REGISTROVAT SE</span>
	</div>
</a>

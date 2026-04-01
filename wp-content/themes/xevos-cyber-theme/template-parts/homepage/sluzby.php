<?php

/**
 * Homepage: Služby – Swiper carousel of numbered cards.
 * Figma: 01 Dohledové centrum, 02 Penetrační testy, 03 Edukace, 04 Kyber politika
 */

$show = get_field('sluzby_zobrazit_sekci');
if ($show === false) return;

$heading = get_field('sluzby_heading');
$sluzby  = get_field('sluzby');

// Fallback data matching Figma + 4 extra cards for testing.
if (! $sluzby) {
	$sluzby = [
		['nazev' => 'Dohledové centrum',   'popis' => 'Security Operations Center – 24/7 monitoring, detekce incidentů a okamžitá reakce na bezpečnostní události.', 'url' => ''],
		['nazev' => 'Penetrační testy',    'popis' => 'Simulace útoku na vaše systémy s cílem identifikovat zranitelná místa a navrhnout účinná opatření k jejich eliminaci.', 'url' => ''],
		['nazev' => 'Edukace',             'popis' => 'Praktická školení a workshopy, jejichž cílem je zvýšení bezpečnostního povědomí zaměstnanců a celé organizace.', 'url' => ''],
		['nazev' => 'Kyber politika',      'popis' => 'Nastavení bezpečnostních politik, NIS2/NIS, směrnic, firemní postupy k dosažení kybernetické odolnosti.', 'url' => ''],
		['nazev' => 'Incident Response',   'popis' => 'Rychlá reakce na kybernetické incidenty – analýza, izolace hrozby a obnova provozu v minimálním čase.', 'url' => ''],
		['nazev' => 'SIEM & Monitoring',   'popis' => 'Nasazení a správa SIEM systémů pro centralizovaný sběr logů, korelaci událostí a včasnou detekci hrozeb.', 'url' => ''],
		['nazev' => 'Cloud Security',      'popis' => 'Zabezpečení cloudové infrastruktury – audit konfigurací, ochrana dat a compliance v prostředí AWS, Azure i GCP.', 'url' => ''],
		['nazev' => 'Forenzní analýza',    'popis' => 'Digitální forenzní vyšetřování – zajištění důkazů, analýza kompromitovaných systémů a reportování zjištění.', 'url' => ''],
	];
}
?>

<?php $use_swiper = count($sluzby) >= 5; ?>
<section class="xevos-section xevos-services">
	<div class="xevos-section__container xevos-services__container">
		<?php if ($use_swiper) : ?>
			<div id="services-prev" class="xevos-services__nav xevos-services__nav--prev"></div>
		<?php endif; ?>

		<div <?php echo $use_swiper ? 'id="services-swiper"' : ''; ?> class="<?php echo $use_swiper ? 'swiper xevos-services__swiper' : 'xevos-services__grid'; ?>">
			<div class="<?php echo $use_swiper ? 'swiper-wrapper' : 'xevos-services__grid-inner'; ?>">
				<?php foreach ($sluzby as $i => $s) :
					$url = ! empty($s['url']) ? $s['url'] : '#';
				?>
					<div class="<?php echo $use_swiper ? 'swiper-slide' : 'xevos-services__grid-item'; ?>">
						<a href="<?php echo esc_url($url); ?>" class="xevos-services__card">
							<div class="xevos-services__card-number"><?php echo esc_html(str_pad($i + 1, 2, '0', STR_PAD_LEFT)); ?></div>
							<h3 class="xevos-services__card-title"><?php echo esc_html($s['nazev'] ?? ''); ?></h3>
							<p class="xevos-services__card-text"><?php echo esc_html($s['popis'] ?? ''); ?></p>
							<span class="xevos-services__card-link">
								Zjistit více <img src="<?php echo esc_url(get_theme_file_uri('assets/img/global/card-arrow.svg')); ?>" alt="" class="xevos-services__card-arrow">
							</span>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<?php if ($use_swiper) : ?>
			<div id="services-next" class="xevos-services__nav xevos-services__nav--next"></div>
		<?php endif; ?>
	</div>
	<?php if ($use_swiper) : ?>
		<div class="xevos-section__container">
			<div class="swiper-pagination xevos-services__pagination" id="services-pagination"></div>
		</div>
	<?php endif; ?>
</section>
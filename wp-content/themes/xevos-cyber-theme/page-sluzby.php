<?php
/**
 * Template Name: Služby
 * Figma: Hero + 4 service detail sections with images and CTAs.
 *
 * @package Xevos\CyberTheme
 */

get_header();
?>

<main id="main" class="xevos-main">

	<!-- Hero -->
	<section class="xevos-page-hero xevos-page-hero--short">
		<div class="xevos-page-hero__bg xevos-page-hero__bg--gradient"></div>
		<div class="xevos-page-hero__content">
			<h1><span>Služby,</span> které chrání váš business</h1>
			<p class="xevos-page-hero__subtitle">Kompletní portfolio kybernetické bezpečnosti – od dohledového centra přes penetrační testy až po školení a kybernetickou politiku.</p>
		</div>
	</section>

	<!-- Services detail sections -->
	<?php
	$services = get_field( 'sluzby_sekce' );
	if ( ! $services ) {
		$services = [
			[
				'cislo' => '01',
				'nazev' => 'Dohledové centrum',
				'popis' => 'Security Operations Center – 24/7 monitoring, detekce incidentů a okamžitá reakce na bezpečnostní události. Náš SOC tým nepřetržitě sleduje vaši infrastrukturu a reaguje na hrozby dříve, než způsobí škody.',
				'body'  => [
					'Nepřetržitý monitoring 24/7/365',
					'Detekce a reakce na incidenty v reálném čase',
					'Analýza logů a bezpečnostních událostí',
					'Pravidelné reporty a doporučení',
				],
			],
			[
				'cislo' => '02',
				'nazev' => 'Penetrační testy',
				'popis' => 'Simulace útoku na vaše systémy s cílem identifikovat zranitelná místa a navrhnout účinná opatření k jejich eliminaci. Používáme kombinaci automatizovaných nástrojů a manuálního testování.',
				'body'  => [
					'Testování webových aplikací (OWASP)',
					'Síťové penetrační testy',
					'Testování sociálního inženýrství',
					'Detailní report s prioritizací nálezů',
				],
			],
			[
				'cislo' => '03',
				'nazev' => 'Edukace',
				'popis' => 'Praktická školení a workshopy zaměřené na zvýšení bezpečnostního povědomí. Od základního awareness tréninku po specializované kurzy pro IT profesionály.',
				'body'  => [
					'Kybernetická bezpečnost – obecná',
					'NIS2 a legislativní požadavky',
					'Awareness školení pro zaměstnance',
					'Specializované workshopy pro IT týmy',
				],
			],
			[
				'cislo' => '04',
				'nazev' => 'Kyber politika',
				'popis' => 'Nastavení bezpečnostních politik a procesů v souladu s NIS2, DORA a dalšími regulacemi. Pomůžeme vám vybudovat systém řízení informační bezpečnosti.',
				'body'  => [
					'NIS2 / DORA compliance',
					'Příprava na ISO 27001 certifikaci',
					'Bezpečnostní politiky a směrnice',
					'Gap analýza a roadmapa implementace',
				],
			],
		];
	}
	?>

	<?php foreach ( $services as $i => $s ) : ?>
	<section class="xevos-section<?php echo $i % 2 === 1 ? ' xevos-section--alt' : ''; ?>">
		<div class="xevos-section__container">
			<div class="xevos-service-detail <?php echo $i % 2 === 1 ? 'xevos-service-detail--reverse' : ''; ?>">
				<div class="xevos-service-detail__content">
					<span class="xevos-service-detail__num"><?php echo esc_html( $s['cislo'] ?? str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) ); ?></span>
					<h2><?php echo esc_html( $s['nazev'] ?? '' ); ?></h2>
					<p class="xevos-text-muted"><?php echo esc_html( $s['popis'] ?? '' ); ?></p>
					<?php if ( ! empty( $s['body'] ) ) :
						xevos_component( 'checklist', [ 'items' => $s['body'] ] );
					endif; ?>
					<?php if ( ! empty( $s['cta_url'] ) ) : ?>
						<a href="<?php echo esc_url( $s['cta_url'] ); ?>" class="xevos-btn xevos-btn--primary xevos-btn--sm">
							<span class="xevos-btn__arrow"><svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M5 15L15 5M15 5H7M15 5v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
							<?php echo esc_html( strtoupper( $s['cta_text'] ?? 'ZJISTIT VÍCE' ) ); ?>
						</a>
					<?php endif; ?>
				</div>
				<div class="xevos-service-detail__image">
					<?php if ( ! empty( $s['obrazek'] ) ) : ?>
						<img src="<?php echo esc_url( $s['obrazek']['url'] ); ?>" alt="" style="border-radius:var(--radius-lg);">
					<?php else : ?>
						<div class="xevos-service-detail__placeholder"></div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<?php endforeach; ?>

	<!-- CTA -->
	<section class="xevos-section">
		<div class="xevos-section__container">
			<div class="xevos-emergency-box" style="text-align:center;">
				<h2 style="color:var(--color-white);">Potřebujete konzultaci?</h2>
				<p style="max-width:500px;margin:0 auto 1.5rem;">Rádi vám poradíme s výběrem služby a navrhneme řešení na míru.</p>
				<a href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>" class="xevos-btn xevos-btn--primary">
					<span class="xevos-btn__arrow"><svg width="18" height="18" viewBox="0 0 20 20" fill="none"><path d="M5 15L15 5M15 5H7M15 5v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
					NEZÁVAZNÁ KONZULTACE
				</a>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>

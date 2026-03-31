<?php
/**
 * Template Name: NIS 2
 * NIS2 compliance page with sections, checklist, CTA.
 *
 * @package Xevos\CyberTheme
 */

get_header();
?>

<main id="main" class="xevos-main">

	<section class="xevos-page-hero xevos-page-hero--short">
		<div class="xevos-page-hero__bg xevos-page-hero__bg--gradient"></div>
		<div class="xevos-page-hero__content">
			<h1>NIS 2 <span>Compliance</span></h1>
			<p class="xevos-page-hero__subtitle">Směrnice NIS2 přináší nové povinnosti v oblasti kybernetické bezpečnosti. Pomůžeme vám s implementací požadavků a zajištěním souladu s legislativou.</p>
		</div>
	</section>

	<section class="xevos-section">
		<div class="xevos-section__container">
			<div class="xevos-two-col">
				<div>
					<h2>Co je NIS 2?</h2>
					<p class="xevos-text-muted"><?php echo wp_kses_post( get_field( 'nis2_co_je' ) ?: 'Směrnice NIS2 (Network and Information Security Directive 2) je evropská legislativa, která rozšiřuje povinnosti v oblasti kybernetické bezpečnosti na širší okruh organizací. Stanovuje přísnější bezpečnostní požadavky, povinnost hlášení incidentů a odpovědnost vedení.' ); ?></p>
				</div>
				<div>
					<h2>Koho se týká?</h2>
					<?php
					$koho = get_field( 'nis2_koho_se_tyka' );
					if ( ! $koho ) {
						$koho = [
							[ 'bod' => 'Energetika, doprava, zdravotnictví' ],
							[ 'bod' => 'Digitální infrastruktura a služby' ],
							[ 'bod' => 'Veřejná správa' ],
							[ 'bod' => 'Výrobní podniky (nad 50 zaměstnanců)' ],
							[ 'bod' => 'Potravinářství, vodárenství, odpadové hospodářství' ],
							[ 'bod' => 'Dodavatelé ICT služeb' ],
						];
					}
					xevos_component( 'checklist', [ 'items' => $koho ] );
					?>
				</div>
			</div>
		</div>
	</section>

	<section class="xevos-section xevos-section--alt">
		<div class="xevos-section__container">
			<h2 style="text-align:center;">Jak vám pomůžeme</h2>
			<div class="xevos-services__grid" style="margin-top:2rem;">
				<?php
				$kroky = [
					[ 'num' => '01', 'title' => 'Gap analýza', 'desc' => 'Zhodnotíme aktuální stav vaší kybernetické bezpečnosti oproti požadavkům NIS2.' ],
					[ 'num' => '02', 'title' => 'Implementace', 'desc' => 'Navrhneme a implementujeme potřebná bezpečnostní opatření a procesy.' ],
					[ 'num' => '03', 'title' => 'Dokumentace', 'desc' => 'Připravíme kompletní bezpečnostní dokumentaci a politiky.' ],
					[ 'num' => '04', 'title' => 'Školení', 'desc' => 'Proškolíme management i zaměstnance na nové povinnosti a postupy.' ],
				];
				foreach ( $kroky as $k ) : ?>
					<div class="xevos-services__card">
						<div class="xevos-services__card-number"><?php echo esc_html( $k['num'] ); ?></div>
						<h3 class="xevos-services__card-title"><?php echo esc_html( $k['title'] ); ?></h3>
						<p class="xevos-services__card-text"><?php echo esc_html( $k['desc'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- CTA -->
	<section class="xevos-section">
		<div class="xevos-section__container">
			<div class="xevos-emergency-box" style="text-align:center;">
				<h2 style="color:var(--color-white);">Potřebujete vyřešit NIS2?</h2>
				<p style="max-width:500px;margin:0 auto 1.5rem;">Kontaktujte nás pro nezávaznou konzultaci a zjistěte, jaké kroky potřebujete podniknout.</p>
				<a href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>" class="xevos-btn xevos-btn--primary">
					<span class="xevos-btn__arrow"><svg width="18" height="18" viewBox="0 0 20 20" fill="none"><path d="M5 15L15 5M15 5H7M15 5v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
					VYŘEŠIT NIS2
				</a>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>

<?php
/**
 * Template Name: Partnerství
 * Partners page – logos, descriptions, benefits.
 *
 * @package Xevos\CyberTheme
 */

get_header();
?>

<main id="main" class="xevos-main">

	<section class="xevos-page-hero xevos-page-hero--short">
		<div class="xevos-page-hero__bg xevos-page-hero__bg--gradient"></div>
		<div class="xevos-page-hero__content">
			<h1><span>Partnerství,</span> která posilují bezpečnost</h1>
			<p class="xevos-page-hero__subtitle">Spolupracujeme s předními světovými dodavateli bezpečnostních technologií. Díky partnerstvím vám můžeme nabídnout ověřená řešení na nejvyšší úrovni.</p>
		</div>
	</section>

	<!-- Partner cards -->
	<section class="xevos-section">
		<div class="xevos-section__container">
			<?php
			$partneri = get_field( 'partneri_seznam' );
			if ( $partneri ) : ?>
				<div class="xevos-partner-grid">
					<?php foreach ( $partneri as $p ) : ?>
						<div class="xevos-partner-card">
							<?php if ( ! empty( $p['logo'] ) ) : ?>
								<img src="<?php echo esc_url( $p['logo']['url'] ); ?>" alt="<?php echo esc_attr( $p['nazev'] ?? '' ); ?>" class="xevos-partner-card__logo">
							<?php endif; ?>
							<h3><?php echo esc_html( $p['nazev'] ?? '' ); ?></h3>
							<?php if ( ! empty( $p['uroven'] ) ) : ?>
								<span class="xevos-card__badge"><?php echo esc_html( $p['uroven'] ); ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $p['popis'] ) ) : ?>
								<p class="xevos-text-muted"><?php echo esc_html( $p['popis'] ); ?></p>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<!-- Fallback from Figma -->
				<div class="xevos-partner-grid">
					<?php
					$defaults = [
						[ 'name' => 'Cisco', 'level' => 'Partner' ],
						[ 'name' => 'ESET', 'level' => 'Gold Partner' ],
						[ 'name' => 'Apple', 'level' => 'Technical Partner' ],
						[ 'name' => 'Palo Alto Networks', 'level' => 'Partner' ],
						[ 'name' => 'Pentera', 'level' => 'Partner' ],
					];
					foreach ( $defaults as $d ) : ?>
						<div class="xevos-partner-card">
							<h3><?php echo esc_html( $d['name'] ); ?></h3>
							<span class="xevos-card__badge"><?php echo esc_html( $d['level'] ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<!-- Content from WP editor -->
	<?php while ( have_posts() ) : the_post(); ?>
		<?php if ( get_the_content() ) : ?>
		<section class="xevos-section">
			<div class="xevos-section__container xevos-article-content__inner">
				<div class="xevos-article-content__body"><?php the_content(); ?></div>
			</div>
		</section>
		<?php endif; ?>
	<?php endwhile; ?>

	<!-- CTA -->
	<section class="xevos-section">
		<div class="xevos-section__container">
			<div class="xevos-emergency-box" style="text-align:center;">
				<h2 style="color:var(--color-white);">Chcete se stát partnerem?</h2>
				<p style="max-width:500px;margin:0 auto 1.5rem;">Kontaktujte nás pro informace o partnerských programech.</p>
				<a href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>" class="xevos-btn xevos-btn--primary">
					<span class="xevos-btn__arrow"><svg width="18" height="18" viewBox="0 0 20 20" fill="none"><path d="M5 15L15 5M15 5H7M15 5v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
					KONTAKTOVAT
				</a>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>

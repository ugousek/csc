<?php
/**
 * Template Name: O nás
 * About page – team, mission, values.
 *
 * @package Xevos\CyberTheme
 */

get_header();
?>

<main id="main" class="xevos-main">

	<section class="xevos-page-hero xevos-page-hero--short">
		<div class="xevos-page-hero__bg xevos-page-hero__bg--gradient"></div>
		<div class="xevos-page-hero__content">
			<h1>O <span>nás</span></h1>
			<p class="xevos-page-hero__subtitle">Jsme tým odborníků na kybernetickou bezpečnost s dlouholetými zkušenostmi v oblasti IT bezpečnosti, penetračního testování a compliance.</p>
		</div>
	</section>

	<!-- Mission -->
	<section class="xevos-section">
		<div class="xevos-section__container">
			<div class="xevos-two-col">
				<div>
					<h2>Naše mise</h2>
					<?php
					$mise = get_field( 'o_nas_mise' );
					if ( $mise ) :
						echo wp_kses_post( $mise );
					else : ?>
						<p class="xevos-text-muted">Pomáháme firmám budovat reálnou kybernetickou odolnost. Věříme, že bezpečnost není o strachu, ale o jistotě. Naším cílem je, aby každá organizace měla jasný přehled o svých rizicích a věděla, jak se bránit.</p>
					<?php endif; ?>
				</div>
				<div>
					<?php $img = get_field( 'o_nas_obrazek' ); ?>
					<?php if ( $img ) : ?>
						<img src="<?php echo esc_url( $img['url'] ); ?>" alt="" style="border-radius:var(--radius-lg);">
					<?php else : ?>
						<div class="xevos-service-detail__placeholder"></div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<!-- Team -->
	<?php
	$team = get_field( 'o_nas_tym' );
	if ( $team ) : ?>
	<section class="xevos-section xevos-section--alt">
		<div class="xevos-section__container">
			<h2 style="text-align:center;">Náš tým</h2>
			<div class="xevos-skoleni-lektori" style="margin-top:2rem;">
				<?php foreach ( $team as $member ) : ?>
					<div class="xevos-lektor-card">
						<?php if ( ! empty( $member['foto'] ) ) : ?>
							<img src="<?php echo esc_url( $member['foto']['sizes']['xevos-thumbnail'] ?? $member['foto']['url'] ); ?>" alt="" class="xevos-lektor-card__foto">
						<?php endif; ?>
						<div class="xevos-lektor-card__name"><?php echo esc_html( $member['jmeno'] ?? '' ); ?></div>
						<?php if ( ! empty( $member['pozice'] ) ) : ?>
							<div class="xevos-lektor-card__role"><?php echo esc_html( $member['pozice'] ); ?></div>
						<?php endif; ?>
						<?php if ( ! empty( $member['bio'] ) ) : ?>
							<p class="xevos-lektor-card__bio"><?php echo esc_html( $member['bio'] ); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<!-- Content (WordPress editor) -->
	<?php while ( have_posts() ) : the_post(); ?>
		<?php if ( get_the_content() ) : ?>
		<section class="xevos-section">
			<div class="xevos-section__container xevos-article-content__inner">
				<div class="xevos-article-content__body"><?php the_content(); ?></div>
			</div>
		</section>
		<?php endif; ?>
	<?php endwhile; ?>

	<?php get_template_part( 'template-parts/components/recenze' ); ?>

</main>

<?php get_footer(); ?>

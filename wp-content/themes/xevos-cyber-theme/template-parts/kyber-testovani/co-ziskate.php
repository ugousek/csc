<?php
/**
 * Kyber testování: Co získáte (benefits).
 */

if ( ! get_field( 'kt_benefity_zobrazit' ) ) return;

$heading  = get_field( 'kt_benefity_heading' );
$benefity = get_field( 'kt_benefity_seznam' );

if ( ! $benefity ) return;
?>

<section class="xevos-section">
	<div class="xevos-section__container">
		<?php if ( $heading ) : ?>
			<?php xevos_component( 'section-heading', [ 'title' => $heading ] ); ?>
		<?php endif; ?>

		<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.5rem;">
			<?php foreach ( $benefity as $b ) : ?>
				<div class="xevos-services__card">
					<?php if ( ! empty( $b['ikona'] ) ) : ?>
						<div class="xevos-services__card-icon">
							<img src="<?php echo esc_url( $b['ikona']['url'] ); ?>" alt="">
						</div>
					<?php endif; ?>
					<h3 class="xevos-services__card-title"><?php echo esc_html( $b['nazev'] ?? '' ); ?></h3>
					<p class="xevos-services__card-text"><?php echo esc_html( $b['popis'] ?? '' ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
/**
 * Kyber testování: Postup testování (stepper/timeline).
 */

if ( ! get_field( 'kt_postup_zobrazit' ) ) return;

$heading = get_field( 'kt_postup_heading' );
$kroky   = get_field( 'kt_postup_kroky' );

if ( ! $kroky ) return;
?>

<section class="xevos-section" style="background:var(--color-primary);color:#fff;">
	<div class="xevos-section__container">
		<?php if ( $heading ) : ?>
			<h2 style="text-align:center;color:#fff;margin-bottom:3rem;"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:2rem;text-align:center;">
			<?php foreach ( $kroky as $i => $krok ) : ?>
				<div>
					<div style="width:48px;height:48px;border-radius:50%;background:var(--color-secondary);display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:1.25rem;margin-bottom:1rem;">
						<?php echo esc_html( $i + 1 ); ?>
					</div>
					<h3 style="color:#fff;font-size:1.125rem;"><?php echo esc_html( $krok['nazev'] ?? '' ); ?></h3>
					<p style="color:rgba(255,255,255,.7);font-size:.875rem;"><?php echo esc_html( $krok['popis'] ?? '' ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

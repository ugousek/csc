<?php
/**
 * Kyber testování: Penetrační testy — 2-column layout (text + image).
 * Figma: heading + description left, dashboard image right.
 */

$show = get_field( 'kt_pentest_zobrazit' );
if ( $show === false ) return;

$heading = get_field( 'kt_pentest_heading' ) ?: '';
$text    = get_field( 'kt_pentest_text' ) ?: '';
$image   = get_field( 'kt_pentest_obrazek' );
$img_url = $image ? $image['url'] : '';
$vyhody  = get_field( 'kt_pentest_vyhody' ) ?: [];

/* Skrýt celou sekci, pokud není vyplněný ani heading, ani vyhody. */
if ( $heading === '' && $text === '' && empty( $vyhody ) ) {
	return;
}
?>

<section class="xevos-section xevos-kt-pentest">
	<div class="xevos-section__container">
		<div class="xevos-hp-recenze__header">
			<h2><?php echo esc_html( $heading ); ?></h2>
			<p><?php echo wp_kses_post( strip_tags( $text, '<strong><b><em><br>' ) ); ?></p>
		</div>

		<div class="xevos-kt-pentest__grid">
			<?php foreach ( $vyhody as $v ) : ?>
				<div class="xevos-services__card">
					<h3 class="xevos-services__card-title"><?php echo esc_html( $v['nazev'] ?? '' ); ?></h3>
					<p class="xevos-services__card-text"><?php echo esc_html( $v['popis'] ?? '' ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

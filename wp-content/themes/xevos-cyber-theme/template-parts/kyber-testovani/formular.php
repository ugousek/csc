<?php
/**
 * Kyber testování: Poptávkový formulář.
 */

$form_shortcode = get_field( 'kt_formular_shortcode' );
$heading        = get_field( 'kt_formular_heading' ) ?: __( 'Poptávkový formulář', 'xevos-cyber' );
?>

<section class="xevos-section" id="formular" style="background:#f8fafc;">
	<div class="xevos-section__container" style="max-width:740px;">
		<?php xevos_component( 'section-heading', [ 'title' => $heading ] ); ?>

		<?php if ( $form_shortcode ) : ?>
			<?php echo do_shortcode( $form_shortcode ); ?>
		<?php else : ?>
			<p style="text-align:center;color:var(--color-text-light);">
				<?php esc_html_e( 'Formulář bude dostupný po konfiguraci Contact Form 7.', 'xevos-cyber' ); ?>
			</p>
		<?php endif; ?>
	</div>
</section>

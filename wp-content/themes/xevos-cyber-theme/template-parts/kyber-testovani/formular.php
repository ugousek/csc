<?php
/**
 * Kyber testování: Poptávkový formulář.
 */

$heading = get_field( 'kt_formular_heading' ) ?: __( 'Poptávkový formulář', 'xevos-cyber' );
?>

<section class="xevos-section" id="formular" style="background:#f8fafc;">
	<div class="xevos-section__container" style="max-width:740px;">
		<?php xevos_component( 'section-heading', [ 'title' => $heading ] ); ?>
		<p style="text-align:center;color:var(--color-text-light);">
			<?php esc_html_e( 'Formulář je dostupný na hlavní stránce kybernetického testování.', 'xevos-cyber' ); ?>
		</p>
	</div>
</section>

<?php
/**
 * Kyber testování: Webové aplikace section.
 */

if ( ! get_field( 'kt_webapps_zobrazit' ) ) return;

$heading = get_field( 'kt_webapps_heading' );
$text    = get_field( 'kt_webapps_text' );
$body    = get_field( 'kt_webapps_seznam' );
?>

<section class="xevos-section">
	<div class="xevos-section__container">
		<?php if ( $heading ) : ?>
			<?php xevos_component( 'section-heading', [ 'title' => $heading ] ); ?>
		<?php endif; ?>
		<?php if ( $text ) : ?>
			<div style="max-width:740px;margin:0 auto;"><?php echo wp_kses_post( $text ); ?></div>
		<?php endif; ?>
		<?php if ( $body ) : ?>
			<ul style="max-width:740px;margin:1.5rem auto 0;">
				<?php foreach ( $body as $item ) : ?>
					<li><?php echo esc_html( $item['bod'] ?? '' ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>

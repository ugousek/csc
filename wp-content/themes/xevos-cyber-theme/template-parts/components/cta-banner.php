<?php
/**
 * Component: CTA Banner.
 *
 * @param array $args {
 *   @type string $title    Heading text.
 *   @type string $text     Body text.
 *   @type string $cta_text Button text.
 *   @type string $cta_url  Button URL.
 *   @type string $variant  'dark'|'light'. Default 'dark'.
 * }
 */

$title    = $args['title'] ?? '';
$text     = $args['text'] ?? '';
$cta_text = $args['cta_text'] ?? '';
$cta_url  = $args['cta_url'] ?? '#';
$variant  = $args['variant'] ?? 'dark';

if ( ! $title ) return;
?>

<section class="xevos-cta-section xevos-section <?php echo $variant === 'light' ? 'xevos-cta-section--light' : ''; ?>">
	<div class="xevos-section__container">
		<h2 class="xevos-cta-section__title"><?php echo esc_html( $title ); ?></h2>
		<?php if ( $text ) : ?>
			<p class="xevos-cta-section__text"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>
		<?php if ( $cta_text ) : ?>
			<?php xevos_component( 'button', [
				'url'     => $cta_url,
				'text'    => $cta_text,
				'variant' => $variant === 'dark' ? 'outline' : 'primary',
				'size'    => 'lg',
			] ); ?>
		<?php endif; ?>
	</div>
</section>

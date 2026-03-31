<?php

/**
 * Component: Contact info items (address, phone, email).
 * Shared on: kontakt, single-skoleni, kyber-testování.
 *
 * @param array $args {
 *   @type string $class  Extra CSS class on wrapper.
 * }
 */

$adresa  = xevos_get_option('adresa', 'Mostárenská 1156/38, 703 00 Ostrava');
$telefon = xevos_get_option('telefon', '+420 591 140 315');
$email   = xevos_get_option('email', 'hello@xevos.eu');
$class   = $args['class'] ?? '';

$wrap_class = 'xevos-order-summary__contact xevos-contact-info';
if ($class) {
	$wrap_class .= ' ' . $class;
}
?>

<div class="<?php echo esc_attr($wrap_class); ?>">
	<?php if ($adresa) : ?>
		<div class="xevos-contact-info__item">
			<span class="xevos-contact-info__icon">
				<img src="http://localhost/csc/wp-content/themes/xevos-cyber-theme/assets/img/global/pin-drop.svg" alt="" class="xevos-kontakt__icon">
			</span>
			<a href="https://www.google.com/maps/place/XEVOS+Solutions/@49.8163154,18.2645651,19z/data=!3m1!4b1!4m6!3m5!1s0x4713e3435b1b7fef:0xdca3ef2a93b12439!8m2!3d49.8163145!4d18.2652088!16s%2Fg%2F1tghbwgx?entry=ttu&g_ep=EgoyMDI2MDMyNC4wIKXMDSoASAFQAw%3D%3D" target="_blank" rel="noopener"><?php echo esc_html($adresa); ?></a>
		</div>
	<?php endif; ?>
	<?php if ($telefon) : ?>
		<div class="xevos-contact-info__item">
			<span class="xevos-contact-info__icon">
				<img src="http://localhost/csc/wp-content/themes/xevos-cyber-theme/assets/img/global/phone.svg" alt="" class="xevos-kontakt__icon">
			</span>
			<a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $telefon)); ?>"><?php echo esc_html($telefon); ?></a>
		</div>
	<?php endif; ?>
	<?php if ($email) : ?>
		<div class="xevos-contact-info__item">
			<span class="xevos-contact-info__icon">
				<img src="http://localhost/csc/wp-content/themes/xevos-cyber-theme/assets/img/global/mail.svg" alt="" class="xevos-kontakt__icon">
			</span>
			<a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
		</div>
	<?php endif; ?>
</div>
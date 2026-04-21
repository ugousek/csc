<?php

/**
 * Component: Inquiry form (Poptávkový formulář).
 * Shared between page-nis2, page-sluzby, page-o-nas, page-kyberneticke-testovani.
 *
 * Required arg:
 *   $args['prefix'] = 'kt' | 'nis' | 'sl' | 'on' — ACF field name prefix for the current page.
 */

$args   = isset( $args ) && is_array( $args ) ? $args : [];
$prefix = ! empty( $args['prefix'] ) ? (string) $args['prefix'] : '';

$get = function ( string $key, string $fallback ) use ( $prefix ) {
	$val = $prefix ? get_field( $prefix . '_form_' . $key ) : '';
	return $val !== '' && $val !== null ? $val : $fallback;
};

$label_jmeno      = $get( 'label_jmeno',    'Jméno' );
$label_prijmeni   = $get( 'label_prijmeni', 'Příjmení' );
$label_telefon    = $get( 'label_telefon',  'Telefon' );
$label_email      = $get( 'label_email',    'E-mail' );
$label_firma      = $get( 'label_firma',    'Firma' );
$label_druh       = $get( 'label_druh',     'Druh testu' );
$label_zprava     = $get( 'label_zprava',   'Zpráva' );
$label_submit     = $get( 'label_submit',   'ODESLAT POPTÁVKU' );
$select_placeholder = $get( 'select_placeholder', '– vyberte –' );

$select_options_raw = $prefix ? get_field( $prefix . '_form_select_options' ) : null;
$select_options = [];
if ( is_array( $select_options_raw ) ) {
	foreach ( $select_options_raw as $opt ) {
		$label = isset( $opt['label'] ) ? trim( (string) $opt['label'] ) : '';
		if ( $label !== '' ) {
			$select_options[] = $label;
		}
	}
}
if ( empty( $select_options ) ) {
	$select_options = [
		'Kybernetická bezpečnost – obecná',
		'Penetrační testy',
		'Audit infrastruktury',
	];
}
?>
<form class="xevos-order-section" method="post" id="xevos-inquiry-form">
	<input type="hidden" name="action" value="xevos_inquiry_form">
	<input type="hidden" name="form_prefix" value="<?php echo esc_attr( $prefix ); ?>">
	<?php wp_nonce_field( 'xevos_inquiry', 'xevos_inquiry_nonce' ); ?>
	<div class="xevos-kontanis__form">
		<div class="xevos-form-row">
			<div class="xevos-form__group">
				<label class="xevos-form__label"><?php echo esc_html( $label_jmeno ); ?> <span class="xevos-form__required">*</span></label>
				<input type="text" class="xevos-form__input" name="jmeno" required>
			</div>
			<div class="xevos-form__group">
				<label class="xevos-form__label"><?php echo esc_html( $label_prijmeni ); ?> <span class="xevos-form__required">*</span></label>
				<input type="text" class="xevos-form__input" name="prijmeni" required>
			</div>
		</div>
		<div class="xevos-form-row">
			<div class="xevos-form__group">
				<label class="xevos-form__label"><?php echo esc_html( $label_telefon ); ?> <span class="xevos-form__required">*</span></label>
				<input type="tel" class="xevos-form__input" name="telefon" required>
			</div>
			<div class="xevos-form__group">
				<label class="xevos-form__label"><?php echo esc_html( $label_email ); ?> <span class="xevos-form__required">*</span></label>
				<input type="email" class="xevos-form__input" name="email" required>
			</div>
		</div>
		<div class="xevos-form-row">
			<div class="xevos-form__group">
				<label class="xevos-form__label"><?php echo esc_html( $label_firma ); ?></label>
				<input type="text" class="xevos-form__input" name="firma">
			</div>
			<div class="xevos-form__group">
				<label class="xevos-form__label"><?php echo esc_html( $label_druh ); ?> <span class="xevos-form__required">*</span></label>
				<select class="xevos-form__input" name="druh_testu" required>
					<option value=""><?php echo esc_html( $select_placeholder ); ?></option>
					<?php foreach ( $select_options as $opt_label ) : ?>
						<option value="<?php echo esc_attr( $opt_label ); ?>"><?php echo esc_html( $opt_label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div class="xevos-form__group">
			<label class="xevos-form__label"><?php echo esc_html( $label_zprava ); ?></label>
			<textarea class="xevos-form__textarea" name="zprava" rows="4"></textarea>
		</div>
		<div class="xevos-form__hp"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
		<div id="xevos-inquiry-message" class="xevos-order-message" style="display:none;"></div>
	</div>

	<div class="xevos-order-summary">
		<?php xevos_component('contact-info'); ?>

		<button type="submit" id="xevos-inquiry-submit" class="xevos-btn xevos-btn--primary">
			<span class="xevos-btn__arrow"><svg width="18" height="18" viewBox="0 0 20 20" fill="none">
					<path d="M5 15L15 5M15 5H7M15 5v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
				</svg></span>
			<?php echo esc_html( $label_submit ); ?>
		</button>
	</div>
</form>

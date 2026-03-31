<?php
/**
 * Component: Checklist with cyan circle + checkmark icons.
 * Used on: kyber-testování, školení detail, služby, NIS2.
 *
 * @param array $args {
 *   @type array  $items  Array of strings or ['bod' => '...'] items.
 *   @type string $class  Extra CSS class on <ul>.
 * }
 */

$items = $args['items'] ?? [];
$class = $args['class'] ?? '';

if ( empty( $items ) ) return;

$ul_class = 'xevos-checklist';
if ( $class ) {
	$ul_class .= ' ' . $class;
}
?>

<ul class="<?php echo esc_attr( $ul_class ); ?>">
	<?php foreach ( $items as $item ) :
		$text = is_array( $item ) ? ( $item['bod'] ?? '' ) : $item;
		if ( ! $text ) continue;
		$filter_id = 'filter_check_' . sanitize_title( $text );
	?>
		<li>
			<span class="xevos-checklist__icon" aria-hidden="true">
				<svg xmlns="http://www.w3.org/2000/svg" width="23" height="17" viewBox="0 0 23 17" fill="none">
					<g filter="url(#<?php echo esc_attr( $filter_id ); ?>)">
						<path d="M22.6 2.8L8.5 16.9L0 8.4L2.8 5.6L8.5 11.3L19.8 0L22.6 2.8Z" fill="#00BBFF"/>
					</g>
					<defs>
						<filter id="<?php echo esc_attr( $filter_id ); ?>" x="0" y="0" width="22.6001" height="20.4" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
							<feFlood flood-opacity="0" result="BackgroundImageFix"/>
							<feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
							<feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
							<feOffset dy="4"/>
							<feGaussianBlur stdDeviation="1.75"/>
							<feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1"/>
							<feColorMatrix type="matrix" values="0 0 0 0 0.4298 0 0 0 0 0.82894 0 0 0 0 1 0 0 0 1 0"/>
							<feBlend mode="normal" in2="shape" result="effect1_innerShadow"/>
						</filter>
					</defs>
				</svg>
			</span>
			<?php echo esc_html( $text ); ?>
		</li>
	<?php endforeach; ?>
</ul>

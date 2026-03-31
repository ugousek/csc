<?php
/**
 * Component: Section Heading.
 *
 * @param array $args {
 *   @type string $title     Main heading.
 *   @type string $subtitle  Subtitle / overline text.
 *   @type string $tag       HTML tag. Default 'h2'.
 *   @type string $align     'left'|'center'. Default 'center'.
 *   @type bool   $decoration Show decorative line. Default true.
 * }
 */

$title      = $args['title'] ?? '';
$subtitle   = $args['subtitle'] ?? '';
$tag        = $args['tag'] ?? 'h2';
$align      = $args['align'] ?? 'center';
$decoration = $args['decoration'] ?? true;

if ( ! $title ) return;
?>

<div class="xevos-section-heading xevos-section-heading--<?php echo esc_attr( $align ); ?>">
	<?php if ( $subtitle ) : ?>
		<p class="xevos-section-heading__subtitle"><?php echo esc_html( $subtitle ); ?></p>
	<?php endif; ?>
	<?php if ( $decoration ) : ?>
		<span class="xevos-section-heading__line"></span>
	<?php endif; ?>
	<<?php echo esc_html( $tag ); ?> class="xevos-section-heading__title"><?php echo esc_html( $title ); ?></<?php echo esc_html( $tag ); ?>>
</div>

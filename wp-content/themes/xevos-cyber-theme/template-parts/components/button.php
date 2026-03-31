<?php
/**
 * Component: Button.
 *
 * @param array $args {
 *   @type string $url      Link URL.
 *   @type string $text     Button text.
 *   @type string $variant  'primary'|'secondary'|'outline'|'ghost'. Default 'primary'.
 *   @type string $size     'sm'|''|'lg'. Default ''.
 *   @type string $class    Additional CSS classes.
 *   @type string $target   Link target. Default ''.
 *   @type bool   $full     Full width. Default false.
 * }
 */

$url     = $args['url'] ?? '#';
$text    = $args['text'] ?? '';
$variant = $args['variant'] ?? 'primary';
$size    = $args['size'] ?? '';
$class   = $args['class'] ?? '';
$target  = $args['target'] ?? '';
$full    = $args['full'] ?? false;

if ( ! $text ) return;

$classes = [ 'xevos-btn', "xevos-btn--{$variant}" ];
if ( $size )  $classes[] = "xevos-btn--{$size}";
if ( $full )  $classes[] = 'xevos-btn--full';
if ( $class ) $classes[] = $class;

$target_attr = $target ? ' target="' . esc_attr( $target ) . '" rel="noopener"' : '';
?>

<a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $target_attr; ?>>
	<?php echo esc_html( $text ); ?>
</a>

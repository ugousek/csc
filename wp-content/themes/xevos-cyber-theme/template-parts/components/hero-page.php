<?php
/**
 * Component: Page hero – two-column (title left + image right).
 * Shared across kyber-testování, školení detail, eventy, and other pages.
 *
 * Pass data via $args:
 *   heading      – (required) H1 text
 *   description  – Paragraph below heading
 *   cta_text     – Button label
 *   cta_url      – Button href
 *   image_url    – Right-column image src
 *   css_class    – Extra class on <section> (e.g. 'xevos-skoleni-hero')
 *   loading      – img loading attribute ('lazy' default, 'eager' for above-fold)
 *   image_mask   – bool, show/hide radial mask on image (default true)
 */

$heading    = $args['heading'] ?? '';
$subheading = $args['subheading'] ?? '';
$desc       = $args['description'] ?? '';
$cta_text   = $args['cta_text'] ?? '';
$cta_url    = $args['cta_url'] ?? '';
$image_url  = $args['image_url'] ?? '';
$css_class  = $args['css_class'] ?? '';
$loading    = $args['loading'] ?? 'lazy';
$image_mask = $args['image_mask'] ?? true;

if ( ! $heading ) return;

$section_classes = 'xevos-page-hero xevos-blog-hero';
if ( $css_class ) {
	$section_classes .= ' ' . $css_class;
}
?>

<section class="<?php echo esc_attr( $section_classes ); ?>">
	<div class="xevos-page-hero__bg xevos-page-hero__bg--gradient"></div>
	<div class="xevos-section__container">
		<div class="xevos-blog-hero__grid">
			<div class="xevos-blog-hero__content">
				<h1><?php echo wp_kses( $heading, [ 'strong' => [], 'em' => [], 'span' => [ 'class' => [], 'style' => [] ], 'br' => [] ] ); ?></h1>
				<?php if ( $subheading ) : ?>
					<p class="xevos-blog-hero__subheading"><?php echo esc_html( $subheading ); ?></p>
				<?php endif; ?>
				<?php if ( $desc ) : ?>
					<p class="xevos-blog-hero__desc"><?php echo wp_kses( strip_tags( $desc, '<strong><b><em><br><span>' ), [ 'strong' => [], 'b' => [], 'em' => [], 'span' => [ 'class' => [], 'style' => [] ], 'br' => [] ] ); ?></p>
				<?php endif; ?>
				<?php if ( $cta_text ) : ?>
					<a href="<?php echo esc_url( $cta_url ); ?>" class="xevos-btn xevos-btn--primary" style="margin-top:2rem;">
						<span class="xevos-btn__arrow">
							<svg width="18" height="18" viewBox="0 0 20 20" fill="none">
								<path d="M5 15L15 5M15 5H7M15 5v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</span>
						<?php echo esc_html( strtoupper( $cta_text ) ); ?>
					</a>
				<?php endif; ?>
			</div>
			<?php if ( $image_url ) : ?>
			<div class="xevos-blog-hero__image<?php echo $image_mask ? '' : ' xevos-blog-hero__image--no-mask'; ?>">
				<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $heading ); ?>" loading="<?php echo esc_attr( $loading ); ?>">
			</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php
/**
 * Template Name: Zásady ochrany osobních údajů
 * Figma node 685:85: Legal page with TOC sidebar, content area.
 *
 * @package Xevos\CyberTheme
 */

get_header();
?>

<main id="main" class="xevos-main xevos-main--glows">
	<div class="xevos-glow-blob xevos-glow-blob--left xevos-glow-blob--lg" style="top:-400px;"></div>
	<div class="xevos-glow-blob xevos-glow-blob--top xevos-glow-blob--lg"></div>
	<div class="xevos-glow-blob xevos-glow-blob--right xevos-glow-blob--lg" style="top:800px;"></div>
	<div class="xevos-glow-blob xevos-glow-blob--left-second xevos-glow-blob--lg" style="bottom:0;"></div>
	<?php echo xevos_breadcrumbs(); ?>

	<?php while ( have_posts() ) : the_post(); ?>
		<?php
		$nadpis = get_field('zasady_nadpis') ?: get_the_title();
		$obsah  = get_field('zasady_obsah');
		?>
		<article class="xevos-section xevos-legal">
			<div class="xevos-section__container xevos-legal__container">
				<h1 class="xevos-legal__title"><?php echo esc_html($nadpis); ?></h1>

				<div class="xevos-legal-content" id="legal-content">
					<?php if ($obsah) :
						echo wp_kses_post($obsah);
					else :
						the_content();
					endif; ?>
				</div>
			</div>
		</article>
	<?php endwhile; ?>


<script>
document.addEventListener('DOMContentLoaded', () => {
	const content = document.getElementById('legal-content');
	const toc = document.getElementById('toc');
	if (!content || !toc) return;

	const headings = content.querySelectorAll('h2, h3');
	if (headings.length < 2) return;

	let html = '<h4><?php esc_html_e( 'Obsah', 'xevos-cyber' ); ?></h4><ol>';
	headings.forEach((h, i) => {
		const id = `section-${i}`;
		h.id = id;
		const indent = h.tagName === 'H3' ? ' style="margin-left:1.5rem;"' : '';
		html += `<li${indent}><a href="#${id}">${h.textContent}</a></li>`;
	});
	html += '</ol>';
	toc.innerHTML = html;
});
</script>

<?php
get_footer();

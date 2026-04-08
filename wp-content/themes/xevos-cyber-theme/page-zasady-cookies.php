<?php
/**
 * Template Name: Zásady cookies
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

	<?php while ( have_posts() ) : the_post(); ?>
		<article class="xevos-section xevos-legal">
			<div class="xevos-section__container xevos-legal__container">
				<h1 class="xevos-legal__title"><?php the_title(); ?></h1>


<div class="xevos-legal-content" id="legal-content">
					<?php
					$obsah = get_field('legal_obsah');
					if ( $obsah ) :
						echo wp_kses_post($obsah);
					elseif ( get_the_content() ) :
						the_content();
					else : ?>
						<h2>Co jsou cookies</h2>
						<p>Cookies jsou malé textové soubory, které se ukládají do vašeho prohlížeče při návštěvě webových stránek. Slouží k zajištění správného fungování webu, analýze návštěvnosti a personalizaci obsahu.</p>

						<h2>Jaké cookies používáme</h2>

						<h3>Nezbytné cookies</h3>
						<p>Tyto cookies jsou nutné pro správné fungování webu. Nelze je vypnout. Zahrnují cookies pro správu relace, bezpečnostní tokeny a preference souhlasu s cookies.</p>

						<h3>Analytické cookies</h3>
						<p>Pomáhají nám porozumět, jak návštěvníci používají naše stránky. Data jsou anonymizována. Používáme Google Analytics 4.</p>

						<h3>Marketingové cookies</h3>
						<p>Slouží k zobrazování relevantních reklam a měření účinnosti reklamních kampaní. Používají se pouze s vaším výslovným souhlasem.</p>

						<h2>Správa cookies</h2>
						<p>Při první návštěvě webu vás požádáme o souhlas s používáním volitelných cookies. Svůj souhlas můžete kdykoliv odvolat nebo změnit v nastavení cookies na našem webu nebo v nastavení vašeho prohlížeče.</p>

						<h2>Doba uchovávání</h2>
						<p>Nezbytné cookies: po dobu relace nebo max. 1 rok. Analytické cookies: max. 26 měsíců. Marketingové cookies: max. 12 měsíců.</p>

						<h2>Další informace</h2>
						<p>Podrobnosti o zpracování osobních údajů naleznete v <a href="<?php echo esc_url( home_url( '/zasady-ochrany-osobnich-udaju/' ) ); ?>">Zásadách ochrany osobních údajů</a>. V případě dotazů nás kontaktujte na hello@xevos.eu.</p>
					<?php endif; ?>
				</div>
			</div>
		</article>
	<?php endwhile; ?>


<?php
get_footer();

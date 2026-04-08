<?php
/**
 * Template Name: Obchodní podmínky
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
						<h2>Úvodní ustanovení</h2>
						<p>Tyto obchodní podmínky společnosti XEVOS Solutions s.r.o., IČO: 14184290, se sídlem Mostárenská 1156/38, 703 00 Ostrava (dále jen „Poskytovatel"), upravují vzájemná práva a povinnosti smluvních stran vzniklé v souvislosti s objednávkou služeb.</p>

						<h2>Předmět smlouvy</h2>
						<p>Předmětem smlouvy je poskytování služeb v oblasti kybernetické bezpečnosti, včetně školení, penetračních testů, auditů a poradenství dle aktuální nabídky Poskytovatele.</p>

						<h2>Objednávka a uzavření smlouvy</h2>
						<p>Objednávka je závazná okamžikem jejího odeslání prostřednictvím objednávkového formuláře. Smlouva je uzavřena potvrzením objednávky ze strany Poskytovatele.</p>

						<h2>Cena a platební podmínky</h2>
						<p>Ceny jsou uvedeny včetně DPH, není-li výslovně uvedeno jinak. Platba probíhá na základě faktury se splatností 14 dní, případně online platbou při objednávce.</p>

						<h2>Storno podmínky</h2>
						<p>Objednavatel může bezplatně zrušit objednávku školení nejpozději 7 pracovních dnů před termínem konání. Při pozdějším zrušení může být účtován storno poplatek ve výši 50 % ceny.</p>

						<h2>Reklamace</h2>
						<p>V případě nespokojenosti s poskytnutou službou je Objednavatel oprávněn uplatnit reklamaci písemně do 14 dnů od poskytnutí služby na e-mail hello@xevos.eu.</p>

						<h2>Závěrečná ustanovení</h2>
						<p>Tyto obchodní podmínky nabývají účinnosti dnem jejich zveřejnění. Poskytovatel si vyhrazuje právo na jejich změnu. Vztahy těmito podmínkami výslovně neupravené se řídí právním řádem České republiky.</p>
					<?php endif; ?>
				</div>
			</div>
		</article>
	<?php endwhile; ?>



<?php
get_footer();

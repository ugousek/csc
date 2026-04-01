<?php
/**
 * Homepage: Řekli o nás – reviews slider.
 * Figma node 271:17056: Centered heading, 3 review cards with big quote mark + nav arrows.
 */

$show = get_field( 'recenze_zobrazit_sekci' );
if ( $show === false && $show !== null ) return;

$heading  = $args['heading']  ?? get_field( 'recenze_heading' ) ?: 'Řekli o nás';
$subtitle = $args['subtitle'] ?? get_field( 'recenze_popis' ) ?: '';

$count = (int) ( get_field( 'recenze_pocet' ) ?: 9 );

$query = new WP_Query( [
	'post_type'      => 'recenze',
	'posts_per_page' => $count,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
] );

$has_recenze = $query->have_posts();

// Fallback reviews when DB is empty
$fallback_recenze = [
	'Nechtěl jsem marketing, chtěl jsem důkaz. Dostali jsme jasný report, re-test a hlavně klid, že víme, kde jsme slabí. Rychlý, věcný, profesionální.',
	'Školení bylo praktické a srozumitelné i pro netechnické zaměstnance. Oceňujeme, že lektoři pracovali s reálnými příklady z naší branže.',
	'Penetrační test odhalil zranitelnosti, o kterých jsme neměli tušení. Díky jasné zprávě s prioritami jsme věděli, co řešit první.',
	'Implementace bezpečnostní politiky proběhla hladce. Tým XEVOS rozuměl našim procesům a navrhl řešení, které nebrzdí provoz.',
	'Spolupráce na NIS2 compliance nám ušetřila měsíce práce. Vše bylo připravené na audit bez stresu.',
	'Ocenili jsme hlavně přístup — žádné strašení, ale konkrétní kroky. Víme přesně, jak na tom jsme a co zlepšit.',
	'Pravidelné testování nám dává jistotu, že investice do bezpečnosti fungují. Doporučujeme každé firmě, která to myslí vážně.',
	'Reakce na incident byla bleskurychlá. Během hodin jsme měli situaci pod kontrolou a jasný postup, jak pokračovat.',
	'Výborná komunikace po celou dobu projektu. Vždy jsme věděli, v jaké jsme fázi a co nás čeká.',
];
?>

<section class="xevos-section xevos-hp-recenze">
	<div class="xevos-section__container">

		<!-- Centered heading -->
		<div class="xevos-hp-recenze__header">
			<h2><?php echo esc_html( $heading ); ?></h2>
			<?php if ( $subtitle ) : ?>
				<p class="xevos-hp-recenze__desc-mixed"><?php echo wp_kses( $subtitle, [ 'strong' => [] ] ); ?></p>
			<?php else : ?>
				<p class="xevos-hp-recenze__desc-mixed">Spolupracujeme s firmami, které chtějí mít v <strong>kybernetické bezpečnosti jasno.</strong> <strong>Zpětná vazba od klientů</strong> je pro nás potvrzením práce v reálném provozu.</p>
			<?php endif; ?>
		</div>

		<!-- Reviews carousel with nav arrows -->
		<div class="xevos-hp-recenze__carousel">
			<button class="xevos-nav-arrow xevos-nav-arrow--prev" aria-label="Předchozí">
				<svg width="12" height="20" viewBox="0 0 12 20" fill="none"><path d="M10 2L2 10l8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>

			<div class="swiper xevos-recenze-swiper" id="recenze-swiper">
				<div class="swiper-wrapper">
					<?php if ( $has_recenze ) :
						while ( $query->have_posts() ) : $query->the_post();
							get_template_part( 'template-parts/components/card-recenze' );
						endwhile;
						wp_reset_postdata();
					else :
						foreach ( $fallback_recenze as $fb_text ) : ?>
							<div class="xevos-recenze-card swiper-slide">
								<div class="xevos-recenze-card__inner">
									<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/global/anotace.svg' ) ); ?>" alt="" class="xevos-recenze-card__quote" aria-hidden="true">
									<blockquote class="xevos-recenze-card__text"><?php echo esc_html( $fb_text ); ?></blockquote>
								</div>
							</div>
						<?php endforeach;
					endif; ?>
				</div>
				<div class="swiper-pagination xevos-recenze-pagination"></div>
			</div>

			<button class="xevos-nav-arrow xevos-nav-arrow--next" aria-label="Další">
				<svg width="12" height="20" viewBox="0 0 12 20" fill="none"><path d="M2 2l8 8-8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
		</div>

	</div>
</section>

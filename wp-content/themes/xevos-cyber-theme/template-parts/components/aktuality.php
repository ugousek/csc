<?php

/**
 * Component: Kyber aktuality – latest articles.
 * Shared between homepage and kyberneticke-testovani page.
 * Figma node 271:16949: Centered heading, 3 article cards with nav arrows, CTA button.
 */

$show = get_field('aktuality_zobrazit_sekci');
if ($show === false && $show !== null) return;

$heading = get_field('aktuality_heading') ?: (get_field('kt_aktuality_heading') ?: 'Kyber aktuality');
$desc    = get_field('aktuality_text') ?: (get_field('kt_aktuality_text') ?: 'Aktuality ze světa kybernetické bezpečnosti, legislativy a praxe. Sdílíme novinky k NIS2, ISO normám, reálným incidentům i změnám, které mají dopad na firmy a jejich bezpečnost.');
$count   = (int) (get_field('aktuality_pocet') ?: 9);

$query = new WP_Query([
	'post_type'      => 'aktualita',
	'post_status'    => 'publish',
	'posts_per_page' => $count,
]);

$has_aktuality = $query->have_posts();

$fallback_aktuality = [
	[ 'title' => 'Phishing v roce 2026: Nové trendy a jak se bránit', 'excerpt' => 'Phishingové útoky jsou stále sofistikovanější. AI generovaný obsah a deep fake technologie zvyšují úspěšnost útoků.', 'img' => 1 ],
	[ 'title' => 'GDPR a kybernetická bezpečnost: Propojení povinností', 'excerpt' => 'GDPR a kybernetická bezpečnost jsou úzce propojeny. Jak zajistit soulad s oběma regulacemi současně?', 'img' => 2 ],
	[ 'title' => 'Kybernetická bezpečnost v cloudu: Best practices 2026', 'excerpt' => 'S přechodem do cloudu přibývají nové výzvy. Přinášíme přehled best practices pro bezpečný cloud.', 'img' => 3 ],
	[ 'title' => 'NIS2: Co musí firmy splnit do konce roku?', 'excerpt' => 'Směrnice NIS2 přináší nové povinnosti. Připravte se na deadline a zjistěte, co vás čeká.', 'img' => 1 ],
	[ 'title' => 'Ransomware útoky: Jak minimalizovat škody', 'excerpt' => 'Ransomware zůstává jednou z největších hrozeb. Klíčové kroky pro prevenci a reakci na útok.', 'img' => 2 ],
	[ 'title' => 'Zero Trust architektura: Budoucnost firemní bezpečnosti', 'excerpt' => 'Model Zero Trust mění přístup k zabezpečení sítí. Proč byste měli zvážit jeho implementaci.', 'img' => 3 ],
	[ 'title' => 'Sociální inženýrství: Nejslabší článek je člověk', 'excerpt' => 'Útočníci cílí na zaměstnance. Jak školení a awareness programy snižují riziko úspěšného útoku.', 'img' => 1 ],
	[ 'title' => 'Penetrační testy: Proč je dělat pravidelně?', 'excerpt' => 'Jednorázový pentest nestačí. Pravidelné testování odhaluje nové zranitelnosti dříve než útočníci.', 'img' => 2 ],
	[ 'title' => 'Bezpečnost mobilních zařízení ve firmě', 'excerpt' => 'BYOD politiky a MDM řešení. Jak ochránit firemní data na zařízeních zaměstnanců.', 'img' => 3 ],
];
?>

<section class="xevos-section xevos-hp-aktuality">
	<div class="xevos-section__container">

		<!-- Centered heading -->
		<div class="xevos-hp-aktuality__header">
			<h2><?php echo esc_html($heading); ?></h2>
			<?php
			$default_desc = 'Aktuality ze světa kybernetické bezpečnosti, legislativy a praxe. Sdílíme novinky k NIS2, ISO normám, reálným incidentům i změnám, které mají dopad na firmy a jejich bezpečnost.';
			if ($desc === $default_desc) : ?>
				<p class="xevos-hp-aktuality__desc--mixed"><strong>Aktuality</strong> ze světa <strong>kybernetické bezpečnosti, legislativy a praxe.</strong> Sdílíme novinky k <strong>NIS2</strong>, <strong>ISO normám</strong>, reálným incidentům i změnám, které mají dopad na firmy a jejich bezpečnost.</p>
			<?php else : ?>
				<p><?php echo esc_html($desc); ?></p>
			<?php endif; ?>
		</div>

		<!-- Cards carousel -->
		<div class="xevos-hp-aktuality__carousel">
			<button class="xevos-nav-arrow xevos-nav-arrow--prev xevos-aktuality-prev" aria-label="Předchozí">
				<svg width="12" height="20" viewBox="0 0 12 20" fill="none">
					<path d="M10 2L2 10l8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</button>

			<div class="swiper xevos-aktuality-swiper" id="aktuality-swiper">
				<div class="swiper-wrapper" style="align-items:stretch;">
					<?php if ( $has_aktuality ) :
						while ($query->have_posts()) : $query->the_post(); ?>
							<div class="swiper-slide">
								<?php get_template_part('template-parts/components/card-aktualita'); ?>
							</div>
						<?php endwhile;
						wp_reset_postdata();
					else :
						$fb_imgs = [
							get_theme_file_uri('assets/img/blog/aktualita-img-1.png'),
							get_theme_file_uri('assets/img/blog/aktualita-img-2.png'),
							get_theme_file_uri('assets/img/blog/aktualita-img-3.png'),
						];
						foreach ( $fallback_aktuality as $fa ) : ?>
							<div class="swiper-slide">
								<a href="#" class="xevos-card">
									<div class="xevos-card__image">
										<img src="<?php echo esc_url( $fb_imgs[ $fa['img'] - 1 ] ); ?>" alt="" width="507" height="293" loading="lazy">
									</div>
									<div class="xevos-card__body">
										<h3 class="xevos-card__title"><?php echo esc_html( $fa['title'] ); ?></h3>
										<p class="xevos-card__excerpt"><?php echo esc_html( $fa['excerpt'] ); ?></p>
										<span class="xevos-card__link">
											Číst více <img src="<?php echo esc_url( get_theme_file_uri('assets/img/global/card-arrow.svg') ); ?>" alt="" class="xevos-card__arrow">
										</span>
									</div>
								</a>
							</div>
						<?php endforeach;
					endif; ?>
				</div>
				<div class="swiper-pagination xevos-aktuality-pagination"></div>
			</div>

			<button class="xevos-nav-arrow xevos-nav-arrow--next xevos-aktuality-next" aria-label="Další">
				<svg width="12" height="20" viewBox="0 0 12 20" fill="none">
					<path d="M2 2l8 8-8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</button>
		</div>

		<!-- CTA button -->
		<div class="xevos-hp-aktuality__footer">
			<a href="<?php echo esc_url(get_post_type_archive_link('aktualita')); ?>" class="xevos-btn xevos-btn--outline">ZOBRAZIT VŠECHNY ČLÁNKY</a>
		</div>

	</div>
</section>
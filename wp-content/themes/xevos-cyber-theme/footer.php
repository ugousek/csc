<?php

/**
 * Footer — Figma: Section 07
 * Partners bar + Footer (logo | menu | social) + Copyright bar
 */
$logo    = xevos_get_option('logo');
$socials = xevos_get_option('socialni_site');
$firma   = xevos_get_option('nazev_firmy', 'XEVOS');
?>

<!-- Partners bar — Figma: 1600px container, space-between; Swiper under 991px -->
<section class="xevos-section xevos-partners">
	<div class="xevos-section__container xevos-partners__inner">
		<?php
		$partners = xevos_get_option('partneri');

		$fallback_logos = [
			'cisco'    => 'cisco.png',
			'eset'     => 'eset.png',
			'apple'    => 'apple.png',
			'paloalto' => 'paloalto.png',
			'palo alto' => 'paloalto.png',
			'pentera'  => 'pentera.png',
		];

		$partner_items = [];

		if ($partners) :
			foreach ($partners as $p) :
				$name = $p['nazev'] ?? '';
				$logo_url = '';
				if (! empty($p['logo']['url'])) {
					$logo_url = $p['logo']['url'];
				} else {
					$key = strtolower(trim($name));
					foreach ($fallback_logos as $match => $file) {
						if (str_contains($key, $match)) {
							$logo_url = get_theme_file_uri('assets/img/global/partners/' . $file);
							break;
						}
					}
				}
				if ($logo_url) {
					$partner_items[] = ['url' => $logo_url, 'name' => $name];
				}
			endforeach;
		endif;

		if (empty($partner_items)) :
			$partner_items = [
				['url' => get_theme_file_uri('assets/img/global/partners/cisco.png'), 'name' => 'Cisco'],
				['url' => get_theme_file_uri('assets/img/global/partners/eset.png'), 'name' => 'ESET Gold Partner'],
				['url' => get_theme_file_uri('assets/img/global/partners/apple.png'), 'name' => 'Apple Technical Partner'],
				['url' => get_theme_file_uri('assets/img/global/partners/paloalto.png'), 'name' => 'Palo Alto Networks'],
				['url' => get_theme_file_uri('assets/img/global/partners/pentera.png'), 'name' => 'Pentera'],
			];
		endif;
		?>

		<div class="swiper xevos-partners__swiper" id="partners-swiper">
			<div class="swiper-wrapper xevos-partners__track">
				<?php foreach ($partner_items as $pi) : ?>
					<div class="swiper-slide xevos-partners__slide">
						<img src="<?php echo esc_url($pi['url']); ?>" alt="<?php echo esc_attr($pi['name']); ?>">
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

<!-- Footer -->
<footer class="xevos-footer">
	<div class="xevos-footer__inner">
		<!-- Logo -->
		<div class="xevos-footer__logo">
			<a href="<?php echo esc_url(home_url('/')); ?>">
				<?php if ($logo) : ?>
					<img src="<?php echo esc_url($logo['url']); ?>" alt="<?php echo esc_attr($firma); ?>">
				<?php else : ?>
					<img src="<?php echo esc_url(get_theme_file_uri('assets/img/global/logo-footer.svg')); ?>" alt="<?php echo esc_attr($firma); ?>">
				<?php endif; ?>
			</a>
		</div>

		<!-- Nav + Social — on mobile: row (menu left, socials right) -->
		<div class="xevos-footer__nav-social">
			<nav class="xevos-footer__nav">
				<?php
				wp_nav_menu([
					'theme_location' => 'footer',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => 'xevos_fallback_menu',
				]);
				?>
			</nav>

			<!-- Social icons — Figma: 24x24, gap 15, white fill -->
			<div class="xevos-footer__social">
			<?php
			/* Inline SVG icons by network name — exact Figma exports */
			$social_icons = [
				'facebook'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M22 12.3038C22 6.74719 17.5229 2.24268 12 2.24268C6.47715 2.24268 2 6.74719 2 12.3038C2 17.3255 5.65684 21.4879 10.4375 22.2427V15.2121H7.89844V12.3038H10.4375V10.0872C10.4375 7.56564 11.9305 6.1728 14.2146 6.1728C15.3088 6.1728 16.4531 6.36931 16.4531 6.36931V8.84529H15.1922C13.95 8.84529 13.5625 9.6209 13.5625 10.4166V12.3038H16.3359L15.8926 15.2121H13.5625V22.2427C18.3432 21.4879 22 17.3257 22 12.3038Z" fill="white"/></svg>',
				'x'         => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M17.1761 4.24268H19.9362L13.9061 11.0201L21 20.2427H15.4456L11.0951 14.6493L6.11723 20.2427H3.35544L9.80517 12.9935L3 4.24268H8.69545L12.6279 9.3553L17.1761 4.24268ZM16.2073 18.6181H17.7368L7.86441 5.78196H6.2232L16.2073 18.6181Z" fill="white"/></svg>',
				'twitter'   => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M17.1761 4.24268H19.9362L13.9061 11.0201L21 20.2427H15.4456L11.0951 14.6493L6.11723 20.2427H3.35544L9.80517 12.9935L3 4.24268H8.69545L12.6279 9.3553L17.1761 4.24268ZM16.2073 18.6181H17.7368L7.86441 5.78196H6.2232L16.2073 18.6181Z" fill="white"/></svg>',
				'instagram' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M16 3.24268H8C5.23858 3.24268 3 5.48126 3 8.24268V16.2427C3 19.0041 5.23858 21.2427 8 21.2427H16C18.7614 21.2427 21 19.0041 21 16.2427V8.24268C21 5.48126 18.7614 3.24268 16 3.24268ZM19.25 16.2427C19.2445 18.0353 17.7926 19.4872 16 19.4927H8C6.20735 19.4872 4.75549 18.0353 4.75 16.2427V8.24268C4.75549 6.45003 6.20735 4.99817 8 4.99268H16C17.7926 4.99817 19.2445 6.45003 19.25 8.24268V16.2427ZM16.75 8.49268C17.3023 8.49268 17.75 8.04496 17.75 7.49268C17.75 6.9404 17.3023 6.49268 16.75 6.49268C16.1977 6.49268 15.75 6.9404 15.75 7.49268C15.75 8.04496 16.1977 8.49268 16.75 8.49268ZM12 7.74268C9.51472 7.74268 7.5 9.7574 7.5 12.2427C7.5 14.728 9.51472 16.7427 12 16.7427C14.4853 16.7427 16.5 14.728 16.5 12.2427C16.5027 11.0484 16.0294 9.90225 15.1849 9.05776C14.3404 8.21327 13.1943 7.74002 12 7.74268ZM9.25 12.2427C9.25 13.7615 10.4812 14.9927 12 14.9927C13.5188 14.9927 14.75 13.7615 14.75 12.2427C14.75 10.7239 13.5188 9.49268 12 9.49268C10.4812 9.49268 9.25 10.7239 9.25 12.2427Z" fill="white"/></svg>',
				'linkedin'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.5 3.24268C3.67157 3.24268 3 3.91425 3 4.74268V19.7427C3 20.5711 3.67157 21.2427 4.5 21.2427H19.5C20.3284 21.2427 21 20.5711 21 19.7427V4.74268C21 3.91425 20.3284 3.24268 19.5 3.24268H4.5ZM8.52076 7.2454C8.52639 8.20165 7.81061 8.79087 6.96123 8.78665C6.16107 8.78243 5.46357 8.1454 5.46779 7.24681C5.47201 6.40165 6.13998 5.72243 7.00764 5.74212C7.88795 5.76181 8.52639 6.40728 8.52076 7.2454ZM12.2797 10.0044H9.75971H9.7583V18.5643H12.4217V18.3646C12.4217 17.9847 12.4214 17.6047 12.4211 17.2246C12.4203 16.2108 12.4194 15.1959 12.4246 14.1824C12.426 13.9363 12.4372 13.6804 12.5005 13.4455C12.7381 12.568 13.5271 12.0013 14.4074 12.1406C14.9727 12.2291 15.3467 12.5568 15.5042 13.0898C15.6013 13.423 15.6449 13.7816 15.6491 14.129C15.6605 15.1766 15.6589 16.2242 15.6573 17.2719C15.6567 17.6417 15.6561 18.0117 15.6561 18.3815V18.5629H18.328V18.3576C18.328 17.9056 18.3278 17.4537 18.3275 17.0018C18.327 15.8723 18.3264 14.7428 18.3294 13.6129C18.3308 13.1024 18.276 12.599 18.1508 12.1054C17.9638 11.3713 17.5771 10.7638 16.9485 10.3251C16.5027 10.0129 16.0133 9.81178 15.4663 9.78928C15.404 9.78669 15.3412 9.7833 15.2781 9.77989C14.9984 9.76477 14.7141 9.74941 14.4467 9.80334C13.6817 9.95662 13.0096 10.3068 12.5019 10.9241C12.4429 10.9949 12.3852 11.0668 12.2991 11.1741L12.2797 11.1984V10.0044ZM5.68164 18.5671H8.33242V10.01H5.68164V18.5671Z" fill="white"/></svg>',
			];

			if ($socials) :
				foreach ($socials as $s) :
					$url   = $s['url'] ?? '#';
					$name  = strtolower($s['nazev'] ?? '');
					$label = $s['nazev'] ?? '';
					$icon  = '';

					/* Use uploaded icon if available */
					if (! empty($s['ikona']['url'])) {
						$icon = '<img src="' . esc_url($s['ikona']['url']) . '" alt="">';
					} else {
						/* Match by name to inline SVG */
						foreach ($social_icons as $key => $svg) {
							if (str_contains($name, $key)) {
								$icon = $svg;
								break;
							}
						}
					}

					if ($icon) : ?>
						<a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr($label); ?>">
							<?php echo $icon; ?>
						</a>
					<?php endif;
				endforeach;
			else :
				/* No ACF data at all — show all 4 with # links */
				foreach ($social_icons as $key => $svg) :
					if ($key === 'twitter') continue; /* skip duplicate of X */ ?>
					<a href="#" aria-label="<?php echo esc_attr(ucfirst($key)); ?>"><?php echo $svg; ?></a>
			<?php endforeach;
			endif; ?>
		</div>
		</div><!-- /.xevos-footer__nav-social -->
	</div>

	<!-- Copyright  -->
	<div class="xevos-footer__copy">
		<span>Copyright &copy;<?php echo esc_html(strtolower($firma)); ?></span>
		<a href="<?php echo esc_url(home_url('/zasady-ochrany-osobnich-udaju/')); ?>">Zásady ochrany osobních údajů</a>
		<a href="<?php echo esc_url(home_url('/obchodni-podminky/')); ?>">Obchodní podmínky</a>
	</div>
</footer>

<?php wp_footer(); ?>
</body>

</html>
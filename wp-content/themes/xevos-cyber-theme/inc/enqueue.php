<?php
/**
 * Enqueue styles and scripts.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'xevos_enqueue_assets' );

function xevos_enqueue_assets(): void {
	$theme_uri = XEVOS_THEME_URI;
	$version   = XEVOS_THEME_VERSION;

	// Main CSS (fonts are self-hosted via @font-face in main.css).
	wp_enqueue_style(
		'xevos-main',
		$theme_uri . '/assets/css/main.css',
		[],
		$version
	);

	// Kyber testování page-specific CSS.
	if ( is_page_template( 'page-kyberneticke-testovani.php' ) ) {
		wp_enqueue_style(
			'xevos-kyber-testovani',
			$theme_uri . '/assets/css/kyber-testovani.css',
			[ 'xevos-main' ],
			$version
		);
	}

	// Školení CSS (archive only).
	if ( is_post_type_archive( 'skoleni' ) ) {
		wp_enqueue_style(
			'xevos-skoleni',
			$theme_uri . '/assets/css/skoleni.css',
			[ 'xevos-main' ],
			$version
		);
	}

	// Single aktualita CSS.
	if ( is_singular( 'aktualita' ) ) {
		wp_enqueue_style(
			'xevos-single-aktualita',
			$theme_uri . '/assets/css/single-aktualita.css',
			[ 'xevos-main' ],
			$version
		);
	}

	// Školení detail CSS (single only).
	if ( is_singular( 'skoleni' ) ) {
		wp_enqueue_style(
			'xevos-detail-skoleni',
			$theme_uri . '/assets/css/detail-skoleni.css',
			[ 'xevos-main' ],
			$version
		);
	}

// Blog / Archive CSS (aktuality + školení).
	if (
		is_post_type_archive( 'aktualita' ) || is_singular( 'aktualita' ) || is_tax( 'kategorie-aktualit' ) ||
		is_post_type_archive( 'skoleni' ) || is_singular( 'skoleni' ) || is_tax( 'kategorie-skoleni' )
	) {
		wp_enqueue_style(
			'xevos-blog',
			$theme_uri . '/assets/css/blog.css',
			[ 'xevos-main' ],
			$version
		);
	}

	// Search results CSS.
	if ( is_search() ) {
		wp_enqueue_style(
			'xevos-search',
			$theme_uri . '/assets/css/search.css',
			[ 'xevos-main' ],
			$version
		);
	}

	// Legal CSS (Obchodní podmínky, GDPR, Cookies).
	if (
		is_page_template( 'page-obchodni-podminky.php' ) ||
		is_page_template( 'page-zasady-ochrany-osobnich-udaju.php' ) ||
		is_page_template( 'page-zasady-cookies.php' ) ||
		is_page_template( 'page-legal.php' )
	) {
		wp_enqueue_style(
			'xevos-legal',
			$theme_uri . '/assets/css/legal.css',
			[ 'xevos-main' ],
			$version
		);
	}

	// Kontakt CSS (only on contact page).
	if ( is_page_template( 'page-kontakt.php' ) ) {
		wp_enqueue_style(
			'xevos-kontakt',
			$theme_uri . '/assets/css/kontakt.css',
			[ 'xevos-main' ],
			$version
		);
	}

	// Frontpage CSS + JS (only on homepage).
	if ( is_front_page() ) {
		wp_enqueue_style(
			'xevos-frontpage',
			$theme_uri . '/assets/css/frontpage.css',
			[ 'xevos-main' ],
			$version
		);

		// Lottie player for hero map animation.
		wp_enqueue_script(
			'lottie',
			'https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js',
			[],
			'5.12.2',
			true
		);

		wp_enqueue_script(
			'xevos-homepage',
			$theme_uri . '/assets/js/homepage.js',
			[ 'swiper', 'lottie' ],
			$version,
			true
		);

		// Pass lottie JSON path to JS.
		wp_localize_script( 'xevos-homepage', 'xevosHero', [
			'lottieUrl' => $theme_uri . '/assets/js/mapa-lottie.json',
		] );
	}

	// Complianz cookie banner overrides (only when plugin active).
	if ( defined( 'CMPLZ_VERSION' ) ) {
		wp_enqueue_style(
			'xevos-complianz',
			$theme_uri . '/assets/css/complianz-override.css',
			[ 'xevos-main' ],
			$version
		);

		// Inject cookie icon into Complianz message.
		add_action( 'wp_footer', function () use ( $theme_uri ) {
			?>
			<script>
			document.addEventListener('DOMContentLoaded', function() {
				var msg = document.querySelector('.cmplz-cookiebanner .cmplz-message');
				if (msg && !msg.querySelector('.xevos-cookie-icon')) {
					var icon = document.createElement('img');
					icon.src = '<?php echo esc_url( $theme_uri . "/assets/img/global/cookie-icon.svg" ); ?>';
					icon.alt = '';
					icon.className = 'xevos-cookie-icon';
					icon.width = 24;
					icon.height = 24;
					icon.style.cssText = 'flex-shrink:0;margin-right:6px;';
					msg.insertBefore(icon, msg.firstChild);
					msg.style.display = 'flex';
					msg.style.alignItems = 'center';
					msg.style.gap = '10px';
				}
			});
			</script>
			<?php
		}, 999 );
	}

	// Swiper.js (CDN).
	wp_enqueue_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', [], '11' );
	wp_enqueue_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], '11', true );

	// Main JS.
	wp_enqueue_script(
		'xevos-main',
		$theme_uri . '/assets/js/main.js',
		[ 'swiper' ],
		$version,
		true
	);

	// Localize script for AJAX.
	wp_localize_script( 'xevos-main', 'xevosAjax', [
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'restUrl' => rest_url( 'xevos/v1/' ),
		'nonce'   => wp_create_nonce( 'xevos_nonce' ),
	] );

	// Archive filter JS — only on archive pages.
	if ( is_post_type_archive( 'aktualita' ) || is_tax( 'kategorie-aktualit' ) ) {
		wp_enqueue_script(
			'xevos-archive-filter',
			$theme_uri . '/assets/js/archive-filter.js',
			[ 'xevos-main' ],
			$version,
			true
		);
	}

	// Ecomail free registration JS (single skoleni with free/invitation mode).
	if ( is_singular( 'skoleni' ) && function_exists( 'get_field' ) && in_array( get_field( 'typ_prihlaseni' ), [ 'zdarma', 'pozvanka' ], true ) ) {
		wp_enqueue_script(
			'xevos-ecomail-register',
			$theme_uri . '/assets/js/ecomail-register.js',
			[ 'xevos-main' ],
			$version,
			true
		);
	}

	// Comgate paid order JS (single skoleni with paid mode).
	if ( is_singular( 'skoleni' ) && function_exists( 'get_field' ) && ! in_array( get_field( 'typ_prihlaseni' ), [ 'zdarma', 'pozvanka' ], true ) ) {
		wp_enqueue_script(
			'xevos-comgate-payment',
			$theme_uri . '/assets/js/comgate-payment.js',
			[ 'xevos-main' ],
			$version,
			true
		);
	}

	// Školení archive filter JS.
	if ( is_post_type_archive( 'skoleni' ) || is_tax( 'kategorie-skoleni' ) ) {
		wp_enqueue_script(
			'xevos-skoleni-filter',
			$theme_uri . '/assets/js/skoleni-filter.js',
			[ 'xevos-main' ],
			$version,
			true
		);
	}
}

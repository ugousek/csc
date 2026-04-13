<?php
/**
 * Enqueue styles and scripts.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'xevos_enqueue_assets' );

/**
 * Returns filemtime of a theme asset as cache-busting version string.
 * Falls back to XEVOS_THEME_VERSION if the file doesn't exist.
 */
function xevos_asset_version( string $relative_path ): string {
	$abs = get_template_directory() . '/' . ltrim( $relative_path, '/' );
	return file_exists( $abs ) ? (string) filemtime( $abs ) : XEVOS_THEME_VERSION;
}

function xevos_enqueue_assets(): void {
	$theme_uri = XEVOS_THEME_URI;

	// Main CSS (fonts are self-hosted via @font-face in main.css).
	wp_enqueue_style(
		'xevos-main',
		$theme_uri . '/assets/css/main.css',
		[],
		xevos_asset_version( 'assets/css/main.css' )
	);

	// Kyber testování CSS + JS (shared across kyber, služby, nis2, o nás).
	if (
		is_page_template( 'page-kyberneticke-testovani.php' ) ||
		is_page_template( 'page-sluzby.php' ) ||
		is_page_template( 'page-nis2.php' ) ||
		is_page_template( 'page-o-nas.php' ) ||
		is_front_page()
	) {
		wp_enqueue_style(
			'xevos-kyber-testovani',
			$theme_uri . '/assets/css/kyber-testovani.css',
			[ 'xevos-main' ],
			xevos_asset_version( 'assets/css/kyber-testovani.css' )
		);
		wp_enqueue_script(
			'xevos-inquiry-form',
			$theme_uri . '/assets/js/inquiry-form.js',
			[ 'xevos-main' ],
			xevos_asset_version( 'assets/js/inquiry-form.js' ),
			true
		);
	}

	// Školení CSS (archive only).
	if ( is_post_type_archive( 'skoleni' ) ) {
		wp_enqueue_style(
			'xevos-skoleni',
			$theme_uri . '/assets/css/skoleni.css',
			[ 'xevos-main' ],
			xevos_asset_version( 'assets/css/skoleni.css' )
		);
	}

	// Single aktualita CSS.
	if ( is_singular( 'aktualita' ) ) {
		wp_enqueue_style(
			'xevos-single-aktualita',
			$theme_uri . '/assets/css/single-aktualita.css',
			[ 'xevos-main' ],
			xevos_asset_version( 'assets/css/single-aktualita.css' )
		);
	}

	// Školení detail CSS (single only).
	if ( is_singular( 'skoleni' ) ) {
		wp_enqueue_style(
			'xevos-detail-skoleni',
			$theme_uri . '/assets/css/detail-skoleni.css',
			[ 'xevos-main' ],
			xevos_asset_version( 'assets/css/detail-skoleni.css' )
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
			xevos_asset_version( 'assets/css/blog.css' )
		);
	}

	// Search results CSS.
	if ( is_search() ) {
		wp_enqueue_style(
			'xevos-search',
			$theme_uri . '/assets/css/search.css',
			[ 'xevos-main' ],
			xevos_asset_version( 'assets/css/search.css' )
		);
	}

	// Služby CSS.
	if ( is_page_template( 'page-sluzby.php' ) ) {
		wp_enqueue_style(
			'xevos-sluzby',
			$theme_uri . '/assets/css/sluzby.css',
			[ 'xevos-main' ],
			xevos_asset_version( 'assets/css/sluzby.css' )
		);
	}

	// NIS 2 CSS.
	if ( is_page_template( 'page-nis2.php' ) ) {
		wp_enqueue_style(
			'xevos-nis2',
			$theme_uri . '/assets/css/nis2.css',
			[ 'xevos-main' ],
			xevos_asset_version( 'assets/css/nis2.css' )
		);
	}

	// O nás CSS.
	if ( is_page_template( 'page-o-nas.php' ) ) {
		wp_enqueue_style(
			'xevos-o-nas',
			$theme_uri . '/assets/css/o-nas.css',
			[ 'xevos-main' ],
			xevos_asset_version( 'assets/css/o-nas.css' )
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
			xevos_asset_version( 'assets/css/legal.css' )
		);
	}

	// Kontakt CSS + JS (only on contact page).
	if ( is_page_template( 'page-kontakt.php' ) ) {
		wp_enqueue_style(
			'xevos-kontakt',
			$theme_uri . '/assets/css/kontakt.css',
			[ 'xevos-main' ],
			xevos_asset_version( 'assets/css/kontakt.css' )
		);
		wp_enqueue_script(
			'xevos-contact-form',
			$theme_uri . '/assets/js/contact-form.js',
			[ 'xevos-main' ],
			xevos_asset_version( 'assets/js/contact-form.js' ),
			true
		);
	}

	// Frontpage CSS + JS (only on homepage).
	if ( is_front_page() ) {
		wp_enqueue_style(
			'xevos-frontpage',
			$theme_uri . '/assets/css/frontpage.css',
			[ 'xevos-main' ],
			xevos_asset_version( 'assets/css/frontpage.css' )
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
			xevos_asset_version( 'assets/js/homepage.js' ),
			true
		);

		// Pass lottie JSON path to JS.
		wp_localize_script( 'xevos-homepage', 'xevosHero', [
			'lottieUrl'    => $theme_uri . '/assets/js/mapa-lottie.json',
			'shieldUrl'    => $theme_uri . '/assets/json/shield-lottie.json',
		] );
	}

	// Complianz cookie banner overrides (only when plugin active).
	if ( defined( 'CMPLZ_VERSION' ) ) {
		wp_enqueue_style(
			'xevos-complianz',
			$theme_uri . '/assets/css/complianz-override.css',
			[ 'xevos-main' ],
			xevos_asset_version( 'assets/css/complianz-override.css' )
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
		xevos_asset_version( 'assets/js/main.js' ),
		true
	);

	// Form validation JS (loaded on pages with forms).
	if ( is_singular( 'skoleni' ) || is_page_template( 'page-kontakt.php' ) || is_page_template( 'page-kyberneticke-testovani.php' ) ) {
		wp_enqueue_script(
			'xevos-form-validation',
			$theme_uri . '/assets/js/form-validation.js',
			[ 'xevos-main' ],
			xevos_asset_version( 'assets/js/form-validation.js' ),
			true
		);
	}

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
			xevos_asset_version( 'assets/js/archive-filter.js' ),
			true
		);
	}

	// Ecomail free registration JS (single skoleni with free/invitation mode).
	if ( is_singular( 'skoleni' ) && function_exists( 'get_field' ) && in_array( get_field( 'typ_prihlaseni' ), [ 'zdarma', 'pozvanka' ], true ) ) {
		wp_enqueue_script(
			'xevos-ecomail-register',
			$theme_uri . '/assets/js/ecomail-register.js',
			[ 'xevos-main' ],
			xevos_asset_version( 'assets/js/ecomail-register.js' ),
			true
		);
	}

	// Comgate paid order JS (single skoleni with paid mode).
	if ( is_singular( 'skoleni' ) && function_exists( 'get_field' ) && ! in_array( get_field( 'typ_prihlaseni' ), [ 'zdarma', 'pozvanka' ], true ) ) {
		wp_enqueue_script(
			'xevos-comgate-payment',
			$theme_uri . '/assets/js/comgate-payment.js',
			[ 'xevos-main' ],
			xevos_asset_version( 'assets/js/comgate-payment.js' ),
			true
		);
	}

	// Školení archive filter JS.
	if ( is_post_type_archive( 'skoleni' ) || is_tax( 'kategorie-skoleni' ) ) {
		wp_enqueue_script(
			'xevos-skoleni-filter',
			$theme_uri . '/assets/js/skoleni-filter.js',
			[ 'xevos-main' ],
			xevos_asset_version( 'assets/js/skoleni-filter.js' ),
			true
		);
	}
}

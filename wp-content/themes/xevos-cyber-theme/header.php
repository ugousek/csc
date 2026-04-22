<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>

	<a class="xevos-skip-link" href="#main"><?php esc_html_e('Skip to content', 'xevos-cyber'); ?></a>

	<?php
	$cta_text    = xevos_get_option('header_cta_text', 'Nezávazná konzultace');
	$cta_url     = xevos_get_option('header_cta_url', '/kontakt/');
	$header_logo = xevos_get_option('logo');
	$firma_nazev = xevos_get_option('nazev_firmy', 'XEVOS Cyber Security');
	?>

	<header class="xevos-header" id="header">
		<div class="xevos-header__inner">
			<!-- Logo -->
			<a href="<?php echo esc_url(home_url('/')); ?>" class="xevos-header__logo">
				<?php if ( is_array($header_logo) && ! empty($header_logo['url']) ) : ?>
					<img src="<?php echo esc_url($header_logo['url']); ?>" alt="<?php echo esc_attr($firma_nazev); ?>">
				<?php else : ?>
					<img src="<?php echo esc_url(get_theme_file_uri('assets/img/global/csc-logo.png')); ?>" alt="<?php echo esc_attr($firma_nazev); ?>">
				<?php endif; ?>
			</a>

			<!-- Desktop navigation -->
			<nav class="xevos-header__nav" aria-label="<?php esc_attr_e('Hlavní navigace', 'xevos-cyber'); ?>">
				<?php
				wp_nav_menu([
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'xevos-header__nav-list',
					'fallback_cb'    => 'xevos_fallback_menu',
					'depth'          => 2,
				]);
				?>
			</nav>

			<!-- Actions: search + CTA + hamburger -->
			<div class="xevos-header__actions">
				<button class="xevos-header__search-btn" aria-label="<?php esc_attr_e('Hledat', 'xevos-cyber'); ?>" id="header-search-toggle">
				</button>

				<a href="<?php echo esc_url($cta_url); ?>" class="xevos-header__cta">
					<?php echo esc_html(strtoupper($cta_text)); ?>
				</a>

				<button class="xevos-header__hamburger" aria-label="<?php esc_attr_e('Menu', 'xevos-cyber'); ?>" aria-expanded="false">
					<span></span>
					<span></span>
					<span></span>
				</button>
			</div>
		</div>
	</header>

	<!-- Mobile menu overlay -->
	<div class="xevos-mobile-menu__overlay" aria-hidden="true"></div>

	<!-- Mobile menu panel -->
	<div class="xevos-mobile-menu" id="mobile-menu" aria-hidden="true">
		<div class="xevos-mobile-menu__content">
			<div class="xevos-mobile-menu__inner">
				<!-- Search with inline icon -->
				<div class="xevos-mobile-menu__search">
					<form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="xevos-mobile-search-form">
						<svg class="xevos-mobile-search-form__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
						<input type="search" name="s" placeholder="Hledat..." class="xevos-mobile-search-form__input" value="<?php echo esc_attr( get_search_query() ); ?>">
					</form>
				</div>

				<!-- Navigation -->
				<?php
				wp_nav_menu([
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'xevos-mobile-menu__nav-list',
					'fallback_cb'    => 'xevos_fallback_menu',
					'depth'          => 2,
				]);
				?>

				<!-- CTA -->
				<div class="xevos-mobile-menu__cta">
					<a href="<?php echo esc_url($cta_url); ?>" class="xevos-btn xevos-btn--outline xevos-btn--full">
						<?php echo esc_html(strtoupper($cta_text)); ?>
					</a>
				</div>
			</div>
		</div>
	</div>

	<!-- Search overlay (desktop) -->
	<div class="xevos-search-overlay" id="search-overlay">
		<div class="xevos-search-overlay__inner">
			<?php get_search_form(); ?>
		</div>
		<button class="xevos-search-overlay__close" aria-label="<?php esc_attr_e('Zavřít', 'xevos-cyber'); ?>">
			<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
				<path d="M18 6L6 18M6 6l12 12" />
			</svg>
		</button>
	</div>
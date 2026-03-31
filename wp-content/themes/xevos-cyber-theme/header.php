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
	$cta_text = xevos_get_option('header_cta_text', 'Nezávazná konzultace');
	$cta_url  = xevos_get_option('header_cta_url', '/kontakt/');
	?>

	<header class="xevos-header" id="header">
		<div class="xevos-header__inner">
			<!-- Logo -->
			<a href="<?php echo esc_url(home_url('/')); ?>" class="xevos-header__logo">
				<img src="<?php echo esc_url(get_theme_file_uri('assets/img/global/csc-logo.png')); ?>" alt="XEVOS Cyber Security">
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
		<!-- Close button -->
		<div class="xevos-mobile-menu__header">
			<a href="<?php echo esc_url(home_url('/')); ?>" class="xevos-header__logo">
				<img src="<?php echo esc_url(get_theme_file_uri('assets/img/global/csc-logo.png')); ?>" alt="XEVOS Cyber Security" style="height:28px;">
			</a>
			<button class="xevos-mobile-menu__close" aria-label="Zavřít menu">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
					<path d="M18 6L6 18M6 6l12 12" />
				</svg>
			</button>
		</div>

		<!-- Search -->
		<div class="xevos-mobile-menu__search">
			<?php get_search_form(); ?>
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

		<!-- Emergency CTA -->
		<div class="xevos-mobile-menu__emergency">
			<div class="xevos-emergency-badge">
				<span class="xevos-emergency-badge__icon">☣</span>
				<div>
					<span class="xevos-emergency-badge__title">Jsem terčem <strong>ÚTOKU!</strong></span>
					<span class="xevos-emergency-badge__sub">Jak postupovat?</span>
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
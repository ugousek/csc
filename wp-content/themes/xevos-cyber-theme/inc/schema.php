<?php
/**
 * Schema.org structured data (JSON-LD).
 * Organization, Course (školení), Article (aktuality), BreadcrumbList.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_head', 'xevos_schema_markup', 5 );

function xevos_schema_markup(): void {
	// Organization – all pages.
	if ( is_front_page() ) {
		xevos_schema_organization();
	}

	// Course – single školení.
	if ( is_singular( 'skoleni' ) ) {
		xevos_schema_course();
	}

	// Article – single aktualita.
	if ( is_singular( 'aktualita' ) ) {
		xevos_schema_article();
	}

	// BreadcrumbList – all except front page.
	if ( ! is_front_page() ) {
		xevos_schema_breadcrumbs();
	}
}

function xevos_schema_organization(): void {
	$firma   = xevos_get_option( 'nazev_firmy', 'XEVOS Cyber Security Center' );
	$telefon = xevos_get_option( 'telefon' );
	$email   = xevos_get_option( 'email' );
	$adresa  = xevos_get_option( 'adresa' );
	$logo    = xevos_get_option( 'logo' );

	$schema = [
		'@context'    => 'https://schema.org',
		'@type'       => 'Organization',
		'name'        => $firma,
		'url'         => home_url( '/' ),
		'description' => get_bloginfo( 'description' ),
	];

	if ( $logo ) {
		$schema['logo'] = $logo['url'];
	}
	if ( $telefon ) {
		$schema['telephone'] = $telefon;
	}
	if ( $email ) {
		$schema['email'] = $email;
	}
	if ( $adresa ) {
		$schema['address'] = [
			'@type'          => 'PostalAddress',
			'streetAddress'  => $adresa,
			'addressCountry' => 'CZ',
		];
	}

	$socials = xevos_get_option( 'socialni_site' );
	if ( $socials ) {
		$schema['sameAs'] = array_column( $socials, 'url' );
	}

	xevos_print_schema( $schema );
}

function xevos_schema_course(): void {
	$popis      = get_field( 'popis' );
	$cena_s_dph = get_field( 'cena_s_dph' );
	$typ        = get_field( 'typ' );
	$terminy    = get_field( 'terminy' );
	$firma      = xevos_get_option( 'nazev_firmy', 'XEVOS' );

	$schema = [
		'@context'    => 'https://schema.org',
		'@type'       => 'Course',
		'name'        => get_the_title(),
		'description' => $popis ? wp_strip_all_tags( $popis ) : get_the_excerpt(),
		'url'         => get_the_permalink(),
		'provider'    => [
			'@type' => 'Organization',
			'name'  => $firma,
			'url'   => home_url( '/' ),
		],
	];

	if ( has_post_thumbnail() ) {
		$schema['image'] = get_the_post_thumbnail_url( get_the_ID(), 'large' );
	}

	// Course instances (terms).
	if ( $terminy ) {
		$instances = [];
		foreach ( $terminy as $t ) {
			if ( empty( $t['datum'] ) ) continue;

			$date_str = str_replace( '.', '-', $t['datum'] );
			$ts = strtotime( $date_str );
			if ( ! $ts || $ts < time() ) continue;

			$instance = [
				'@type'      => 'CourseInstance',
				'courseMode'  => $typ === 'online' ? 'Online' : 'Onsite',
				'startDate'  => date( 'Y-m-d', $ts ),
			];

			if ( ! empty( $t['misto'] ) ) {
				$instance['location'] = [
					'@type'   => 'Place',
					'name'    => $t['misto'],
					'address' => $t['misto'],
				];
			}

			if ( $cena_s_dph ) {
				$instance['offers'] = [
					'@type'         => 'Offer',
					'price'         => (float) $cena_s_dph,
					'priceCurrency' => 'CZK',
					'availability'  => 'https://schema.org/InStock',
					'url'           => get_the_permalink(),
				];
			}

			$instances[] = $instance;
		}
		if ( $instances ) {
			$schema['hasCourseInstance'] = $instances;
		}
	}

	xevos_print_schema( $schema );
}

function xevos_schema_article(): void {
	$schema = [
		'@context'      => 'https://schema.org',
		'@type'         => 'Article',
		'headline'      => get_the_title(),
		'description'   => get_the_excerpt(),
		'url'           => get_the_permalink(),
		'datePublished' => get_the_date( 'c' ),
		'dateModified'  => get_the_modified_date( 'c' ),
		'author'        => [
			'@type' => 'Person',
			'name'  => get_the_author(),
		],
		'publisher'     => [
			'@type' => 'Organization',
			'name'  => xevos_get_option( 'nazev_firmy', 'XEVOS' ),
			'url'   => home_url( '/' ),
		],
	];

	if ( has_post_thumbnail() ) {
		$schema['image'] = get_the_post_thumbnail_url( get_the_ID(), 'large' );
	}

	xevos_print_schema( $schema );
}

function xevos_schema_breadcrumbs(): void {
	$items = [];
	$pos   = 1;

	$items[] = [
		'@type'    => 'ListItem',
		'position' => $pos++,
		'name'     => 'Úvod',
		'item'     => home_url( '/' ),
	];

	if ( is_singular( 'aktualita' ) ) {
		$items[] = [ '@type' => 'ListItem', 'position' => $pos++, 'name' => 'Aktuality', 'item' => get_post_type_archive_link( 'aktualita' ) ];
		$items[] = [ '@type' => 'ListItem', 'position' => $pos++, 'name' => get_the_title() ];
	} elseif ( is_singular( 'skoleni' ) ) {
		$items[] = [ '@type' => 'ListItem', 'position' => $pos++, 'name' => 'Školení', 'item' => get_post_type_archive_link( 'skoleni' ) ];
		$items[] = [ '@type' => 'ListItem', 'position' => $pos++, 'name' => get_the_title() ];
	} elseif ( is_post_type_archive( 'aktualita' ) ) {
		$items[] = [ '@type' => 'ListItem', 'position' => $pos++, 'name' => 'Aktuality' ];
	} elseif ( is_post_type_archive( 'skoleni' ) ) {
		$items[] = [ '@type' => 'ListItem', 'position' => $pos++, 'name' => 'Školení' ];
	} elseif ( is_page() ) {
		$items[] = [ '@type' => 'ListItem', 'position' => $pos++, 'name' => get_the_title() ];
	}

	$schema = [
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	];

	xevos_print_schema( $schema );
}

function xevos_print_schema( array $data ): void {
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}

<?php
/**
 * Kyber testování: Penetrační testy — 2-column layout (text + image).
 * Figma: heading + description left, dashboard image right.
 */

$show = get_field( 'kt_pentest_zobrazit' );
if ( $show === false ) return;

$heading = get_field( 'kt_pentest_heading' ) ?: 'Penetrační testy';
$text    = get_field( 'kt_pentest_text' ) ?: 'Simulace útoku na vaše systémy s cílem identifikovat zranitelná místa a navrhnout účinná opatření k jejich eliminaci.';
$image   = get_field( 'kt_pentest_obrazek' );
$img_url = $image ? $image['url'] : get_theme_file_uri( 'assets/img/figma-assets/kyber-test-penetracni-dashboard.png' );

$vyhody = get_field( 'kt_pentest_vyhody' );
if ( ! $vyhody ) {
	$vyhody = [
		[ 'nazev' => 'Testování webových aplikací', 'popis' => 'OWASP Top 10, SQL Injection, XSS a autentizační testy.' ],
		[ 'nazev' => 'Testování infrastruktury', 'popis' => 'Síťový audit, cloud security, Active Directory a firewall review.' ],
		[ 'nazev' => 'Sociální inženýrství', 'popis' => 'Phishing kampaně, vishing testy, USB drop testy a awareness reporting.' ],
		[ 'nazev' => 'Red Team operace', 'popis' => 'Realistická simulace pokročilého útočníka kombinující technické a sociální vektory.' ],
	];
}
?>

<section class="xevos-section xevos-kt-pentest">
	<div class="xevos-section__container">
		<div class="xevos-hp-recenze__header">
			<h2><?php echo esc_html( $heading ); ?></h2>
			<p><?php echo wp_kses_post( $text ); ?></p>
		</div>

		<div class="xevos-kt-pentest__grid">
			<?php foreach ( $vyhody as $v ) : ?>
				<div class="xevos-services__card">
					<h3 class="xevos-services__card-title"><?php echo esc_html( $v['nazev'] ?? '' ); ?></h3>
					<p class="xevos-services__card-text"><?php echo esc_html( $v['popis'] ?? '' ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

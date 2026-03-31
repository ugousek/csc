<!DOCTYPE html>
<html lang="cs">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background-color:#f4f4f7;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f7;">
<tr><td align="center" style="padding:40px 0;">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;overflow:hidden;">

<tr><td style="background-color:#0f172a;padding:30px 40px;text-align:center;">
	<h1 style="color:#ffffff;margin:0;font-size:22px;">Připomenutí školení</h1>
</td></tr>

<tr><td style="padding:40px;">
	<p style="font-size:16px;color:#333;margin:0 0 20px;">Dobrý den, <?php echo esc_html( $email_data['jmeno'] ?? '' ); ?>,</p>
	<p style="font-size:14px;color:#555;margin:0 0 20px;">připomínáme Vám blížící se školení:</p>

	<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;padding:20px;margin:20px 0;">
		<h3 style="color:#1e40af;margin:0 0 10px;font-size:16px;"><?php echo esc_html( $email_data['nazev_skoleni'] ?? '' ); ?></h3>
		<p style="font-size:14px;color:#333;margin:4px 0;"><strong>Datum:</strong> <?php echo esc_html( $email_data['termin'] ?? '' ); ?></p>
		<p style="font-size:14px;color:#333;margin:4px 0;"><strong>Čas:</strong> <?php echo esc_html( $email_data['cas'] ?? '' ); ?></p>
		<p style="font-size:14px;color:#333;margin:4px 0;"><strong>Místo:</strong> <?php echo esc_html( $email_data['misto'] ?? '' ); ?></p>
	</div>

	<?php if ( ! empty( $email_data['poznamky'] ) ) : ?>
	<h3 style="font-size:14px;color:#333;margin:20px 0 10px;">Co si vzít / Jak se připojit</h3>
	<p style="font-size:14px;color:#555;"><?php echo wp_kses_post( $email_data['poznamky'] ); ?></p>
	<?php endif; ?>

	<p style="font-size:13px;color:#94a3b8;margin-top:30px;">Kontakt na organizátora: <?php echo esc_html( $email_data['kontakt_email'] ?? '' ); ?></p>
</td></tr>

<tr><td style="background-color:#f8fafc;padding:20px 40px;text-align:center;border-top:1px solid #e2e8f0;">
	<p style="font-size:12px;color:#94a3b8;margin:0;">&copy; <?php echo date( 'Y' ); ?> <?php echo esc_html( $email_data['firma'] ?? 'XEVOS' ); ?></p>
</td></tr>

</table>
</td></tr>
</table>
</body>
</html>

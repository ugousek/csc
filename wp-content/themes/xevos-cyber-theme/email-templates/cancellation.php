<!DOCTYPE html>
<html lang="cs">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background-color:#f4f4f7;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f7;">
<tr><td align="center" style="padding:40px 0;">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;overflow:hidden;">

<tr><td style="background-color:#0f172a;padding:30px 40px;text-align:center;">
	<h1 style="color:#ffffff;margin:0;font-size:22px;">Zrušení účasti</h1>
</td></tr>

<tr><td style="padding:40px;">
	<p style="font-size:16px;color:#333;margin:0 0 20px;">Dobrý den, <?php echo esc_html( $email_data['jmeno'] ?? '' ); ?>,</p>
	<p style="font-size:14px;color:#555;margin:0 0 20px;">Vaše účast na školení <strong><?php echo esc_html( $email_data['nazev_skoleni'] ?? '' ); ?></strong> (<?php echo esc_html( $email_data['termin'] ?? '' ); ?>) byla zrušena.</p>

	<?php if ( ! empty( $email_data['refundace'] ) ) : ?>
	<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;padding:16px;margin:20px 0;">
		<p style="color:#16a34a;font-weight:bold;margin:0;">Refundace <?php echo esc_html( $email_data['cena'] ?? '' ); ?> Kč byla zahájena a bude zpracována do 5–10 pracovních dnů.</p>
	</div>
	<?php endif; ?>

	<p style="font-size:14px;color:#555;">Pokud máte jakékoli dotazy ohledně zrušení nebo refundace, neváhejte nás kontaktovat.</p>
	<p style="font-size:13px;color:#94a3b8;margin-top:30px;">Kontakt: <?php echo esc_html( $email_data['kontakt_email'] ?? '' ); ?></p>
</td></tr>

<tr><td style="background-color:#f8fafc;padding:20px 40px;text-align:center;border-top:1px solid #e2e8f0;">
	<p style="font-size:12px;color:#94a3b8;margin:0;">&copy; <?php echo date( 'Y' ); ?> <?php echo esc_html( $email_data['firma'] ?? 'XEVOS' ); ?></p>
</td></tr>

</table>
</td></tr>
</table>
</body>
</html>

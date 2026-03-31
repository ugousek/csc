<!DOCTYPE html>
<html lang="cs">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background-color:#f4f4f7;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f7;">
<tr><td align="center" style="padding:40px 0;">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;overflow:hidden;">

<!-- Header -->
<tr><td style="background-color:#0f172a;padding:30px 40px;text-align:center;">
	<h1 style="color:#ffffff;margin:0;font-size:22px;">Potvrzení objednávky</h1>
</td></tr>

<!-- Body -->
<tr><td style="padding:40px;">
	<p style="font-size:16px;color:#333;margin:0 0 20px;">Dobrý den, <?php echo esc_html( $email_data['jmeno'] ?? '' ); ?>,</p>
	<p style="font-size:14px;color:#555;margin:0 0 20px;">děkujeme za Vaši objednávku. Níže naleznete souhrn:</p>

	<table role="presentation" width="100%" cellpadding="8" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:4px;margin-bottom:20px;">
		<tr style="background:#f8fafc;">
			<td style="font-size:13px;color:#64748b;font-weight:bold;">Číslo objednávky</td>
			<td style="font-size:14px;color:#333;"><?php echo esc_html( $email_data['cislo_objednavky'] ?? '' ); ?></td>
		</tr>
		<tr>
			<td style="font-size:13px;color:#64748b;font-weight:bold;">Školení</td>
			<td style="font-size:14px;color:#333;"><?php echo esc_html( $email_data['nazev_skoleni'] ?? '' ); ?></td>
		</tr>
		<tr style="background:#f8fafc;">
			<td style="font-size:13px;color:#64748b;font-weight:bold;">Termín</td>
			<td style="font-size:14px;color:#333;"><?php echo esc_html( $email_data['termin'] ?? '' ); ?></td>
		</tr>
		<tr>
			<td style="font-size:13px;color:#64748b;font-weight:bold;">Cena</td>
			<td style="font-size:14px;color:#333;font-weight:bold;"><?php echo esc_html( $email_data['cena'] ?? '' ); ?> Kč</td>
		</tr>
	</table>

	<?php if ( ! empty( $email_data['payment_url'] ) ) : ?>
	<p style="text-align:center;margin:30px 0;">
		<a href="<?php echo esc_url( $email_data['payment_url'] ); ?>" style="display:inline-block;background:#3b82f6;color:#ffffff;padding:14px 32px;border-radius:6px;text-decoration:none;font-weight:bold;font-size:14px;">Dokončit platbu</a>
	</p>
	<?php endif; ?>

	<p style="font-size:13px;color:#94a3b8;margin-top:30px;">Pokud máte jakékoli dotazy, kontaktujte nás na <?php echo esc_html( $email_data['kontakt_email'] ?? '' ); ?>.</p>
</td></tr>

<!-- Footer -->
<tr><td style="background-color:#f8fafc;padding:20px 40px;text-align:center;border-top:1px solid #e2e8f0;">
	<p style="font-size:12px;color:#94a3b8;margin:0;">&copy; <?php echo date( 'Y' ); ?> <?php echo esc_html( $email_data['firma'] ?? 'XEVOS' ); ?>. Všechna práva vyhrazena.</p>
</td></tr>

</table>
</td></tr>
</table>
</body>
</html>

<!DOCTYPE html>
<html lang="cs">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background-color:#f4f4f7;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f7;">
<tr><td align="center" style="padding:40px 0;">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;overflow:hidden;">

<tr><td style="background-color:#0f172a;padding:24px 40px;text-align:center;">
	<h1 style="color:#ffffff;margin:0;font-size:20px;">Nová objednávka</h1>
</td></tr>

<tr><td style="padding:30px 40px;">
	<table role="presentation" width="100%" cellpadding="6" cellspacing="0" style="font-size:14px;">
		<tr><td style="color:#64748b;width:140px;">Objednávka:</td><td style="color:#333;font-weight:bold;"><?php echo esc_html( $email_data['cislo_objednavky'] ?? '' ); ?></td></tr>
		<tr><td style="color:#64748b;">Jméno:</td><td style="color:#333;"><?php echo esc_html( ( $email_data['jmeno'] ?? '' ) . ' ' . ( $email_data['prijmeni'] ?? '' ) ); ?></td></tr>
		<tr><td style="color:#64748b;">E-mail:</td><td style="color:#333;"><?php echo esc_html( $email_data['email'] ?? '' ); ?></td></tr>
		<tr><td style="color:#64748b;">Telefon:</td><td style="color:#333;"><?php echo esc_html( $email_data['telefon'] ?? '' ); ?></td></tr>
		<tr><td style="color:#64748b;">Firma:</td><td style="color:#333;"><?php echo esc_html( $email_data['firma_nazev'] ?? '—' ); ?></td></tr>
		<tr><td style="color:#64748b;">Školení:</td><td style="color:#333;"><?php echo esc_html( $email_data['nazev_skoleni'] ?? '' ); ?></td></tr>
		<tr><td style="color:#64748b;">Termín:</td><td style="color:#333;"><?php echo esc_html( $email_data['termin'] ?? '' ); ?></td></tr>
		<tr><td style="color:#64748b;">Částka:</td><td style="color:#333;font-weight:bold;"><?php echo esc_html( $email_data['cena'] ?? '' ); ?> Kč</td></tr>
	</table>

	<p style="text-align:center;margin:30px 0 10px;">
		<a href="<?php echo esc_url( $email_data['admin_url'] ?? '' ); ?>" style="display:inline-block;background:#3b82f6;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:bold;font-size:14px;">Zobrazit v administraci</a>
	</p>
</td></tr>

<tr><td style="background-color:#f8fafc;padding:16px 40px;text-align:center;border-top:1px solid #e2e8f0;">
	<p style="font-size:12px;color:#94a3b8;margin:0;">Automatická notifikace z webu</p>
</td></tr>

</table>
</td></tr>
</table>
</body>
</html>

<!DOCTYPE html>
<html lang="cs">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background-color:#f4f4f7;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f7;">
<tr><td align="center" style="padding:40px 0;">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;overflow:hidden;">

<tr><td style="background-color:#0f172a;padding:24px 40px;text-align:center;">
	<h1 style="color:#ffffff;margin:0;font-size:20px;">Nová poptávka testování</h1>
</td></tr>

<tr><td style="padding:30px 40px;">
	<table role="presentation" width="100%" cellpadding="6" cellspacing="0" style="font-size:14px;">
		<tr><td style="color:#64748b;width:120px;">Jméno:</td><td style="color:#333;font-weight:bold;"><?php echo esc_html( ( $email_data['jmeno'] ?? '' ) . ' ' . ( $email_data['prijmeni'] ?? '' ) ); ?></td></tr>
		<tr><td style="color:#64748b;">E-mail:</td><td style="color:#333;"><?php echo esc_html( $email_data['email'] ?? '' ); ?></td></tr>
		<tr><td style="color:#64748b;">Telefon:</td><td style="color:#333;"><?php echo esc_html( $email_data['telefon'] ?? '' ); ?></td></tr>
		<tr><td style="color:#64748b;">Firma:</td><td style="color:#333;"><?php echo esc_html( $email_data['firma'] ?? '—' ); ?></td></tr>
		<tr><td style="color:#64748b;">Druh testu:</td><td style="color:#333;font-weight:bold;"><?php echo esc_html( $email_data['druh_testu'] ?? '' ); ?></td></tr>
	</table>

	<?php if ( ! empty( $email_data['zprava'] ) ) : ?>
	<div style="margin-top:20px;padding:16px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;">
		<p style="font-size:13px;color:#64748b;margin:0 0 8px;font-weight:bold;">Zpráva:</p>
		<p style="font-size:14px;color:#333;margin:0;white-space:pre-wrap;"><?php echo esc_html( $email_data['zprava'] ); ?></p>
	</div>
	<?php endif; ?>

	<p style="text-align:center;margin:30px 0 10px;">
		<a href="mailto:<?php echo esc_attr( $email_data['email'] ?? '' ); ?>" style="display:inline-block;background:#3b82f6;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:bold;font-size:14px;">Odpovědět</a>
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

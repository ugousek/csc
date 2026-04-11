<!DOCTYPE html>
<html lang="cs" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="x-apple-disable-message-reformatting">
<title>Poptávka přijata</title>
<!--[if mso]>
<noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
<![endif]-->
<style type="text/css">
@media only screen and (max-width:620px){
  .wrapper{width:100%!important;min-width:100%!important;}
  .body-cell{padding:24px!important;}
}
</style>
</head>
<body style="margin:0;padding:0;background-color:#0d1117;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#0d1117;">
<tr><td align="center" style="padding:40px 16px;">

<!--[if mso]><table role="presentation" align="center" width="600" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
<table class="wrapper" role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px;background-color:#111827;">

<!-- HEADER -->
<tr>
  <td style="background-color:#0a0f1a;padding:28px 40px;text-align:center;">
    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/global/logo-footer.png" width="300" alt="XEVOS" style="display:block;margin:0 auto;border:0;outline:0;text-decoration:none;-ms-interpolation-mode:bicubic;">
  </td>
</tr>
<!-- ACCENT LINE -->
<tr>
  <td style="background-color:#F527AA;font-size:0;line-height:3px;mso-line-height-rule:exactly;padding:0;">&nbsp;</td>
</tr>

<!-- BODY -->
<tr>
  <td class="body-cell" style="padding:40px;background-color:#111827;">

    <h1 style="margin:0 0 8px;font-size:24px;font-weight:700;color:#ffffff;font-family:Arial,Helvetica,sans-serif;line-height:1.3;">Poptávka přijata</h1>
    <p style="margin:0 0 28px;font-size:15px;color:#94a3b8;font-family:Arial,Helvetica,sans-serif;line-height:1.6;">Dobrý den, <strong style="color:#e2e8f0;"><?php echo esc_html( $email_data['jmeno'] ?? '' ); ?></strong>,</p>
    <p style="margin:0 0 28px;font-size:15px;color:#cbd5e1;font-family:Arial,Helvetica,sans-serif;line-height:1.6;">Děkujeme za Váš zájem o naše služby kybernetického testování. Vaše poptávka byla přijata.</p>

    <?php if ( ! empty( $email_data['druh_testu'] ) ) : ?>
    <!-- INFO BOX -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 28px;">
      <tr>
        <td style="background-color:#0f172a;border:1px solid #1e293b;border-left:4px solid #F527AA;padding:16px 24px;">
          <p style="margin:0;font-size:14px;color:#64748b;font-family:Arial,Helvetica,sans-serif;">Druh testu:</p>
          <p style="margin:4px 0 0;font-size:16px;font-weight:700;color:#ffffff;font-family:Arial,Helvetica,sans-serif;"><?php echo esc_html( $email_data['druh_testu'] ); ?></p>
        </td>
      </tr>
    </table>
    <?php endif; ?>

    <p style="margin:0 0 28px;font-size:15px;color:#cbd5e1;font-family:Arial,Helvetica,sans-serif;line-height:1.6;">Náš specialista Vás bude kontaktovat do <strong style="color:#ffffff;">2 pracovních dnů</strong> s návrhem řešení.</p>

    <p style="margin:0;font-size:13px;color:#4b5563;font-family:Arial,Helvetica,sans-serif;line-height:1.6;">V případě urgentního požadavku nás kontaktujte na <a href="mailto:<?php echo esc_attr( $email_data['kontakt_email'] ?? 'hello@xevos.eu' ); ?>" style="color:#F527AA;text-decoration:none;"><?php echo esc_html( $email_data['kontakt_email'] ?? 'hello@xevos.eu' ); ?></a>.</p>

  </td>
</tr>

<!-- FOOTER -->
<tr>
  <td style="background-color:#0a0f1a;padding:28px 40px;border-top:1px solid #1e293b;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
      <tr>
        <td align="center" style="padding-bottom:16px;">
          <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/global/logo-footer.png" width="200" alt="XEVOS" style="display:block;margin:0 auto;border:0;outline:0;text-decoration:none;-ms-interpolation-mode:bicubic;opacity:0.7;">
        </td>
      </tr>
      <tr>
        <td align="center" style="font-size:13px;color:#64748b;font-family:Arial,Helvetica,sans-serif;line-height:1.8;">
          Mostárenská 1156/38, 703 00 Ostrava<br>
          <a href="tel:+420591140315" style="color:#64748b;text-decoration:none;">+420 591 140 315</a>
          &nbsp;&bull;&nbsp;
          <a href="mailto:hello@xevos.eu" style="color:#F527AA;text-decoration:none;">hello@xevos.eu</a>
        </td>
      </tr>
      <tr>
        <td align="center" style="padding-top:16px;font-size:12px;color:#374151;font-family:Arial,Helvetica,sans-serif;">
          &copy; <?php echo date( 'Y' ); ?> <?php echo esc_html( $email_data['firma'] ?? 'XEVOS' ); ?>. Všechna práva vyhrazena.
        </td>
      </tr>
    </table>
  </td>
</tr>

</table>
<!--[if mso]></td></tr></table><![endif]-->

</td></tr>
</table>
</body>
</html>

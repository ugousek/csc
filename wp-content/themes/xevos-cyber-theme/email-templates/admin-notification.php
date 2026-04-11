<!DOCTYPE html>
<html lang="cs" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="x-apple-disable-message-reformatting">
<title>Nová objednávka</title>
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
  <td style="background-color:#0a0f1a;padding:22px 40px;text-align:center;">
    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/global/logo-footer.png" width="280" alt="XEVOS" style="display:block;margin:0 auto;border:0;outline:0;text-decoration:none;-ms-interpolation-mode:bicubic;">
    <p style="margin:10px 0 0;font-size:11px;color:#64748b;font-family:Arial,Helvetica,sans-serif;text-transform:uppercase;letter-spacing:1px;">Administrátorská notifikace</p>
  </td>
</tr>
<!-- ACCENT LINE -->
<tr>
  <td style="background-color:#F527AA;font-size:0;line-height:3px;mso-line-height-rule:exactly;padding:0;">&nbsp;</td>
</tr>

<!-- TITLE BAR -->
<tr>
  <td style="background-color:#0f172a;padding:16px 40px;">
    <p style="margin:0;font-size:18px;font-weight:700;color:#ffffff;font-family:Arial,Helvetica,sans-serif;">Nová objednávka</p>
    <p style="margin:4px 0 0;font-size:13px;color:#F527AA;font-family:Arial,Helvetica,sans-serif;font-weight:600;"><?php echo esc_html( $email_data['nazev_skoleni'] ?? '' ); ?></p>
  </td>
</tr>

<!-- BODY -->
<tr>
  <td class="body-cell" style="padding:32px 40px;background-color:#111827;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #1e293b;">
      <tr style="background-color:#0f172a;">
        <td style="padding:10px 16px;font-size:13px;color:#64748b;font-family:Arial,Helvetica,sans-serif;width:140px;border-bottom:1px solid #1e293b;">Objednávka:</td>
        <td style="padding:10px 16px;font-size:14px;color:#e2e8f0;font-family:Arial,Helvetica,sans-serif;font-weight:700;border-bottom:1px solid #1e293b;"><?php echo esc_html( $email_data['cislo_objednavky'] ?? '' ); ?></td>
      </tr>
      <tr>
        <td style="padding:10px 16px;font-size:13px;color:#64748b;font-family:Arial,Helvetica,sans-serif;border-bottom:1px solid #1e293b;">Jméno:</td>
        <td style="padding:10px 16px;font-size:14px;color:#e2e8f0;font-family:Arial,Helvetica,sans-serif;font-weight:600;border-bottom:1px solid #1e293b;"><?php echo esc_html( trim( ( $email_data['jmeno'] ?? '' ) . ' ' . ( $email_data['prijmeni'] ?? '' ) ) ); ?></td>
      </tr>
      <tr style="background-color:#0f172a;">
        <td style="padding:10px 16px;font-size:13px;color:#64748b;font-family:Arial,Helvetica,sans-serif;border-bottom:1px solid #1e293b;">E-mail:</td>
        <td style="padding:10px 16px;font-size:14px;border-bottom:1px solid #1e293b;"><a href="mailto:<?php echo esc_attr( $email_data['email'] ?? '' ); ?>" style="color:#F527AA;text-decoration:none;font-family:Arial,Helvetica,sans-serif;"><?php echo esc_html( $email_data['email'] ?? '' ); ?></a></td>
      </tr>
      <tr>
        <td style="padding:10px 16px;font-size:13px;color:#64748b;font-family:Arial,Helvetica,sans-serif;border-bottom:1px solid #1e293b;">Telefon:</td>
        <td style="padding:10px 16px;font-size:14px;color:#e2e8f0;font-family:Arial,Helvetica,sans-serif;border-bottom:1px solid #1e293b;"><?php echo esc_html( $email_data['telefon'] ?? '—' ); ?></td>
      </tr>
      <tr style="background-color:#0f172a;">
        <td style="padding:10px 16px;font-size:13px;color:#64748b;font-family:Arial,Helvetica,sans-serif;border-bottom:1px solid #1e293b;">Firma:</td>
        <td style="padding:10px 16px;font-size:14px;color:#e2e8f0;font-family:Arial,Helvetica,sans-serif;border-bottom:1px solid #1e293b;"><?php echo esc_html( $email_data['firma_nazev'] ?? '—' ); ?></td>
      </tr>
      <tr>
        <td style="padding:10px 16px;font-size:13px;color:#64748b;font-family:Arial,Helvetica,sans-serif;border-bottom:1px solid #1e293b;">Termín:</td>
        <td style="padding:10px 16px;font-size:14px;color:#e2e8f0;font-family:Arial,Helvetica,sans-serif;font-weight:600;border-bottom:1px solid #1e293b;"><?php echo esc_html( $email_data['termin'] ?? '' ); ?></td>
      </tr>
      <tr style="background-color:#0f172a;">
        <td style="padding:10px 16px;font-size:13px;color:#64748b;font-family:Arial,Helvetica,sans-serif;">Částka:</td>
        <td style="padding:10px 16px;font-size:16px;color:#F527AA;font-family:Arial,Helvetica,sans-serif;font-weight:700;"><?php echo esc_html( $email_data['cena'] ?? '' ); ?> Kč</td>
      </tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:24px;">
      <tr>
        <?php if ( ! empty( $email_data['admin_url'] ) ) : ?>
        <td align="center" style="padding:4px;">
          <!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="<?php echo esc_url( $email_data['admin_url'] ); ?>" style="height:44px;v-text-anchor:middle;width:210px;" arcsize="8%" strokecolor="#F527AA" fillcolor="#F527AA"><w:anchorlock/><center style="color:#ffffff;font-family:Arial,Helvetica,sans-serif;font-size:13px;font-weight:bold;">Zobrazit objednávku</center></v:roundrect><![endif]-->
          <!--[if !mso]><!--><a href="<?php echo esc_url( $email_data['admin_url'] ); ?>" style="display:inline-block;background-color:#F527AA;color:#ffffff;padding:11px 20px;text-decoration:none;font-weight:700;font-size:13px;font-family:Arial,Helvetica,sans-serif;border-radius:4px;">Zobrazit objednávku</a><!--<![endif]-->
        </td>
        <?php endif; ?>
        <?php if ( ! empty( $email_data['skoleni_admin_url'] ) ) : ?>
        <td align="center" style="padding:4px;">
          <!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="<?php echo esc_url( $email_data['skoleni_admin_url'] ); ?>" style="height:44px;v-text-anchor:middle;width:160px;" arcsize="8%" strokecolor="#1e293b" fillcolor="#1e293b"><w:anchorlock/><center style="color:#ffffff;font-family:Arial,Helvetica,sans-serif;font-size:13px;font-weight:bold;">Upravit školení</center></v:roundrect><![endif]-->
          <!--[if !mso]><!--><a href="<?php echo esc_url( $email_data['skoleni_admin_url'] ); ?>" style="display:inline-block;background-color:#1e293b;color:#ffffff;padding:11px 20px;text-decoration:none;font-weight:700;font-size:13px;font-family:Arial,Helvetica,sans-serif;border-radius:4px;border:1px solid #334155;">Upravit školení</a><!--<![endif]-->
        </td>
        <?php endif; ?>
        <?php if ( ! empty( $email_data['skoleni_url'] ) ) : ?>
        <td align="center" style="padding:4px;">
          <!--[if !mso]><!--><a href="<?php echo esc_url( $email_data['skoleni_url'] ); ?>" style="display:inline-block;background-color:#0f172a;color:#94a3b8;padding:11px 20px;text-decoration:none;font-weight:600;font-size:13px;font-family:Arial,Helvetica,sans-serif;border-radius:4px;border:1px solid #334155;">Web školení &rarr;</a><!--<![endif]-->
        </td>
        <?php endif; ?>
      </tr>
    </table>

  </td>
</tr>

<!-- FOOTER -->
<tr>
  <td style="background-color:#0a0f1a;padding:16px 40px;border-top:1px solid #1e293b;text-align:center;">
    <p style="margin:0;font-size:12px;color:#374151;font-family:Arial,Helvetica,sans-serif;">Automatická notifikace z webu &bull; <?php echo esc_html( gmdate( 'd.m.Y H:i' ) ); ?></p>
  </td>
</tr>

</table>
<!--[if mso]></td></tr></table><![endif]-->

</td></tr>
</table>
</body>
</html>

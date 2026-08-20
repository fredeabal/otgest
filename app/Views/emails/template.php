<?php
// Obtener el nombre de la empresa de forma global si no se ha pasado
if (!isset($companyName)) {
    $company = model('App\Models\CompanyModel')->getCompany();
    $companyName = $company ? $company['name'] : 'OtGest';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        :root { color-scheme: light; }
        .btn {
            display: inline-block;
            background-color: #5d87ff;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            margin-top: 5px;
        }
    </style>
</head>
<body style="background-color: #ffffff; margin: 0; padding: 0; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; background-image: linear-gradient(#ffffff, #ffffff); margin: 0; padding: 40px 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
        <tr>
            <td align="center">
                <a href="<?= base_url() ?>">
                    <img src="<?= base_url('assets/images/logos/logo-email.png') ?>" alt="Logo" style="max-width: 180px; height: auto; margin-bottom: 30px; display: block;">
                </a>
                <table width="100%" max-width="600" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; background-color: #f8f9fa; background-image: linear-gradient(#f8f9fa, #f8f9fa); border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e9ecef;">
                    <tr>
                        <td align="left" style="padding: 40px;">
                            <h2 style="color: #333f52; -webkit-text-fill-color: #333f52; margin-top: 0; text-align: center; font-weight: 600;"><?= esc($title ?? 'Notificación') ?></h2>
                            
                            <?php if (!empty($intro)): ?>
                                <p style="color: #5a6a85; -webkit-text-fill-color: #5a6a85; font-size: 16px; line-height: 1.6; text-align: center; margin-bottom: 25px;">
                                    <?= $intro ?>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($content)): ?>
                                <div style="background-color: #ffffff; border-radius: 8px; border: 1px solid #e9ecef; padding: 20px; margin: 25px 0; text-align: center;">
                                    <?= $content ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($buttonUrl) && !empty($buttonText)): ?>
                                <div style="text-align: center; margin-top: 20px;">
                                    <a href="<?= esc($buttonUrl) ?>" class="btn"><?= esc($buttonText) ?></a>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                <table width="100%" max-width="600" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; margin-top: 20px;">
                    <tr>
                        <td align="center" style="padding: 0 20px;">
                            <p style="color: #8c98a4; -webkit-text-fill-color: #8c98a4; font-size: 11px; line-height: 1.5; margin: 0;">
                                Este es un mensaje automático, por favor no respondas a este correo.<br>
                                &copy; <?= date('Y') ?> <?= esc($companyName) ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

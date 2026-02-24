<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva recibida</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .email-container { max-width: 600px; margin: 0 auto; background-color: #fff; }
        .header { background: linear-gradient(135deg, #7986CB 0%, #5C6BC0 100%); color: white; padding: 30px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; }
        .content { padding: 30px 20px; }
        .info-box { background: #f8f9fa; border-left: 4px solid #7986CB; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #e0e0e0; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>✓ Reserva recibida</h1>
        </div>
        <div class="content">
            <p>Hola <strong><?= esc($nombrePaciente) ?></strong>,</p>
            <div class="info-box">
                <?= $mensaje ?>
            </div>
            <p>Si tienes dudas, contacta directamente con <?= esc($nombreNutricionista) ?>.</p>
        </div>
        <div class="footer">
            <p>Este es un correo automático. No respondas a este mensaje.</p>
        </div>
    </div>
</body>
</html>

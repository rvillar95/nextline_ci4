<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelación de Cita</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            background: linear-gradient(135deg, #E57373 0%, #EF5350 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
        }
        .info-box {
            background: #fff3f3;
            border-left: 4px solid #E57373;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e0e0e0;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="icon">❌</div>
            <h1>Cancelación de Cita</h1>
        </div>

        <div class="content">
            <p>Estimado/a <strong><?= esc($paciente_nombre) ?></strong>,</p>

            <p>Lamentamos informarle que su cita ha sido cancelada:</p>

            <div class="info-box">
                <p style="margin: 0 0 8px 0;"><strong>📅 Fecha:</strong> <?= esc($fecha) ?></p>
                <p style="margin: 0;"><strong>🕐 Hora:</strong> <?= esc($hora) ?></p>
            </div>

            <?php if (!empty($motivo) && trim($motivo) !== ''): ?>
            <p><strong>Motivo:</strong><br><?= nl2br(esc(trim($motivo))) ?></p>
            <?php endif; ?>

            <p>Por favor, contáctenos para reagendar su consulta.</p>
            <p style="color: #666; font-size: 14px; margin-top: 30px;">
                Saludos cordiales,<br>
                <strong><?= esc($nutricionista_nombre) ?></strong>
            </p>
        </div>

        <div class="footer">
            <p>Este es un email automático del sistema de gestión de citas.</p>
        </div>
    </div>
</body>
</html>

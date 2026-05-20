<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva reserva desde la web</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .email-container { max-width: 600px; margin: 0 auto; background-color: #fff; }
        .header { background: linear-gradient(135deg, #7bc143 0%, #2daae1 100%); color: white; padding: 30px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; }
        .content { padding: 30px 20px; }
        .info-box { background: #f8f9fa; border-left: 4px solid #7bc143; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .info-row { padding: 6px 0; border-bottom: 1px solid #e8e8e8; }
        .info-row:last-child { border-bottom: none; }
        .btn { display: inline-block; background: #7bc143; color: #fff !important; text-decoration: none; padding: 12px 24px; border-radius: 6px; margin-top: 16px; font-weight: bold; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #e0e0e0; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Nueva reserva desde la web</h1>
        </div>
        <div class="content">
            <p>Hola <strong><?= esc($nombreNutricionista) ?></strong>,</p>
            <p>Un paciente ha solicitado una hora a través del sitio web. La cita quedó en estado <strong>reservada</strong> y debe ser revisada en el calendario.</p>
            <div class="info-box">
                <div class="info-row"><strong>Paciente:</strong> <?= esc($nombrePaciente) ?></div>
                <div class="info-row"><strong>Correo:</strong> <?= esc($emailPaciente) ?></div>
                <?php if (!empty($telefonoPaciente)): ?>
                <div class="info-row"><strong>Teléfono:</strong> <?= esc($telefonoPaciente) ?></div>
                <?php endif; ?>
                <div class="info-row"><strong>Fecha:</strong> <?= esc($fecha) ?></div>
                <div class="info-row"><strong>Horario:</strong> <?= esc($horaInicio) ?><?= !empty($horaFin) ? ' - ' . esc($horaFin) : '' ?></div>
            </div>
            <p>Ingresa al sistema para confirmar o gestionar la reserva.</p>
            <?php if (!empty($enlaceLista)): ?>
            <p style="text-align: center;">
                <a href="<?= esc($enlaceLista) ?>" class="btn">Ver en lista de citas</a>
            </p>
            <?php endif; ?>
        </div>
        <div class="footer">
            <p>Correo automático de NutriNext. No respondas a este mensaje.</p>
        </div>
    </div>
</body>
</html>

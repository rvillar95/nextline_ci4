<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Cancelación de Cita</title>
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
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            width: 40%;
        }
        .info-value {
            color: #333;
            width: 60%;
            text-align: right;
        }
        .alert-box {
            background-color: #FFF3CD;
            border: 1px solid #FFC107;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
        }
        .alert-box p {
            margin: 0;
            color: #856404;
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
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-presencial {
            background-color: #E3F2FD;
            color: #1976D2;
        }
        .badge-online {
            background-color: #F3E5F5;
            color: #7B1FA2;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="icon">❌</div>
            <h1>Cita Cancelada</h1>
        </div>
        
        <div class="content">
            <p>Hola <strong><?= esc($nombreNutricionista) ?></strong>,</p>
            
            <p>Te informamos que el paciente <strong><?= esc($nombrePaciente) ?></strong> ha cancelado la siguiente cita:</p>
            
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">👤 Paciente:</span>
                    <span class="info-value"><strong><?= esc($nombrePaciente) ?></strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">📅 Fecha:</span>
                    <span class="info-value"><strong><?= esc($fecha) ?></strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">🕐 Hora:</span>
                    <span class="info-value"><strong><?= esc($horaInicio) ?> - <?= esc($horaFin) ?></strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">📍 Modalidad:</span>
                    <span class="info-value">
                        <?php if (stripos($modalidad, 'presencial') !== false): ?>
                            <span class="badge badge-presencial">🏥 Presencial</span>
                        <?php elseif (stripos($modalidad, 'online') !== false): ?>
                            <span class="badge badge-online">💻 Online</span>
                        <?php else: ?>
                            <?= esc($modalidad) ?>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">📋 Tipo de Consulta:</span>
                    <span class="info-value"><?= esc($tipoConsulta) ?></span>
                </div>
                <?php if (!empty($motivo)): ?>
                <div class="info-row">
                    <span class="info-label">💬 Motivo Original:</span>
                    <span class="info-value"><?= esc($motivo) ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="alert-box">
                <p><strong>⚠️ Importante:</strong> El horario ha sido liberado automáticamente y está disponible nuevamente en tu agenda.</p>
            </div>
            
            <p style="color: #666; font-size: 14px; margin-top: 30px;">
                Puedes revisar tu agenda en el sistema para ver los horarios disponibles.
            </p>
        </div>
        
        <div class="footer">
            <p>Este es un email automático del sistema de gestión de citas.</p>
            <p>Para más información, accede a tu panel de control.</p>
        </div>
    </div>
</body>
</html>

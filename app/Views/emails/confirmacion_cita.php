<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Cita</title>
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
            background: linear-gradient(135deg, #6BCB77 0%, #4A90E2 100%);
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
            background: #f8f9fa;
            border-left: 4px solid #4A90E2;
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
        .buttons {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-confirmar {
            background-color: #6BCB77;
            color: white;
        }
        .btn-confirmar:hover {
            background-color: #5ab868;
        }
        .btn-cancelar {
            background-color: #E57373;
            color: white;
        }
        .btn-cancelar:hover {
            background-color: #d46464;
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
            <div class="icon">📅</div>
            <h1>Confirmación de Cita</h1>
        </div>
        
        <div class="content">
            <p>Hola <strong><?= esc($nombrePaciente) ?></strong>,</p>
            
            <p>Te informamos que se ha agendado una cita con <strong><?= esc($nombreNutricionista) ?></strong>.</p>
            
            <div class="info-box">
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
                    <span class="info-label">💬 Motivo:</span>
                    <span class="info-value"><?= esc($motivo) ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <p><strong>Por favor, confirma o cancela tu cita haciendo clic en uno de los botones a continuación:</strong></p>
            
            <div class="buttons">
                <a href="<?= esc($baseUrl) ?>confirmar-cita?token=<?= esc($tokenConfirmar) ?>" class="btn btn-confirmar">
                    ✅ Confirmar Cita
                </a>
                <a href="<?= esc($baseUrl) ?>cancelar-cita?token=<?= esc($tokenCancelar) ?>" class="btn btn-cancelar">
                    ❌ Cancelar Cita
                </a>
            </div>
            
            <p style="color: #666; font-size: 14px; margin-top: 30px;">
                <strong>Nota:</strong> Si no puedes asistir, por favor cancela la cita con al menos 24 horas de anticipación.
            </p>
        </div>
        
        <div class="footer">
            <p>Este es un email automático, por favor no respondas a este mensaje.</p>
            <p>Si tienes alguna consulta, contacta directamente con tu nutricionista.</p>
        </div>
    </div>
</body>
</html>

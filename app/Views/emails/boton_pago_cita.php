<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago de Consulta</title>
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
        }
        .info-value {
            color: #333;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .payment-button {
            display: inline-block;
            background: linear-gradient(135deg, #009EE3 0%, #0070BA 100%);
            color: white !important;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(0, 158, 227, 0.3);
            transition: all 0.3s ease;
        }
        .payment-button:hover {
            background: linear-gradient(135deg, #0070BA 0%, #005A94 100%);
            box-shadow: 0 6px 20px rgba(0, 158, 227, 0.4);
            transform: translateY(-2px);
        }
        .amount-box {
            background: linear-gradient(135deg, #6BCB77 0%, #4A90E2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .amount-label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        .amount-value {
            font-size: 32px;
            font-weight: bold;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>💳 Pago de Consulta</h1>
        </div>
        
        <div class="content">
            <p>Hola <strong><?= esc($nombrePaciente) ?></strong>,</p>
            
            <p>Te enviamos el botón de pago para tu consulta:</p>
            
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Tipo de Consulta:</span>
                    <span class="info-value"><?= esc($titulo) ?></span>
                </div>
                <?php if (!empty($fecha)): ?>
                <div class="info-row">
                    <span class="info-label">Fecha:</span>
                    <span class="info-value"><?= esc($fecha) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($horaInicio)): ?>
                <div class="info-row">
                    <span class="info-label">Hora:</span>
                    <span class="info-value"><?= esc($horaInicio) ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="amount-box">
                <div class="amount-label">Monto a Pagar</div>
                <div class="amount-value"><?= esc($monto) ?></div>
            </div>
            
            <div class="button-container">
                <a href="<?= esc($botonUrl) ?>" class="payment-button" target="_blank">
                    💳 Pagar Ahora
                </a>
            </div>
            
            <div class="warning-box">
                <strong>⚠️ Importante:</strong> Haz clic en el botón "Pagar Ahora" para completar el pago de tu consulta. 
                El pago se procesará de forma segura a través de Mercado Pago.
            </div>
            
            <p style="margin-top: 30px;">
                Si tienes alguna pregunta, no dudes en contactarnos.
            </p>
            
            <p>
                Saludos,<br>
                <strong>Equipo NextLine Nutrición</strong>
            </p>
        </div>
        
        <div class="footer">
            <p>Este es un email automático, por favor no respondas a este mensaje.</p>
            <p>© <?= date('Y') ?> NextLine Nutrición. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>

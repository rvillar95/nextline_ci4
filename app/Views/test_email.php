<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Email - Local</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f5f5;
            padding: 40px 0;
        }
        .card {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .config-item {
            background: #f8f9fa;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            font-family: monospace;
        }
        .status-ok { color: #6BCB77; }
        .status-error { color: #E57373; }
        .status-warning { color: #FFA726; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">📧 Prueba de Envío de Email</h3>
                    </div>
                    <div class="card-body">
                        <!-- Mensajes -->
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-success">
                                <?= esc($message) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger">
                                <strong>Error:</strong><br>
                                <pre style="white-space: pre-wrap;"><?= esc($error) ?></pre>
                            </div>
                        <?php endif; ?>

                        <!-- Configuración Actual -->
                        <h5 class="mt-4">⚙️ Configuración Actual</h5>
                        <div class="config-item">
                            <strong>From Email:</strong> 
                            <span class="<?= $config['fromEmail'] !== 'NO CONFIGURADO' ? 'status-ok' : 'status-error' ?>">
                                <?= esc($config['fromEmail']) ?>
                            </span>
                        </div>
                        <div class="config-item">
                            <strong>From Name:</strong> 
                            <span class="<?= $config['fromName'] !== 'NO CONFIGURADO' ? 'status-ok' : 'status-error' ?>">
                                <?= esc($config['fromName']) ?>
                            </span>
                        </div>
                        <div class="config-item">
                            <strong>SMTP Host:</strong> 
                            <span class="<?= $config['SMTPHost'] !== 'NO CONFIGURADO' ? 'status-ok' : 'status-error' ?>">
                                <?= esc($config['SMTPHost']) ?>
                            </span>
                        </div>
                        <div class="config-item">
                            <strong>SMTP User:</strong> 
                            <span class="<?= $config['SMTPUser'] !== 'NO CONFIGURADO' ? 'status-ok' : 'status-warning' ?>">
                                <?= $config['SMTPUser'] !== 'NO CONFIGURADO' ? esc($config['SMTPUser']) : 'Oculto por seguridad' ?>
                            </span>
                        </div>
                        <div class="config-item">
                            <strong>SMTP Port:</strong> <?= esc($config['SMTPPort']) ?>
                        </div>
                        <div class="config-item">
                            <strong>SMTP Crypto:</strong> <?= esc($config['SMTPCrypto'] ?: 'Ninguno') ?>
                        </div>

                        <!-- Formulario de Prueba -->
                        <hr>
                        <h5 class="mt-4">🚀 Enviar Email de Prueba</h5>
                        <form method="GET" action="<?= base_url('test-email') ?>">
                            <div class="mb-3">
                                <label for="to" class="form-label">Email Destino:</label>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="to" 
                                    name="to" 
                                    placeholder="tu-email@gmail.com"
                                    required
                                >
                                <small class="form-text text-muted">
                                    Ingresa tu email para recibir un mensaje de prueba
                                </small>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                📤 Enviar Email de Prueba
                            </button>
                        </form>

                        <!-- Instrucciones -->
                        <hr>
                        <div class="alert alert-info">
                            <h6>📝 Instrucciones:</h6>
                            <ol>
                                <li>Configura las variables de email en tu archivo <code>.env</code></li>
                                <li>Ingresa tu email en el formulario de arriba</li>
                                <li>Haz clic en "Enviar Email de Prueba"</li>
                                <li>Revisa tu bandeja de entrada (y spam)</li>
                            </ol>
                            <p class="mb-0">
                                <strong>Ver:</strong> <code>CONFIGURACION_EMAIL_LOCAL.md</code> para más opciones de configuración.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

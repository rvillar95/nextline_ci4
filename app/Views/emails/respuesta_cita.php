<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $exito ? 'Cita Confirmada' : 'Error al Procesar' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6BCB77 0%, #4A90E2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: none;
            border-radius: 15px;
        }
        .icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        .success-icon {
            color: #6BCB77;
        }
        .error-icon {
            color: #E57373;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-body text-center p-5">
            <?php if ($exito): ?>
                <div class="icon success-icon">✅</div>
                <h2 class="card-title mb-3 text-success">¡Operación Exitosa!</h2>
                <p class="card-text mb-4"><?= esc($mensaje) ?></p>
            <?php else: ?>
                <div class="icon error-icon">❌</div>
                <h2 class="card-title mb-3 text-danger">Error al Procesar</h2>
                <p class="card-text mb-4"><?= esc($mensaje) ?></p>
            <?php endif; ?>
            
            <a href="<?= base_url() ?>" class="btn btn-primary mt-3">
                Volver al Inicio
            </a>
        </div>
    </div>
</body>
</html>

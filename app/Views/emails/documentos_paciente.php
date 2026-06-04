<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos NutriNext</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background: #f4f4f4; }
        .email-container { max-width: 600px; margin: 0 auto; background: #fff; }
        .header { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: #fff; padding: 28px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; }
        .content { padding: 28px 20px; }
        .doc-item { background: #f8f9fa; border-left: 4px solid #4facfe; padding: 14px 16px; margin: 14px 0; border-radius: 4px; }
        .doc-item h3 { margin: 0 0 6px; font-size: 16px; color: #222; }
        .doc-meta { font-size: 13px; color: #666; margin-bottom: 8px; }
        .btn-link { display: inline-block; background: #4facfe; color: #fff !important; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-size: 14px; margin-top: 6px; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #888; border-top: 1px solid #eee; }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <h1>Sus documentos</h1>
    </div>
    <div class="content">
        <p>Hola <strong><?= esc($nombrePaciente) ?></strong>,</p>
        <p>
            <?= esc($nombreNutricionista) ?> le comparte los siguientes documentos de su atención nutricional.
            <?php if (!empty(array_filter($documentos, static fn ($d) => !empty($d['adjunto'])))): ?>
                Los archivos van adjuntos a este correo cuando el tamaño lo permite.
            <?php endif; ?>
            También puede descargarlos con los enlaces siguientes (válidos varios días).
        </p>

        <?php if (!empty($mensajePersonal)): ?>
        <div style="background: #e8f4fd; border-left: 4px solid #4facfe; padding: 14px 16px; margin: 18px 0; border-radius: 4px;">
            <p style="margin: 0 0 6px; font-size: 13px; color: #555; font-weight: bold;">Mensaje de su nutricionista:</p>
            <p style="margin: 0; font-size: 15px; white-space: pre-wrap;"><?= esc($mensajePersonal) ?></p>
        </div>
        <?php endif; ?>

        <?php foreach ($documentos as $doc): ?>
        <div class="doc-item">
            <h3><?= esc($doc['titulo']) ?></h3>
            <div class="doc-meta">
                <?= esc($doc['tipo']) ?> · <?= esc($doc['fecha']) ?>
                <?php if (!empty($doc['adjunto'])): ?>
                    · <em>Adjunto en este correo</em>
                <?php endif; ?>
            </div>
            <?php if (!empty($doc['descripcion'])): ?>
                <p style="margin: 8px 0 0; font-size: 14px;"><?= esc($doc['descripcion']) ?></p>
            <?php endif; ?>
            <?php if (!empty($doc['contenido']) && empty($doc['enlace'])): ?>
                <div style="margin-top: 10px; font-size: 14px; white-space: pre-wrap;"><?= esc($doc['contenido']) ?></div>
            <?php endif; ?>
            <?php if (!empty($doc['enlace'])): ?>
                <a class="btn-link" href="<?= esc($doc['enlace']) ?>">Descargar archivo</a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <p style="margin-top: 24px; font-size: 14px; color: #555;">
            Si tiene dudas, responda a este correo o contacte a su nutricionista.
        </p>
    </div>
    <div class="footer">
        Enviado desde NutriNext · No responda si no reconoce este mensaje.
    </div>
</div>
</body>
</html>

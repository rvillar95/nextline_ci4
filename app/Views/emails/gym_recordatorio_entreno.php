<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><title>Recordatorio entrenamiento</title></head>
<body style="font-family: Arial, sans-serif; line-height: 1.5; color: #222;">
    <p>Hola <?= esc($nombre ?: 'alumno') ?>,</p>
    <p>Hoy te corresponde entrenar <strong><?= esc($rutina) ?></strong>
        <?php if (!empty($programa)) : ?> (<?= esc($programa) ?>)<?php endif; ?>.</p>
    <p>Entrá al portal y registrá tu sesión cuando estés listo:</p>
    <p><a href="<?= esc($portal_url) ?>" style="display:inline-block;padding:10px 18px;background:#7bc143;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;">Ir a entrenar</a></p>
    <p style="font-size:12px;color:#666;"><?= esc($empresa) ?> · NutriNext Gym</p>
    <p style="font-size:11px;color:#999;">Podés desactivar estos avisos en Suscripción / Plan del portal alumno.</p>
</body>
</html>

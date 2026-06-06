<?= $this->extend('layout/alumno') ?>

<?= $this->section('content') ?>

<div class="nn-alumno-card">
    <h2>Mi suscripción</h2>
    <?php if ($suscripcion) : ?>
        <p class="mb-1"><strong>Estado:</strong> <?= esc($suscripcion->estado) ?></p>
        <p class="mb-0"><strong>Vence:</strong> <?= esc($suscripcion->fecha_fin ?? '-') ?></p>
    <?php else : ?>
        <p class="text-muted mb-0">Aún no tienes suscripción activa.</p>
    <?php endif; ?>
</div>

<div class="nn-alumno-card">
    <h3>Notificaciones</h3>
    <p class="text-muted small">Recibe un email en los días que tienes rutina asignada si aún no has entrenado.</p>
    <form method="POST" action="<?= base_url('alumno/suscripcion/recordatorios') ?>">
        <?= csrf_field() ?>
        <?php $rec = ($recibirRecordatorios ?? 'S') !== 'N'; ?>
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" role="switch" id="recRecordatorios" name="recibir" value="S"
                   <?= $rec ? 'checked' : '' ?>>
            <label class="form-check-label" for="recRecordatorios">Recordatorios de entrenamiento por email</label>
        </div>
        <button type="submit" class="btn nn-alumno-btn-primary">Guardar preferencias</button>
    </form>
</div>

<div class="nn-alumno-card">
    <h3>Planes</h3>
    <?php if (empty($planes)) : ?>
        <p class="text-muted mb-0">No hay planes disponibles.</p>
    <?php else : ?>
        <?php foreach ($planes as $p) : ?>
        <div class="border-bottom py-3">
            <h4 class="h6 mb-1"><?= esc($p->nombre) ?></h4>
            <p class="mb-2"><strong><?= esc($p->moneda ?? 'CLP') ?></strong> <?= esc($p->monto_mensual) ?> / mes</p>
            <?php if (!empty($p->descripcion)) : ?>
                <p class="text-muted small"><?= esc($p->descripcion) ?></p>
            <?php endif; ?>
            <form method="POST" action="<?= base_url('alumno/suscripcion/pagar') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="plan_id" value="<?= (int) $p->id ?>">
                <button type="submit" class="btn nn-alumno-btn-primary w-100">Pagar con Mercado Pago</button>
            </form>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

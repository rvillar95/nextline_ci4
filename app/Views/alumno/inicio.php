<?= $this->extend('layout/alumno') ?>

<?= $this->section('content') ?>

<div class="nn-alumno-card">
    <h2>Hola, <?= esc($usuario['nombre'] ?? '') ?></h2>
    <p class="text-muted mb-0">Tu espacio de entrenamiento</p>
</div>

<?php if (($racha ?? 0) > 0 || ($resumen30['sesiones'] ?? 0) > 0) : ?>
<div class="row g-2 mb-2">
    <div class="col-6">
        <a href="<?= base_url('alumno/progreso') ?>" class="text-decoration-none">
            <div class="nn-alumno-card nn-stat-card text-center py-3 mb-0">
                <div class="nn-stat-card__value"><?= (int) ($racha ?? 0) ?></div>
                <div class="nn-stat-card__label"><i class="fas fa-fire text-warning"></i> Racha días</div>
            </div>
        </a>
    </div>
    <div class="col-6">
        <a href="<?= base_url('alumno/progreso') ?>" class="text-decoration-none">
            <div class="nn-alumno-card nn-stat-card text-center py-3 mb-0">
                <div class="nn-stat-card__value"><?= (int) ($resumen30['sesiones'] ?? 0) ?></div>
                <div class="nn-stat-card__label">Sesiones (30 d)</div>
            </div>
        </a>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($rutinaHoy)) : ?>
<div class="nn-alumno-card nn-alumno-card--highlight">
    <h3><i class="fas fa-calendar-day text-success me-1"></i> Hoy toca</h3>
    <p class="mb-2"><strong><?= esc($rutinaHoy->rutina_nombre) ?></strong></p>
    <small class="text-muted d-block mb-3"><?= esc($rutinaHoy->programa_nombre ?? '') ?></small>
    <a href="<?= base_url('alumno/rutina/' . (int) $rutinaHoy->rutina_id) ?>" class="btn nn-alumno-btn-primary w-100">
        Ver rutina y entrenar
    </a>
</div>
<?php endif; ?>

<?php if (!empty($sesionEnCurso)) : ?>
<div class="nn-alumno-card border-warning">
    <h3><i class="fas fa-play-circle text-warning me-1"></i> Sesión en curso</h3>
    <p class="mb-2">Tienes un entrenamiento sin terminar.</p>
    <a href="<?= base_url('alumno/entrenamiento/' . (int) $sesionEnCurso->id) ?>" class="btn btn-warning w-100">
        Continuar entrenamiento
    </a>
</div>
<?php endif; ?>

<div class="nn-alumno-card">
    <h3>Programas asignados</h3>
    <?php if (empty($asignaciones)) : ?>
        <p class="text-muted mb-0">Aún no tienes programas asignados.</p>
    <?php else : ?>
        <div class="list-group list-group-flush">
            <?php foreach ($asignaciones as $a) : ?>
                <a class="list-group-item list-group-item-action px-0 d-flex justify-content-between align-items-center"
                   href="<?= base_url('alumno/programa/' . (int) $a->programa_id) ?>">
                    <div>
                        <strong><?= esc($a->programa_nombre) ?></strong>
                        <small class="d-block text-muted">Estado: <?= esc($a->estado) ?></small>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

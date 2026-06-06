<?= $this->extend('layout/alumno') ?>

<?= $this->section('content') ?>

<div class="nn-alumno-card">
    <a href="<?= base_url('alumno/inicio') ?>" class="text-muted small text-decoration-none"><i class="fas fa-arrow-left"></i> Inicio</a>
    <h2 class="mt-2 mb-1"><?= esc($asign->rutina_nombre ?? 'Rutina') ?></h2>
    <small class="text-muted"><?= esc($asign->programa_nombre ?? '') ?></small>
    <?php if (!empty($asign->rutina_descripcion)) : ?>
        <p class="mt-2 mb-0"><?= esc($asign->rutina_descripcion) ?></p>
    <?php endif; ?>
</div>

<div class="nn-alumno-card">
    <h3>Ejercicios</h3>
    <?php if (empty($items)) : ?>
        <p class="text-muted mb-0">Sin ejercicios en esta rutina.</p>
    <?php else : ?>
        <ul class="list-unstyled mb-0">
            <?php foreach ($items as $it) : ?>
            <li class="py-2 border-bottom">
                <strong><?= esc($it->ejercicio_nombre) ?></strong>
                <small class="d-block text-muted">
                    <?= (int) ($it->series ?? 0) ?> series
                    <?php if (!empty($it->repeticiones)) : ?> · <?= esc($it->repeticiones) ?> reps<?php endif; ?>
                    <?php if (!empty($it->descanso_seg)) : ?> · <?= (int) $it->descanso_seg ?>s descanso<?php endif; ?>
                </small>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<?php if (session()->getFlashdata('conflicto_id')) : ?>
<div class="nn-alumno-card border-warning">
    <p class="mb-2">Ya tienes otra sesión en curso. ¿Qué deseas hacer?</p>
    <a href="<?= base_url('alumno/entrenamiento/' . (int) session()->getFlashdata('conflicto_id')) ?>" class="btn btn-outline-secondary w-100 mb-2">Continuar la anterior</a>
    <form method="post" action="<?= base_url('alumno/rutina/' . (int) $asign->rutina_id . '/iniciar') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="abandonar_anterior" value="1">
        <button type="submit" class="btn btn-warning w-100">Abandonar anterior y empezar esta</button>
    </form>
</div>
<?php elseif (!empty($sesion) && (int) $sesion->rutina_id === (int) $asign->rutina_id) : ?>
<a href="<?= base_url('alumno/entrenamiento/' . (int) $sesion->id) ?>" class="btn nn-alumno-btn-primary w-100 btn-lg">
    Continuar entrenamiento
</a>
<?php else : ?>
<form method="post" action="<?= base_url('alumno/rutina/' . (int) $asign->rutina_id . '/iniciar') ?>">
    <?= csrf_field() ?>
    <button type="submit" class="btn nn-alumno-btn-primary w-100 btn-lg" <?= empty($items) ? 'disabled' : '' ?>>
        <i class="fas fa-play me-1"></i> Empezar entrenamiento
    </button>
</form>
<?php endif; ?>

<?= $this->endSection() ?>

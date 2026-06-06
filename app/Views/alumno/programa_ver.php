<?= $this->extend('layout/alumno') ?>

<?= $this->section('content') ?>

<div class="nn-alumno-card">
    <a href="<?= base_url('alumno/inicio') ?>" class="text-muted small text-decoration-none"><i class="fas fa-arrow-left"></i> Inicio</a>
    <h2 class="mt-2 mb-1"><?= esc($asign->programa_nombre ?? 'Programa') ?></h2>
    <small class="text-muted">Estado: <?= esc($asign->estado ?? '') ?></small>
</div>

<div class="nn-alumno-card">
    <h3>Rutinas</h3>
    <?php if (empty($rutinas)) : ?>
        <p class="text-muted mb-0">Este programa no tiene rutinas todavía.</p>
    <?php else : ?>
        <?php
        $dias = [1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 7 => 'Dom'];
        foreach ($rutinas as $r) :
            $diaLabel = !empty($r->dia_semana) ? ($dias[(int) $r->dia_semana] ?? '') : '';
        ?>
        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
            <div>
                <strong><?= esc($r->rutina_nombre) ?></strong>
                <?php if ($diaLabel) : ?>
                    <small class="d-block text-muted"><?= esc($diaLabel) ?></small>
                <?php endif; ?>
            </div>
            <a href="<?= base_url('alumno/rutina/' . (int) $r->rutina_id) ?>" class="btn btn-sm nn-alumno-btn-primary">
                Entrenar
            </a>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

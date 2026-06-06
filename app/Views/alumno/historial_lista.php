<?= $this->extend('layout/alumno') ?>

<?= $this->section('content') ?>

<div class="nn-alumno-card">
    <h2>Historial</h2>
    <p class="text-muted mb-0">Tus últimas sesiones de entrenamiento</p>
</div>

<?php if (empty($historial)) : ?>
<div class="nn-alumno-card">
    <p class="text-muted mb-0">Aún no has registrado entrenamientos.</p>
</div>
<?php else : ?>
    <?php foreach ($historial as $h) :
        $estadoClass = $h->estado === 'completado' ? 'success' : 'secondary';
        $inicio = $h->iniciado_en ?? '';
        $fin = $h->finalizado_en ?? '';
        $dur = '';
        if ($inicio && $fin) {
            $mins = max(0, (int) round((strtotime($fin) - strtotime($inicio)) / 60));
            $dur = $mins . ' min';
        }
    ?>
    <a href="<?= base_url('alumno/historial/' . (int) $h->id) ?>" class="nn-alumno-card text-decoration-none text-dark d-block">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <strong><?= esc($h->rutina_nombre ?? 'Rutina') ?></strong>
                <small class="d-block text-muted"><?= esc(date('d/m/Y H:i', strtotime($inicio))) ?></small>
                <?php if ($dur) : ?><small class="text-muted"><?= esc($dur) ?></small><?php endif; ?>
            </div>
            <span class="badge bg-<?= esc($estadoClass) ?>"><?= esc(ucfirst($h->estado)) ?></span>
        </div>
    </a>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>

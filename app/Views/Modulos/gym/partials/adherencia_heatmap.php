<?php
$heatmap = $heatmap ?? [];
$pct28 = (int) ($adherencia28 ?? 0);
$badgeClass = $pct28 >= 75 ? 'success' : ($pct28 >= 50 ? 'warning' : 'danger');
?>
<div class="gym-adh-block mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
        <h4 class="h6 mb-0"><i class="fas fa-calendar-check me-1"></i> Actividad (4 semanas)</h4>
        <span class="badge bg-<?= esc($badgeClass) ?>"><?= $pct28 ?>% adherencia</span>
    </div>
    <?php if ($heatmap === []) : ?>
        <p class="text-muted small mb-0">Sin datos de entrenamiento en el periodo.</p>
    <?php else : ?>
        <div class="gym-heatmap" role="img" aria-label="Calendario de entrenamientos últimas 4 semanas">
            <?php foreach ($heatmap as $d) :
                $active = ! empty($d['entreno']);
            ?>
            <span class="gym-heatmap__cell <?= $active ? 'is-active' : '' ?>"
                  title="<?= esc($d['fecha'] ?? '') ?><?= $active ? ' — entrenó' : '' ?>">
                <span class="gym-heatmap__label"><?= esc($d['label'] ?? '') ?></span>
            </span>
            <?php endforeach; ?>
        </div>
        <p class="text-muted small mt-2 mb-0">
            <span class="gym-heatmap__legend gym-heatmap__cell is-active d-inline-block"></span> Sesión completada
            <span class="gym-heatmap__legend gym-heatmap__cell d-inline-block ms-2"></span> Sin actividad
        </p>
    <?php endif; ?>
</div>

<?php
$ent = $sesion['entrenamiento'];
$ejercicios = $sesion['ejercicios'];
?>
<?= $this->extend('layout/alumno') ?>

<?= $this->section('content') ?>

<div class="nn-alumno-card">
    <a href="<?= base_url('alumno/historial') ?>" class="text-muted small text-decoration-none"><i class="fas fa-arrow-left"></i> Historial</a>
    <h2 class="mt-2 mb-1"><?= esc($ent->rutina_nombre ?? 'Sesión') ?></h2>
    <small class="text-muted d-block"><?= esc(date('d/m/Y H:i', strtotime($ent->iniciado_en ?? 'now'))) ?></small>
    <span class="badge bg-<?= ($ent->estado ?? '') === 'completado' ? 'success' : 'secondary' ?> mt-2">
        <?= esc(ucfirst($ent->estado ?? '')) ?>
    </span>
    <?php if (!empty($ent->notas)) : ?>
        <p class="mt-2 mb-0"><em><?= esc($ent->notas) ?></em></p>
    <?php endif; ?>
</div>

<?php foreach ($ejercicios as $bloque) :
    $ej = $bloque['ejercicio'];
    $series = $bloque['series'];
    $hechas = array_filter($series, [\App\Services\Gym\EntrenamientoService::class, 'serieTieneRegistro']);
    $prSeries = $prSeries ?? [];
    $mejorVolumenSeries = $mejorVolumenSeries ?? [];
?>
<div class="nn-alumno-card">
    <h3 class="h6"><?= esc($ej->nombre_ejercicio) ?></h3>
    <?php if (empty($hechas)) : ?>
        <p class="text-muted small mb-0">Sin series registradas</p>
    <?php else : ?>
        <ul class="list-unstyled small mb-0 nn-historial-series">
            <?php foreach ($hechas as $s) :
                $serieId = (int) $s->id;
                $esPrPeso = !empty($prSeries[$serieId]);
                $volSerie = null;
                if ($s->peso_kg !== null && $s->repeticiones !== null && (float) $s->peso_kg > 0 && (int) $s->repeticiones > 0) {
                    $volSerie = (int) round((float) $s->peso_kg * (int) $s->repeticiones);
                }
                $esMejorVol = isset($mejorVolumenSeries[$serieId]);
            ?>
            <li class="nn-historial-serie-row py-1 border-bottom">
                <span>
                    Serie <?= (int) $s->numero_serie ?>:
                    <?php if ($s->peso_kg !== null) : ?><?= esc($s->peso_kg) ?> kg<?php endif; ?>
                    <?php if ($s->repeticiones !== null) : ?> × <?= (int) $s->repeticiones ?> reps<?php endif; ?>
                    <?php if ($volSerie !== null) : ?>
                        <span class="text-muted"> · <?= number_format($volSerie, 0, ',', '.') ?> kg</span>
                    <?php endif; ?>
                </span>
                <span class="nn-historial-serie-badges">
                    <?php if ($esMejorVol) : ?>
                    <span class="nn-serie-vol-badge" title="Mayor carga en esta sesión: <?= (int) ($mejorVolumenSeries[$serieId] ?? $volSerie) ?> kg (peso × reps)">
                        <i class="fas fa-star" aria-hidden="true"></i>
                        <span class="visually-hidden">Mayor carga de la sesión</span>
                    </span>
                    <?php endif; ?>
                    <?php if ($esPrPeso) : ?>
                    <span class="nn-serie-pr-badge" title="Récord personal de peso">
                        <i class="fas fa-trophy" aria-hidden="true"></i>
                        <span class="visually-hidden">Récord personal de peso</span>
                    </span>
                    <?php endif; ?>
                </span>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <?php if (!empty(trim((string) ($ej->sensacion ?? '')))) : ?>
        <p class="small mb-0 mt-2 nn-historial-sensacion">
            <i class="far fa-smile text-muted" aria-hidden="true"></i>
            <span class="text-muted">Sensación:</span> <?= esc($ej->sensacion) ?>
        </p>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<?= $this->endSection() ?>

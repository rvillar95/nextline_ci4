<?php
$ent = $sesion['entrenamiento'];
$ejercicios = $sesion['ejercicios'];
?>
<?= $this->extend('layout/alumno') ?>

<?= $this->section('content') ?>

<div class="nn-alumno-card mb-2">
    <div class="d-flex justify-content-between align-items-start nn-entrenar-head">
        <div>
            <h2 class="h5 mb-0"><?= esc($ent->rutina_nombre ?? 'Rutina') ?></h2>
            <small class="text-muted"><?= esc($ent->programa_nombre ?? '') ?></small>
        </div>
        <form method="post" action="<?= base_url('alumno/entrenamiento/' . (int) $ent->id . '/abandonar') ?>" id="nnFormAbandonar">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-sm btn-outline-danger">Salir</button>
        </form>
    </div>
</div>

<?php if (!empty($notasCoachPrograma)) : ?>
<div class="nn-coach-note nn-coach-note--programa nn-alumno-card mb-2" role="note">
    <div class="nn-coach-note__label"><i class="fas fa-user-tie" aria-hidden="true"></i> Tu entrenador</div>
    <p class="nn-coach-note__text mb-0"><?= esc($notasCoachPrograma) ?></p>
</div>
<?php endif; ?>

<div id="nnEntrenarApp"
     data-entrenamiento-id="<?= (int) $ent->id ?>"
     data-csrf-name="<?= esc(csrf_token()) ?>"
     data-csrf-hash="<?= esc(csrf_hash()) ?>">

    <?php
    $totalSeries = 0;
    $seriesHechas = 0;
    foreach ($ejercicios as $bloque) {
        foreach ($bloque['series'] as $s) {
            $totalSeries++;
            if (($s->completada ?? 'N') === 'S' || $s->peso_kg !== null || $s->repeticiones !== null) {
                $seriesHechas++;
            }
        }
    }
    $pctSeries = $totalSeries > 0 ? (int) round(($seriesHechas / $totalSeries) * 100) : 0;
    ?>

    <div class="nn-entrenar-summary nn-alumno-card mb-2">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span id="nnEntrenarEjLabel" class="small fw-bold text-muted">
                <?= $seriesHechas ?> / <?= $totalSeries ?> series
            </span>
            <span class="small text-muted"><?= count($ejercicios) ?> ejercicios</span>
        </div>
        <div class="nn-entrenar-progress">
            <div id="nnEntrenarProgressBar" class="nn-entrenar-progress__bar" style="width:<?= $pctSeries ?>%"></div>
        </div>
    </div>

    <nav class="nn-ejercicio-jump" aria-label="Ir a ejercicio">
        <?php foreach ($ejercicios as $i => $bloque) :
            $ej = $bloque['ejercicio'];
            $doneEj = 0;
            $totEj = count($bloque['series']);
            foreach ($bloque['series'] as $s) {
                if (($s->completada ?? 'N') === 'S' || $s->peso_kg !== null || $s->repeticiones !== null) {
                    $doneEj++;
                }
            }
            $nombreCorto = mb_strlen($ej->nombre_ejercicio) > 18
                ? mb_substr($ej->nombre_ejercicio, 0, 16) . '…'
                : $ej->nombre_ejercicio;
        ?>
        <button type="button"
                class="nn-ej-jump-chip <?= $doneEj >= $totEj && $totEj > 0 ? 'is-complete' : '' ?>"
                data-target="ej-<?= $i ?>"
                data-done="<?= $doneEj ?>"
                data-total="<?= $totEj ?>">
            <span class="nn-ej-jump-chip__num"><?= $i + 1 ?></span>
            <span class="nn-ej-jump-chip__name"><?= esc($nombreCorto) ?></span>
            <?php if ($doneEj >= $totEj && $totEj > 0) : ?><i class="fas fa-check nn-ej-jump-chip__check" aria-hidden="true"></i><?php endif; ?>
        </button>
        <?php endforeach; ?>
    </nav>

    <div class="nn-ejercicio-list">
    <?php foreach ($ejercicios as $i => $bloque) :
        $ej = $bloque['ejercicio'];
        $series = $bloque['series'];
        $ultimoPeso = $ultimosPesos[(int) $ej->ejercicio_id] ?? null;
        $doneEj = 0;
        foreach ($series as $s) {
            if (($s->completada ?? 'N') === 'S' || $s->peso_kg !== null || $s->repeticiones !== null) {
                $doneEj++;
            }
        }
    ?>
    <article class="nn-alumno-card nn-ejercicio-panel" id="ej-<?= $i ?>" data-index="<?= $i ?>">
        <header class="nn-ejercicio-panel__head">
            <span class="nn-ejercicio-panel__order"><?= $i + 1 ?></span>
            <div>
                <h3 class="nn-ejercicio-panel__title"><?= esc($ej->nombre_ejercicio) ?></h3>
                <?php if (!empty($ej->reps_planificadas)) : ?>
                    <p class="text-muted small mb-0">Objetivo: <?= esc($ej->series_planificadas) ?> × <?= esc($ej->reps_planificadas) ?></p>
                <?php endif; ?>
            </div>
            <div class="nn-ejercicio-panel__actions">
                <?php
                $videoUrl = gym_normalizar_video_url($ej->video_url ?? null);
                if ($videoUrl) :
                ?>
                <a href="<?= esc($videoUrl) ?>" class="nn-ejercicio-video" target="_blank" rel="noopener"
                   aria-label="Ver video de <?= esc($ej->nombre_ejercicio) ?>">
                    <i class="fas fa-play-circle" aria-hidden="true"></i>
                </a>
                <?php endif; ?>
                <span class="nn-ejercicio-panel__badge" data-ej-badge="<?= $i ?>"><?= $doneEj ?>/<?= count($series) ?></span>
            </div>
        </header>
        <?php
        $notaCoach = trim((string) ($ej->notas_coach ?? $ej->notas_plan ?? ''));
        if ($notaCoach !== '') :
        ?>
        <div class="nn-coach-note nn-coach-note--ejercicio mb-2" role="note">
            <div class="nn-coach-note__label"><i class="fas fa-comment-dots" aria-hidden="true"></i> Indicación</div>
            <p class="nn-coach-note__text mb-0"><?= esc($notaCoach) ?></p>
        </div>
        <?php endif; ?>
        <?php if ($ultimoPeso !== null) : ?>
            <p class="small text-success mb-2"><i class="fas fa-weight-hanging"></i> Último peso: <?= esc($ultimoPeso) ?> kg</p>
        <?php endif; ?>

        <div class="nn-serie-header">
            <span class="nn-serie-header__num">#</span>
            <span class="nn-serie-header__inp">Peso</span>
            <span class="nn-serie-header__inp">Reps</span>
            <span class="nn-serie-header__done">Listo</span>
        </div>
        <?php foreach ($series as $s) :
            $descanso = (int) ($ej->descanso_seg ?? 0);
            $hecha = ($s->completada ?? 'N') === 'S'
                || $s->peso_kg !== null
                || $s->repeticiones !== null;
        ?>
        <div class="nn-serie-row <?= $hecha ? 'is-done' : '' ?>"
             data-serie-id="<?= (int) $s->id ?>"
             data-descanso="<?= $descanso ?>">
            <span class="nn-serie-row__num"><?= (int) $s->numero_serie ?></span>
            <input type="number" step="0.5" min="0" class="form-control nn-inp-peso"
                   value="<?= $s->peso_kg !== null ? esc($s->peso_kg) : '' ?>"
                   placeholder="kg" inputmode="decimal" aria-label="Peso serie <?= (int) $s->numero_serie ?>">
            <input type="number" min="0" class="form-control nn-inp-reps"
                   value="<?= $s->repeticiones !== null ? (int) $s->repeticiones : '' ?>"
                   placeholder="reps" inputmode="numeric" aria-label="Reps serie <?= (int) $s->numero_serie ?>">
            <button type="button"
                    class="nn-btn-serie-done"
                    aria-label="Marcar serie <?= (int) $s->numero_serie ?> como hecha"
                    aria-pressed="<?= $hecha ? 'true' : 'false' ?>">
                <i class="fas fa-check" aria-hidden="true"></i>
            </button>
        </div>
        <?php endforeach; ?>

        <div class="nn-ejercicio-sensacion mt-2">
            <label class="nn-ejercicio-sensacion__label" for="sensacion-<?= (int) $ej->id ?>">
                <i class="far fa-smile" aria-hidden="true"></i> ¿Cómo te sentiste?
            </label>
            <textarea id="sensacion-<?= (int) $ej->id ?>"
                      class="form-control nn-inp-sensacion"
                      rows="2"
                      maxlength="500"
                      data-ee-id="<?= (int) $ej->id ?>"
                      placeholder="Ej. bien, fatigado, dolor leve en hombro..."><?= esc($ej->sensacion ?? '') ?></textarea>
            <span class="nn-ejercicio-sensacion__status" data-sensacion-status="<?= (int) $ej->id ?>" aria-live="polite"></span>
        </div>
    </article>
    <?php endforeach; ?>
    </div>
</div>

<form method="post" action="<?= base_url('alumno/entrenamiento/' . (int) $ent->id . '/finalizar') ?>" class="mt-3 nn-entrenar-footer" id="nnFormFinalizar">
    <?= csrf_field() ?>
    <textarea name="notas" class="form-control mb-2" rows="2" placeholder="Notas de la sesión (opcional)"></textarea>
    <button type="submit" class="btn nn-alumno-btn-primary w-100 btn-lg">
        <i class="fas fa-check-circle me-1"></i> Finalizar entrenamiento
    </button>
</form>

<div id="nnRestTimer" class="nn-rest-timer" hidden aria-live="polite">
    <div class="nn-rest-timer__progress" aria-hidden="true">
        <div id="nnRestTimerBar" class="nn-rest-timer__progress-bar"></div>
    </div>
    <div class="nn-rest-timer__body">
        <span id="nnRestTimerLabel" class="nn-rest-timer__label">Descanso</span>
        <span id="nnTimerDisplay" class="nn-rest-timer__time">0:00</span>
    </div>
    <div class="nn-rest-timer__adjust">
        <button type="button" class="nn-rest-timer__adj" id="nnBtnRestMinus" aria-label="Restar 15 segundos">−15</button>
        <button type="button" class="nn-rest-timer__adj" id="nnBtnRestPlus" aria-label="Agregar 15 segundos">+15</button>
    </div>
    <button type="button" class="nn-rest-timer__skip" id="nnBtnSkipTimer" aria-label="Cerrar cronómetro">
        <i class="fas fa-times"></i>
    </button>
</div>

<div id="nnPrToast" class="nn-pr-toast" aria-live="polite" hidden></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('lib/js/nutrinext-alumno-entrenar.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var f = document.getElementById('nnFormAbandonar');
    if (f && typeof confirmarFormulario === 'function') {
        confirmarFormulario(f, '¿Abandonar esta sesión? Los datos guardados se conservan.', {
            titulo: 'Abandonar entrenamiento',
            botonOk: 'Abandonar',
            botonClase: 'btn-warning',
            mostrarHint: false
        });
    }
});
</script>
<?= $this->endSection() ?>

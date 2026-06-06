<?php
$ent = $sesion['entrenamiento'];
$ejercicios = $sesion['ejercicios'];
$alumnoNombre = trim(($alumno->nombre ?? '') . ' ' . ($alumno->apellido ?? ''));
$alumnoId = (int) $alumno->id;
$entId = (int) $ent->id;
$baseAlumno = base_url('dashboard/gym/alumno/' . $alumnoId);
?>
<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/alumno/entrenamiento_detalle') ?>

<?= $this->include('Modulos/gym/partials/assets') ?>

<div class="container-fluid gym-page gym-page--alumno">
    <?= view('Modulos/gym/partials/page_header', [
        'icon' => 'fa-clipboard-list',
        'title' => $ent->rutina_nombre ?? 'Sesión',
        'subtitle' => $alumnoNombre . ' · ' . date('d/m/Y H:i', strtotime($ent->iniciado_en ?? 'now')),
        'module' => 'alumno',
        'back' => ['url' => $baseAlumno . '/entrenamientos', 'label' => 'Historial'],
    ]) ?>

    <?= $this->include('Modulos/gym/partials/flash') ?>

    <div class="gym-section-card mb-4">
        <div class="gym-section-card__body py-3">
            <span class="badge bg-<?= ($ent->estado ?? '') === 'completado' ? 'success' : 'secondary' ?> me-2">
                <?= esc(ucfirst($ent->estado ?? '')) ?>
            </span>
            <?php if (!empty($ent->notas)) : ?>
                <p class="mb-0 mt-2"><em><?= esc($ent->notas) ?></em></p>
            <?php endif; ?>
            <?php if (!empty($sesionAnteriorId)) :
                $prevUrl = $baseAlumno . '/comparar?a=' . (int) $sesionAnteriorId . '&b=' . $entId;
            ?>
            <div class="mt-3 p-3 gym-compare-hint rounded">
                <i class="fas fa-exchange-alt me-1"></i>
                <strong>Última vs esta sesión:</strong>
                compara con la sesión anterior de la misma rutina.
                <a href="<?= esc($prevUrl) ?>" class="btn btn-sm gym-btn-primary ms-2">Ver comparación</a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php foreach ($ejercicios as $bloque) :
        $ej = $bloque['ejercicio'];
        $series = $bloque['series'];
        $hechas = array_filter($series, [\App\Services\Gym\EntrenamientoService::class, 'serieTieneRegistro']);
    ?>
    <div class="gym-section-card mb-3">
        <div class="gym-section-card__head">
            <h3 class="h6 mb-0"><?= esc($ej->nombre_ejercicio) ?></h3>
        </div>
        <div class="gym-section-card__body">
            <?php if (empty($hechas)) : ?>
                <p class="text-muted small mb-0">Sin series registradas</p>
            <?php else : ?>
                <ul class="list-unstyled small mb-0 gym-serie-list">
                    <?php foreach ($hechas as $s) :
                        $vol = null;
                        if ($s->peso_kg !== null && $s->repeticiones !== null) {
                            $vol = (int) round((float) $s->peso_kg * (int) $s->repeticiones);
                        }
                    ?>
                    <li class="py-1 border-bottom">
                        Serie <?= (int) $s->numero_serie ?>:
                        <?php if ($s->peso_kg !== null) : ?><?= esc($s->peso_kg) ?> kg<?php endif; ?>
                        <?php if ($s->repeticiones !== null) : ?> × <?= (int) $s->repeticiones ?> reps<?php endif; ?>
                        <?php if ($vol !== null) : ?>
                            <span class="text-muted"> · <?= number_format($vol, 0, ',', '.') ?> kg vol.</span>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if (!empty(trim((string) ($ej->sensacion ?? '')))) : ?>
                <p class="small mb-0 mt-2 gym-alumno-sensacion">
                    <i class="far fa-smile text-muted" aria-hidden="true"></i>
                    <strong>Sensación del alumno:</strong> <?= esc($ej->sensacion) ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?= $this->endSection() ?>

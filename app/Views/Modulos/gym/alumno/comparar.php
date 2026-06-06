<?php
$alumnoNombre = trim(($alumno->nombre ?? '') . ' ' . ($alumno->apellido ?? ''));
$alumnoId = (int) $alumno->id;
$baseAlumno = base_url('dashboard/gym/alumno/' . $alumnoId);
$cmp = $comparacion ?? null;
?>
<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/alumno/comparar') ?>

<?= $this->include('Modulos/gym/partials/assets') ?>

<div class="container-fluid gym-page gym-page--alumno">
    <?= view('Modulos/gym/partials/page_header', [
        'icon' => 'fa-columns',
        'title' => 'Comparar sesiones',
        'subtitle' => $alumnoNombre,
        'module' => 'alumno',
        'back' => ['url' => $baseAlumno . '/entrenamientos', 'label' => 'Historial'],
    ]) ?>

    <?= $this->include('Modulos/gym/partials/flash') ?>

    <div class="gym-section-card mb-4">
        <div class="gym-section-card__head">
            <h3><i class="fas fa-sliders-h"></i> Elegir sesiones</h3>
        </div>
        <div class="gym-section-card__body">
            <form method="get" action="<?= esc($baseAlumno . '/comparar') ?>" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Sesión A (referencia)</label>
                    <select name="a" class="form-select" required>
                        <option value="">— Seleccionar —</option>
                        <?php foreach (($entrenamientos ?? []) as $t) :
                            $tid = (int) $t->id;
                            $label = date('d/m/Y H:i', strtotime($t->iniciado_en ?? 'now'))
                                . ' — ' . ($t->rutina_nombre ?? 'Rutina');
                        ?>
                        <option value="<?= $tid ?>" <?= (int) ($idA ?? 0) === $tid ? 'selected' : '' ?>><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Sesión B (más reciente)</label>
                    <select name="b" class="form-select" required>
                        <option value="">— Seleccionar —</option>
                        <?php foreach (($entrenamientos ?? []) as $t) :
                            $tid = (int) $t->id;
                            $label = date('d/m/Y H:i', strtotime($t->iniciado_en ?? 'now'))
                                . ' — ' . ($t->rutina_nombre ?? 'Rutina');
                        ?>
                        <option value="<?= $tid ?>" <?= (int) ($idB ?? 0) === $tid ? 'selected' : '' ?>><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn gym-btn-primary w-100">Comparar</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($errorCompare)) : ?>
        <div class="alert alert-warning"><?= esc($errorCompare) ?></div>
    <?php endif; ?>

    <?php if ($cmp) :
        $entA = $cmp['sesion_a'];
        $entB = $cmp['sesion_b'];
        $fechaA = date('d/m/Y', strtotime($entA->iniciado_en ?? 'now'));
        $fechaB = date('d/m/Y', strtotime($entB->iniciado_en ?? 'now'));
    ?>
    <div class="gym-section-card">
        <div class="gym-section-card__head">
            <div>
                <h3><i class="fas fa-chart-bar"></i> <?= esc($cmp['rutina_nombre'] ?? 'Comparación') ?></h3>
                <?php if (empty($cmp['misma_rutina'])) : ?>
                    <p class="text-warning small mb-0"><i class="fas fa-info-circle"></i> Rutinas distintas: solo ejercicios en común.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="gym-section-card__body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0 gym-compare-table">
                    <thead>
                        <tr>
                            <th>Ejercicio</th>
                            <th>Sesión A<br><small class="text-muted fw-normal"><?= esc($fechaA) ?></small></th>
                            <th>Sesión B<br><small class="text-muted fw-normal"><?= esc($fechaB) ?></small></th>
                            <th>Δ peso</th>
                            <th>Δ volumen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cmp['filas'] as $fila) :
                            $a = $fila['a'];
                            $b = $fila['b'];
                        ?>
                        <tr>
                            <td class="fw-semibold"><?= esc($fila['nombre']) ?></td>
                            <td>
                                <?php if ($a && !empty($a['series'])) : ?>
                                    <?php foreach ($a['series'] as $s) : ?>
                                        <div class="small"><?= (int) $s['numero'] ?>. <?= $s['peso'] !== null ? esc($s['peso']) . ' kg' : '—' ?> × <?= $s['reps'] ?? '—' ?></div>
                                    <?php endforeach; ?>
                                    <?php if ($a['volumen'] > 0) : ?>
                                        <div class="text-muted small mt-1">Vol. <?= number_format($a['volumen'], 0, ',', '.') ?> kg</div>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($b && !empty($b['series'])) : ?>
                                    <?php foreach ($b['series'] as $s) : ?>
                                        <div class="small"><?= (int) $s['numero'] ?>. <?= $s['peso'] !== null ? esc($s['peso']) . ' kg' : '—' ?> × <?= $s['reps'] ?? '—' ?></div>
                                    <?php endforeach; ?>
                                    <?php if ($b['volumen'] > 0) : ?>
                                        <div class="text-muted small mt-1">Vol. <?= number_format($b['volumen'], 0, ',', '.') ?> kg</div>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $dp = $fila['delta_peso'];
                                if ($dp === null) :
                                    echo '<span class="text-muted">—</span>';
                                elseif ($dp > 0) :
                                    echo '<span class="text-success fw-bold">+' . esc($dp) . ' kg</span>';
                                elseif ($dp < 0) :
                                    echo '<span class="text-danger fw-bold">' . esc($dp) . ' kg</span>';
                                else :
                                    echo '<span class="text-muted">=</span>';
                                endif;
                                ?>
                            </td>
                            <td>
                                <?php
                                $dv = $fila['delta_vol'];
                                if ($dv === null) :
                                    echo '<span class="text-muted">—</span>';
                                elseif ($dv > 0) :
                                    echo '<span class="text-success fw-bold">+' . number_format($dv, 0, ',', '.') . '</span>';
                                elseif ($dv < 0) :
                                    echo '<span class="text-danger fw-bold">' . number_format($dv, 0, ',', '.') . '</span>';
                                else :
                                    echo '<span class="text-muted">=</span>';
                                endif;
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

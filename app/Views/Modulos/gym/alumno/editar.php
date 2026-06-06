<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/alumno/editar') ?>

<?= $this->include('Modulos/gym/partials/assets') ?>

<div class="container-fluid gym-page gym-page--alumno">
    <?= view('Modulos/gym/partials/page_header', [
        'icon' => 'fa-user-edit',
        'title' => 'Editar alumno',
        'subtitle' => esc(trim(($alumno->nombre ?? '') . ' ' . ($alumno->apellido ?? ''))),
        'module' => 'alumno',
        'back' => ['url' => base_url('dashboard/gym/alumno/lista'), 'label' => 'Volver al listado'],
    ]) ?>

    <?= $this->include('Modulos/gym/partials/flash') ?>

    <div class="gym-section-card">
        <div class="gym-section-card__head">
            <h3><i class="fas fa-id-card"></i> Datos del alumno</h3>
        </div>
        <div class="gym-section-card__body">
            <form method="POST" action="<?= base_url('dashboard/gym/alumno/update') ?>" class="gym-form">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $alumno->id ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="<?= esc(old('nombre') ?? $alumno->nombre) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido</label>
                        <input type="text" name="apellido" class="form-control" value="<?= esc(old('apellido') ?? $alumno->apellido) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email" name="correo" class="form-control" value="<?= esc(old('correo') ?? $alumno->correo) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" value="<?= esc(old('telefono') ?? ($alumno->telefono ?? '')) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nueva clave <span class="text-muted fw-normal">(opcional)</span></label>
                        <input type="password" name="clave" class="form-control" value="" placeholder="Dejar vacío para no cambiar">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Estado</label>
                        <?php $estado = old('estado') ?? $alumno->estado; ?>
                        <select name="estado" class="form-select" required>
                            <option value="A" <?= $estado == 'A' ? 'selected' : '' ?>>Activo</option>
                            <option value="I" <?= $estado == 'I' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="gym-form__actions">
                    <button type="submit" class="btn gym-btn-primary"><i class="fas fa-save me-1"></i> Guardar cambios</button>
                    <a href="<?= base_url('dashboard/gym/alumno/lista') ?>" class="btn gym-btn-outline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($asignacionesActivas)) : ?>
    <div class="gym-section-card mt-4">
        <div class="gym-section-card__head">
            <h3><i class="fas fa-comment-medical"></i> Notas para el alumno</h3>
            <p class="gym-section-card__hint mb-0">El alumno las verá al entrenar. Las notas por ejercicio se definen en cada rutina (columna «Notas alumno»).</p>
        </div>
        <div class="gym-section-card__body">
            <?php foreach ($asignacionesActivas as $asig) : ?>
            <form method="POST" action="<?= base_url('dashboard/gym/alumno/update-notas-asignacion') ?>" class="gym-form mb-4 pb-4 border-bottom">
                <?= csrf_field() ?>
                <input type="hidden" name="asignacion_id" value="<?= (int) $asig->id ?>">
                <input type="hidden" name="alumno_id" value="<?= (int) $alumno->id ?>">
                <label class="form-label fw-semibold"><?= esc($asig->programa_nombre ?? 'Programa') ?></label>
                <textarea name="notas_coach" class="form-control mb-2" rows="4"
                          placeholder="Ej. Prioriza técnica esta semana, hidrátate bien antes de la sesión..."><?= esc($asig->notas_coach ?? '') ?></textarea>
                <button type="submit" class="btn btn-sm gym-btn-primary"><i class="fas fa-save me-1"></i> Guardar notas</button>
            </form>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="gym-section-card mt-4">
        <div class="gym-section-card__head d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h3 class="mb-0"><i class="fas fa-dumbbell"></i> Entrenamientos y adherencia</h3>
            <a href="<?= base_url('dashboard/gym/alumno/' . (int) $alumno->id . '/entrenamientos') ?>" class="btn btn-sm gym-btn-outline">
                Ver historial completo
            </a>
        </div>
        <div class="gym-section-card__body">
            <?php if (!empty($adherencia)) : ?>
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded text-center">
                        <div class="h3 mb-0 text-success"><?= (int) ($adherencia['porcentaje'] ?? 0) ?>%</div>
                        <small class="text-muted">Adherencia (7 días)</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded text-center">
                        <div class="h3 mb-0"><?= (int) ($adherencia['completadas'] ?? 0) ?></div>
                        <small class="text-muted">Sesiones completadas</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded text-center">
                        <div class="h3 mb-0"><?= (int) ($adherencia['planificadas'] ?? 0) ?></div>
                        <small class="text-muted">Referencia planificada</small>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?= view('Modulos/gym/partials/adherencia_heatmap', [
                'heatmap' => $heatmap ?? [],
                'adherencia28' => $adherencia28 ?? 0,
            ]) ?>

            <?php if (empty($entrenamientos)) : ?>
                <p class="text-muted mb-0">El alumno aún no registra entrenamientos en el portal.</p>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Rutina</th>
                                <th>Estado</th>
                                <th>Duración</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($entrenamientos as $t) :
                                $mins = '';
                                if (!empty($t->iniciado_en) && !empty($t->finalizado_en)) {
                                    $mins = max(0, (int) round((strtotime($t->finalizado_en) - strtotime($t->iniciado_en)) / 60)) . ' min';
                                }
                                $badge = ($t->estado ?? '') === 'completado' ? 'success' : (($t->estado ?? '') === 'en_curso' ? 'warning' : 'secondary');
                                $tid = (int) $t->id;
                            ?>
                            <tr>
                                <td><?= esc(date('d/m/Y H:i', strtotime($t->iniciado_en ?? 'now'))) ?></td>
                                <td><?= esc($t->rutina_nombre ?? '-') ?></td>
                                <td><span class="badge bg-<?= esc($badge) ?>"><?= esc(ucfirst($t->estado ?? '')) ?></span></td>
                                <td><?= esc($mins ?: '-') ?></td>
                                <td class="text-end">
                                    <a href="<?= base_url('dashboard/gym/alumno/' . (int) $alumno->id . '/entrenamiento/' . $tid) ?>" class="btn btn-sm gym-btn-outline">Detalle</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

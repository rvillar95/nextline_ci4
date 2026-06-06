<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/ejercicio/registro') ?>

<?= $this->include('Modulos/gym/partials/assets') ?>

<div class="container-fluid gym-page gym-page--ejercicio">
    <?= view('Modulos/gym/partials/page_header', [
        'icon' => 'fa-dumbbell',
        'title' => 'Nuevo ejercicio',
        'subtitle' => 'Completa los datos básicos. Podrás usarlo al armar rutinas.',
        'module' => 'ejercicio',
        'back' => ['url' => base_url('dashboard/gym/ejercicio/lista'), 'label' => 'Volver al listado'],
    ]) ?>

    <?= view('Modulos/gym/partials/workflow', ['active' => 'ejercicio']) ?>
    <?= $this->include('Modulos/gym/partials/flash') ?>

    <div class="gym-section-card">
        <div class="gym-section-card__head">
            <h3><i class="fas fa-pen"></i> Datos del ejercicio</h3>
        </div>
        <div class="gym-section-card__body">
            <form method="POST" action="<?= base_url('dashboard/gym/ejercicio/registrar') ?>" class="gym-form">
                <?= csrf_field() ?>

                <p class="gym-form__group-title">Información general</p>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre del ejercicio</label>
                        <input type="text" name="nombre" class="form-control" value="<?= esc(old('nombre') ?? '') ?>" placeholder="Ej. Press banca con mancuernas" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Grupo muscular principal</label>
                        <select name="grupo_muscular_principal_id" class="form-select" required>
                            <option value="">Seleccione</option>
                            <?php foreach (($grupos ?? []) as $g) : ?>
                                <option value="<?= (int) $g->id ?>" <?= old('grupo_muscular_principal_id') == $g->id ? 'selected' : '' ?>>
                                    <?= esc($g->nombre) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Grupo secundario <span class="text-muted fw-normal">(opcional)</span></label>
                        <select name="grupo_muscular_secundario_id" class="form-select">
                            <option value="">Sin secundario</option>
                            <?php foreach (($grupos ?? []) as $g) : ?>
                                <option value="<?= (int) $g->id ?>" <?= old('grupo_muscular_secundario_id') == $g->id ? 'selected' : '' ?>>
                                    <?= esc($g->nombre) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <p class="gym-form__group-title">Clasificación</p>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tipo de base</label>
                        <select name="tipo_base_id" class="form-select" required>
                            <option value="">Seleccione</option>
                            <?php foreach (($tiposBase ?? []) as $t) : ?>
                                <option value="<?= (int) $t->id ?>" <?= old('tipo_base_id') == $t->id ? 'selected' : '' ?>>
                                    <?= esc($t->nombre) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">¿Activo en catálogo?</label>
                        <select name="activo" class="form-select">
                            <option value="1" <?= old('activo', '1') == '1' ? 'selected' : '' ?>>Sí, visible</option>
                            <option value="0" <?= old('activo') == '0' ? 'selected' : '' ?>>No, oculto</option>
                        </select>
                    </div>
                </div>

                <p class="gym-form__group-title">Indicaciones para el alumno</p>
                <div class="mb-3">
                    <label class="form-label">Instrucciones / técnica</label>
                    <textarea name="instrucciones" class="form-control" rows="5" placeholder="Postura, respiración, errores comunes..."><?= esc(old('instrucciones') ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Video demostrativo <span class="text-muted fw-normal">(opcional)</span></label>
                    <input type="url" name="video_url" class="form-control" value="<?= esc(old('video_url') ?? '') ?>"
                           placeholder="https://www.youtube.com/watch?v=...">
                    <small class="text-muted">YouTube, Vimeo o enlace directo. El alumno lo verá al entrenar.</small>
                </div>

                <div class="gym-form__actions">
                    <button type="submit" class="btn gym-btn-primary">
                        <i class="fas fa-save me-1"></i> Guardar ejercicio
                    </button>
                    <a href="<?= base_url('dashboard/gym/ejercicio/lista') ?>" class="btn gym-btn-outline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

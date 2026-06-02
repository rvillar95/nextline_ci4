<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/ejercicio/editar') ?>

<?= $this->include('Modulos/gym/partials/assets') ?>

<div class="container-fluid gym-page gym-page--ejercicio">
    <?= view('Modulos/gym/partials/page_header', [
        'icon' => 'fa-dumbbell',
        'title' => 'Editar ejercicio',
        'subtitle' => esc($ejercicio->nombre ?? ''),
        'module' => 'ejercicio',
        'back' => ['url' => base_url('dashboard/gym/ejercicio/lista'), 'label' => 'Volver al listado'],
    ]) ?>

    <?= $this->include('Modulos/gym/partials/flash') ?>

    <div class="gym-section-card">
        <div class="gym-section-card__head">
            <h3><i class="fas fa-pen"></i> Datos del ejercicio</h3>
        </div>
        <div class="gym-section-card__body">
            <form method="POST" action="<?= base_url('dashboard/gym/ejercicio/update') ?>" class="gym-form">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $ejercicio->id ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre del ejercicio</label>
                        <input type="text" name="nombre" class="form-control" value="<?= esc(old('nombre') ?? $ejercicio->nombre) ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Grupo muscular principal</label>
                        <select name="grupo_muscular_principal_id" class="form-select" required>
                            <option value="">Seleccione</option>
                            <?php foreach (($grupos ?? []) as $g) : ?>
                                <?php $selected = (old('grupo_muscular_principal_id') !== null) ? (old('grupo_muscular_principal_id') == $g->id) : ((int) $ejercicio->grupo_muscular_principal_id === (int) $g->id); ?>
                                <option value="<?= (int) $g->id ?>" <?= $selected ? 'selected' : '' ?>><?= esc($g->nombre) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Grupo secundario <span class="text-muted fw-normal">(opcional)</span></label>
                        <select name="grupo_muscular_secundario_id" class="form-select">
                            <option value="">Sin secundario</option>
                            <?php foreach (($grupos ?? []) as $g) : ?>
                                <?php
                                $secOld = old('grupo_muscular_secundario_id');
                                $selected = ($secOld !== null)
                                    ? ($secOld == $g->id)
                                    : ((int) ($ejercicio->grupo_muscular_secundario_id ?? 0) === (int) $g->id);
                                ?>
                                <option value="<?= (int) $g->id ?>" <?= $selected ? 'selected' : '' ?>><?= esc($g->nombre) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tipo de base</label>
                        <select name="tipo_base_id" class="form-select" required>
                            <option value="">Seleccione</option>
                            <?php foreach (($tiposBase ?? []) as $t) : ?>
                                <?php $selected = (old('tipo_base_id') !== null) ? (old('tipo_base_id') == $t->id) : ((int) $ejercicio->tipo_base_id === (int) $t->id); ?>
                                <option value="<?= (int) $t->id ?>" <?= $selected ? 'selected' : '' ?>><?= esc($t->nombre) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Estado</label>
                        <?php $activoVal = old('activo'); ?>
                        <?php $activo = ($activoVal !== null) ? (int) $activoVal : (int) $ejercicio->activo; ?>
                        <select name="activo" class="form-select">
                            <option value="1" <?= $activo === 1 ? 'selected' : '' ?>>Activo</option>
                            <option value="0" <?= $activo === 0 ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Instrucciones</label>
                        <textarea name="instrucciones" class="form-control" rows="6"><?= esc(old('instrucciones') ?? ($ejercicio->instrucciones ?? '')) ?></textarea>
                    </div>
                </div>

                <div class="gym-form__actions">
                    <button type="submit" class="btn gym-btn-primary"><i class="fas fa-save me-1"></i> Guardar cambios</button>
                    <a href="<?= base_url('dashboard/gym/ejercicio/lista') ?>" class="btn gym-btn-outline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/ejercicio/editar') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Editar Ejercicio
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/gym/ejercicio/lista') ?>" class="btn btn-light">
                            Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors') !== null) : ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php if (!is_array(session()->getFlashdata('errors'))) : ?>
                                    <li><?= session()->getFlashdata('errors') ?></li>
                                <?php else : ?>
                                    <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success') !== null) : ?>
                        <div class="alert alert-success" role="alert">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= base_url('dashboard/gym/ejercicio/update') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $ejercicio->id ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" value="<?= esc(old('nombre') ?? $ejercicio->nombre) ?>" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Grupo principal</label>
                                <select name="grupo_muscular_principal_id" class="form-control" required>
                                    <option value="">Seleccione</option>
                                    <?php foreach (($grupos ?? []) as $g) : ?>
                                        <?php $selected = (old('grupo_muscular_principal_id') !== null) ? (old('grupo_muscular_principal_id') == $g->id) : ((int)$ejercicio->grupo_muscular_principal_id === (int)$g->id); ?>
                                        <option value="<?= (int) $g->id ?>" <?= $selected ? 'selected' : '' ?>>
                                            <?= esc($g->nombre) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Grupo secundario (opcional)</label>
                                <select name="grupo_muscular_secundario_id" class="form-control">
                                    <option value="">(Sin secundario)</option>
                                    <?php foreach (($grupos ?? []) as $g) : ?>
                                        <?php
                                        $secOld = old('grupo_muscular_secundario_id');
                                        $selected = ($secOld !== null)
                                            ? ($secOld == $g->id)
                                            : ((int)($ejercicio->grupo_muscular_secundario_id ?? 0) === (int)$g->id);
                                        ?>
                                        <option value="<?= (int) $g->id ?>" <?= $selected ? 'selected' : '' ?>>
                                            <?= esc($g->nombre) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Base</label>
                                <select name="tipo_base_id" class="form-control" required>
                                    <option value="">Seleccione</option>
                                    <?php foreach (($tiposBase ?? []) as $t) : ?>
                                        <?php $selected = (old('tipo_base_id') !== null) ? (old('tipo_base_id') == $t->id) : ((int)$ejercicio->tipo_base_id === (int)$t->id); ?>
                                        <option value="<?= (int) $t->id ?>" <?= $selected ? 'selected' : '' ?>>
                                            <?= esc($t->nombre) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Activo</label>
                                <select name="activo" class="form-control">
                                    <?php $activoVal = old('activo'); ?>
                                    <?php $activo = ($activoVal !== null) ? (int)$activoVal : (int)$ejercicio->activo; ?>
                                    <option value="1" <?= $activo === 1 ? 'selected' : '' ?>>Sí</option>
                                    <option value="0" <?= $activo === 0 ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Instrucciones</label>
                                <textarea name="instrucciones" class="form-control" rows="6"><?= esc(old('instrucciones') ?? ($ejercicio->instrucciones ?? '')) ?></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


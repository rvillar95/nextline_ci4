<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/ejercicio/registro') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus"></i> Nuevo Ejercicio
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

                    <form method="POST" action="<?= base_url('dashboard/gym/ejercicio/registrar') ?>">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" value="<?= esc(old('nombre') ?? '') ?>" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Grupo principal</label>
                                <select name="grupo_muscular_principal_id" class="form-control" required>
                                    <option value="">Seleccione</option>
                                    <?php foreach (($grupos ?? []) as $g) : ?>
                                        <option value="<?= (int) $g->id ?>" <?= old('grupo_muscular_principal_id') == $g->id ? 'selected' : '' ?>>
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
                                        <option value="<?= (int) $g->id ?>" <?= old('grupo_muscular_secundario_id') == $g->id ? 'selected' : '' ?>>
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
                                        <option value="<?= (int) $t->id ?>" <?= old('tipo_base_id') == $t->id ? 'selected' : '' ?>>
                                            <?= esc($t->nombre) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Activo</label>
                                <select name="activo" class="form-control">
                                    <option value="1" <?= old('activo', '1') == '1' ? 'selected' : '' ?>>Sí</option>
                                    <option value="0" <?= old('activo') == '0' ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Instrucciones</label>
                                <textarea name="instrucciones" class="form-control" rows="5"><?= esc(old('instrucciones') ?? '') ?></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


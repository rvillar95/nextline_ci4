<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/programa/registro') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus"></i> Nuevo Programa
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/gym/programa/lista') ?>" class="btn btn-light">Volver</a>
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

                    <form method="POST" action="<?= base_url('dashboard/gym/programa/registrar') ?>">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" value="<?= esc(old('nombre') ?? '') ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Duración (semanas)</label>
                                <input type="number" name="duracion_semanas" class="form-control" value="<?= esc(old('duracion_semanas') ?? '') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Activo</label>
                                <select name="activo" class="form-control">
                                    <option value="1" <?= old('activo', '1') == '1' ? 'selected' : '' ?>>Sí</option>
                                    <option value="0" <?= old('activo') == '0' ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="4"><?= esc(old('descripcion') ?? '') ?></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Crear programa</button>
                        <div class="text-muted mt-2">Luego podrás agregar rutinas en la pantalla de edición.</div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


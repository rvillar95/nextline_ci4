<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/alumno/editar') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-edit"></i> Editar Alumno
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/gym/alumno/lista') ?>" class="btn btn-light">Volver</a>
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

                    <form method="POST" action="<?= base_url('dashboard/gym/alumno/update') ?>">
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
                                <label class="form-label">Nueva clave (opcional)</label>
                                <input type="password" name="clave" class="form-control" value="">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado</label>
                                <?php $estado = old('estado') ?? $alumno->estado; ?>
                                <select name="estado" class="form-control" required>
                                    <option value="A" <?= $estado == 'A' ? 'selected' : '' ?>>Activo</option>
                                    <option value="I" <?= $estado == 'I' ? 'selected' : '' ?>>Inactivo</option>
                                </select>
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


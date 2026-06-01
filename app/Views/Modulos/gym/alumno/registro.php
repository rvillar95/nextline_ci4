<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/alumno/registro') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-plus"></i> Nuevo Alumno
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

                    <form method="POST" action="<?= base_url('dashboard/gym/alumno/registrar') ?>">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" value="<?= esc(old('nombre') ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Apellido</label>
                                <input type="text" name="apellido" class="form-control" value="<?= esc(old('apellido') ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Correo</label>
                                <input type="email" name="correo" class="form-control" value="<?= esc(old('correo') ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control" value="<?= esc(old('telefono') ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Clave</label>
                                <input type="password" name="clave" class="form-control" required>
                                <div class="text-muted mt-1">El alumno podrá cambiarla más adelante (Fase 2).</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado</label>
                                <select name="estado" class="form-control" required>
                                    <option value="A" <?= old('estado', 'A') == 'A' ? 'selected' : '' ?>>Activo</option>
                                    <option value="I" <?= old('estado') == 'I' ? 'selected' : '' ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Crear alumno</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


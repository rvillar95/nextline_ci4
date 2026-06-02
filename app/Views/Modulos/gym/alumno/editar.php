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
</div>

<?= $this->endSection() ?>

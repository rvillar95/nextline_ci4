<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/alumno/registro') ?>

<?= $this->include('Modulos/gym/partials/assets') ?>

<div class="container-fluid gym-page gym-page--alumno">
    <?= view('Modulos/gym/partials/page_header', [
        'icon' => 'fa-user-plus',
        'title' => 'Nuevo alumno',
        'subtitle' => 'Crea la cuenta del alumno. Podrá entrar al portal con correo y clave.',
        'module' => 'alumno',
        'back' => ['url' => base_url('dashboard/gym/alumno/lista'), 'label' => 'Volver al listado'],
    ]) ?>

    <?= view('Modulos/gym/partials/workflow', ['active' => 'alumno']) ?>
    <?= $this->include('Modulos/gym/partials/flash') ?>

    <div class="gym-section-card">
        <div class="gym-section-card__head">
            <h3><i class="fas fa-id-card"></i> Datos del alumno</h3>
        </div>
        <div class="gym-section-card__body">
            <form method="POST" action="<?= base_url('dashboard/gym/alumno/registrar') ?>" class="gym-form">
                <?= csrf_field() ?>
                <p class="gym-form__group-title">Identificación</p>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="<?= esc(old('nombre') ?? '') ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido</label>
                        <input type="text" name="apellido" class="form-control" value="<?= esc(old('apellido') ?? '') ?>" required>
                    </div>
                </div>
                <p class="gym-form__group-title">Contacto y acceso</p>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Correo (usuario de login)</label>
                        <input type="email" name="correo" class="form-control" value="<?= esc(old('correo') ?? '') ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" value="<?= esc(old('telefono') ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Clave inicial</label>
                        <input type="password" name="clave" class="form-control" required>
                        <small class="text-muted">Compártela de forma segura con el alumno.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Estado de la cuenta</label>
                        <select name="estado" class="form-select" required>
                            <option value="A" <?= old('estado', 'A') == 'A' ? 'selected' : '' ?>>Activo — puede ingresar</option>
                            <option value="I" <?= old('estado') == 'I' ? 'selected' : '' ?>>Inactivo — bloqueado</option>
                        </select>
                    </div>
                </div>
                <div class="gym-form__actions">
                    <button type="submit" class="btn gym-btn-primary"><i class="fas fa-save me-1"></i> Crear alumno</button>
                    <a href="<?= base_url('dashboard/gym/alumno/lista') ?>" class="btn gym-btn-outline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

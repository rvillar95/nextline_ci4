<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/programa/registro') ?>

<?= $this->include('Modulos/gym/partials/assets') ?>

<div class="container-fluid gym-page gym-page--programa">
    <?= view('Modulos/gym/partials/page_header', [
        'icon' => 'fa-calendar-week',
        'title' => 'Nuevo programa',
        'subtitle' => 'Planifica la duración en semanas. Luego enlazarás las rutinas.',
        'module' => 'programa',
        'back' => ['url' => base_url('dashboard/gym/programa/lista'), 'label' => 'Volver al listado'],
    ]) ?>

    <?= view('Modulos/gym/partials/workflow', ['active' => 'programa']) ?>
    <?= $this->include('Modulos/gym/partials/flash') ?>

    <div class="gym-section-card">
        <div class="gym-section-card__head">
            <h3><i class="fas fa-pen"></i> Datos del programa</h3>
        </div>
        <div class="gym-section-card__body">
            <form method="POST" action="<?= base_url('dashboard/gym/programa/registrar') ?>" class="gym-form">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Nombre del programa</label>
                        <input type="text" name="nombre" class="form-control" value="<?= esc(old('nombre') ?? '') ?>" placeholder="Ej. Hipertrofia 8 semanas" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Duración (semanas)</label>
                        <input type="number" name="duracion_semanas" class="form-control" min="1" value="<?= esc(old('duracion_semanas') ?? '') ?>" placeholder="8">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Estado</label>
                        <select name="activo" class="form-select">
                            <option value="1" <?= old('activo', '1') == '1' ? 'selected' : '' ?>>Activo</option>
                            <option value="0" <?= old('activo') == '0' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="4"><?= esc(old('descripcion') ?? '') ?></textarea>
                    </div>
                </div>
                <div class="gym-form__actions">
                    <button type="submit" class="btn gym-btn-primary"><i class="fas fa-arrow-right me-1"></i> Crear y armar rutinas</button>
                    <a href="<?= base_url('dashboard/gym/programa/lista') ?>" class="btn gym-btn-outline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

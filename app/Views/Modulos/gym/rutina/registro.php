<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/rutina/registro') ?>

<?= $this->include('Modulos/gym/partials/assets') ?>

<div class="container-fluid gym-page gym-page--rutina">
    <?= view('Modulos/gym/partials/page_header', [
        'icon' => 'fa-clipboard-list',
        'title' => 'Nueva rutina',
        'subtitle' => 'Define el nombre y la descripción. En el siguiente paso agregarás los ejercicios.',
        'module' => 'rutina',
        'back' => ['url' => base_url('dashboard/gym/rutina/lista'), 'label' => 'Volver al listado'],
    ]) ?>

    <?= view('Modulos/gym/partials/workflow', ['active' => 'rutina']) ?>
    <?= $this->include('Modulos/gym/partials/flash') ?>

    <div class="gym-section-card">
        <div class="gym-section-card__head">
            <h3><i class="fas fa-pen"></i> Datos de la rutina</h3>
        </div>
        <div class="gym-section-card__body">
            <form method="POST" action="<?= base_url('dashboard/gym/rutina/registrar') ?>" class="gym-form">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Nombre de la rutina</label>
                        <input type="text" name="nombre" class="form-control" value="<?= esc(old('nombre') ?? '') ?>" placeholder="Ej. Piernas — fuerza A" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Estado</label>
                        <select name="activo" class="form-select">
                            <option value="1" <?= old('activo', '1') == '1' ? 'selected' : '' ?>>Activa</option>
                            <option value="0" <?= old('activo') == '0' ? 'selected' : '' ?>>Inactiva</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Descripción <span class="text-muted fw-normal">(opcional)</span></label>
                        <textarea name="descripcion" class="form-control" rows="4" placeholder="Objetivo, nivel, notas para ti..."><?= esc(old('descripcion') ?? '') ?></textarea>
                    </div>
                </div>
                <div class="gym-form__actions">
                    <button type="submit" class="btn gym-btn-primary"><i class="fas fa-arrow-right me-1"></i> Crear y agregar ejercicios</button>
                    <a href="<?= base_url('dashboard/gym/rutina/lista') ?>" class="btn gym-btn-outline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

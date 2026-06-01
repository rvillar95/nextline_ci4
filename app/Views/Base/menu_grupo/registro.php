<?= $this->extend('layout/dashboard') ?>

<?= $this->section('menu_grupo/registro') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="main-header mb-3">
                <h2 class="text-white mb-0"><i class="fas fa-plus me-2"></i> Nueva sección de menú</h2>
            </div>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <?php if (is_array(session()->getFlashdata('errors'))): ?>
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                                <li><?= esc($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <?= esc(session()->getFlashdata('errors')) ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="post" action="<?= base_url('dashboard/menu-grupo/registrar') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Etiqueta visible *</label>
                            <input type="text" name="etiqueta" class="form-control" value="<?= old('etiqueta') ?>" required placeholder="Ej: Día a día">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug *</label>
                            <input type="text" name="slug" class="form-control" value="<?= old('slug') ?>" required placeholder="Ej: operacion">
                            <small class="text-muted">Identificador interno (letras, números, guiones).</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Orden *</label>
                                <input type="number" name="orden" class="form-control" value="<?= old('orden', '10') ?>" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado *</label>
                                <select name="estado" class="form-select">
                                    <option value="A" <?= old('estado', 'A') === 'A' ? 'selected' : '' ?>>Activo</option>
                                    <option value="I" <?= old('estado') === 'I' ? 'selected' : '' ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <a href="<?= base_url('dashboard/menu-grupo/lista') ?>" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

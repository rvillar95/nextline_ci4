<?= $this->extend('layout/dashboard') ?>

<?= $this->section('menu_grupo/detalle') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="main-header mb-3 d-flex justify-content-between align-items-center">
                <h2 class="text-white mb-0"><i class="fas fa-edit me-2"></i> Editar sección de menú</h2>
                <a href="<?= base_url('dashboard/menu-grupo/lista') ?>" class="btn btn-light btn-sm">Volver</a>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
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
                    <form method="post" action="<?= base_url('dashboard/menu-grupo/update') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $grupo['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Etiqueta visible *</label>
                            <input type="text" name="etiqueta" class="form-control" value="<?= esc($grupo['etiqueta']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug *</label>
                            <input type="text" name="slug" class="form-control" value="<?= esc($grupo['slug']) ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Orden *</label>
                                <input type="number" name="orden" class="form-control" value="<?= (int) $grupo['orden'] ?>" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado *</label>
                                <select name="estado" class="form-select">
                                    <option value="A" <?= $grupo['estado'] === 'A' ? 'selected' : '' ?>>Activo</option>
                                    <option value="I" <?= $grupo['estado'] === 'I' ? 'selected' : '' ?>>Inactivo</option>
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

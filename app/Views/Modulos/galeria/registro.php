<?= $this->extend('layout/dashboard') ?>

<?= $this->section('galeria/registro') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-images"></i> Registrar Nueva Imagen
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/galeria/lista') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= base_url('dashboard/galeria/registrar') ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nombre de la Galería *</label>
                                    <input type="text" name="nombre" class="form-control" 
                                           value="<?= old('nombre') ?>" required placeholder="Ej: Quincho Principal, Casa Residencial...">
                                    <small class="text-muted">Nombre descriptivo de la galería</small>
                                    <?php if (session()->getFlashdata('errors')['nombre'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['nombre']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Categoría *</label>
                                    <select name="categoria_id" class="form-control" required>
                                        <option value="">Seleccionar categoría...</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= $categoria->id; ?>" 
                                                <?= set_select('categoria_id', $categoria->id); ?>>
                                                <?= $categoria->nombre; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Categoría de la galería (obligatorio)</small>
                                    <?php if (session()->getFlashdata('errors')['categoria_id'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['categoria_id']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Estado</label>
                                    <select name="estado" class="form-control">
                                        <option value="A" <?= set_select('estado', 'A'); ?>>Activo</option>
                                        <option value="I" <?= set_select('estado', 'I'); ?>>Inactivo</option>
                                    </select>
                                    <?php if (session()->getFlashdata('errors')['estado'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['estado']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Imagen de Portada *</label>
                                    <input type="file" name="portada" class="form-control" accept="image/*" required>
                                    <small class="text-muted">Imagen principal de la galería (JPG, PNG, GIF - máximo 5MB)</small>
                                    <?php if (session()->getFlashdata('errors')['portada'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['portada']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="descripcion" class="form-control" rows="3" 
                                              placeholder="Descripción de la galería..."><?= old('descripcion') ?></textarea>
                                    <small class="text-muted">Descripción opcional de la galería</small>
                                    <?php if (session()->getFlashdata('errors')['descripcion'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['descripcion']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Espaciado adicional antes de los botones -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Registrar Galería
                                    </button>
                                    <a href="<?= base_url('dashboard/galeria/lista') ?>" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->extend('layout/dashboard') ?>

<?= $this->section('servicio_categoria/registro') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tags"></i> Registrar Nueva Categoría de Servicio
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/servicio-categoria/lista') ?>" class="btn btn-secondary">
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

                    <form method="post" action="<?= base_url('dashboard/servicio-categoria/registrar') ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nombre de la Categoría *</label>
                                    <input type="text" name="nombre" class="form-control" 
                                           value="<?= old('nombre') ?>" required placeholder="Ej: Construcción Residencial, Remodelaciones...">
                                    <small class="text-muted">Nombre único para identificar la categoría</small>
                                    <?php if (session()->getFlashdata('errors')['nombre'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['nombre']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Icono</label>
                                    <input type="text" name="icono" class="form-control" 
                                           value="<?= old('icono') ?>" placeholder="Ej: fas fa-home, bi bi-house...">
                                    <small class="text-muted">Clase CSS del icono (opcional)</small>
                                    <?php if (session()->getFlashdata('errors')['icono'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['icono']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Color</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <input type="color" name="color" class="form-control form-control-color" 
                                               value="<?= old('color', '#000000') ?>" style="width: 80px; height: 40px; border: 2px solid #ddd; border-radius: 5px;">
                                    </div>
                                    <small class="text-muted">Color representativo de la categoría</small>
                                    <?php if (session()->getFlashdata('errors')['color'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['color']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Orden</label>
                                    <input type="number" name="orden" class="form-control" 
                                           value="<?= old('orden', '1') ?>" min="0" placeholder="Orden de aparición">
                                    <small class="text-muted">Número para ordenar las categorías</small>
                                    <?php if (session()->getFlashdata('errors')['orden'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['orden']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="descripcion" class="form-control" rows="3" 
                                              placeholder="Descripción breve de la categoría..."><?= old('descripcion') ?></textarea>
                                    <small class="text-muted">Descripción opcional de la categoría</small>
                                    <?php if (session()->getFlashdata('errors')['descripcion'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['descripcion']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Estado</label>
                                    <select name="estado" class="form-control">
                                        <option value="A" <?= old('estado', 'A') == 'A' ? 'selected' : '' ?>>Activo</option>
                                        <option value="I" <?= old('estado') == 'I' ? 'selected' : '' ?>>Inactivo</option>
                                    </select>
                                    <?php if (session()->getFlashdata('errors')['estado'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['estado']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Campos SEO ocultos - se generan automáticamente -->
                        <input type="hidden" name="meta_titulo" value="">
                        <input type="hidden" name="meta_descripcion" value="">
                        <input type="hidden" name="meta_keywords" value="">

                        <!-- Espaciado adicional antes de los botones -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Registrar Categoría
                                    </button>
                                    <a href="<?= base_url('dashboard/servicio-categoria/lista') ?>" class="btn btn-secondary">
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
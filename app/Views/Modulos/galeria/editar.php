<?php $this->extend('layout/dashboard') ?>

<?= $this->section("galeria/detalle") ?>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="col-md-12">
            <div class="mb-3">
                <h4>Editar Galería</h4>
            </div>
        </div>
        <?= form_open_multipart('dashboard/galeria/update') ?>
            <div class="row">
                <?= csrf_field(); ?>
                
                <!-- Información Básica -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la Galería *</label>
                        <input type="text" id="nombre" name="nombre" value="<?= $galeria->nombre; ?>" class="form-control" required>
                        <input type="hidden" id="id" name="id" value="<?= $galeria->id; ?>">
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['nombre'])) : ?>
                        <p style="color:red; font-weight:bold;"><?= session()->getFlashdata('errors')['nombre']; ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Categoría *</label>
                        <select class="form-select" id="categoria_id" name="categoria_id" required>
                            <option value="">Seleccionar categoría...</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria->id; ?>" 
                                    <?= ($galeria->categoria_id == $categoria->id) ? 'selected' : ''; ?>>
                                    <?= $categoria->nombre; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Categoría de la galería (obligatorio)</small>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['categoria_id'])) : ?>
                        <p style="color:red; font-weight:bold;"><?= session()->getFlashdata('errors')['categoria_id']; ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="A" <?= $galeria->estado == 'A' ? 'selected' : '' ?>>Activo</option>
                            <option value="I" <?= $galeria->estado == 'I' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Descripción de la Galería *</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="5" required><?= $galeria->descripcion; ?></textarea>
                        <small class="text-muted">Descripción detallada de la obra realizada</small>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['descripcion'])) : ?>
                        <p style="color:red; font-weight:bold;"><?= session()->getFlashdata('errors')['descripcion']; ?></p>
                    <?php endif; ?>
                </div>
                
                <!-- Imagen Actual -->
                <?php if (!empty($galeria->portada) && file_exists(ROOTPATH . $galeria->portada)): ?>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Imagen Actual</label>
                        <div style="text-align: center;">
                            <img src="<?= base_url($galeria->portada); ?>" style="max-width: 300px; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" />
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Nueva Imagen -->
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Nueva Imagen de la Galería</label>
                        <input type="file" id="portada" name="portada" class="form-control" accept="image/*">
                        <small class="text-muted">Dejar vacío para mantener la imagen actual</small>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['portada'])) : ?>
                        <p style="color:red; font-weight:bold;"><?= session()->getFlashdata('errors')['portada']; ?></p>
                    <?php endif; ?>
                </div>
                
                <!-- Mensajes de Error/Success -->
                <?php if (session()->getFlashdata('success') !== null) : ?>
                    <div class="alert alert-success my-3" role="alert">
                        <?= session()->getFlashdata('success'); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->getFlashdata('errors') !== null) : ?>
                    <p style="color:red; font-weight:bold;">
                        <?php if (!is_array(session()->getFlashdata('errors'))) : ?>
                            <?= session()->getFlashdata('errors'); ?>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
                
                <!-- Botones -->
                <div class="col-12">
                    <div class="mb-4">
                        <button type="submit" class="btn btn-primary w-100">Actualizar Galería</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
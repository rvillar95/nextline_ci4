<?php $this->extend('layout/dashboard') ?>

<?= $this->section("servicio/detalle") ?>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="col-md-12">
            <div class="mb-3">
                <h4>Editar Servicio</h4>
            </div>
        </div>
        
        <?= form_open_multipart('dashboard/servicio/update') ?>
            <div class="row">
                <?= csrf_field(); ?>
                
                <!-- Información Básica -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" id="nombre" name="nombre" value="<?= $servicio->nombre; ?>" class="form-control" required>
                        <input type="hidden" id="id" name="id" value="<?= $servicio->id; ?>">
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
                                    <?= ($servicio->categoria_id == $categoria->id) ? 'selected' : ''; ?>>
                                    <?= $categoria->nombre; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Categoría del servicio (obligatorio)</small>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['categoria_id'])) : ?>
                        <p style="color:red; font-weight:bold;"><?= session()->getFlashdata('errors')['categoria_id']; ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Descripción Corta *</label>
                        <input type="text" id="descripcionCorta" name="descripcionCorta" value="<?= $servicio->descripcionCorta; ?>" class="form-control" required>
                        <small class="text-muted">Descripción breve para mostrar en listados</small>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['descripcionCorta'])) : ?>
                        <p style="color:red; font-weight:bold;"><?= session()->getFlashdata('errors')['descripcionCorta']; ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Descripción Larga *</label>
                        <textarea id="descripcionLarga" name="descripcionLarga" class="form-control" rows="5" required><?= $servicio->descripcionLarga; ?></textarea>
                        <small class="text-muted">Descripción detallada del servicio</small>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['descripcionLarga'])) : ?>
                        <p style="color:red; font-weight:bold;"><?= session()->getFlashdata('errors')['descripcionLarga']; ?></p>
                    <?php endif; ?>
                </div>
                
                <!-- Características y Beneficios -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Características</label>
                        <textarea id="caracteristicas" name="caracteristicas" class="form-control" rows="3" placeholder="Características principales del servicio"><?= $servicio->caracteristicas ?? ''; ?></textarea>
                        <small class="text-muted">Lista las características principales</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Beneficios</label>
                        <textarea id="beneficios" name="beneficios" class="form-control" rows="3" placeholder="Beneficios que obtiene el cliente"><?= $servicio->beneficios ?? ''; ?></textarea>
                        <small class="text-muted">Beneficios para el cliente</small>
                    </div>
                </div>
                
                <!-- Información Adicional -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tiempo Estimado</label>
                        <input type="text" id="tiempo_estimado" name="tiempo_estimado" value="<?= $servicio->tiempo_estimado ?? ''; ?>" class="form-control" placeholder="Ej: 30 días, 2 semanas">
                        <small class="text-muted">Tiempo estimado de ejecución</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Garantía</label>
                        <input type="text" id="garantia" name="garantia" value="<?= $servicio->garantia ?? ''; ?>" class="form-control" placeholder="Ej: 1 año, 6 meses">
                        <small class="text-muted">Período de garantía</small>
                    </div>
                </div>
                
                <!-- Precios -->
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Precio Desde</label>
                        <input type="number" id="precio_desde" name="precio_desde" value="<?= $servicio->precio_desde ?? ''; ?>" class="form-control" step="0.01" min="0">
                        <small class="text-muted">Precio mínimo</small>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Precio Hasta</label>
                        <input type="number" id="precio_hasta" name="precio_hasta" value="<?= $servicio->precio_hasta ?? ''; ?>" class="form-control" step="0.01" min="0">
                        <small class="text-muted">Precio máximo</small>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Mostrar Precio</label>
                        <select class="form-select" id="mostrar_precio" name="mostrar_precio">
                            <option value="S" <?= ($servicio->mostrar_precio ?? 'S') == 'S' ? 'selected' : '' ?>>Sí</option>
                            <option value="N" <?= ($servicio->mostrar_precio ?? 'S') == 'N' ? 'selected' : '' ?>>No</option>
                        </select>
                        <small class="text-muted">¿Mostrar precio en la web?</small>
                    </div>
                </div>
                
                <!-- Configuración -->
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Orden</label>
                        <input type="number" id="orden" name="orden" value="<?= $servicio->orden ?? 0; ?>" class="form-control" min="0">
                        <small class="text-muted">Orden de aparición (0 = primero)</small>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Destacado</label>
                        <select class="form-select" id="destacado" name="destacado">
                            <option value="N" <?= ($servicio->destacado ?? 'N') == 'N' ? 'selected' : '' ?>>No</option>
                            <option value="S" <?= ($servicio->destacado ?? 'N') == 'S' ? 'selected' : '' ?>>Sí</option>
                        </select>
                        <small class="text-muted">¿Servicio destacado?</small>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="A" <?= $servicio->estado == 'A' ? 'selected' : '' ?>>Activo</option>
                            <option value="I" <?= $servicio->estado == 'I' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                </div>
                
                <!-- Imagen Actual -->
                <?php if (!empty($servicio->foto) && file_exists(ROOTPATH . $servicio->foto)): ?>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Imagen Actual</label>
                        <div style="text-align: center;">
                            <img src="<?= base_url($servicio->foto); ?>" style="max-width: 300px; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" />
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Nueva Imagen -->
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Nueva Imagen</label>
                        <input type="file" id="img" name="img" class="form-control" accept="image/*">
                        <small class="text-muted">Dejar vacío para mantener la imagen actual</small>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['img'])) : ?>
                        <p style="color:red; font-weight:bold;"><?= session()->getFlashdata('errors')['img']; ?></p>
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
                        <button type="submit" class="btn btn-primary w-100">Actualizar Servicio</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
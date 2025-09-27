<?php $this->extend('layout/dashboard') ?>

<?= $this->section("servicio_categoria/registro") ?>

<div id="basic" class="col-lg-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Registro de Categoría de Servicio</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="post" action="<?= base_url('dashboard/servicio-categoria/registrar') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nombre de la Categoría *</label>
                            <input type="text" id="nombre" name="nombre" value="<?= set_value('nombre'); ?>" class="form-control" required placeholder="Ej: Construcción Residencial, Remodelaciones...">
                            <small class="text-muted">Nombre único para identificar la categoría</small>
                        </div>
                        <?php if (isset(session()->getFlashdata('errors')['nombre'])) : ?>
                            <p style="color:red; font-weight:bold;">
                                <?= session()->getFlashdata('errors')['nombre']; ?>
                            <p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Icono</label>
                            <input type="text" id="icono" name="icono" value="<?= set_value('icono'); ?>" class="form-control" placeholder="Ej: fas fa-home, bi bi-house...">
                            <small class="text-muted">Clase CSS del icono (opcional)</small>
                        </div>
                        <?php if (isset(session()->getFlashdata('errors')['icono'])) : ?>
                            <p style="color:red; font-weight:bold;">
                                <?= session()->getFlashdata('errors')['icono']; ?>
                            <p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Color</label>
                            <div class="d-flex align-items-center gap-3">
                                <input type="color" id="color" name="color" value="<?= set_value('color', '#000000'); ?>" class="form-control form-control-color" style="width: 80px; height: 40px; border: 2px solid #ddd; border-radius: 5px;">
                            </div>
                            <small class="text-muted">Color representativo de la categoría</small>
                        </div>
                        <?php if (isset(session()->getFlashdata('errors')['color'])) : ?>
                            <p style="color:red; font-weight:bold;">
                                <?= session()->getFlashdata('errors')['color']; ?>
                            <p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Orden</label>
                            <input type="number" id="orden" name="orden" value="<?= set_value('orden', '1'); ?>" class="form-control" min="0" placeholder="Orden de aparición">
                            <small class="text-muted">Número para ordenar las categorías</small>
                        </div>
                        <?php if (isset(session()->getFlashdata('errors')['orden'])) : ?>
                            <p style="color:red; font-weight:bold;">
                                <?= session()->getFlashdata('errors')['orden']; ?>
                            <p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea id="descripcion" name="descripcion" class="form-control" rows="3" placeholder="Descripción breve de la categoría..."><?= set_value('descripcion'); ?></textarea>
                            <small class="text-muted">Descripción opcional de la categoría</small>
                        </div>
                        <?php if (isset(session()->getFlashdata('errors')['descripcion'])) : ?>
                            <p style="color:red; font-weight:bold;">
                                <?= session()->getFlashdata('errors')['descripcion']; ?>
                            <p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="A" <?= set_select('estado', 'A', true); ?>>Activo</option>
                                <option value="I" <?= set_select('estado', 'I'); ?>>Inactivo</option>
                            </select>
                        </div>
                        <?php if (isset(session()->getFlashdata('errors')['estado'])) : ?>
                            <p style="color:red; font-weight:bold;">
                                <?= session()->getFlashdata('errors')['estado']; ?>
                            <p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Campos SEO ocultos - se generan automáticamente -->
                <input type="hidden" name="meta_titulo" value="">
                <input type="hidden" name="meta_descripcion" value="">
                <input type="hidden" name="meta_keywords" value="">
                <div class="row">
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
                    <div class="col-md-12">
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Registrar Categoría</button>
                            <a href="<?= base_url('dashboard/servicio-categoria/lista') ?>" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

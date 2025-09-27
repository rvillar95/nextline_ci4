<?php $this->extend('layout/dashboard') ?>

<?= $this->section("proyecto/registro") ?>

<div id="basic" class="col-lg-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <div class="mb-3">
                        <h4>Registro de Proyecto</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">

            <div class="row">
                <div class="col-lg-12 col-12 ">

                    <div class="form-group">
                    <?= form_open_multipart('dashboard/proyecto/registrar') ?>
                        <?= csrf_field(); ?>
                        
                        <!-- Información Básica -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombre del Proyecto *</label>
                                    <input type="text" id="nombre" name="nombre" value="<?= set_value('nombre'); ?>" class="form-control" autofocus required>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['nombre'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['nombre']; ?>
                                    <p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Cliente</label>
                                    <input type="text" id="cliente" name="cliente" value="<?= set_value('cliente'); ?>" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tipo de Proyecto *</label>
                                    <select class="form-select" id="tipo_proyecto" name="tipo_proyecto" required>
                                        <option value="">Seleccionar tipo...</option>
                                        <?php foreach ($tipos_proyecto as $key => $value): ?>
                                            <option value="<?= $key; ?>" <?= set_select('tipo_proyecto', $key); ?>>
                                                <?= $value; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['tipo_proyecto'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['tipo_proyecto']; ?>
                                    <p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Estado *</label>
                                    <select class="form-select" id="estado" name="estado" required>
                                        <option value="">Seleccionar estado...</option>
                                        <?php foreach ($estados_proyecto as $key => $value): ?>
                                            <option value="<?= $key; ?>" <?= set_select('estado', $key); ?>>
                                                <?= $value; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['estado'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['estado']; ?>
                                    <p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Ubicación -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Ubicación</label>
                                    <input type="text" id="ubicacion" name="ubicacion" value="<?= set_value('ubicacion'); ?>" class="form-control" placeholder="Ciudad, Región">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" id="direccion" name="direccion" value="<?= set_value('direccion'); ?>" class="form-control" placeholder="Dirección específica">
                                </div>
                            </div>
                        </div>

                        <!-- Fechas -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Fecha de Inicio</label>
                                    <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= set_value('fecha_inicio'); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Fecha de Finalización</label>
                                    <input type="date" id="fecha_finalizacion" name="fecha_finalizacion" value="<?= set_value('fecha_finalizacion'); ?>" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Presupuesto -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Presupuesto</label>
                                    <input type="number" id="presupuesto" name="presupuesto" value="<?= set_value('presupuesto'); ?>" class="form-control" step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="mostrar_presupuesto" name="mostrar_presupuesto" value="1" <?= set_checkbox('mostrar_presupuesto', '1'); ?>>
                                        <label class="form-check-label" for="mostrar_presupuesto">
                                            Mostrar presupuesto públicamente
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Descripciones -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Descripción Corta</label>
                                    <textarea id="descripcion_corta" name="descripcion_corta" class="form-control" rows="3" placeholder="Descripción breve del proyecto"><?= set_value('descripcion_corta'); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Descripción Detallada</label>
                                    <textarea id="descripcion_detallada" name="descripcion_detallada" class="form-control" rows="5" placeholder="Descripción completa del proyecto, proceso, características especiales..."><?= set_value('descripcion_detallada'); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Características Técnicas -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Área Construida (m²)</label>
                                    <input type="number" id="area_construida" name="area_construida" value="<?= set_value('area_construida'); ?>" class="form-control" step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Materiales Principales</label>
                                    <input type="text" id="materiales_principales" name="materiales_principales" value="<?= set_value('materiales_principales'); ?>" class="form-control" placeholder="Hormigón, Acero, Madera...">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Características Técnicas</label>
                                    <textarea id="caracteristicas_tecnicas" name="caracteristicas_tecnicas" class="form-control" rows="4" placeholder="Detalles técnicos, especificaciones, sistemas utilizados..."><?= set_value('caracteristicas_tecnicas'); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonio del Cliente -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombre del Cliente</label>
                                    <input type="text" id="nombre_cliente" name="nombre_cliente" value="<?= set_value('nombre_cliente'); ?>" class="form-control" placeholder="Nombre para el testimonio">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="destacado" name="destacado" value="1" <?= set_checkbox('destacado', '1'); ?>>
                                        <label class="form-check-label" for="destacado">
                                            Proyecto Destacado
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Testimonio del Cliente</label>
                                    <textarea id="testimonio_cliente" name="testimonio_cliente" class="form-control" rows="3" placeholder="Testimonio o comentario del cliente sobre el proyecto"><?= set_value('testimonio_cliente'); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Imágenes del Proyecto -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Imágenes del Proyecto</label>
                                    <input type="file" id="imagenes" name="imagenes[]" class="form-control" multiple accept="image/*">
                                    <small class="text-muted">Puedes seleccionar múltiples imágenes. La primera será la portada por defecto.</small>
                                </div>
                            </div>
                        </div>

                        <!-- SEO -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="mt-4 mb-3">Configuración SEO</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Meta Título</label>
                                    <input type="text" id="meta_titulo" name="meta_titulo" value="<?= set_value('meta_titulo'); ?>" class="form-control" placeholder="Título para motores de búsqueda">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Meta Descripción</label>
                                    <textarea id="meta_descripcion" name="meta_descripcion" class="form-control" rows="2" placeholder="Descripción para motores de búsqueda"><?= set_value('meta_descripcion'); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Meta Keywords</label>
                                    <input type="text" id="meta_keywords" name="meta_keywords" value="<?= set_value('meta_keywords'); ?>" class="form-control" placeholder="Palabras clave separadas por comas">
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">Registrar Proyecto</button>
                                    <a href="<?= base_url('dashboard/proyecto/lista') ?>" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </div>
                        </div>

                    <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

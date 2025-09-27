<?php $this->extend('layout/dashboard') ?>

<?= $this->section("servicio/registro") ?>

<div id="basic" class="col-lg-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <div class="mb-3">
                        <h4>Registro de Servicio</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">

            <div class="row">
                <div class="col-lg-12 col-12 ">

                    <div class="form-group">
                    <?= form_open_multipart('dashboard/servicio/registrar') ?>
                        <!--form method="POST" action="<?= base_url('dashboard/servicio/registrar'); ?>"-->
                            <?= csrf_field(); ?>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombre del Servicio *</label>
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
                                    <label class="form-label">Categoría *</label>
                                    <select class="form-select" id="categoria_id" name="categoria_id" required>
                                        <option value="">Seleccionar categoría...</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= $categoria->id; ?>" 
                                                <?= set_select('categoria_id', $categoria->id); ?>>
                                                <?= $categoria->nombre; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Categoría del servicio (obligatorio)</small>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['categoria_id'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['categoria_id']; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Descripción Corta *</label>
                                    <input type="text" id="descripcionCorta" name="descripcionCorta" value="<?= set_value('descripcionCorta'); ?>" class="form-control" required>
                                    <small class="form-text text-muted">Descripción breve que aparecerá en las tarjetas de servicios (máximo 500 caracteres)</small>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['descripcionCorta'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['descripcionCorta']; ?>
                                    <p>
                                    <?php endif; ?>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Descripción Larga *</label>
                                    <textarea id="descripcionLarga" name="descripcionLarga" class="form-control" rows="5" required><?= set_value('descripcionLarga'); ?></textarea>
                                    <small class="form-text text-muted">Descripción detallada del servicio (máximo 2000 caracteres)</small>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['descripcionLarga'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['descripcionLarga']; ?>
                                    <p>
                                    <?php endif; ?>
                            </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Características</label>
                                    <textarea id="caracteristicas" name="caracteristicas" class="form-control" rows="3" placeholder="Una característica por línea"><?= set_value('caracteristicas'); ?></textarea>
                                    <small class="form-text text-muted">Lista las principales características del servicio (una por línea)</small>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['caracteristicas'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['caracteristicas']; ?>
                                    <p>
                                    <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Beneficios</label>
                                    <textarea id="beneficios" name="beneficios" class="form-control" rows="3" placeholder="Un beneficio por línea"><?= set_value('beneficios'); ?></textarea>
                                    <small class="form-text text-muted">Lista los beneficios que ofrece este servicio (uno por línea)</small>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['beneficios'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['beneficios']; ?>
                                    <p>
                                    <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Tiempo Estimado</label>
                                    <input type="text" id="tiempo_estimado" name="tiempo_estimado" value="<?= set_value('tiempo_estimado'); ?>" class="form-control" placeholder="Ej: 30 días, 2-3 meses">
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['tiempo_estimado'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['tiempo_estimado']; ?>
                                    <p>
                                    <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Garantía</label>
                                    <input type="text" id="garantia" name="garantia" value="<?= set_value('garantia'); ?>" class="form-control" placeholder="Ej: 1 año, 6 meses">
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['garantia'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['garantia']; ?>
                                    <p>
                                    <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Orden de Visualización</label>
                                    <input type="number" id="orden" name="orden" value="<?= set_value('orden', '0'); ?>" class="form-control" min="0">
                                    <small class="form-text text-muted">Número para ordenar los servicios (menor número = primero)</small>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['orden'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['orden']; ?>
                                    <p>
                                    <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Mostrar Precio</label>
                                    <select class="form-select" id="mostrar_precio" name="mostrar_precio">
                                        <option value="S" <?= set_select('mostrar_precio', 'S', true); ?>>Sí</option>
                                        <option value="N" <?= set_select('mostrar_precio', 'N'); ?>>No</option>
                                    </select>
                                    <small class="form-text text-muted">Si seleccionas "No", aparecerá "Consultar precio"</small>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['mostrar_precio'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['mostrar_precio']; ?>
                                    <p>
                                    <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Precio Desde</label>
                                    <input type="number" id="precio_desde" name="precio_desde" value="<?= set_value('precio_desde'); ?>" class="form-control" step="0.01" placeholder="0.00">
                                    <small class="form-text text-muted">Precio mínimo del servicio</small>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['precio_desde'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['precio_desde']; ?>
                                    <p>
                                    <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Precio Hasta</label>
                                    <input type="number" id="precio_hasta" name="precio_hasta" value="<?= set_value('precio_hasta'); ?>" class="form-control" step="0.01" placeholder="0.00">
                                    <small class="form-text text-muted">Precio máximo del servicio</small>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['precio_hasta'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['precio_hasta']; ?>
                                    <p>
                                    <?php endif; ?>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Imagen del Servicio</label>
                                    <input type="file" id="img" name="img" class="form-control" accept="image/*">
                                    <small class="form-text text-muted">Imagen representativa del servicio (JPG, PNG, GIF - máximo 2MB)</small>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['img'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['img']; ?>
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
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Servicio Destacado</label>
                                    <select class="form-select" id="destacado" name="destacado">
                                        <option value="N" <?= set_select('destacado', 'N', true); ?>>No</option>
                                        <option value="S" <?= set_select('destacado', 'S'); ?>>Sí</option>
                                    </select>
                                    <small class="form-text text-muted">Los servicios destacados aparecen en la página principal</small>
                                </div>
                            </div>
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
                            <div class="col-12">
                                <div class="mb-4">
                                    <button type="submit" class="btn btn-secondary w-100">Crear</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
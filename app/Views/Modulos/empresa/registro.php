<?= $this->extend('layout/dashboard') ?>

<?= $this->section('empresa/registro') ?>

<?php
$esVistaConsultorio = ! empty($es_vista_consultorio);
$tituloCard = $esVistaConsultorio
    ? 'Mi consultorio'
    : ($empresa ? 'Editar Datos de la Empresa' : 'Configurar Datos de la Empresa');
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-<?= $esVistaConsultorio ? 'map-marker-alt' : 'building' ?>"></i> <?= esc($tituloCard) ?>
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url($esVistaConsultorio ? 'dashboard/menu' : (isset($es_super_admin) && $es_super_admin ? 'dashboard/empresa/lista' : 'dashboard/menu')) ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors') !== null) : ?>
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> Errores de Validación</h5>
                            <?php if (is_array(session()->getFlashdata('errors'))) : ?>
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $field => $error) : ?>
                                        <li><strong><?= ucfirst(str_replace('_', ' ', $field)) ?>:</strong> <?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else : ?>
                                <p class="mb-0"><?= session()->getFlashdata('errors') ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($esVistaConsultorio && empty($empresa)): ?>
                        <div class="alert alert-warning mb-0">
                            Su usuario no tiene una empresa asociada. Contacte al administrador del sistema.
                        </div>
                    <?php else: ?>

                    <?php if ($esVistaConsultorio): ?>
                        <p class="text-muted mb-4">
                            Actualice la ubicación y datos de contacto de su consultorio. Estos datos pueden usarse en confirmaciones y comunicaciones con pacientes.
                        </p>
                    <?php endif; ?>

                    <form action="<?= base_url($empresa ? 'dashboard/empresa/update' : 'dashboard/empresa/registrar') ?>" method="post">
                        <?= csrf_field() ?>

                        <?php if ($empresa): ?>
                            <input type="hidden" name="id" value="<?= (int) $empresa->id ?>">
                        <?php endif; ?>

                        <?php if ($esVistaConsultorio): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Razón social</label>
                                        <input type="text" class="form-control" value="<?= esc($empresa->nombre ?? '') ?>" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="nombre_comercial" class="form-label">Nombre comercial</label>
                                        <input type="text" class="form-control" id="nombre_comercial" name="nombre_comercial"
                                               value="<?= old('nombre_comercial', $empresa->nombre_comercial ?? '') ?>" maxlength="255"
                                               placeholder="Ej.: NutriNext Linares">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="telefono" name="telefono" required
                                               value="<?= old('telefono', $empresa->telefono ?? '') ?>" maxlength="50">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label">Correo de contacto <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" required
                                               value="<?= old('email', $empresa->email ?? '') ?>" maxlength="150">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="direccion" class="form-label">Dirección <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="direccion" name="direccion" rows="2" required minlength="10" maxlength="500"><?= old('direccion', $empresa->direccion ?? '') ?></textarea>
                                <small class="form-text text-muted">Dirección del consultorio para pacientes y recordatorios.</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="url_google_maps" class="form-label">URL de Google Maps</label>
                                <input type="url" class="form-control" id="url_google_maps" name="url_google_maps"
                                       value="<?= old('url_google_maps', $empresa->url_google_maps ?? '') ?>"
                                       placeholder="https://maps.google.com/... o https://goo.gl/maps/...">
                                <small class="form-text text-muted">Enlace para que los pacientes abran la ubicación en Google Maps.</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="sitio_web" class="form-label">Sitio web</label>
                                <input type="url" class="form-control" id="sitio_web" name="sitio_web"
                                       value="<?= old('sitio_web', $empresa->sitio_web ?? '') ?>" placeholder="https://www.ejemplo.com">
                            </div>

                            <div class="form-group mb-3">
                                <label for="descripcion" class="form-label">Descripción breve</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="2000"><?= old('descripcion', $empresa->descripcion ?? '') ?></textarea>
                            </div>

                        <?php else: ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre" class="form-label">Nombre de la Empresa <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombre" name="nombre"
                                           value="<?= old('nombre', $empresa->nombre ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre_comercial" class="form-label">Nombre Comercial</label>
                                    <input type="text" class="form-control" id="nombre_comercial" name="nombre_comercial"
                                           value="<?= old('nombre_comercial', $empresa->nombre_comercial ?? '') ?>" placeholder="Nombre comercial o marca">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rut" class="form-label">RUT</label>
                                    <input type="text" class="form-control" id="rut" name="rut"
                                           value="<?= old('rut', $empresa->rut ?? '') ?>" placeholder="12.345.678-9">
                                </div>
                            </div>
                            <?php if (isset($es_super_admin) && $es_super_admin): ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="paquete_id" class="form-label">Paquete <span class="text-danger">*</span></label>
                                    <select class="form-control" id="paquete_id" name="paquete_id" required>
                                        <option value="">Seleccione un paquete</option>
                                        <?php foreach ($paquetes as $paquete): ?>
                                            <option value="<?= $paquete->id ?>"
                                                <?= old('paquete_id', $empresa->paquete_id ?? '') == $paquete->id ? 'selected' : '' ?>>
                                                <?= esc($paquete->nombre) ?>
                                                <?php if ($paquete->precio_mensual > 0): ?>
                                                    - $<?= number_format($paquete->precio_mensual, 0, ',', '.') ?>/mes
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">El paquete define qué módulos tiene disponibles esta empresa</small>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="<?= old('email', $empresa->email ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono"
                                           value="<?= old('telefono', $empresa->telefono ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sitio_web" class="form-label">Sitio Web</label>
                                    <input type="url" class="form-control" id="sitio_web" name="sitio_web"
                                           value="<?= old('sitio_web', $empresa->sitio_web ?? '') ?>" placeholder="https://www.ejemplo.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="logo_path" class="form-label">Ruta del Logo</label>
                                    <input type="text" class="form-control" id="logo_path" name="logo_path"
                                           value="<?= old('logo_path', $empresa->logo_path ?? '') ?>" placeholder="/ruta/al/logo.jpg">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="direccion" class="form-label">Dirección</label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="2"><?= old('direccion', $empresa->direccion ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="url_google_maps" class="form-label">URL de Google Maps</label>
                            <input type="url" class="form-control" id="url_google_maps" name="url_google_maps"
                                   value="<?= old('url_google_maps', $empresa->url_google_maps ?? '') ?>"
                                   placeholder="https://maps.google.com/... o https://goo.gl/maps/...">
                            <small class="form-text text-muted">Enlace del consultorio en Google Maps (para compartir ubicación).</small>
                        </div>

                        <div class="form-group">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= old('descripcion', $empresa->descripcion ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="mision" class="form-label">Misión</label>
                            <textarea class="form-control" id="mision" name="mision" rows="3"><?= old('mision', $empresa->mision ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="vision" class="form-label">Visión</label>
                            <textarea class="form-control" id="vision" name="vision" rows="3"><?= old('vision', $empresa->vision ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="valores" class="form-label">Valores</label>
                            <textarea class="form-control" id="valores" name="valores" rows="3"><?= old('valores', $empresa->valores ?? '') ?></textarea>
                        </div>

                        <?php endif; ?>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> <?= $empresa ? 'Guardar cambios' : 'Registrar Empresa' ?>
                                </button>
                                <a href="<?= base_url($esVistaConsultorio ? 'dashboard/menu' : (isset($es_super_admin) && $es_super_admin ? 'dashboard/empresa/lista' : 'dashboard/menu')) ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

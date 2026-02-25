<?= $this->extend('layout/dashboard') ?>

<?= $this->section('empresa/registro') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-building"></i> <?= $empresa ? 'Editar Datos de la Empresa' : 'Configurar Datos de la Empresa' ?>
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/menu') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Dashboard
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

                    <form action="<?= base_url($empresa ? 'dashboard/empresa/update' : 'dashboard/empresa/registrar') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <?php if ($empresa): ?>
                            <input type="hidden" name="id" value="<?= $empresa->id ?>">
                        <?php endif; ?>
                        
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

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> <?= $empresa ? 'Actualizar' : 'Registrar' ?> Empresa
                                </button>
                                <a href="<?= base_url(isset($es_super_admin) && $es_super_admin ? 'dashboard/empresa/lista' : 'dashboard/menu') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

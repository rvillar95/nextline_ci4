<?= $this->extend('layout/dashboard') ?>

<?= $this->section('paquete/registro') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cube"></i> <?= $paquete ? 'Editar Paquete' : 'Crear Nuevo Paquete' ?>
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/paquete/lista') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver a Lista
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> Errores de Validación</h5>
                            <?php if (is_array(session()->getFlashdata('errors'))): ?>
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $field => $error): ?>
                                        <li><strong><?= ucfirst(str_replace('_', ' ', $field)) ?>:</strong> <?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
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

                    <form action="<?= base_url($paquete ? 'dashboard/paquete/update' : 'dashboard/paquete/registrar') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <?php if ($paquete): ?>
                            <input type="hidden" name="id" value="<?= $paquete->id ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nombre" class="form-label">Nombre del Paquete <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           value="<?= old('nombre', $paquete->nombre ?? '') ?>" required
                                           placeholder="Ej: NextLine Nutrición Premium">
                                    <small class="form-text text-muted">Nombre visible del paquete</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="slug" name="slug" 
                                           value="<?= old('slug', $paquete->slug ?? '') ?>" required
                                           placeholder="Ej: nutricion-premium">
                                    <small class="form-text text-muted">Identificador único (solo letras, números y guiones)</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                                      placeholder="Descripción del paquete y sus características"><?= old('descripcion', $paquete->descripcion ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="precio_setup" class="form-label">Precio Setup</label>
                                    <input type="number" class="form-control" id="precio_setup" name="precio_setup" 
                                           value="<?= old('precio_setup', $paquete->precio_setup ?? 0) ?>" 
                                           min="0" step="0.01" placeholder="0.00">
                                    <small class="form-text text-muted">Precio de implementación inicial</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="precio_mensual" class="form-label">Precio Mensual</label>
                                    <input type="number" class="form-control" id="precio_mensual" name="precio_mensual" 
                                           value="<?= old('precio_mensual', $paquete->precio_mensual ?? 0) ?>" 
                                           min="0" step="0.01" placeholder="0.00">
                                    <small class="form-text text-muted">Precio mensual del plan</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label for="orden" class="form-label">Orden</label>
                                    <input type="number" class="form-control" id="orden" name="orden" 
                                           value="<?= old('orden', $paquete->orden ?? 0) ?>" 
                                           min="0" placeholder="0">
                                    <small class="form-text text-muted">Orden de visualización</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label for="activo" class="form-label">Estado</label>
                                    <select class="form-control" id="activo" name="activo" required>
                                        <option value="A" <?= old('activo', $paquete->activo ?? 'A') == 'A' ? 'selected' : '' ?>>Activo</option>
                                        <option value="I" <?= old('activo', $paquete->activo ?? 'A') == 'I' ? 'selected' : '' ?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> <?= $paquete ? 'Actualizar' : 'Crear' ?> Paquete
                                </button>
                                <a href="<?= base_url('dashboard/paquete/lista') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <?php if ($paquete): ?>
                                <a href="<?= base_url('dashboard/paquete/gestionar-metodos/' . $paquete->id) ?>" class="btn btn-secondary">
                                    <i class="fas fa-calculator"></i> Métodos cálculo
                                </a>
                                <a href="<?= base_url('dashboard/paquete/gestionar-modulos/' . $paquete->id) ?>" class="btn btn-warning">
                                    <i class="fas fa-cogs"></i> Gestionar Módulos
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-generar slug desde nombre
$('#nombre').on('blur', function() {
    if (!$('#slug').val() || $('#slug').val() == '<?= old('slug', $paquete->slug ?? '') ?>') {
        var slug = $(this).val()
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // Eliminar acentos
            .replace(/[^a-z0-9]+/g, '-') // Reemplazar espacios y caracteres especiales con guiones
            .replace(/^-+|-+$/g, ''); // Eliminar guiones al inicio y final
        $('#slug').val(slug);
    }
});
</script>

<?= $this->endSection() ?>

<?php $this->extend('layout/dashboard') ?>

<?= $this->section("perfil_detalle/registro") ?>

<style>
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }
    
    .section-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    
    .form-label {
        font-weight: 600;
        color: #34495e;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .icon-label {
        color: #667eea;
        font-size: 1rem;
    }
    
    .form-control, .form-select {
        border: 2px solid #e1e8ed;
        border-radius: 8px;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }
    
    .required {
        color: #e74c3c;
        font-weight: bold;
    }
    
    .permission-group {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        border-left: 3px solid #667eea;
    }
    
    .permission-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 10px;
        display: block;
    }
    
    .form-check-input {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    
    .form-check-label {
        margin-left: 8px;
        cursor: pointer;
        user-select: none;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 12px 35px;
        font-size: 1.05rem;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        transition: all 0.3s ease;
        color: white;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        background: linear-gradient(135deg, #764ba2 0%, #5a3d7a 100%);
        color: white;
    }
    
    .btn-cancel {
        padding: 12px 35px;
        font-size: 1.05rem;
        font-weight: 600;
        border-radius: 8px;
        border: 2px solid #6c757d;
        color: #495057 !important;
        background: white;
        transition: all 0.3s ease;
    }
    
    .btn-cancel:hover {
        background: #6c757d;
        border-color: #6c757d;
        color: white !important;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- HEADER -->
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white; margin: 0;">
                            <i class="fas fa-user-cog me-2"></i> Nuevo Permiso de Perfil
                        </h2>
                        <p style="color: white; margin: 10px 0 0 0; opacity: 0.9;">
                            Asigne permisos de un perfil a un módulo específico
                        </p>
                    </div>
                    <a href="<?= base_url('dashboard/perfil-detalle/lista') ?>" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i> Volver
                    </a>
                </div>
            </div>

            <!-- MENSAJES -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> <?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <h5><i class="fas fa-exclamation-triangle"></i> Errores de Validación</h5>
                    <ul class="mb-0">
                        <?php if (is_array(session()->getFlashdata('errors'))): ?>
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><?= esc(session()->getFlashdata('errors')) ?></li>
                        <?php endif; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- FORMULARIO -->
            <div class="section-card">
                <form method="POST" action="<?= base_url('dashboard/perfil-detalle/registrar'); ?>">
                    <?= csrf_field(); ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user-shield icon-label"></i>
                                    Perfil
                                    <span class="required">*</span>
                                </label>
                                <select class="form-select" id="perfil" name="perfil" required>
                                    <option value="">-- Seleccione un perfil --</option>
                                    <?php foreach ($perfiles as $perfil) : ?>
                                        <option value="<?= esc($perfil['id']) ?>" <?= old('perfil') == $perfil['id'] ? 'selected' : '' ?>>
                                            <?= esc($perfil['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset(session()->getFlashdata('errors')['perfil'])) : ?>
                                    <small class="text-danger">
                                        <?= session()->getFlashdata('errors')['perfil']; ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">
                                    <i class="fas fa-cube icon-label"></i>
                                    Módulo
                                    <span class="required">*</span>
                                </label>
                                <select class="form-select" id="modulo" name="modulo" required>
                                    <option value="">-- Seleccione un módulo --</option>
                                    <?php foreach ($modulos as $modulo) : ?>
                                        <option value="<?= esc($modulo['id']) ?>" <?= old('modulo') == $modulo['id'] ? 'selected' : '' ?>>
                                            <?= esc($modulo['nombre']) ?> (<?= esc($modulo['descripcion']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset(session()->getFlashdata('errors')['modulo'])) : ?>
                                    <small class="text-danger">
                                        <?= session()->getFlashdata('errors')['modulo']; ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Permisos -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="permission-group">
                                <label class="permission-label">
                                    <i class="fas fa-eye me-2" style="color: #667eea;"></i> Ver
                                </label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ver" value="1" id="ver_si" <?= old('ver', '1') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="ver_si">
                                            Sí
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ver" value="0" id="ver_no" <?= old('ver') == '0' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="ver_no">
                                            No
                                        </label>
                                    </div>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['ver'])) : ?>
                                    <small class="text-danger">
                                        <?= session()->getFlashdata('errors')['ver']; ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="permission-group">
                                <label class="permission-label">
                                    <i class="fas fa-plus me-2" style="color: #667eea;"></i> Registrar
                                </label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="registrar" value="1" id="registrar_si" <?= old('registrar', '1') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="registrar_si">
                                            Sí
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="registrar" value="0" id="registrar_no" <?= old('registrar') == '0' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="registrar_no">
                                            No
                                        </label>
                                    </div>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['registrar'])) : ?>
                                    <small class="text-danger">
                                        <?= session()->getFlashdata('errors')['registrar']; ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="permission-group">
                                <label class="permission-label">
                                    <i class="fas fa-edit me-2" style="color: #667eea;"></i> Editar
                                </label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="editar" value="1" id="editar_si" <?= old('editar', '1') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="editar_si">
                                            Sí
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="editar" value="0" id="editar_no" <?= old('editar') == '0' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="editar_no">
                                            No
                                        </label>
                                    </div>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['editar'])) : ?>
                                    <small class="text-danger">
                                        <?= session()->getFlashdata('errors')['editar']; ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="permission-group">
                                <label class="permission-label">
                                    <i class="fas fa-trash me-2" style="color: #667eea;"></i> Eliminar
                                </label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="eliminar" value="1" id="eliminar_si" <?= old('eliminar', '1') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="eliminar_si">
                                            Sí
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="eliminar" value="0" id="eliminar_no" <?= old('eliminar') == '0' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="eliminar_no">
                                            No
                                        </label>
                                    </div>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['eliminar'])) : ?>
                                    <small class="text-danger">
                                        <?= session()->getFlashdata('errors')['eliminar']; ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">
                                    <i class="fas fa-sort-numeric-down icon-label"></i>
                                    Orden
                                </label>
                                <input type="number" min="0" id="orden" name="orden" 
                                       value="<?= old('orden', 0) ?>" 
                                       class="form-control">
                                <?php if (isset(session()->getFlashdata('errors')['orden'])) : ?>
                                    <small class="text-danger">
                                        <?= session()->getFlashdata('errors')['orden']; ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex gap-3 justify-content-end">
                                <button type="submit" class="btn btn-submit">
                                    <i class="fas fa-save me-2"></i> Crear Permiso
                                </button>
                                <a href="<?= base_url('dashboard/perfil-detalle/lista') ?>" class="btn btn-cancel">
                                    <i class="fas fa-times me-2"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

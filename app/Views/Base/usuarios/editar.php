<?php $this->extend('layout/dashboard') ?>

<?= $this->section("usuario/detalle") ?>

<style>
    .main-header {
        background: linear-gradient(135deg, #f0841a 0%, #e67e00 100%);
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #f0841a;
        transition: all 0.3s ease;
    }
    
    .section-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    
    .section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        font-size: 1.2rem;
        font-weight: 600;
        color: #2c3e50;
        padding-bottom: 10px;
        border-bottom: 2px solid #e1e8ed;
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
        color: #f0841a;
        font-size: 1rem;
    }
    
    .form-control, .form-select {
        border: 2px solid #e1e8ed;
        border-radius: 8px;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #f0841a;
        box-shadow: 0 0 0 0.2rem rgba(240, 132, 26, 0.15);
    }
    
    .required {
        color: #e74c3c;
        font-weight: bold;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #f0841a 0%, #e67e00 100%);
        border: none;
        padding: 12px 35px;
        font-size: 1.05rem;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(240, 132, 26, 0.3);
        transition: all 0.3s ease;
        color: white;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(240, 132, 26, 0.4);
        background: linear-gradient(135deg, #e67e00 0%, #cc6f00 100%);
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
                            <i class="fas fa-user-edit me-2"></i> Editar Usuario
                        </h2>
                        <p style="color: white; margin: 10px 0 0 0; opacity: 0.9;">
                            Modifique la información del usuario
                        </p>
                    </div>
                    <a href="<?= base_url('dashboard/usuario/lista') ?>" class="btn btn-light">
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

            <!-- FORMULARIO: Información del Usuario -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-user-circle" style="color: #f0841a;"></i>
                    <span>Información del Usuario</span>
                </div>
                <form method="POST" action="<?= base_url('dashboard/usuario/update'); ?>">
                    <?= csrf_field(); ?>
                    <input type="hidden" id="id" name="id" value="<?= esc($usuario['id']) ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user icon-label"></i>
                                    Nombre
                                    <span class="required">*</span>
                                </label>
                                <input type="text" id="nombre" name="nombre" 
                                       value="<?= esc($usuario['nombre']) ?>" 
                                       class="form-control" 
                                       required>
                                <?php if (isset(session()->getFlashdata('errors')['nombre'])) : ?>
                                    <small class="text-danger">
                                        <?= session()->getFlashdata('errors')['nombre']; ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user icon-label"></i>
                                    Apellido
                                    <span class="required">*</span>
                                </label>
                                <input type="text" id="apellido" name="apellido" 
                                       value="<?= esc($usuario['apellido']) ?>" 
                                       class="form-control" 
                                       required>
                                <?php if (isset(session()->getFlashdata('errors')['apellido'])) : ?>
                                    <small class="text-danger">
                                        <?= session()->getFlashdata('errors')['apellido']; ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">
                                    <i class="fas fa-envelope icon-label"></i>
                                    Correo
                                    <span class="required">*</span>
                                </label>
                                <input type="email" id="correo" name="correo" 
                                       value="<?= esc($usuario['correo']) ?>" 
                                       class="form-control" 
                                       required>
                                <?php if (isset(session()->getFlashdata('errors')['correo'])) : ?>
                                    <small class="text-danger">
                                        <?= session()->getFlashdata('errors')['correo']; ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">
                                    <i class="fas fa-phone icon-label"></i>
                                    Teléfono
                                </label>
                                <input type="text" id="telefono" name="telefono" 
                                       value="<?= esc($usuario['telefono'] ?? '') ?>" 
                                       class="form-control">
                                <?php if (isset(session()->getFlashdata('errors')['telefono'])) : ?>
                                    <small class="text-danger">
                                        <?= session()->getFlashdata('errors')['telefono']; ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user-shield icon-label"></i>
                                    Perfil
                                    <span class="required">*</span>
                                </label>
                                <select class="form-select" id="perfil" name="perfil" required>
                                    <?php foreach ($perfiles as $perfil) : ?>
                                        <option value="<?= esc($perfil['id']) ?>" <?= $perfil['id'] == $usuario['perfil_id'] ? 'selected' : '' ?>>
                                            <?= esc($perfil['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">
                                    <i class="fas fa-building icon-label"></i>
                                    Empresa
                                </label>
                                <select class="form-select" id="empresa" name="empresa">
                                    <option value="">-- Sin empresa --</option>
                                    <?php if (!empty($empresas)) : ?>
                                        <?php foreach ($empresas as $emp) : ?>
                                            <option value="<?= esc($emp->id) ?>" <?= (isset($usuario['empresa_id']) && $emp->id == $usuario['empresa_id']) ? 'selected' : '' ?>>
                                                <?= esc($emp->nombre) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">
                                    <i class="fas fa-toggle-on icon-label"></i>
                                    Estado
                                    <span class="required">*</span>
                                </label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="A" <?= ($usuario['estado'] ?? 'A') == 'A' ? 'selected' : '' ?>>Activo</option>
                                    <option value="I" <?= ($usuario['estado'] ?? '') == 'I' ? 'selected' : '' ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex gap-3 justify-content-end">
                                <button type="submit" class="btn btn-submit">
                                    <i class="fas fa-save me-2"></i> Guardar Cambios
                                </button>
                                <a href="<?= base_url('dashboard/usuario/lista') ?>" class="btn btn-cancel">
                                    <i class="fas fa-times me-2"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- FORMULARIO: Cambiar Contraseña -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-key" style="color: #f0841a;"></i>
                    <span>Cambiar Contraseña</span>
                </div>
                <form method="POST" action="<?= base_url('dashboard/usuario/update/clave'); ?>">
                    <?= csrf_field(); ?>
                    <input type="hidden" id="id" name="id" value="<?= esc($usuario['id']) ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">
                                    <i class="fas fa-lock icon-label"></i>
                                    Nueva Clave
                                    <span class="required">*</span>
                                </label>
                                <input type="password" id="clave" name="clave" 
                                       class="form-control" 
                                       placeholder="********"
                                       required>
                                <?php if (isset(session()->getFlashdata('errorsPassword')['clave'])) : ?>
                                    <small class="text-danger">
                                        <?= session()->getFlashdata('errorsPassword')['clave']; ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">
                                    <i class="fas fa-lock icon-label"></i>
                                    Repita Clave
                                    <span class="required">*</span>
                                </label>
                                <input type="password" id="reclave" name="reclave" 
                                       class="form-control" 
                                       placeholder="********"
                                       required>
                                <?php if (isset(session()->getFlashdata('errorsPassword')['reclave'])) : ?>
                                    <small class="text-danger">
                                        <?= session()->getFlashdata('errorsPassword')['reclave']; ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if (session()->getFlashdata('successPassword')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle"></i> <?= esc(session()->getFlashdata('successPassword')) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Botones -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex gap-3 justify-content-end">
                                <button type="submit" class="btn btn-submit">
                                    <i class="fas fa-key me-2"></i> Cambiar Contraseña
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

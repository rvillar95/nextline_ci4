<?php $this->extend('layout/dashboard') ?>

<?= $this->section("perfil/registro") ?>

<style>
    .main-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
                            <i class="fas fa-user-shield me-2"></i> Nuevo Perfil
                        </h2>
                        <p style="color: white; margin: 10px 0 0 0; opacity: 0.9;">
                            Complete la información para crear un nuevo perfil
                        </p>
                    </div>
                    <a href="<?= base_url('dashboard/perfil/lista') ?>" class="btn btn-light">
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
                <form method="POST" action="<?= base_url('dashboard/perfil/registrar'); ?>">
                    <?= csrf_field(); ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">
                                    <i class="fas fa-tag icon-label"></i>
                                    Nombre
                                    <span class="required">*</span>
                                </label>
                                <input type="text" id="nombre" name="nombre" 
                                       value="<?= old('nombre') ?>" 
                                       class="form-control" 
                                       placeholder="Ej: Administrador"
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
                                    <i class="fas fa-toggle-on icon-label"></i>
                                    Estado
                                    <span class="required">*</span>
                                </label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="A" <?= old('estado', 'A') == 'A' ? 'selected' : '' ?>>Activo</option>
                                    <option value="I" <?= old('estado') == 'I' ? 'selected' : '' ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex gap-3 justify-content-end">
                                <button type="submit" class="btn btn-submit">
                                    <i class="fas fa-save me-2"></i> Crear Perfil
                                </button>
                                <a href="<?= base_url('dashboard/perfil/lista') ?>" class="btn btn-cancel">
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

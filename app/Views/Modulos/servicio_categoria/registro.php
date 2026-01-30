<?= $this->extend('layout/dashboard') ?>

<?= $this->section('servicio_categoria/registro') ?>

<style>
    /* Estilos generales para las cards de sección */
    .section-card {
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
        padding: 30px;
        border-left: 6px solid #00bcd4; /* Turquesa para categorías de servicio */
        transition: all 0.3s ease;
    }

    .section-card:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        transform: translateY(-3px);
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .section-title .step-number {
        background: #00bcd4; /* Turquesa para categorías de servicio */
        color: white;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        font-weight: 700;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(0, 188, 212, 0.3);
    }

    .section-title span {
        font-size: 1.6rem;
        font-weight: 700;
        color: #333;
    }

    .section-subtitle {
        font-size: 1.05rem;
        color: #666;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        font-weight: 600;
        color: #444;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-label .icon-label {
        color: #00bcd4; /* Turquesa para categorías de servicio */
        font-size: 1.1rem;
    }

    .form-control {
        border-radius: 8px;
        padding: 10px 15px;
        border: 1px solid #ddd;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #00bcd4; /* Turquesa para categorías de servicio */
        box-shadow: 0 0 0 0.2rem rgba(0, 188, 212, 0.25);
    }

    .help-text {
        font-size: 0.875rem;
        color: #888;
        margin-top: 5px;
        display: block;
    }

    .help-text .fas {
        color: #00bcd4; /* Turquesa para categorías de servicio */
    }

    .main-header h2 {
        color: white;
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 5px;
    }

    .main-header p {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.1rem;
    }

    .btn-light {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 10px 25px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-light:hover {
        background-color: rgba(255, 255, 255, 0.3);
        color: white;
        border-color: rgba(255, 255, 255, 0.5);
    }

    .btn-submit {
        padding: 12px 35px;
        font-size: 1.05rem;
        font-weight: 600;
        border-radius: 8px;
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        border: none;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
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

    .required {
        color: #dc3545;
        font-weight: 700;
    }
</style>

<div class="container-fluid">
    <div class="main-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 style="color: white;"><i class="fas fa-tags me-2"></i> Registrar Categoría de Servicio</h2>
                <p style="color: white;">Complete la información para crear una nueva categoría para sus servicios.</p>
            </div>
            <a href="<?= base_url('dashboard/servicio-categoria/lista') ?>" class="btn btn-light">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i> Errores de Validación</h5>
                    <ul class="mb-0">
                        <?php 
                        $errors = session()->getFlashdata('errors');
                        if (is_array($errors)):
                            foreach ($errors as $field => $error): ?>
                                <li><strong><?= esc(ucfirst(str_replace('_', ' ', $field))) ?>:</strong> <?= esc($error) ?></li>
                            <?php endforeach;
                        else: ?>
                            <li><?= esc($errors) ?></li>
                        <?php endif; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= base_url('dashboard/servicio-categoria/registrar') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <!-- PASO 1: Información General -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">1</span>
                        <span><i class="fas fa-info-circle icon-label"></i> Información General</span>
                    </div>
                    <p class="section-subtitle">
                        Datos básicos para identificar la categoría de servicios.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-heading icon-label"></i>
                                    Nombre de la Categoría <span class="required">*</span>
                                </label>
                                <input type="text" name="nombre" class="form-control" 
                                       value="<?= old('nombre') ?>" required 
                                       placeholder="Ej: Construcción Residencial, Remodelaciones">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Nombre único y descriptivo para agrupar servicios similares.
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-icons icon-label"></i>
                                    Icono
                                </label>
                                <input type="text" name="icono" id="icono" class="form-control" 
                                       value="<?= old('icono') ?>" placeholder="Ej: fas fa-home, fas fa-tools">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Clase CSS del icono FontAwesome (opcional, mejora presentación visual).
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 2: Apariencia -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">2</span>
                        <span><i class="fas fa-palette icon-label"></i> Apariencia</span>
                    </div>
                    <p class="section-subtitle">
                        Configure el color representativo y el orden de visualización.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-palette icon-label"></i>
                                    Color Representativo
                                </label>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="color" name="color" class="form-control form-control-color" 
                                           value="<?= old('color', '#00bcd4') ?>" style="width: 80px; height: 40px; border: 2px solid #ddd; border-radius: 5px;">
                                    <span class="text-muted">Seleccione un color que identifique esta categoría</span>
                                </div>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Color que aparecerá en badges, botones o filtros de categoría.
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-sort-numeric-down icon-label"></i>
                                    Orden de Visualización
                                </label>
                                <input type="number" name="orden" class="form-control" 
                                       value="<?= old('orden', '1') ?>" min="0" placeholder="1">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Número que define el orden de aparición (menor número = aparece primero).
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 3: Descripción y Estado -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">3</span>
                        <span><i class="fas fa-align-left icon-label"></i> Descripción y Estado</span>
                    </div>
                    <p class="section-subtitle">
                        Agregue una descripción opcional y configure el estado de publicación.
                    </p>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-paragraph icon-label"></i>
                                    Descripción
                                </label>
                                <textarea name="descripcion" class="form-control" rows="4" 
                                          placeholder="Descripción breve de qué tipo de servicios incluye esta categoría..."><?= old('descripcion') ?></textarea>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Ayuda a los usuarios a entender qué servicios pertenecen a esta categoría.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-toggle-on icon-label"></i>
                                    Estado de Publicación
                                </label>
                                <select name="estado" class="form-control">
                                    <option value="A" <?= old('estado', 'A') == 'A' ? 'selected' : '' ?>>✅ Activo (Visible públicamente)</option>
                                    <option value="I" <?= old('estado') == 'I' ? 'selected' : '' ?>>❌ Inactivo (Oculto)</option>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Si está "Activo", la categoría será visible en filtros y listados.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Campos SEO ocultos - se generan automáticamente -->
                <input type="hidden" name="meta_titulo" value="">
                <input type="hidden" name="meta_descripcion" value="">
                <input type="hidden" name="meta_keywords" value="">

                <!-- Botones de Acción -->
                <div class="section-card" style="border-left-color: #28a745;">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex gap-3 justify-content-center">
                                <button type="submit" class="btn btn-primary btn-submit">
                                    <i class="fas fa-save me-2"></i> Registrar Categoría
                                </button>
                                <a href="<?= base_url('dashboard/servicio-categoria/lista') ?>" class="btn btn-secondary btn-cancel">
                                    <i class="fas fa-times me-2"></i> Cancelar
                                </a>
                            </div>
                            <p class="text-center mt-3 mb-0 text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Revise toda la información antes de guardar
                            </p>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Cargar archivos del selector de iconos -->
<link rel="stylesheet" href="<?= base_url('lib/css/icon-selector.css') ?>">
<script src="<?= base_url('lib/js/icon-selector.js') ?>"></script>

<?= $this->endSection() ?>

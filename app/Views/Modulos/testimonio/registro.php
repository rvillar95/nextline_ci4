<?= $this->extend('layout/dashboard') ?>

<?= $this->section('testimonio/registro') ?>

<style>
    .main-header {
        background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
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
        border-left: 4px solid #9b59b6;
        transition: all 0.3s ease;
    }
    
    .section-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    
    .section-title {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        font-size: 1.3rem;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .step-number {
        background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.1rem;
        box-shadow: 0 2px 8px rgba(155, 89, 182, 0.3);
    }
    
    .section-subtitle {
        color: #7f8c8d;
        font-size: 0.95rem;
        margin-bottom: 20px;
        line-height: 1.5;
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
        color: #9b59b6;
        font-size: 1rem;
    }
    
    .form-control, .form-select {
        border: 2px solid #e1e8ed;
        border-radius: 8px;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #9b59b6;
        box-shadow: 0 0 0 0.2rem rgba(155, 89, 182, 0.15);
    }
    
    .help-text {
        display: block;
        color: #95a5a6;
        font-size: 0.875rem;
        margin-top: 5px;
    }
    
    .required {
        color: #e74c3c;
        font-weight: bold;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
        border: none;
        padding: 12px 35px;
        font-size: 1.05rem;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(155, 89, 182, 0.3);
        transition: all 0.3s ease;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(155, 89, 182, 0.4);
        background: linear-gradient(135deg, #8e44ad 0%, #7d3c98 100%);
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
                        <h2 style="color: white;"><i class="fas fa-quote-left me-2"></i> Registrar Nuevo Testimonio</h2>
                        <p style="color: white;">Complete la información del testimonio para agregarlo al sitio web</p>
                    </div>
                    <a href="<?= base_url('dashboard/testimonio/lista') ?>" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i> Volver
                    </a>
                </div>
            </div>

            <!-- MENSAJES -->
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

            <form action="<?= base_url('dashboard/testimonio/registrar') ?>" method="post">
                <?= csrf_field() ?>
                
                <!-- PASO 1: Información del Cliente -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">1</span>
                        <span><i class="fas fa-user icon-label"></i> Información del Cliente</span>
                    </div>
                    <p class="section-subtitle">
                        Datos de la persona que proporciona el testimonio.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user-circle icon-label"></i>
                                    Nombre Completo
                                    <span class="required">*</span>
                                </label>
                                <input type="text" name="nombre" class="form-control" 
                                       value="<?= old('nombre') ?>" required
                                       placeholder="Ejemplo: Juan Pérez González">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Nombre completo de la persona que da el testimonio
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-briefcase icon-label"></i>
                                    Cargo / Profesión
                                </label>
                                <input type="text" name="cargo" class="form-control" 
                                       value="<?= old('cargo') ?>" 
                                       placeholder="Ejemplo: Propietario, Arquitecto, Ingeniero">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Cargo o profesión (dejar vacío si es particular)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-building icon-label"></i>
                                    Empresa / Organización
                                </label>
                                <input type="text" name="empresa" class="form-control" 
                                       value="<?= old('empresa') ?>" 
                                       placeholder="Ejemplo: Casa Residencial, Empresa S.A.">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Empresa u organización (opcional)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 2: Calificación y Relaciones -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">2</span>
                        <span><i class="fas fa-star icon-label"></i> Calificación y Relaciones</span>
                    </div>
                    <p class="section-subtitle">
                        Puntuación del cliente y relación con proyectos o servicios.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-star-half-alt icon-label"></i>
                                    Calificación
                                    <span class="required">*</span>
                                </label>
                                <select name="calificacion" class="form-control" required>
                                    <option value="">-- Seleccione calificación --</option>
                                    <option value="1" <?= old('calificacion') == '1' ? 'selected' : '' ?>>⭐ 1 Estrella</option>
                                    <option value="2" <?= old('calificacion') == '2' ? 'selected' : '' ?>>⭐⭐ 2 Estrellas</option>
                                    <option value="3" <?= old('calificacion') == '3' ? 'selected' : '' ?>>⭐⭐⭐ 3 Estrellas</option>
                                    <option value="4" <?= old('calificacion') == '4' ? 'selected' : '' ?>>⭐⭐⭐⭐ 4 Estrellas</option>
                                    <option value="5" <?= old('calificacion') == '5' ? 'selected' : '' ?>>⭐⭐⭐⭐⭐ 5 Estrellas</option>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Nivel de satisfacción del cliente
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-project-diagram icon-label"></i>
                                    Proyecto Relacionado
                                </label>
                                <select name="proyecto_id" id="proyecto_id" class="form-control">
                                    <option value="">-- Seleccione proyecto (opcional) --</option>
                                    <?php if (!empty($proyectos)): foreach ($proyectos as $proyecto): ?>
                                        <option value="<?= $proyecto->id ?>" 
                                                data-fecha="<?= $proyecto->fecha_finalizacion ?? $proyecto->fecha_inicio ?? '' ?>"
                                                <?= old('proyecto_id') == $proyecto->id ? 'selected' : '' ?>>
                                            <?= esc($proyecto->nombre) ?>
                                        </option>
                                    <?php endforeach; endif; ?>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Vincular con un proyecto específico (opcional)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-tools icon-label"></i>
                                    Servicio Relacionado
                                </label>
                                <select name="servicio_id" class="form-control">
                                    <option value="">-- Seleccione servicio (opcional) --</option>
                                    <?php if (!empty($servicios)): foreach ($servicios as $servicio): ?>
                                        <option value="<?= $servicio->id ?>" <?= old('servicio_id') == $servicio->id ? 'selected' : '' ?>>
                                            <?= esc($servicio->nombre) ?>
                                        </option>
                                    <?php endforeach; endif; ?>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Vincular con un servicio específico (opcional)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 3: Testimonio -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">3</span>
                        <span><i class="fas fa-comment-dots icon-label"></i> Testimonio</span>
                    </div>
                    <p class="section-subtitle">
                        Opinión y experiencia del cliente con nuestros servicios.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-quote-right icon-label"></i>
                                    Testimonio del Cliente
                                    <span class="required">*</span>
                                </label>
                                <textarea name="testimonio" class="form-control" rows="6" required
                                          placeholder="Escriba aquí la opinión del cliente sobre su experiencia con nuestros servicios..."><?= old('testimonio') ?></textarea>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Máximo 2000 caracteres. Incluya detalles específicos y positivos de la experiencia
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar-alt icon-label"></i>
                                    Fecha del Proyecto
                                </label>
                                <input type="date" name="fecha_proyecto" id="fecha_proyecto" class="form-control" 
                                       value="<?= old('fecha_proyecto') ?>">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Fecha en que se realizó el proyecto (opcional)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 4: Configuración -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">4</span>
                        <span><i class="fas fa-cog icon-label"></i> Configuración de Visibilidad</span>
                    </div>
                    <p class="section-subtitle">
                        Configure cómo se mostrará este testimonio en el sitio web.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-eye icon-label"></i>
                                    Estado
                                    <span class="required">*</span>
                                </label>
                                <select name="estado" class="form-control" required>
                                    <option value="A" <?= old('estado', 'A') == 'A' ? 'selected' : '' ?>>✅ Activo (Visible en el sitio web)</option>
                                    <option value="I" <?= old('estado') == 'I' ? 'selected' : '' ?>>❌ Inactivo (Oculto)</option>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Solo los testimonios activos aparecen en la web
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-star icon-label"></i>
                                    ¿Destacar este testimonio?
                                    <span class="required">*</span>
                                </label>
                                <select name="destacado" class="form-control" required>
                                    <option value="N" <?= old('destacado', 'N') == 'N' ? 'selected' : '' ?>>No</option>
                                    <option value="S" <?= old('destacado') == 'S' ? 'selected' : '' ?>>⭐ Sí, destacar en portada</option>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Los testimonios destacados aparecen primero
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="section-card" style="border-left-color: #28a745;">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex gap-3 justify-content-center">
                                <button type="submit" class="btn btn-primary btn-submit">
                                    <i class="fas fa-save me-2"></i> Guardar Testimonio
                                </button>
                                <a href="<?= base_url('dashboard/testimonio/lista') ?>" class="btn btn-secondary btn-cancel">
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

<script>
// Autocompletar fecha del proyecto cuando se selecciona un proyecto
document.addEventListener('DOMContentLoaded', function() {
    const proyectoSelect = document.getElementById('proyecto_id');
    const fechaInput = document.getElementById('fecha_proyecto');
    
    if (proyectoSelect && fechaInput) {
        proyectoSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const fecha = selectedOption.getAttribute('data-fecha');
            
            if (fecha) {
                fechaInput.value = fecha;
                // Añadir un efecto visual para que el usuario note el cambio
                fechaInput.style.background = '#d1f2eb';
                setTimeout(() => {
                    fechaInput.style.background = '';
                }, 1000);
            }
        });
    }
});
</script>

<?= $this->endSection() ?>

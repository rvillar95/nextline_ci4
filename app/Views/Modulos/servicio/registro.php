<?= $this->extend('layout/dashboard') ?>

<?= $this->section('servicio/registro') ?>

<style>
    .section-card {
        background: #fff;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #4361ee;
    }
    
    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .section-subtitle {
        font-size: 0.95rem;
        color: #6c757d;
        margin-bottom: 25px;
        line-height: 1.6;
    }
    
    .step-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: #4361ee;
        color: white;
        border-radius: 50%;
        font-weight: bold;
        font-size: 1.1rem;
    }
    
    .form-group {
        margin-bottom: 25px !important;
    }
    
    .form-label {
        font-weight: 600;
        font-size: 1rem;
        color: #2c3e50;
        margin-bottom: 10px;
        display: block;
    }
    
    .form-label .required {
        color: #dc3545;
        font-weight: bold;
        margin-left: 3px;
    }
    
    .form-control, .form-select {
        padding: 12px 15px;
        font-size: 1rem;
        border: 2px solid #e1e8ed;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
    }
    
    .help-text {
        display: block;
        margin-top: 8px;
        font-size: 0.9rem;
        color: #6c757d;
        line-height: 1.5;
    }
    
    .icon-label {
        color: #4361ee;
        margin-right: 8px;
        font-size: 1.1rem;
    }
    
    .btn-submit {
        padding: 15px 40px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 8px;
        background: #4361ee;
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-submit:hover {
        background: #3451d1;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    }
    
    .btn-cancel {
        padding: 15px 40px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 8px;
    }
    
    .main-header {
        background: linear-gradient(135deg, #4361ee 0%, #3451d1 100%);
        color: white;
        padding: 25px 30px;
        border-radius: 12px;
        margin-bottom: 30px;
    }
    
    .main-header h2 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 700;
        color: white;
    }
    
    .main-header p {
        margin: 8px 0 0 0;
        opacity: 0.95;
        font-size: 1rem;
        color: white;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            
            <!-- Encabezado Principal -->
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2><i class="fas fa-tools me-2"></i> Registrar Nuevo Servicio</h2>
                        <p>Complete la información del servicio siguiendo los pasos a continuación</p>
                    </div>
                    <a href="<?= base_url('dashboard/servicio/lista') ?>" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i> Volver
                    </a>
                </div>
            </div>

            <!-- Mensajes de Error -->
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i> Por favor corrija los siguientes errores:</h5>
                    <ul class="mb-0 mt-2">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= form_open_multipart('dashboard/servicio/registrar') ?>
                <?= csrf_field() ?>
                
                <!-- PASO 1: Información Básica -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">1</span>
                        <span><i class="fas fa-info-circle icon-label"></i> Información Básica del Servicio</span>
                    </div>
                    <p class="section-subtitle">
                        Comience ingresando el nombre y categoría del servicio. Los campos marcados con <span style="color: #dc3545; font-weight: bold;">*</span> son obligatorios.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-tag icon-label"></i>
                                    Nombre del Servicio
                                    <span class="required">*</span>
                                </label>
                                <input type="text" name="nombre" class="form-control" 
                                       value="<?= old('nombre') ?>" 
                                       placeholder="Ejemplo: Construcción de Viviendas"
                                       required>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Ingrese un nombre claro y descriptivo del servicio
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-folder icon-label"></i>
                                    Categoría
                                    <span class="required">*</span>
                                </label>
                                <select name="categoria_id" class="form-control" required>
                                    <option value="">-- Seleccione una categoría --</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= $categoria->id ?>" <?= old('categoria_id') == $categoria->id ? 'selected' : '' ?>>
                                            <?= esc($categoria->nombre) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Seleccione la categoría a la que pertenece este servicio
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 2: Descripciones -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">2</span>
                        <span><i class="fas fa-align-left icon-label"></i> Descripción del Servicio</span>
                    </div>
                    <p class="section-subtitle">
                        Describa el servicio de manera clara y atractiva para sus clientes potenciales.
                    </p>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-file-alt icon-label"></i>
                                    Descripción Corta
                                    <span class="required">*</span>
                                </label>
                                <input type="text" name="descripcionCorta" class="form-control" 
                                       value="<?= old('descripcionCorta') ?>"
                                       placeholder="Ejemplo: Construimos su hogar con los más altos estándares de calidad..."
                                       required>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Resumen breve que aparecerá en las tarjetas de servicios (máximo 500 caracteres)
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-file-lines icon-label"></i>
                                    Descripción Detallada
                                    <span class="required">*</span>
                                </label>
                                <textarea name="descripcionLarga" class="form-control" rows="6" 
                                          placeholder="Describa detalladamente el servicio, qué incluye, cómo se realiza, qué materiales se utilizan, etc."
                                          required><?= old('descripcionLarga') ?></textarea>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Descripción completa que aparecerá en la página del servicio (máximo 2000 caracteres)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 3: Detalles del Servicio -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">3</span>
                        <span><i class="fas fa-list-check icon-label"></i> Características y Beneficios</span>
                    </div>
                    <p class="section-subtitle">
                        Liste las características principales y beneficios que ofrece este servicio.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-check-circle icon-label"></i>
                                    Características Principales
                                </label>
                                <textarea name="caracteristicas" class="form-control" rows="5" 
                                          placeholder="Escriba cada característica en una línea nueva:&#10;- Materiales de primera calidad&#10;- Personal altamente calificado&#10;- Tecnología de punta"><?= old('caracteristicas') ?></textarea>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Una característica por línea (opcional)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-star icon-label"></i>
                                    Beneficios para el Cliente
                                </label>
                                <textarea name="beneficios" class="form-control" rows="5" 
                                          placeholder="Escriba cada beneficio en una línea nueva:&#10;- Ahorro de tiempo&#10;- Garantía extendida&#10;- Asesoría personalizada"><?= old('beneficios') ?></textarea>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Un beneficio por línea (opcional)
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-clock icon-label"></i>
                                    Tiempo Estimado
                                </label>
                                <input type="text" name="tiempo_estimado" class="form-control" 
                                       value="<?= old('tiempo_estimado') ?>" 
                                       placeholder="Ejemplo: 30 días, 2-3 meses, 1 semana">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Tiempo aproximado para completar el servicio (opcional)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-shield-alt icon-label"></i>
                                    Garantía
                                </label>
                                <input type="text" name="garantia" class="form-control" 
                                       value="<?= old('garantia') ?>" 
                                       placeholder="Ejemplo: 1 año, 6 meses, Sin garantía">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Tiempo de garantía que ofrece el servicio (opcional)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 4: Precios -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">4</span>
                        <span><i class="fas fa-dollar-sign icon-label"></i> Información de Precios</span>
                    </div>
                    <p class="section-subtitle">
                        Configure cómo se mostrarán los precios del servicio a los clientes.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-eye icon-label"></i>
                                    ¿Mostrar Precio?
                                </label>
                                <select name="mostrar_precio" class="form-control">
                                    <option value="S" <?= old('mostrar_precio', 'S') == 'S' ? 'selected' : '' ?>>✅ Sí, mostrar precios</option>
                                    <option value="N" <?= old('mostrar_precio') == 'N' ? 'selected' : '' ?>>❌ No, mostrar "Consultar precio"</option>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Si selecciona "No", aparecerá "Consultar precio"
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-arrow-down icon-label"></i>
                                    Precio Desde
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="precio_desde" class="form-control" 
                                           value="<?= old('precio_desde') ?>" 
                                           step="0.01" 
                                           placeholder="500000">
                                </div>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Precio mínimo del servicio (opcional)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-arrow-up icon-label"></i>
                                    Precio Hasta
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="precio_hasta" class="form-control" 
                                           value="<?= old('precio_hasta') ?>" 
                                           step="0.01" 
                                           placeholder="2000000">
                                </div>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Precio máximo del servicio (opcional)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 5: Configuración -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">5</span>
                        <span><i class="fas fa-cog icon-label"></i> Configuración del Servicio</span>
                    </div>
                    <p class="section-subtitle">
                        Configure cómo se mostrará y ordenará este servicio en su sitio web.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-toggle-on icon-label"></i>
                                    Estado del Servicio
                                </label>
                                <select name="estado" class="form-control">
                                    <option value="A" <?= old('estado', 'A') == 'A' ? 'selected' : '' ?>>✅ Activo (Visible en la web)</option>
                                    <option value="I" <?= old('estado') == 'I' ? 'selected' : '' ?>>❌ Inactivo (Oculto en la web)</option>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Solo los servicios activos aparecen en el sitio público
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-star icon-label"></i>
                                    ¿Servicio Destacado?
                                </label>
                                <select name="destacado" class="form-control">
                                    <option value="N" <?= old('destacado', 'N') == 'N' ? 'selected' : '' ?>>No</option>
                                    <option value="S" <?= old('destacado') == 'S' ? 'selected' : '' ?>>⭐ Sí, destacar este servicio</option>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Los servicios destacados aparecen primero en la página principal
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-sort-numeric-down icon-label"></i>
                                    Orden de Visualización
                                </label>
                                <input type="number" name="orden" class="form-control" 
                                       value="<?= old('orden', '0') ?>" 
                                       min="0"
                                       placeholder="0">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Número para ordenar (menor número = aparece primero)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 6: Imagen -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">6</span>
                        <span><i class="fas fa-image icon-label"></i> Imagen del Servicio</span>
                    </div>
                    <p class="section-subtitle">
                        Agregue una imagen representativa del servicio para hacerlo más atractivo visualmente.
                    </p>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-camera icon-label"></i>
                                    Seleccionar Imagen
                                </label>
                                <input type="file" name="foto" class="form-control" accept="image/*">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Imagen representativa del servicio. Formatos aceptados: JPG, PNG, GIF (máximo 2MB)
                                </small>
                                <div class="mt-3 p-3 bg-light rounded">
                                    <strong><i class="fas fa-info-circle me-2"></i>Consejos para la imagen:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Use una imagen de buena calidad y bien iluminada</li>
                                        <li>La imagen debe representar claramente el servicio</li>
                                        <li>Las imágenes horizontales se ven mejor en las tarjetas</li>
                                        <li>Recomendado: 800x600 píxeles o superior</li>
                                    </ul>
                                </div>
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
                                    <i class="fas fa-save me-2"></i> Guardar Servicio
                                </button>
                                <a href="<?= base_url('dashboard/servicio/lista') ?>" class="btn btn-secondary btn-cancel">
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

<?= $this->endSection() ?>

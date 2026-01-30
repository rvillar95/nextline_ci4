<?= $this->extend('layout/dashboard') ?>

<?= $this->section('proyecto/registro') ?>

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
    
    .main-header h2 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 700;
    }
    
    .main-header p {
        margin: 8px 0 0 0;
        opacity: 0.95;
        font-size: 1rem;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            
            <!-- Encabezado Principal -->
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-project-diagram me-2"></i> Registrar Nuevo Proyecto</h2>
                        <p style="color: white;">Complete la información del proyecto siguiendo los pasos a continuación</p>
                    </div>
                    <a href="<?= base_url('dashboard/proyecto/lista') ?>" class="btn btn-light">
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

            <?= form_open_multipart('dashboard/proyecto/registrar') ?>
                <?= csrf_field() ?>
                
                <!-- PASO 1: Información Básica -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">1</span>
                        <span><i class="fas fa-info-circle icon-label"></i> Información Básica del Proyecto</span>
                    </div>
                    <p class="section-subtitle">
                        Comience ingresando el nombre y los datos principales del proyecto. Los campos marcados con <span style="color: #dc3545; font-weight: bold;">*</span> son obligatorios.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-pencil-alt icon-label"></i>
                                    Nombre del Proyecto
                                    <span class="required">*</span>
                                </label>
                                <input type="text" name="nombre" class="form-control" 
                                       value="<?= old('nombre') ?>" 
                                       placeholder="Ejemplo: Casa Familiar en Las Condes"
                                       required>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Ingrese un nombre descriptivo que identifique claramente el proyecto
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user icon-label"></i>
                                    Cliente
                                </label>
                                <input type="text" name="cliente" class="form-control" 
                                       value="<?= old('cliente') ?>"
                                       placeholder="Ejemplo: Juan Pérez">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Nombre del cliente para quien se realizó el proyecto (opcional)
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-layer-group icon-label"></i>
                                    Tipo de Proyecto
                                    <span class="required">*</span>
                                </label>
                                <select name="tipo_proyecto" class="form-control" required>
                                    <option value="">-- Seleccione el tipo de proyecto --</option>
                                    <option value="residencial" <?= old('tipo_proyecto') == 'residencial' ? 'selected' : '' ?>>🏠 Residencial (Casas, Departamentos, Remodelaciones)</option>
                                    <option value="comercial" <?= old('tipo_proyecto') == 'comercial' ? 'selected' : '' ?>>🏢 Comercial (Tiendas, Oficinas, Locales)</option>
                                    <option value="industrial" <?= old('tipo_proyecto') == 'industrial' ? 'selected' : '' ?>>🏭 Industrial (Fábricas, Bodegas, Galpones)</option>
                                    <option value="institucional" <?= old('tipo_proyecto') == 'institucional' ? 'selected' : '' ?>>🏛️ Institucional (Escuelas, Hospitales, Edificios Públicos)</option>
                                    <option value="otro" <?= old('tipo_proyecto') == 'otro' ? 'selected' : '' ?>>📋 Otro (Infraestructura, Mantenimiento, Reparaciones, etc.)</option>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Seleccione la categoría que mejor describe su proyecto
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-tasks icon-label"></i>
                                    Estado del Proyecto
                                    <span class="required">*</span>
                                </label>
                                <select name="estado" class="form-control" required>
                                    <option value="">-- Seleccione el estado actual --</option>
                                    <option value="en_progreso" <?= old('estado') == 'en_progreso' ? 'selected' : '' ?>>🔄 En Progreso (Se está trabajando actualmente)</option>
                                    <option value="completado" <?= old('estado') == 'completado' ? 'selected' : '' ?>>✅ Completado (Proyecto terminado)</option>
                                    <option value="en_pausa" <?= old('estado') == 'en_pausa' ? 'selected' : '' ?>>⏸️ En Pausa (Temporalmente detenido)</option>
                                    <option value="cancelado" <?= old('estado') == 'cancelado' ? 'selected' : '' ?>>❌ Cancelado</option>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Indique en qué estado se encuentra el proyecto actualmente
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 2: Ubicación y Fechas -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">2</span>
                        <span><i class="fas fa-map-marker-alt icon-label"></i> Ubicación y Fechas</span>
                    </div>
                    <p class="section-subtitle">
                        Proporcione información sobre dónde se realizó el proyecto y las fechas importantes.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-location-dot icon-label"></i>
                                    Ubicación del Proyecto
                                </label>
                                <input type="text" name="ubicacion" class="form-control" 
                                       value="<?= old('ubicacion') ?>" 
                                       placeholder="Ejemplo: Av. Las Condes 9876, Las Condes, Santiago">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Dirección completa donde se ejecutó el proyecto (opcional)
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar-plus icon-label"></i>
                                    Fecha de Inicio
                                </label>
                                <input type="date" name="fecha_inicio" class="form-control" 
                                       value="<?= old('fecha_inicio') ?>">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    ¿Cuándo comenzó el proyecto? (opcional)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar-check icon-label"></i>
                                    Fecha de Finalización
                                </label>
                                <input type="date" name="fecha_finalizacion" class="form-control" 
                                       value="<?= old('fecha_finalizacion') ?>">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    ¿Cuándo finalizó o finalizará el proyecto? (opcional)
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-ruler-combined icon-label"></i>
                                    Área Construida
                                </label>
                                <div class="input-group">
                                    <input type="number" name="area_construida" class="form-control" 
                                           value="<?= old('area_construida') ?>" 
                                           step="0.01" 
                                           placeholder="150.50">
                                    <span class="input-group-text">m²</span>
                                </div>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Metros cuadrados de área construida (opcional)
                                </small>
                            </div>
                        </div>
                        
                    </div>
                </div>

                <!-- PASO 3: Información Económica -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">3</span>
                        <span><i class="fas fa-dollar-sign icon-label"></i> Información Económica</span>
                    </div>
                    <p class="section-subtitle">
                        Configure el presupuesto del proyecto y su visibilidad pública.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-dollar-sign icon-label"></i>
                                    Presupuesto del Proyecto
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="presupuesto" class="form-control" 
                                           value="<?= old('presupuesto') ?>" 
                                           step="0.01" 
                                           placeholder="50000000">
                                </div>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Valor total o estimado del proyecto en pesos chilenos (opcional)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-money-bill-wave icon-label"></i>
                                    ¿Mostrar el presupuesto públicamente?
                                </label>
                                <select name="mostrar_presupuesto" class="form-control">
                                    <option value="0" <?= old('mostrar_presupuesto', '0') == '0' ? 'selected' : '' ?>>❌ No, mantener privado</option>
                                    <option value="1" <?= old('mostrar_presupuesto') == '1' ? 'selected' : '' ?>>✅ Sí, mostrar el valor en la web</option>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Si elige "Sí", el presupuesto será visible para todos los visitantes
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 4: Descripciones -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">4</span>
                        <span><i class="fas fa-align-left icon-label"></i> Descripción del Proyecto</span>
                    </div>
                    <p class="section-subtitle">
                        Cuéntenos más sobre el proyecto. Agregue descripciones que ayuden a entender mejor el trabajo realizado.
                    </p>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-file-alt icon-label"></i>
                                    Descripción Corta
                                </label>
                                <textarea name="descripcion_corta" class="form-control" rows="3" 
                                          placeholder="Escriba una descripción breve del proyecto. Por ejemplo: 'Construcción de casa moderna de dos pisos con diseño minimalista y acabados de primera calidad...'"><?= old('descripcion_corta') ?></textarea>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Resumen breve que aparecerá en las tarjetas de proyectos (máximo 500 caracteres)
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
                                </label>
                                <textarea name="descripcion_detallada" class="form-control" rows="5" 
                                          placeholder="Describa el proyecto con mayor detalle. Incluya aspectos importantes como el alcance del trabajo, desafíos superados, soluciones implementadas, etc."><?= old('descripcion_detallada') ?></textarea>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Descripción completa que aparecerá en la página del proyecto (opcional)
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-tools icon-label"></i>
                                    Características Técnicas
                                </label>
                                <textarea name="caracteristicas_tecnicas" class="form-control" rows="4" 
                                          placeholder="Mencione aspectos técnicos importantes. Por ejemplo: 'Sistema eléctrico trifásico, instalación de paneles solares, aislación térmica de alta eficiencia...'"><?= old('caracteristicas_tecnicas') ?></textarea>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Detalles técnicos, sistemas especiales, tecnologías utilizadas (opcional)
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-boxes-stacked icon-label"></i>
                                    Materiales Principales
                                </label>
                                <textarea name="materiales_principales" class="form-control" rows="3" 
                                          placeholder="Liste los materiales principales. Por ejemplo: 'Hormigón armado, Cerámica porcelanato, Ventanas de PVC termopanel...'"><?= old('materiales_principales') ?></textarea>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Principales materiales y elementos utilizados en el proyecto (opcional)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 5: Testimonio del Cliente -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">5</span>
                        <span><i class="fas fa-comment-dots icon-label"></i> Testimonio del Cliente</span>
                    </div>
                    <p class="section-subtitle">
                        Si el cliente dio su opinión sobre el proyecto, puede agregarla aquí. Esto ayuda a generar confianza con futuros clientes.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-quote-left icon-label"></i>
                                    Comentario del Cliente
                                </label>
                                <textarea name="testimonio_cliente" class="form-control" rows="4" 
                                          placeholder="Por ejemplo: 'Excelente trabajo, muy profesionales y cumplieron con todos los plazos. Estamos muy contentos con el resultado...'"><?= old('testimonio_cliente') ?></textarea>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Opinión o comentario del cliente sobre el proyecto (opcional)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user-tag icon-label"></i>
                                    Nombre del Cliente
                                </label>
                                <input type="text" name="nombre_cliente" class="form-control" 
                                       value="<?= old('nombre_cliente') ?>" 
                                       placeholder="Ejemplo: María González">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Nombre que aparecerá junto al testimonio
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 6: Imágenes -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">6</span>
                        <span><i class="fas fa-images icon-label"></i> Imágenes del Proyecto</span>
                    </div>
                    <p class="section-subtitle">
                        Agregue fotografías del proyecto para mostrar el trabajo realizado. Las imágenes son muy importantes para atraer clientes.
                    </p>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-camera icon-label"></i>
                                    Seleccionar Imágenes
                                </label>
                                <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Puede seleccionar múltiples imágenes a la vez. Formatos aceptados: JPG, PNG, GIF (máximo 5MB por imagen)
                                </small>
                                <div class="mt-3 p-3 bg-light rounded">
                                    <strong><i class="fas fa-info-circle me-2"></i>Consejos para las imágenes:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Use fotos de buena calidad y bien iluminadas</li>
                                        <li>Muestre diferentes ángulos del proyecto</li>
                                        <li>Incluya fotos del antes y después si es posible</li>
                                        <li>Las fotos horizontales se ven mejor en la web</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 7: Configuración de Visibilidad -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">7</span>
                        <span><i class="fas fa-cog icon-label"></i> Configuración de Visibilidad</span>
                    </div>
                    <p class="section-subtitle">
                        Configure cómo se mostrará este proyecto en su sitio web público.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-eye icon-label"></i>
                                    ¿Mostrar este proyecto públicamente?
                                </label>
                                <select name="estado_publico" class="form-control">
                                    <option value="A" <?= old('estado_publico', 'A') == 'A' ? 'selected' : '' ?>>✅ Sí, mostrar en el sitio web</option>
                                    <option value="I" <?= old('estado_publico') == 'I' ? 'selected' : '' ?>>❌ No, mantener oculto</option>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Solo los proyectos activos aparecen en la web pública
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-star icon-label"></i>
                                    ¿Proyecto Destacado?
                                </label>
                                <select name="destacado" class="form-control">
                                    <option value="0" <?= old('destacado', '0') == '0' ? 'selected' : '' ?>>No</option>
                                    <option value="1" <?= old('destacado') == '1' ? 'selected' : '' ?>>⭐ Sí, destacar este proyecto</option>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Los proyectos destacados aparecen primero en la página principal
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
                                    <i class="fas fa-save me-2"></i> Guardar Proyecto
                                </button>
                                <a href="<?= base_url('dashboard/proyecto/lista') ?>" class="btn btn-secondary btn-cancel">
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

<?= $this->extend('layout/dashboard') ?>

<?= $this->section('cotizacion/registro') ?>

<style>
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #1c9b8e;
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
        background: linear-gradient(135deg, #1c9b8e 0%, #148278 100%);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.1rem;
        box-shadow: 0 2px 8px rgba(28, 155, 142, 0.3);
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
        color: #1c9b8e;
        font-size: 1rem;
    }
    
    .form-control, .form-select {
        border: 2px solid #e1e8ed;
        border-radius: 8px;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #1c9b8e;
        box-shadow: 0 0 0 0.2rem rgba(28, 155, 142, 0.15);
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
        background: linear-gradient(135deg, #1c9b8e 0%, #148278 100%);
        border: none;
        padding: 12px 35px;
        font-size: 1.05rem;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(28, 155, 142, 0.3);
        transition: all 0.3s ease;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(28, 155, 142, 0.4);
        background: linear-gradient(135deg, #148278 0%, #0f6b61 100%);
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
                        <h2 style="color: white;"><i class="fas fa-file-invoice-dollar me-2"></i> Nueva Cotización</h2>
                        <p style="color: white;">Complete la información para crear una nueva cotización profesional</p>
                    </div>
                    <a href="<?= base_url('dashboard/cotizacion/lista') ?>" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i> Volver
                    </a>
                </div>
            </div>

            <!-- MENSAJES -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> <?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i> <?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <h5><i class="fas fa-exclamation-triangle"></i> Errores de Validación</h5>
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('dashboard/cotizacion/registrar') ?>" method="post" id="formCotizacion">
                <?= csrf_field() ?>
                
                <!-- PASO 1: Información del Cliente -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">1</span>
                        <span><i class="fas fa-user-tie icon-label"></i> Información del Cliente</span>
                    </div>
                    <p class="section-subtitle">
                        Seleccione el cliente y defina el título de la cotización.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user icon-label"></i>
                                    Cliente
                                    <span class="required">*</span>
                                </label>
                                <select name="cliente_id" id="cliente_id" class="form-control" required>
                                    <option value="">-- Seleccione un cliente --</option>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <option value="<?= $cliente->id ?>" 
                                                data-tipo="<?= $cliente->tipo_cliente ?>"
                                                data-contacto="<?= esc($cliente->contacto_completo) ?>"
                                                <?= (old('cliente_id') == $cliente->id || (isset($clienteSeleccionado) && $clienteSeleccionado && $clienteSeleccionado->id == $cliente->id)) ? 'selected' : '' ?>>
                                            <?= esc($cliente->display_name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Elija el cliente para quien se realizará la cotización
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-heading icon-label"></i>
                                    Título de la Cotización
                                    <span class="required">*</span>
                                </label>
                                <input type="text" name="titulo" class="form-control" 
                                       value="<?= old('titulo') ?>" required 
                                       placeholder="Ejemplo: Construcción Casa Residencial">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Un título descriptivo que identifique el proyecto
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 2: Detalles de la Cotización -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">2</span>
                        <span><i class="fas fa-calendar-alt icon-label"></i> Detalles de la Cotización</span>
                    </div>
                    <p class="section-subtitle">
                        Configure las fechas, estado, prioridad y parámetros de cálculo.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar-day icon-label"></i>
                                    Fecha de Cotización
                                    <span class="required">*</span>
                                </label>
                                <input type="date" name="fecha_cotizacion" class="form-control" 
                                       value="<?= old('fecha_cotizacion', date('Y-m-d')) ?>" required>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Fecha en que se emite la cotización
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar-check icon-label"></i>
                                    Válida hasta
                                </label>
                                <input type="date" name="fecha_validez" class="form-control" 
                                       value="<?= old('fecha_validez', date('Y-m-d', strtotime('+30 days'))) ?>">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Por defecto 30 días desde hoy (opcional)
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-tasks icon-label"></i>
                                    Estado
                                    <span class="required">*</span>
                                </label>
                                <select name="estado_cotizacion" class="form-control" required>
                                    <option value="borrador" <?= old('estado_cotizacion') == 'borrador' ? 'selected' : '' ?>>📝 Borrador (En proceso)</option>
                                    <option value="enviada" <?= old('estado_cotizacion') == 'enviada' ? 'selected' : '' ?>>📧 Enviada (Entregada al cliente)</option>
                                    <option value="revisada" <?= old('estado_cotizacion') == 'revisada' ? 'selected' : '' ?>>👁️ Revisada (Cliente la revisó)</option>
                                    <option value="aprobada" <?= old('estado_cotizacion') == 'aprobada' ? 'selected' : '' ?>>✅ Aprobada (Aceptada)</option>
                                    <option value="rechazada" <?= old('estado_cotizacion') == 'rechazada' ? 'selected' : '' ?>>❌ Rechazada (No aceptada)</option>
                                    <option value="expirada" <?= old('estado_cotizacion') == 'expirada' ? 'selected' : '' ?>>⏰ Expirada (Venció el plazo)</option>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Estado actual de la cotización
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-flag icon-label"></i>
                                    Prioridad
                                    <span class="required">*</span>
                                </label>
                                <select name="prioridad" class="form-control" required>
                                    <option value="baja" <?= old('prioridad') == 'baja' ? 'selected' : '' ?>>🟢 Baja</option>
                                    <option value="media" <?= old('prioridad') == 'media' ? 'selected' : '' ?>>🟡 Media</option>
                                    <option value="alta" <?= old('prioridad') == 'alta' ? 'selected' : '' ?>>🔴 Alta (Urgente)</option>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Nivel de importancia
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-percentage icon-label"></i>
                                    IVA (%)
                                </label>
                                <input type="number" name="iva_porcentaje" id="iva_porcentaje" class="form-control" 
                                       value="<?= old('iva_porcentaje', 19) ?>" min="0" max="100" step="0.01">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Porcentaje de IVA a aplicar (19% por defecto)
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-percent icon-label"></i>
                                    Descuento (%)
                                </label>
                                <input type="number" name="descuento_porcentaje" id="descuento_porcentaje" class="form-control" 
                                       value="<?= old('descuento_porcentaje', 0) ?>" min="0" max="100" step="0.01">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Descuento porcentual sobre el subtotal (opcional)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-dollar-sign icon-label"></i>
                                    Descuento ($)
                                </label>
                                <input type="text" name="descuento_monto" id="descuento_monto" class="form-control" 
                                       value="<?= old('descuento_monto', 0) ?>" placeholder="0">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Descuento en monto fijo en pesos (opcional)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 3: Información del Proyecto -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">3</span>
                        <span><i class="fas fa-building icon-label"></i> Información del Proyecto</span>
                    </div>
                    <p class="section-subtitle">
                        Proporcione detalles adicionales sobre el proyecto a cotizar.
                    </p>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-align-left icon-label"></i>
                                    Descripción del Proyecto
                                </label>
                                <textarea name="descripcion" class="form-control" rows="4" 
                                          placeholder="Describa detalladamente el proyecto a cotizar..."><?= old('descripcion') ?></textarea>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Una descripción clara ayuda al cliente a entender el alcance (opcional)
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
                                <select name="proyecto_tipo" class="form-control" required>
                                    <option value="">-- Seleccione el tipo de proyecto --</option>
                                    <option value="residencial" <?= old('proyecto_tipo') == 'residencial' ? 'selected' : '' ?>>🏠 Residencial</option>
                                    <option value="comercial" <?= old('proyecto_tipo') == 'comercial' ? 'selected' : '' ?>>🏢 Comercial</option>
                                    <option value="industrial" <?= old('proyecto_tipo') == 'industrial' ? 'selected' : '' ?>>🏭 Industrial</option>
                                    <option value="institucional" <?= old('proyecto_tipo') == 'institucional' ? 'selected' : '' ?>>🏛️ Institucional</option>
                                    <option value="infraestructura" <?= old('proyecto_tipo') == 'infraestructura' ? 'selected' : '' ?>>🛣️ Infraestructura</option>
                                    <option value="remodelacion" <?= old('proyecto_tipo') == 'remodelacion' ? 'selected' : '' ?>>🔨 Remodelación</option>
                                    <option value="ampliacion" <?= old('proyecto_tipo') == 'ampliacion' ? 'selected' : '' ?>>➕ Ampliación</option>
                                    <option value="mantenimiento" <?= old('proyecto_tipo') == 'mantenimiento' ? 'selected' : '' ?>>🔧 Mantenimiento</option>
                                    <option value="reparacion" <?= old('proyecto_tipo') == 'reparacion' ? 'selected' : '' ?>>🛠️ Reparación</option>
                                    <option value="otros" <?= old('proyecto_tipo') == 'otros' ? 'selected' : '' ?>>📋 Otros</option>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Categoría del proyecto a realizar
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-ruler-combined icon-label"></i>
                                    Área del Proyecto (m²)
                                </label>
                                <input type="number" name="proyecto_area" class="form-control" 
                                       value="<?= old('proyecto_area') ?>" min="0" step="0.01" 
                                       placeholder="Ejemplo: 120.50">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Metros cuadrados del área a intervenir (opcional)
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-map-marked-alt icon-label"></i>
                                    Ubicación del Proyecto
                                </label>
                                <input type="text" name="proyecto_ubicacion" class="form-control" 
                                       value="<?= old('proyecto_ubicacion') ?>" 
                                       placeholder="Ejemplo: Santiago, Región Metropolitana">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Ciudad o comuna donde se realizará el proyecto (opcional)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-map-marker-alt icon-label"></i>
                                    Dirección del Proyecto
                                </label>
                                <input type="text" name="proyecto_direccion" class="form-control" 
                                       value="<?= old('proyecto_direccion') ?>" 
                                       placeholder="Ejemplo: Av. Principal 123, Comuna">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Dirección exacta del proyecto (opcional)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 4: Items de la Cotización -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">4</span>
                        <span><i class="fas fa-list-ul icon-label"></i> Items de la Cotización</span>
                    </div>
                    <p class="section-subtitle">
                        Agregue los items que componen la cotización. Puede añadir materiales, mano de obra, equipos y servicios.
                    </p>
                    
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" onclick="agregarItem()">
                            <i class="fas fa-plus me-2"></i> Agregar Item
                        </button>
                    </div>
                    
                    <div id="items-container">
                        <!-- Los items se agregarán dinámicamente -->
                    </div>
                </div>

                <!-- Totales -->
                <div class="section-card" style="border-left-color: #1c9b8e;">
                    <div class="row">
                        <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <div class="card" style="border: 2px solid #1c9b8e;">
                                <div class="card-header" style="background: linear-gradient(135deg, #1c9b8e 0%, #148278 100%); color: white;">
                                    <h6 class="card-title mb-0"><i class="fas fa-calculator me-2"></i> Resumen de Totales</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-6"><strong>Subtotal:</strong></div>
                                        <div class="col-6 text-right">$<span id="subtotal">0</span></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-6"><strong>Descuento:</strong></div>
                                        <div class="col-6 text-right">$<span id="descuento">0</span> <span id="descuento_detalle" class="text-muted small"></span></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-6"><strong>IVA:</strong></div>
                                        <div class="col-6 text-right">$<span id="iva">0</span></div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6"><strong style="font-size: 1.1rem;">TOTAL:</strong></div>
                                        <div class="col-6 text-right"><strong style="font-size: 1.1rem; color: #1c9b8e;">$<span id="total">0</span></strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campos ocultos para totales -->
                <input type="hidden" name="subtotal" id="hidden_subtotal" value="0">
                <input type="hidden" name="descuento_monto" id="hidden_descuento" value="0">
                <input type="hidden" name="iva_monto" id="hidden_iva" value="0">
                <input type="hidden" name="total_general" id="hidden_total" value="0">

                <!-- PASO 5: Condiciones y Observaciones -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">5</span>
                        <span><i class="fas fa-file-contract icon-label"></i> Condiciones y Observaciones</span>
                    </div>
                    <p class="section-subtitle">
                        Agregue información adicional sobre las condiciones de pago y observaciones relevantes.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-hand-holding-usd icon-label"></i>
                                    Condiciones de Pago
                                </label>
                                <textarea name="condiciones_pago" class="form-control" rows="3" 
                                          placeholder="Ejemplo: 30% anticipo, 40% al 50% de avance, 30% al finalizar..."><?= old('condiciones_pago') ?></textarea>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Especifique cómo y cuándo se realizarán los pagos (opcional)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-comment-dots icon-label"></i>
                                    Observaciones
                                </label>
                                <textarea name="observaciones" class="form-control" rows="3" 
                                          placeholder="Información adicional relevante..."><?= old('observaciones') ?></textarea>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Notas adicionales, exclusiones, garantías, etc. (opcional)
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
                                    <i class="fas fa-save me-2"></i> Guardar Cotización
                                </button>
                                <a href="<?= base_url('dashboard/cotizacion/lista') ?>" class="btn btn-secondary btn-cancel">
                                    <i class="fas fa-times me-2"></i> Cancelar
                                </a>
                            </div>
                            <p class="text-center mt-3 mb-0 text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Revise todos los items y totales antes de guardar
                            </p>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
let itemCounter = 0;

function agregarItem() {
    itemCounter++;
    const container = document.getElementById('items-container');
    
    const itemHtml = `
        <div class="item-row border p-3 mb-3" data-item="${itemCounter}" style="border-radius: 10px; background: #f8f9fa;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0" style="color: #1c9b8e;"><i class="fas fa-cube me-2"></i> Item #${itemCounter}</h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="eliminarItem(${itemCounter})">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <label class="form-label">Descripción <span class="required">*</span></label>
                    <input type="text" name="items[${itemCounter}][descripcion]" class="form-control" required 
                           placeholder="Ej: Excavación de terreno">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Cantidad <span class="required">*</span></label>
                    <input type="number" name="items[${itemCounter}][cantidad]" class="form-control cantidad" 
                           min="0" step="0.01" required placeholder="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Unidad <span class="required">*</span></label>
                    <select name="items[${itemCounter}][unidad]" class="form-control unidad-select" required>
                        <option value="unidad">Unidad</option>
                        <option value="m2">m²</option>
                        <option value="m3">m³</option>
                        <option value="ml">ml</option>
                        <option value="kg">kg</option>
                        <option value="ton">ton</option>
                        <option value="hr">Hora</option>
                        <option value="dia">Día</option>
                        <option value="semana">Semana</option>
                        <option value="mes">Mes</option>
                        <option value="jornada">Jornada</option>
                        <option value="obra">Obra</option>
                        <option value="servicio">Servicio</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Precio Unitario</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="text" name="items[${itemCounter}][precio_unitario]" class="form-control precio" 
                               placeholder="0">
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-3">
                    <label class="form-label">Tipo de Item</label>
                    <select name="items[${itemCounter}][categoria]" class="form-control">
                        <option value="material">🧱 Material</option>
                        <option value="mano_obra">👷 Mano de Obra</option>
                        <option value="equipo">🚜 Equipo</option>
                        <option value="servicio">⚙️ Servicio</option>
                        <option value="transporte">🚚 Transporte</option>
                        <option value="otros">📦 Otros</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Subtipo</label>
                    <div class="position-relative">
                        <input type="text" name="items[${itemCounter}][subcategoria]" class="form-control subtipo-input" 
                               placeholder="Escribir o seleccionar..." autocomplete="off">
                        <div class="subtipo-suggestions" style="display: none;"></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">¿Opcional?</label>
                    <select name="items[${itemCounter}][es_opcional]" class="form-control">
                        <option value="0">No</option>
                        <option value="1">Sí</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Subtotal</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="text" class="form-control subtotal" readonly style="background: #e9ecef; font-weight: 600;">
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHtml);
    
    // Agregar event listeners para cálculo automático
    const row = container.querySelector(`[data-item="${itemCounter}"]`);
    const cantidadInput = row.querySelector('.cantidad');
    const precioInput = row.querySelector('.precio');
    const subtotalInput = row.querySelector('.subtotal');
    
    cantidadInput.addEventListener('input', calcularSubtotal);
    precioInput.addEventListener('input', function() {
        formatearPrecioInput(this);
        calcularSubtotal();
    });
    
    // Agregar listener para cambio de tipo de item
    const tipoItemSelect = row.querySelector('select[name*="[categoria]"]');
    const unidadSelect = row.querySelector('.unidad-select');
    const subtipoInput = row.querySelector('.subtipo-input');
    
    tipoItemSelect.addEventListener('change', function() {
        actualizarUnidadesPorTipo(this.value, unidadSelect);
    });
    
    // Configurar autocompletado para subtipo
    configurarAutocompletadoSubtipo(subtipoInput);
    
    function calcularSubtotal() {
        const cantidad = parseFloat(cantidadInput.value) || 0;
        const precio = parseFloat(precioInput.value.replace(/\./g, '').replace(',', '.')) || 0;
        const subtotal = cantidad * precio;
        
        subtotalInput.value = formatearNumero(subtotal);
        calcularTotales();
    }
}

function eliminarItem(itemId) {
    const item = document.querySelector(`[data-item="${itemId}"]`);
    if (item) {
        item.remove();
        calcularTotales();
    }
}

function calcularTotales() {
    let subtotal = 0;
    
    document.querySelectorAll('.subtotal').forEach(input => {
        subtotal += parseFloat(input.value.replace(/\./g, '').replace(',', '.')) || 0;
    });
    
    const ivaPorcentaje = parseFloat(document.getElementById('iva_porcentaje').value) || 0;
    const descuentoPorcentaje = parseFloat(document.getElementById('descuento_porcentaje').value) || 0;
    const descuentoMonto = parseFloat(document.getElementById('descuento_monto').value.replace(/\./g, '').replace(',', '.')) || 0;
    
    // Calcular descuento total (porcentaje + monto fijo)
    const descuentoPorcentajeCalculado = subtotal * (descuentoPorcentaje / 100);
    const descuentoTotal = descuentoPorcentajeCalculado + descuentoMonto;
    
    // Calcular subtotal después del descuento
    const subtotalConDescuento = subtotal - descuentoTotal;
    
    const iva = subtotalConDescuento * (ivaPorcentaje / 100);
    const total = subtotalConDescuento + iva;
    
    // Actualizar display
    document.getElementById('subtotal').textContent = formatearNumero(subtotal);
    document.getElementById('descuento').textContent = formatearNumero(descuentoTotal);
    
    // Mostrar detalle del descuento
    const descuentoDetalle = document.getElementById('descuento_detalle');
    let detalleTexto = '';
    
    if (descuentoPorcentaje > 0 && descuentoMonto > 0) {
        detalleTexto = `(-${descuentoPorcentaje}% - $${formatearNumero(descuentoMonto)})`;
    } else if (descuentoPorcentaje > 0) {
        detalleTexto = `(-${descuentoPorcentaje}%)`;
    } else if (descuentoMonto > 0) {
        detalleTexto = `(-$${formatearNumero(descuentoMonto)})`;
    }
    
    descuentoDetalle.textContent = detalleTexto;
    
    document.getElementById('iva').textContent = formatearNumero(iva);
    document.getElementById('total').textContent = formatearNumero(total);
    
    // Actualizar campos ocultos
    document.getElementById('hidden_subtotal').value = subtotal;
    document.getElementById('hidden_descuento').value = descuentoTotal;
    document.getElementById('hidden_iva').value = iva;
    document.getElementById('hidden_total').value = total;
}

// Función para formatear números con puntos de miles
function formatearNumero(numero) {
    return new Intl.NumberFormat('es-CL', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    }).format(numero);
}

// Función para formatear input de precio mientras se escribe
function formatearPrecioInput(input) {
    let valor = input.value.replace(/[^\d,]/g, '');
    if (valor) {
        let numero = parseFloat(valor.replace(',', '.'));
        if (!isNaN(numero)) {
            input.value = formatearNumero(numero);
        }
    }
}

// Función para actualizar unidades según el tipo de item
function actualizarUnidadesPorTipo(tipoItem, unidadSelect) {
    const opciones = {
        'material': [
            {value: 'unidad', text: 'Unidad'},
            {value: 'm2', text: 'm²'},
            {value: 'm3', text: 'm³'},
            {value: 'ml', text: 'ml'},
            {value: 'kg', text: 'kg'},
            {value: 'ton', text: 'ton'}
        ],
        'mano_obra': [
            {value: 'hr', text: 'Hora'},
            {value: 'dia', text: 'Día'},
            {value: 'jornada', text: 'Jornada'},
            {value: 'semana', text: 'Semana'},
            {value: 'mes', text: 'Mes'},
            {value: 'obra', text: 'Obra'}
        ],
        'equipo': [
            {value: 'hr', text: 'Hora'},
            {value: 'dia', text: 'Día'},
            {value: 'semana', text: 'Semana'},
            {value: 'mes', text: 'Mes'},
            {value: 'unidad', text: 'Unidad'}
        ],
        'servicio': [
            {value: 'servicio', text: 'Servicio'},
            {value: 'unidad', text: 'Unidad'},
            {value: 'hr', text: 'Hora'},
            {value: 'dia', text: 'Día'}
        ],
        'otro': [
            {value: 'unidad', text: 'Unidad'},
            {value: 'hr', text: 'Hora'},
            {value: 'dia', text: 'Día'},
            {value: 'otro', text: 'Otro'}
        ]
    };
    
    // Limpiar opciones actuales
    unidadSelect.innerHTML = '';
    
    // Agregar nuevas opciones
    const unidades = opciones[tipoItem] || opciones['otro'];
    unidades.forEach(unidad => {
        const option = document.createElement('option');
        option.value = unidad.value;
        option.textContent = unidad.text;
        unidadSelect.appendChild(option);
    });
}

// Lista completa de subtipos de construcción
const subtiposConstruccion = [
    // Materiales
    'Hormigón', 'Ladrillos', 'Cemento', 'Arena', 'Grava', 'Acero', 'Alambre', 'Malla',
    'Madera', 'Tableros', 'Tejas', 'Zinc', 'Pintura', 'Impermeabilizante', 'Aislamiento',
    'Vidrios', 'Puertas', 'Ventanas', 'Cerámicas', 'Porcelanato', 'Mármol', 'Granito',
    'Tornillos', 'Clavos', 'Pegamentos', 'Selladores', 'Tuberías', 'Conexiones', 'Cables',
    'Iluminación', 'Enchufes', 'Interruptores',
    // Mano de Obra
    'Excavación', 'Cimentación', 'Estructura', 'Albañilería', 'Carpintería', 'Herrería',
    'Techumbre', 'Revestimientos', 'Pintura', 'Instalación Eléctrica', 'Instalación Sanitaria',
    'Instalación de Gas', 'Demolición', 'Limpieza', 'Acabados', 'Impermeabilización',
    'Aislamiento', 'Jardinería', 'Paisajismo',
    // Servicios
    'Diseño', 'Arquitectura', 'Ingeniería', 'Topografía', 'Estudios de Suelo', 'Permisos',
    'Inspecciones', 'Supervisión', 'Consultoría', 'Asesoría', 'Mantenimiento', 'Reparación',
    'Restauración', 'Remodelación', 'Ampliación', 'Construcción Completa',
    // Equipos
    'Excavadora', 'Retroexcavadora', 'Bulldozer', 'Grúa', 'Andamios', 'Escaleras',
    'Herramientas', 'Generador', 'Compresor', 'Mezcladora', 'Vibrador', 'Cortadora',
    'Taladro', 'Soldadora', 'Pulidora', 'Lijadora', 'Pistola de Pintura', 'Nivel Láser',
    'Medidor', 'Camión', 'Volquete', 'Plataforma'
];

// Función para configurar autocompletado de subtipo
function configurarAutocompletadoSubtipo(input) {
    const container = input.closest('.position-relative');
    const suggestions = container.querySelector('.subtipo-suggestions');
    
    // Estilos para el contenedor de sugerencias
    suggestions.style.cssText = `
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ccc;
        border-top: none;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    `;
    
    input.addEventListener('input', function() {
        const valor = this.value.toLowerCase();
        
        if (valor.length < 2) {
            suggestions.style.display = 'none';
            return;
        }
        
        // Filtrar subtipos que coincidan
        const coincidencias = subtiposConstruccion.filter(subtipo => 
            subtipo.toLowerCase().includes(valor)
        );
        
        if (coincidencias.length === 0) {
            suggestions.style.display = 'none';
            return;
        }
        
        // Mostrar sugerencias
        suggestions.innerHTML = '';
        coincidencias.slice(0, 10).forEach(subtipo => {
            const div = document.createElement('div');
            div.textContent = subtipo;
            div.style.cssText = `
                padding: 8px 12px;
                cursor: pointer;
                border-bottom: 1px solid #eee;
            `;
            
            div.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f0f0f0';
            });
            
            div.addEventListener('mouseleave', function() {
                this.style.backgroundColor = 'white';
            });
            
            div.addEventListener('click', function() {
                input.value = subtipo;
                suggestions.style.display = 'none';
            });
            
            suggestions.appendChild(div);
        });
        
        suggestions.style.display = 'block';
    });
    
    // Ocultar sugerencias al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!container.contains(e.target)) {
            suggestions.style.display = 'none';
        }
    });
    
    // Ocultar sugerencias al presionar Escape
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            suggestions.style.display = 'none';
        }
    });
}

// Agregar un item inicial
document.addEventListener('DOMContentLoaded', function() {
    agregarItem();
    
    // Agregar listeners para los campos de cálculo
    const ivaInput = document.getElementById('iva_porcentaje');
    const descuentoPorcentajeInput = document.getElementById('descuento_porcentaje');
    const descuentoMontoInput = document.getElementById('descuento_monto');
    
    if (ivaInput) {
        ivaInput.addEventListener('input', calcularTotales);
    }
    if (descuentoPorcentajeInput) {
        descuentoPorcentajeInput.addEventListener('input', calcularTotales);
    }
    if (descuentoMontoInput) {
        descuentoMontoInput.addEventListener('input', function() {
            formatearPrecioInput(this);
            calcularTotales();
        });
    }
});
</script>

<?= $this->endSection() ?>

<?= $this->extend('layout/dashboard') ?>

<?= $this->section('cotizacion/editar') ?>

<style>
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
        gap: 15px;
        margin-bottom: 15px;
        font-size: 1.3rem;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .step-number {
        background: linear-gradient(135deg, #f0841a 0%, #e67e00 100%);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.1rem;
        box-shadow: 0 2px 8px rgba(240, 132, 26, 0.3);
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
        background: linear-gradient(135deg, #f0841a 0%, #e67e00 100%);
        border: none;
        padding: 12px 35px;
        font-size: 1.05rem;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(240, 132, 26, 0.3);
        transition: all 0.3s ease;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(240, 132, 26, 0.4);
        background: linear-gradient(135deg, #e67e00 0%, #d97300 100%);
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
                        <h2 style="color: white;"><i class="fas fa-edit me-2"></i> Editar Cotización</h2>
                        <p style="color: white;">Nº <?= esc($cotizacion->numero_cotizacion) ?> - Modifique la información y guarde los cambios</p>
                    </div>
                    <a href="<?= base_url('dashboard/cotizacion/lista') ?>" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i> Volver
                    </a>
                </div>
            </div>

            <!-- MENSAJES -->
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

            <form action="<?= base_url('dashboard/cotizacion/update') ?>" method="post" id="formCotizacion">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $cotizacion->id ?>">
                
                <!-- PASO 1: Información del Cliente -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">1</span>
                        <span><i class="fas fa-user-tie icon-label"></i> Información del Cliente</span>
                    </div>
                    <p class="section-subtitle">
                        Cliente y título de la cotización.
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
                                                data-contacto="<?= esc($cliente->contacto_completo ?? '') ?>"
                                                <?= old('cliente_id', $cotizacion->cliente_id) == $cliente->id ? 'selected' : '' ?>>
                                            <?= esc($cliente->display_name ?? $cliente->nombre_razon_social) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Cliente para quien se realizará la cotización
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
                                       value="<?= old('titulo', $cotizacion->proyecto_nombre ?? $cotizacion->titulo ?? '') ?>" required>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Título descriptivo que identifique el proyecto
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
                        Fechas, estado, prioridad y parámetros de cálculo.
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
                                       value="<?= old('fecha_cotizacion', $cotizacion->fecha_cotizacion) ?>" required>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Fecha en que se emitió la cotización
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
                                       value="<?= old('fecha_validez', $cotizacion->fecha_validez) ?>">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Fecha límite de vigencia (opcional)
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
                                    <option value="borrador" <?= old('estado_cotizacion', $cotizacion->estado ?? 'borrador') == 'borrador' ? 'selected' : '' ?>>📝 Borrador (En proceso)</option>
                                    <option value="enviada" <?= old('estado_cotizacion', $cotizacion->estado ?? 'borrador') == 'enviada' ? 'selected' : '' ?>>📧 Enviada (Entregada al cliente)</option>
                                    <option value="revisada" <?= old('estado_cotizacion', $cotizacion->estado ?? 'borrador') == 'revisada' ? 'selected' : '' ?>>👁️ Revisada (Cliente la revisó)</option>
                                    <option value="aprobada" <?= old('estado_cotizacion', $cotizacion->estado ?? 'borrador') == 'aprobada' ? 'selected' : '' ?>>✅ Aprobada (Aceptada)</option>
                                    <option value="rechazada" <?= old('estado_cotizacion', $cotizacion->estado ?? 'borrador') == 'rechazada' ? 'selected' : '' ?>>❌ Rechazada (No aceptada)</option>
                                    <option value="expirada" <?= old('estado_cotizacion', $cotizacion->estado ?? 'borrador') == 'expirada' ? 'selected' : '' ?>>⏰ Expirada (Venció el plazo)</option>
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
                                    <option value="baja" <?= old('prioridad', $cotizacion->prioridad ?? 'media') == 'baja' ? 'selected' : '' ?>>🟢 Baja</option>
                                    <option value="media" <?= old('prioridad', $cotizacion->prioridad ?? 'media') == 'media' ? 'selected' : '' ?>>🟡 Media</option>
                                    <option value="alta" <?= old('prioridad', $cotizacion->prioridad ?? 'media') == 'alta' ? 'selected' : '' ?>>🔴 Alta (Urgente)</option>
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
                                <input type="number" name="iva_porcentaje" class="form-control" 
                                       value="<?= old('iva_porcentaje', $cotizacion->iva_porcentaje ?? 19) ?>" min="0" max="100" step="0.01">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Porcentaje de IVA a aplicar
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
                        Detalles adicionales sobre el proyecto.
                    </p>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-align-left icon-label"></i>
                                    Descripción del Proyecto
                                </label>
                                <textarea name="descripcion" class="form-control" rows="4"><?= old('descripcion', $cotizacion->proyecto_descripcion ?? '') ?></textarea>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Descripción clara del alcance del proyecto (opcional)
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-ruler-combined icon-label"></i>
                                    Área del Proyecto (m²)
                                </label>
                                <input type="number" name="proyecto_area" class="form-control" 
                                       value="<?= old('proyecto_area', $cotizacion->proyecto_area ?? '') ?>" min="0" step="0.01" 
                                       placeholder="Ejemplo: 120.50">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Metros cuadrados del área (opcional)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-map-marked-alt icon-label"></i>
                                    Ubicación
                                </label>
                                <input type="text" name="proyecto_ubicacion" class="form-control" 
                                       value="<?= old('proyecto_ubicacion', $cotizacion->proyecto_ubicacion ?? '') ?>" 
                                       placeholder="Ejemplo: Santiago, Región Metropolitana">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Ciudad o comuna (opcional)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-map-marker-alt icon-label"></i>
                                    Dirección
                                </label>
                                <input type="text" name="proyecto_direccion" class="form-control" 
                                       value="<?= old('proyecto_direccion', $cotizacion->proyecto_direccion ?? '') ?>" 
                                       placeholder="Ejemplo: Av. Principal 123, Comuna">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Dirección exacta (opcional)
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
                        Gestione los items que componen la cotización.
                    </p>
                    
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" onclick="agregarItem()">
                            <i class="fas fa-plus me-2"></i> Agregar Item
                        </button>
                    </div>
                    
                    <div id="items-container">
                        <?php if (!empty($items)): ?>
                            <?php foreach ($items as $index => $item): ?>
                                <div class="item-row border p-3 mb-3" data-item="<?= $index + 1 ?>" style="border-radius: 10px; background: #f8f9fa;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0" style="color: #f0841a;"><i class="fas fa-cube me-2"></i> Item #<?= $index + 1 ?></h6>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="eliminarItem(<?= $index + 1 ?>)">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label class="form-label">Descripción <span class="required">*</span></label>
                                            <input type="text" name="items[<?= $index + 1 ?>][descripcion]" class="form-control" 
                                                   value="<?= esc($item->descripcion ?? '') ?>" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Cantidad <span class="required">*</span></label>
                                            <input type="number" name="items[<?= $index + 1 ?>][cantidad]" class="form-control cantidad" 
                                                   value="<?= $item->cantidad ?? 1 ?>" min="0" step="0.01" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Unidad <span class="required">*</span></label>
                                            <select name="items[<?= $index + 1 ?>][unidad]" class="form-control unidad-select" required>
                                                <option value="unidad" <?= ($item->unidad ?? 'unidad') == 'unidad' ? 'selected' : '' ?>>Unidad</option>
                                                <option value="m2" <?= ($item->unidad ?? '') == 'm2' ? 'selected' : '' ?>>m²</option>
                                                <option value="m3" <?= ($item->unidad ?? '') == 'm3' ? 'selected' : '' ?>>m³</option>
                                                <option value="ml" <?= ($item->unidad ?? '') == 'ml' ? 'selected' : '' ?>>ml</option>
                                                <option value="kg" <?= ($item->unidad ?? '') == 'kg' ? 'selected' : '' ?>>kg</option>
                                                <option value="ton" <?= ($item->unidad ?? '') == 'ton' ? 'selected' : '' ?>>ton</option>
                                                <option value="hr" <?= ($item->unidad ?? '') == 'hr' ? 'selected' : '' ?>>Hora</option>
                                                <option value="dia" <?= ($item->unidad ?? '') == 'dia' ? 'selected' : '' ?>>Día</option>
                                                <option value="semana" <?= ($item->unidad ?? '') == 'semana' ? 'selected' : '' ?>>Semana</option>
                                                <option value="mes" <?= ($item->unidad ?? '') == 'mes' ? 'selected' : '' ?>>Mes</option>
                                                <option value="jornada" <?= ($item->unidad ?? '') == 'jornada' ? 'selected' : '' ?>>Jornada</option>
                                                <option value="servicio" <?= ($item->unidad ?? '') == 'servicio' ? 'selected' : '' ?>>Servicio</option>
                                                <option value="otro" <?= ($item->unidad ?? '') == 'otro' ? 'selected' : '' ?>>Otro</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Precio Unitario</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="text" name="items[<?= $index + 1 ?>][precio_unitario]" class="form-control precio" 
                                                       value="<?= number_format($item->precio_unitario ?? 0, 0, ',', '.') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <label class="form-label">Tipo de Item</label>
                                            <select name="items[<?= $index + 1 ?>][categoria]" class="form-control">
                                                <option value="material" <?= ($item->categoria ?? '') == 'material' ? 'selected' : '' ?>>🧱 Material</option>
                                                <option value="mano_obra" <?= ($item->categoria ?? '') == 'mano_obra' ? 'selected' : '' ?>>👷 Mano de Obra</option>
                                                <option value="equipo" <?= ($item->categoria ?? '') == 'equipo' ? 'selected' : '' ?>>🚜 Equipo</option>
                                                <option value="servicio" <?= ($item->categoria ?? '') == 'servicio' ? 'selected' : '' ?>>⚙️ Servicio</option>
                                                <option value="transporte" <?= ($item->categoria ?? '') == 'transporte' ? 'selected' : '' ?>>🚚 Transporte</option>
                                                <option value="otros" <?= ($item->categoria ?? '') == 'otros' ? 'selected' : '' ?>>📦 Otros</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Subtipo</label>
                                            <div class="position-relative">
                                                <input type="text" name="items[<?= $index + 1 ?>][subcategoria]" class="form-control subtipo-input" 
                                                       value="<?= esc($item->subcategoria ?? '') ?>" placeholder="Escribir o seleccionar..." autocomplete="off">
                                                <div class="subtipo-suggestions" style="display: none;"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">¿Opcional?</label>
                                            <select name="items[<?= $index + 1 ?>][es_opcional]" class="form-control">
                                                <option value="0" <?= ($item->es_opcional ?? 0) == 0 ? 'selected' : '' ?>>No</option>
                                                <option value="1" <?= ($item->es_opcional ?? 0) == 1 ? 'selected' : '' ?>>Sí</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Subtotal</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="text" class="form-control subtotal" value="<?= number_format($item->subtotal ?? 0, 0, ',', '.') ?>" readonly style="background: #e9ecef; font-weight: 600;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Totales -->
                <div class="section-card" style="border-left-color: #f0841a;">
                    <div class="row">
                        <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <div class="card" style="border: 2px solid #f0841a;">
                                <div class="card-header" style="background: linear-gradient(135deg, #f0841a 0%, #e67e00 100%); color: white;">
                                    <h6 class="card-title mb-0"><i class="fas fa-calculator me-2"></i> Resumen de Totales</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-6"><strong>Subtotal:</strong></div>
                                        <div class="col-6 text-right">$<span id="subtotal"><?= number_format($cotizacion->subtotal ?? 0, 0, ',', '.') ?></span></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-6"><strong>Descuento:</strong></div>
                                        <div class="col-6 text-right">$<span id="descuento"><?= number_format($cotizacion->descuento_monto ?? 0, 0, ',', '.') ?></span></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-6"><strong>IVA (<span id="iva_porcentaje_display"><?= $cotizacion->iva_porcentaje ?? 19 ?></span>%):</strong></div>
                                        <div class="col-6 text-right">$<span id="iva"><?= number_format($cotizacion->iva_monto ?? 0, 0, ',', '.') ?></span></div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6"><strong style="font-size: 1.1rem;">TOTAL:</strong></div>
                                        <div class="col-6 text-right"><strong style="font-size: 1.1rem; color: #f0841a;">$<span id="total"><?= number_format($cotizacion->total_general ?? 0, 0, ',', '.') ?></span></strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campos ocultos para totales -->
                <input type="hidden" name="subtotal" id="hidden_subtotal" value="<?= $cotizacion->subtotal ?? 0 ?>">
                <input type="hidden" name="descuento_monto" id="hidden_descuento" value="<?= $cotizacion->descuento_monto ?? 0 ?>">
                <input type="hidden" name="iva_monto" id="hidden_iva" value="<?= $cotizacion->iva_monto ?? 0 ?>">
                <input type="hidden" name="total_general" id="hidden_total" value="<?= $cotizacion->total_general ?? 0 ?>">

                <!-- PASO 5: Condiciones y Observaciones -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">5</span>
                        <span><i class="fas fa-file-contract icon-label"></i> Condiciones y Observaciones</span>
                    </div>
                    <p class="section-subtitle">
                        Información adicional sobre condiciones de pago y observaciones.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-hand-holding-usd icon-label"></i>
                                    Condiciones de Pago
                                </label>
                                <textarea name="condiciones_pago" class="form-control" rows="3"><?= old('condiciones_pago', $cotizacion->condiciones_pago ?? '') ?></textarea>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Cómo y cuándo se realizarán los pagos (opcional)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-comment-dots icon-label"></i>
                                    Observaciones
                                </label>
                                <textarea name="observaciones" class="form-control" rows="3"><?= old('observaciones', $cotizacion->observaciones ?? '') ?></textarea>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Notas adicionales, exclusiones, garantías, etc. (opcional)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="section-card" style="border-left-color: #f39c12;">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex gap-3 justify-content-center">
                                <button type="submit" class="btn btn-primary btn-submit">
                                    <i class="fas fa-save me-2"></i> Actualizar Cotización
                                </button>
                                <a href="<?= base_url('dashboard/cotizacion/lista') ?>" class="btn btn-secondary btn-cancel">
                                    <i class="fas fa-times me-2"></i> Cancelar
                                </a>
                            </div>
                            <p class="text-center mt-3 mb-0 text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Revise todos los cambios antes de actualizar
                            </p>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
let itemCounter = <?= count($items ?? []) ?>;

function agregarItem() {
    itemCounter++;
    const container = document.getElementById('items-container');
    
    const itemHtml = `
        <div class="item-row border p-3 mb-3" data-item="${itemCounter}" style="border-radius: 10px; background: #f8f9fa;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0" style="color: #f0841a;"><i class="fas fa-cube me-2"></i> Item #${itemCounter}</h6>
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
    const tipoItemSelect = row.querySelector('select[name*="[categoria]"]');
    const unidadSelect = row.querySelector('.unidad-select');
    const subtipoInput = row.querySelector('.subtipo-input');
    
    cantidadInput.addEventListener('input', () => calcularSubtotal(row));
    precioInput.addEventListener('input', function() {
        formatearPrecioInput(this);
        calcularSubtotal(row);
    });
    
    if (tipoItemSelect && unidadSelect) {
        tipoItemSelect.addEventListener('change', function() {
            actualizarUnidadesPorTipo(this.value, unidadSelect);
        });
    }
    
    if (subtipoInput) {
        configurarAutocompletadoSubtipo(subtipoInput);
    }
}

function eliminarItem(itemId) {
    const item = document.querySelector(`[data-item="${itemId}"]`);
    if (item) {
        item.remove();
        calcularTotales();
    }
}

function calcularSubtotal(row) {
    const cantidadInput = row.querySelector('.cantidad');
    const precioInput = row.querySelector('.precio');
    const subtotalInput = row.querySelector('.subtotal');
    
    if (!cantidadInput || !precioInput || !subtotalInput) return;
    
    const cantidad = parseFloat(cantidadInput.value) || 0;
    const precio = parseFloat(precioInput.value.replace(/\./g, '').replace(',', '.')) || 0;
    const subtotal = cantidad * precio;
    
    subtotalInput.value = formatearNumero(subtotal);
    calcularTotales();
}

function calcularTotales() {
    let subtotal = 0;
    
    document.querySelectorAll('.subtotal').forEach(input => {
        subtotal += parseFloat(input.value.replace(/\./g, '').replace(',', '.')) || 0;
    });
    
    const ivaPorcentajeElement = document.querySelector('input[name="iva_porcentaje"]');
    const ivaPorcentaje = ivaPorcentajeElement ? parseFloat(ivaPorcentajeElement.value) || 0 : 0;
    
    const iva = subtotal * (ivaPorcentaje / 100);
    const total = subtotal + iva;
    
    // Actualizar display
    if (document.getElementById('subtotal')) document.getElementById('subtotal').textContent = formatearNumero(subtotal);
    if (document.getElementById('descuento')) document.getElementById('descuento').textContent = '0';
    if (document.getElementById('iva')) document.getElementById('iva').textContent = formatearNumero(iva);
    if (document.getElementById('total')) document.getElementById('total').textContent = formatearNumero(total);
    
    // Actualizar el porcentaje de IVA mostrado
    if (document.getElementById('iva_porcentaje_display')) {
        document.getElementById('iva_porcentaje_display').textContent = ivaPorcentaje;
    }
    
    // Actualizar campos ocultos
    if (document.getElementById('hidden_subtotal')) document.getElementById('hidden_subtotal').value = subtotal;
    if (document.getElementById('hidden_descuento')) document.getElementById('hidden_descuento').value = 0;
    if (document.getElementById('hidden_iva')) document.getElementById('hidden_iva').value = iva;
    if (document.getElementById('hidden_total')) document.getElementById('hidden_total').value = total;
}

// Lista de subtipos de construcción
const subtiposConstruccion = [
    'Cemento', 'Hormigón', 'Ladrillos', 'Bloques', 'Arena', 'Gravilla', 'Piedra', 'Ripio',
    'Acero', 'Hierro', 'Alambre', 'Malla', 'Tornillos', 'Pernos', 'Tuercas', 'Clavos',
    'Madera', 'Tableros', 'Vigas', 'Listones', 'Molduras', 'Pisos', 'Parrillas',
    'Pintura', 'Barniz', 'Laca', 'Primer', 'Sellador', 'Masilla', 'Pegamento',
    'Cerámica', 'Porcelanato', 'Mármol', 'Granito', 'Cuarzo', 'Piedra Laja',
    'Vidrio', 'Espejo', 'Aluminio', 'PVC', 'Poliestireno', 'Aislante',
    'Tuberías', 'Válvulas', 'Grifería', 'Sanitarios', 'Duchas', 'Bañeras',
    'Cables', 'Conductores', 'Interruptores', 'Tomas', 'Luminarias', 'Ventiladores',
    'Albañilería', 'Hormigón Armado', 'Enfierrado', 'Encofrado', 'Demolición',
    'Excavación', 'Relleno', 'Compactación', 'Nivelación', 'Replanteo',
    'Instalación Eléctrica', 'Instalación Sanitaria', 'Instalación de Gas',
    'Instalación de Agua', 'Instalación de Cloacas', 'Instalación de Calefacción',
    'Pintura Interior', 'Pintura Exterior', 'Empapelado', 'Texturizado',
    'Carpintería', 'Herrería', 'Soldadura', 'Pulido', 'Lijado', 'Barnizado',
    'Instalación de Pisos', 'Instalación de Cerámicas', 'Instalación de Ventanas',
    'Instalación de Puertas', 'Instalación de Techos', 'Instalación de Cielorrasos',
    'Excavadora', 'Retroexcavadora', 'Bulldozer', 'Motoconformadora', 'Rodillo',
    'Grúa', 'Pluma', 'Andamio', 'Escalera', 'Carretilla', 'Vibrador',
    'Mezcladora', 'Hormigonera', 'Bomba de Hormigón', 'Compresor', 'Martillo',
    'Taladro', 'Soldadora', 'Pulidora', 'Lijadora', 'Pistola de Pintura', 'Nivel Láser',
    'Medidor', 'Camión', 'Volquete', 'Plataforma'
];

function configurarAutocompletadoSubtipo(input) {
    const container = input.closest('.position-relative');
    const suggestions = container.querySelector('.subtipo-suggestions');
    
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
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    `;
    
    input.addEventListener('input', function() {
        const valor = this.value.toLowerCase();
        if (valor.length < 2) {
            suggestions.style.display = 'none';
            return;
        }
        
        const coincidencias = subtiposConstruccion.filter(subtipo => 
            subtipo.toLowerCase().includes(valor)
        );
        
        if (coincidencias.length > 0) {
            suggestions.innerHTML = coincidencias.map(subtipo => 
                `<div class="suggestion-item" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;" 
                       onmouseover="this.style.backgroundColor='#f5f5f5'" 
                       onmouseout="this.style.backgroundColor='white'"
                       onclick="seleccionarSubtipo(this, '${input.name}')">${subtipo}</div>`
            ).join('');
            suggestions.style.display = 'block';
        } else {
            suggestions.style.display = 'none';
        }
    });
    
    document.addEventListener('click', function(e) {
        if (!container.contains(e.target)) {
            suggestions.style.display = 'none';
        }
    });
}

function seleccionarSubtipo(elemento, inputName) {
    const input = document.querySelector(`input[name="${inputName}"]`);
    const container = input.closest('.position-relative');
    const suggestions = container.querySelector('.subtipo-suggestions');
    
    input.value = elemento.textContent;
    suggestions.style.display = 'none';
}

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
            {value: 'mes', text: 'Mes'}
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
    
    unidadSelect.innerHTML = '';
    
    const opcionesTipo = opciones[tipoItem] || opciones['otro'];
    opcionesTipo.forEach(opcion => {
        const option = document.createElement('option');
        option.value = opcion.value;
        option.textContent = opcion.text;
        unidadSelect.appendChild(option);
    });
}

function formatearNumero(numero) {
    return new Intl.NumberFormat('es-CL', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    }).format(numero);
}

function formatearPrecioInput(input) {
    let valor = input.value.replace(/[^\d,]/g, '');
    if (valor) {
        let numero = parseFloat(valor.replace(',', '.'));
        if (!isNaN(numero)) {
            input.value = formatearNumero(numero);
        }
    }
}

// Event listeners para cálculo automático de totales
document.addEventListener('DOMContentLoaded', function() {
    const ivaInput = document.querySelector('input[name="iva_porcentaje"]');
    
    if (ivaInput) {
        ivaInput.addEventListener('input', calcularTotales);
    }
    
    // Configurar event listeners para items existentes
    document.querySelectorAll('.precio').forEach(precioInput => {
        precioInput.addEventListener('input', function() {
            formatearPrecioInput(this);
            const row = this.closest('.item-row');
            if (row) {
                calcularSubtotal(row);
            }
        });
    });
    
    document.querySelectorAll('.cantidad').forEach(cantidadInput => {
        cantidadInput.addEventListener('input', function() {
            const row = this.closest('.item-row');
            if (row) {
                calcularSubtotal(row);
            }
        });
    });
    
    document.querySelectorAll('.subtipo-input').forEach(subtipoInput => {
        configurarAutocompletadoSubtipo(subtipoInput);
    });
    
    // Calcular totales iniciales
    setTimeout(() => {
        calcularTotales();
    }, 100);
});
</script>

<?= $this->endSection() ?>

<?= $this->extend('layout/dashboard') ?>

<?= $this->section('cotizacion/registro') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-invoice-dollar"></i> Nueva Cotización
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/cotizacion/lista') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
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
                        
                        <!-- Información Básica -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Cliente *</label>
                                    <select name="cliente_id" id="cliente_id" class="form-control" required>
                                        <option value="">Seleccionar cliente</option>
                                        <?php foreach ($clientes as $cliente): ?>
                                            <option value="<?= $cliente->id ?>" 
                                                    data-tipo="<?= $cliente->tipo_cliente ?>"
                                                    data-contacto="<?= esc($cliente->contacto_completo) ?>"
                                                    <?= (old('cliente_id') == $cliente->id || (isset($clienteSeleccionado) && $clienteSeleccionado && $clienteSeleccionado->id == $cliente->id)) ? 'selected' : '' ?>>
                                                <?= esc($cliente->display_name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (session()->getFlashdata('errors')['cliente_id'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['cliente_id']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Título de la Cotización *</label>
                                    <input type="text" name="titulo" class="form-control" 
                                           value="<?= old('titulo') ?>" required placeholder="Ej: Construcción Casa Residencial">
                                    <?php if (session()->getFlashdata('errors')['titulo'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['titulo']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Fecha de Cotización *</label>
                                    <input type="date" name="fecha_cotizacion" class="form-control" 
                                           value="<?= old('fecha_cotizacion', date('Y-m-d')) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Válida hasta</label>
                                    <input type="date" name="fecha_validez" class="form-control" 
                                           value="<?= old('fecha_validez', date('Y-m-d', strtotime('+30 days'))) ?>">
                                    <small class="text-muted">Por defecto 30 días desde hoy</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Estado *</label>
                                    <select name="estado_cotizacion" class="form-control" required>
                                        <option value="borrador" <?= old('estado_cotizacion') == 'borrador' ? 'selected' : '' ?>>Borrador</option>
                                        <option value="enviada" <?= old('estado_cotizacion') == 'enviada' ? 'selected' : '' ?>>Enviada</option>
                                        <option value="aceptada" <?= old('estado_cotizacion') == 'aceptada' ? 'selected' : '' ?>>Aceptada</option>
                                        <option value="rechazada" <?= old('estado_cotizacion') == 'rechazada' ? 'selected' : '' ?>>Rechazada</option>
                                        <option value="expirada" <?= old('estado_cotizacion') == 'expirada' ? 'selected' : '' ?>>Expirada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Prioridad *</label>
                                    <select name="prioridad" class="form-control" required>
                                        <option value="baja" <?= old('prioridad') == 'baja' ? 'selected' : '' ?>>Baja</option>
                                        <option value="media" <?= old('prioridad') == 'media' ? 'selected' : '' ?>>Media</option>
                                        <option value="alta" <?= old('prioridad') == 'alta' ? 'selected' : '' ?>>Alta</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">IVA (%)</label>
                                    <input type="number" name="iva_porcentaje" id="iva_porcentaje" class="form-control" 
                                           value="<?= old('iva_porcentaje', 19) ?>" min="0" max="100" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Descuento (%)</label>
                                    <input type="number" name="descuento_porcentaje" id="descuento_porcentaje" class="form-control" 
                                           value="<?= old('descuento_porcentaje', 0) ?>" min="0" max="100" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Descuento ($)</label>
                                    <input type="text" name="descuento_monto" id="descuento_monto" class="form-control" 
                                           value="<?= old('descuento_monto', 0) ?>" placeholder="0">
                                </div>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Descripción del Proyecto</label>
                                    <textarea name="descripcion" class="form-control" rows="4" 
                                              placeholder="Describe detalladamente el proyecto a cotizar"><?= old('descripcion') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Proyecto -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tipo de Proyecto *</label>
                                    <select name="proyecto_tipo" class="form-control" required>
                                        <option value="">Seleccionar tipo...</option>
                                        <option value="residencial" <?= old('proyecto_tipo') == 'residencial' ? 'selected' : '' ?>>Residencial</option>
                                        <option value="comercial" <?= old('proyecto_tipo') == 'comercial' ? 'selected' : '' ?>>Comercial</option>
                                        <option value="industrial" <?= old('proyecto_tipo') == 'industrial' ? 'selected' : '' ?>>Industrial</option>
                                        <option value="institucional" <?= old('proyecto_tipo') == 'institucional' ? 'selected' : '' ?>>Institucional</option>
                                        <option value="infraestructura" <?= old('proyecto_tipo') == 'infraestructura' ? 'selected' : '' ?>>Infraestructura</option>
                                        <option value="remodelacion" <?= old('proyecto_tipo') == 'remodelacion' ? 'selected' : '' ?>>Remodelación</option>
                                        <option value="ampliacion" <?= old('proyecto_tipo') == 'ampliacion' ? 'selected' : '' ?>>Ampliación</option>
                                        <option value="mantenimiento" <?= old('proyecto_tipo') == 'mantenimiento' ? 'selected' : '' ?>>Mantenimiento</option>
                                        <option value="reparacion" <?= old('proyecto_tipo') == 'reparacion' ? 'selected' : '' ?>>Reparación</option>
                                        <option value="otros" <?= old('proyecto_tipo') == 'otros' ? 'selected' : '' ?>>Otros</option>
                                    </select>
                                    <?php if (session()->getFlashdata('errors')['proyecto_tipo'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['proyecto_tipo']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Área del Proyecto (m²)</label>
                                    <input type="number" name="proyecto_area" class="form-control" 
                                           value="<?= old('proyecto_area') ?>" min="0" step="0.01" placeholder="Ej: 120.50">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Ubicación del Proyecto</label>
                                    <input type="text" name="proyecto_ubicacion" class="form-control" 
                                           value="<?= old('proyecto_ubicacion') ?>" placeholder="Ej: Santiago, Región Metropolitana">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Dirección del Proyecto</label>
                                    <input type="text" name="proyecto_direccion" class="form-control" 
                                           value="<?= old('proyecto_direccion') ?>" placeholder="Ej: Av. Principal 123, Comuna">
                                </div>
                            </div>
                        </div>

                        <!-- Items de la Cotización -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">
                                            <i class="fas fa-list"></i> Items de la Cotización
                                        </h5>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="agregarItem()">
                                            <i class="fas fa-plus"></i> Agregar Item
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="items-container">
                                            <!-- Los items se agregarán dinámicamente -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Totales -->
                        <div class="row">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title">Resumen de Totales</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6"><strong>Subtotal:</strong></div>
                                            <div class="col-6 text-right">$<span id="subtotal">0</span></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6"><strong>Descuento:</strong></div>
                                            <div class="col-6 text-right">$<span id="descuento">0</span> <span id="descuento_detalle" class="text-muted small"></span></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6"><strong>IVA:</strong></div>
                                            <div class="col-6 text-right">$<span id="iva">0</span></div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-6"><strong>TOTAL:</strong></div>
                                            <div class="col-6 text-right"><strong>$<span id="total">0</span></strong></div>
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

                        <!-- Condiciones y Observaciones -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Condiciones de Pago</label>
                                    <textarea name="condiciones_pago" class="form-control" rows="3" 
                                              placeholder="Ej: 30% anticipo, 40% al 50% de avance, 30% al finalizar"><?= old('condiciones_pago') ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Observaciones</label>
                                    <textarea name="observaciones" class="form-control" rows="3" 
                                              placeholder="Información adicional"><?= old('observaciones') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Cotización
                                </button>
                                <a href="<?= base_url('dashboard/cotizacion/lista') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let itemCounter = 0;

function agregarItem() {
    itemCounter++;
    const container = document.getElementById('items-container');
    
    const itemHtml = `
        <div class="item-row border p-3 mb-3" data-item="${itemCounter}">
            <div class="row">
                <div class="col-md-1">
                    <label class="form-label">#</label>
                    <input type="text" class="form-control" value="${itemCounter}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Descripción</label>
                    <input type="text" name="items[${itemCounter}][descripcion]" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Cantidad</label>
                    <input type="number" name="items[${itemCounter}][cantidad]" class="form-control cantidad" min="0" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Unidad</label>
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
                <div class="col-md-2">
                    <label class="form-label">Precio Unit.</label>
                    <input type="text" name="items[${itemCounter}][precio_unitario]" class="form-control precio" min="0" step="0.01" required placeholder="0">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Subtotal</label>
                    <input type="text" class="form-control subtotal" readonly>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-3">
                    <label class="form-label">Tipo de Item</label>
                    <select name="items[${itemCounter}][tipo_item]" class="form-control">
                        <option value="material">Material</option>
                        <option value="mano_obra">Mano de Obra</option>
                        <option value="equipo">Equipo</option>
                        <option value="servicio">Servicio</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Subtipo</label>
                    <div class="position-relative">
                        <input type="text" name="items[${itemCounter}][subtipo_item]" class="form-control subtipo-input" 
                               placeholder="Escribir o seleccionar..." autocomplete="off">
                        <div class="subtipo-suggestions" style="display: none;"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Opcional</label>
                    <select name="items[${itemCounter}][es_opcional]" class="form-control">
                        <option value="0">No</option>
                        <option value="1">Sí</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-block" onclick="eliminarItem(${itemCounter})">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
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
    const tipoItemSelect = row.querySelector('select[name*="[tipo_item]"]');
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

<?= $this->extend('layout/dashboard') ?>

<?= $this->section('cotizacion/editar') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Editar Cotización: <?= esc($cotizacion->numero_cotizacion) ?>
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/cotizacion/lista') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('dashboard/cotizacion/update') ?>" method="post" id="formCotizacion">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $cotizacion->id ?>">
                        
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
                                                    data-contacto="<?= esc($cliente->contacto_completo ?? '') ?>"
                                                    <?= old('cliente_id', $cotizacion->cliente_id) == $cliente->id ? 'selected' : '' ?>>
                                                <?= esc($cliente->display_name ?? $cliente->nombre_razon_social) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Título de la Cotización *</label>
                                    <input type="text" name="titulo" class="form-control" 
                                           value="<?= old('titulo', $cotizacion->proyecto_nombre ?? $cotizacion->titulo ?? '') ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Fecha de Cotización *</label>
                                    <input type="date" name="fecha_cotizacion" class="form-control" 
                                           value="<?= old('fecha_cotizacion', $cotizacion->fecha_cotizacion) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Válida hasta</label>
                                    <input type="date" name="fecha_validez" class="form-control" 
                                           value="<?= old('fecha_validez', $cotizacion->fecha_validez) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Estado *</label>
                                    <select name="estado_cotizacion" class="form-control" required>
                                        <option value="borrador" <?= old('estado_cotizacion', $cotizacion->estado ?? 'borrador') == 'borrador' ? 'selected' : '' ?>>Borrador</option>
                                        <option value="enviada" <?= old('estado_cotizacion', $cotizacion->estado ?? 'borrador') == 'enviada' ? 'selected' : '' ?>>Enviada</option>
                                        <option value="aceptada" <?= old('estado_cotizacion', $cotizacion->estado ?? 'borrador') == 'aceptada' ? 'selected' : '' ?>>Aceptada</option>
                                        <option value="rechazada" <?= old('estado_cotizacion', $cotizacion->estado ?? 'borrador') == 'rechazada' ? 'selected' : '' ?>>Rechazada</option>
                                        <option value="expirada" <?= old('estado_cotizacion', $cotizacion->estado ?? 'borrador') == 'expirada' ? 'selected' : '' ?>>Expirada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Prioridad *</label>
                                    <select name="prioridad" class="form-control" required>
                                        <option value="baja" <?= old('prioridad', $cotizacion->prioridad ?? 'media') == 'baja' ? 'selected' : '' ?>>Baja</option>
                                        <option value="media" <?= old('prioridad', $cotizacion->prioridad ?? 'media') == 'media' ? 'selected' : '' ?>>Media</option>
                                        <option value="alta" <?= old('prioridad', $cotizacion->prioridad ?? 'media') == 'alta' ? 'selected' : '' ?>>Alta</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">IVA (%)</label>
                                    <input type="number" name="iva_porcentaje" class="form-control" 
                                           value="<?= old('iva_porcentaje', $cotizacion->iva_porcentaje ?? 19) ?>" min="0" max="100" step="0.01">
                                </div>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Descripción del Proyecto</label>
                                    <textarea name="descripcion" class="form-control" rows="4"><?= old('descripcion', $cotizacion->proyecto_descripcion ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Espacio entre secciones -->
                        <div class="row">
                            <div class="col-12">
                                <hr class="my-4">
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
                                            <?php if (!empty($items)): ?>
                                                <?php foreach ($items as $index => $item): ?>
                                                    <div class="item-row border p-3 mb-3" data-item="<?= $index + 1 ?>">
                                                        <div class="row">
                                                            <div class="col-md-1">
                                                                <label class="form-label">#</label>
                                                                <input type="text" class="form-control" value="<?= $index + 1 ?>" readonly>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label">Descripción</label>
                                                                <input type="text" name="items[<?= $index + 1 ?>][descripcion]" class="form-control" 
                                                                       value="<?= esc($item->descripcion ?? '') ?>" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="form-label">Cantidad</label>
                                                                <input type="number" name="items[<?= $index + 1 ?>][cantidad]" class="form-control cantidad" 
                                                                       value="<?= $item->cantidad ?? 1 ?>" min="0" step="0.01" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="form-label">Unidad</label>
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
                                                            <div class="col-md-2">
                                                                <label class="form-label">Precio Unit.</label>
                                                                <input type="number" name="items[<?= $index + 1 ?>][precio_unitario]" class="form-control precio" 
                                                                       value="<?= $item->precio_unitario ?? 0 ?>" min="0" step="0.01" required>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <label class="form-label">Subtotal</label>
                                                                <input type="text" class="form-control subtotal" value="<?= $item->subtotal ?? 0 ?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-md-3">
                                                                <label class="form-label">Tipo de Item</label>
                                                                <select name="items[<?= $index + 1 ?>][tipo_item]" class="form-control">
                                                                    <option value="material" <?= ($item->tipo_item ?? 'material') == 'material' ? 'selected' : '' ?>>Material</option>
                                                                    <option value="mano_obra" <?= ($item->tipo_item ?? 'material') == 'mano_obra' ? 'selected' : '' ?>>Mano de Obra</option>
                                                                    <option value="equipo" <?= ($item->tipo_item ?? 'material') == 'equipo' ? 'selected' : '' ?>>Equipo</option>
                                                                    <option value="servicio" <?= ($item->tipo_item ?? 'material') == 'servicio' ? 'selected' : '' ?>>Servicio</option>
                                                                    <option value="otro" <?= ($item->tipo_item ?? 'material') == 'otro' ? 'selected' : '' ?>>Otro</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label">Subtipo</label>
                                                                <div class="position-relative">
                                                                    <input type="text" name="items[<?= $index + 1 ?>][subtipo_item]" class="form-control subtipo-input" 
                                                                           value="<?= esc($item->subtipo_item ?? '') ?>" placeholder="Escribir o seleccionar..." autocomplete="off">
                                                                    <div class="subtipo-suggestions" style="display: none;"></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label">Opcional</label>
                                                                <select name="items[<?= $index + 1 ?>][es_opcional]" class="form-control">
                                                                    <option value="0" <?= ($item->es_opcional ?? 0) == 0 ? 'selected' : '' ?>>No</option>
                                                                    <option value="1" <?= ($item->es_opcional ?? 0) == 1 ? 'selected' : '' ?>>Sí</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label">&nbsp;</label>
                                                                <button type="button" class="btn btn-danger btn-block" onclick="eliminarItem(<?= $index + 1 ?>)">
                                                                    <i class="fas fa-trash"></i> Eliminar
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
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
                                            <div class="col-6 text-right">$<span id="subtotal"><?= number_format($cotizacion->subtotal ?? 0, 0, ',', '.') ?></span></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6"><strong>Descuento:</strong></div>
                                            <div class="col-6 text-right">$<span id="descuento"><?= number_format($cotizacion->descuento_monto ?? 0, 0, ',', '.') ?></span></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6"><strong>IVA:</strong></div>
                                            <div class="col-6 text-right">$<span id="iva"><?= number_format($cotizacion->iva_monto ?? 0, 0, ',', '.') ?></span></div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-6"><strong>TOTAL:</strong></div>
                                            <div class="col-6 text-right"><strong>$<span id="total"><?= number_format($cotizacion->total_general ?? 0, 0, ',', '.') ?></span></strong></div>
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

                        <!-- Condiciones y Observaciones -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Condiciones de Pago</label>
                                    <textarea name="condiciones_pago" class="form-control" rows="3"><?= old('condiciones_pago', $cotizacion->condiciones_pago ?? '') ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Observaciones</label>
                                    <textarea name="observaciones" class="form-control" rows="3"><?= old('observaciones', $cotizacion->observaciones ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Actualizar Cotización
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
let itemCounter = <?= count($items) ?>;

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
                        <option value="servicio">Servicio</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Precio Unit.</label>
                    <input type="number" name="items[${itemCounter}][precio_unitario]" class="form-control precio" min="0" step="0.01" required>
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
    const tipoItemSelect = row.querySelector('select[name*="[tipo_item]"]');
    const unidadSelect = row.querySelector('.unidad-select');
    const subtipoInput = row.querySelector('.subtipo-input');
    
    cantidadInput.addEventListener('input', calcularSubtotal);
    precioInput.addEventListener('input', calcularSubtotal);
    
    // Configurar funcionalidades dinámicas
    if (tipoItemSelect && unidadSelect) {
        tipoItemSelect.addEventListener('change', function() {
            actualizarUnidadesPorTipo(this.value, unidadSelect);
        });
    }
    
    if (subtipoInput) {
        configurarAutocompletadoSubtipo(subtipoInput);
    }
    
    function calcularSubtotal() {
        const cantidad = parseFloat(cantidadInput.value) || 0;
        const precio = parseFloat(precioInput.value) || 0;
        const subtotal = cantidad * precio;
        
        subtotalInput.value = subtotal.toFixed(2);
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
        subtotal += parseFloat(input.value) || 0;
    });
    
    const ivaPorcentaje = parseFloat(document.querySelector('input[name="iva_porcentaje"]').value) || 19;
    const descuento = parseFloat(document.getElementById('hidden_descuento').value) || 0;
    
    const iva = subtotal * (ivaPorcentaje / 100);
    const total = subtotal - descuento + iva;
    
    // Actualizar display
    document.getElementById('subtotal').textContent = subtotal.toLocaleString();
    document.getElementById('descuento').textContent = descuento.toLocaleString();
    document.getElementById('iva').textContent = iva.toLocaleString();
    document.getElementById('total').textContent = total.toLocaleString();
    
    // Actualizar campos ocultos
    document.getElementById('hidden_subtotal').value = subtotal;
    document.getElementById('hidden_iva').value = iva;
    document.getElementById('hidden_total').value = total;
}

// Agregar event listeners a los items existentes
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.cantidad, .precio').forEach(input => {
        input.addEventListener('input', calcularTotales);
    });
    
    // Configurar funcionalidades para items existentes
    document.querySelectorAll('.item-row').forEach(row => {
        const tipoItemSelect = row.querySelector('select[name*="[tipo_item]"]');
        const unidadSelect = row.querySelector('.unidad-select');
        const subtipoInput = row.querySelector('.subtipo-input');
        
        if (tipoItemSelect && unidadSelect) {
            tipoItemSelect.addEventListener('change', function() {
                actualizarUnidadesPorTipo(this.value, unidadSelect);
            });
        }
        
        if (subtipoInput) {
            configurarAutocompletadoSubtipo(subtipoInput);
        }
    });
});

// Lista de subtipos de construcción
const subtiposConstruccion = [
    // Materiales
    'Cemento', 'Hormigón', 'Ladrillos', 'Bloques', 'Arena', 'Gravilla', 'Piedra', 'Ripio',
    'Acero', 'Hierro', 'Alambre', 'Malla', 'Tornillos', 'Pernos', 'Tuercas', 'Clavos',
    'Madera', 'Tableros', 'Vigas', 'Listones', 'Molduras', 'Pisos', 'Parrillas',
    'Pintura', 'Barniz', 'Laca', 'Primer', 'Sellador', 'Masilla', 'Pegamento',
    'Cerámica', 'Porcelanato', 'Mármol', 'Granito', 'Cuarzo', 'Piedra Laja',
    'Vidrio', 'Espejo', 'Aluminio', 'PVC', 'Poliestireno', 'Aislante',
    'Tuberías', 'Válvulas', 'Grifería', 'Sanitarios', 'Duchas', 'Bañeras',
    'Cables', 'Conductores', 'Interruptores', 'Tomas', 'Luminarias', 'Ventiladores',
    
    // Mano de Obra
    'Albañilería', 'Hormigón Armado', 'Enfierrado', 'Encofrado', 'Demolición',
    'Excavación', 'Relleno', 'Compactación', 'Nivelación', 'Replanteo',
    'Instalación Eléctrica', 'Instalación Sanitaria', 'Instalación de Gas',
    'Instalación de Agua', 'Instalación de Cloacas', 'Instalación de Calefacción',
    'Pintura Interior', 'Pintura Exterior', 'Empapelado', 'Texturizado',
    'Carpintería', 'Herrería', 'Soldadura', 'Pulido', 'Lijado', 'Barnizado',
    'Instalación de Pisos', 'Instalación de Cerámicas', 'Instalación de Ventanas',
    'Instalación de Puertas', 'Instalación de Techos', 'Instalación de Cielorrasos',
    
    // Equipos
    'Excavadora', 'Retroexcavadora', 'Bulldozer', 'Motoconformadora', 'Rodillo',
    'Grúa', 'Pluma', 'Andamio', 'Escalera', 'Carretilla', 'Vibrador',
    'Mezcladora', 'Hormigonera', 'Bomba de Hormigón', 'Compresor', 'Martillo',
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
    
    // Ocultar sugerencias al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!container.contains(e.target)) {
            suggestions.style.display = 'none';
        }
    });
}

// Función para seleccionar un subtipo
function seleccionarSubtipo(elemento, inputName) {
    const input = document.querySelector(`input[name="${inputName}"]`);
    const container = input.closest('.position-relative');
    const suggestions = container.querySelector('.subtipo-suggestions');
    
    input.value = elemento.textContent;
    suggestions.style.display = 'none';
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
    
    // Limpiar opciones actuales
    unidadSelect.innerHTML = '';
    
    // Agregar nuevas opciones
    const opcionesTipo = opciones[tipoItem] || opciones['otro'];
    opcionesTipo.forEach(opcion => {
        const option = document.createElement('option');
        option.value = opcion.value;
        option.textContent = opcion.text;
        unidadSelect.appendChild(option);
    });
}
</script>

<?= $this->endSection() ?>

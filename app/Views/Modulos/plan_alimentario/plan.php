<div id="planAlimentarioSection">
    <input type="hidden" id="detalle_agenda_id_plan" value="<?= $cita->id ?? '' ?>">
    
    <style>
        #planAlimentarioSection .plan-row-aligned { align-items: flex-end; }
        #planAlimentarioSection .plan-badges {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
            padding-left: 0;
        }
        #planAlimentarioSection .bloque-gramos-content { max-width: 100%; }
        #planAlimentarioSection .bloque-gramos-content .row.g-2 { align-items: flex-end; }
        #planAlimentarioSection .bloque-gramos-content .form-label.small { font-size: 0.8rem; white-space: nowrap; }
        #planAlimentarioSection .macros-cards { margin-top: 0.35rem; margin-bottom: 0; }
        #planAlimentarioSection .macros-cards .card { height: 100%; }
        #planAlimentarioSection .macros-cards .card-body { display: flex; flex-direction: column; justify-content: center; min-height: 80px; }
        #planAlimentarioSection .distribucion-label-row { display: flex; align-items: center; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.5rem; }
        #planAlimentarioSection .text-muted.mt-1 { margin-top: 0.5rem !important; }
        #planAlimentarioSection .align-badges-gramos { align-items: baseline; }
    </style>
    <!-- Formulario de Plan Alimentario (o div si está embebido en form padre, ej. historial/editar) -->
    <?php if (!empty($embebidoEnForm)): ?><div id="formPlanAlimentario"><?php else: ?><form id="formPlanAlimentario"><?php endif; ?>
        <!-- Requerimiento Energético (arriba, ancho completo) -->
        <div class="row mb-3">
            <div class="col-12 col-md-5 col-lg-4">
                <label class="form-label">Requerimiento Energético (kcal) <span class="text-danger">*</span></label>
                <input type="number" step="0.1" class="form-control" id="plan_requerimiento_kcal" name="requerimiento_kcal" required>
                <small class="text-muted d-block mt-1">Si hay calorimetría y no hay plan guardado, se pre-llenará automáticamente</small>
            </div>
        </div>

        <!-- Distribución Calórica (debajo del requerimiento) -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="distribucion-label-row">
                    <label class="form-label mb-0">Distribución Calórica <span class="text-danger">*</span></label>
                    <div class="btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="modo_distribucion" id="modo_porcentaje" value="porcentaje" checked>
                        <label class="btn btn-outline-primary" for="modo_porcentaje">Por %</label>
                        <input type="radio" class="btn-check" name="modo_distribucion" id="modo_gramos" value="gramos">
                        <label class="btn btn-outline-primary" for="modo_gramos">Por gramos</label>
                    </div>
                </div>
                <!-- Bloque: ingresar por porcentaje -->
                <div id="bloque_porcentaje">
                    <div class="row g-2" style="max-width: 400px;">
                        <div class="col-4">
                            <label class="form-label small mb-1">Proteínas (%)</label>
                            <input type="number" step="0.1" class="form-control" id="plan_prot_porcentaje" name="prot_porcentaje" placeholder="%" value="20" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label small mb-1">Grasas (%)</label>
                            <input type="number" step="0.1" class="form-control" id="plan_grasa_porcentaje" name="grasa_porcentaje" placeholder="%" value="30" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label small mb-1">CHO (%)</label>
                            <input type="number" step="0.1" class="form-control" id="plan_cho_porcentaje" name="cho_porcentaje" placeholder="%" value="50" required>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-1">Debe sumar 100%</small>
                    <div class="plan-badges">
                        <span id="suma_distribucion" class="badge bg-secondary">Total: 100%</span>
                    </div>
                </div>
                <!-- Bloque: ingresar por gramos -->
                <div id="bloque_gramos" class="bloque-gramos-content" style="display: none;">
                    <div class="row g-2" style="max-width: 500px;">
                        <div class="col-4">
                            <label class="form-label small mb-1">Proteínas (g)</label>
                            <input type="number" step="0.1" class="form-control" id="plan_prot_gramos_input" name="prot_gramos" placeholder="g" min="0" value="">
                        </div>
                        <div class="col-4">
                            <label class="form-label small mb-1">Grasas (g)</label>
                            <input type="number" step="0.1" class="form-control" id="plan_grasa_gramos_input" name="grasa_gramos" placeholder="g" min="0" value="">
                        </div>
                        <div class="col-4">
                            <label class="form-label small mb-1">CHO (g)</label>
                            <input type="number" step="0.1" class="form-control" id="plan_cho_gramos_input" name="cho_gramos" placeholder="g" min="0" value="">
                        </div>
                    </div>
                    <small class="text-muted d-block mt-1">Las calorías de los macros deben coincidir con el requerimiento (Prot×4 + Grasa×9 + CHO×4 = kcal)</small>
                    <div class="plan-badges align-badges-gramos">
                        <span id="suma_gramos_info" class="badge bg-secondary">Calorías macros: 0 kcal</span>
                        <span id="equivale_porcentaje" class="badge bg-secondary">Equivale: - % · - % · - %</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Macros Objetivo (altiro abajo de la distribución: Proteínas, Grasas, CHO en gramos) -->
        <div class="row g-2 macros-cards mt-1 mb-3" id="macrosObjetivo" style="display: none;">
            <div class="col-12 col-sm-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <small class="d-block opacity-75">Proteínas (g)</small>
                        <h5 class="mb-0 mt-1" id="plan_prot_gramos">0</h5>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <small class="d-block opacity-75">Grasas (g)</small>
                        <h5 class="mb-0 mt-1" id="plan_grasa_gramos">0</h5>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <small class="d-block opacity-75">CHO (g)</small>
                        <h5 class="mb-0 mt-1" id="plan_cho_gramos">0</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Porciones por Grupo -->
        <div class="mb-3">
            <h6 class="text-info mb-3"><i class="fas fa-list me-2"></i> Porciones por Grupo Alimentario</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered" id="tablaPorciones">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 30%;">Grupo</th>
                            <th style="width: 15%;">Porciones</th>
                            <th style="width: 13%;">Calorías</th>
                            <th style="width: 13%;">CHO (g)</th>
                            <th style="width: 13%;">Grasa (g)</th>
                            <th style="width: 13%;">Prot (g)</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyPorciones">
                        <!-- Se llenará con JavaScript -->
                    </tbody>
                    <tfoot class="table-info">
                        <tr>
                            <th>Totales</th>
                            <th id="total_porciones">0</th>
                            <th id="total_kcal_plan">0</th>
                            <th id="total_cho_plan">0</th>
                            <th id="total_grasa_plan">0</th>
                            <th id="total_prot_plan">0</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Adecuación -->
        <div class="row mb-3" id="adecuacionPlan" style="display: none;">
            <div class="col-md-12">
                <h6 class="text-success mb-1"><i class="fas fa-chart-pie me-2"></i> Adecuación del Plan</h6>
                <p class="text-muted small mb-2" style="font-size: 0.8rem;">
                    <strong>% Adecuación = Aporte del plan ÷ Objetivo × 100</strong> (cuánto cubre el plan respecto a la meta), criterio habitual de adecuación dietaria.
                </p>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Nutriente</th>
                                <th>Objetivo</th>
                                <th>Plan</th>
                                <th>Adecuación</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Calorías</strong></td>
                                <td id="obj_kcal">0</td>
                                <td id="plan_kcal">0</td>
                                <td id="adec_kcal_porc">0%</td>
                                <td id="estado_kcal"><span class="badge bg-secondary">-</span></td>
                            </tr>
                            <tr>
                                <td><strong>CHO</strong></td>
                                <td id="obj_cho">0 g</td>
                                <td id="plan_cho">0 g</td>
                                <td id="adec_cho_porc">0%</td>
                                <td id="estado_cho"><span class="badge bg-secondary">-</span></td>
                            </tr>
                            <tr>
                                <td><strong>Grasas</strong></td>
                                <td id="obj_grasa">0 g</td>
                                <td id="plan_grasa">0 g</td>
                                <td id="adec_grasa_porc">0%</td>
                                <td id="estado_grasa"><span class="badge bg-secondary">-</span></td>
                            </tr>
                            <tr>
                                <td><strong>Proteínas</strong></td>
                                <td id="obj_prot">0 g</td>
                                <td id="plan_prot">0 g</td>
                                <td id="adec_prot_porc">0%</td>
                                <td id="estado_prot"><span class="badge bg-secondary">-</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Observaciones -->
        <div class="mb-3">
            <label class="form-label">Observaciones</label>
            <textarea class="form-control" id="plan_observaciones" name="observaciones" rows="3" placeholder="Observaciones adicionales sobre el plan..."></textarea>
        </div>

        <!-- Botones -->
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="limpiarPlan()">
                <i class="fas fa-redo me-2"></i> Limpiar
            </button>
            <button type="button" class="btn btn-primary" onclick="guardarPlanAlimentario()">
                <i class="fas fa-save me-2"></i> Guardar Plan
            </button>
        </div>
    <?php if (!empty($embebidoEnForm)): ?></div><?php else: ?></form><?php endif; ?>
</div>

<script>
let intercambios = [];
let planGuardado = null;

// Cargar intercambios al iniciar; el plan existente se carga después de tener la tabla de porciones
$(document).ready(function() {
    cargarIntercambios();
    
    // Al mostrar la pestaña Plan: pre-llenar desde calorimetría solo cuando NO hay plan guardado en BD
    $(document).on('shown.bs.tab', '#plan-tab, [data-bs-target="#plan"]', function() {
        if (planGuardado && planGuardado.requerimiento_kcal) return; // ya hay valor de BD, no pisar
        if (typeof calorimetriaGuardada !== 'undefined' && calorimetriaGuardada && calorimetriaGuardada.requerimiento_total) {
            var $kcal = $('#plan_requerimiento_kcal');
            if (!$kcal.length || !$kcal.val() || parseFloat($kcal.val()) <= 0) {
                $kcal.val(Math.round(parseFloat(calorimetriaGuardada.requerimiento_total)));
                calcularMacros();
            }
        }
    });
    
    // Calcular macros cuando cambian los porcentajes o requerimiento
    $('#plan_requerimiento_kcal, #plan_prot_porcentaje, #plan_grasa_porcentaje, #plan_cho_porcentaje').on('input', function() {
        if ($('#modo_porcentaje').is(':checked')) {
            calcularMacros();
            calcularTotalesPlan();
            validarDistribucion();
        }
    });
    
    // Modo gramos: al cambiar gramos, derivar % y actualizar cards e info
    $('#plan_prot_gramos_input, #plan_grasa_gramos_input, #plan_cho_gramos_input').on('input', function() {
        if ($('#modo_gramos').is(':checked')) {
            actualizarDesdeGramos();
        }
    });
    
    // Cambio de modo: Por % <-> Por gramos
    $('input[name="modo_distribucion"]').on('change', function() {
        const modoGramos = $('#modo_gramos').is(':checked');
        $('#bloque_porcentaje').toggle(!modoGramos);
        $('#bloque_gramos').toggle(modoGramos);
        if (modoGramos) {
            // Rellenar inputs de gramos desde los valores actuales de las cards (calculados desde %)
            const p = parseFloat($('#plan_prot_gramos').text()) || 0;
            const g = parseFloat($('#plan_grasa_gramos').text()) || 0;
            const c = parseFloat($('#plan_cho_gramos').text()) || 0;
            $('#plan_prot_gramos_input').val(p > 0 ? p : '');
            $('#plan_grasa_gramos_input').val(g > 0 ? g : '');
            $('#plan_cho_gramos_input').val(c > 0 ? c : '');
            actualizarDesdeGramos();
        } else {
            validarDistribucion();
            calcularMacros();
        }
        calcularTotalesPlan();
    });
});

function cargarIntercambios() {
    $.ajax({
        url: '<?= base_url("dashboard/plan-alimentario/intercambios") ?>',
        method: 'GET',
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response.error) {
                console.error('Error del servidor:', response.error);
                toastr.error(response.error);
                return;
            }
            intercambios = response.intercambios || [];
            
            // Llenar tabla de porciones
            let html = '';
            let orden = 0;
            intercambios.forEach(function(inter) {
                html += `
                    <tr>
                        <td>
                            <strong>${inter.nombre}</strong>
                            <br><small class="text-muted">(${inter.codigo})</small>
                        </td>
                        <td>
                            <input type="number" step="0.1" class="form-control form-control-sm porciones-input" 
                                   data-intercambio-id="${inter.id}" 
                                   data-kcal="${inter.kcal}" 
                                   data-cho="${inter.cho_g}" 
                                   data-grasa="${inter.grasa_g}" 
                                   data-prot="${inter.prot_g}"
                                   value="0" min="0">
                        </td>
                        <td class="calorias-porcion text-center">0</td>
                        <td class="cho-porcion text-center">0</td>
                        <td class="grasa-porcion text-center">0</td>
                        <td class="prot-porcion text-center">0</td>
                    </tr>
                `;
            });
            $('#tbodyPorciones').html(html);
            
            // Event listeners para calcular en tiempo real
            $(document).on('input', '.porciones-input', function() {
                calcularPorcion($(this));
                calcularTotalesPlan();
            });
            
            // Cargar plan existente solo cuando la tabla de porciones ya existe
            cargarPlanExistente();
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar intercambios:', error);
            console.error('Response:', xhr.responseText);
            let errorMsg = 'Error al cargar intercambios';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMsg = xhr.responseJSON.error;
            }
            toastr.error(errorMsg);
        }
    });
}

function calcularMacros() {
    const kcal = parseFloat($('#plan_requerimiento_kcal').val()) || 0;
    const protPorc = parseFloat($('#plan_prot_porcentaje').val()) || 0;
    const grasaPorc = parseFloat($('#plan_grasa_porcentaje').val()) || 0;
    const choPorc = parseFloat($('#plan_cho_porcentaje').val()) || 0;
    
    if (kcal > 0) {
        const prot_gramos = (kcal * protPorc / 100) / 4;
        const grasa_gramos = (kcal * grasaPorc / 100) / 9;
        const cho_gramos = (kcal * choPorc / 100) / 4;
        
        $('#plan_prot_gramos').text(Math.round(prot_gramos * 10) / 10);
        $('#plan_grasa_gramos').text(Math.round(grasa_gramos * 10) / 10);
        $('#plan_cho_gramos').text(Math.round(cho_gramos * 10) / 10);
        $('#macrosObjetivo').show();
    } else {
        $('#macrosObjetivo').hide();
    }
}

// Modo gramos: desde los inputs de gramos derivar calorías, actualizar Requerimiento Energético y %
function actualizarDesdeGramos() {
    const prot_g = parseFloat($('#plan_prot_gramos_input').val()) || 0;
    const grasa_g = parseFloat($('#plan_grasa_gramos_input').val()) || 0;
    const cho_g = parseFloat($('#plan_cho_gramos_input').val()) || 0;
    
    const kcal_prot = prot_g * 4;
    const kcal_grasa = grasa_g * 9;
    const kcal_cho = cho_g * 4;
    const total_kcal = kcal_prot + kcal_grasa + kcal_cho;
    
    // Actualizar Requerimiento Energético con las calorías macros calculadas por gramos
    if (total_kcal > 0) {
        $('#plan_requerimiento_kcal').val(Math.round(total_kcal * 10) / 10);
    }
    
    $('#plan_prot_gramos').text(prot_g > 0 ? (Math.round(prot_g * 10) / 10) : '0');
    $('#plan_grasa_gramos').text(grasa_g > 0 ? (Math.round(grasa_g * 10) / 10) : '0');
    $('#plan_cho_gramos').text(cho_g > 0 ? (Math.round(cho_g * 10) / 10) : '0');
    $('#macrosObjetivo').show();
    
    $('#suma_gramos_info').text('Calorías macros: ' + Math.round(total_kcal) + ' kcal');
    if (total_kcal > 0) {
        const p = (kcal_prot / total_kcal * 100);
        const g = (kcal_grasa / total_kcal * 100);
        const c = (kcal_cho / total_kcal * 100);
        $('#equivale_porcentaje').text('Equivale: ' + p.toFixed(1) + '% · ' + g.toFixed(1) + '% · ' + c.toFixed(1) + '%');
        $('#equivale_porcentaje').removeClass('bg-danger bg-success').addClass('bg-success');
        // Sincronizar % derivados para el backend (base total_kcal = 100%)
        $('#plan_prot_porcentaje').val(Math.round(p * 10) / 10);
        $('#plan_grasa_porcentaje').val(Math.round(g * 10) / 10);
        $('#plan_cho_porcentaje').val(Math.round(c * 10) / 10);
    } else {
        $('#equivale_porcentaje').text('Equivale: - % · - % · - %').removeClass('bg-danger bg-success').addClass('bg-secondary');
    }
    $('#suma_gramos_info').removeClass('bg-danger bg-success').addClass(total_kcal > 0 ? 'bg-success' : 'bg-secondary');
    
    calcularTotalesPlan();
}

function calcularPorcion($input) {
    const porciones = parseFloat($input.val()) || 0;
    const kcal = parseFloat($input.data('kcal')) || 0;
    const cho = parseFloat($input.data('cho')) || 0;
    const grasa = parseFloat($input.data('grasa')) || 0;
    const prot = parseFloat($input.data('prot')) || 0;
    
    const $row = $input.closest('tr');
    $row.find('.calorias-porcion').text(Math.round(porciones * kcal));
    $row.find('.cho-porcion').text(Math.round(porciones * cho));
    $row.find('.grasa-porcion').text(Math.round(porciones * grasa));
    $row.find('.prot-porcion').text(Math.round(porciones * prot));
}

function calcularTotalesPlan() {
    let total_porciones = 0;
    let total_kcal = 0;
    let total_cho = 0;
    let total_grasa = 0;
    let total_prot = 0;
    
    $('.porciones-input').each(function() {
        const porciones = parseFloat($(this).val()) || 0;
        if (porciones > 0) {
            total_porciones += porciones;
            total_kcal += parseFloat($(this).closest('tr').find('.calorias-porcion').text()) || 0;
            total_cho += parseFloat($(this).closest('tr').find('.cho-porcion').text()) || 0;
            total_grasa += parseFloat($(this).closest('tr').find('.grasa-porcion').text()) || 0;
            total_prot += parseFloat($(this).closest('tr').find('.prot-porcion').text()) || 0;
        }
    });
    
    $('#total_porciones').text(total_porciones.toFixed(1));
    $('#total_kcal_plan').text(Math.round(total_kcal));
    $('#total_cho_plan').text(Math.round(total_cho));
    $('#total_grasa_plan').text(Math.round(total_grasa));
    $('#total_prot_plan').text(Math.round(total_prot));
    
    // Calcular adecuación
    calcularAdecuacionPlan(total_kcal, total_cho, total_grasa, total_prot);
}

function calcularAdecuacionPlan(total_kcal, total_cho, total_grasa, total_prot) {
    const requerimiento_kcal = parseFloat($('#plan_requerimiento_kcal').val()) || 0;
    const obj_cho = parseFloat($('#plan_cho_gramos').text()) || 0;
    const obj_grasa = parseFloat($('#plan_grasa_gramos').text()) || 0;
    const obj_prot = parseFloat($('#plan_prot_gramos').text()) || 0;
    
    // % Adecuación = Aporte del plan ÷ Objetivo × 100 (cuánto cubre el plan respecto a la meta)
    if (requerimiento_kcal > 0) {
        const adec_kcal = Math.round((total_kcal / requerimiento_kcal) * 100);
        const adec_cho = (obj_cho > 0) ? Math.round((total_cho / obj_cho) * 100) : 0;
        const adec_grasa = (obj_grasa > 0) ? Math.round((total_grasa / obj_grasa) * 100) : 0;
        const adec_prot = (obj_prot > 0) ? Math.round((total_prot / obj_prot) * 100) : 0;
        
        $('#obj_kcal').text(Math.round(requerimiento_kcal));
        $('#plan_kcal').text(Math.round(total_kcal));
        $('#adec_kcal_porc').text(adec_kcal + '%');
        actualizarEstadoAdecuacion('kcal', adec_kcal);
        
        $('#obj_cho').text(Math.round(obj_cho) + ' g');
        $('#plan_cho').text(Math.round(total_cho) + ' g');
        $('#adec_cho_porc').text(adec_cho + '%');
        actualizarEstadoAdecuacion('cho', adec_cho);
        
        $('#obj_grasa').text(Math.round(obj_grasa) + ' g');
        $('#plan_grasa').text(Math.round(total_grasa) + ' g');
        $('#adec_grasa_porc').text(adec_grasa + '%');
        actualizarEstadoAdecuacion('grasa', adec_grasa);
        
        $('#obj_prot').text(Math.round(obj_prot) + ' g');
        $('#plan_prot').text(Math.round(total_prot) + ' g');
        $('#adec_prot_porc').text(adec_prot + '%');
        actualizarEstadoAdecuacion('prot', adec_prot);
        
        $('#adecuacionPlan').show();
    }
}

function actualizarEstadoAdecuacion(tipo, porcentaje) {
    let badge = '';
    if (porcentaje >= 90 && porcentaje <= 110) {
        badge = '<span class="badge bg-success">Óptimo</span>';
    } else if (porcentaje >= 80 && porcentaje < 90) {
        badge = '<span class="badge bg-warning">Bajo</span>';
    } else if (porcentaje > 110 && porcentaje <= 120) {
        badge = '<span class="badge bg-warning">Alto</span>';
    } else {
        badge = '<span class="badge bg-danger">Fuera de rango</span>';
    }
    $('#estado_' + tipo).html(badge);
}

function parseNum(text) {
    if (!text) return 0;
    return parseFloat(String(text).replace(/\s*g/i, '').trim()) || 0;
}

function badgeAdecuacion(porcentaje) {
    if (porcentaje >= 90 && porcentaje <= 110) return '<span class="badge bg-success">Óptimo</span>';
    if (porcentaje >= 80 && porcentaje < 90) return '<span class="badge bg-warning">Bajo</span>';
    if (porcentaje > 110 && porcentaje <= 120) return '<span class="badge bg-warning">Alto</span>';
    return '<span class="badge bg-danger">Fuera de rango</span>';
}

function validarDistribucion() {
    const prot = parseFloat($('#plan_prot_porcentaje').val()) || 0;
    const grasa = parseFloat($('#plan_grasa_porcentaje').val()) || 0;
    const cho = parseFloat($('#plan_cho_porcentaje').val()) || 0;
    const suma = prot + grasa + cho;
    
    $('#suma_distribucion').text('Total: ' + suma.toFixed(1) + '%');
    
    if (Math.abs(suma - 100) < 0.1) {
        $('#suma_distribucion').removeClass('bg-secondary bg-danger').addClass('bg-success');
    } else {
        $('#suma_distribucion').removeClass('bg-secondary bg-success').addClass('bg-danger');
    }
    
    calcularMacros();
}

function guardarPlanAlimentario() {
    const detalleAgendaId = $('#detalle_agenda_id_plan').val();
    const requerimientoKcal = parseFloat($('#plan_requerimiento_kcal').val());
    const modoGramos = $('#modo_gramos').is(':checked');
    
    let protPorc, grasaPorc, choPorc;
    if (modoGramos) {
        const prot_g = parseFloat($('#plan_prot_gramos_input').val()) || 0;
        const grasa_g = parseFloat($('#plan_grasa_gramos_input').val()) || 0;
        const cho_g = parseFloat($('#plan_cho_gramos_input').val()) || 0;
        const totalKcalMacros = prot_g * 4 + grasa_g * 9 + cho_g * 4;
        if (!requerimientoKcal || requerimientoKcal <= 0) {
            toastr.error('El requerimiento energético es requerido');
            return;
        }
        protPorc = requerimientoKcal > 0 ? (prot_g * 4 / requerimientoKcal) * 100 : (totalKcalMacros > 0 ? (prot_g * 4 / totalKcalMacros) * 100 : 0);
        grasaPorc = requerimientoKcal > 0 ? (grasa_g * 9 / requerimientoKcal) * 100 : (totalKcalMacros > 0 ? (grasa_g * 9 / totalKcalMacros) * 100 : 0);
        choPorc = requerimientoKcal > 0 ? (cho_g * 4 / requerimientoKcal) * 100 : (totalKcalMacros > 0 ? (cho_g * 4 / totalKcalMacros) * 100 : 0);
    } else {
        protPorc = parseFloat($('#plan_prot_porcentaje').val()) || 0;
        grasaPorc = parseFloat($('#plan_grasa_porcentaje').val()) || 0;
        choPorc = parseFloat($('#plan_cho_porcentaje').val()) || 0;
    }
    
    if (!requerimientoKcal || requerimientoKcal <= 0) {
        toastr.error('El requerimiento energético es requerido');
        return;
    }
    
    // Recopilar porciones
    const porciones = [];
    $('.porciones-input').each(function() {
        const porcionesVal = parseFloat($(this).val()) || 0;
        if (porcionesVal > 0) {
            porciones.push({
                intercambio_porcion_id: $(this).data('intercambio-id'),
                porciones: porcionesVal
            });
        }
    });
    
    const calorimetriaId = calorimetriaGuardada ? calorimetriaGuardada.id : null;
    
    $.ajax({
        url: '<?= base_url("dashboard/plan-alimentario/crear-plan") ?>',
        method: 'POST',
        data: {
            detalle_agenda_id: detalleAgendaId,
            requerimiento_kcal: requerimientoKcal,
            prot_porcentaje: protPorc,
            grasa_porcentaje: grasaPorc,
            cho_porcentaje: choPorc,
            porciones: porciones,
            calorimetria_id: calorimetriaId,
            observaciones: $('#plan_observaciones').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                if (typeof window.actualizarEstadoCalorimetriaPlan === 'function') window.actualizarEstadoCalorimetriaPlan('guardado');
                toastr.success('Plan alimentario guardado correctamente');
                planGuardado = response.plan;
                
                // Habilitar tab de distribución
                $('#distribucion-tab').removeClass('disabled');
            } else {
                if (typeof window.actualizarEstadoCalorimetriaPlan === 'function') window.actualizarEstadoCalorimetriaPlan('cambios');
                toastr.error(response.error || 'Error al guardar plan');
            }
        },
        error: function(xhr, status, error) {
            if (typeof window.actualizarEstadoCalorimetriaPlan === 'function') window.actualizarEstadoCalorimetriaPlan('cambios');
            console.error('Error:', error);
            toastr.error('Error al guardar plan alimentario');
        }
    });
}

function aplicarPlanEnFormulario(plan, esReferencia) {
    if (!plan) return false;

    if (plan.requerimiento_kcal != null) {
        $('#plan_requerimiento_kcal').val(plan.requerimiento_kcal);
    }
    if (plan.prot_porcentaje != null) {
        $('#plan_prot_porcentaje').val(plan.prot_porcentaje);
    }
    if (plan.grasa_porcentaje != null) {
        $('#plan_grasa_porcentaje').val(plan.grasa_porcentaje);
    }
    if (plan.cho_porcentaje != null) {
        $('#plan_cho_porcentaje').val(plan.cho_porcentaje);
    }
    $('#plan_observaciones').val(plan.observaciones || '');

    var usarGramos = (parseFloat(plan.prot_gramos) || 0) > 0
        || (parseFloat(plan.grasa_gramos) || 0) > 0
        || (parseFloat(plan.cho_gramos) || 0) > 0;
    if (usarGramos) {
        $('#modo_gramos').prop('checked', true);
        $('#modo_porcentaje').prop('checked', false);
        $('#bloque_porcentaje').hide();
        $('#bloque_gramos').show();
        $('#plan_prot_gramos_input').val(plan.prot_gramos || '');
        $('#plan_grasa_gramos_input').val(plan.grasa_gramos || '');
        $('#plan_cho_gramos_input').val(plan.cho_gramos || '');
    } else {
        $('#modo_porcentaje').prop('checked', true);
        $('#modo_gramos').prop('checked', false);
        $('#bloque_porcentaje').show();
        $('#bloque_gramos').hide();
        $('#plan_prot_gramos_input, #plan_grasa_gramos_input, #plan_cho_gramos_input').val('');
    }

    calcularMacros();
    validarDistribucion();

    if (esReferencia) {
        $('.porciones-input').val(0);
        $('.calorias-porcion, .cho-porcion, .grasa-porcion, .prot-porcion').text('0');
    }
    if (plan.porciones && plan.porciones.length) {
        plan.porciones.forEach(function(por) {
            const $input = $('.porciones-input[data-intercambio-id="' + por.intercambio_porcion_id + '"]');
            if (!$input.length) return;
            $input.val(por.porciones);
            calcularPorcion($input);
        });
        calcularTotalesPlan();
    }

    if (esReferencia) {
        window.planReferenciaUltimaConsulta = plan;
        $('#distribucion-tab').removeClass('disabled');
        if (plan.id && typeof cargarDistribucionComidas === 'function') {
            cargarDistribucionComidas(plan.id);
        }
    } else {
        planGuardado = plan;
        window.planReferenciaUltimaConsulta = null;
        $('#distribucion-tab').removeClass('disabled');
    }
    return true;
}

function cargarPlanDesdeDetalle(detalleAgendaId, esReferencia, callback) {
    if (!detalleAgendaId) {
        if (callback) callback(false);
        return;
    }
    $.ajax({
        url: '<?= base_url("dashboard/plan-alimentario/plan") ?>/' + detalleAgendaId,
        method: 'GET',
        dataType: 'json',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(response) {
            if (!response.plan) {
                if (callback) callback(false);
                return;
            }
            var ok = aplicarPlanEnFormulario(response.plan, esReferencia);
            if (callback) callback(ok);
        },
        error: function() {
            if (callback) callback(false);
        }
    });
}

function cargarPlanExistente() {
    const detalleAgendaId = $('#detalle_agenda_id_plan').val();
    if (!detalleAgendaId) return;

    cargarPlanDesdeDetalle(detalleAgendaId, false, function(encontrado) {
        if (!encontrado && window.referenciaDetalleAgendaId
            && String(window.referenciaDetalleAgendaId) !== String(detalleAgendaId)) {
            cargarPlanDesdeDetalle(window.referenciaDetalleAgendaId, true);
        }
    });
}

function limpiarPlan() {
    $('#formPlanAlimentario')[0].reset();
    $('#modo_porcentaje').prop('checked', true);
    $('#modo_gramos').prop('checked', false);
    $('#bloque_porcentaje').show();
    $('#bloque_gramos').hide();
    $('#plan_prot_porcentaje').val(20);
    $('#plan_grasa_porcentaje').val(30);
    $('#plan_cho_porcentaje').val(50);
    $('#plan_prot_gramos_input, #plan_grasa_gramos_input, #plan_cho_gramos_input').val('');
    $('#macrosObjetivo').hide();
    $('#adecuacionPlan').hide();
    $('.porciones-input').val(0);
    $('.calorias-porcion, .cho-porcion, .grasa-porcion, .prot-porcion').text('0');
    $('#total_porciones, #total_kcal_plan, #total_cho_plan, #total_grasa_plan, #total_prot_plan').text('0');
    planGuardado = null;
    validarDistribucion();
}
</script>

<div id="distribucionComidasSection">
    <input type="hidden" id="plan_id_distribucion" value="">
    
    <div id="sinPlanAlert" class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Primero debes crear un Plan Alimentario</strong> en la pestaña "Plan Alimentario".
    </div>
    
    <div id="conPlanContent" style="display: none;">
        <!-- Header: Resumen del Plan (Cálculo de porciones) -->
        <div class="mb-4" id="resumenPlanHeader">
            <h6 class="mb-3" style="font-family: 'Segoe Print', 'Comic Sans MS', cursive;"><i class="fas fa-chart-pie me-2"></i> Cálculo de porciones</h6>
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="card border-primary bg-light">
                        <div class="card-body py-2">
                            <small class="text-muted d-block">Requerimiento energético</small>
                            <strong id="resumenReqKcal">—</strong> kcal
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-secondary bg-light">
                        <div class="card-body py-2">
                            <small class="text-muted d-block">Distribución calórica</small>
                            <span id="resumenDistrib">—</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-info bg-light">
                        <div class="card-body py-2">
                            <small class="text-muted d-block">Adecuación (kcal)</small>
                            Mín. <strong id="resumenAdecMin">—</strong> · Máx. <strong id="resumenAdecMax">—</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contador de Porciones -->
        <div class="mb-4">
            <h6 class="text-primary mb-3"><i class="fas fa-calculator me-2"></i> Contador de Porciones para Planificar</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover" id="tablaContadorPorciones">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 20%;">Grupo</th>
                            <th style="width: 12%;">Objetivo</th>
                            <th style="width: 11%;">Desayuno</th>
                            <th style="width: 11%;">Colación</th>
                            <th style="width: 11%;">Almuerzo</th>
                            <th style="width: 11%;">Cena</th>
                            <th style="width: 11%;">Once</th>
                            <th style="width: 11%;">Otros</th>
                            <th style="width: 12%;">Sobra</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyContadorPorciones">
                        <!-- Se llenará con JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detalle de Comidas -->
        <div class="mb-4">
            <h6 class="text-success mb-3"><i class="fas fa-utensils me-2"></i> Detalle de Comidas</h6>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <select class="form-select" id="selectComida">
                        <option value="">Seleccione una comida</option>
                        <option value="desayuno">Desayuno</option>
                        <option value="colacion">Colación</option>
                        <option value="almuerzo">Almuerzo</option>
                        <option value="cena">Cena</option>
                        <option value="once">Once</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <button type="button" class="btn btn-primary" onclick="abrirModalComida()">
                        <i class="fas fa-plus me-2"></i> Agregar/Editar Comida
                    </button>
                </div>
            </div>

            <!-- Lista de Comidas -->
            <div id="listaComidas">
                <!-- Se llenará con JavaScript -->
            </div>
        </div>

        <!-- Botones -->
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-primary" onclick="guardarDistribucion()">
                <i class="fas fa-save me-2"></i> Guardar Distribución
            </button>
        </div>
    </div>
</div>

<!-- Modal para Editar Comida -->
<div class="modal fade" id="modalComida" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalComidaTitle">Agregar Comida</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formComida">
                    <input type="hidden" id="comida_id" value="">
                    <input type="hidden" id="comida_tipo" value="">
                    
                    <div class="mb-3">
                        <label class="form-label">Comida</label>
                        <select class="form-select" id="comida_nombre" required>
                            <option value="">Seleccione</option>
                            <option value="desayuno">Desayuno</option>
                            <option value="colacion">Colación</option>
                            <option value="almuerzo">Almuerzo</option>
                            <option value="cena">Cena</option>
                            <option value="once">Once</option>
                            <option value="otros">Otros</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">% VCT</label>
                        <input type="number" step="0.1" class="form-control" id="comida_porcentaje_vct" placeholder="Ej: 25.5">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Minuta (Descripción)</label>
                        <textarea class="form-control" id="comida_minuta" rows="2" placeholder="Ej: Pan con palta, leche descremada fría y jugo de naranja"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Items de la Comida</label>
                        <button type="button" class="btn btn-sm btn-outline-primary mb-2" onclick="agregarItemComida()">
                            <i class="fas fa-plus me-2"></i> Agregar Item
                        </button>
                        <div id="itemsComida">
                            <!-- Se llenará dinámicamente -->
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarComida()">Guardar Comida</button>
            </div>
        </div>
    </div>
</div>

<script>
let porcionesObjetivo = [];
let distribucionComidas = {
    desayuno: { items: [] },
    colacion: { items: [] },
    almuerzo: { items: [] },
    cena: { items: [] },
    once: { items: [] },
    otros: { items: [] }
};

$(document).ready(function() {
    if (typeof planGuardado !== 'undefined' && planGuardado && planGuardado.id) {
        cargarDistribucionComidas(planGuardado.id);
    } else {
        // En editar historial el plan puede cargarse después; reintentar al tener plan
        setTimeout(function() {
            if (typeof planGuardado !== 'undefined' && planGuardado && planGuardado.id) {
                cargarDistribucionComidas(planGuardado.id);
            }
        }, 800);
    }
});

// Al mostrar la pestaña Distribución (consulta o editar historial): si no hay plan en memoria, obtenerlo por detalle_agenda_id
$(document).on('shown.bs.tab', '#distribucion-tab, #distribucion-tab-editar, [data-bs-target="#distribucion"], [data-bs-target="#distribucion-editar"]', function() {
    function abrirDistribucion(planId) {
        if ((typeof intercambios === 'undefined' || !intercambios || intercambios.length === 0)) {
            $.ajax({ url: '<?= base_url("dashboard/plan-alimentario/intercambios") ?>', method: 'GET', dataType: 'json', headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(r) {
                    var list = (r && r.intercambios) ? r.intercambios : [];
                    if (typeof intercambios !== 'undefined') intercambios = list;
                    else window.intercambios = list;
                    cargarDistribucionComidas(planId);
                }
            });
        } else {
            cargarDistribucionComidas(planId);
        }
    }
    if (typeof planGuardado !== 'undefined' && planGuardado && planGuardado.id) {
        abrirDistribucion(planGuardado.id);
        return;
    }
    var detalleAgendaId = $('#detalle_agenda_id_plan').val() || $('#detalle_agenda_id_cal').val();
    if (!detalleAgendaId) return;
    $.ajax({
        url: '<?= base_url("dashboard/plan-alimentario/plan") ?>/' + detalleAgendaId,
        method: 'GET',
        dataType: 'json',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(response) {
            if (response.plan && response.plan.id) {
                if (typeof planGuardado !== 'undefined') planGuardado = response.plan;
                abrirDistribucion(response.plan.id);
            }
        }
    });
});

function cargarDistribucionComidas(planId) {
    $('#plan_id_distribucion').val(planId);
    
    $.ajax({
        url: '<?= base_url("dashboard/plan-alimentario/comidas") ?>/' + planId,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.comidas && response.comidas.length > 0) {
                distribucionComidas = {};
                response.comidas.forEach(function(comida) {
                    distribucionComidas[comida.comida] = {
                        id: comida.id,
                        porcentaje_vct: comida.porcentaje_vct,
                        minuta: comida.minuta,
                        items: comida.items || []
                    };
                });
            }
            
            actualizarContadorPorciones();
            actualizarListaComidas();
            actualizarResumenPlanHeader();
            $('#sinPlanAlert').hide();
            $('#conPlanContent').show();
        },
        error: function() {
            // Si no hay comidas, solo mostrar contador vacío
            var planFallback = (typeof planGuardado !== 'undefined' && planGuardado)
                ? planGuardado
                : (typeof window.planReferenciaUltimaConsulta !== 'undefined' ? window.planReferenciaUltimaConsulta : null);
            if (planFallback && planFallback.porciones) {
                porcionesObjetivo = planFallback.porciones;
                actualizarContadorPorciones();
                actualizarResumenPlanHeader();
                $('#sinPlanAlert').hide();
                $('#conPlanContent').show();
            }
        }
    });
}

function actualizarResumenPlanHeader() {
    var planRef = (typeof planGuardado !== 'undefined' && planGuardado)
        ? planGuardado
        : (typeof window.planReferenciaUltimaConsulta !== 'undefined' ? window.planReferenciaUltimaConsulta : null);
    if (!planRef) return;
    var req = (planRef.requerimiento_kcal != null && planRef.requerimiento_kcal !== '') ? parseFloat(planRef.requerimiento_kcal) : null;
    var prot = (planRef.prot_porcentaje != null && planRef.prot_porcentaje !== '') ? parseFloat(planRef.prot_porcentaje) : null;
    var grasa = (planRef.grasa_porcentaje != null && planRef.grasa_porcentaje !== '') ? parseFloat(planRef.grasa_porcentaje) : null;
    var cho = (planRef.cho_porcentaje != null && planRef.cho_porcentaje !== '') ? parseFloat(planRef.cho_porcentaje) : null;
    var minA = (planRef.adecuacion_min != null && planRef.adecuacion_min !== '') ? parseFloat(planRef.adecuacion_min) : (req != null ? Math.round(req * 0.9) : null);
    var maxA = (planRef.adecuacion_max != null && planRef.adecuacion_max !== '') ? parseFloat(planRef.adecuacion_max) : (req != null ? Math.round(req * 1.1) : null);
    $('#resumenReqKcal').text(req != null ? Math.round(req) : '—');
    $('#resumenAdecMin').text(minA != null ? Math.round(minA) : '—');
    $('#resumenAdecMax').text(maxA != null ? Math.round(maxA) : '—');
    var dist = [];
    if (prot != null) dist.push('Proteínas ' + prot + '%');
    if (grasa != null) dist.push('Grasas ' + grasa + '%');
    if (cho != null) dist.push('CHO ' + cho + '%');
    $('#resumenDistrib').text(dist.length ? dist.join(' · ') : '—');
}

function sumarPorcionesPorComida(intercambioId) {
    var out = { desayuno: 0, colacion: 0, almuerzo: 0, cena: 0, once: 0, otros: 0 };
    ['desayuno', 'colacion', 'almuerzo', 'cena', 'once', 'otros'].forEach(function(comida) {
        if (distribucionComidas[comida] && distribucionComidas[comida].items) {
            distribucionComidas[comida].items.forEach(function(item) {
                if (item.intercambio_porcion_id == intercambioId) {
                    out[comida] += parseFloat(item.porciones) || 0;
                }
            });
        }
    });
    return out;
}

function actualizarContadorPorciones() {
    if (!planGuardado) return;
    
    var listaGrupos = [];
    var listaI = (typeof intercambios !== 'undefined' && intercambios && intercambios.length > 0) ? intercambios : (window.intercambios || []);
    
    if (listaI.length > 0) {
        listaI.forEach(function(inter) {
            var por = (planGuardado.porciones || []).filter(function(p) { return p.intercambio_porcion_id == inter.id; })[0];
            listaGrupos.push({
                id: inter.id,
                nombre: inter.nombre,
                objetivo: por ? (parseFloat(por.porciones) || 0) : 0,
                kcal: parseFloat(inter.kcal) || 0
            });
        });
    } else if (planGuardado.porciones && planGuardado.porciones.length > 0) {
        porcionesObjetivo = planGuardado.porciones;
        porcionesObjetivo.forEach(function(por) {
            listaGrupos.push({
                id: por.intercambio_porcion_id,
                nombre: (por.intercambio && por.intercambio.nombre) ? por.intercambio.nombre : 'Grupo',
                objetivo: parseFloat(por.porciones) || 0,
                kcal: (por.intercambio && por.intercambio.kcal) ? parseFloat(por.intercambio.kcal) : 0
            });
        });
    } else return;
    
    var idToKcal = {};
    listaGrupos.forEach(function(g) { idToKcal[g.id] = g.kcal; });
    
    var html = '';
    listaGrupos.forEach(function(g) {
        var d = sumarPorcionesPorComida(g.id);
        var totalAsignado = d.desayuno + d.colacion + d.almuerzo + d.cena + d.once + d.otros;
        var sobra = g.objetivo - totalAsignado;
        var sobraClass = (sobra >= 0) ? 'text-success' : 'text-danger';
        html += '<tr><td><strong>' + g.nombre + '</strong></td><td class="text-center"><strong>' + g.objetivo.toFixed(1) + '</strong></td>';
        html += '<td class="text-center">' + d.desayuno.toFixed(1) + '</td><td class="text-center">' + d.colacion.toFixed(1) + '</td><td class="text-center">' + d.almuerzo.toFixed(1) + '</td>';
        html += '<td class="text-center">' + d.cena.toFixed(1) + '</td><td class="text-center">' + d.once.toFixed(1) + '</td><td class="text-center">' + d.otros.toFixed(1) + '</td>';
        html += '<td class="text-center ' + sobraClass + '"><strong>' + sobra.toFixed(1) + '</strong></td></tr>';
    });
    
    var kcalDesayuno = 0, kcalColacion = 0, kcalAlmuerzo = 0, kcalCena = 0, kcalOnce = 0, kcalOtros = 0;
    ['desayuno', 'colacion', 'almuerzo', 'cena', 'once', 'otros'].forEach(function(comida) {
        if (distribucionComidas[comida] && distribucionComidas[comida].items) {
            distribucionComidas[comida].items.forEach(function(item) {
                var k = (idToKcal[item.intercambio_porcion_id] || 0) * (parseFloat(item.porciones) || 0);
                if (comida === 'desayuno') kcalDesayuno += k;
                else if (comida === 'colacion') kcalColacion += k;
                else if (comida === 'almuerzo') kcalAlmuerzo += k;
                else if (comida === 'cena') kcalCena += k;
                else if (comida === 'once') kcalOnce += k;
                else kcalOtros += k;
            });
        }
    });
    html += '<tr class="table-info"><td><strong>Calorías</strong></td><td class="text-center">—</td>';
    html += '<td class="text-center">' + Math.round(kcalDesayuno) + '</td><td class="text-center">' + Math.round(kcalColacion) + '</td><td class="text-center">' + Math.round(kcalAlmuerzo) + '</td>';
    html += '<td class="text-center">' + Math.round(kcalCena) + '</td><td class="text-center">' + Math.round(kcalOnce) + '</td><td class="text-center">' + Math.round(kcalOtros) + '</td>';
    var totalKcalDist = kcalDesayuno + kcalColacion + kcalAlmuerzo + kcalCena + kcalOnce + kcalOtros;
    var reqKcal = (planGuardado.requerimiento_kcal && parseFloat(planGuardado.requerimiento_kcal)) ? parseFloat(planGuardado.requerimiento_kcal) : 0;
    var sobraKcal = reqKcal - totalKcalDist;
    html += '<td class="text-center ' + (sobraKcal >= 0 ? 'text-success' : 'text-danger') + '"><strong>' + Math.round(sobraKcal) + '</strong></td></tr>';
    
    $('#tbodyContadorPorciones').html(html);
}

function actualizarListaComidas() {
    let html = '';
    const comidas = ['desayuno', 'colacion', 'almuerzo', 'cena', 'once', 'otros'];
    
    comidas.forEach(function(comida) {
        const comidaData = distribucionComidas[comida] || { items: [] };
        const totalItems = comidaData.items ? comidaData.items.length : 0;
        
        html += `
            <div class="card mb-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-capitalize">${comida}</h6>
                            ${comidaData.minuta ? '<small class="text-muted">' + comidaData.minuta + '</small>' : ''}
                            ${comidaData.porcentaje_vct ? '<small class="badge bg-info">' + comidaData.porcentaje_vct + '% VCT</small>' : ''}
                        </div>
                        <div>
                            <span class="badge bg-secondary me-2">${totalItems} items</span>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarComida('${comida}')">
                                <i class="fas fa-edit me-1"></i> Editar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    $('#listaComidas').html(html);
}

function abrirModalComida() {
    const comidaTipo = $('#selectComida').val();
    if (!comidaTipo) {
        toastr.warning('Seleccione una comida primero');
        return;
    }
    abrirModalComidaPorTipo(comidaTipo);
}

function editarComida(comidaTipo) {
    $('#selectComida').val(comidaTipo);
    abrirModalComidaPorTipo(comidaTipo);
}

function abrirModalComidaPorTipo(comidaTipo) {
    $('#comida_tipo').val(comidaTipo);
    $('#comida_nombre').val(comidaTipo);
    $('#comida_id').val(distribucionComidas[comidaTipo] ? distribucionComidas[comidaTipo].id : '');
    $('#comida_porcentaje_vct').val(distribucionComidas[comidaTipo] ? distribucionComidas[comidaTipo].porcentaje_vct : '');
    $('#comida_minuta').val(distribucionComidas[comidaTipo] ? distribucionComidas[comidaTipo].minuta : '');
    cargarItemsComida(comidaTipo);
    $('#modalComidaTitle').text('Editar ' + comidaTipo.charAt(0).toUpperCase() + comidaTipo.slice(1));
    $('#modalComida').modal('show');
}

function cargarItemsComida(comidaTipo) {
    const items = distribucionComidas[comidaTipo] ? distribucionComidas[comidaTipo].items : [];
    let html = '';
    
    items.forEach(function(item, index) {
        html += crearFilaItemComida(item, index);
    });
    
    if (html === '') {
        html = crearFilaItemComida(null, 0);
    }
    
    $('#itemsComida').html(html);
}

function intercambiosDelPlan() {
    var list = (typeof intercambios !== 'undefined' && intercambios && intercambios.length) ? intercambios : (window.intercambios || []);
    var ids = [];
    if (typeof planGuardado !== 'undefined' && planGuardado && planGuardado.porciones) {
        planGuardado.porciones.forEach(function(p) { ids.push(parseInt(p.intercambio_porcion_id, 10)); });
    }
    return ids.length ? list.filter(function(inter) { return ids.indexOf(parseInt(inter.id, 10)) !== -1; }) : list;
}

function crearFilaItemComida(item, index) {
    const intercambioId = item ? item.intercambio_porcion_id : '';
    const porciones = item ? item.porciones : 0;
    const ingrediente = item ? item.ingrediente : '';
    const medida = item ? item.medida_casera : '';
    const gramaje = item ? item.gramaje : '';
    
    var opciones = intercambiosDelPlan();
    let selectHtml = '<option value="">Seleccione</option>';
    opciones.forEach(function(inter) {
        selectHtml += `<option value="${inter.id}" ${inter.id == intercambioId ? 'selected' : ''}>${inter.nombre} (${inter.codigo})</option>`;
    });
    
    return `
        <div class="card mb-2 item-comida-row" data-index="${index}">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label small">Intercambio</label>
                        <select class="form-select form-select-sm item-intercambio" required>
                            ${selectHtml}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Porciones</label>
                        <input type="number" step="0.1" class="form-control form-control-sm item-porciones" value="${porciones}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Medida Casera</label>
                        <input type="text" class="form-control form-control-sm item-medida" value="${medida}" placeholder="1/2 pan">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Gramaje</label>
                        <input type="text" class="form-control form-control-sm item-gramaje" value="${gramaje}" placeholder="50g">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label small">&nbsp;</label>
                        <button type="button" class="btn btn-sm btn-danger w-100" onclick="eliminarItemComida($(this))">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <label class="form-label small">Ingrediente/Alimento</label>
                        <input type="text" class="form-control form-control-sm item-ingrediente" value="${ingrediente}" placeholder="Ej: Pan integral">
                    </div>
                </div>
            </div>
        </div>
    `;
}

function agregarItemComida() {
    const index = $('#itemsComida .item-comida-row').length;
    $('#itemsComida').append(crearFilaItemComida(null, index));
}

function eliminarItemComida($btn) {
    $btn.closest('.item-comida-row').remove();
}

function guardarComida() {
    const comidaTipo = $('#comida_tipo').val();
    if (!comidaTipo) {
        toastr.error('Seleccione un tipo de comida');
        return;
    }
    
    // Recopilar items
    const items = [];
    $('#itemsComida .item-comida-row').each(function() {
        const intercambioId = $(this).find('.item-intercambio').val();
        const porciones = parseFloat($(this).find('.item-porciones').val()) || 0;
        
        if (intercambioId && porciones > 0) {
            items.push({
                intercambio_porcion_id: intercambioId,
                porciones: porciones,
                ingrediente: $(this).find('.item-ingrediente').val(),
                medida_casera: $(this).find('.item-medida').val(),
                gramaje: $(this).find('.item-gramaje').val()
            });
        }
    });
    
    if (items.length === 0) {
        toastr.warning('Agregue al menos un item a la comida');
        return;
    }
    
    // Guardar en estructura local
    distribucionComidas[comidaTipo] = {
        porcentaje_vct: $('#comida_porcentaje_vct').val(),
        minuta: $('#comida_minuta').val(),
        items: items
    };
    
    $('#modalComida').modal('hide');
    actualizarContadorPorciones();
    actualizarListaComidas();
    if (typeof window.actualizarEstadoCalorimetriaPlan === 'function') {
        window.actualizarEstadoCalorimetriaPlan('cambios');
    }
    if (typeof window.programarAutoGuardadoCalorimetriaPlan === 'function') {
        window.programarAutoGuardadoCalorimetriaPlan();
    }
    toastr.success('Comida guardada (localmente). Guarde la distribución para persistir los cambios.');
}

function guardarDistribucion(silent) {
    silent = !!silent;
    const planId = $('#plan_id_distribucion').val();
    if (!planId) {
        if (!silent) toastr.error('No hay plan alimentario guardado');
        if (typeof window.actualizarEstadoCalorimetriaPlan === 'function') window.actualizarEstadoCalorimetriaPlan('cambios');
        return;
    }
    
    // Convertir estructura a formato para API
    const distribucion = [];
    ['desayuno', 'colacion', 'almuerzo', 'cena', 'once', 'otros'].forEach(function(comida) {
        if (distribucionComidas[comida] && distribucionComidas[comida].items && distribucionComidas[comida].items.length > 0) {
            distribucion.push({
                comida: comida,
                porcentaje_vct: distribucionComidas[comida].porcentaje_vct,
                minuta: distribucionComidas[comida].minuta,
                items: distribucionComidas[comida].items
            });
        }
    });
    
    $.ajax({
        url: '<?= base_url("dashboard/plan-alimentario/distribuir-comidas") ?>',
        method: 'POST',
        data: {
            plan_id: planId,
            distribucion: distribucion
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                if (typeof window.actualizarEstadoCalorimetriaPlan === 'function') window.actualizarEstadoCalorimetriaPlan('guardado');
                if (!silent) {
                    toastGuardadoExito(response.message || 'Distribución por comidas actualizada correctamente');
                }
                planGuardado = response.plan;
                actualizarContadorPorciones();
                actualizarListaComidas();
            } else {
                if (typeof window.actualizarEstadoCalorimetriaPlan === 'function') window.actualizarEstadoCalorimetriaPlan('cambios');
                if (!silent) toastGuardadoError(response.error || 'Error al guardar distribución');
            }
        },
        error: function(xhr, status, error) {
            if (typeof window.actualizarEstadoCalorimetriaPlan === 'function') window.actualizarEstadoCalorimetriaPlan('cambios');
            console.error('Error:', error);
            if (!silent) toastGuardadoError('Error al guardar distribución');
        }
    });
}

// Cuando se carga un plan, habilitar distribución
if (typeof planGuardado !== 'undefined' && planGuardado) {
    cargarDistribucionComidas(planGuardado.id);
}
</script>

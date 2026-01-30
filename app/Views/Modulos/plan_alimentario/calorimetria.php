<div id="calorimetriaSection">
    <input type="hidden" id="detalle_agenda_id_cal" value="<?= (isset($cita) && $cita && isset($cita->id)) ? (int)$cita->id : '' ?>">
    
    <!-- Información del Paciente para Calorimetría -->
    <div class="alert alert-info mb-3">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Nota:</strong> Los datos de peso, talla y edad se obtendrán del paciente. Si no están disponibles, puedes ingresarlos manualmente.
    </div>

    <!-- Formulario de Calorimetría -->
    <form id="formCalorimetria">
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label">Peso (kg) <span class="text-danger">*</span></label>
                <input type="number" step="0.1" class="form-control" id="cal_peso" name="peso" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Talla (cm) <span class="text-danger">*</span></label>
                <input type="number" step="0.1" class="form-control" id="cal_talla" name="talla" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Edad <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="cal_edad" name="edad" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Sexo <span class="text-danger">*</span></label>
                <select class="form-select" id="cal_sexo" name="sexo" required>
                    <option value="">Seleccione</option>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                </select>
            </div>
        </div>

        <!-- Resultados TMB (valores exactos en hidden para cálculos = planilla Excel) -->
        <input type="hidden" id="tmb_hombres_exact" value="">
        <input type="hidden" id="tmb_mujeres_exact" value="">
        <input type="hidden" id="tmb_usado_exact" value="">
        <div class="row mb-3" id="tmbResults" style="display: none;">
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <small class="text-muted d-block">TMB Hombres</small>
                        <h4 class="mb-0" id="tmb_hombres">-</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <small class="text-muted d-block">TMB Mujeres</small>
                        <h4 class="mb-0" id="tmb_mujeres">-</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <small class="d-block opacity-75">TMB Usado</small>
                        <h4 class="mb-0" id="tmb_usado">-</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actividades Diarias -->
        <div class="mb-4">
            <h6 class="text-primary mb-3"><i class="fas fa-walking me-2"></i> Actividades Diarias</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered" id="tablaActividadesDiarias">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40%;">Actividad</th>
                            <th style="width: 15%;">METs</th>
                            <th style="width: 20%;">Minutos/día</th>
                            <th style="width: 12%;">Cal. Hombre</th>
                            <th style="width: 12%;">Cal. Mujer</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyActividadesDiarias">
                        <!-- Se llenará con JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Deportes/Fitness -->
        <div class="mb-4">
            <h6 class="text-success mb-3"><i class="fas fa-dumbbell me-2"></i> Deportes / Fitness</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered" id="tablaDeportes">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40%;">Actividad</th>
                            <th style="width: 15%;">METs</th>
                            <th style="width: 20%;">Minutos/día</th>
                            <th style="width: 12%;">Cal. Hombre</th>
                            <th style="width: 12%;">Cal. Mujer</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyDeportes">
                        <!-- Se llenará con JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Totales y Requerimiento -->
        <div class="row mb-3" id="totalesCalorimetria" style="display: none;">
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <small class="d-block opacity-75">Total Minutos</small>
                        <h5 class="mb-0" id="total_minutos">0</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-secondary text-white">
                    <div class="card-body text-center">
                        <small class="d-block opacity-75">Cals Habituales</small>
                        <h5 class="mb-0" id="cals_habituales">0</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <small class="d-block opacity-75">Cals Entrenamiento</small>
                        <h5 class="mb-0" id="cals_entrenamiento">0</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <small class="d-block opacity-75">Requerimiento Total</small>
                        <h5 class="mb-0" id="requerimiento_total">0</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="limpiarCalorimetria()">
                <i class="fas fa-redo me-2"></i> Limpiar
            </button>
            <button type="button" class="btn btn-primary" onclick="calcularYGuardarCalorimetria()">
                <i class="fas fa-calculator me-2"></i> Calcular y Guardar
            </button>
        </div>
    </form>
</div>

<script>
// Variables globales para calorimetría
let actividadesDiarias = [];
let actividadesDeportes = [];
let calorimetriaGuardada = null;

// Cargar actividades al iniciar; la calorimetría guardada se carga después de tener la tabla de actividades
$(document).ready(function() {
    prellenarDatosPaciente();
    cargarActividades(); // dentro de su success se llama cargarCalorimetriaExistente()
});

function prellenarDatosPaciente() {
    <?php if (isset($cita) && $cita): ?>
    // Pre-llenar datos del paciente si están disponibles
    <?php if (isset($cita->genero) && $cita->genero): ?>
    $('#cal_sexo').val('<?= $cita->genero ?>');
    <?php endif; ?>
    
    <?php if (isset($cita->fecha_nacimiento) && $cita->fecha_nacimiento): ?>
    // Calcular edad desde fecha de nacimiento
    const fechaNac = new Date('<?= $cita->fecha_nacimiento ?>');
    const hoy = new Date();
    let edad = hoy.getFullYear() - fechaNac.getFullYear();
    const mes = hoy.getMonth() - fechaNac.getMonth();
    if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
        edad--;
    }
    $('#cal_edad').val(edad);
    <?php endif; ?>
    
    // Si hay historial con peso y altura, pre-llenarlos (soporta array u objeto)
    <?php
    $histPeso = isset($historial) ? (is_array($historial) ? ($historial['peso_actual'] ?? null) : ($historial->peso_actual ?? null)) : null;
    $histAltura = isset($historial) ? (is_array($historial) ? ($historial['altura_actual'] ?? null) : ($historial->altura_actual ?? null)) : null;
    ?>
    <?php if (!empty($histPeso)): ?>
    $('#cal_peso').val(<?= (float) $histPeso ?>);
    <?php endif; ?>
    <?php if (!empty($histAltura)): ?>
    $('#cal_talla').val(<?= (float) $histAltura ?>);
    <?php endif; ?>
    <?php endif; ?>
}

function cargarActividades() {
    $.ajax({
        url: '<?= base_url("dashboard/plan-alimentario/actividades") ?>',
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
            actividadesDiarias = response.actividades_diarias || [];
            actividadesDeportes = response.actividades_deportes || [];
            
            // Llenar tabla de actividades diarias
            let htmlDiarias = '';
            actividadesDiarias.forEach(function(act) {
                htmlDiarias += `
                    <tr>
                        <td>${act.nombre}</td>
                        <td>${act.mets}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm minutos-actividad" 
                                   data-actividad-id="${act.id}" data-tipo="diaria" 
                                   data-mets="${act.mets}" value="0" min="0">
                        </td>
                        <td class="calorias-hombre text-center">0</td>
                        <td class="calorias-mujer text-center">0</td>
                    </tr>
                `;
            });
            $('#tbodyActividadesDiarias').html(htmlDiarias);
            
            // Llenar tabla de deportes
            let htmlDeportes = '';
            actividadesDeportes.forEach(function(act) {
                htmlDeportes += `
                    <tr>
                        <td>${act.nombre}</td>
                        <td>${act.mets}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm minutos-actividad" 
                                   data-actividad-id="${act.id}" data-tipo="deporte" 
                                   data-mets="${act.mets}" value="0" min="0">
                        </td>
                        <td class="calorias-hombre text-center">0</td>
                        <td class="calorias-mujer text-center">0</td>
                    </tr>
                `;
            });
            $('#tbodyDeportes').html(htmlDeportes);
            
            // Event listeners para calcular en tiempo real
            $(document).on('input', '.minutos-actividad', function() {
                calcularCaloriasActividad($(this));
            });
            
            $(document).on('input', '#cal_peso, #cal_talla, #cal_edad, #cal_sexo', function() {
                calcularTMB();
            });
            // Cargar calorimetría guardada una vez existan los inputs de actividades
            cargarCalorimetriaExistente();
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar actividades:', error);
            console.error('Response:', xhr.responseText);
            let errorMsg = 'Error al cargar actividades';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMsg = xhr.responseJSON.error;
            }
            toastr.error(errorMsg);
        }
    });
}

function calcularTMB() {
    const peso = parseFloat($('#cal_peso').val()) || 0;
    const talla = parseFloat($('#cal_talla').val()) || 0;
    const edad = parseInt($('#cal_edad').val()) || 0;
    const sexo = $('#cal_sexo').val();
    
    if (peso > 0 && talla > 0 && edad > 0 && sexo) {
        // Fórmula Harris-Benedict (igual que planilla CALORIMETRIA)
        const tmb_hombres = 66 + (13.7 * peso) + (5 * talla) - (6.8 * edad);
        const tmb_mujeres = 655 + (9.6 * peso) + (1.8 * talla) - (4.7 * edad);
        const tmb_usado = (sexo == 'M') ? tmb_hombres : tmb_mujeres;
        // Guardar valores exactos para fórmula = planilla: Cal = METs × (min/60) × (TMB/24)
        $('#tmb_hombres_exact').val(tmb_hombres.toFixed(2));
        $('#tmb_mujeres_exact').val(tmb_mujeres.toFixed(2));
        $('#tmb_usado_exact').val(tmb_usado.toFixed(2));
        $('#tmb_hombres').text(Math.round(tmb_hombres));
        $('#tmb_mujeres').text(Math.round(tmb_mujeres));
        $('#tmb_usado').text(Math.round(tmb_usado));
        $('#tmbResults').show();
        
        // Recalcular todas las actividades usando TMB exacto
        $('.minutos-actividad').each(function() {
            calcularCaloriasActividad($(this), tmb_hombres, tmb_mujeres);
        });
        
        calcularTotalesCalorimetria();
    } else {
        $('#tmbResults').hide();
    }
}

function calcularCaloriasActividad($input, tmbHombres = null, tmbMujeres = null) {
    // Usar TMB exacto como en planilla (inputs ocultos). Si vienen por parámetro, ya son exactos.
    if (tmbHombres == null || tmbMujeres == null) {
        tmbHombres = parseFloat($('#tmb_hombres_exact').val()) || parseFloat($('#tmb_hombres').text()) || 0;
        tmbMujeres = parseFloat($('#tmb_mujeres_exact').val()) || parseFloat($('#tmb_mujeres').text()) || 0;
    }
    
    if (tmbHombres <= 0 || tmbMujeres <= 0) {
        return;
    }
    
    const minutos = parseFloat($input.val()) || 0;
    const mets = parseFloat($input.data('mets')) || 0;
    
    // Fórmula idéntica a planilla: Calorías = METs × (minutos/60) × (TMB/24)
    const calorias_hombre = mets * (minutos / 60) * (tmbHombres / 24);
    const calorias_mujer = mets * (minutos / 60) * (tmbMujeres / 24);
    
    const $row = $input.closest('tr');
    // Redondeo al entero más cercano (como Excel: 9.5→10, 9.4→9)
    $row.find('.calorias-hombre').text(Math.round(calorias_hombre));
    $row.find('.calorias-mujer').text(Math.round(calorias_mujer));
    
    calcularTotalesCalorimetria();
}

function calcularTotalesCalorimetria() {
    let total_minutos = 0;
    let cals_habituales_exacto = 0;
    let cals_entrenamiento_exacto = 0;
    // TMB exacto del paciente (planilla: Requerimiento = TMB + actividades de ese sexo)
    const tmbUsadoExact = parseFloat($('#tmb_usado_exact').val()) || parseFloat($('#tmb_usado').text()) || 0;
    
    $('#tablaActividadesDiarias .minutos-actividad').each(function() {
        const minutos = parseFloat($(this).val()) || 0;
        const mets = parseFloat($(this).data('mets')) || 0;
        total_minutos += minutos;
        cals_habituales_exacto += mets * (minutos / 60) * (tmbUsadoExact / 24);
    });
    
    $('#tablaDeportes .minutos-actividad').each(function() {
        const minutos = parseFloat($(this).val()) || 0;
        const mets = parseFloat($(this).data('mets')) || 0;
        total_minutos += minutos;
        cals_entrenamiento_exacto += mets * (minutos / 60) * (tmbUsadoExact / 24);
    });
    
    const tmb = tmbUsadoExact;
    const requerimiento_total = tmb + cals_habituales_exacto + cals_entrenamiento_exacto;
    
    $('#total_minutos').text(total_minutos);
    $('#cals_habituales').text(Math.round(cals_habituales_exacto));
    $('#cals_entrenamiento').text(Math.round(cals_entrenamiento_exacto));
    $('#requerimiento_total').text(Math.round(requerimiento_total));
    
    if (tmb > 0) {
        $('#totalesCalorimetria').show();
    }
}

function calcularYGuardarCalorimetria() {
    const detalleAgendaId = $('#detalle_agenda_id_cal').val();
    const peso = parseFloat($('#cal_peso').val());
    const talla = parseFloat($('#cal_talla').val());
    const edad = parseInt($('#cal_edad').val());
    const sexo = $('#cal_sexo').val();
    
    if (!peso || !talla || !edad || !sexo) {
        toastr.error('Por favor complete todos los campos');
        return;
    }
    
    // Recopilar actividades
    const actividades = [];
    $('.minutos-actividad').each(function() {
        const minutos = parseFloat($(this).val()) || 0;
        if (minutos > 0) {
            actividades.push({
                actividad_met_id: $(this).data('actividad-id'),
                minutos_dia: minutos
            });
        }
    });
    
    $.ajax({
        url: '<?= base_url("dashboard/plan-alimentario/calcular-calorimetria") ?>',
        method: 'POST',
        data: {
            detalle_agenda_id: detalleAgendaId,
            peso: peso,
            talla: talla,
            edad: edad,
            sexo: sexo,
            actividades: actividades
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                toastr.success('Calorimetría calculada y guardada correctamente');
                calorimetriaGuardada = response.calorimetria;
                
                // Actualizar requerimiento en Plan solo cuando no hay plan guardado en BD
                if (response.requerimiento_total) {
                    var tienePlan = (typeof planGuardado !== 'undefined' && planGuardado && planGuardado.requerimiento_kcal);
                    if (!tienePlan && $('#plan_requerimiento_kcal').length) {
                        $('#plan_requerimiento_kcal').val(Math.round(response.requerimiento_total));
                    }
                }
            } else {
                toastr.error(response.error || 'Error al guardar calorimetría');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            toastr.error('Error al calcular calorimetría');
        }
    });
}

function cargarCalorimetriaExistente() {
    const detalleAgendaId = ($('#detalle_agenda_id_cal').val() || '').toString().trim();
    if (!detalleAgendaId || detalleAgendaId === '0') return;
    
    $.ajax({
        url: '<?= base_url("dashboard/plan-alimentario/calorimetria") ?>/' + detalleAgendaId,
        method: 'GET',
        dataType: 'json',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(response) {
            if (response.error) {
                console.warn('Calorimetría:', response.error);
                return;
            }
            if (response.calorimetria) {
                const cal = response.calorimetria;
                $('#cal_peso').val(cal.peso);
                $('#cal_talla').val(cal.talla);
                $('#cal_edad').val(cal.edad);
                $('#cal_sexo').val(cal.sexo);
                
                calcularTMB();
                
                // Cargar actividades guardadas (actividad_met_id viene enriquecido desde el backend)
                if (cal.actividades && cal.actividades.length) {
                    cal.actividades.forEach(function(act) {
                        if (act.actividad_met_id) {
                            const $input = $(`.minutos-actividad[data-actividad-id="${act.actividad_met_id}"]`);
                            if ($input.length) {
                                $input.val(act.minutos_dia);
                                calcularCaloriasActividad($input);
                            }
                        }
                    });
                    calcularTotalesCalorimetria();
                }
                
                $('#tmbResults').show();
                if (parseFloat($('#tmb_usado').text()) > 0) {
                    $('#totalesCalorimetria').show();
                }
                calorimetriaGuardada = cal;
                // Pre-llenar Requerimiento Energético solo cuando no hay plan guardado en BD
                if (cal.requerimiento_total != null && $('#plan_requerimiento_kcal').length) {
                    var tienePlan = (typeof planGuardado !== 'undefined' && planGuardado && planGuardado.requerimiento_kcal);
                    var vacio = !$('#plan_requerimiento_kcal').val() || parseFloat($('#plan_requerimiento_kcal').val()) <= 0;
                    if (!tienePlan && vacio) {
                        $('#plan_requerimiento_kcal').val(Math.round(parseFloat(cal.requerimiento_total)));
                    }
                }
            }
        },
        error: function(xhr) {
            if (xhr.status === 404 || (xhr.responseJSON && xhr.responseJSON.calorimetria === null)) {
                return; // Sin calorimetría guardada, es normal
            }
            console.warn('Error al cargar calorimetría:', xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : xhr.statusText);
        }
    });
}

function limpiarCalorimetria() {
    $('#formCalorimetria')[0].reset();
    $('#tmb_hombres_exact, #tmb_mujeres_exact, #tmb_usado_exact').val('');
    $('#tmbResults').hide();
    $('#totalesCalorimetria').hide();
    $('.minutos-actividad').val(0);
    $('.calorias-hombre, .calorias-mujer').text('0');
    calorimetriaGuardada = null;
}
</script>

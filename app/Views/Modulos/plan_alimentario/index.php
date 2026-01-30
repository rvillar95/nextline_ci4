<?= $this->extend('layout/dashboard') ?>

<?= $this->section('plan_alimentario/index') ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #9C27B0;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-utensils me-2"></i> Plan Alimentario</h2>
                        <p style="color: white;">Cálculo de calorimetría, plan alimentario y distribución por comidas</p>
                    </div>
                </div>
            </div>

            <!-- Selección de Paciente y Consulta -->
            <div class="section-card">
                <h5 class="text-primary mb-3"><i class="fas fa-user me-2"></i> Seleccionar Paciente y Consulta</h5>
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Paciente <span class="text-danger">*</span></label>
                        <select class="form-select" id="selectPaciente" required>
                            <option value="">Seleccione un paciente</option>
                            <?php foreach ($pacientes as $paciente): ?>
                                <option value="<?= $paciente->id ?>"><?= esc($paciente->nombre_completo) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Consulta</label>
                        <select class="form-select" id="selectConsulta">
                            <option value="">Seleccione una consulta</option>
                        </select>
                        <small class="text-muted">O deje vacío para crear nueva consulta</small>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" class="btn btn-primary w-100" onclick="cargarPlanAlimentario()">
                            <i class="fas fa-search me-2"></i> Cargar / Crear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Contenido Principal (se muestra después de seleccionar paciente) -->
            <div id="contenidoPrincipal" style="display: none;">
                <!-- Información del Paciente -->
                <div class="section-card" id="infoPaciente">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-user me-2"></i> Información del Paciente</h6>
                            <p class="mb-1"><strong>Nombre:</strong> <span id="paciente_nombre">-</span></p>
                            <p class="mb-1"><strong>RUT/DNI:</strong> <span id="paciente_rut">-</span></p>
                            <p class="mb-0"><strong>Email:</strong> <span id="paciente_email">-</span></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-info"><i class="fas fa-calendar-alt me-2"></i> Consulta</h6>
                            <p class="mb-1"><strong>Fecha:</strong> <span id="consulta_fecha">-</span></p>
                            <p class="mb-0"><strong>Detalle ID:</strong> <span id="detalle_agenda_id_display">-</span></p>
                        </div>
                    </div>
                </div>

                <!-- Pestañas para Calorimetría, Plan y Distribución -->
                <div class="section-card">
                    <ul class="nav nav-tabs mb-3" id="planAlimentarioTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="calorimetria-tab" data-bs-toggle="tab" data-bs-target="#calorimetria" type="button" role="tab" aria-controls="calorimetria" aria-selected="true">
                                <i class="fas fa-fire me-2"></i> Calorimetría
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="plan-tab" data-bs-toggle="tab" data-bs-target="#plan" type="button" role="tab" aria-controls="plan" aria-selected="false">
                                <i class="fas fa-clipboard-list me-2"></i> Plan Alimentario
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="distribucion-tab" data-bs-toggle="tab" data-bs-target="#distribucion" type="button" role="tab" aria-controls="distribucion" aria-selected="false">
                                <i class="fas fa-utensils me-2"></i> Distribución por Comidas
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="planAlimentarioTabContent">
                        <!-- Tab: Calorimetría -->
                        <div class="tab-pane fade show active" id="calorimetria" role="tabpanel" aria-labelledby="calorimetria-tab">
                            <div id="calorimetriaContent">
                                <!-- Se cargará dinámicamente -->
                            </div>
                        </div>

                        <!-- Tab: Plan Alimentario -->
                        <div class="tab-pane fade" id="plan" role="tabpanel" aria-labelledby="plan-tab">
                            <div id="planContent">
                                <!-- Se cargará dinámicamente -->
                            </div>
                        </div>

                        <!-- Tab: Distribución por Comidas -->
                        <div class="tab-pane fade" id="distribucion" role="tabpanel" aria-labelledby="distribucion-tab">
                            <div id="distribucionContent">
                                <!-- Se cargará dinámicamente -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let detalleAgendaIdActual = null;
let pacienteIdActual = null;
let pacienteData = null;

$(document).ready(function() {
    // Cargar consultas cuando se selecciona un paciente
    $('#selectPaciente').on('change', function() {
        const pacienteId = $(this).val();
        if (pacienteId) {
            cargarConsultasPaciente(pacienteId);
        } else {
            $('#selectConsulta').html('<option value="">Seleccione una consulta</option>');
        }
    });
});

function cargarConsultasPaciente(pacienteId) {
    $.ajax({
        url: '<?= base_url("dashboard/agenda/getAgendas") ?>',
        method: 'GET',
        data: {
            paciente_id: pacienteId,
            draw: 1,
            start: 0,
            length: 100
        },
        dataType: 'json',
        success: function(response) {
            let html = '<option value="">Nueva consulta</option>';
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(consulta) {
                    const fecha = consulta.fecha || consulta.fecha_agenda || '';
                    const hora = consulta.hora_inicio || '';
                    html += `<option value="${consulta.id}">${fecha} ${hora} - ${consulta.tipo_consulta || 'Consulta'}</option>`;
                });
            }
            $('#selectConsulta').html(html);
        },
        error: function() {
            $('#selectConsulta').html('<option value="">Nueva consulta</option>');
        }
    });
}

function cargarPlanAlimentario() {
    const pacienteId = $('#selectPaciente').val();
    const consultaId = $('#selectConsulta').val();
    
    if (!pacienteId) {
        toastr.error('Debe seleccionar un paciente');
        return;
    }
    
    // Obtener datos del paciente desde el select
    const pacienteOption = $('#selectPaciente option:selected');
    if (pacienteOption.length && pacienteOption.val()) {
        // Extraer datos del texto del option o hacer petición AJAX
        const pacienteTexto = pacienteOption.text();
        pacienteIdActual = pacienteId;
        
        // Obtener datos completos del paciente
        $.ajax({
            url: '<?= base_url("dashboard/plan-alimentario/get-paciente-data") ?>',
            method: 'GET',
            data: { paciente_id: pacienteId },
            dataType: 'json',
            success: function(response) {
                if (response.paciente) {
                    pacienteData = response.paciente;
                    
                    // Si hay consulta seleccionada, usar su detalle_agenda_id
                    if (consultaId) {
                        detalleAgendaIdActual = consultaId;
                        // Obtener fecha de la consulta
                        const consultaOption = $('#selectConsulta option:selected');
                        const consultaTexto = consultaOption.text();
                        $('#consulta_fecha').text(consultaTexto.split(' - ')[0] || '-');
                    } else {
                        // Crear nueva consulta o usar una existente
                        detalleAgendaIdActual = null; // Se creará al guardar
                        $('#consulta_fecha').text('Nueva consulta');
                    }
                    
                    // Mostrar información
                    $('#paciente_nombre').text((pacienteData.nombre || '') + ' ' + (pacienteData.apellido || ''));
                    $('#paciente_rut').text(pacienteData.rut_dni || '-');
                    $('#paciente_email').text(pacienteData.email || '-');
                    $('#detalle_agenda_id_display').text(detalleAgendaIdActual || 'Nueva');
                    
                    // Mostrar contenido principal
                    $('#contenidoPrincipal').show();
                    
                    // Cargar las vistas de las pestañas
                    cargarVistaCalorimetria();
                    cargarVistaPlan();
                    cargarVistaDistribucion();
                }
            },
            error: function() {
                toastr.error('Error al cargar datos del paciente');
            }
        });
    }
}

function cargarVistaCalorimetria() {
    $('#calorimetriaContent').load('<?= base_url("dashboard/plan-alimentario/vista-calorimetria") ?>', {
        paciente_id: pacienteIdActual,
        detalle_agenda_id: detalleAgendaIdActual
    });
}

function cargarVistaPlan() {
    $('#planContent').load('<?= base_url("dashboard/plan-alimentario/vista-plan") ?>', {
        paciente_id: pacienteIdActual,
        detalle_agenda_id: detalleAgendaIdActual
    });
}

function cargarVistaDistribucion() {
    $('#distribucionContent').load('<?= base_url("dashboard/plan-alimentario/vista-distribucion") ?>', {
        paciente_id: pacienteIdActual,
        detalle_agenda_id: detalleAgendaIdActual
    });
}
</script>

<?= $this->endSection() ?>

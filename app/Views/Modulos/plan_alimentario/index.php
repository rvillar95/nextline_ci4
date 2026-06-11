<?= $this->extend('layout/dashboard') ?>

<?= $this->section('plan_alimentario/index') ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="<?= base_url('lib/js/toast-guardado.js') ?>"></script>

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
                                <option value="<?= $paciente->id ?>" <?= !empty($autoPacienteId) && (int) $autoPacienteId === (int) $paciente->id ? 'selected' : '' ?>>
                                    <?= esc($paciente->nombre_completo) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Consulta</label>
                        <select class="form-select" id="selectConsulta">
                            <option value="">Nueva consulta</option>
                            <?php
                            $consultaEnLista = false;
                            foreach ($consultasPreload ?? [] as $consulta):
                                $sel = !empty($detalleAgendaIdActivo) && (int) $detalleAgendaIdActivo === (int) $consulta['id'];
                                if ($sel) {
                                    $consultaEnLista = true;
                                }
                                $hora = !empty($consulta['hora_inicio']) ? substr((string) $consulta['hora_inicio'], 0, 5) : '';
                                $tipo = str_replace('_', ' ', $consulta['tipo_consulta'] ?? 'consulta');
                            ?>
                                <option value="<?= (int) $consulta['id'] ?>" <?= $sel ? 'selected' : '' ?>>
                                    <?= esc(($consulta['fecha_agenda'] ?? '') . ' ' . $hora . ' - ' . $tipo) ?>
                                </option>
                            <?php endforeach; ?>
                            <?php if (!empty($detalleAgendaIdActivo) && !$consultaEnLista): ?>
                                <option value="<?= (int) $detalleAgendaIdActivo ?>" selected>
                                    Consulta #<?= (int) $detalleAgendaIdActivo ?>
                                </option>
                            <?php endif; ?>
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
            <?php
            $tabActivo = !empty($mostrarContenido) ? ($autoTab ?? 'plan') : 'calorimetria';
            $consultaFechaTxt = 'Nueva consulta';
            if (!empty($detalleAgendaIdActivo) && !empty($consultasPreload)) {
                foreach ($consultasPreload as $cPre) {
                    if ((int) $cPre['id'] === (int) $detalleAgendaIdActivo) {
                        $horaPre = !empty($cPre['hora_inicio']) ? substr((string) $cPre['hora_inicio'], 0, 5) : '';
                        $consultaFechaTxt = trim(($cPre['fecha_agenda'] ?? '') . ' ' . $horaPre);
                        break;
                    }
                }
            } elseif (!empty($detalleAgendaIdActivo)) {
                $consultaFechaTxt = 'Consulta #' . (int) $detalleAgendaIdActivo;
            }
            ?>
            <div id="contenidoPrincipal" style="display: <?= !empty($mostrarContenido) ? 'block' : 'none' ?>;">
                <!-- Información del Paciente -->
                <div class="section-card" id="infoPaciente">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-user me-2"></i> Información del Paciente</h6>
                            <p class="mb-1"><strong>Nombre:</strong> <span id="paciente_nombre"><?= !empty($pacientePrecargado) ? esc(trim(($pacientePrecargado->nombre ?? '') . ' ' . ($pacientePrecargado->apellido ?? ''))) : '-' ?></span></p>
                            <p class="mb-1"><strong>RUT/DNI:</strong> <span id="paciente_rut"><?= !empty($pacientePrecargado->rut_dni) ? esc($pacientePrecargado->rut_dni) : '-' ?></span></p>
                            <p class="mb-0"><strong>Email:</strong> <span id="paciente_email"><?= !empty($pacientePrecargado->email) ? esc($pacientePrecargado->email) : '-' ?></span></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-info"><i class="fas fa-calendar-alt me-2"></i> Consulta</h6>
                            <p class="mb-1"><strong>Fecha:</strong> <span id="consulta_fecha"><?= esc($consultaFechaTxt) ?></span></p>
                            <p class="mb-0"><strong>Detalle ID:</strong> <span id="detalle_agenda_id_display"><?= !empty($detalleAgendaIdActivo) ? (int) $detalleAgendaIdActivo : 'Nueva' ?></span></p>
                        </div>
                    </div>
                </div>

                <!-- Pestañas para Calorimetría, Plan y Distribución -->
                <div class="section-card">
                    <ul class="nav nav-tabs mb-3" id="planAlimentarioTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= $tabActivo === 'calorimetria' ? 'active' : '' ?>" id="calorimetria-tab" data-bs-toggle="tab" data-bs-target="#calorimetria" type="button" role="tab" aria-controls="calorimetria" aria-selected="<?= $tabActivo === 'calorimetria' ? 'true' : 'false' ?>">
                                <i class="fas fa-fire me-2"></i> Calorimetría
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= $tabActivo === 'plan' ? 'active' : '' ?>" id="plan-tab" data-bs-toggle="tab" data-bs-target="#plan" type="button" role="tab" aria-controls="plan" aria-selected="<?= $tabActivo === 'plan' ? 'true' : 'false' ?>">
                                <i class="fas fa-clipboard-list me-2"></i> Plan Alimentario
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= $tabActivo === 'distribucion' ? 'active' : '' ?>" id="distribucion-tab" data-bs-toggle="tab" data-bs-target="#distribucion" type="button" role="tab" aria-controls="distribucion" aria-selected="<?= $tabActivo === 'distribucion' ? 'true' : 'false' ?>">
                                <i class="fas fa-utensils me-2"></i> Distribución por Comidas
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="planAlimentarioTabContent">
                        <!-- Tab: Calorimetría -->
                        <div class="tab-pane fade <?= $tabActivo === 'calorimetria' ? 'show active' : '' ?>" id="calorimetria" role="tabpanel" aria-labelledby="calorimetria-tab">
                            <div id="calorimetriaContent">
                                <?= $htmlCalorimetria ?? '' ?>
                            </div>
                        </div>

                        <!-- Tab: Plan Alimentario -->
                        <div class="tab-pane fade <?= $tabActivo === 'plan' ? 'show active' : '' ?>" id="plan" role="tabpanel" aria-labelledby="plan-tab">
                            <div id="planContent">
                                <?= $htmlPlan ?? '' ?>
                            </div>
                        </div>

                        <!-- Tab: Distribución por Comidas -->
                        <div class="tab-pane fade <?= $tabActivo === 'distribucion' ? 'show active' : '' ?>" id="distribucion" role="tabpanel" aria-labelledby="distribucion-tab">
                            <div id="distribucionContent">
                                <?= $htmlDistribucion ?? '' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($mostrarContenido)): ?>
<script>
window.PLAN_PRECARGADO = true;
window.PLAN_AUTO_PACIENTE_ID = <?= (int) ($autoPacienteId ?? 0) ?>;
window.PLAN_AUTO_DETALLE_ID = <?= !empty($detalleAgendaIdActivo) ? (int) $detalleAgendaIdActivo : 0 ?>;
</script>
<?php elseif (!empty($autoPacienteId)): ?>
<script>
window.PLAN_AUTO_PACIENTE_ID = <?= (int) $autoPacienteId ?>;
window.PLAN_AUTO_DETALLE_ID = <?= !empty($autoDetalleAgendaId) ? (int) $autoDetalleAgendaId : 0 ?>;
</script>
<?php endif; ?>

<script>
let detalleAgendaIdActual = <?= !empty($detalleAgendaIdActivo) ? (int) $detalleAgendaIdActivo : 'null' ?>;
let pacienteIdActual = <?= !empty($autoPacienteId) ? (int) $autoPacienteId : 'null' ?>;
let pacienteData = null;

function urlVistaPlan(basePath, pacienteId, detalleId) {
    var url = basePath + '?paciente_id=' + encodeURIComponent(pacienteId);
    if (detalleId) {
        url += '&detalle_agenda_id=' + encodeURIComponent(detalleId);
    }
    return url;
}

function leerParamsPlanUrl() {
    var params = new URLSearchParams(window.location.search);
    var pacienteId = parseInt(params.get('paciente_id') || '0', 10);
    var detalleId = parseInt(params.get('detalle_agenda_id') || '0', 10);
    var tab = params.get('tab') || 'plan';
    if (pacienteId <= 0 && typeof window.PLAN_AUTO_PACIENTE_ID !== 'undefined') {
        pacienteId = window.PLAN_AUTO_PACIENTE_ID;
    }
    if (detalleId <= 0 && typeof window.PLAN_AUTO_DETALLE_ID !== 'undefined') {
        detalleId = window.PLAN_AUTO_DETALLE_ID;
    }
    return {
        pacienteId: pacienteId,
        detalleAgendaId: detalleId > 0 ? detalleId : null,
        tab: tab
    };
}

function initPlanAlimentarioPage() {
    if (typeof window.jQuery === 'undefined') {
        console.error('jQuery no disponible en plan alimentario');
        return;
    }
    var $ = window.jQuery;

    $('#selectPaciente').on('change', function() {
        var pacienteId = $(this).val();
        if (pacienteId) {
            cargarConsultasPaciente(pacienteId);
        } else {
            $('#selectConsulta').html('<option value="">Nueva consulta</option>');
        }
    });

    if (window.PLAN_PRECARGADO) {
        return;
    }

    var p = leerParamsPlanUrl();
    if (p.pacienteId > 0) {
        abrirPlanDesdeUrl(p.pacienteId, p.detalleAgendaId, p.tab);
    }
}

if (typeof window.jQuery !== 'undefined') {
    window.jQuery(document).ready(initPlanAlimentarioPage);
} else {
    document.addEventListener('DOMContentLoaded', initPlanAlimentarioPage);
}

function abrirPlanDesdeUrl(pacienteId, detalleAgendaId, tab) {
    if ($('#selectPaciente option[value="' + pacienteId + '"]').length === 0) {
        $('#selectPaciente').append('<option value="' + pacienteId + '">Paciente #' + pacienteId + '</option>');
    }
    $('#selectPaciente').val(String(pacienteId));

    cargarConsultasPaciente(pacienteId, function() {
        if (detalleAgendaId) {
            if ($('#selectConsulta option[value="' + detalleAgendaId + '"]').length === 0) {
                $('#selectConsulta').append(
                    '<option value="' + detalleAgendaId + '">Consulta #' + detalleAgendaId + '</option>'
                );
            }
            $('#selectConsulta').val(String(detalleAgendaId));
        }
        cargarPlanAlimentario(function() {
            if (tab && tab !== 'calorimetria') {
                var tabBtn = document.querySelector('#planAlimentarioTabs button[data-bs-target="#' + tab + '"]');
                if (tabBtn && typeof bootstrap !== 'undefined') {
                    bootstrap.Tab.getOrCreateInstance(tabBtn).show();
                }
            }
        }, pacienteId, detalleAgendaId);
    });
}

function cargarConsultasPaciente(pacienteId, onDone) {
    $.ajax({
        url: '<?= base_url("dashboard/plan-alimentario/consultas-paciente") ?>',
        method: 'GET',
        data: { paciente_id: pacienteId },
        dataType: 'json',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(response) {
            var html = '<option value="">Nueva consulta</option>';
            if (response.success && response.consultas && response.consultas.length > 0) {
                response.consultas.forEach(function(consulta) {
                    var fecha = consulta.fecha_agenda || '';
                    var hora = consulta.hora_inicio ? String(consulta.hora_inicio).substring(0, 5) : '';
                    var tipo = (consulta.tipo_consulta || 'consulta').replace(/_/g, ' ');
                    html += '<option value="' + consulta.id + '">' + fecha + ' ' + hora + ' - ' + tipo + '</option>';
                });
            }
            $('#selectConsulta').html(html);
            if (typeof onDone === 'function') onDone();
        },
        error: function(xhr) {
            console.error('consultas-paciente', xhr.status, xhr.responseText);
            $('#selectConsulta').html('<option value="">Nueva consulta</option>');
            if (typeof onDone === 'function') onDone();
        }
    });
}

function cargarPlanAlimentario(onDone, pacienteIdForzado, detalleAgendaForzado) {
    var pacienteId = pacienteIdForzado || $('#selectPaciente').val();
    var consultaId = (detalleAgendaForzado !== undefined && detalleAgendaForzado !== null)
        ? detalleAgendaForzado
        : ($('#selectConsulta').val() || null);

    if (!pacienteId) {
        if (typeof toastr !== 'undefined') toastr.error('Debe seleccionar un paciente');
        return;
    }

    pacienteIdActual = pacienteId;

    $.ajax({
        url: '<?= base_url("dashboard/plan-alimentario/get-paciente-data") ?>',
        method: 'GET',
        data: { paciente_id: pacienteId },
        dataType: 'json',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(response) {
            if (!response.paciente) {
                if (typeof toastr !== 'undefined') toastr.error('Paciente no encontrado');
                return;
            }
            pacienteData = response.paciente;

            if (consultaId) {
                detalleAgendaIdActual = consultaId;
                var consultaOption = $('#selectConsulta option:selected');
                var consultaTexto = consultaOption.length ? consultaOption.text() : ('Consulta #' + consultaId);
                $('#consulta_fecha').text(consultaTexto.split(' - ')[0] || consultaTexto);
            } else {
                detalleAgendaIdActual = null;
                $('#consulta_fecha').text('Nueva consulta');
            }

            $('#paciente_nombre').text(((pacienteData.nombre || '') + ' ' + (pacienteData.apellido || '')).trim());
            $('#paciente_rut').text(pacienteData.rut_dni || '-');
            $('#paciente_email').text(pacienteData.email || '-');
            $('#detalle_agenda_id_display').text(detalleAgendaIdActual || 'Nueva');

            $('#contenidoPrincipal').show();
            cargarVistaCalorimetria();
            cargarVistaPlan();
            cargarVistaDistribucion();
            if (typeof onDone === 'function') onDone();
        },
        error: function(xhr) {
            console.error('get-paciente-data', xhr.status, xhr.responseText);
            if (typeof toastr !== 'undefined') toastr.error('Error al cargar datos del paciente (¿permisos?)');
            if (typeof onDone === 'function') onDone();
        }
    });
}

function cargarVistaCalorimetria() {
    if (!pacienteIdActual) return;
    jQuery('#calorimetriaContent').load(urlVistaPlan(
        '<?= base_url("dashboard/plan-alimentario/vista-calorimetria") ?>',
        pacienteIdActual,
        detalleAgendaIdActual
    ));
}

function cargarVistaPlan() {
    if (!pacienteIdActual) return;
    jQuery('#planContent').load(urlVistaPlan(
        '<?= base_url("dashboard/plan-alimentario/vista-plan") ?>',
        pacienteIdActual,
        detalleAgendaIdActual
    ));
}

function cargarVistaDistribucion() {
    if (!pacienteIdActual) return;
    jQuery('#distribucionContent').load(urlVistaPlan(
        '<?= base_url("dashboard/plan-alimentario/vista-distribucion") ?>',
        pacienteIdActual,
        detalleAgendaIdActual
    ));
}
</script>

<?= $this->endSection() ?>

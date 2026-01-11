<?= $this->extend('layout/dashboard') ?>

<?= $this->section('historial/comparar') ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
    .main-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        color: white;
    }
    
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #667eea;
    }
    
    .historial-item {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .historial-item:hover {
        border-color: #667eea;
        background-color: #f8f9fa;
    }
    
    .historial-item.selected {
        border-color: #667eea;
        background-color: #e7edff;
    }
    
    .chart-container {
        position: relative;
        height: 400px;
        margin-bottom: 30px;
    }
    
    .comparison-table {
        font-size: 0.9rem;
    }
    
    /* Headers con fondo azul y texto blanco */
    .comparison-table thead th,
    .comparison-table > thead > tr > th {
        background-color: #667eea !important;
        color: #ffffff !important;
        font-weight: 600;
        border-color: #667eea !important;
    }
    
    /* Celdas de datos con texto oscuro */
    .comparison-table tbody td,
    .comparison-table > tbody > tr > td {
        color: #212529 !important;
        background-color: #ffffff !important;
        border-color: #dee2e6 !important;
    }
    
    /* Filas de datos */
    .comparison-table tbody tr {
        background-color: #ffffff !important;
    }
    
    /* Hover en filas */
    .comparison-table tbody tr:hover {
        background-color: #f8f9fa !important;
    }
    
    .comparison-table tbody tr:hover td {
        background-color: #f8f9fa !important;
        color: #212529 !important;
    }
    
    .badge-comparison {
        font-size: 0.85rem;
        padding: 5px 10px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2><i class="fas fa-chart-line me-2"></i> Comparación de Historiales Clínicos</h2>
                        <p class="mb-0">Seleccione un paciente y compare múltiples consultas para ver la evolución</p>
                    </div>
                    <a href="<?= base_url('dashboard/historial/lista') ?>" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i> Volver
                    </a>
                </div>
            </div>

            <!-- Paso 1: Seleccionar Paciente -->
            <div class="section-card">
                <h5 class="mb-4"><i class="fas fa-user me-2"></i> Paso 1: Seleccionar Paciente</h5>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Paciente</label>
                        <select id="selectPaciente" class="form-select form-select-lg">
                            <option value="">-- Seleccione un paciente --</option>
                            <?php foreach ($pacientes as $paciente): ?>
                                <option value="<?= $paciente['id'] ?>">
                                    <?= esc($paciente['nombre'] . ' ' . $paciente['apellido']) ?> 
                                    (<?= $paciente['total_historiales'] ?> consulta<?= $paciente['total_historiales'] > 1 ? 's' : '' ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Paso 2: Seleccionar Historiales (oculto hasta seleccionar paciente) -->
            <div class="section-card" id="seccionHistoriales" style="display: none;">
                <h5 class="mb-4"><i class="fas fa-list me-2"></i> Paso 2: Seleccionar Historiales para Comparar</h5>
                <p class="text-muted mb-3">Seleccione al menos 2 historiales haciendo clic en ellos. Los seleccionados se resaltarán.</p>
                <div id="listaHistoriales" class="row">
                    <!-- Se llenará dinámicamente -->
                </div>
                <div class="mt-3">
                    <button id="btnComparar" class="btn btn-primary btn-lg" disabled>
                        <i class="fas fa-chart-bar me-2"></i> Comparar Historiales Seleccionados
                    </button>
                    <button id="btnLimpiar" class="btn btn-outline-secondary btn-lg ms-2">
                        <i class="fas fa-eraser me-2"></i> Limpiar Selección
                    </button>
                </div>
            </div>

            <!-- Resultados de Comparación (oculto hasta comparar) -->
            <div id="seccionComparacion" style="display: none;">
                <!-- Gráficos -->
                <div class="section-card">
                    <h5 class="mb-4"><i class="fas fa-chart-area me-2"></i> Gráficos de Evolución</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="chart-container">
                                <canvas id="chartPeso"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="chart-container">
                                <canvas id="chartIMC"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="chart-container">
                                <canvas id="chartCircunferencias"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="chart-container">
                                <canvas id="chartGrasa"></canvas>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="chart-container">
                                <canvas id="chartPliegues"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla Comparativa -->
                <div class="section-card">
                    <h5 class="mb-4"><i class="fas fa-table me-2"></i> Tabla Comparativa Detallada</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered comparison-table" id="tablaComparacion">
                            <thead>
                                <tr>
                                    <th style="background-color: #667eea !important; color: #ffffff !important; border-color: #667eea !important;">Fecha</th>
                                    <th style="background-color: #667eea !important; color: #ffffff !important; border-color: #667eea !important;">Peso (kg)</th>
                                    <th style="background-color: #667eea !important; color: #ffffff !important; border-color: #667eea !important;">IMC</th>
                                    <th style="background-color: #667eea !important; color: #ffffff !important; border-color: #667eea !important;">Cintura (cm)</th>
                                    <th style="background-color: #667eea !important; color: #ffffff !important; border-color: #667eea !important;">Cadera (cm)</th>
                                    <th style="background-color: #667eea !important; color: #ffffff !important; border-color: #667eea !important;">Grasa (%)</th>
                                    <th style="background-color: #667eea !important; color: #ffffff !important; border-color: #667eea !important;">Masa Muscular (kg)</th>
                                    <th style="background-color: #667eea !important; color: #ffffff !important; border-color: #667eea !important;">Suma Pliegues (mm)</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyComparacion">
                                <!-- Se llenará dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Análisis de Cambios -->
                <div class="section-card">
                    <h5 class="mb-4"><i class="fas fa-calculator me-2"></i> Análisis de Cambios</h5>
                    <div id="analisisCambios" class="row">
                        <!-- Se llenará dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let historialesDisponibles = [];
let historialesSeleccionados = [];
let charts = {};

$(document).ready(function() {
    // Seleccionar paciente
    $('#selectPaciente').on('change', function() {
        const pacienteId = $(this).val();
        if (pacienteId) {
            cargarHistoriales(pacienteId);
        } else {
            $('#seccionHistoriales').hide();
            $('#seccionComparacion').hide();
        }
    });

    // Limpiar selección
    $('#btnLimpiar').on('click', function() {
        historialesSeleccionados = [];
        actualizarListaHistoriales();
        $('#btnComparar').prop('disabled', true);
    });

    // Comparar historiales
    $('#btnComparar').on('click', function() {
        if (historialesSeleccionados.length < 2) {
            alert('Debe seleccionar al menos 2 historiales para comparar');
            return;
        }
        compararHistoriales();
    });
});

function cargarHistoriales(pacienteId) {
    $.ajax({
        url: '<?= base_url('dashboard/historial/getHistorialesPaciente') ?>',
        type: 'GET',
        data: { paciente_id: pacienteId },
        dataType: 'json',
        success: function(response) {
            historialesDisponibles = response;
            historialesSeleccionados = [];
            actualizarListaHistoriales();
            $('#seccionHistoriales').show();
            $('#seccionComparacion').hide();
        },
        error: function() {
            alert('Error al cargar historiales');
        }
    });
}

function actualizarListaHistoriales() {
    const $lista = $('#listaHistoriales');
    $lista.empty();

    if (historialesDisponibles.length === 0) {
        $lista.html('<div class="col-12"><div class="alert alert-info">No hay historiales disponibles para este paciente.</div></div>');
        return;
    }

    historialesDisponibles.forEach(function(historial) {
        const fecha = historial.fecha_consulta || historial.fecha_detalle || 'N/A';
        const hora = historial.hora_consulta || historial.hora_inicio || '';
        const tipo = historial.tipo_registro || 'consulta';
        const peso = historial.peso_actual ? parseFloat(historial.peso_actual).toFixed(1) + ' kg' : 'N/A';
        const imc = historial.imc_actual ? parseFloat(historial.imc_actual).toFixed(2) : 'N/A';
        
        const isSelected = historialesSeleccionados.includes(historial.id);
        const badgeClass = isSelected ? 'bg-primary' : 'bg-secondary';
        
        const $item = $(`
            <div class="col-md-6 col-lg-4">
                <div class="historial-item ${isSelected ? 'selected' : ''}" data-id="${historial.id}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-2">
                                <span class="badge ${badgeClass} badge-comparison me-2">${tipo}</span>
                                ${fecha}
                            </h6>
                            <p class="mb-1 text-muted small">${hora ? hora.substring(0, 5) : ''}</p>
                            <div class="mt-2">
                                <span class="badge bg-info me-1">Peso: ${peso}</span>
                                <span class="badge bg-success">IMC: ${imc}</span>
                            </div>
                        </div>
                        <div>
                            <i class="fas fa-check-circle text-primary ${isSelected ? '' : 'd-none'}" id="check-${historial.id}"></i>
                        </div>
                    </div>
                </div>
            </div>
        `);

        $item.find('.historial-item').on('click', function() {
            toggleHistorial(historial.id);
        });

        $lista.append($item);
    });
}

function toggleHistorial(historialId) {
    const index = historialesSeleccionados.indexOf(historialId);
    if (index > -1) {
        historialesSeleccionados.splice(index, 1);
    } else {
        historialesSeleccionados.push(historialId);
    }
    
    actualizarListaHistoriales();
    $('#btnComparar').prop('disabled', historialesSeleccionados.length < 2);
}

function compararHistoriales() {
    $.ajax({
        url: '<?= base_url('dashboard/historial/compararHistoriales') ?>',
        type: 'POST',
        data: {
            historial_ids: historialesSeleccionados,
            csrf_test_name: $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                alert(response.error);
                return;
            }
            mostrarComparacion(response);
        },
        error: function() {
            alert('Error al comparar historiales');
        }
    });
}

function mostrarComparacion(historiales) {
    $('#seccionComparacion').show();
    $('html, body').animate({ scrollTop: $('#seccionComparacion').offset().top - 100 }, 500);

    // Preparar datos para gráficos
    const fechas = historiales.map(h => h.fecha_consulta || h.fecha_detalle || 'N/A');
    const pesos = historiales.map(h => h.peso_actual ? parseFloat(h.peso_actual) : null);
    const imcs = historiales.map(h => h.imc_actual ? parseFloat(h.imc_actual) : null);
    const cinturas = historiales.map(h => h.circunferencia_cintura ? parseFloat(h.circunferencia_cintura) : null);
    const caderas = historiales.map(h => h.circunferencia_cadera ? parseFloat(h.circunferencia_cadera) : null);
    const grasas = historiales.map(h => h.grasa_corporal ? parseFloat(h.grasa_corporal) : null);
    const pliegues = historiales.map(h => h.suma_pliegues ? parseFloat(h.suma_pliegues) : null);

    // Crear gráficos
    crearGrafico('chartPeso', 'Evolución del Peso', fechas, pesos, 'kg', 'rgba(54, 162, 235, 0.6)');
    crearGrafico('chartIMC', 'Evolución del IMC', fechas, imcs, '', 'rgba(255, 99, 132, 0.6)');
    crearGraficoDual('chartCircunferencias', 'Evolución de Circunferencias', fechas, cinturas, caderas, 'cm');
    crearGrafico('chartGrasa', 'Evolución de Grasa Corporal', fechas, grasas, '%', 'rgba(255, 206, 86, 0.6)');
    crearGrafico('chartPliegues', 'Evolución de Suma de Pliegues', fechas, pliegues, 'mm', 'rgba(75, 192, 192, 0.6)');

    // Llenar tabla
    llenarTablaComparacion(historiales);

    // Análisis de cambios
    mostrarAnalisisCambios(historiales);
}

function crearGrafico(canvasId, titulo, labels, data, unidad, color) {
    const ctx = document.getElementById(canvasId);
    if (charts[canvasId]) {
        charts[canvasId].destroy();
    }
    
    charts[canvasId] = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: titulo,
                data: data,
                borderColor: color.replace('0.6', '1'),
                backgroundColor: color,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: titulo,
                    font: { size: 16, weight: 'bold' }
                },
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + (context.parsed.y !== null ? context.parsed.y.toFixed(2) + ' ' + unidad : 'N/A');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    title: {
                        display: true,
                        text: unidad
                    }
                }
            }
        }
    });
}

function crearGraficoDual(canvasId, titulo, labels, data1, data2, unidad) {
    const ctx = document.getElementById(canvasId);
    if (charts[canvasId]) {
        charts[canvasId].destroy();
    }
    
    charts[canvasId] = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Cintura',
                    data: data1,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Cadera',
                    data: data2,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: titulo,
                    font: { size: 16, weight: 'bold' }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + (context.parsed.y !== null ? context.parsed.y.toFixed(1) + ' ' + unidad : 'N/A');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    title: {
                        display: true,
                        text: unidad
                    }
                }
            }
        }
    });
}

function llenarTablaComparacion(historiales) {
    const $tbody = $('#tbodyComparacion');
    $tbody.empty();

    historiales.forEach(function(h) {
        const fecha = h.fecha_consulta || h.fecha_detalle || 'N/A';
        const peso = h.peso_actual ? parseFloat(h.peso_actual).toFixed(1) : '-';
        const imc = h.imc_actual ? parseFloat(h.imc_actual).toFixed(2) : '-';
        const cintura = h.circunferencia_cintura ? parseFloat(h.circunferencia_cintura).toFixed(1) : '-';
        const cadera = h.circunferencia_cadera ? parseFloat(h.circunferencia_cadera).toFixed(1) : '-';
        const grasa = h.grasa_corporal ? parseFloat(h.grasa_corporal).toFixed(1) : '-';
        const masa = h.masa_muscular ? parseFloat(h.masa_muscular).toFixed(1) : '-';
        const pliegues = h.suma_pliegues ? parseFloat(h.suma_pliegues).toFixed(2) : '-';

        $tbody.append(`
            <tr>
                <td style="color: #212529 !important; background-color: #ffffff !important;">${fecha}</td>
                <td style="color: #212529 !important; background-color: #ffffff !important;">${peso}</td>
                <td style="color: #212529 !important; background-color: #ffffff !important;">${imc}</td>
                <td style="color: #212529 !important; background-color: #ffffff !important;">${cintura}</td>
                <td style="color: #212529 !important; background-color: #ffffff !important;">${cadera}</td>
                <td style="color: #212529 !important; background-color: #ffffff !important;">${grasa}</td>
                <td style="color: #212529 !important; background-color: #ffffff !important;">${masa}</td>
                <td style="color: #212529 !important; background-color: #ffffff !important;">${pliegues}</td>
            </tr>
        `);
    });
}

function mostrarAnalisisCambios(historiales) {
    if (historiales.length < 2) return;

    const primero = historiales[0];
    const ultimo = historiales[historiales.length - 1];
    
    const cambios = [];
    
    if (primero.peso_actual && ultimo.peso_actual) {
        const cambio = parseFloat(ultimo.peso_actual) - parseFloat(primero.peso_actual);
        cambios.push({
            parametro: 'Peso',
            inicial: primero.peso_actual,
            final: ultimo.peso_actual,
            cambio: cambio,
            porcentaje: ((cambio / parseFloat(primero.peso_actual)) * 100).toFixed(1),
            unidad: 'kg'
        });
    }

    if (primero.imc_actual && ultimo.imc_actual) {
        const cambio = parseFloat(ultimo.imc_actual) - parseFloat(primero.imc_actual);
        cambios.push({
            parametro: 'IMC',
            inicial: primero.imc_actual,
            final: ultimo.imc_actual,
            cambio: cambio,
            porcentaje: ((cambio / parseFloat(primero.imc_actual)) * 100).toFixed(1),
            unidad: ''
        });
    }

    if (primero.circunferencia_cintura && ultimo.circunferencia_cintura) {
        const cambio = parseFloat(ultimo.circunferencia_cintura) - parseFloat(primero.circunferencia_cintura);
        cambios.push({
            parametro: 'Circunferencia Cintura',
            inicial: primero.circunferencia_cintura,
            final: ultimo.circunferencia_cintura,
            cambio: cambio,
            porcentaje: ((cambio / parseFloat(primero.circunferencia_cintura)) * 100).toFixed(1),
            unidad: 'cm'
        });
    }

    if (primero.grasa_corporal && ultimo.grasa_corporal) {
        const cambio = parseFloat(ultimo.grasa_corporal) - parseFloat(primero.grasa_corporal);
        cambios.push({
            parametro: 'Grasa Corporal',
            inicial: primero.grasa_corporal,
            final: ultimo.grasa_corporal,
            cambio: cambio,
            porcentaje: ((cambio / parseFloat(primero.grasa_corporal)) * 100).toFixed(1),
            unidad: '%'
        });
    }

    const $analisis = $('#analisisCambios');
    $analisis.empty();

    cambios.forEach(function(cambio) {
        const badgeClass = cambio.cambio < 0 ? 'bg-success' : cambio.cambio > 0 ? 'bg-danger' : 'bg-secondary';
        const icono = cambio.cambio < 0 ? 'fa-arrow-down' : cambio.cambio > 0 ? 'fa-arrow-up' : 'fa-minus';
        
        $analisis.append(`
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title">${cambio.parametro}</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">Inicial: ${parseFloat(cambio.inicial).toFixed(2)} ${cambio.unidad}</small><br>
                                <small class="text-muted">Final: ${parseFloat(cambio.final).toFixed(2)} ${cambio.unidad}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge ${badgeClass} fs-6">
                                    <i class="fas ${icono} me-1"></i>
                                    ${Math.abs(cambio.cambio).toFixed(2)} ${cambio.unidad}
                                </span>
                                <br>
                                <small class="text-muted">${cambio.porcentaje}%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `);
    });
}
</script>

<?= $this->endSection() ?>

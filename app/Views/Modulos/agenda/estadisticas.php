<?= $this->extend('layout/dashboard') ?>

<?= $this->section('agenda/estadisticas') ?>

<link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #667eea;
        transition: transform 0.2s;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        color: #667eea;
    }
    
    .stat-label {
        color: #666;
        font-size: 0.9rem;
        margin-top: 5px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-chart-bar me-2"></i> Estadísticas de Consultas</h2>
                        <p style="color: white;">Análisis y métricas de tus consultas</p>
                    </div>
                    <div>
                        <a href="<?= base_url('dashboard/agenda/calendario') ?>" class="btn btn-light">
                            <i class="fas fa-arrow-left me-2"></i> Volver al Calendario
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="stat-card">
                <h5 class="mb-3"><i class="fas fa-filter me-2"></i> Filtros</h5>
                <div class="row">
                    <div class="col-md-3">
                        <label>Fecha Desde</label>
                        <input type="date" id="fecha_desde" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Fecha Hasta</label>
                        <input type="date" id="fecha_hasta" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <button class="btn btn-primary form-control" onclick="cargarEstadisticas()">
                            <i class="fas fa-search me-2"></i> Buscar
                        </button>
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <button class="btn btn-secondary form-control" onclick="resetearFiltros()">
                            <i class="fas fa-redo me-2"></i> Resetear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tarjetas de Resumen -->
            <div class="row" id="resumenEstadisticas">
                <div class="col-md-3">
                    <div class="stat-card text-center">
                        <div class="stat-number" id="totalConsultas">0</div>
                        <div class="stat-label">Total Consultas</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card text-center">
                        <div class="stat-number" id="duracionPromedio">0</div>
                        <div class="stat-label">Duración Promedio (min)</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card text-center">
                        <div class="stat-number" id="totalMinutos">0</div>
                        <div class="stat-label">Total Minutos</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card text-center">
                        <div class="stat-number" id="consultasCompletadas">0</div>
                        <div class="stat-label">Consultas Completadas</div>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="row">
                <div class="col-md-6">
                    <div class="stat-card">
                        <h5 class="mb-3"><i class="fas fa-chart-pie me-2"></i> Consultas por Estado</h5>
                        <canvas id="chartEstados"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-card">
                        <h5 class="mb-3"><i class="fas fa-chart-line me-2"></i> Duración de Consultas</h5>
                        <canvas id="chartDuracion"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tabla de Consultas -->
            <div class="stat-card">
                <h5 class="mb-3"><i class="fas fa-table me-2"></i> Detalle de Consultas</h5>
                <div class="table-responsive">
                    <table id="tablaEstadisticas" class="table table-bordered table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Paciente</th>
                                <th>Duración (min)</th>
                                <th>Estado</th>
                                <th>Modalidad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var chartEstados = null;
var chartDuracion = null;

$(document).ready(function() {
    // Establecer fechas por defecto (último mes)
    var hoy = new Date();
    var haceUnMes = new Date();
    haceUnMes.setMonth(haceUnMes.getMonth() - 1);
    
    $('#fecha_desde').val(haceUnMes.toISOString().split('T')[0]);
    $('#fecha_hasta').val(hoy.toISOString().split('T')[0]);
    
    cargarEstadisticas();
});

function resetearFiltros() {
    var hoy = new Date();
    var haceUnMes = new Date();
    haceUnMes.setMonth(haceUnMes.getMonth() - 1);
    
    $('#fecha_desde').val(haceUnMes.toISOString().split('T')[0]);
    $('#fecha_hasta').val(hoy.toISOString().split('T')[0]);
    cargarEstadisticas();
}

function cargarEstadisticas() {
    var fechaDesde = $('#fecha_desde').val();
    var fechaHasta = $('#fecha_hasta').val();
    
    $.ajax({
        url: '<?= base_url('dashboard/agenda/getEstadisticas') ?>',
        type: 'GET',
        data: {
            fecha_desde: fechaDesde,
            fecha_hasta: fechaHasta
        },
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                console.error('Error:', response.error);
                return;
            }
            
            // Actualizar resumen
            $('#totalConsultas').text(response.resumen.total_consultas || 0);
            $('#duracionPromedio').text(response.resumen.duracion_promedio || 0);
            $('#totalMinutos').text(response.resumen.total_minutos || 0);
            $('#consultasCompletadas').text(response.resumen.consultas_completadas || 0);
            
            // Actualizar gráficos
            actualizarGraficos(response.graficos);
            
            // Actualizar tabla
            actualizarTabla(response.consultas);
        },
        error: function(xhr) {
            console.error('Error al cargar estadísticas:', xhr);
        }
    });
}

function actualizarGraficos(datos) {
    // Gráfico de estados
    var ctxEstados = document.getElementById('chartEstados').getContext('2d');
    if (chartEstados) {
        chartEstados.destroy();
    }
    chartEstados = new Chart(ctxEstados, {
        type: 'doughnut',
        data: {
            labels: datos.estados.labels || [],
            datasets: [{
                data: datos.estados.data || [],
                backgroundColor: [
                    '#4A90E2', // Confirmada
                    '#6BCB77', // Completada
                    '#FFA726', // En proceso
                    '#E57373', // Cancelada
                    '#BA68C8'  // No asistió
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });
    
    // Gráfico de duración
    var ctxDuracion = document.getElementById('chartDuracion').getContext('2d');
    if (chartDuracion) {
        chartDuracion.destroy();
    }
    chartDuracion = new Chart(ctxDuracion, {
        type: 'bar',
        data: {
            labels: datos.duracion.labels || [],
            datasets: [{
                label: 'Duración (minutos)',
                data: datos.duracion.data || [],
                backgroundColor: '#667eea'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function actualizarTabla(consultas) {
    // Implementar DataTable si es necesario
    console.log('Consultas:', consultas);
}
</script>

<?= $this->endSection() ?>

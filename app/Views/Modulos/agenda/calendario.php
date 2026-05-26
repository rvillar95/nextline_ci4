<?= $this->extend('layout/dashboard') ?>

<?= $this->section('agenda/calendario') ?>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css' rel='stylesheet' />
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales/es.js'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #f5576c;
    }
    
    #calendar {
        max-width: 100%;
        margin: 0 auto;
    }
    
    .fc-event {
        cursor: pointer;
    }
    
    .fc-daygrid-event {
        border-radius: 6px;
        padding: 4px 8px;
        border: none !important;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    /* Aplicar color de fondo directamente - FullCalendar usa el atributo 'color' */
    .fc-event {
        /* No sobrescribir, dejar que FullCalendar y eventDidMount manejen los colores */
    }
    
    .fc-daygrid-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        opacity: 0.95;
        filter: brightness(1.1);
    }
    
    .fc-event-title {
        font-size: 0.9em;
        line-height: 1.3;
        color: #FFFFFF !important; /* Texto blanco para todos los estados */
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2); /* Sombra para mejor legibilidad */
    }
    
    /* Estilos específicos para vista semanal (timeGridWeek) */
    .fc-timeGridWeek-view .fc-event-title,
    .fc-timeGridDay-view .fc-event-title {
        font-size: 0.8em !important;
        line-height: 1.25 !important;
        padding: 1px 3px !important;
        white-space: pre-wrap !important;
        word-wrap: break-word !important;
        overflow: visible !important;
        display: block !important;
    }
    
    .fc-timeGridWeek-view .fc-timegrid-event,
    .fc-timeGridDay-view .fc-timegrid-event {
        overflow: hidden !important;
        border-radius: 4px !important;
    }
    .fc-timeGridWeek-view .fc-timegrid-event .fc-event-main,
    .fc-timeGridDay-view .fc-timegrid-event .fc-event-main {
        overflow: hidden !important;
        height: 100% !important;
        display: flex !important;
        align-items: flex-start !important;
    }
    
    .fc-timeGridWeek-view .fc-event {
        min-height: 30px !important;
        padding: 4px 6px !important;
    }
    
    .fc-timeGridWeek-view .fc-event-time {
        font-size: 0.85em !important;
        font-weight: 600 !important;
        margin-bottom: 2px !important;
        display: block !important;
    }
    
    .fc-timeGridWeek-view .fc-event-title-container {
        display: flex !important;
        flex-direction: column !important;
        gap: 2px !important;
    }
    
    .fc-timeGridWeek-view .fc-timegrid-event {
        margin: 1px 2px !important;
    }
    
    /* Mejorar legibilidad en celdas pequeñas */
    .fc-timeGridWeek-view .fc-event-main {
        padding: 3px 5px !important;
    }
    
    /* Aumentar altura de cada slot de hora en vista semanal */
    .fc-timeGridWeek-view .fc-timegrid-slot {
        height: 60px !important;
        min-height: 60px !important;
    }
    
    .fc-timeGridWeek-view .fc-timegrid-slot-lane {
        height: 60px !important;
        min-height: 60px !important;
    }
    
    /* Ajustar altura de las filas de tiempo */
    .fc-timeGridWeek-view .fc-timegrid-col-frame {
        min-height: 60px !important;
    }
    
    /* Forzar altura en las celdas de tiempo */
    .fc-timeGridWeek-view .fc-timegrid-slot-table {
        height: auto !important;
    }
    
    .fc-timeGridWeek-view .fc-timegrid-slot-minor {
        height: 52px !important;
        min-height: 52px !important;
    }
    
    .fc-timeGridDay-view .fc-timegrid-slot-minor {
        height: 52px !important;
        min-height: 52px !important;
    }
    
    .fc-timeGridWeek-view .fc-timegrid-slot-major {
        height: 104px !important;
        min-height: 104px !important;
    }
    
    .fc-timeGridDay-view .fc-timegrid-slot-major {
        height: 104px !important;
        min-height: 104px !important;
    }
    
    /* Solo las filas de contenido (slots de hora) tienen 60px; la fila del divider no */
    .fc-timeGridWeek-view tbody tr.fc-scrollgrid-section-body {
        height: 60px !important;
    }
    .fc-timeGridWeek-view tbody tr:has(.fc-timegrid-divider) {
        height: 0 !important;
        min-height: 0 !important;
        overflow: hidden !important;
    }
    .fc-timeGridWeek-view tbody tr:has(.fc-timegrid-divider) td {
        height: 0 !important;
        min-height: 0 !important;
        padding: 0 !important;
        border: none !important;
        line-height: 0 !important;
    }
    .fc-timeGridDay-view tbody tr:has(.fc-timegrid-divider) {
        height: 0 !important;
        min-height: 0 !important;
    }
    .fc-timeGridDay-view tbody tr:has(.fc-timegrid-divider) td {
        height: 0 !important;
        min-height: 0 !important;
    }
    
    .fc-timeGridWeek-view .fc-timegrid-slot-label {
        height: 60px !important;
    }
    
    /* Padding para vista de día */
    .fc-timeGridDay-view .fc-timegrid-event {
        padding: 6px 8px !important;
        margin: 2px 4px !important;
    }
    
    .fc-timeGridDay-view .fc-event-main {
        padding: 4px 6px !important;
    }
    
    .fc-timeGridDay-view .fc-event-title {
        padding: 2px 4px !important;
    }
    
    /* Aumentar altura de slots en vista de día también */
    .fc-timeGridDay-view .fc-timegrid-slot {
        height: 60px !important;
        min-height: 60px !important;
    }
    
    .fc-timeGridDay-view .fc-timegrid-slot-lane {
        height: 60px !important;
        min-height: 60px !important;
    }
    
    /* Tooltip personalizado para eventos */
    .fc-event-tooltip {
        position: absolute;
        background: rgba(0, 0, 0, 0.9);
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.85em;
        z-index: 10000;
        pointer-events: none;
        white-space: normal;
        max-width: 320px;
        line-height: 1.4;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        opacity: 0;
        transition: opacity 0.2s ease;
        margin-top: 5px;
    }
    
    .fc-event-tooltip.show {
        opacity: 1;
    }
    
    .fc-event-tooltip::before {
        content: '';
        position: absolute;
        top: -5px;
        left: 15px;
        width: 0;
        height: 0;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-bottom: 5px solid rgba(0, 0, 0, 0.9);
    }
    
    .fc-event-tooltip .tooltip-estado {
        font-weight: 600;
        margin-right: 8px;
    }
    
    .fc-event-tooltip .tooltip-separator {
        margin: 0 8px;
        opacity: 0.5;
    }
    
    .fc-event-tooltip .tooltip-modalidad {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-right: 6px;
    }
    
    .fc-event-tooltip .tooltip-duracion {
        opacity: 0.9;
    }
    
    /* Asegurar que el texto sea blanco en todos los eventos */
    .fc-event-time,
    .fc-event-title-container {
        color: #FFFFFF !important;
    }
    
    /* Mejorar contraste para accesibilidad - mantener pero ajustar */
    .fc-event {
        filter: brightness(1.02);
    }
    
    /* Estilo especial para citas completadas - hacerlas más visibles */
    .fc-event-completada {
        border-width: 2px !important;
        border-style: solid !important;
        border-color: #607D8B !important;
        background: repeating-linear-gradient(
            45deg,
            #90A4AE,
            #90A4AE 10px,
            #A5B9C7 10px,
            #A5B9C7 20px
        ) !important;
        opacity: 0.9;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(96, 125, 139, 0.3);
    }
    
    .fc-event-completada:hover {
        opacity: 1;
        box-shadow: 0 3px 6px rgba(96, 125, 139, 0.4);
        transform: translateY(-1px);
    }
    
    /* Alternativa más sutil: solo borde más grueso y sombra */
    .fc-event-completada-alt {
        border-width: 3px !important;
        border-color: #607D8B !important;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.3), 0 2px 4px rgba(96, 125, 139, 0.4);
        position: relative;
    }
    
    .fc-event-completada-alt::after {
        content: '✓';
        position: absolute;
        right: 4px;
        top: 2px;
        font-size: 0.8em;
        color: white;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }
    /* Botones del header Agenda de Citas: integrados al header (mismo criterio que Lista de Citas) */
    .main-header .agenda-header-actions .btn-agenda-ghost {
        background: transparent;
        border: 1px solid rgba(255,255,255,0.85);
        color: #fff;
        font-weight: 500;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        border-radius: 8px;
        transition: background 0.2s, border-color 0.2s;
    }
    .main-header .agenda-header-actions .btn-agenda-ghost:hover {
        background: rgba(255,255,255,0.12);
        border-color: #fff;
        color: #fff;
    }
    .main-header .agenda-header-actions .btn-agenda-primary {
        background: rgba(255,255,255,0.22);
        border: 1px solid rgba(255,255,255,0.9);
        color: #fff;
        font-weight: 600;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        border-radius: 8px;
        transition: background 0.2s, border-color 0.2s;
    }
    .main-header .agenda-header-actions .btn-agenda-primary:hover {
        background: rgba(255,255,255,0.35);
        border-color: #fff;
        color: #fff;
    }
    .main-header .agenda-header-actions .btn-agenda-cancelar {
        background: rgba(220, 53, 69, 0.25);
        border: 1px solid rgba(255,255,255,0.6);
        color: #fff;
        font-weight: 500;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        border-radius: 8px;
        transition: background 0.2s, border-color 0.2s;
    }
    .main-header .agenda-header-actions .btn-agenda-cancelar:hover {
        background: rgba(220, 53, 69, 0.45);
        border-color: rgba(255,255,255,0.9);
        color: #fff;
    }

    .agenda-page-header .agenda-header-inner {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }
    .agenda-page-header .agenda-header-titles {
        flex: 1 1 220px;
        min-width: 0;
    }
    .agenda-page-header .agenda-header-titles h2 {
        font-size: clamp(1.15rem, 4.5vw, 1.75rem);
        margin-bottom: 0.25rem;
    }
    .agenda-page-header .agenda-header-titles p {
        font-size: 0.9rem;
        margin-bottom: 0;
    }
    .agenda-page-header .agenda-header-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: stretch;
        gap: 0.5rem;
    }
    .agenda-page-header .agenda-header-actions .btn {
        white-space: nowrap;
    }

    @media (max-width: 767.98px) {
        .agenda-page-header .agenda-header-inner {
            flex-direction: column;
            align-items: stretch;
        }
        .agenda-page-header .agenda-header-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100%;
        }
        .agenda-page-header .agenda-header-actions .btn {
            width: 100%;
            justify-content: center;
            padding: 0.6rem 0.45rem;
            font-size: 0.78rem;
        }
        .agenda-page-header .agenda-header-actions .btn-agenda-cancelar {
            grid-column: 1 / -1;
        }
        .agenda-page-header .agenda-header-actions .btn .me-2 {
            margin-right: 0.35rem !important;
        }
        .section-card {
            padding: 12px;
            margin-bottom: 16px;
        }
        #calendar {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .fc .fc-toolbar.fc-header-toolbar {
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
            margin-bottom: 0.75rem !important;
        }
        .fc .fc-toolbar.fc-footer-toolbar {
            margin-top: 0.5rem !important;
        }
        .fc .fc-toolbar-chunk {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 0.35rem;
            width: 100%;
        }
        .fc .fc-toolbar-title {
            font-size: 1rem !important;
            line-height: 1.3 !important;
            white-space: normal !important;
            text-align: center;
            padding: 0.15rem 0.25rem;
            max-width: 100%;
        }
        .fc .fc-button {
            padding: 0.4rem 0.55rem;
            font-size: 0.78rem;
        }
        .fc .fc-button-group > .fc-button {
            padding: 0.4rem 0.5rem;
        }
        .fc .fc-col-header-cell-cushion,
        .fc .fc-timegrid-axis-cushion {
            font-size: 0.72rem;
        }
        .fc .fc-timegrid-slot-label-cushion {
            font-size: 0.7rem;
        }
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header agenda-page-header">
                <div class="agenda-header-inner">
                    <div class="agenda-header-titles">
                        <h2 style="color: white;"><i class="fas fa-calendar-alt me-2"></i> Agenda de Citas</h2>
                        <p style="color: white;">Gestione las citas y horarios de sus pacientes</p>
                    </div>
                    <div class="agenda-header-actions">
                        <a href="<?= base_url('dashboard/agenda/lista') ?>" class="btn btn-agenda-ghost" title="Ver lista de citas">
                            <i class="fas fa-list me-2"></i><span class="d-none d-sm-inline">Vista </span>Lista
                        </a>
                        <a href="<?= base_url('dashboard/agenda/gestionar') ?>" class="btn btn-agenda-ghost" title="Editar o eliminar días de agenda">
                            <i class="fas fa-edit me-2"></i><span class="d-none d-sm-inline">Editar </span>Agenda
                        </a>
                        <button type="button" class="btn btn-agenda-primary" onclick="crearHorarios()" title="Crear horarios disponibles para los próximos días">
                            <i class="fas fa-clock me-2"></i><span class="d-none d-sm-inline">Crear </span>Horarios
                        </button>
                        <a href="<?= base_url('dashboard/agenda/cancelar-horas') ?>" class="btn btn-agenda-cancelar" title="Cancelar horas masivamente por emergencia o enfermedad">
                            <i class="fas fa-calendar-times me-2"></i>Cancelar horas
                        </a>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <!-- Leyenda Rediseñada: Estados y Modalidades Separados -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
                            <div class="card-body py-3">
                                <div class="row">
                                    <!-- Sección: ESTADOS (Colores) -->
                                    <div class="col-lg-7 mb-3 mb-lg-0">
                                        <h6 class="mb-3 text-primary">
                                            <i class="fas fa-palette me-2"></i>Estados de Cita
                                        </h6>
                                        <div class="row g-2">
                                            <div class="col-6 col-md-4">
                                                <div class="d-flex align-items-center p-2 rounded" style="background: rgba(123, 203, 135, 0.1);">
                                                    <div class="legend-color-box me-2" style="width: 24px; height: 24px; background-color: #7BCB87; border-radius: 6px; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                                    <span class="small fw-semibold">Disponible</span>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <div class="d-flex align-items-center p-2 rounded" style="background: rgba(74, 144, 226, 0.1);">
                                                    <div class="legend-color-box me-2" style="width: 24px; height: 24px; background-color: #4A90E2; border-radius: 6px; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                                    <span class="small fw-semibold">Confirmada</span>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <div class="d-flex align-items-center p-2 rounded" style="background: rgba(121, 134, 203, 0.1);">
                                                    <div class="legend-color-box me-2" style="width: 24px; height: 24px; background-color: #7986CB; border-radius: 6px; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                                    <span class="small fw-semibold">Reservada</span>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <div class="d-flex align-items-center p-2 rounded" style="background: rgba(255, 183, 77, 0.1);">
                                                    <div class="legend-color-box me-2" style="width: 24px; height: 24px; background-color: #FFB74D; border-radius: 6px; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                                    <span class="small fw-semibold">Pendiente</span>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <div class="d-flex align-items-center p-2 rounded" style="background: rgba(255, 152, 0, 0.1);">
                                                    <div class="legend-color-box me-2" style="width: 24px; height: 24px; background-color: #FF9800; border-radius: 6px; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                                    <span class="small fw-semibold">En Proceso</span>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <div class="d-flex align-items-center p-2 rounded" style="background: rgba(229, 115, 115, 0.1);">
                                                    <div class="legend-color-box me-2" style="width: 24px; height: 24px; background-color: #E57373; border-radius: 6px; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                                    <span class="small fw-semibold">Cancelada</span>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <div class="d-flex align-items-center p-2 rounded" style="background: rgba(189, 189, 189, 0.1);">
                                                    <div class="legend-color-box me-2" style="width: 24px; height: 24px; background-color: #BDBDBD; border-radius: 6px; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                                    <span class="small fw-semibold">No Disponible</span>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <div class="d-flex align-items-center p-2 rounded" style="background: rgba(144, 164, 174, 0.1);">
                                                    <div class="legend-color-box me-2" style="width: 24px; height: 24px; background-color: #90A4AE; border-radius: 6px; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                                    <span class="small fw-semibold">Completada</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Sección: MODALIDADES (Iconos) -->
                                    <div class="col-lg-5">
                                        <h6 class="mb-3 text-secondary">
                                            <i class="fas fa-tag me-2"></i>Modalidades
                                        </h6>
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <div class="d-flex align-items-center p-2 rounded" style="background: rgba(0, 0, 0, 0.02);">
                                                    <span class="me-2" style="font-size: 1.2em;">🏥</span>
                                                    <span class="small fw-semibold">Presencial</span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="d-flex align-items-center p-2 rounded" style="background: rgba(0, 0, 0, 0.02);">
                                                    <span class="me-2" style="font-size: 1.2em;">💻</span>
                                                    <span class="small fw-semibold">Online</span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="d-flex align-items-center p-2 rounded" style="background: rgba(0, 0, 0, 0.02);">
                                                    <span class="me-2" style="font-size: 1.2em;">❔</span>
                                                    <span class="small fw-semibold">No Definido</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 pt-3 border-top">
                                    <p class="small text-muted mb-0">
                                        <i class="fas fa-lightbulb me-1"></i>
                                        <strong>Tip:</strong> El color indica el estado de la cita. El ícono muestra la modalidad (Presencial/Online).
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para configurar y crear horarios -->
<div class="modal fade" id="modalConfirmarCrearHorarios" tabindex="-1" aria-labelledby="modalConfirmarCrearHorariosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title" id="modalConfirmarCrearHorariosLabel">
                    <i class="fas fa-clock me-2"></i> Configurar Horarios Disponibles
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar" style="opacity: 1; background-color: rgba(255, 255, 255, 0.2); border-radius: 4px; padding: 8px; width: 32px; height: 32px;">
                    <span style="color: white; font-size: 20px; line-height: 1; display: block;">&times;</span>
                </button>
            </div>
            <form id="formCrearHorarios">
                <div class="modal-body">
                    <?= view('Modulos/agenda/_form_configurar_horarios', ['modalidades' => $modalidades, 'modoFormHorarios' => 'crear']) ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnConfirmarCrearHorarios">
                        <i class="fas fa-check me-2"></i> Crear Horarios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para agendar cita -->
<div class="modal fade" id="modalAgendar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i> Agendar Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar" style="opacity: 1; background-color: rgba(255, 255, 255, 0.2); border-radius: 4px; padding: 8px; width: 32px; height: 32px;">
                    <span style="color: white; font-size: 20px; line-height: 1; display: block;">&times;</span>
                </button>
            </div>
            <form id="formAgendar">
                <div class="modal-body">
                    <input type="hidden" id="detalle_agenda_id" name="detalle_agenda_id">
                    <input type="hidden" id="fecha_seleccionada" name="fecha_seleccionada">
                    <input type="hidden" id="hora_seleccionada" name="hora_seleccionada">
                    
                    <div class="form-group position-relative">
                        <label class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                            <span>Paciente <span class="text-danger">*</span></span>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAbrirCrearPacienteRapido">
                                <i class="fas fa-user-plus me-1"></i> Crear paciente
                            </button>
                        </label>
                        <input type="hidden" name="paciente_id" id="paciente_id" value="">
                        <input type="text" id="inputPacienteAgendar" class="form-control" placeholder="Escriba para buscar por nombre o RUT..." autocomplete="off">
                        <div id="listaPacientesAgendar" class="list-group position-absolute shadow-sm" style="left: 0; right: 0; top: 100%; margin-top: 2px; z-index: 1050; max-height: 220px; overflow-y: auto; display: none;"></div>
                        <small class="text-muted">Si no aparece en la lista, créelo sin salir del calendario.</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Tipo de Consulta</label>
                        <select name="tipo_consulta" id="tipo_consulta" class="form-control">
                            <option value="control">Control</option>
                            <option value="primera_vez">Primera Vez</option>
                            <option value="seguimiento">Seguimiento</option>
                            <option value="emergencia">Emergencia</option>
                        </select>
                    </div>
                    
                    <?php if (!empty($plantillas_pago)): ?>
                    <div class="form-group">
                        <label>Tipo de Pago <small class="text-muted">(Opcional)</small></label>
                        <select name="boton_pago_plantilla_id" id="boton_pago_plantilla_id" class="form-control">
                            <option value="">-- Sin pago --</option>
                            <?php foreach ($plantillas_pago as $plantilla) : ?>
                                <option value="<?= esc($plantilla->id) ?>">
                                    <?= esc($plantilla->titulo) ?> - 
                                    <?= number_format($plantilla->monto, 0, ',', '.') ?> <?= esc($plantilla->moneda) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">
                            Si selecciona un tipo de pago, se enviará automáticamente el botón de pago al correo del paciente.
                        </small>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>Motivo</label>
                        <textarea name="motivo" id="motivo" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea name="observaciones" id="observaciones" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calendar-check me-2"></i> Agendar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
var pacientesAgendarOpciones = <?= json_encode(array_map(function($p) { $nombre = $p->nombre_completo ?? (trim(($p->nombre ?? '') . ' ' . ($p->apellido ?? ''))); return ['value' => (int)$p->id, 'text' => $nombre, 'rut' => $p->rut_dni ?? '']; }, $pacientes)) ?>;
</script>

<!-- Modal crear paciente rápido (desde Agendar Cita) -->
<div class="modal fade" id="modalCrearPacienteRapido" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i> Nuevo paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar" style="opacity: 1; background-color: rgba(255, 255, 255, 0.2); border-radius: 4px; padding: 8px; width: 32px; height: 32px;">
                    <span style="color: white; font-size: 20px; line-height: 1; display: block;">&times;</span>
                </button>
            </div>
            <form id="formCrearPacienteRapido">
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        El paciente quedará asociado a su consulta. El mismo RUT puede existir con otro nutricionista, pero no se repite en su lista.
                    </p>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="rapido_nombre" name="nombre" required maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellido <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="rapido_apellido" name="apellido" required maxlength="100">
                        </div>
                        <div class="col-12">
                            <label class="form-label">RUT / documento <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="rapido_rut_dni" name="rut_dni" required maxlength="20" placeholder="12.345.678-9">
                            <div id="rapido_rut_feedback" class="form-text"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo de paciente <span class="text-danger">*</span></label>
                            <select class="form-control" id="rapido_tipo_paciente" name="tipo_paciente" required>
                                <option value="particular">Particular</option>
                                <option value="convenio">Convenio</option>
                                <option value="seguro">Seguro</option>
                                <option value="fonasa">Fonasa</option>
                                <option value="isapre">Isapre</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="rapido_telefono" name="telefono" maxlength="50">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Correo</label>
                            <input type="email" class="form-control" id="rapido_email" name="email" maxlength="150" placeholder="Recomendado para confirmación y botón de pago">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarPacienteRapido">
                        <i class="fas fa-save me-1"></i> Guardar y seleccionar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para elegir acción (Agendar o Editar Modalidad) -->
<div class="modal fade" id="modalElegirAccion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title"><i class="fas fa-question-circle me-2"></i> ¿Qué desea hacer?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar" style="opacity: 1; background-color: rgba(255, 255, 255, 0.2); border-radius: 4px; padding: 8px; width: 32px; height: 32px;">
                    <span style="color: white; font-size: 20px; line-height: 1; display: block;">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Horario seleccionado:</strong> <span id="horario_elegir_accion"></span>
                </div>
                <p class="mb-4">Seleccione la acción que desea realizar con este horario:</p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary btn-lg" id="btnAgendarDesdeElegir">
                        <i class="fas fa-calendar-check me-2"></i> Agendar Cita
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-lg" id="btnEditarModalidadDesdeElegir">
                        <i class="fas fa-edit me-2"></i> Editar Modalidad
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver información completa de la cita -->
<div class="modal fade" id="modalVerCita" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #4A90E2 0%, #6BCB77 100%); color: white;">
                <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i> Información de la Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar" style="opacity: 1; background-color: rgba(255, 255, 255, 0.2); border-radius: 4px; padding: 8px; width: 32px; height: 32px;">
                    <span style="color: white; font-size: 20px; line-height: 1; display: block;">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contenidoCita">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando información...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal cancelar cita (desde vista calendario) -->
<div class="modal fade" id="modalCancelarCitaCalendario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title"><i class="fas fa-calendar-times me-2 text-danger"></i>Cancelar cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">¿Cancelar esta cita? El horario quedará libre para agendar a otro paciente.</p>
                <label class="form-label small text-muted">Motivo de cancelación (opcional)</label>
                <textarea class="form-control" id="motivoCancelarCitaCalendario" rows="2" placeholder="Ej.: Paciente reprogramó"></textarea>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarCancelarCitaCalendario">
                    <i class="fas fa-times me-1"></i> Sí, cancelar cita
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar modalidad de un horario -->
<div class="modal fade" id="modalEditarModalidad" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Editar Modalidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar" style="opacity: 1; background-color: rgba(255, 255, 255, 0.2); border-radius: 4px; padding: 8px; width: 32px; height: 32px;">
                    <span style="color: white; font-size: 20px; line-height: 1; display: block;">&times;</span>
                </button>
            </div>
            <form id="formEditarModalidad">
                <div class="modal-body">
                    <input type="hidden" id="detalle_agenda_id_modalidad" name="detalle_agenda_id">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Horario:</strong> <span id="horario_mostrar"></span>
                    </div>
                    <div class="form-group">
                        <label>Modalidad <span class="text-danger">*</span></label>
                        <select name="modalidad_id" id="modalidad_id_editar" class="form-control" required>
                            <option value="">-- Seleccione una modalidad --</option>
                            <?php foreach ($modalidades as $modalidad) : ?>
                                <option value="<?= $modalidad->id ?>"><?= esc($modalidad->nombre) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Configurar toastr
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

// Función para obtener el token CSRF de la cookie (método más confiable con cookie protection)
function obtenerTokenCSRF() {
    // Intentar obtener de la cookie primero (método preferido con cookie protection)
    // CodeIgniter usa 'csrf_cookie_name' como nombre de cookie (según Security.php)
    var cookies = document.cookie.split(';');
    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i].trim();
        // Buscar cookie que contenga 'csrf_cookie_name' (puede tener prefijo)
        if (cookie.indexOf('csrf_cookie_name=') !== -1) {
            var parts = cookie.split('=');
            if (parts.length >= 2) {
                // Decodificar y obtener el valor
                var token = decodeURIComponent(parts.slice(1).join('='));
                if (token && token.length > 0) {
                    return token;
                }
            }
        }
    }
    // Si no está en la cookie, intentar del meta tag
    var metaToken = $('meta[name="csrf-token"]').attr('content');
    if (metaToken) {
        return metaToken;
    }
    // Último recurso: del input hidden si existe
    var inputToken = $('input[name="csrf_test_name"]').val();
    if (inputToken) {
        return inputToken;
    }
    return null;
}

// Función global para actualizar el token CSRF después de cada petición
function actualizarTokenCSRF(xhr) {
    // Con cookie protection, CodeIgniter actualiza la cookie automáticamente
    // Leemos el nuevo token de la cookie
    var nuevoToken = obtenerTokenCSRF();
    if (nuevoToken) {
        // Actualizar el meta tag para futuras referencias
        $('meta[name="csrf-token"]').attr('content', nuevoToken);
        // También actualizar en cualquier input hidden que pueda existir
        $('input[name="csrf_test_name"]').val(nuevoToken);
    }
    // También intentar obtener del header si está disponible
    var headerToken = xhr.getResponseHeader('X-CSRF-TOKEN');
    if (headerToken) {
        $('meta[name="csrf-token"]').attr('content', headerToken);
        $('input[name="csrf_test_name"]').val(headerToken);
    }
    // O de la respuesta JSON si está disponible
    else if (xhr.responseJSON && xhr.responseJSON.csrf_token) {
        var jsonToken = xhr.responseJSON.csrf_token;
        $('meta[name="csrf-token"]').attr('content', jsonToken);
        $('input[name="csrf_test_name"]').val(jsonToken);
    }
}

var calendar;
var selectedEvent = null;

function agendaEsVistaMovil() {
    return window.matchMedia('(max-width: 767.98px)').matches;
}

function toolbarCalendarioPorPantalla() {
    if (agendaEsVistaMovil()) {
        return {
            headerToolbar: {
                left: 'prev,next',
                center: 'title',
                right: 'today'
            },
            footerToolbar: {
                center: 'dayGridMonth,timeGridWeek,timeGridDay'
            }
        };
    }
    return {
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        footerToolbar: false
    };
}

function aplicarToolbarResponsiveCalendario() {
    if (!calendar) return;
    var cfg = toolbarCalendarioPorPantalla();
    calendar.setOption('headerToolbar', cfg.headerToolbar);
    calendar.setOption('footerToolbar', cfg.footerToolbar);
}

var resizeToolbarTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeToolbarTimer);
    resizeToolbarTimer = setTimeout(aplicarToolbarResponsiveCalendario, 150);
});

document.addEventListener('DOMContentLoaded', function() {
    // Verificar si debemos abrir el modal de crear horarios
    if (sessionStorage.getItem('abrirModalCrearHorarios') === 'true') {
        sessionStorage.removeItem('abrirModalCrearHorarios');
        setTimeout(function() {
            $('#modalConfirmarCrearHorarios').modal('show');
        }, 500);
    }
    
    var calendarEl = document.getElementById('calendar');
    var toolbarCfg = toolbarCalendarioPorPantalla();

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: agendaEsVistaMovil() ? 'timeGridDay' : 'timeGridWeek',
        locale: 'es',
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día'
        },
        slotMinTime: '09:00:00', // Hora mínima por defecto, se actualizará dinámicamente
        slotDuration: '00:30:00',
        height: 'auto', // Altura automática
        contentHeight: 'auto', // Altura de contenido automática
        views: {
            timeGridWeek: {
                displayEventTime: false
            },
            timeGridDay: {
                displayEventTime: false
            }
        },
        headerToolbar: toolbarCfg.headerToolbar,
        footerToolbar: toolbarCfg.footerToolbar,
        events: function(fetchInfo, successCallback, failureCallback) {
            $.ajax({
                url: '<?= base_url('dashboard/agenda/getEventos') ?>',
                type: 'GET',
                data: {
                    start: fetchInfo.startStr,
                    end: fetchInfo.endStr
                },
                success: function(response) {
                    // Obtener los eventos (puede ser array directo o response.events)
                    var eventos = Array.isArray(response) ? response : (response.events || []);
                    
                    // Si la respuesta incluye slotMinTime, actualizar el calendario
                    if (response.slotMinTime && !Array.isArray(response)) {
                        calendar.setOption('slotMinTime', response.slotMinTime);
                    }
                    
                    if (eventos.length === 0) {
                        // Si no hay eventos, mostrar mensaje informativo
                        toastr.info('No hay horarios disponibles en este período. Por favor, crea horarios disponibles primero.', 'Sin Horarios', {
                            timeOut: 6000,
                            progressBar: true
                        });
                    }
                    successCallback(eventos);
                },
                error: function(xhr) {
                    console.error('Error al cargar eventos:', xhr);
                    toastr.error('Error al cargar los eventos del calendario', 'Error', {
                        timeOut: 4000,
                        progressBar: true
                    });
                    failureCallback();
                }
            });
        },
        eventClick: function(info) {
            selectedEvent = info.event;
            var estadoCita = info.event.extendedProps.estado_cita || 'disponible';
            var pacienteId = info.event.extendedProps.paciente_id || null;
            var modalidadId = info.event.extendedProps.modalidad_id || 3;
            
            // Guardar datos del evento para usar en los modales
            window.selectedEventData = {
                id: info.event.id,
                fecha: info.event.startStr.split('T')[0],
                hora: info.event.startStr.split('T')[1] ? info.event.startStr.split('T')[1].substring(0, 5) : '',
                modalidadId: modalidadId
            };
            
            // Si el evento está disponible (sin paciente o estado 'disponible')
            if (!pacienteId && (estadoCita === 'disponible' || !estadoCita || estadoCita === null)) {
                // Mostrar modal para elegir acción
                var fechaHora = info.event.startStr.split('T');
                var fecha = fechaHora[0];
                var hora = fechaHora[1] ? fechaHora[1].substring(0, 5) : '';
                
                // Convertir fecha de YYYY-MM-DD a DD-MM-YYYY
                var fechaFormateada = fecha;
                if (fecha.match(/^(\d{4})-(\d{2})-(\d{2})$/)) {
                    fechaFormateada = fecha.replace(/^(\d{4})-(\d{2})-(\d{2})$/, '$3-$2-$1');
                }
                $('#horario_elegir_accion').text(fechaFormateada + ' ' + hora);
                $('#modalElegirAccion').modal('show');
            } else {
                // Si ya tiene paciente, cargar y mostrar información completa
                cargarInformacionCita(info.event.id);
            }
        },
        dateClick: function(info) {
            // Al hacer clic en una fecha vacía, buscar horarios disponibles para esa fecha
            $('#fecha_seleccionada').val(info.dateStr);
            $('#detalle_agenda_id').val(''); // Limpiar porque no hay evento específico
            $('#hora_seleccionada').val('');
            
            // Mostrar mensaje indicando que debe seleccionar un horario disponible
            toastr.warning('Por favor, seleccione un horario disponible del calendario para agendar la cita.', 'Seleccione un Horario', {
                timeOut: 4000,
                progressBar: true
            });
        },
        viewDidMount: function(arg) {
            // Ajustar altura de los slots después de que se renderice la vista
            ajustarAlturaSlots();
        },
        datesSet: function(arg) {
            // Ajustar altura cuando cambian las fechas
            if (arg.view.type === 'timeGridWeek') {
                setTimeout(ajustarAlturaSlots, 100);
            }
        },
        eventDidMount: function(info) {
            // PRIMERO: Usar el color que viene del servidor (FullCalendar lo pasa aquí)
            var colorFondo = info.event.backgroundColor || info.event.color;
            
            // Si no hay color del servidor, usar la paleta según el estado
            if (!colorFondo || colorFondo === 'inherit' || colorFondo === 'transparent') {
                var estado = info.event.extendedProps.estado_cita || 'disponible';
                
                // Paleta de colores profesional (COLOR = ESTADO)
                // Optimizada para uso prolongado (ergonomía visual)
                var coloresPaleta = {
                    'disponible': '#7BCB87',      // Verde suave (saturación reducida para fatiga visual)
                    'pendiente': '#FFB74D',        // Naranjo claro - esperando confirmación del paciente
                    'confirmada': '#4A90E2',      // Azul confiable - confirmada por paciente
                    'en_proceso': '#FF9800',       // Naranjo intenso - consulta en curso
                    'completada': '#90A4AE',      // Gris azulado - consulta finalizada
                    'cancelada': '#E57373',        // Rojo suave
                    'no_asistio': '#BA68C8',       // Morado suave
                    'bloqueado': '#BDBDBD',        // Gris claro
                    'no_disponible': '#BDBDBD'     // Gris claro
                };
                
                colorFondo = coloresPaleta[estado] || '#BDBDBD';
            }
            
            var estado = info.event.extendedProps.estado_cita;
            
            // Aplicar color al fondo completo del evento - FORZAR con !important
            if (colorFondo) {
                info.el.style.setProperty('background-color', colorFondo, 'important');
                info.el.style.setProperty('border-color', colorFondo, 'important');
            }
            
            info.el.style.borderWidth = '0';
            info.el.style.borderRadius = '6px';
            
            // Asegurar que el texto sea blanco con buena legibilidad
            var textoElementos = info.el.querySelectorAll('.fc-event-title, .fc-event-time, .fc-event-title-container, .fc-event-main');
            textoElementos.forEach(function(el) {
                el.style.setProperty('color', '#FFFFFF', 'important');
                el.style.textShadow = '0 1px 2px rgba(0, 0, 0, 0.3)';
                el.style.fontWeight = '600';
            });
            
            // Para citas completadas, agregar estilo especial
            if (estado === 'completada') {
                info.el.classList.add('fc-event-completada-alt');
                info.el.style.borderWidth = '2px';
                info.el.style.borderColor = '#607D8B';
            }
        },
        eventMouseEnter: function(info) {
            var props = info.event.extendedProps;
            var estado = props.estado_cita || 'disponible';
            var pacienteId = props.paciente_id;
            var modalidadId = props.modalidad_id || 3;
            var inicio = new Date(info.event.start);
            var fin = new Date(info.event.end);
            var duracionMinutos = Math.round((fin - inicio) / (1000 * 60));
            var horaInicio = props.hora_inicio || (info.event.startStr.split('T')[1] || '').substring(0, 5);
            var horaFin = props.hora_fin || (info.event.endStr.split('T')[1] || '').substring(0, 5);

            var iconoModalidad = '❔';
            var nombreModalidad = 'No definido';
            if (modalidadId == 1) {
                iconoModalidad = '🏥';
                nombreModalidad = 'Presencial';
            } else if (modalidadId == 2) {
                iconoModalidad = '💻';
                nombreModalidad = 'Online';
            }

            var etiquetasEstado = {
                disponible: 'Disponible',
                reservada: 'Reservada',
                confirmada: 'Confirmada',
                agendada: 'Agendada',
                pendiente: 'Pendiente',
                en_proceso: 'En proceso',
                completada: 'Completada',
                cancelada: 'Cancelada',
                no_asistio: 'No asistió',
                bloqueado: 'Bloqueado',
                no_disponible: 'No disponible'
            };
            var etiquetaEstado = etiquetasEstado[estado] || estado;

            var tooltip = document.createElement('div');
            tooltip.className = 'fc-event-tooltip';

            if (!pacienteId && (estado === 'disponible' || !estado || estado === null)) {
                tooltip.innerHTML = '<span class="tooltip-estado">' + etiquetaEstado + '</span>' +
                    '<span class="tooltip-separator">|</span>' +
                    '<span class="tooltip-modalidad">' + iconoModalidad + ' ' + nombreModalidad + '</span>' +
                    '<span class="tooltip-separator">•</span>' +
                    '<span class="tooltip-duracion">' + duracionMinutos + ' min</span>';
            } else {
                var tituloCompleto = props.titulo_completo || info.event.title;
                var lineas = [
                    '<strong>' + (horaInicio && horaFin ? horaInicio + ' – ' + horaFin : '') + '</strong>',
                    tituloCompleto,
                    iconoModalidad + ' ' + nombreModalidad + ' · ' + duracionMinutos + ' min'
                ];
                if (props.motivo) {
                    lineas.push('Motivo: ' + props.motivo);
                }
                tooltip.innerHTML = lineas.filter(Boolean).join('<br>');
            }

            var rect = info.el.getBoundingClientRect();
            var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            var scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

            tooltip.style.top = (rect.bottom + scrollTop + 5) + 'px';
            tooltip.style.left = (rect.left + scrollLeft + (rect.width / 2)) + 'px';
            tooltip.style.transform = 'translateX(-50%)';

            document.body.appendChild(tooltip);
            setTimeout(function() {
                tooltip.classList.add('show');
            }, 10);
            info.el._tooltip = tooltip;
        },
        eventMouseLeave: function(info) {
            // Remover tooltip si existe
            if (info.el._tooltip) {
                info.el._tooltip.classList.remove('show');
                setTimeout(function() {
                    if (info.el._tooltip && info.el._tooltip.parentNode) {
                        info.el._tooltip.parentNode.removeChild(info.el._tooltip);
                    }
                    info.el._tooltip = null;
                }, 200);
            }
        }
    });
    
    calendar.render();
    
    // Función para ajustar altura de los slots
    function ajustarAlturaSlots() {
        var alturaSlot = 52;
        var slots = document.querySelectorAll('.fc-timeGridWeek-view .fc-timegrid-slot, .fc-timeGridDay-view .fc-timegrid-slot');
        slots.forEach(function(slot) {
            if (slot.classList.contains('fc-timegrid-slot-minor') || slot.offsetHeight < alturaSlot) {
                slot.style.height = alturaSlot + 'px';
                slot.style.minHeight = alturaSlot + 'px';
            }
        });
        
        var slotLanes = document.querySelectorAll('.fc-timeGridWeek-view .fc-timegrid-slot-lane, .fc-timeGridDay-view .fc-timegrid-slot-lane');
        slotLanes.forEach(function(lane) {
            if (lane.offsetHeight < alturaSlot) {
                lane.style.height = alturaSlot + 'px';
                lane.style.minHeight = alturaSlot + 'px';
            }
        });
        
        // Ajustar también las filas de la tabla, pero NO las que tienen fc-timegrid-divider
        var rows = document.querySelectorAll('.fc-timeGridWeek-view tbody tr, .fc-timeGridDay-view tbody tr');
        rows.forEach(function(row) {
            // Saltar las filas que tienen el divider
            if (row.querySelector('.fc-timegrid-divider')) {
                //row.style.height = '0';
                //row.style.minHeight = '0';
                //row.style.maxHeight = '0';
                return;
            }
            if (row.offsetHeight < 60 && !row.classList.contains('fc-scrollgrid-section')) {
                row.style.height = '60px';
                row.style.minHeight = '60px';
            }
        });
        
        // Asegurar que el divider tenga altura 0
        var dividers = document.querySelectorAll('.fc-timegrid-divider');
        dividers.forEach(function(divider) {
            //divider.style.height = '0';
            //divider.style.minHeight = '0';
            //divider.style.maxHeight = '0';
        });
        
        var dividerRows = document.querySelectorAll('tr.fc-scrollgrid-section .fc-timegrid-divider');
        dividerRows.forEach(function(row) {
            //row.style.height = '0';
            //row.style.minHeight = '0';
            //row.style.maxHeight = '0';
        });
    }
    
    // Ajustar altura después del render inicial
    setTimeout(ajustarAlturaSlots, 200);
    
    // Ajustar altura cuando cambia el tamaño de la ventana
    window.addEventListener('resize', function() {
        setTimeout(ajustarAlturaSlots, 100);
    });
});

// Combobox Paciente en modal Agendar: un solo input para buscar y seleccionar
$(function() {
    var $input = $('#inputPacienteAgendar');
    var $lista = $('#listaPacientesAgendar');
    var $hidden = $('#paciente_id');

    function mostrarLista(termino) {
        var t = (termino || '').toLowerCase().trim();
        var filtrados = !t ? pacientesAgendarOpciones : pacientesAgendarOpciones.filter(function(o) {
            var texto = (o.text || '').toLowerCase();
            var rut = (o.rut || '').toLowerCase().replace(/[^0-9k]/g, '');
            var termRut = t.replace(/[^0-9k]/g, '');
            return texto.indexOf(t) !== -1 || (termRut.length >= 3 && rut.indexOf(termRut) !== -1);
        });
        $lista.empty();
        if (filtrados.length === 0) {
            $lista.append('<div class="list-group-item text-muted">Sin coincidencias. Use «Crear paciente».</div>');
        } else {
            filtrados.forEach(function(o) {
                var $a = $('<a href="#" class="list-group-item list-group-item-action"></a>').attr('data-id', o.value).text(o.text);
                $lista.append($a);
            });
        }
        $lista.show();
    }
    function ocultarLista() {
        $lista.hide();
    }
    function elegirPaciente(id, text) {
        $hidden.val(id);
        $input.val(text);
        ocultarLista();
    }

    $input.on('focus', function() { mostrarLista($input.val()); });
    $input.on('input', function() {
        $hidden.val('');
        mostrarLista($input.val());
    });
    $input.on('blur', function() {
        setTimeout(ocultarLista, 200);
    });
    $lista.on('click', 'a.list-group-item-action', function(e) {
        e.preventDefault();
        elegirPaciente($(this).data('id'), $(this).text());
    });
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#inputPacienteAgendar, #listaPacientesAgendar').length) ocultarLista();
    });

    $('#modalAgendar').on('show.bs.modal', function() {
        $input.val('');
        $hidden.val('');
        $lista.hide();
    });

    window.elegirPacienteAgendar = elegirPaciente;
});

/** Validador RUT chileno (módulo 11) */
function validarRutChilenoAgenda(rut) {
    var s = (rut || '').trim().replace(/\./g, '').replace(/-/g, '').toUpperCase();
    if (s.length < 2) return false;
    var dv = s.slice(-1);
    var body = s.slice(0, -1);
    if (!/^\d+$/.test(body)) return false;
    if (!/^[\dK]$/.test(dv)) return false;
    var sum = 0, serie = [2, 3, 4, 5, 6, 7];
    for (var i = 0; i < body.length; i++) {
        sum += parseInt(body.charAt(body.length - 1 - i), 10) * serie[i % 6];
    }
    var rest = sum % 11;
    var expected = 11 - rest;
    if (expected === 11) expected = '0';
    else if (expected === 10) expected = 'K';
    else expected = String(expected);
    return expected === dv;
}

function agregarPacienteAgendarOpcion(paciente) {
    if (!paciente || !paciente.id) return;
    var existe = pacientesAgendarOpciones.some(function(o) { return o.value === paciente.id; });
    if (!existe) {
        pacientesAgendarOpciones.push({
            value: paciente.id,
            text: paciente.text,
            rut: paciente.rut_dni || ''
        });
        pacientesAgendarOpciones.sort(function(a, b) {
            return String(a.text).localeCompare(String(b.text), 'es');
        });
    }
}

$('#btnAbrirCrearPacienteRapido').on('click', function() {
    var $form = $('#formCrearPacienteRapido');
    $form[0].reset();
    $('#rapido_tipo_paciente').val('particular');
    $('#rapido_rut_feedback').text('').removeClass('text-danger text-success');
    var busqueda = $('#inputPacienteAgendar').val().trim();
    if (busqueda && busqueda.indexOf(' ') === -1 && busqueda.length < 20) {
        $('#rapido_rut_dni').val(busqueda);
    }
    var modalCrear = new bootstrap.Modal(document.getElementById('modalCrearPacienteRapido'));
    modalCrear.show();
});

var verificarRutTimer = null;
$('#rapido_rut_dni').on('input blur', function() {
    var $fb = $('#rapido_rut_feedback');
    var rut = $(this).val().trim();
    clearTimeout(verificarRutTimer);
    if (rut.length < 3) {
        $fb.text('').removeClass('text-danger text-success');
        return;
    }
    if (/^\d{7,8}[\dKk-]?$/.test(rut.replace(/\./g, '').replace(/-/g, '')) && !validarRutChilenoAgenda(rut)) {
        $fb.text('RUT con dígito verificador incorrecto.').addClass('text-danger').removeClass('text-success');
        return;
    }
    verificarRutTimer = setTimeout(function() {
        $.get('<?= base_url('dashboard/paciente/verificarRutNutricionista') ?>', { rut: rut }, function(res) {
            if (res.csrf_token) {
                $('meta[name="csrf-token"]').attr('content', res.csrf_token);
            }
            if (!res.valido) {
                $fb.text(res.message || 'RUT no válido').addClass('text-danger').removeClass('text-success');
            } else if (!res.disponible) {
                $fb.text(res.message || 'RUT ya registrado').addClass('text-danger').removeClass('text-success');
            } else {
                $fb.text(res.message || 'RUT disponible').addClass('text-success').removeClass('text-danger');
            }
        }, 'json');
    }, 400);
});

$('#formCrearPacienteRapido').on('submit', function(e) {
    e.preventDefault();
    var rut = $('#rapido_rut_dni').val().trim();
    var rutLimpio = rut.replace(/\./g, '').replace(/-/g, '').toUpperCase();
    var pareceRutChileno = /^\d{7,8}[\dK]$/.test(rutLimpio);
    if (pareceRutChileno && !validarRutChilenoAgenda(rut)) {
        toastr.error('El RUT chileno no es válido (revise el dígito verificador).');
        return;
    }
    if (!pareceRutChileno && rut.replace(/[^0-9a-zA-Z]/gi, '').length < 3) {
        toastr.error('Ingrese un RUT o documento de identidad válido.');
        return;
    }
    var csrfToken = obtenerTokenCSRF() || '<?= csrf_hash() ?>';
    var $btn = $('#btnGuardarPacienteRapido');
    $btn.prop('disabled', true);
    $.ajax({
        url: '<?= base_url('dashboard/paciente/crearRapido') ?>',
        type: 'POST',
        dataType: 'json',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
        data: {
            csrf_test_name: csrfToken,
            nombre: $('#rapido_nombre').val(),
            apellido: $('#rapido_apellido').val(),
            rut_dni: rut,
            tipo_paciente: $('#rapido_tipo_paciente').val(),
            telefono: $('#rapido_telefono').val(),
            email: $('#rapido_email').val()
        },
        success: function(res, textStatus, xhr) {
            actualizarTokenCSRF(xhr);
            $btn.prop('disabled', false);
            if (res.success && res.paciente) {
                agregarPacienteAgendarOpcion({
                    id: res.paciente.id,
                    text: res.paciente.text,
                    rut_dni: res.paciente.rut_dni
                });
                if (typeof window.elegirPacienteAgendar === 'function') {
                    window.elegirPacienteAgendar(res.paciente.id, res.paciente.text);
                } else {
                    $('#paciente_id').val(res.paciente.id);
                    $('#inputPacienteAgendar').val(res.paciente.text);
                }
                bootstrap.Modal.getInstance(document.getElementById('modalCrearPacienteRapido')).hide();
                toastr.success(res.message || 'Paciente creado');
            } else {
                var msg = res.message || 'No se pudo crear el paciente';
                if (res.errors) {
                    Object.keys(res.errors).forEach(function(k) {
                        toastr.warning(res.errors[k]);
                    });
                }
                toastr.error(msg);
            }
        },
        error: function(xhr) {
            $btn.prop('disabled', false);
            actualizarTokenCSRF(xhr);
            var res = xhr.responseJSON || {};
            var msg = res.message || 'Error al crear el paciente';
            if (xhr.status === 409) {
                toastr.warning(msg, 'RUT duplicado', { timeOut: 6000 });
            } else if (xhr.status === 403) {
                toastr.error('Sesión expirada. Recargue la página.');
            } else {
                toastr.error(msg);
            }
        }
    });
});

function abrirModalAgendar() {
    $('#formAgendar')[0].reset();
    $('#detalle_agenda_id').val('');
    $('#fecha_seleccionada').val('');
    $('#hora_seleccionada').val('');
    $('#inputPacienteAgendar').val('');
    $('#paciente_id').val('');
    $('#listaPacientesAgendar').hide();
    $('#modalAgendar').modal('show');
    
    toastr.warning('Por favor, seleccione un horario disponible del calendario haciendo clic en un evento disponible (verde).', 'Seleccione un Horario', {
        timeOut: 5000,
        progressBar: true
    });
}

function crearHorarios() {
    // Resetear formulario
    $('#formCrearHorarios')[0].reset();
    $('#dias_crear').val(30);
    $('#duracion_cita').val(30);
    $('#hora_inicio').val('09:00');
    $('#hora_fin').val('18:00');
    $('#almuerzo_inicio').val('13:00');
    $('#almuerzo_fin').val('14:00');
    // Marcar días por defecto
    $('#dia_lunes, #dia_martes, #dia_miercoles, #dia_jueves, #dia_viernes').prop('checked', true);
    $('#dia_sabado, #dia_domingo').prop('checked', false);
    $('#incluir_almuerzo').prop('checked', false);
    $('#horario_almuerzo').hide();
    
    $('#modalConfirmarCrearHorarios').modal('show');
}

// Mostrar/ocultar horario de almuerzo
$('#incluir_almuerzo').on('change', function() {
    if ($(this).is(':checked')) {
        $('#horario_almuerzo').slideDown();
    } else {
        $('#horario_almuerzo').slideUp();
    }
});

// Manejar envío del formulario
$('#formCrearHorarios').on('submit', function(e) {
    e.preventDefault();
    
    // Validar que al menos un día esté seleccionado
    var diasSeleccionados = $('input[name="dias_semana[]"]:checked').length;
    if (diasSeleccionados === 0) {
        toastr.error('Debe seleccionar al menos un día de la semana', 'Error de Validación', {
            timeOut: 4000,
            progressBar: true
        });
        return;
    }
    
    // Validar que hora fin sea mayor que hora inicio
    var horaInicio = $('#hora_inicio').val();
    var horaFin = $('#hora_fin').val();
    if (horaFin <= horaInicio) {
        toastr.error('La hora de fin debe ser mayor que la hora de inicio', 'Error de Validación', {
            timeOut: 4000,
            progressBar: true
        });
        return;
    }
    
    $('#modalConfirmarCrearHorarios').modal('hide');
    ejecutarCrearHorarios();
});

function ejecutarCrearHorarios() {
    // Obtener datos del formulario como objeto
    var formDataObj = {
        fecha_inicio: $('#fecha_inicio').val(),
        dias: $('#dias_crear').val(),
        duracion: $('#duracion_cita').val(),
        hora_inicio: $('#hora_inicio').val(),
        hora_fin: $('#hora_fin').val(),
        incluir_almuerzo: $('#incluir_almuerzo').is(':checked') ? 'on' : '',
        almuerzo_inicio: $('#almuerzo_inicio').val(),
        almuerzo_fin: $('#almuerzo_fin').val(),
        modalidad_id: $('#modalidad_id').val() || 3
    };
    
    // Obtener días seleccionados
    formDataObj['dias_semana'] = [];
    $('input[name="dias_semana[]"]:checked').each(function() {
        formDataObj['dias_semana'].push($(this).val());
    });
    
    // Obtener token CSRF de la cookie (método más confiable)
    var csrfToken = obtenerTokenCSRF() || $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
    var csrfName = 'csrf_test_name'; // Nombre fijo según app/Config/Security.php
    formDataObj[csrfName] = csrfToken;
    
    toastr.info('Creando horarios disponibles...', 'Procesando', {
        timeOut: 2000,
        progressBar: true
    });
    
    $.ajax({
        url: '<?= base_url('dashboard/agenda/crearHorarios') ?>',
        type: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        data: formDataObj,
        dataType: 'json',
        success: function(response, textStatus, xhr) {
            // Actualizar token CSRF después de petición exitosa
            actualizarTokenCSRF(xhr);
            
            if (response.success) {
                toastr.success('Se crearon ' + (response.horarios_creados || 0) + ' horarios disponibles exitosamente.', 'Éxito', {
                    timeOut: 4000,
                    progressBar: true
                });
                calendar.refetchEvents();
            } else {
                var mensaje = response.message || 'Error al crear horarios';
                toastr.error(mensaje, 'Error', {
                    timeOut: 5000,
                    progressBar: true,
                    closeButton: true
                });
            }
        },
        error: function(xhr) {
            var errorMsg = 'Error al crear horarios';
            var errorTitle = 'Error';
            
            if (xhr.status === 404) {
                errorMsg = 'No se encontró la ruta solicitada. Por favor, recargue la página.';
                errorTitle = 'Ruta no encontrada';
            } else if (xhr.status === 403) {
                errorMsg = 'No tiene permisos para realizar esta acción o su sesión ha expirado. Por favor, recargue la página.';
                errorTitle = 'Acceso Denegado';
            } else if (xhr.status === 400) {
                errorTitle = 'Error de Validación';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        var errors = Object.values(xhr.responseJSON.errors);
                        errorMsg = errors.join('<br>');
                    }
                }
            } else if (xhr.status === 500) {
                errorTitle = 'Error del Servidor';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else {
                    errorMsg = 'Ocurrió un error en el servidor. Por favor, intente nuevamente.';
                }
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            
            toastr.error(errorMsg, errorTitle, {
                timeOut: 5000,
                progressBar: true,
                closeButton: true
            });
        }
    });
}

$('#formAgendar').on('submit', function(e) {
    e.preventDefault();
    
    // Validar que se haya seleccionado un horario
    var detalleAgendaId = $('#detalle_agenda_id').val();
    if (!detalleAgendaId || detalleAgendaId === '') {
        toastr.error('Debe seleccionar un horario disponible del calendario haciendo clic en un evento disponible.', 'Horario Requerido', {
            timeOut: 5000,
            progressBar: true
        });
        return;
    }
    // Validar que se haya elegido un paciente (combobox)
    var pacienteId = $('#paciente_id').val();
    if (!pacienteId || pacienteId === '') {
        toastr.error('Debe buscar y seleccionar un paciente de la lista.', 'Paciente Requerido', {
            timeOut: 4000,
            progressBar: true
        });
        return;
    }
    
    var formData = $(this).serialize();
    
    // Obtener token CSRF de la cookie (método más confiable)
    var csrfToken = obtenerTokenCSRF() || $('meta[name="csrf-token"]').attr('content') || $('input[name="csrf_test_name"]').val() || '<?= csrf_hash() ?>';
    var csrfName = 'csrf_test_name'; // Nombre fijo según app/Config/Security.php
    
    // Agregar CSRF token a los datos si no está ya incluido
    if (formData.indexOf(csrfName) === -1) {
        formData += '&' + encodeURIComponent(csrfName) + '=' + encodeURIComponent(csrfToken);
    }
    
    // Mostrar indicador de carga
    var $submitBtn = $(this).find('button[type="submit"]');
    var originalText = $submitBtn.html();
    $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Agendando...');
    
    $.ajax({
        url: '<?= base_url('dashboard/agenda/agendar') ?>',
        type: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        data: formData,
        success: function(response, textStatus, xhr) {
            // Actualizar token CSRF después de petición exitosa
            actualizarTokenCSRF(xhr);
            
            if (response.success) {
                toastr.success('Cita agendada con éxito. Se envió un correo al paciente para confirmación.', 'Éxito', {
                    timeOut: 3000,
                    progressBar: true
                });
                $('#modalAgendar').modal('hide');
                $('#formAgendar')[0].reset();
                calendar.refetchEvents();
            } else {
                toastr.error(response.error || 'Error al agendar la cita', 'Error', {
                    timeOut: 4000,
                    progressBar: true
                });
            }
        },
        error: function(xhr) {
            // Intentar actualizar token incluso en errores (puede venir en el header)
            actualizarTokenCSRF(xhr);
            
            var errorMsg = 'Error al agendar la cita';
            var errorTitle = 'Error';
            
            if (xhr.status === 403) {
                errorMsg = 'No tiene permisos para realizar esta acción o su sesión ha expirado. Por favor, recargue la página.';
                errorTitle = 'Acceso Denegado';
            } else if (xhr.status === 400) {
                errorTitle = 'Error de Validación';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        var errors = Object.values(xhr.responseJSON.errors);
                        errorMsg = errors.join('<br>');
                    }
                }
            } else if (xhr.status === 500) {
                errorTitle = 'Error del Servidor';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else {
                    errorMsg = 'Ocurrió un error en el servidor. Por favor, intente nuevamente.';
                }
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            
            toastr.error(errorMsg, errorTitle, {
                timeOut: 5000,
                progressBar: true,
                closeButton: true
            });
        },
        complete: function() {
            // Restaurar botón
            $submitBtn.prop('disabled', false).html(originalText);
        }
    });
});

// Manejar botones del modal de elegir acción
$('#btnAgendarDesdeElegir').on('click', function() {
    if (window.selectedEventData) {
        $('#detalle_agenda_id').val(window.selectedEventData.id);
        $('#fecha_seleccionada').val(window.selectedEventData.fecha);
        $('#hora_seleccionada').val(window.selectedEventData.hora);
        $('#modalidad_cita').val(window.selectedEventData.modalidadId);
        $('#modalElegirAccion').modal('hide');
        $('#modalAgendar').modal('show');
    }
});

$('#btnEditarModalidadDesdeElegir').on('click', function() {
    if (window.selectedEventData) {
        $('#detalle_agenda_id_modalidad').val(window.selectedEventData.id);
        $('#modalidad_id_editar').val(window.selectedEventData.modalidadId);
        // Convertir fecha de YYYY-MM-DD a DD-MM-YYYY
        var fechaFormateada = window.selectedEventData.fecha;
        if (window.selectedEventData.fecha && window.selectedEventData.fecha.match(/^(\d{4})-(\d{2})-(\d{2})$/)) {
            fechaFormateada = window.selectedEventData.fecha.replace(/^(\d{4})-(\d{2})-(\d{2})$/, '$3-$2-$1');
        }
        $('#horario_mostrar').text(fechaFormateada + ' ' + window.selectedEventData.hora);
        $('#modalElegirAccion').modal('hide');
        $('#modalEditarModalidad').modal('show');
    }
});

// Manejar formulario de editar modalidad
$('#formEditarModalidad').on('submit', function(e) {
    e.preventDefault();
    
    // Obtener token CSRF de la cookie (método más confiable)
    var csrfToken = obtenerTokenCSRF() || $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
    var csrfName = 'csrf_test_name';
    
    var formData = {
        detalle_agenda_id: $('#detalle_agenda_id_modalidad').val(),
        modalidad_id: $('#modalidad_id_editar').val(),
        [csrfName]: csrfToken
    };
    
    $.ajax({
        url: '<?= base_url('dashboard/agenda/actualizarModalidad') ?>',
        type: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        data: formData,
        dataType: 'json',
        success: function(response, textStatus, xhr) {
            // Actualizar token CSRF después de petición exitosa
            actualizarTokenCSRF(xhr);
            
            if (response.success) {
                toastr.success(response.message || 'Modalidad actualizada correctamente', 'Éxito', {
                    timeOut: 3000,
                    progressBar: true
                });
                $('#formEditarModalidad')[0].reset();
                $('#modalEditarModalidad').modal('hide');
                calendar.refetchEvents();
            } else {
                toastr.error(response.error || 'Error al actualizar la modalidad', 'Error', {
                    timeOut: 4000,
                    progressBar: true
                });
            }
        },
        error: function(xhr) {
            // Intentar actualizar token incluso en errores (puede venir en el header)
            actualizarTokenCSRF(xhr);
            
            var errorMsg = 'Error al actualizar la modalidad';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            toastr.error(errorMsg, 'Error', {
                timeOut: 4000,
                progressBar: true
            });
        }
    });
});

// Función para cargar información completa de una cita
function cargarInformacionCita(detalleAgendaId) {
    $('#modalVerCita').modal('show');
    $('#contenidoCita').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><p class="mt-2">Cargando información...</p></div>');
    
    $.ajax({
        url: '<?= base_url('dashboard/agenda/getDetalleCita') ?>',
        type: 'GET',
        data: { id: detalleAgendaId },
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                $('#contenidoCita').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>' + response.error + '</div>');
                return;
            }
            
            // Construir HTML con la información
            var html = '<div class="row">';
            
            // Información básica de la cita
            html += '<div class="col-md-6 mb-3">';
            html += '<div class="card border-0 shadow-sm h-100">';
            html += '<div class="card-body">';
            html += '<h6 class="card-title text-primary"><i class="fas fa-calendar-alt me-2"></i>Información de la Cita</h6>';
            html += '<hr>';
            html += '<p class="mb-2"><strong>Fecha:</strong> ' + response.fecha + '</p>';
            html += '<p class="mb-2"><strong>Hora:</strong> ' + response.hora_inicio + ' - ' + response.hora_fin + '</p>';
            html += '<p class="mb-2"><strong>Modalidad:</strong> ';
            if (response.modalidad.id == 1) {
                html += '<span class="badge bg-info">🏥 ' + response.modalidad.nombre + '</span>';
            } else if (response.modalidad.id == 2) {
                html += '<span class="badge bg-primary">💻 ' + response.modalidad.nombre + '</span>';
            } else {
                html += '<span class="badge bg-secondary">❔ ' + response.modalidad.nombre + '</span>';
            }
            html += '</p>';
            html += '<p class="mb-2"><strong>Estado del Horario:</strong> ';
            html += response.estado == 'disponible' ? '<span class="badge bg-success">Disponible</span>' : '<span class="badge bg-warning">Ocupado</span>';
            html += '</p>';
            var estadoCitaRaw = (response.estado_cita || '').toString().trim().toLowerCase();
            if (!estadoCitaRaw && response.paciente) estadoCitaRaw = 'pendiente';
            var estadoBadgeMap = {
                'pendiente': '<span class="badge" style="background-color: #FFA726; color: white;">Pendiente</span>',
                'agendada': '<span class="badge bg-primary">Agendada</span>',
                'confirmada': '<span class="badge" style="background-color: #4A90E2; color: white;">Confirmada</span>',
                'en_proceso': '<span class="badge" style="background-color: #FFA726; color: white;">En Proceso</span>',
                'completada': '<span class="badge" style="background-color: #90A4AE; color: white;">Completada</span>',
                'cancelada': '<span class="badge bg-danger">Cancelada</span>',
                'no_asistio': '<span class="badge bg-secondary">No Asistió</span>'
            };
            var estadoBadge = estadoBadgeMap[estadoCitaRaw] || (response.estado_cita ? '<span class="badge bg-secondary">' + response.estado_cita + '</span>' : '<span class="badge" style="background-color: #FFA726; color: white;">Pendiente</span>');
            html += '<p class="mb-2"><strong>Estado de la Cita:</strong> ' + estadoBadge + '</p>';
            html += '</div></div></div>';
            
            // Información del paciente (si existe)
            if (response.paciente) {
                html += '<div class="col-md-6 mb-3">';
                html += '<div class="card border-0 shadow-sm h-100">';
                html += '<div class="card-body">';
                html += '<h6 class="card-title text-success"><i class="fas fa-user me-2"></i>Información del Paciente</h6>';
                html += '<hr>';
                html += '<p class="mb-2"><strong>Nombre:</strong> ' + response.paciente.nombre + '</p>';
                if (response.paciente.rut_dni) {
                    html += '<p class="mb-2"><strong>RUT/DNI:</strong> ' + response.paciente.rut_dni + '</p>';
                }
                if (response.paciente.telefono) {
                    html += '<p class="mb-2"><strong>Teléfono:</strong> <a href="tel:' + response.paciente.telefono + '">' + response.paciente.telefono + '</a></p>';
                }
                if (response.paciente.email) {
                    html += '<p class="mb-2"><strong>Email:</strong> <a href="mailto:' + response.paciente.email + '">' + response.paciente.email + '</a></p>';
                }
                html += '</div></div></div>';
            }
            
            html += '</div>';
            
            // Información adicional de la consulta
            if (response.tipo_consulta || response.motivo || response.observaciones) {
                html += '<div class="row mt-3">';
                html += '<div class="col-12">';
                html += '<div class="card border-0 shadow-sm">';
                html += '<div class="card-body">';
                html += '<h6 class="card-title text-info"><i class="fas fa-file-medical me-2"></i>Detalles de la Consulta</h6>';
                html += '<hr>';
                if (response.tipo_consulta) {
                    var tipoConsulta = response.tipo_consulta.charAt(0).toUpperCase() + response.tipo_consulta.slice(1).replace('_', ' ');
                    html += '<p class="mb-2"><strong>Tipo de Consulta:</strong> ' + tipoConsulta + '</p>';
                }
                if (response.motivo) {
                    html += '<p class="mb-2"><strong>Motivo:</strong></p>';
                    html += '<p class="text-muted">' + response.motivo + '</p>';
                }
                if (response.observaciones) {
                    html += '<p class="mb-2"><strong>Observaciones:</strong></p>';
                    html += '<p class="text-muted">' + response.observaciones + '</p>';
                }
                html += '</div></div></div></div>';
            }
            
            // Información de fechas importantes
            if (response.fecha_confirmacion || response.fecha_cancelacion) {
                html += '<div class="row mt-3">';
                html += '<div class="col-12">';
                html += '<div class="card border-0 shadow-sm">';
                html += '<div class="card-body">';
                html += '<h6 class="card-title text-warning"><i class="fas fa-clock me-2"></i>Historial</h6>';
                html += '<hr>';
                if (response.fecha_confirmacion) {
                    html += '<p class="mb-2"><strong>Fecha de Confirmación:</strong> ' + response.fecha_confirmacion + '</p>';
                }
                if (response.fecha_cancelacion) {
                    html += '<p class="mb-2"><strong>Fecha de Cancelación:</strong> ' + response.fecha_cancelacion + '</p>';
                    if (response.motivo_cancelacion) {
                        html += '<p class="mb-2"><strong>Motivo de Cancelación:</strong></p>';
                        html += '<p class="text-muted">' + response.motivo_cancelacion + '</p>';
                    }
                }
                html += '</div></div></div></div>';
            }
            
            // Mostrar notas de consulta si existen (con formato HTML)
            if (response.notas_consulta) {
                html += '<div class="row mt-3">';
                html += '<div class="col-12">';
                html += '<div class="card border-0 shadow-sm" style="border-left: 4px solid #667eea !important;">';
                html += '<div class="card-body">';
                html += '<h6 class="card-title text-primary"><i class="fas fa-sticky-note me-2"></i>Notas de la Consulta</h6>';
                html += '<hr>';
                html += '<div class="notas-consulta">' + response.notas_consulta + '</div>';
                html += '</div></div></div></div>';
            }
            
            // Mostrar objetivos si existen
            if (response.objetivos) {
                html += '<div class="row mt-3">';
                html += '<div class="col-12">';
                html += '<div class="card border-0 shadow-sm" style="border-left: 4px solid #6BCB77 !important;">';
                html += '<div class="card-body">';
                html += '<h6 class="card-title text-success"><i class="fas fa-bullseye me-2"></i>Objetivos Establecidos</h6>';
                html += '<hr>';
                html += '<div class="objetivos-consulta">' + response.objetivos + '</div>';
                html += '</div></div></div></div>';
            }
            
            // Mostrar plan de alimentación si existe
            if (response.plan_alimentacion) {
                html += '<div class="row mt-3">';
                html += '<div class="col-12">';
                html += '<div class="card border-0 shadow-sm" style="border-left: 4px solid #4A90E2 !important;">';
                html += '<div class="card-body">';
                html += '<h6 class="card-title text-info"><i class="fas fa-utensils me-2"></i>Plan de Alimentación</h6>';
                html += '<hr>';
                html += '<div class="plan-alimentacion">' + response.plan_alimentacion + '</div>';
                html += '</div></div></div></div>';
            }
            
            // Mostrar recomendaciones si existen
            if (response.recomendaciones) {
                html += '<div class="row mt-3">';
                html += '<div class="col-12">';
                html += '<div class="card border-0 shadow-sm" style="border-left: 4px solid #FFA726 !important;">';
                html += '<div class="card-body">';
                html += '<h6 class="card-title text-warning"><i class="fas fa-lightbulb me-2"></i>Recomendaciones</h6>';
                html += '<hr>';
                html += '<div class="recomendaciones">' + response.recomendaciones + '</div>';
                html += '</div></div></div></div>';
            }
            
            // Botón para iniciar consulta (solo si está confirmada y no iniciada)
            if (response.paciente && response.estado_cita === 'confirmada' && !response.fecha_inicio_real) {
                html += '<div class="row mt-3">';
                html += '<div class="col-12 text-center d-flex flex-wrap justify-content-center gap-2">';
                html += '<a href="<?= base_url('dashboard/agenda/consulta?id=') ?>' + response.id + '" class="btn btn-success btn-lg">';
                html += '<i class="fas fa-play-circle me-2"></i>Iniciar Consulta';
                html += '</a>';
                html += '<button type="button" class="btn btn-outline-danger btn-lg" onclick="cancelarCitaCalendario(' + response.id + ')">';
                html += '<i class="fas fa-calendar-times me-2"></i>Cancelar cita';
                html += '</button>';
                html += '</div></div>';
            }

            // Cancelar citas pendientes/agendadas/en proceso (sin duplicar si ya se mostró arriba)
            var estadosCancelablesCal = ['pendiente', 'agendada', 'en_proceso'];
            if (response.paciente && estadosCancelablesCal.indexOf(estadoCitaRaw) !== -1 && !response.fecha_fin_real) {
                html += '<div class="row mt-3"><div class="col-12 text-center">';
                html += '<button type="button" class="btn btn-outline-danger" onclick="cancelarCitaCalendario(' + response.id + ')">';
                html += '<i class="fas fa-calendar-times me-2"></i>Cancelar cita</button>';
                html += '</div></div>';
            }
            
            // Si la consulta está en curso, mostrar botón para continuar
            if (response.paciente && response.fecha_inicio_real && !response.fecha_fin_real) {
                html += '<div class="row mt-3">';
                html += '<div class="col-12 text-center">';
                html += '<a href="<?= base_url('dashboard/agenda/consulta?id=') ?>' + response.id + '" class="btn btn-warning btn-lg">';
                html += '<i class="fas fa-clock me-2"></i>Consulta en Curso - Continuar';
                html += '</a>';
                html += '</div></div>';
            }
            
            // Si la consulta está completada, mostrar botón para ver detalles
            if (response.paciente && response.estado_cita === 'completada' && response.fecha_fin_real) {
                html += '<div class="row mt-3">';
                html += '<div class="col-12 text-center">';
                html += '<a href="<?= base_url('dashboard/agenda/consulta?id=') ?>' + response.id + '" class="btn btn-info btn-lg">';
                html += '<i class="fas fa-eye me-2"></i>Ver Detalles de la Consulta';
                html += '</a>';
                html += '</div></div>';
            }
            
            // Sección de Notas del Nutricionista (siempre visible, editable)
            html += '<div class="row mt-3">';
            html += '<div class="col-12">';
            html += '<div class="card border-0 shadow-sm" style="border-left: 4px solid #FFA726 !important;">';
            html += '<div class="card-body">';
            html += '<h6 class="card-title text-warning"><i class="fas fa-sticky-note me-2"></i>Notas y Recordatorios</h6>';
            html += '<hr>';
            html += '<form id="formNotasNutricionista" onsubmit="guardarNotasNutricionista(event, ' + response.id + ')">';
            html += '<div class="mb-3">';
            html += '<label for="notas_nutricionista" class="form-label"><small class="text-muted">Puntos clave, recordatorios o notas para esta consulta:</small></label>';
            html += '<textarea class="form-control" id="notas_nutricionista" name="notas_nutricionista" rows="4" placeholder="Ej: Revisar resultados de análisis, recordar hablar sobre dieta sin gluten, preparar plan de ejercicios...">' + (response.notas_nutricionista || '') + '</textarea>';
            html += '<small class="form-text text-muted">Estas notas son privadas y solo visibles para ti.</small>';
            html += '</div>';
            html += '<div class="d-flex justify-content-end">';
            html += '<button type="submit" class="btn btn-warning btn-sm">';
            html += '<i class="fas fa-save me-2"></i>Guardar Notas';
            html += '</button>';
            html += '</div>';
            html += '</form>';
            html += '</div></div></div></div>';
            
            $('#contenidoCita').html(html);
        },
        error: function(xhr) {
            var errorMsg = 'Error al cargar la información de la cita';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMsg = xhr.responseJSON.error;
            }
            $('#contenidoCita').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>' + errorMsg + '</div>');
        }
    });
}

var citaIdCancelarCalendario = null;

function cancelarCitaCalendario(detalleAgendaId) {
    citaIdCancelarCalendario = detalleAgendaId;
    $('#motivoCancelarCitaCalendario').val('');
    new bootstrap.Modal(document.getElementById('modalCancelarCitaCalendario')).show();
}

$('#btnConfirmarCancelarCitaCalendario').on('click', function() {
    if (!citaIdCancelarCalendario) return;
    var id = citaIdCancelarCalendario;
    var motivo = $('#motivoCancelarCitaCalendario').val().trim();
    var csrfToken = obtenerTokenCSRF() || $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
    var $btn = $(this);
    $btn.prop('disabled', true);
    $.ajax({
        url: '<?= base_url('dashboard/agenda/cancelarCita') ?>',
        type: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
        data: { csrf_test_name: csrfToken, id: id, motivo: motivo },
        dataType: 'json',
        success: function(response, textStatus, xhr) {
            actualizarTokenCSRF(xhr);
            $btn.prop('disabled', false);
            if (response && (response.success || response.message)) {
                bootstrap.Modal.getInstance(document.getElementById('modalCancelarCitaCalendario')).hide();
                $('#modalVerCita').modal('hide');
                citaIdCancelarCalendario = null;
                toastr.success(response.message || 'Cita cancelada', 'Éxito');
                if (typeof calendar !== 'undefined' && calendar) calendar.refetchEvents();
            } else {
                toastr.error((response && (response.message || response.error)) || 'Error al cancelar', 'Error');
            }
        },
        error: function(xhr) {
            actualizarTokenCSRF(xhr);
            $btn.prop('disabled', false);
            var r = xhr.responseJSON || {};
            toastr.error(r.message || r.error || 'Error al cancelar la cita', 'Error');
        }
    });
});

// Función para guardar las notas del nutricionista
function guardarNotasNutricionista(event, detalleAgendaId) {
    event.preventDefault();
    
    var notas = $('#notas_nutricionista').val();
    var csrfToken = obtenerTokenCSRF() || $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
    var csrfName = 'csrf_test_name';
    
    var formData = {
        detalle_agenda_id: detalleAgendaId,
        notas_nutricionista: notas,
        [csrfName]: csrfToken
    };
    
    var $submitBtn = $('#formNotasNutricionista button[type="submit"]');
    var originalText = $submitBtn.html();
    $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');
    
    $.ajax({
        url: '<?= base_url('dashboard/agenda/actualizarNotasNutricionista') ?>',
        type: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        data: formData,
        dataType: 'json',
        success: function(response, textStatus, xhr) {
            // Actualizar token CSRF después de petición exitosa
            actualizarTokenCSRF(xhr);
            
            if (response.success) {
                toastr.success(response.message || 'Notas guardadas correctamente', 'Éxito', {
                    timeOut: 3000,
                    progressBar: true
                });
                $submitBtn.prop('disabled', false).html(originalText);
            } else {
                toastr.error(response.error || 'Error al guardar las notas', 'Error', {
                    timeOut: 4000,
                    progressBar: true
                });
                $submitBtn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr) {
            // Intentar actualizar token incluso en errores
            actualizarTokenCSRF(xhr);
            
            var errorMsg = 'Error al guardar las notas';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            toastr.error(errorMsg, 'Error', {
                timeOut: 4000,
                progressBar: true
            });
            $submitBtn.prop('disabled', false).html(originalText);
        }
    });
}

// Sistema de notificaciones para consultas próximas
function verificarConsultasProximas() {
    $.ajax({
        url: '<?= base_url('dashboard/agenda/getConsultasProximas') ?>',
        type: 'GET',
        data: { minutos: 15 }, // Notificar 15 minutos antes
        dataType: 'json',
        success: function(consultas) {
            if (consultas && consultas.length > 0) {
                consultas.forEach(function(cita) {
                    var mensaje = 'Consulta próxima: ' + cita.paciente + ' a las ' + cita.hora;
                    if (cita.minutos_restantes <= 5) {
                        toastr.warning(mensaje + ' (en ' + cita.minutos_restantes + ' minutos)', '¡Consulta Próxima!', {
                            timeOut: 10000,
                            progressBar: true
                        });
                    } else {
                        toastr.info(mensaje + ' (en ' + cita.minutos_restantes + ' minutos)', 'Recordatorio', {
                            timeOut: 5000,
                            progressBar: true
                        });
                    }
                });
            }
        },
        error: function(xhr) {
            console.error('Error al verificar consultas próximas:', xhr);
        }
    });
}

// Verificar consultas próximas cada 5 minutos
setInterval(verificarConsultasProximas, 300000); // 5 minutos
// Verificar inmediatamente al cargar (después de 5 segundos)
setTimeout(verificarConsultasProximas, 5000);

</script>

<?= $this->endSection() ?>

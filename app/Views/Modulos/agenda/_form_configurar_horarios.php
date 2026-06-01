<?php
$modoFormHorarios = $modoFormHorarios ?? 'crear';
$modalidades = $modalidades ?? [];
?>
<div id="bloque_form_editar_info" class="alert alert-warning" style="<?= $modoFormHorarios === 'editar' ? '' : 'display:none;' ?>">
    <i class="fas fa-edit me-2"></i>
    <strong>Editando <span id="editar_cantidad_dias">0</span> día(s):</strong>
    <span id="editar_fechas_lista" class="d-block mt-1 small"></span>
</div>

<div id="bloque_form_crear" style="<?= $modoFormHorarios === 'crear' ? '' : 'display:none;' ?>">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Fecha de inicio <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>">
                <small class="form-text text-muted">Fecha desde la cual crear horarios</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Días a crear <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="dias_crear" name="dias" value="30" min="1" max="365">
                <small class="form-text text-muted">Número de días laborables</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="text-muted small mb-0">Duración por bloque</label>
                <p class="form-control-plaintext small mb-0">Se define abajo en «Horarios del día»</p>
            </div>
        </div>
    </div>

    <hr>

    <h6 class="mb-3"><i class="fas fa-calendar me-2"></i> Días de la Semana</h6>
    <div class="form-group">
        <div class="row">
            <div class="col-md-6">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="dia_lunes" name="dias_semana[]" value="1" checked>
                    <label class="form-check-label" for="dia_lunes">Lunes</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="dia_martes" name="dias_semana[]" value="2" checked>
                    <label class="form-check-label" for="dia_martes">Martes</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="dia_miercoles" name="dias_semana[]" value="3" checked>
                    <label class="form-check-label" for="dia_miercoles">Miércoles</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="dia_jueves" name="dias_semana[]" value="4" checked>
                    <label class="form-check-label" for="dia_jueves">Jueves</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="dia_viernes" name="dias_semana[]" value="5" checked>
                    <label class="form-check-label" for="dia_viernes">Viernes</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="dia_sabado" name="dias_semana[]" value="6">
                    <label class="form-check-label" for="dia_sabado">Sábado</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="dia_domingo" name="dias_semana[]" value="0">
                    <label class="form-check-label" for="dia_domingo">Domingo</label>
                </div>
            </div>
        </div>
    </div>

    <hr>
</div>

<h6 class="mb-3"><i class="fas fa-clock me-2"></i> Horarios del día</h6>
<div class="row mb-3">
    <div class="col-md-4">
        <div class="form-group">
            <label>Duración de cada cita (minutos) <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="duracion_cita" name="duracion" value="30" min="5" max="480" step="1">
        </div>
    </div>
</div>
<div class="alert alert-info" id="alerta_nota_horarios">
    <i class="fas fa-info-circle me-2"></i>
    <span id="texto_nota_horarios">Configure el horario laboral que se aplicará a los días seleccionados.</span>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Hora de Inicio <span class="text-danger">*</span></label>
            <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" value="09:00">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Hora de Fin <span class="text-danger">*</span></label>
            <input type="time" class="form-control" id="hora_fin" name="hora_fin" value="18:00">
        </div>
    </div>
</div>

<div class="form-group">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="incluir_almuerzo" name="incluir_almuerzo">
        <label class="form-check-label" for="incluir_almuerzo">
            Excluir horario de almuerzo
        </label>
    </div>
</div>

<div id="horario_almuerzo" style="display: none;">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Inicio Almuerzo</label>
                <input type="time" class="form-control" id="almuerzo_inicio" name="almuerzo_inicio" value="13:00">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Fin Almuerzo</label>
                <input type="time" class="form-control" id="almuerzo_fin" name="almuerzo_fin" value="14:00">
            </div>
        </div>
    </div>
</div>

<hr>

<div class="form-group">
    <label>Modalidad por Defecto <span class="text-danger">*</span></label>
    <select class="form-control" id="modalidad_id" name="modalidad_id">
        <option value="">-- Seleccione una modalidad --</option>
        <?php foreach ($modalidades as $modalidad) : ?>
            <option value="<?= (int) $modalidad->id ?>" <?= (int) $modalidad->id === 3 ? 'selected' : '' ?>>
                <?= esc($modalidad->nombre) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <small class="form-text text-muted">
        Modalidad por defecto para los bloques disponibles generados.
    </small>
</div>

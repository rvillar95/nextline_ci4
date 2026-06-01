<?= $this->extend('layout/dashboard') ?>

<?= $this->section('boton_pago/crear') ?>

<?php
$modoTarifa = !empty($modo_tarifa) || (!empty($es_edicion));
$desdeCita = !empty($cita) && !empty($cita->id);
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #667eea;
    }
    .btn-generar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 12px 35px;
        font-size: 1.05rem;
        font-weight: 600;
        border-radius: 8px;
    }
    .boton-url-container {
        background: #f8f9fa;
        border: 2px dashed #667eea;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
    }
    .boton-url {
        font-family: monospace;
        background: white;
        padding: 10px;
        border-radius: 4px;
        word-break: break-all;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;">
                            <i class="fas <?= !empty($es_edicion) ? 'fa-edit' : 'fa-plus-circle' ?> me-2"></i>
                            <?= !empty($es_edicion) ? 'Editar' : 'Nueva' ?> tarifa de consulta
                        </h2>
                        <p style="color: white;">
                            <?php if ($modoTarifa && !$desdeCita): ?>
                                Defina un precio fijo para elegirlo al agendar citas. El cobro al paciente se hace con Mercado Pago en ese momento.
                            <?php elseif ($desdeCita): ?>
                                Cobro puntual para la cita seleccionada (link de pago para un paciente).
                            <?php else: ?>
                                Esta tarifa aparecerá al agendar citas para cobrar con Mercado Pago.
                            <?php endif; ?>
                        </p>
                    </div>
                    <a href="<?= base_url('dashboard/boton-pago/lista') ?>" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i> Volver
                    </a>
                </div>
            </div>

            <?php if ($desdeCita): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Cita seleccionada:</strong>
                    <?php if (isset($cita->paciente) && $cita->paciente): ?>
                        <?= esc($cita->paciente->nombre . ' ' . ($cita->paciente->apellido ?? '')) ?>
                        <?php if (isset($cita->fecha)): ?>
                            — <?= date('d/m/Y', strtotime($cita->fecha)) ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php elseif ($modoTarifa): ?>
                <div class="alert alert-light border mb-3">
                    <i class="fas fa-lightbulb text-warning me-2"></i>
                    <strong>¿Cómo se usa?</strong> Cree tarifas como «Primera consulta — $50.000» o «Control — $35.000».
                    Al agendar una cita en el calendario, elija la tarifa y el sistema enviará el cobro al paciente después de que confirme la cita.
                </div>
            <?php endif; ?>

            <form id="formCrearBoton" class="section-card">
                <?= csrf_field() ?>

                <?php if (!empty($es_edicion) && isset($plantilla)): ?>
                    <input type="hidden" name="plantilla_id" value="<?= $plantilla->id ?? $plantilla['id'] ?? '' ?>">
                <?php endif; ?>

                <?php if ($modoTarifa || !empty($es_edicion)): ?>
                    <input type="hidden" name="es_plantilla" value="1">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Nombre de la tarifa <span class="text-danger">*</span></label>
                            <input type="text" name="titulo" class="form-control"
                                   value="<?= isset($plantilla) ? esc($plantilla->titulo ?? $plantilla['titulo'] ?? '') : ($desdeCita ? 'Consulta Nutricional - ' . date('d/m/Y', strtotime($cita->fecha ?? 'now')) : '') ?>"
                                   required placeholder="Ej: Control mensual, Primera consulta">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Monto <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="1" name="monto" class="form-control"
                                       value="<?= isset($plantilla) ? ($plantilla->monto ?? $plantilla['monto'] ?? '') : '' ?>"
                                       required placeholder="35000" min="1">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label>Moneda</label>
                            <select name="moneda" class="form-control">
                                <option value="CLP" selected>CLP (Peso chileno)</option>
                                <option value="USD" <?= (isset($plantilla) && ($plantilla->moneda ?? '') === 'USD') ? 'selected' : '' ?>>USD</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label>Descripción <small class="text-muted">(opcional, uso interno)</small></label>
                            <textarea name="descripcion" class="form-control" rows="2"
                                      placeholder="Ej: Incluye plan alimentario inicial"><?= isset($plantilla) ? esc($plantilla->descripcion ?? $plantilla['descripcion'] ?? '') : '' ?></textarea>
                        </div>
                    </div>
                </div>

                <?php if ($desdeCita): ?>
                <div class="border-top pt-3 mt-2" id="bloqueCobroPuntual">
                    <p class="small text-muted mb-3">Para cobrar solo esta cita (sin guardar en el menú), desmarque la opción siguiente y complete el correo del paciente.</p>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="es_plantilla" id="es_plantilla" value="1">
                        <label class="form-check-label" for="es_plantilla">Guardar también como tarifa reutilizable</label>
                    </div>
                    <div id="datosPagador">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Email del paciente <span class="text-danger" id="emailRequired">*</span></label>
                                    <input type="email" name="email_pagador" id="email_pagador" class="form-control"
                                           value="<?= isset($cita->paciente) ? esc($cita->paciente->email ?? '') : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label>Nombre</label>
                                    <input type="text" name="nombre_pagador" class="form-control"
                                           value="<?= isset($cita->paciente) ? esc($cita->paciente->nombre ?? '') : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label>Apellido</label>
                                    <input type="text" name="apellido_pagador" class="form-control"
                                           value="<?= isset($cita->paciente) ? esc($cita->paciente->apellido ?? '') : '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="detalle_agenda_id" value="<?= (int)$cita->id ?>">
                </div>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-generar" id="btnGenerar">
                        <i class="fas fa-save me-2"></i> <span id="btnText"><?= ($modoTarifa && !$desdeCita) || !empty($es_edicion) ? 'Guardar tarifa' : 'Generar link de pago' ?></span>
                    </button>
                    <a href="<?= base_url('dashboard/boton-pago/lista') ?>" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </a>
                </div>
            </form>

            <div id="botonGenerado" class="section-card" style="display: none;">
                <h4><i class="fas fa-check-circle text-success me-2"></i> Link de pago generado</h4>
                <p class="text-muted">Comparta este enlace con el paciente:</p>
                <div class="boton-url-container">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>URL</strong>
                        <button type="button" class="btn btn-sm btn-primary" onclick="copiarUrl()">
                            <i class="fas fa-copy me-1"></i> Copiar
                        </button>
                    </div>
                    <div class="boton-url" id="urlBoton"></div>
                </div>
                <div class="mt-3">
                    <a href="<?= base_url('dashboard/boton-pago/lista') ?>" class="btn btn-primary">
                        <i class="fas fa-list me-2"></i> Volver a mis tarifas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var desdeCita = <?= $desdeCita ? 'true' : 'false' ?>;

    function aplicarModoPlantilla(esPlantilla) {
        if (!desdeCita) return;
        var emailField = $('#email_pagador');
        if (esPlantilla) {
            $('#btnText').text('Guardar tarifa');
            emailField.prop('required', false);
            $('#datosPagador').slideUp(200);
        } else {
            $('#btnText').text('Generar link de pago');
            emailField.prop('required', true);
            $('#datosPagador').slideDown(200);
        }
    }

    if (desdeCita) {
        $('#es_plantilla').on('change', function() {
            aplicarModoPlantilla($(this).is(':checked'));
        });
        aplicarModoPlantilla($('#es_plantilla').is(':checked'));
    }

    $('#formCrearBoton').on('submit', function(e) {
        e.preventDefault();
        var btnGenerar = $('#btnGenerar');
        var originalText = btnGenerar.html();
        btnGenerar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');

        var csrfToken = $('input[name="<?= csrf_token() ?>"]').val();
        if (!csrfToken) {
            toastr.error('Recargue la página e intente de nuevo.', 'Error');
            btnGenerar.prop('disabled', false).html(originalText);
            return;
        }

        $.ajax({
            url: '<?= base_url('dashboard/boton-pago/generar') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function(response) {
                if (response.csrf_hash) {
                    $('input[name="<?= csrf_token() ?>"]').val(response.csrf_hash);
                }
                if (response.success) {
                    if (response.es_plantilla) {
                        toastr.success(response.message || 'Tarifa guardada.', 'Listo');
                        setTimeout(function() {
                            window.location.href = '<?= base_url('dashboard/boton-pago/lista') ?>';
                        }, 1200);
                    } else {
                        $('#urlBoton').text(response.boton_url);
                        $('#botonGenerado').slideDown();
                        $('#formCrearBoton').slideUp();
                        toastr.success('Link de pago generado.', 'Listo');
                    }
                } else {
                    toastr.error(response.error || 'No se pudo guardar.', 'Error');
                    btnGenerar.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                var errorMsg = 'Error al guardar';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                toastr.error(errorMsg, 'Error');
                btnGenerar.prop('disabled', false).html(originalText);
                var headerToken = xhr.getResponseHeader('X-CSRF-TOKEN');
                if (headerToken) {
                    $('input[name="<?= csrf_token() ?>"]').val(headerToken);
                }
            }
        });
    });
});

function copiarUrl() {
    var url = $('#urlBoton').text();
    navigator.clipboard.writeText(url).then(function() {
        toastr.success('URL copiada', 'Listo');
    });
}
</script>

<?= $this->endSection() ?>

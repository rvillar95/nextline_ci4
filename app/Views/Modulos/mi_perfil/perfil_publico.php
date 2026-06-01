<?php
/** @var array $usuario */
/** @var array $credenciales */
/** @var array $tipos_credencial */
/** @var array $tipos_label */
$u = $usuario;
?>
<div class="card mb-4 border-success">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-globe me-2"></i> Perfil público (página Equipo)</h5>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">Esta información se muestra en <a href="<?= base_url('equipo') ?>" target="_blank" rel="noopener">/equipo</a> para que los pacientes te conozcan antes de reservar.</p>

        <form id="formPerfilPublico">
            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small">Título profesional</label>
                    <input type="text" class="form-control form-control-sm" name="titulo_profesional" maxlength="150"
                           value="<?= esc($u['titulo_profesional'] ?? '') ?>" placeholder="Ej. Nutricionista clínica">
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Especialidad</label>
                    <input type="text" class="form-control form-control-sm" name="especialidad" maxlength="200"
                           value="<?= esc($u['especialidad'] ?? '') ?>" placeholder="Ej. Diabetes, deporte">
                </div>
                <div class="col-12">
                    <label class="form-label small">Carrera / formación principal</label>
                    <input type="text" class="form-control form-control-sm" name="carrera" maxlength="200"
                           value="<?= esc($u['carrera'] ?? '') ?>" placeholder="Ej. Nutrición, Universidad de Chile">
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Teléfono (visible en web)</label>
                    <input type="text" class="form-control form-control-sm" name="telefono" maxlength="100"
                           value="<?= esc($u['telefono'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Correo (visible en web)</label>
                    <input type="email" class="form-control form-control-sm" name="correo" maxlength="100"
                           value="<?= esc($u['correo'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label small">Presentación</label>
                    <textarea class="form-control form-control-sm" name="presentacion" rows="3"
                              placeholder="Breve mensaje de bienvenida para pacientes"><?= esc($u['presentacion'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label small">Descripción profesional</label>
                    <textarea class="form-control form-control-sm" name="descripcion_profesional" rows="4"
                              placeholder="Experiencia, enfoque de trabajo, trayectoria"><?= esc($u['descripcion_profesional'] ?? '') ?></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-success btn-sm mt-3" id="btnGuardarPerfilPublico">
                <i class="fas fa-save me-1"></i> Guardar perfil público
            </button>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0"><i class="fas fa-certificate me-2"></i> Formación y documentos</h5>
    </div>
    <div class="card-body">
        <div id="listaCredenciales" class="mb-4">
            <?php if (empty($credenciales)): ?>
                <p class="text-muted small mb-0" id="sinCredenciales">Aún no has agregado títulos, diplomados o cursos.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0" id="tablaCredenciales">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Nombre</th>
                                <th>Institución</th>
                                <th>Año</th>
                                <th>Web</th>
                                <th>Doc.</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($credenciales as $c): ?>
                                <tr data-id="<?= (int) $c['id'] ?>">
                                    <td><?= esc($tipos_label[$c['tipo']] ?? $c['tipo']) ?></td>
                                    <td><?= esc($c['nombre']) ?></td>
                                    <td><?= esc($c['institucion'] ?? '—') ?></td>
                                    <td><?= !empty($c['anio']) ? (int) $c['anio'] : '—' ?></td>
                                    <td><?= ($c['visible_web'] ?? 'S') === 'S' ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                                    <td>
                                        <?php if (!empty($c['archivo_ruta'])): ?>
                                            <a href="<?= base_url($c['archivo_ruta']) ?>" target="_blank" rel="noopener" class="small">Ver</a>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-nowrap">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-editar-credencial"
                                                data-credencial="<?= htmlspecialchars(json_encode($c), ENT_QUOTES, 'UTF-8') ?>">Editar</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-credencial"
                                                data-id="<?= (int) $c['id'] ?>">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <hr>
        <h6 class="mb-3" id="tituloFormCredencial">Agregar credencial</h6>
        <form id="formCredencial" enctype="multipart/form-data">
            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
            <input type="hidden" name="id" id="credencial_id" value="0">
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label small">Tipo</label>
                    <select name="tipo" id="credencial_tipo" class="form-select form-select-sm" required>
                        <?php foreach ($tipos_credencial as $t): ?>
                            <option value="<?= esc($t) ?>"><?= esc($tipos_label[$t] ?? $t) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label small">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" id="credencial_nombre" class="form-control form-control-sm" maxlength="200" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Institución</label>
                    <input type="text" name="institucion" id="credencial_institucion" class="form-control form-control-sm" maxlength="200">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Año</label>
                    <input type="number" name="anio" id="credencial_anio" class="form-control form-control-sm" min="1950" max="<?= (int) date('Y') + 1 ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Orden</label>
                    <input type="number" name="orden" id="credencial_orden" class="form-control form-control-sm" value="0" min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Visible en web</label>
                    <select name="visible_web" id="credencial_visible" class="form-select form-select-sm">
                        <option value="S">Sí</option>
                        <option value="N">No</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label small">Documento (PDF o imagen, máx. 5 MB)</label>
                    <input type="file" name="archivo" id="credencial_archivo" class="form-control form-control-sm"
                           accept=".pdf,image/jpeg,image/png,image/webp">
                </div>
                <div class="col-12">
                    <label class="form-label small">Descripción</label>
                    <textarea name="descripcion" id="credencial_descripcion" class="form-control form-control-sm" rows="2"></textarea>
                </div>
            </div>
            <div class="mt-2">
                <button type="submit" class="btn btn-primary btn-sm" id="btnGuardarCredencial">
                    <i class="fas fa-plus me-1"></i> Guardar credencial
                </button>
                <button type="button" class="btn btn-light btn-sm d-none" id="btnCancelarCredencial">Cancelar edición</button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    var csrfToken = '<?= csrf_hash() ?>';
    var csrfName = '<?= csrf_token() ?>';

    function refreshCsrf(res) {
        if (res && res.csrf_token) {
            csrfToken = res.csrf_token;
            $('input[name="' + csrfName + '"]').val(csrfToken);
        }
    }

    $('#formPerfilPublico').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btnGuardarPerfilPublico').prop('disabled', true);
        $.post('<?= base_url('dashboard/mi-perfil/perfil-publico') ?>', $(this).serialize())
            .done(function(res) {
                refreshCsrf(res);
                if (res.success) toastr.success(res.message || 'Guardado');
                else toastr.error(res.error || 'Error');
            })
            .fail(function(xhr) {
                toastr.error((xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'Error al guardar');
            })
            .always(function() { $btn.prop('disabled', false); });
    });

    function resetFormCredencial() {
        $('#credencial_id').val('0');
        $('#formCredencial')[0].reset();
        $('#credencial_orden').val('0');
        $('#credencial_visible').val('S');
        $('#tituloFormCredencial').text('Agregar credencial');
        $('#btnCancelarCredencial').addClass('d-none');
        $('#credencial_archivo').prop('required', false);
    }

    $('#btnCancelarCredencial').on('click', resetFormCredencial);

    $('.btn-editar-credencial').on('click', function() {
        var c = $(this).data('credencial');
        if (!c) return;
        $('#credencial_id').val(c.id);
        $('#credencial_tipo').val(c.tipo);
        $('#credencial_nombre').val(c.nombre);
        $('#credencial_institucion').val(c.institucion || '');
        $('#credencial_anio').val(c.anio || '');
        $('#credencial_orden').val(c.orden || 0);
        $('#credencial_visible').val(c.visible_web || 'S');
        $('#credencial_descripcion').val(c.descripcion || '');
        $('#tituloFormCredencial').text('Editar credencial');
        $('#btnCancelarCredencial').removeClass('d-none');
    });

    $('#formCredencial').on('submit', function(e) {
        e.preventDefault();
        var fd = new FormData(this);
        fd.set(csrfName, csrfToken);
        var $btn = $('#btnGuardarCredencial').prop('disabled', true);
        $.ajax({
            url: '<?= base_url('dashboard/mi-perfil/credencial') ?>',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false
        }).done(function(res) {
            refreshCsrf(res);
            if (res.success) {
                toastr.success(res.message || 'Guardado');
                setTimeout(function() { location.reload(); }, 500);
            } else toastr.error(res.error || 'Error');
        }).fail(function(xhr) {
            toastr.error((xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'Error');
        }).always(function() { $btn.prop('disabled', false); });
    });

    $(document).on('click', '.btn-eliminar-credencial', function() {
        if (!confirm('¿Eliminar esta credencial?')) return;
        var id = $(this).data('id');
        var data = {};
        data[csrfName] = csrfToken;
        data.id = id;
        $.post('<?= base_url('dashboard/mi-perfil/credencial/eliminar') ?>', data)
            .done(function(res) {
                refreshCsrf(res);
                if (res.success) {
                    toastr.success(res.message);
                    $('tr[data-id="' + id + '"]').remove();
                    if (!$('#tablaCredenciales tbody tr').length) location.reload();
                } else toastr.error(res.error);
            })
            .fail(function(xhr) {
                toastr.error((xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'Error');
            });
    });
})();
</script>

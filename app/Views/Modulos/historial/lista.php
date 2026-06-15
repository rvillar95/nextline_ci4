<?= $this->extend('layout/dashboard') ?>

<?= $this->section('historial/lista') ?>

<!-- Tagify para búsqueda por tags -->
<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@4.17.9/dist/tagify.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify@4.17.9/dist/tagify.min.js"></script>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-history me-2"></i> Historial Clínico</h2>
                        <p style="color: white;">Registro de consultas y evolución de pacientes</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if (!empty($retorno_consulta_id)): ?>
                        <a href="<?= base_url('dashboard/agenda/consulta?id=' . (int)$retorno_consulta_id) ?>" class="btn btn-light me-2">
                            <i class="fas fa-arrow-left me-2"></i> Volver a la consulta
                        </a>
                        <?php endif; ?>
                        <a href="<?= base_url('dashboard/historial/comparar' . (!empty($retorno_consulta_id) ? '?retorno=consulta&id=' . (int)$retorno_consulta_id : '')) ?>" class="btn btn-light me-2">
                            <i class="fas fa-chart-line me-2"></i> Comparar Historiales
                        </a>
                    </div>
                </div>
            </div>

            <div class="section-card" style="border-left-color: #fa709a;">
                <!-- Filtros de búsqueda -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Buscar por Tags</label>
                        <input type="text" name="tags_busqueda" id="tags_busqueda" class="form-control" 
                               placeholder="Ej: diabetes, hipertensión, seguimiento">
                        <small class="text-muted">Escriba los tags separados por comas y presione Enter</small>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="button" class="btn btn-primary me-2" onclick="aplicarFiltros()">
                            <i class="fas fa-search me-2"></i> Buscar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="limpiarFiltros()">
                            <i class="fas fa-times me-2"></i> Limpiar
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tablaHistorial" class="table table-bordered table-striped nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Tipo</th>
                                <th>Medidas</th>
                                <th>Motivo</th>
                                <th>Tags</th>
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

<div class="modal fade" id="modalConfirmarEliminarHistorial" tabindex="-1" aria-labelledby="modalConfirmarEliminarHistorialLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarEliminarHistorialLabel">Eliminar registro clínico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>¿Desea eliminar este registro del historial clínico?</p>
                <p class="text-muted small mb-0">
                    Se borrarán las mediciones, anamnesis y exámenes asociados a esta consulta.
                    La cita en agenda y los planes alimentarios vinculados a la misma fecha no se eliminan.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminarHistorial">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
var table;
var tagifyBusqueda = null;
var historialIdAEliminar = null;

$(document).ready(function() {
    // Inicializar Tagify para búsqueda por tags
    var inputBusqueda = document.querySelector('#tags_busqueda');
    if (inputBusqueda) {
        var tagsSugeridos = <?= json_encode(array_column($tags_sugeridos ?? [], 'tag_display')) ?>;
        tagifyBusqueda = new Tagify(inputBusqueda, {
            whitelist: tagsSugeridos,
            maxTags: 10,
            dropdown: {
                maxItems: 20,
                classname: 'tags-look',
                enabled: 1,
                closeOnSelect: false
            }
        });

        // Cargar tags sugeridos dinámicamente
        tagifyBusqueda.on('input', function(e) {
            var value = e.detail.value;
            if (value.length < 1) return;
            
            $.ajax({
                url: '<?= base_url('dashboard/historial/getTagsSugeridos') ?>',
                dataType: 'json',
                data: { q: value },
                success: function(data) {
                    var whitelist = data.results.map(function(item) {
                        return item.text;
                    });
                    tagifyBusqueda.settings.whitelist = whitelist;
                    tagifyBusqueda.dropdown.show.call(tagifyBusqueda, value);
                }
            });
        });

        // Buscar al presionar Enter
        tagifyBusqueda.on('add', function() {
            aplicarFiltros();
        });
    }

    // Ordenar columna Fecha por data-order (Y-m-d), no por texto dd/mm/yyyy
    $.fn.dataTable.ext.type.order['fecha-historial-pre'] = function(data) {
        var $el = $('<div>').html(data);
        var order = $el.find('[data-order]').attr('data-order');
        if (order) {
            return order;
        }
        var text = ($el.text() || data || '').trim();
        var m = text.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
        if (m) {
            return m[3] + '-' + m[2] + '-' + m[1];
        }
        return '0000-00-00';
    };

    // Inicializar DataTable
    table = $('#tablaHistorial').DataTable({
        "processing": true,
        "serverSide": false, // Cambiar a false para permitir filtros personalizados
        "ajax": {
            "url": "<?= base_url('dashboard/historial/getHistorial') ?>",
            "type": "GET",
            "data": function(d) {
                // Agregar filtro de tags
                if (tagifyBusqueda && tagifyBusqueda.value && tagifyBusqueda.value.length > 0) {
                    var tagsArray = tagifyBusqueda.value.map(function(item) {
                        return typeof item === 'string' ? item : (item.value || item);
                    });
                    d.tags = tagsArray.join(',');
                }
            }
        },
        "columns": [
            { "data": 0 },
            { "data": 1 },
            { "data": 2 },
            { "data": 3 },
            { "data": 4 },
            { "data": 5 },
            { "data": 6 },
            { "data": 7, "orderable": false }
        ],
        "columnDefs": [
            { "targets": 1, "type": "fecha-historial" }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "responsive": true,
        "pageLength": 25,
        "order": [[1, "desc"]]
    });

    $('#btnConfirmarEliminarHistorial').on('click', function() {
        if (!historialIdAEliminar) {
            return;
        }
        var id = historialIdAEliminar;
        var csrfName = window.NutriNextCsrf ? NutriNextCsrf.getName() : '<?= csrf_token() ?>';
        var csrfToken = window.NutriNextCsrf ? NutriNextCsrf.getToken() : '<?= csrf_hash() ?>';
        var postData = { id: id };
        postData[csrfName] = csrfToken;

        $('#btnConfirmarEliminarHistorial').prop('disabled', true);

        $.ajax({
            url: '<?= site_url('dashboard/historial/eliminar') ?>',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            data: postData,
            success: function(response) {
                if (window.NutriNextCsrf) NutriNextCsrf.applyFromJson(response);
                if (response.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Registro eliminado correctamente');
                    }
                    var modalEl = document.getElementById('modalConfirmarEliminarHistorial');
                    if (modalEl && typeof bootstrap !== 'undefined') {
                        bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                    }
                    $('#tablaHistorial').DataTable().ajax.reload(null, false);
                } else if (typeof toastr !== 'undefined') {
                    toastr.error(response.message || 'Error al eliminar el registro');
                }
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message
                    : (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error
                    : 'Error al eliminar el registro';
                if (typeof toastr !== 'undefined') {
                    toastr.error(msg);
                }
            },
            complete: function() {
                $('#btnConfirmarEliminarHistorial').prop('disabled', false);
                historialIdAEliminar = null;
            }
        });
    });
});

function aplicarFiltros() {
    var tableInstance = $('#tablaHistorial').DataTable();
    if (tableInstance) {
        tableInstance.ajax.reload();
    }
}

function limpiarFiltros() {
    if (tagifyBusqueda) {
        tagifyBusqueda.removeAllTags();
    }
    var tableInstance = $('#tablaHistorial').DataTable();
    if (tableInstance) {
        tableInstance.ajax.reload();
    }
}

function verHistorial(id) {
    window.location.href = '<?= base_url('dashboard/historial/editar/') ?>' + id;
}

function editarHistorial(id) {
    window.location.href = '<?= base_url('dashboard/historial/editar/') ?>' + id;
}

function eliminarHistorial(id) {
    historialIdAEliminar = id;
    var modalEl = document.getElementById('modalConfirmarEliminarHistorial');
    if (modalEl && typeof bootstrap !== 'undefined') {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
}
</script>

<?= $this->endSection() ?>

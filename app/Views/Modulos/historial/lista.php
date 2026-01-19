<?= $this->extend('layout/dashboard') ?>

<?= $this->section('historial/lista') ?>

<!-- Tagify para búsqueda por tags -->
<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@4.17.9/dist/tagify.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify@4.17.9/dist/tagify.min.js"></script>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-history me-2"></i> Historial Clínico</h2>
                        <p style="color: white;">Registro de consultas y evolución de pacientes</p>
                    </div>
                    <div>
                        <a href="<?= base_url('dashboard/historial/comparar') ?>" class="btn btn-light me-2">
                            <i class="fas fa-chart-line me-2"></i> Comparar Historiales
                        </a>
                        <a href="<?= base_url('dashboard/historial/registro') ?>" class="btn btn-light">
                            <i class="fas fa-plus me-2"></i> Nueva Consulta
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
                                <th>Estado</th>
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
var table;
var tagifyBusqueda = null;

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
            { "data": 7 },
            { "data": 8, "orderable": false }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "responsive": true,
        "pageLength": 25,
        "order": [[1, "desc"]]
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
    if (!confirm('¿Está seguro de que desea eliminar este registro del historial clínico?')) {
        return;
    }
    
    $.ajax({
        url: '<?= base_url('dashboard/historial/eliminar') ?>',
        type: 'POST',
        data: {
            id: id,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.success) {
                toastr.success('Registro eliminado correctamente');
                var tableInstance = $('#tablaHistorial').DataTable();
                if (tableInstance) {
                    tableInstance.ajax.reload();
                }
            } else {
                toastr.error(response.message || 'Error al eliminar el registro');
            }
        },
        error: function() {
            toastr.error('Error al eliminar el registro');
        }
    });
}
</script>

<?= $this->endSection() ?>

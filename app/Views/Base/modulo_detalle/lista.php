<?php $this->extend('layout/dashboard') ?>


<?= $this->section("modulo_detalle/lista") ?>

<div id="basic" class="col-lg-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <div class="mb-3">
                        <h4>Lista de Modulo Detalle</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">

            <div class="row">
                <div class="col-lg-12 col-12 ">
                    <div class="row">
                        <div class="col-lg-4 col-4 ">
                            <label for="perfil_id" class="me-2">Filtro por Módulo:</label>
                            <select name="modulo_id" id="modulo_id" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <?php foreach ($modulos as $m): ?>
                                    <option value="<?= (int)$m['id'] ?>" <?= (int)$selectedModuloId === (int)$m['id'] ? 'selected' : '' ?>>
                                        <?= esc($m['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <br>
                        </div>
                    </div>
                    <?php if (session()->getFlashdata('errors') !== null) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?php if (!is_array(session()->getFlashdata('errors'))) : ?>
                                <?= session()->getFlashdata('errors'); ?>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('success') !== null) : ?>
                        <div class="alert alert-success my-3" role="alert">
                            <?= session()->getFlashdata('success'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tabla-modulo-detalle">
                            <thead>
                                <tr>
                                    <th>Módulo</th>
                                    <th>Descripción</th>
                                    <th>Ruta</th>
                                    <th>Acción</th>
                                    <th>Mostrar</th>
                                    <th>Estado</th>
                                    <th>Orden</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEliminacion" tabindex="-1" aria-labelledby="modalEliminacionTitle" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEliminacionTitle">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <p class="modal-text">¿Estás seguro de que deseas eliminar este modulo detalle? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button"
                    class="btn btn-light-dark _effect--ripple waves-effect waves-light"
                    data-bs-dismiss="modal">
                    Cancelar
                </button>
                <form method="POST" action="<?= base_url('dashboard/modulo-detalle/eliminar'); ?>"
                    onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                    <?= csrf_field() ?>
                    <input type="hidden" id="id" name="id" value="">
                    <input type="hidden" id="modulo_id_filter" name="modulo_id_filter" value="">
                    <button type="submit"
                        class="btn btn-danger"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Eliminar">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    const dt = $('#tabla-modulo-detalle').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '<?= base_url('dashboard/modulo-detalle/getModuloDetalle') ?>',
            type: 'GET',
            data: function(d) {
                d.modulo_id = document.getElementById('modulo_id').value || '';
            }
        },
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        columns: [{
                data: 'modulo_nombre'
            },
            {
                data: 'descripcion'
            },
            {
                data: 'ruta'
            },
            {
                data: 'accion'
            },
            {
                data: 'mostrar_html',
                orderable: false,
                searchable: false
            },
            {
                data: 'estado_html',
                orderable: false,
                searchable: false
            },
            {
                data: 'orden'
            },
            {
                data: 'acciones_html',
                orderable: false,
                searchable: false
            }
        ]
    });

    document.getElementById('modulo_id').addEventListener('change', function() {
        dt.ajax.reload(null, true);
    });

    $('body').on('click', '#btnEliminar', function(e) {
        e.preventDefault();
        $('#id').val(this.value);
        // Capturar el filtro actual del select
        $('#modulo_id_filter').val(document.getElementById('modulo_id').value);
        $('#modalEliminacion').modal('show');
    });
</script>
<?= $this->endSection() ?>
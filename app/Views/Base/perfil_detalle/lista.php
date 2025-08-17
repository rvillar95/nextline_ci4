<?php $this->extend('layout/dashboard') ?>


<?= $this->section("perfil_detalle/lista") ?>
<div id="basic" class="col-lg-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <div class="mb-3">
                        <h4>Lista de Perfil Detalle</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">

            <div class="row">
                <div class="col-lg-12 col-12 ">
                    <div class="row">
                        <div class="col-lg-4 col-4 ">
                            <label for="perfil_id" class="me-2">Filtro por perfil:</label>
                            <select name="perfil_id" id="perfil_id" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <?php foreach ($perfiles as $p): ?>
                                    <option value="<?= (int)$p['id'] ?>" <?= (int)$selectedPerfilId === (int)$p['id'] ? 'selected' : '' ?>>
                                        <?= esc($p['nombre']) ?>
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
                        <table class="table table-bordered" id="tabla-perfil-detalle">
                            <thead>
                                <tr>
                                    <th>Perfil</th>
                                    <th>Módulo</th>
                                    <th>Ver</th>
                                    <th>Registrar</th>
                                    <th>Editar</th>
                                    <th>Eliminar</th>
                                    <th>Orden</th>
                                    <th>Estado</th>
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
                <p class="modal-text">¿Estás seguro de que deseas eliminar este perfil detalle? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light-dark _effect--ripple waves-effect waves-light" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="<?= base_url('dashboard/perfil-detalle/eliminar'); ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" id="id" name="id" value="">
                    <button class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const dt = $('#tabla-perfil-detalle').DataTable({
        serverSide: true,
        processing: true,
        searching: true,
        ordering: true,
        ajax: {
            url: '<?= base_url('dashboard/perfil-detalle/getPerfilDetalle') ?>',
            type: 'GET',
            data: function(d) {
                d.perfil_id = document.getElementById('perfil_id').value || '';
            }
        },
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        columns: [{
                data: 'perfil_nombre'
            },
            {
                data: 'modulo_nombre'
            },
            {
                data: 'ver_html',
                orderable: false,
                searchable: false
            },
            {
                data: 'registrar_html',
                orderable: false,
                searchable: false
            },
            {
                data: 'editar_html',
                orderable: false,
                searchable: false
            },
            {
                data: 'eliminar_html',
                orderable: false,
                searchable: false
            },
            {
                data: 'orden'
            },
            {
                data: 'estado_html',
                orderable: false,
                searchable: false
            },
            {
                data: 'acciones_html',
                orderable: false,
                searchable: false
            }
        ]
    });

    // Recargar solo la tabla cuando cambie el perfil
    document.getElementById('perfil_id').addEventListener('change', function() {
        dt.ajax.reload(null, true);
    });

    $("body").on("click", "#btnEliminar", function(e) {
        e.preventDefault();
        $("#id").val(this.value);
        $("#modalEliminacion").modal("show");
    });
</script>

<script>
    /*getPerfil();

    function getPerfil() {
        $('.getPerfilDetalle').DataTable().clear().destroy();
        $('.getPerfilDetalle').DataTable({
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Registros _MENU_ ",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla =(",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                },
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad"
                }
            },
            "ajax": {
                url: 'getPerfilDetalle',
                type: 'GET',
                data: {
                    perfil_id: document.getElementById('perfil_id').value || ''
                }
            }
        });
    }

    $("body").on("click", "#btnEliminar", function(e) {
        e.preventDefault();
        console.log(this.value);
        $("#id").attr("value", this.value);
        $("#modalEliminacion").modal("show");
    });*/
</script>

<?= $this->endSection() ?>
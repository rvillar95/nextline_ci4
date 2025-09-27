<?php $this->extend('layout/dashboard') ?>


<?= $this->section("galeria/lista") ?>

<div id="basic" class="col-lg-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <div class="mb-3">
                        <h4>Lista de Galería</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">

            <div class="row">
                <div class="col-lg-12 col-12 ">
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
                        <table class="table table-bordered getGaleria">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Portada</th>
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
                <p class="modal-text">¿Estás seguro de que deseas eliminar esta galería? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light-dark _effect--ripple waves-effect waves-light" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="<?= base_url('dashboard/galeria/eliminar'); ?>">
                      <?= csrf_field() ?>
                    <input type="hidden" id="id" name="id" value="">
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

<div class="modal fade" id="modalDetalle" tabindex="-1" aria-labelledby="modalDetalle" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalleTitle">Detalle de Galería</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div id="detalleContenido">
                    <!-- El contenido se cargará aquí -->
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light-dark _effect--ripple waves-effect waves-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script>
    getGaleria();

    function getGaleria() {
        $('.getGaleria').DataTable().clear().destroy();
        $('.getGaleria').DataTable({
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
                url: 'getGaleria',
                type: 'GET'
            }
        });
    }

    $("body").on("click", "#btnEliminar", function(e) {
        e.preventDefault();
        console.log(this.value);
        $("#id").attr("value", this.value);
        $("#modalEliminacion").modal("show");
    });

    $("body").on("click", "#btnVerDetalle", function(e) {
        e.preventDefault();
        var galeriaId = this.value;
        
        // Aquí podrías hacer una llamada AJAX para obtener los detalles
        // Por ahora, mostraremos un mensaje simple
        $("#detalleContenido").html(`
            <div class="text-center">
                <h6>Detalle de Galería ID: ${galeriaId}</h6>
                <p>Esta funcionalidad se puede expandir para mostrar más detalles de la galería.</p>
            </div>
        `);
        $("#modalDetalle").modal("show");
    });
</script>

<?= $this->endSection() ?>
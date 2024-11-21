<?php $this->extend('layout/dashboard') ?>


<?= $this->section("galeria/lista") ?>

<div id="basic" class="col-lg-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <div class="mb-3">
                        <h4>Lista de Galeria</h4>
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
                                    <th>Descripción</th>
                                    <th>Portada</th>
                                    <th>fecha</th>
                                    <th>estado</th>
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
                <p class="modal-text">¿Estás seguro de que deseas eliminar esta galeria? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light-dark _effect--ripple waves-effect waves-light" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="<?= base_url('dashboard/galeria/eliminar'); ?>">
                    <input type="hidden" id="id" name="id" value="">
                    <button class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalle" tabindex="-1" aria-labelledby="modalDetalle" aria-hidden="true" style="display: none;">
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
                <?= form_open_multipart('dashboard/galeria/registrar') ?>
                <?= csrf_field(); ?>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" id="nombre" name="nombre" value="<?= set_value('nombre'); ?>" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['nombre'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['nombre']; ?>
                        <p>
                        <?php endif; ?>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea type="text" id="descripcion" name="descripcion" class="form-control" autofocus rows="5"><?= set_value('descripcion'); ?></textarea>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['descripcion'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['descripcion']; ?>
                        <p>
                        <?php endif; ?>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Portada</label>
                        <input type="file" id="portada" name="portada" value="<?= set_value('portada'); ?>" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['portada'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['portada']; ?>
                        <p>
                        <?php endif; ?>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="datetime-local" id="fecha" name="fecha" value="<?= set_value('fecha'); ?>" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['fecha'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['fecha']; ?>
                        <p>
                        <?php endif; ?>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Seleccione el Estado</label>
                        <select class="form-select" id="estado" name="estado" value="<?php set_value('estado'); ?>">
                            <option value="A">Activo</option>
                            <option value="I">Inactivo</option>
                        </select>
                    </div>
                </div>
                <?php if (session()->getFlashdata('success') !== null) : ?>
                    <div class="alert alert-success my-3" role="alert">
                        <?= session()->getFlashdata('success'); ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('errors') !== null) : ?>
                    <p style="color:red; font-weight:bold;">
                        <?php if (!is_array(session()->getFlashdata('errors'))) : ?>
                            <?= session()->getFlashdata('errors'); ?>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
                <div class="col-12">
                    <div class="mb-4">
                        <button type="submit" class="btn btn-secondary w-100">Crear</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-light-dark _effect--ripple waves-effect waves-light" data-bs-dismiss="modal">Cancelar</button>
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
</script>

<?= $this->endSection() ?>
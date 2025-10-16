<?= $this->extend('layout/dashboard') ?>

<?= $this->section('testimonio/lista') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-quote-left"></i> Gestión de Testimonios
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/testimonio/registro') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Testimonio
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i> Errores</h5>
                            <ul class="mb-0">
                                <?php 
                                $errors = session()->getFlashdata('errors');
                                if (is_array($errors)):
                                    foreach ($errors as $field => $error): ?>
                                        <li><strong><?= esc(ucfirst(str_replace('_', ' ', $field))) ?>:</strong> <?= esc($error) ?></li>
                                    <?php endforeach;
                                else: ?>
                                    <li><?= esc($errors) ?></li>
                                <?php endif; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> <?= esc(session()->getFlashdata('error')) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> <?= esc(session()->getFlashdata('success')) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered getTestimonios">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Empresa</th>
                                    <th>Testimonio</th>
                                    <th>Calificación</th>
                                    <th>Proyecto</th>
                                    <th>Servicio</th>
                                    <th>Estado</th>
                                    <th>Destacado</th>
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
                <p class="modal-text">¿Estás seguro de que deseas eliminar este testimonio? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light-dark _effect--ripple waves-effect waves-light" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="<?= base_url('dashboard/testimonio/eliminar'); ?>"> 
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

<script>
    getTestimonios();

    function getTestimonios() {
        $('.getTestimonios').DataTable().clear().destroy();
        $('.getTestimonios').DataTable({
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
                url: 'getTestimonios',
                type: 'GET'
            }
        });
    }

    // Función para editar testimonio
    window.editarTestimonio = function(id) {
        window.location.href = '<?= base_url('dashboard/testimonio/editar') ?>/' + id;
    };

    // Función para eliminar testimonio
    window.eliminarTestimonio = function(id) {
        $("#id").attr("value", id);
        $("#modalEliminacion").modal("show");
    };

    $("body").on("click", "#btnEliminar", function(e) {
        e.preventDefault();
        console.log(this.value);
        $("#id").attr("value", this.value);
        $("#modalEliminacion").modal("show");
    });
</script>

<?= $this->endSection() ?>
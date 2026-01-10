<?php $this->extend('layout/dashboard') ?>

<?= $this->section("perfil/lista") ?>

<style>
    .main-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .card-modern {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }
    
    .card-header-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px 25px;
        border-bottom: none;
    }
    
    .card-header-modern h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }
    
    .table-modern {
        margin: 0;
    }
    
    .table-modern thead {
        background: #f8f9fa;
    }
    
    .table-modern thead th {
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        padding: 15px;
    }
    
    .table-modern tbody td {
        padding: 12px 15px;
        vertical-align: middle;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- HEADER -->
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white; margin: 0;">
                            <i class="fas fa-user-shield me-2"></i> Gestión de Perfiles
                        </h2>
                        <p style="color: white; margin: 10px 0 0 0; opacity: 0.9;">
                            Administra los perfiles y roles del sistema
                        </p>
                    </div>
                    <a href="<?= base_url('dashboard/perfil/registro') ?>" class="btn btn-light">
                        <i class="fas fa-plus me-2"></i> Nuevo Perfil
                    </a>
                </div>
            </div>

            <!-- CARD -->
            <div class="card card-modern">
                <div class="card-header card-header-modern">
                    <h3 class="card-title">
                        <i class="fas fa-list me-2"></i> Lista de Perfiles
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Mensajes -->
                    <?php if (session()->getFlashdata('errors') !== null) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php if (!is_array(session()->getFlashdata('errors'))) : ?>
                                <?= session()->getFlashdata('errors'); ?>
                            <?php else: ?>
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('success') !== null) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= session()->getFlashdata('success'); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Tabla -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-modern getPerfil" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
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
</div>

<!-- Modal de Eliminación -->
<div class="modal fade" id="modalEliminacion" tabindex="-1" aria-labelledby="modalEliminacionTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminacionTitle">
                    <i class="fas fa-exclamation-triangle me-2"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    <i class="fas fa-info-circle text-warning me-2"></i>
                    ¿Estás seguro de que deseas eliminar este perfil? Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <form method="POST" action="<?= base_url('dashboard/perfil/eliminar'); ?>" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" id="id" name="id" value="">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i> Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    getPerfil();

    function getPerfil() {
        $('.getPerfil').DataTable().clear().destroy();
        $('.getPerfil').DataTable({
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
                url: 'getPerfiles',
                type: 'GET'
            }
        });
    }

    $("body").on("click", "#btnEliminar", function(e) {
        e.preventDefault();
        console.log(this.value);
        $("#id").attr("value",this.value);
        $("#modalEliminacion").modal("show");
    });
</script>

<?= $this->endSection() ?>

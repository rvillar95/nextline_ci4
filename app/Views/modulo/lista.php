<?php $this->extend('layout/dashboard') ?>


<?= $this->section("modulo/lista") ?>


<div class="widget-content widget-content-area">

    <div class="row">

        <div class="col-md-12">
            <?php if (session()->getFlashdata('errors') !== null) : ?>
                <p style="color:red; font-weight:bold;">
                    <?php if (!is_array(session()->getFlashdata('errors'))) : ?>
                        <?= session()->getFlashdata('errors'); ?>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
            <div class="table-responsive">
                <table class="table table-bordered getModulo">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Ruta</th>
                            <th>Estado</th>
                            <th>Mostrar</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    getModulo();

    function getModulo() {
        $('.getModulo').DataTable().clear().destroy();
        $('.getModulo').DataTable({
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
                url: 'getModulos',
                type: 'GET'
            }
        });
    }
</script>

<?= $this->endSection() ?>
<?php $this->extend('layout/dashboard') ?>


<?= $this->section("lista") ?>


<div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered getDetalleInventario">
            <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Apellido</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Teléfono</th>
                    <th scope="col">Perfil</th>
                    <th scope="col">Creación</th>
                    <th class="text-center" scope="col"></th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
    getDetalleInventario();
    function getDetalleInventario() {
        $('.getDetalleInventario').DataTable().clear().destroy();
        $('.getDetalleInventario').DataTable({
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
                url: 'lista',
                type: 'GET'
            }
        });
    }
</script>

<?= $this->endSection() ?>
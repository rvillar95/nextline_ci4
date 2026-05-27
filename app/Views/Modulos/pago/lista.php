<?= $this->extend('layout/dashboard') ?>

<?= $this->section('pago/lista') ?>

<?php
$scope = $scope ?? 'cita';
$esSuperAdmin = !empty($es_super_admin);
$tituloLista = match ($scope) {
    'cita' => 'Cobros a pacientes',
    'plataforma' => 'Pagos de plataforma',
    default => 'Gestión de pagos',
};
$subtituloLista = match ($scope) {
    'cita' => 'Pagos de consultas cobrados con Mercado Pago',
    'plataforma' => 'Suscripciones y pagos del sistema NutriNext',
    default => 'Todos los pagos registrados',
};
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-hand-holding-usd me-2"></i> <?= esc($tituloLista) ?></h2>
                        <p style="color: white;" class="mb-0"><?= esc($subtituloLista) ?></p>
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <?php if ($esSuperAdmin): ?>
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="<?= base_url('dashboard/pago/cobros') ?>" class="btn <?= $scope === 'cita' ? 'btn-light' : 'btn-outline-light' ?>">Pacientes</a>
                            <a href="<?= base_url('dashboard/pago/lista?scope=plataforma') ?>" class="btn <?= $scope === 'plataforma' ? 'btn-light' : 'btn-outline-light' ?>">Plataforma</a>
                            <a href="<?= base_url('dashboard/pago/lista?scope=todos') ?>" class="btn <?= $scope === 'todos' ? 'btn-light' : 'btn-outline-light' ?>">Todos</a>
                        </div>
                        <?php if ($scope !== 'cita'): ?>
                        <a href="<?= base_url('dashboard/pago/registro') ?>" class="btn btn-light">
                            <i class="fas fa-plus me-2"></i> Registrar pago
                        </a>
                        <?php else: ?>
                        <a href="<?= base_url('dashboard/boton-pago/lista') ?>" class="btn btn-light">
                            <i class="fas fa-tags me-2"></i> Tarifas de consulta
                        </a>
                        <?php endif; ?>
                        <?php elseif ($scope === 'cita'): ?>
                        <a href="<?= base_url('dashboard/boton-pago/lista') ?>" class="btn btn-light">
                            <i class="fas fa-tags me-2"></i> Tarifas de consulta
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="section-card" style="border-left-color: #30cfd0;">
                <div class="table-responsive">
                    <table id="tablaPagos" class="table table-bordered table-striped nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <?php if ($scope === 'todos' || $scope === 'plataforma'): ?><th>Empresa</th><?php endif; ?>
                                <th>Monto</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Fecha pago</th>
                                <th>Referencia</th>
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
$(document).ready(function() {
    var scopePago = <?= json_encode($scope) ?>;
    var mostrarEmpresa = (scopePago === 'todos' || scopePago === 'plataforma');
    var columnas = [];
    if (mostrarEmpresa) {
        columnas.push({ data: 0 });
    }
    var baseIdx = mostrarEmpresa ? 1 : 0;
    columnas.push(
        { data: baseIdx },
        { data: baseIdx + 1 },
        { data: baseIdx + 2 },
        { data: baseIdx + 3 },
        { data: baseIdx + 4 },
        { data: baseIdx + 5, orderable: false }
    );

    $('#tablaPagos').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url('dashboard/pago/getPagos') ?>",
            type: "GET",
            data: function(d) {
                d.scope = scopePago;
                if (scopePago === 'cita') {
                    d.tipo_pago = 'cita';
                } else if (scopePago === 'plataforma') {
                    d.tipo_pago = '__plataforma__';
                }
            }
        },
        columns: columnas,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        responsive: true,
        pageLength: 25,
        order: [[mostrarEmpresa ? 4 : 3, "desc"]]
    });
});

function verPago(pagoId) {
    if (!pagoId) {
        alert('Error: ID de pago no válido');
        return;
    }
    window.location.href = '<?= base_url('dashboard/pago/editar/') ?>' + pagoId;
}
</script>

<?= $this->endSection() ?>

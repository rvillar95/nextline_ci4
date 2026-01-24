<?= $this->extend('layout/dashboard') ?>

<?= $this->section('facturacion/lista') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-dollar-sign"></i> Facturación e Ingresos
                    </h3>
                </div>
                <div class="card-body">

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Resumen Global -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-line me-2"></i> Total Mensual Global Estimado
                                    </h5>
                                    <h2 class="mt-2 mb-0" id="totalGlobal">
                                        <i class="fas fa-spinner fa-spin"></i> Cargando...
                                    </h2>
                                    <small class="text-white-50">Suma de todos los planes base + add-ons activos</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Empresa</label>
                            <select id="empresa_select" class="form-select">
                                <option value="">-- Todas --</option>
                                <?php foreach (($empresas ?? []) as $e): ?>
                                    <option value="<?= (int) $e->id ?>"><?= esc($e->nombre) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Paquete</label>
                            <select id="paquete_select" class="form-select">
                                <option value="">-- Todos --</option>
                                <?php foreach (($paquetes ?? []) as $p): ?>
                                    <option value="<?= (int) $p->id ?>"><?= esc($p->nombre) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Estado Empresa</label>
                            <select id="estado_select" class="form-select">
                                <option value="">Todos</option>
                                <option value="A">Activa</option>
                                <option value="I">Inactiva</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Búsqueda</label>
                            <input id="busqueda_input" class="form-control" placeholder="Empresa, email, RUT, paquete...">
                        </div>
                    </div>

                    <!-- Tabla -->
                    <table id="tablaFacturacion" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Empresa</th>
                                <th>Paquete</th>
                                <th>Precio Plan</th>
                                <th>Add-ons Activos</th>
                                <th>Total Add-ons</th>
                                <th>Total Mensual</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr class="table-info">
                                <th colspan="5" class="text-end"><strong>Total Global:</strong></th>
                                <th id="totalGlobalFooter" class="text-primary"><i class="fas fa-spinner fa-spin"></i></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- Botón Exportar -->
                    <div class="mt-3">
                        <button type="button" class="btn btn-success" id="btnExportar">
                            <i class="fas fa-file-excel me-1"></i> Exportar a CSV
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(function($) {
    if (typeof $.fn.DataTable === 'undefined') {
        alert('DataTables no está cargado. Recarga la página.');
        return;
    }

    // CSRF dinámico
    let csrfName = '<?= csrf_token() ?>';
    let csrfHash = '<?= csrf_hash() ?>';
    function setCsrf(newHash) {
        if (!newHash) return;
        csrfHash = newHash;
        $('input[name="'+csrfName+'"]').val(csrfHash);
    }

    // Formatear moneda
    function formatCurrency(num) {
        return '$' + new Intl.NumberFormat('es-CL', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(num);
    }

    const table = $('#tablaFacturacion').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url('dashboard/facturacion/getFacturacion') ?>",
            type: "GET",
            data: function(d) {
                d.empresa_id = $('#empresa_select').val() || '';
                d.paquete_id = $('#paquete_select').val() || '';
                d.estado_empresa = $('#estado_select').val() || '';
                d.busqueda = $('#busqueda_input').val() || '';
            },
            dataSrc: function(json) {
                // Actualizar total global
                if (json.totalGlobal !== undefined) {
                    $('#totalGlobal').text(formatCurrency(json.totalGlobal));
                    $('#totalGlobalFooter').text(formatCurrency(json.totalGlobal));
                }
                if (json.csrf_hash) setCsrf(json.csrf_hash);
                return json.data || [];
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Error al cargar facturación. Revisa consola.');
            }
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 }
        ],
        language: { url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" },
        responsive: true,
        autoWidth: false,
        pageLength: 25,
        order: [[5, 'desc']], // Ordenar por total mensual descendente
        footerCallback: function(row, data, start, end, display) {
            // El total ya se actualiza desde dataSrc
        }
    });

    function reload() { 
        table.ajax.reload(null, false);
    }
    
    $('#empresa_select,#paquete_select,#estado_select').on('change', reload);
    $('#busqueda_input').on('keyup', function() {
        clearTimeout(this._t);
        this._t = setTimeout(reload, 400);
    });

    // Exportar a CSV
    $('#btnExportar').on('click', function() {
        const empresaId = $('#empresa_select').val() || '';
        const paqueteId = $('#paquete_select').val() || '';
        const estadoEmpresa = $('#estado_select').val() || '';
        const busqueda = $('#busqueda_input').val() || '';

        const params = new URLSearchParams({
            empresa_id: empresaId,
            paquete_id: paqueteId,
            estado_empresa: estadoEmpresa,
            busqueda: busqueda,
            exportar: 'csv'
        });

        window.location.href = "<?= base_url('dashboard/facturacion/getFacturacion') ?>?" + params.toString();
    });
});
</script>

<?= $this->endSection() ?>

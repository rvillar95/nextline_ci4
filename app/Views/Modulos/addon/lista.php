<?= $this->extend('layout/dashboard') ?>

<?= $this->section('addon/lista') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-puzzle-piece"></i> Gestión de Add-ons
                    </h3>
                </div>
                <div class="card-body">

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Empresa</label>
                            <select id="empresa_select" class="form-select">
                                <option value="">-- Selecciona --</option>
                                <?php foreach (($empresas ?? []) as $e): ?>
                                    <option value="<?= (int) $e->id ?>"><?= esc($e->nombre) ?> (<?= esc($e->paquete_nombre ?? '') ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tipo</label>
                            <select id="tipo_select" class="form-select">
                                <option value="">Todos</option>
                                <option value="metodo_calculo">Método cálculo</option>
                                <option value="modulo">Módulo</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Estado</label>
                            <select id="estado_select" class="form-select">
                                <option value="">Todos</option>
                                <option value="activo">Activo</option>
                                <option value="suspendido">Suspendido</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Búsqueda</label>
                            <input id="busqueda_input" class="form-control" placeholder="Empresa, email, método, slug...">
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <strong>Asignar/Actualizar Add-on</strong>
                        </div>
                        <div class="card-body">
                            <form id="formAddon" class="row g-3">
                                <?= csrf_field() ?>
                                <div class="col-md-3">
                                    <label class="form-label">Empresa *</label>
                                    <select name="empresa_id" class="form-select" required>
                                        <option value="">-- Selecciona --</option>
                                        <?php foreach (($empresas ?? []) as $e): ?>
                                            <option value="<?= (int) $e->id ?>"><?= esc($e->nombre) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tipo *</label>
                                    <select name="tipo" class="form-select" required id="form_tipo">
                                        <option value="metodo_calculo">Método cálculo</option>
                                        <option value="modulo">Módulo</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Item *</label>
                                    <select name="referencia_id" class="form-select" required id="form_item">
                                        <!-- se llena por JS -->
                                    </select>
                                    <small class="text-muted">Para métodos: referencia a <code>metodos_calculo.id</code>. Para módulos: <code>modulo.id</code>.</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Precio mensual</label>
                                    <input name="precio_mensual" type="number" step="0.01" class="form-control" value="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Inicio</label>
                                    <input name="fecha_inicio" type="date" class="form-control" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Fin (opcional)</label>
                                    <input name="fecha_fin" type="date" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Estado</label>
                                    <select name="estado" class="form-select">
                                        <option value="activo">Activo</option>
                                        <option value="suspendido">Suspendido</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button class="btn btn-primary w-100" type="submit">
                                        <i class="fas fa-save"></i> Guardar Add-on
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <table id="tablaAddons" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Empresa</th>
                                <th>Tipo</th>
                                <th>Item</th>
                                <th>Precio</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Cancelación -->
<div class="modal fade" id="modalConfirmarCancelarAddon" tabindex="-1" aria-labelledby="modalConfirmarCancelarAddonLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarCancelarAddonLabel">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i> Confirmar Cancelación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas <strong>cancelar</strong> este add-on?</p>
                <p class="text-muted small mb-0">
                    Se marcará como <strong>cancelado</strong> y se registrará <strong>fecha fin = hoy</strong>.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarCancelarAddon">
                    <i class="fas fa-times me-1"></i> Cancelar Add-on
                </button>
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

    // Catálogos desde PHP
    const metodos = <?= json_encode(array_map(function($m){
        return [
            'id' => (int) $m->id,
            'nombre' => $m->nombre,
            'slug' => $m->slug,
            'precio_mensual' => (float) ($m->precio_mensual ?? 0),
            'es_addon' => $m->es_addon ?? 'N',
        ];
    }, $metodos ?? [])) ?>;

    const modulosAddon = <?= json_encode(array_map(function($m){
        return [
            'id' => (int) $m->id,
            'nombre' => $m->nombre,
            'ruta' => $m->ruta,
        ];
    }, $modulos_addon ?? [])) ?>;

    // CSRF dinámico
    let csrfName = '<?= csrf_token() ?>';
    let csrfHash = '<?= csrf_hash() ?>';
    function setCsrf(newHash) {
        if (!newHash) return;
        csrfHash = newHash;
        $('input[name="'+csrfName+'"]').val(csrfHash);
    }

    function fillItems() {
        const tipo = $('#form_tipo').val();
        const $sel = $('#form_item');
        $sel.empty();
        if (tipo === 'metodo_calculo') {
            metodos.forEach(m => {
                const label = `${m.nombre} (${m.slug})`;
                $sel.append(`<option value="${m.id}" data-precio="${m.precio_mensual}">${label}</option>`);
            });
        } else {
            modulosAddon.forEach(m => {
                const label = `${m.nombre} (${m.ruta})`;
                $sel.append(`<option value="${m.id}" data-precio="0">${label}</option>`);
            });
        }
        // Auto precio
        const precio = Number($sel.find('option:selected').data('precio') || 0);
        $('#formAddon input[name="precio_mensual"]').val(precio);
    }

    $('#form_tipo').on('change', fillItems);
    $('#form_item').on('change', function() {
        const precio = Number($(this).find('option:selected').data('precio') || 0);
        $('#formAddon input[name="precio_mensual"]').val(precio);
    });
    fillItems();

    const table = $('#tablaAddons').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url('dashboard/addon/getAddons') ?>",
            type: "GET",
            data: function(d) {
                d.empresa_id = $('#empresa_select').val() || '';
                d.tipo = $('#tipo_select').val() || '';
                d.estado = $('#estado_select').val() || '';
                d.busqueda = $('#busqueda_input').val() || '';
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Error al cargar add-ons. Revisa consola.');
            }
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7, orderable: false }
        ],
        language: { url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" },
        responsive: true,
        autoWidth: false,
        pageLength: 25
    });

    function reload() { table.ajax.reload(null, false); }
    $('#empresa_select,#tipo_select,#estado_select').on('change', reload);
    $('#busqueda_input').on('keyup', function() {
        clearTimeout(this._t);
        this._t = setTimeout(reload, 400);
    });

    $('#formAddon').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const data = $form.serializeArray();
        const post = {};
        data.forEach(x => post[x.name] = x.value);
        post[csrfName] = csrfHash;

        $.ajax({
            url: "<?= base_url('dashboard/addon/registrar') ?>",
            type: "POST",
            data: post,
            headers: { 'X-CSRF-TOKEN': csrfHash },
            success: function(resp) {
                if (resp && resp.csrf_hash) setCsrf(resp.csrf_hash);
                if (resp && resp.success) {
                    if (typeof toastr !== 'undefined') toastr.success(resp.message || 'Add-on guardado');
                    reload();
                } else {
                    if (typeof toastr !== 'undefined') toastr.error((resp && resp.error) || 'Error');
                }
            },
            error: function(xhr) {
                const resp = xhr.responseJSON || {};
                if (resp && resp.csrf_hash) setCsrf(resp.csrf_hash);
                const headerToken = xhr.getResponseHeader('X-CSRF-TOKEN');
                if (headerToken) setCsrf(headerToken);
                alert(resp.error || resp.message || 'Error al guardar add-on');
            }
        });
    });

    // Cancelar con modal
    window._addonIdCancelar = null;
    window.cancelarAddon = function(id) {
        window._addonIdCancelar = id;
        const modalEl = document.getElementById('modalConfirmarCancelarAddon');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    };

    $('#btnConfirmarCancelarAddon').on('click', function() {
        const id = window._addonIdCancelar;
        if (!id) return;

        const post = {};
        post[csrfName] = csrfHash;

        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Cancelando...');

        $.ajax({
            url: "<?= base_url('dashboard/addon/cancelar') ?>/" + id,
            type: "POST",
            data: post,
            headers: { 'X-CSRF-TOKEN': csrfHash },
            success: function(resp) {
                if (resp && resp.csrf_hash) setCsrf(resp.csrf_hash);
                if (resp && resp.success) {
                    if (typeof toastr !== 'undefined') toastr.success(resp.message || 'Add-on cancelado');
                    reload();
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmarCancelarAddon'));
                    if (modal) modal.hide();
                } else {
                    alert((resp && resp.error) || 'Error');
                }
            },
            error: function(xhr) {
                const resp = xhr.responseJSON || {};
                if (resp && resp.csrf_hash) setCsrf(resp.csrf_hash);
                const headerToken = xhr.getResponseHeader('X-CSRF-TOKEN');
                if (headerToken) setCsrf(headerToken);
                alert(resp.error || resp.message || 'Error');
            },
            complete: function() {
                $('#btnConfirmarCancelarAddon').prop('disabled', false).html('<i class="fas fa-times me-1"></i> Cancelar Add-on');
                window._addonIdCancelar = null;
            }
        });
    });

    window.activarAddon = function(id) {
        const post = {};
        post[csrfName] = csrfHash;
        $.ajax({
            url: "<?= base_url('dashboard/addon/activar') ?>/" + id,
            type: "POST",
            data: post,
            headers: { 'X-CSRF-TOKEN': csrfHash },
            success: function(resp) {
                if (resp && resp.csrf_hash) setCsrf(resp.csrf_hash);
                if (resp && resp.success) {
                    if (typeof toastr !== 'undefined') toastr.success(resp.message || 'Activado');
                    reload();
                } else {
                    alert(resp.error || 'Error');
                }
            },
            error: function(xhr) {
                const resp = xhr.responseJSON || {};
                if (resp && resp.csrf_hash) setCsrf(resp.csrf_hash);
                const headerToken = xhr.getResponseHeader('X-CSRF-TOKEN');
                if (headerToken) setCsrf(headerToken);
                alert(resp.error || resp.message || 'Error');
            }
        });
    }
});
</script>

<?= $this->endSection() ?>


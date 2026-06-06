<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/rutina/editar') ?>

<?= $this->include('Modulos/gym/partials/assets') ?>

<div class="container-fluid gym-page gym-page--rutina">
    <?= view('Modulos/gym/partials/page_header', [
        'icon' => 'fa-clipboard-list',
        'title' => 'Editar rutina',
        'subtitle' => esc($rutina->nombre ?? ''),
        'module' => 'rutina',
        'back' => ['url' => base_url('dashboard/gym/rutina/lista'), 'label' => 'Volver al listado'],
    ]) ?>

    <?= view('Modulos/gym/partials/workflow', ['active' => 'rutina']) ?>
    <?= $this->include('Modulos/gym/partials/flash') ?>

    <div class="gym-section-card">
        <div class="gym-section-card__head">
            <h3><i class="fas fa-info-circle"></i> Información general</h3>
        </div>
        <div class="gym-section-card__body">
            <form method="POST" action="<?= base_url('dashboard/gym/rutina/update') ?>" class="gym-form">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $rutina->id ?>">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="<?= esc(old('nombre') ?? $rutina->nombre) ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Estado</label>
                        <?php $activoVal = old('activo'); ?>
                        <?php $activo = ($activoVal !== null) ? (int) $activoVal : (int) $rutina->activo; ?>
                        <select name="activo" class="form-select">
                            <option value="1" <?= $activo === 1 ? 'selected' : '' ?>>Activa</option>
                            <option value="0" <?= $activo === 0 ? 'selected' : '' ?>>Inactiva</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"><?= esc(old('descripcion') ?? ($rutina->descripcion ?? '')) ?></textarea>
                    </div>
                </div>
                <button type="submit" class="btn gym-btn-primary btn-sm"><i class="fas fa-save me-1"></i> Guardar datos</button>
            </form>
        </div>
    </div>

    <div class="gym-section-card gym-builder-panel">
        <div class="gym-section-card__head">
            <div>
                <h3><i class="fas fa-layer-group"></i> Ejercicios de la rutina</h3>
                <p class="gym-section-card__hint">Agrega movimientos, define series, reps y descanso. Guarda al terminar.</p>
            </div>
            <button id="btnGuardarDetalle" type="button" class="btn gym-btn-primary">
                <i class="fas fa-save me-1"></i> Guardar ejercicios
            </button>
        </div>
        <div class="gym-section-card__body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Agregar ejercicio</label>
                    <select id="selEjercicio" class="form-select">
                        <option value="">Seleccione del catálogo...</option>
                        <?php foreach (($ejercicios ?? []) as $e) : ?>
                            <option value="<?= (int) $e->id ?>"><?= esc($e->nombre) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button id="btnAgregar" type="button" class="btn gym-btn-primary mt-2 w-100">
                        <i class="fas fa-plus me-1"></i> Agregar a la rutina
                    </button>
                    <div class="gym-builder-tip">
                        Ordena con el número de la primera columna. El descanso va en segundos. «Notas alumno» aparece al entrenar.
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="gym-table-wrap table-responsive">
                        <table class="table gym-table mb-0" id="tblDetalle">
                            <thead>
                                <tr>
                                    <th style="width:56px;min-width:56px;">#</th>
                                    <th>Ejercicio</th>
                                    <th style="width:72px;min-width:72px;">Series</th>
                                    <th style="width:120px;">Reps</th>
                                    <th style="width:120px;">Descanso</th>
                                    <th>Notas alumno</th>
                                    <th style="width:80px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (($detalle ?? []) as $d) : ?>
                                    <tr data-ejercicio-id="<?= (int) $d['ejercicio_id'] ?>">
                                        <td><input type="number" min="1" step="1" class="form-control form-control-sm orden" value="<?= (int) $d['orden'] ?>"></td>
                                        <td class="align-middle fw-semibold"><?= esc($d['ejercicio_nombre'] ?? '') ?></td>
                                        <td><input type="number" min="0" step="1" class="form-control form-control-sm series" value="<?= $d['series'] !== null && $d['series'] !== '' ? (int) $d['series'] : '' ?>"></td>
                                        <td><input type="text" class="form-control form-control-sm repeticiones" value="<?= esc($d['repeticiones'] ?? '') ?>"></td>
                                        <td><input type="number" class="form-control form-control-sm descanso_seg" value="<?= esc($d['descanso_seg'] ?? '') ?>"></td>
                                        <td><textarea class="form-control form-control-sm notas" rows="2" placeholder="Indicaciones para el alumno"><?= esc($d['notas'] ?? '') ?></textarea></td>
                                        <td><button type="button" class="btn btn-sm btn-outline-danger btnQuitar">Quitar</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('page_scripts') ?>
<script>
(function () {
    const rutinaId = <?= (int) $rutina->id ?>;
    let csrfHashGym = window.NutriNextCsrf ? NutriNextCsrf.getToken() : '<?= csrf_hash() ?>';
    const updateUrl = <?= json_encode(base_url('dashboard/gym/rutina/update-ejercicios')) ?>;

    function notify(msg, type) {
        if (typeof nnNotify === 'function') {
            nnNotify(msg, type || 'info');
        } else if (typeof alert === 'function') {
            alert(msg);
        }
    }

    function nextOrden() {
        let max = 0;
        $('#tblDetalle tbody tr').each(function () {
            const v = parseInt($(this).find('input.orden').val() || '0', 10);
            if (v > max) max = v;
        });
        return max + 1;
    }

    $('#btnAgregar').on('click', function (e) {
        e.preventDefault();
        const ejercicioId = $('#selEjercicio').val();
        const texto = $('#selEjercicio option:selected').text();
        if (!ejercicioId) return;

        if ($('#tblDetalle tbody tr[data-ejercicio-id="' + ejercicioId + '"]').length) {
            notify('Ese ejercicio ya está en la rutina', 'warning');
            return;
        }

        const orden = nextOrden();
        $('#tblDetalle tbody').append(`
            <tr data-ejercicio-id="${ejercicioId}">
                <td><input type="number" min="1" step="1" class="form-control form-control-sm orden" value="${orden}"></td>
                <td class="align-middle fw-semibold">${texto}</td>
                <td><input type="number" min="0" step="1" class="form-control form-control-sm series" value=""></td>
                <td><input type="text" class="form-control form-control-sm repeticiones" value=""></td>
                <td><input type="number" class="form-control form-control-sm descanso_seg" value=""></td>
                <td><textarea class="form-control form-control-sm notas" rows="2" placeholder="Indicaciones para el alumno"></textarea></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger btnQuitar">Quitar</button></td>
            </tr>
        `);
        $('#selEjercicio').val('');
    });

    $(document).on('click', '.btnQuitar', function () {
        $(this).closest('tr').remove();
    });

    $('#btnGuardarDetalle').on('click', async function () {
        const btn = this;
        if (btn.disabled) return;
        btn.disabled = true;

        const items = [];
        $('#tblDetalle tbody tr').each(function () {
            const $tr = $(this);
            items.push({
                ejercicio_id: parseInt($tr.data('ejercicio-id'), 10),
                orden: parseInt($tr.find('input.orden').val() || '0', 10),
                series: $tr.find('input.series').val(),
                repeticiones: $tr.find('input.repeticiones').val(),
                descanso_seg: $tr.find('input.descanso_seg').val(),
                notas: $tr.find('textarea.notas').val(),
            });
        });

        try {
            const csrfRef = { token: csrfHashGym };
            const result = window.NutriNextGym
                ? await NutriNextGym.postJson(updateUrl, { rutina_id: rutinaId, items }, csrfRef)
                : { ok: false, error: 'Script gym no cargado' };
            csrfHashGym = csrfRef.token;
            if (!result.ok) {
                notify(result.error || 'Error al guardar', 'error');
                return;
            }
            $('#tblDetalle tbody tr').each(function (i) {
                $(this).find('input.orden').val(i + 1);
            });
            notify('Ejercicios de la rutina guardados correctamente', 'success');
        } finally {
            btn.disabled = false;
        }
    });
})();
</script>
<?= $this->endSection() ?>

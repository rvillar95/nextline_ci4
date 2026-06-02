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
                        Ordena con el número de la primera columna. El descanso va en segundos.
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="gym-table-wrap table-responsive">
                        <table class="table gym-table mb-0" id="tblDetalle">
                            <thead>
                                <tr>
                                    <th style="width:60px;">#</th>
                                    <th>Ejercicio</th>
                                    <th style="width:90px;">Series</th>
                                    <th style="width:120px;">Reps</th>
                                    <th style="width:120px;">Descanso</th>
                                    <th>Notas</th>
                                    <th style="width:80px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (($detalle ?? []) as $d) : ?>
                                    <tr data-ejercicio-id="<?= (int) $d['ejercicio_id'] ?>">
                                        <td><input type="number" class="form-control form-control-sm orden" value="<?= (int) $d['orden'] ?>"></td>
                                        <td class="align-middle fw-semibold"><?= esc($d['ejercicio_nombre'] ?? '') ?></td>
                                        <td><input type="number" class="form-control form-control-sm series" value="<?= esc($d['series'] ?? '') ?>"></td>
                                        <td><input type="text" class="form-control form-control-sm repeticiones" value="<?= esc($d['repeticiones'] ?? '') ?>"></td>
                                        <td><input type="number" class="form-control form-control-sm descanso_seg" value="<?= esc($d['descanso_seg'] ?? '') ?>"></td>
                                        <td><input type="text" class="form-control form-control-sm notas" value="<?= esc($d['notas'] ?? '') ?>"></td>
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

<script>
    const rutinaId = <?= (int) $rutina->id ?>;

    function nextOrden() {
        let max = 0;
        $('#tblDetalle tbody tr').each(function() {
            const v = parseInt($(this).find('input.orden').val() || '0', 10);
            if (v > max) max = v;
        });
        return max + 1;
    }

    $('#btnAgregar').on('click', function(e) {
        e.preventDefault();
        const ejercicioId = $('#selEjercicio').val();
        const texto = $('#selEjercicio option:selected').text();
        if (!ejercicioId) return;

        if ($('#tblDetalle tbody tr[data-ejercicio-id="' + ejercicioId + '"]').length) {
            alert('Ese ejercicio ya está en la rutina');
            return;
        }

        const orden = nextOrden();
        $('#tblDetalle tbody').append(`
            <tr data-ejercicio-id="${ejercicioId}">
                <td><input type="number" class="form-control form-control-sm orden" value="${orden}"></td>
                <td class="align-middle fw-semibold">${texto}</td>
                <td><input type="number" class="form-control form-control-sm series" value=""></td>
                <td><input type="text" class="form-control form-control-sm repeticiones" value=""></td>
                <td><input type="number" class="form-control form-control-sm descanso_seg" value=""></td>
                <td><input type="text" class="form-control form-control-sm notas" value=""></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger btnQuitar">Quitar</button></td>
            </tr>
        `);
        $('#selEjercicio').val('');
    });

    $(document).on('click', '.btnQuitar', function() {
        $(this).closest('tr').remove();
    });

    $('#btnGuardarDetalle').on('click', async function() {
        const items = [];
        $('#tblDetalle tbody tr').each(function() {
            const $tr = $(this);
            items.push({
                ejercicio_id: parseInt($tr.data('ejercicio-id'), 10),
                orden: parseInt($tr.find('input.orden').val() || '0', 10),
                series: $tr.find('input.series').val(),
                repeticiones: $tr.find('input.repeticiones').val(),
                descanso_seg: $tr.find('input.descanso_seg').val(),
                notas: $tr.find('input.notas').val(),
            });
        });

        const res = await fetch("<?= base_url('dashboard/gym/rutina/update-ejercicios') ?>", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "<?= csrf_hash() ?>"
            },
            body: JSON.stringify({ rutina_id: rutinaId, items })
        });

        const json = await res.json().catch(() => null);
        if (!res.ok || !json || json.success !== true) {
            alert((json && json.error) ? json.error : 'Error al guardar');
            return;
        }
        alert('Ejercicios de la rutina guardados correctamente');
        if (json.csrf_token) {
            document.querySelectorAll('input[name="<?= csrf_token() ?>"]').forEach(el => el.value = json.csrf_token);
        }
    });
</script>

<?= $this->endSection() ?>

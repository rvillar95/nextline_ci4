<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/rutina/editar') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Editar Rutina
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/gym/rutina/lista') ?>" class="btn btn-light">Volver</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors') !== null) : ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php if (!is_array(session()->getFlashdata('errors'))) : ?>
                                    <li><?= session()->getFlashdata('errors') ?></li>
                                <?php else : ?>
                                    <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success') !== null) : ?>
                        <div class="alert alert-success" role="alert">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= base_url('dashboard/gym/rutina/update') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $rutina->id ?>">

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" value="<?= esc(old('nombre') ?? $rutina->nombre) ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Activa</label>
                                <?php $activoVal = old('activo'); ?>
                                <?php $activo = ($activoVal !== null) ? (int)$activoVal : (int)$rutina->activo; ?>
                                <select name="activo" class="form-control">
                                    <option value="1" <?= $activo === 1 ? 'selected' : '' ?>>Sí</option>
                                    <option value="0" <?= $activo === 0 ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="4"><?= esc(old('descripcion') ?? ($rutina->descripcion ?? '')) ?></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar datos</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> Builder: ejercicios de la rutina
                    </h3>
                    <div class="card-tools">
                        <button id="btnGuardarDetalle" class="btn btn-success">Guardar ejercicios</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Agregar ejercicio</label>
                            <select id="selEjercicio" class="form-control">
                                <option value="">Seleccione...</option>
                                <?php foreach (($ejercicios ?? []) as $e) : ?>
                                    <option value="<?= (int)$e->id ?>"><?= esc($e->nombre) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button id="btnAgregar" class="btn btn-primary mt-2">Agregar</button>
                            <div class="text-muted mt-2">Tip: agrega y luego edita series/reps/descanso.</div>
                        </div>
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tblDetalle">
                                    <thead>
                                        <tr>
                                            <th style="width:60px;">Orden</th>
                                            <th>Ejercicio</th>
                                            <th style="width:90px;">Series</th>
                                            <th style="width:120px;">Reps</th>
                                            <th style="width:140px;">Descanso (seg)</th>
                                            <th>Notas</th>
                                            <th style="width:80px;">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (($detalle ?? []) as $d) : ?>
                                            <tr data-ejercicio-id="<?= (int)$d['ejercicio_id'] ?>">
                                                <td><input type="number" class="form-control orden" value="<?= (int)$d['orden'] ?>"></td>
                                                <td><?= esc($d['ejercicio_nombre'] ?? '') ?></td>
                                                <td><input type="number" class="form-control series" value="<?= esc($d['series'] ?? '') ?>"></td>
                                                <td><input type="text" class="form-control repeticiones" value="<?= esc($d['repeticiones'] ?? '') ?>"></td>
                                                <td><input type="number" class="form-control descanso_seg" value="<?= esc($d['descanso_seg'] ?? '') ?>"></td>
                                                <td><input type="text" class="form-control notas" value="<?= esc($d['notas'] ?? '') ?>"></td>
                                                <td><button type="button" class="btn btn-sm btn-danger btnQuitar">Quitar</button></td>
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
    </div>
</div>

<script>
    const rutinaId = <?= (int)$rutina->id ?>;

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

        if ($('#tblDetalle tbody tr[data-ejercicio-id=\"' + ejercicioId + '\"]').length) {
            alert('Ese ejercicio ya está en la rutina');
            return;
        }

        const orden = nextOrden();
        $('#tblDetalle tbody').append(`
            <tr data-ejercicio-id="${ejercicioId}">
                <td><input type="number" class="form-control orden" value="${orden}"></td>
                <td>${texto}</td>
                <td><input type="number" class="form-control series" value=""></td>
                <td><input type="text" class="form-control repeticiones" value=""></td>
                <td><input type="number" class="form-control descanso_seg" value=""></td>
                <td><input type="text" class="form-control notas" value=""></td>
                <td><button type="button" class="btn btn-sm btn-danger btnQuitar">Quitar</button></td>
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
        alert('Rutina guardada');
        // refrescar token CSRF si viene
        if (json.csrf_token) {
            document.querySelectorAll('input[name=\"<?= csrf_token() ?>\"]').forEach(el => el.value = json.csrf_token);
        }
    });
</script>

<?= $this->endSection() ?>


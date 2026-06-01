<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/programa/editar') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Editar Programa
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/gym/programa/lista') ?>" class="btn btn-light">Volver</a>
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

                    <form method="POST" action="<?= base_url('dashboard/gym/programa/update') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $programa->id ?>">

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" value="<?= esc(old('nombre') ?? $programa->nombre) ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Duración (semanas)</label>
                                <input type="number" name="duracion_semanas" class="form-control" value="<?= esc(old('duracion_semanas') ?? ($programa->duracion_semanas ?? '')) ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Activo</label>
                                <?php $activoVal = old('activo'); ?>
                                <?php $activo = ($activoVal !== null) ? (int)$activoVal : (int)$programa->activo; ?>
                                <select name="activo" class="form-control">
                                    <option value="1" <?= $activo === 1 ? 'selected' : '' ?>>Sí</option>
                                    <option value="0" <?= $activo === 0 ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="4"><?= esc(old('descripcion') ?? ($programa->descripcion ?? '')) ?></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar datos</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> Rutinas del programa
                    </h3>
                    <div class="card-tools">
                        <button id="btnGuardarRutinas" class="btn btn-success">Guardar rutinas</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Agregar rutina</label>
                            <select id="selRutina" class="form-control">
                                <option value="">Seleccione...</option>
                                <?php foreach (($rutinas ?? []) as $r) : ?>
                                    <option value="<?= (int)$r->id ?>"><?= esc($r->nombre) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button id="btnAgregar" class="btn btn-primary mt-2">Agregar</button>
                            <div class="text-muted mt-2">Puedes definir el orden y (opcional) día de la semana.</div>
                        </div>
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tblRutinas">
                                    <thead>
                                        <tr>
                                            <th style="width:60px;">Orden</th>
                                            <th>Rutina</th>
                                            <th style="width:140px;">Día semana</th>
                                            <th style="width:80px;">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (($detalle ?? []) as $d) : ?>
                                            <tr data-rutina-id="<?= (int)$d['rutina_id'] ?>">
                                                <td><input type="number" class="form-control orden" value="<?= (int)$d['orden'] ?>"></td>
                                                <td><?= esc($d['rutina_nombre'] ?? '') ?></td>
                                                <td>
                                                    <select class="form-control dia_semana">
                                                        <?php
                                                        $dias = [
                                                            '' => '(Sin día)',
                                                            '1' => 'Lunes',
                                                            '2' => 'Martes',
                                                            '3' => 'Miércoles',
                                                            '4' => 'Jueves',
                                                            '5' => 'Viernes',
                                                            '6' => 'Sábado',
                                                            '7' => 'Domingo',
                                                        ];
                                                        foreach ($dias as $k => $label) {
                                                            $sel = ((string)($d['dia_semana'] ?? '') === (string)$k) ? 'selected' : '';
                                                            echo '<option value="' . esc($k) . '" ' . $sel . '>' . esc($label) . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
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
    const programaId = <?= (int)$programa->id ?>;

    function nextOrden() {
        let max = 0;
        $('#tblRutinas tbody tr').each(function() {
            const v = parseInt($(this).find('input.orden').val() || '0', 10);
            if (v > max) max = v;
        });
        return max + 1;
    }

    $('#btnAgregar').on('click', function(e) {
        e.preventDefault();
        const rutinaId = $('#selRutina').val();
        const texto = $('#selRutina option:selected').text();
        if (!rutinaId) return;

        if ($('#tblRutinas tbody tr[data-rutina-id=\"' + rutinaId + '\"]').length) {
            alert('Esa rutina ya está en el programa');
            return;
        }

        const orden = nextOrden();
        $('#tblRutinas tbody').append(`
            <tr data-rutina-id="${rutinaId}">
                <td><input type="number" class="form-control orden" value="${orden}"></td>
                <td>${texto}</td>
                <td>
                    <select class="form-control dia_semana">
                        <option value="">(Sin día)</option>
                        <option value="1">Lunes</option>
                        <option value="2">Martes</option>
                        <option value="3">Miércoles</option>
                        <option value="4">Jueves</option>
                        <option value="5">Viernes</option>
                        <option value="6">Sábado</option>
                        <option value="7">Domingo</option>
                    </select>
                </td>
                <td><button type="button" class="btn btn-sm btn-danger btnQuitar">Quitar</button></td>
            </tr>
        `);
        $('#selRutina').val('');
    });

    $(document).on('click', '.btnQuitar', function() {
        $(this).closest('tr').remove();
    });

    $('#btnGuardarRutinas').on('click', async function() {
        const items = [];
        $('#tblRutinas tbody tr').each(function() {
            const $tr = $(this);
            items.push({
                rutina_id: parseInt($tr.data('rutina-id'), 10),
                orden: parseInt($tr.find('input.orden').val() || '0', 10),
                dia_semana: $tr.find('select.dia_semana').val(),
            });
        });

        const res = await fetch("<?= base_url('dashboard/gym/programa/update-rutinas') ?>", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "<?= csrf_hash() ?>"
            },
            body: JSON.stringify({ programa_id: programaId, items })
        });

        const json = await res.json().catch(() => null);
        if (!res.ok || !json || json.success !== true) {
            alert((json && json.error) ? json.error : 'Error al guardar');
            return;
        }
        alert('Programa guardado');
    });
</script>

<?= $this->endSection() ?>


<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/programa/editar') ?>

<?= $this->include('Modulos/gym/partials/assets') ?>

<div class="container-fluid gym-page gym-page--programa">
    <?= view('Modulos/gym/partials/page_header', [
        'icon' => 'fa-calendar-week',
        'title' => 'Editar programa',
        'subtitle' => esc($programa->nombre ?? ''),
        'module' => 'programa',
        'back' => ['url' => base_url('dashboard/gym/programa/lista'), 'label' => 'Volver al listado'],
    ]) ?>

    <?= $this->include('Modulos/gym/partials/flash') ?>

    <div class="gym-section-card">
        <div class="gym-section-card__head">
            <h3><i class="fas fa-info-circle"></i> Información general</h3>
        </div>
        <div class="gym-section-card__body">
            <form method="POST" action="<?= base_url('dashboard/gym/programa/update') ?>" class="gym-form">
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
                        <label class="form-label">Estado</label>
                        <?php $activoVal = old('activo'); ?>
                        <?php $activo = ($activoVal !== null) ? (int) $activoVal : (int) $programa->activo; ?>
                        <select name="activo" class="form-select">
                            <option value="1" <?= $activo === 1 ? 'selected' : '' ?>>Activo</option>
                            <option value="0" <?= $activo === 0 ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"><?= esc(old('descripcion') ?? ($programa->descripcion ?? '')) ?></textarea>
                    </div>
                </div>
                <button type="submit" class="btn gym-btn-primary btn-sm"><i class="fas fa-save me-1"></i> Guardar datos</button>
            </form>
        </div>
    </div>

    <div class="gym-section-card gym-builder-panel">
        <div class="gym-section-card__head">
            <div>
                <h3><i class="fas fa-link"></i> Rutinas del programa</h3>
                <p class="gym-section-card__hint">Ordena las sesiones y opcionalmente asigna un día de la semana.</p>
            </div>
            <button id="btnGuardarRutinas" type="button" class="btn gym-btn-primary">
                <i class="fas fa-save me-1"></i> Guardar rutinas
            </button>
        </div>
        <div class="gym-section-card__body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Agregar rutina</label>
                    <select id="selRutina" class="form-select">
                        <option value="">Seleccione...</option>
                        <?php foreach (($rutinas ?? []) as $r) : ?>
                            <option value="<?= (int) $r->id ?>"><?= esc($r->nombre) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button id="btnAgregar" type="button" class="btn gym-btn-primary mt-2 w-100"><i class="fas fa-plus me-1"></i> Agregar</button>
                </div>
                <div class="col-md-8">
                    <div class="gym-table-wrap table-responsive">
                        <table class="table gym-table mb-0" id="tblRutinas">
                            <thead>
                                <tr>
                                    <th style="width:60px;">#</th>
                                    <th>Rutina</th>
                                    <th style="width:160px;">Día</th>
                                    <th style="width:80px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (($detalle ?? []) as $d) : ?>
                                    <tr data-rutina-id="<?= (int) $d['rutina_id'] ?>">
                                        <td><input type="number" class="form-control form-control-sm orden" value="<?= (int) $d['orden'] ?>"></td>
                                        <td class="align-middle fw-semibold"><?= esc($d['rutina_nombre'] ?? '') ?></td>
                                        <td>
                                            <select class="form-select form-select-sm dia_semana">
                                                <?php
                                                $dias = [
                                                    '' => 'Sin día fijo',
                                                    '1' => 'Lunes', '2' => 'Martes', '3' => 'Miércoles',
                                                    '4' => 'Jueves', '5' => 'Viernes', '6' => 'Sábado', '7' => 'Domingo',
                                                ];
                                                foreach ($dias as $k => $label) {
                                                    $sel = ((string) ($d['dia_semana'] ?? '') === (string) $k) ? 'selected' : '';
                                                    echo '<option value="' . esc($k) . '" ' . $sel . '>' . esc($label) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
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
    const programaId = <?= (int) $programa->id ?>;

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
        if ($('#tblRutinas tbody tr[data-rutina-id="' + rutinaId + '"]').length) {
            alert('Esa rutina ya está en el programa');
            return;
        }
        const orden = nextOrden();
        $('#tblRutinas tbody').append(`
            <tr data-rutina-id="${rutinaId}">
                <td><input type="number" class="form-control form-control-sm orden" value="${orden}"></td>
                <td class="align-middle fw-semibold">${texto}</td>
                <td>
                    <select class="form-select form-select-sm dia_semana">
                        <option value="">Sin día fijo</option>
                        <option value="1">Lunes</option><option value="2">Martes</option>
                        <option value="3">Miércoles</option><option value="4">Jueves</option>
                        <option value="5">Viernes</option><option value="6">Sábado</option><option value="7">Domingo</option>
                    </select>
                </td>
                <td><button type="button" class="btn btn-sm btn-outline-danger btnQuitar">Quitar</button></td>
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
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "<?= csrf_hash() ?>" },
            body: JSON.stringify({ programa_id: programaId, items })
        });
        const json = await res.json().catch(() => null);
        if (!res.ok || !json || json.success !== true) {
            alert((json && json.error) ? json.error : 'Error al guardar');
            return;
        }
        alert('Rutinas del programa guardadas');
    });
</script>

<?= $this->endSection() ?>

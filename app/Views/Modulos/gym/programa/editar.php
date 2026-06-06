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

    <?php if (!empty($alumnos)) : ?>
    <div class="gym-section-card mt-4">
        <div class="gym-section-card__head">
            <div>
                <h3><i class="fas fa-copy"></i> Asignar copia a alumno</h3>
                <p class="gym-section-card__hint mb-0">Duplica este programa (con rutinas y ejercicios) y asígnalo directamente.</p>
            </div>
        </div>
        <div class="gym-section-card__body gym-assign-box">
            <form method="POST" action="<?= base_url('dashboard/gym/programa/duplicar-asignar') ?>" class="gym-form">
                <?= csrf_field() ?>
                <input type="hidden" name="programa_id" value="<?= (int) $programa->id ?>">
                <div class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label class="form-label">Alumno</label>
                        <select name="usuario_id" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($alumnos as $a) : ?>
                            <option value="<?= (int) $a->id ?>">
                                <?= esc(trim($a->nombre . ' ' . $a->apellido)) ?> — <?= esc($a->correo) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nombre de la copia</label>
                        <input type="text" name="nombre" class="form-control" maxlength="160"
                               value="<?= esc('Copia de ' . ($programa->nombre ?? '')) ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn gym-btn-primary w-100">
                            <i class="fas fa-paper-plane me-1"></i> Duplicar y asignar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

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
                                    <th style="width:56px;min-width:56px;">#</th>
                                    <th>Rutina</th>
                                    <th style="width:160px;">Día</th>
                                    <th style="width:80px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (($detalle ?? []) as $d) : ?>
                                    <tr data-rutina-id="<?= (int) $d['rutina_id'] ?>">
                                        <td><input type="number" min="1" step="1" class="form-control form-control-sm orden" value="<?= (int) $d['orden'] ?>"></td>
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

<?= $this->endSection() ?>

<?= $this->section('page_scripts') ?>
<script>
(function () {
    const programaId = <?= (int) $programa->id ?>;
    let csrfHashGym = window.NutriNextCsrf ? NutriNextCsrf.getToken() : '<?= csrf_hash() ?>';
    const updateUrl = <?= json_encode(base_url('dashboard/gym/programa/update-rutinas')) ?>;

    function notify(msg, type) {
        if (typeof nnNotify === 'function') {
            nnNotify(msg, type || 'info');
        } else if (typeof alert === 'function') {
            alert(msg);
        }
    }

    function nextOrden() {
        let max = 0;
        $('#tblRutinas tbody tr').each(function () {
            const v = parseInt($(this).find('input.orden').val() || '0', 10);
            if (v > max) max = v;
        });
        return max + 1;
    }

    $('#btnAgregar').on('click', function (e) {
        e.preventDefault();
        const rutinaId = $('#selRutina').val();
        const texto = $('#selRutina option:selected').text();
        if (!rutinaId) return;
        if ($('#tblRutinas tbody tr[data-rutina-id="' + rutinaId + '"]').length) {
            notify('Esa rutina ya está en el programa', 'warning');
            return;
        }
        const orden = nextOrden();
        $('#tblRutinas tbody').append(`
            <tr data-rutina-id="${rutinaId}">
                <td><input type="number" min="1" step="1" class="form-control form-control-sm orden" value="${orden}"></td>
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

    $(document).on('click', '.btnQuitar', function () {
        $(this).closest('tr').remove();
    });

    $('#btnGuardarRutinas').on('click', async function () {
        const btn = this;
        if (btn.disabled) return;
        btn.disabled = true;

        const items = [];
        $('#tblRutinas tbody tr').each(function () {
            const $tr = $(this);
            items.push({
                rutina_id: parseInt($tr.data('rutina-id'), 10),
                orden: parseInt($tr.find('input.orden').val() || '0', 10),
                dia_semana: $tr.find('select.dia_semana').val(),
            });
        });

        try {
            const csrfRef = { token: csrfHashGym };
            const result = window.NutriNextGym
                ? await NutriNextGym.postJson(updateUrl, { programa_id: programaId, items }, csrfRef)
                : { ok: false, error: 'Script gym no cargado' };
            csrfHashGym = csrfRef.token;
            if (!result.ok) {
                notify(result.error || 'Error al guardar', 'error');
                return;
            }
            $('#tblRutinas tbody tr').each(function (i) {
                $(this).find('input.orden').val(i + 1);
            });
            notify('Rutinas del programa guardadas', 'success');
        } finally {
            btn.disabled = false;
        }
    });
})();
</script>
<?= $this->endSection() ?>

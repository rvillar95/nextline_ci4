<?= $this->extend('layout/dashboard') ?>

<?= $this->section('servicio-nutrinext/registro') ?>

<?php
$item   = $item ?? null;
$isEdit = ! empty($item);
$accent = '#22c55e';
$accentDark = '#15803d';
$iconVal = old('icono', $item->icono ?? 'fas fa-circle');

$colorRaw = old('color', $item->color ?? '#22c55e');
$colorVal = '#22c55e';
if (is_string($colorRaw) && $colorRaw !== '') {
    $c = trim($colorRaw);
    if ($c[0] !== '#') {
        $c = '#' . $c;
    }
    if (preg_match('/^#([0-9A-Fa-f]{6})$/', $c, $m)) {
        $colorVal = '#' . strtolower($m[1]);
    } elseif (preg_match('/^#([0-9A-Fa-f]{3})$/', $c, $m)) {
        $h = $m[1];
        $colorVal = '#' . strtolower($h[0] . $h[0] . $h[1] . $h[1] . $h[2] . $h[2]);
    }
}
?>

<style>
    .section-card {
        background: #fff;
        border-radius: 12px;
        padding: 28px 30px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border-left: 4px solid <?= $accent ?>;
    }

    .section-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .section-subtitle {
        font-size: 0.95rem;
        color: #6c757d;
        margin-bottom: 22px;
        line-height: 1.6;
    }

    .step-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: <?= $accent ?>;
        color: #fff;
        border-radius: 50%;
        font-weight: 700;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .sn-form .form-group {
        margin-bottom: 22px;
    }

    .sn-form .form-label {
        font-weight: 600;
        font-size: 0.95rem;
        color: #2c3e50;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .sn-form .form-label .required {
        color: #dc3545;
        font-weight: 700;
    }

    .sn-form .form-control,
    .sn-form .form-select {
        padding: 10px 14px;
        border: 2px solid #e1e8ed;
        border-radius: 8px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .sn-form .form-control:focus,
    .sn-form .form-select:focus {
        border-color: <?= $accent ?>;
        box-shadow: 0 0 0 0.2rem rgba(34, 197, 94, 0.15);
    }

    .help-text {
        display: block;
        margin-top: 6px;
        font-size: 0.85rem;
        color: #6c757d;
        line-height: 1.45;
    }

    .icon-label {
        color: <?= $accent ?>;
    }

    .icon-preview-box {
        width: 72px;
        height: 72px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        border: 2px solid #e1e8ed;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .paquete-check-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 14px;
        height: 100%;
        transition: border-color 0.2s ease, background 0.2s ease;
    }

    .paquete-check-card:has(.form-check-input:checked) {
        border-color: <?= $accent ?>;
        background: #f0fdf4;
    }

    .btn-submit {
        padding: 14px 36px;
        font-size: 1.05rem;
        font-weight: 600;
        border-radius: 8px;
        background: <?= $accent ?>;
        border: none;
    }

    .btn-submit:hover {
        background: <?= $accentDark ?>;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35);
    }

    .btn-cancel {
        padding: 14px 32px;
        font-size: 1.05rem;
        font-weight: 600;
        border-radius: 8px;
    }

    .main-header h2 {
        margin: 0;
        font-size: 1.65rem;
        font-weight: 700;
    }

    .main-header p {
        margin: 8px 0 0;
        font-size: 0.95rem;
    }

    .form-switch .form-check-input:checked {
        background-color: <?= $accent ?>;
        border-color: <?= $accent ?>;
    }

    /* input[type=color]: sin padding alto de .form-control (tapaba el color) */
    .sn-form input[type="color"].sn-color-input {
        width: 100%;
        height: 46px;
        min-height: 46px;
        padding: 4px;
        border: 2px solid #e1e8ed;
        border-radius: 8px;
        cursor: pointer;
        background-color: #fff;
        -webkit-appearance: none;
        appearance: none;
    }

    .sn-form input[type="color"].sn-color-input::-webkit-color-swatch-wrapper {
        padding: 0;
        border-radius: 6px;
    }

    .sn-form input[type="color"].sn-color-input::-webkit-color-swatch {
        border: none;
        border-radius: 6px;
        width: 100%;
        height: 100%;
    }

    .sn-form input[type="color"].sn-color-input::-moz-color-swatch {
        border: none;
        border-radius: 6px;
        width: 100%;
        height: 100%;
    }

    .sn-color-hex {
        font-family: ui-monospace, monospace;
        font-size: 0.9rem;
        text-transform: uppercase;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h2>
                            <i class="fas fa-puzzle-piece me-2"></i>
                            <?= $isEdit ? 'Editar servicio NutriNext' : 'Nuevo servicio NutriNext' ?>
                        </h2>
                        <p><?= $isEdit ? 'Modifique los datos del catálogo de funcionalidades de la plataforma' : 'Registre una funcionalidad para el catálogo interno y la web pública' ?></p>
                    </div>
                    <a href="<?= base_url('dashboard/servicio-nutrinext/lista') ?>" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i> Volver al catálogo
                    </a>
                </div>
            </div>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading mb-2"><i class="fas fa-exclamation-triangle me-2"></i> Errores de validación</h5>
                    <?php $errors = session()->getFlashdata('errors'); ?>
                    <?php if (is_array($errors)): ?>
                        <ul class="mb-0">
                            <?php foreach ($errors as $field => $error): ?>
                                <li>
                                    <?php if (is_numeric($field)): ?>
                                        <?= esc(is_array($error) ? implode(', ', $error) : $error) ?>
                                    <?php else: ?>
                                        <strong><?= esc(ucfirst(str_replace('_', ' ', (string) $field))) ?>:</strong>
                                        <?= esc(is_array($error) ? implode(', ', $error) : $error) ?>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="mb-0"><?= nl2br(esc($errors)) ?></p>
                    <?php endif; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>

            <form class="sn-form" method="post"
                  action="<?= base_url($isEdit ? 'dashboard/servicio-nutrinext/update' : 'dashboard/servicio-nutrinext/registrar') ?>">
                <?= csrf_field() ?>
                <?php if ($isEdit): ?>
                    <input type="hidden" name="id" value="<?= (int) $item->id ?>">
                <?php endif; ?>

                <!-- Paso 1 -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">1</span>
                        <span><i class="fas fa-info-circle icon-label"></i> Información básica</span>
                    </div>
                    <p class="section-subtitle">
                        Nombre, categoría e identificador del servicio. Los campos con <span class="required">*</span> son obligatorios.
                    </p>

                    <div class="row">
                        <div class="col-lg-5 col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="nombre">
                                    <i class="fas fa-tag icon-label"></i> Nombre <span class="required">*</span>
                                </label>
                                <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="150"
                                       value="<?= esc(old('nombre', $item->nombre ?? '')) ?>"
                                       placeholder="Ej: Agenda de citas">
                                <small class="help-text">Título visible en el catálogo y en la web.</small>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="codigo">
                                    <i class="fas fa-link icon-label"></i> Código (slug)
                                </label>
                                <input type="text" id="codigo" name="codigo" class="form-control" maxlength="80"
                                       value="<?= esc(old('codigo', $item->codigo ?? '')) ?>"
                                       placeholder="agenda-citas">
                                <small class="help-text">URL en la web: /funcionalidades/<strong>codigo</strong>. Se genera solo si está vacío.</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="categoria">
                                    <i class="fas fa-folder icon-label"></i> Categoría <span class="required">*</span>
                                </label>
                                <select id="categoria" name="categoria" class="form-select" required>
                                    <?php foreach ($categorias as $key => $label): ?>
                                        <option value="<?= esc($key) ?>" <?= old('categoria', $item->categoria ?? 'clinica') === $key ? 'selected' : '' ?>>
                                            <?= esc($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-end">
                        <div class="col-md-4 col-lg-3">
                            <div class="form-group">
                                <label class="form-label" for="icono">
                                    <i class="fas fa-icons icon-label"></i> Icono (Font Awesome)
                                </label>
                                <input type="text" id="icono" name="icono" class="form-control"
                                       value="<?= esc($iconVal) ?>"
                                       placeholder="fas fa-calendar-alt">
                            </div>
                        </div>
                        <div class="col-md-2 col-lg-2">
                            <div class="form-group">
                                <label class="form-label" for="color">Color</label>
                                <input type="color" id="color" name="color" class="sn-color-input"
                                       value="<?= esc($colorVal) ?>" title="Seleccionar color">
                                <input type="text" id="color_hex" class="form-control sn-color-hex mt-2"
                                       value="<?= esc($colorVal) ?>" maxlength="7"
                                       pattern="^#[0-9A-Fa-f]{6}$" placeholder="#22C55E"
                                       aria-label="Código hexadecimal del color">
                            </div>
                        </div>
                        <div class="col-md-2 col-lg-2">
                            <div class="form-group mb-md-0">
                                <label class="form-label d-block">Vista previa</label>
                                <div class="icon-preview-box" id="iconPreview"
                                     style="background: <?= esc($colorVal) ?>22; color: <?= esc($colorVal) ?>;">
                                    <i class="<?= esc($iconVal) ?>" id="iconPreviewI"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-lg-2">
                            <div class="form-group">
                                <label class="form-label" for="orden">Orden</label>
                                <input type="number" id="orden" name="orden" class="form-control" min="0"
                                       value="<?= esc(old('orden', $item->orden ?? 0)) ?>">
                                <small class="help-text">Menor = primero.</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-lg-3">
                            <div class="form-group">
                                <label class="form-label" for="estado">Estado</label>
                                <select id="estado" name="estado" class="form-select">
                                    <option value="A" <?= old('estado', $item->estado ?? 'A') === 'A' ? 'selected' : '' ?>>Activo</option>
                                    <option value="I" <?= old('estado', $item->estado ?? '') === 'I' ? 'selected' : '' ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paso 2 -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">2</span>
                        <span><i class="fas fa-align-left icon-label"></i> Descripción y contenido</span>
                    </div>
                    <p class="section-subtitle">Textos que verán los nutricionistas y los visitantes del sitio web.</p>

                    <div class="form-group">
                        <label class="form-label" for="descripcion_corta">
                            Descripción corta <span class="required">*</span>
                        </label>
                        <textarea id="descripcion_corta" name="descripcion_corta" class="form-control" rows="2"
                                  required maxlength="500" placeholder="Resumen para tarjetas y listados"><?= esc(old('descripcion_corta', $item->descripcion_corta ?? '')) ?></textarea>
                        <small class="help-text">Máximo 500 caracteres. Aparece en tarjetas del home y listado de funcionalidades.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="descripcion_larga">Descripción larga</label>
                        <textarea id="descripcion_larga" name="descripcion_larga" class="form-control" rows="5"
                                  placeholder="Detalle completo en la página pública del servicio"><?= esc(old('descripcion_larga', $item->descripcion_larga ?? '')) ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-md-0">
                                <label class="form-label" for="beneficios">
                                    <i class="fas fa-star icon-label"></i> Beneficios
                                </label>
                                <textarea id="beneficios" name="beneficios" class="form-control" rows="5"
                                          placeholder="Un beneficio por línea"><?= esc(old('beneficios', $item->beneficios ?? '')) ?></textarea>
                                <small class="help-text">Un ítem por línea. También puede usar | como separador.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="form-label" for="incluye">
                                    <i class="fas fa-check-circle icon-label"></i> Qué incluye
                                </label>
                                <textarea id="incluye" name="incluye" class="form-control" rows="5"
                                          placeholder="Calendario&#10;Estados de cita&#10;Sync calendario"><?= esc(old('incluye', $item->incluye ?? '')) ?></textarea>
                                <small class="help-text">Un ítem por línea o separados por |</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3 mb-0">
                        <label class="form-label" for="etiquetas">
                            <i class="fas fa-tags icon-label"></i> Etiquetas (opcional)
                        </label>
                        <input type="text" id="etiquetas" name="etiquetas" class="form-control"
                               value="<?= esc(old('etiquetas', $item->etiquetas ?? '')) ?>"
                               placeholder="agenda, whatsapp, pacientes">
                        <small class="help-text">Separadas por comas, para búsqueda interna.</small>
                    </div>
                </div>

                <!-- Paso 3 -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">3</span>
                        <span><i class="fas fa-cogs icon-label"></i> Vínculo con el sistema</span>
                    </div>
                    <p class="section-subtitle">Relacione este servicio con un módulo del dashboard y la ruta de acceso.</p>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="modulo_id">Módulo relacionado</label>
                                <select id="modulo_id" name="modulo_id" class="form-select">
                                    <option value="">— Ninguno —</option>
                                    <?php foreach ($modulos as $m): ?>
                                        <option value="<?= (int) $m['id'] ?>"
                                            <?= (string) old('modulo_id', $item->modulo_id ?? '') === (string) $m['id'] ? 'selected' : '' ?>>
                                            <?= esc($m['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="form-label" for="ruta_dashboard">Ruta en dashboard</label>
                                <input type="text" id="ruta_dashboard" name="ruta_dashboard" class="form-control"
                                       value="<?= esc(old('ruta_dashboard', $item->ruta_dashboard ?? '')) ?>"
                                       placeholder="/dashboard/agenda/calendario">
                                <small class="help-text">Enlace del botón «Ir» en el listado del catálogo.</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label" for="requiere_configuracion">Requiere configuración</label>
                                <select id="requiere_configuracion" name="requiere_configuracion" class="form-select">
                                    <option value="N" <?= old('requiere_configuracion', $item->requiere_configuracion ?? 'N') === 'N' ? 'selected' : '' ?>>No</option>
                                    <option value="S" <?= old('requiere_configuracion', $item->requiere_configuracion ?? '') === 'S' ? 'selected' : '' ?>>Sí</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group mb-0">
                                <label class="form-label" for="nota_configuracion">Nota de configuración</label>
                                <input type="text" id="nota_configuracion" name="nota_configuracion" class="form-control"
                                       value="<?= esc(old('nota_configuracion', $item->nota_configuracion ?? '')) ?>"
                                       placeholder="Ej: Configurar credenciales de Mercado Pago">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paso 4 -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">4</span>
                        <span><i class="fas fa-globe icon-label"></i> Visibilidad y enlaces</span>
                    </div>
                    <p class="section-subtitle">Defina dónde se muestra este servicio y recursos adicionales.</p>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="sw_visible_web"
                                       <?= old('visible_web', $item->visible_web ?? 'N') === 'S' ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold" for="sw_visible_web">Visible en web pública</label>
                            </div>
                            <input type="hidden" name="visible_web" id="visible_web"
                                   value="<?= old('visible_web', $item->visible_web ?? 'N') ?>">
                            <small class="help-text">Aparece en /funcionalidades y en el home.</small>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="sw_visible_catalogo"
                                       <?= old('visible_catalogo', $item->visible_catalogo ?? 'S') === 'S' ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold" for="sw_visible_catalogo">Visible en catálogo interno</label>
                            </div>
                            <input type="hidden" name="visible_catalogo" id="visible_catalogo"
                                   value="<?= old('visible_catalogo', $item->visible_catalogo ?? 'S') ?>">
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="sw_destacado"
                                       <?= old('destacado', $item->destacado ?? 'N') === 'S' ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold" for="sw_destacado">Destacado en home</label>
                            </div>
                            <input type="hidden" name="destacado" id="destacado"
                                   value="<?= old('destacado', $item->destacado ?? 'N') ?>">
                            <small class="help-text">Hasta 6 destacados en la landing.</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="documentacion_url">
                                    <i class="fas fa-book icon-label"></i> URL documentación
                                </label>
                                <input type="url" id="documentacion_url" name="documentacion_url" class="form-control"
                                       value="<?= esc(old('documentacion_url', $item->documentacion_url ?? '')) ?>"
                                       placeholder="https://...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="form-label" for="video_url">
                                    <i class="fab fa-youtube icon-label"></i> URL video
                                </label>
                                <input type="url" id="video_url" name="video_url" class="form-control"
                                       value="<?= esc(old('video_url', $item->video_url ?? '')) ?>"
                                       placeholder="https://youtube.com/...">
                            </div>
                        </div>
                    </div>

                    <?php if (! empty($paquetes)): ?>
                    <hr class="my-4">
                    <label class="form-label mb-3">
                        <i class="fas fa-cubes icon-label"></i> Incluido en paquetes
                    </label>
                    <div class="row g-2">
                        <?php foreach ($paquetes as $p): ?>
                            <div class="col-md-4 col-lg-3">
                                <div class="paquete-check-card">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="paquetes[]"
                                               value="<?= (int) $p->id ?>" id="pkg_<?= (int) $p->id ?>"
                                               <?= in_array((int) $p->id, $paquetes_seleccionados ?? [], true) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="pkg_<?= (int) $p->id ?>">
                                            <?= esc($p->nombre) ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Acciones -->
                <div class="section-card" style="border-left-color: #28a745;">
                    <div class="d-flex flex-wrap gap-3 justify-content-center">
                        <button type="submit" class="btn btn-primary btn-submit text-white">
                            <i class="fas fa-save me-2"></i><?= $isEdit ? 'Guardar cambios' : 'Registrar servicio' ?>
                        </button>
                        <a href="<?= base_url('dashboard/servicio-nutrinext/lista') ?>" class="btn btn-secondary btn-cancel">
                            <i class="fas fa-times me-2"></i> Cancelar
                        </a>
                    </div>
                    <?php if ($isEdit && ! empty($item->codigo)): ?>
                        <p class="text-center mt-3 mb-0">
                            <a href="<?= base_url('funcionalidades/' . rawurlencode($item->codigo)) ?>" target="_blank" rel="noopener" class="text-success">
                                <i class="fas fa-external-link-alt me-1"></i> Ver en sitio web
                            </a>
                        </p>
                    <?php endif; ?>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var iconInput = document.getElementById('icono');
    var colorInput = document.getElementById('color');
    var colorHex = document.getElementById('color_hex');
    var preview = document.getElementById('iconPreview');
    var previewI = document.getElementById('iconPreviewI');

    function normalizeHex(val) {
        if (!val) return '#22c55e';
        var c = String(val).trim();
        if (c.charAt(0) !== '#') c = '#' + c;
        if (/^#[0-9A-Fa-f]{6}$/.test(c)) return c.toLowerCase();
        if (/^#[0-9A-Fa-f]{3}$/.test(c)) {
            return ('#' + c[1] + c[1] + c[2] + c[2] + c[3] + c[3]).toLowerCase();
        }
        return '#22c55e';
    }

    function applyColor(hex) {
        var color = normalizeHex(hex);
        if (colorInput) colorInput.value = color;
        if (colorHex) colorHex.value = color.toUpperCase();
        if (preview && previewI) {
            var icon = (iconInput && iconInput.value.trim()) ? iconInput.value.trim() : 'fas fa-circle';
            previewI.className = icon;
            preview.style.background = color + '22';
            preview.style.color = color;
        }
    }

    function updatePreview() {
        applyColor(colorInput ? colorInput.value : (colorHex ? colorHex.value : '#22c55e'));
    }

    if (iconInput) iconInput.addEventListener('input', updatePreview);
    if (colorInput) {
        colorInput.addEventListener('input', function () { applyColor(colorInput.value); });
        colorInput.addEventListener('change', function () { applyColor(colorInput.value); });
    }
    if (colorHex) {
        colorHex.addEventListener('input', function () {
            if (/^#[0-9A-Fa-f]{6}$/i.test(colorHex.value.trim())) {
                applyColor(colorHex.value);
            }
        });
        colorHex.addEventListener('blur', function () { applyColor(colorHex.value); });
    }

    applyColor(colorInput ? colorInput.value : '#22c55e');

    function bindSwitch(switchId, hiddenId) {
        var sw = document.getElementById(switchId);
        var hid = document.getElementById(hiddenId);
        if (!sw || !hid) return;
        sw.addEventListener('change', function () {
            hid.value = sw.checked ? 'S' : 'N';
        });
    }

    bindSwitch('sw_visible_web', 'visible_web');
    bindSwitch('sw_visible_catalogo', 'visible_catalogo');
    bindSwitch('sw_destacado', 'destacado');
})();
</script>

<?= $this->endSection() ?>

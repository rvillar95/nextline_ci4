<?= $this->extend('layout/dashboard') ?>

<?= $this->section('mi_perfil/index') ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2><i class="fas fa-user-circle me-2"></i> Mi perfil</h2>
                        <p>Foto de perfil, tema y colores del dashboard</p>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <?= csrf_field() ?>

                    <!-- Foto de perfil -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-camera me-2"></i> Foto de perfil</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-start gap-4 flex-wrap">
                                <div class="text-center">
                                    <?php
                                    $fotoUrl = !empty($usuario['foto']) ? base_url($usuario['foto']) : base_url('lib/src/assets/img/profile-30.png');
                                    ?>
                                    <img id="previewFoto" src="<?= esc($fotoUrl) ?>" alt="Foto de perfil" class="rounded-circle border" style="width: 120px; height: 120px; object-fit: cover;">
                                    <p class="small text-muted mt-2 mb-0">JPG, PNG, GIF o WebP · máx. 2 MB</p>
                                </div>
                                <div class="flex-grow-1">
                                    <form id="formFoto" enctype="multipart/form-data">
                                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                                        <div class="mb-2">
                                            <input type="file" class="form-control" id="inputFoto" name="foto" accept="image/jpeg,image/png,image/gif,image/webp">
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm" id="btnSubirFoto">
                                            <i class="fas fa-upload me-1"></i> Subir foto
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tema (claro / oscuro) y color de acento -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-palette me-2"></i> Apariencia</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">Elegí el tema y el color de acento del dashboard. Los cambios se aplican al recargar o en la próxima visita.</p>
                            <form id="formTema">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Tema</label>
                                    <div class="d-flex gap-3 flex-wrap">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tema" id="tema_claro" value="claro" <?= ($usuario['tema'] ?? 'claro') === 'claro' ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="tema_claro">
                                                <i class="fas fa-sun me-1"></i> Claro
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tema" id="tema_oscuro" value="oscuro" <?= ($usuario['tema'] ?? '') === 'oscuro' ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="tema_oscuro">
                                                <i class="fas fa-moon me-1"></i> Oscuro
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold" for="color_primario">Color de acento</label>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <input type="color" class="form-control form-control-color" id="color_primario" name="color_primario" value="<?= esc($usuario['color_primario'] ?? '#7bc143') ?>" title="Elegir color" style="width: 2.5rem; height: 2.5rem; padding: 2px; cursor: pointer;">
                                        
                                        <input type="text" class="form-control form-control-sm" id="color_primario_hex" maxlength="7" style="max-width: 100px;" value="<?= esc($usuario['color_primario'] ?? '#7bc143') ?>" placeholder="#7bc143">
                                    </div>
                                    <p class="text-muted small mb-0 mt-1">Se usa en botones, enlaces y detalles del menú.</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Encabezados de tarjetas (card-header)</label>
                                    <p class="text-muted small mb-2">Fondo y texto de los títulos de las tarjetas (ej. "Foto de perfil", "Apariencia", "Agenda de Citas").</p>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="card_header_por_defecto" id="card_header_por_defecto" value="1" <?= (int)($usuario['card_header_por_defecto'] ?? 1) === 1 ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="card_header_por_defecto">Dejar por defecto (estilo original de Bootstrap)</label>
                                    </div>
                                    <div id="card_header_personalizado" class="<?= (int)($usuario['card_header_por_defecto'] ?? 1) === 1 ? 'd-none' : '' ?>">
                                        <div class="mb-2">
                                            <label class="form-label small mb-1">Tipo de fondo</label>
                                            <div class="d-flex gap-3 flex-wrap">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="card_header_es_gradiente" id="card_header_un_color" value="0" <?= (int)($usuario['card_header_es_gradiente'] ?? 0) === 0 ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="card_header_un_color">Un color</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="card_header_es_gradiente" id="card_header_gradiente" value="1" <?= (int)($usuario['card_header_es_gradiente'] ?? 0) === 1 ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="card_header_gradiente">Gradiente (2 colores)</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small mb-0" id="label_fondo"><?= (int)($usuario['card_header_es_gradiente'] ?? 0) === 1 ? 'Color 1' : 'Fondo' ?></label>
                                            <div class="d-flex align-items-center gap-1">
                                                <input type="color" id="color_card_header_bg" name="color_card_header_bg" value="<?= esc($usuario['color_card_header_bg'] ?? '#6c757d') ?>" title="Fondo" style="width: 2.25rem; height: 2.25rem; padding: 2px; cursor: pointer;">
                                                <input type="text" class="form-control form-control-sm" id="color_card_header_bg_hex" maxlength="7" style="max-width: 85px;" value="<?= esc($usuario['color_card_header_bg'] ?? '#6c757d') ?>">
                                            </div>
                                        </div>
                                        <div id="card_header_fondo_gradiente" class="mb-2 <?= (int)($usuario['card_header_es_gradiente'] ?? 0) === 0 ? 'd-none' : '' ?>">
                                            <label class="form-label small mb-0">Color 2</label>
                                            <div class="d-flex align-items-center gap-1">
                                                <input type="color" id="color_card_header_bg2" name="color_card_header_bg2" value="<?= esc($usuario['color_card_header_bg2'] ?? '#495057') ?>" title="Color 2" style="width: 2.25rem; height: 2.25rem; padding: 2px; cursor: pointer;">
                                                <input type="text" class="form-control form-control-sm" id="color_card_header_bg2_hex" maxlength="7" style="max-width: 85px;" value="<?= esc($usuario['color_card_header_bg2'] ?? '#495057') ?>">
                                            </div>
                                        </div>
                                        <div class="row g-2 mt-1">
                                            <div class="col-auto">
                                                <label class="form-label small mb-0">Texto</label>
                                                <div class="d-flex align-items-center gap-1">
                                                    <input type="color" id="color_card_header_text" name="color_card_header_text" value="<?= esc($usuario['color_card_header_text'] ?? '#ffffff') ?>" title="Texto" style="width: 2.25rem; height: 2.25rem; padding: 2px; cursor: pointer;">
                                                    <input type="text" class="form-control form-control-sm" id="color_card_header_text_hex" maxlength="7" style="max-width: 85px;" value="<?= esc($usuario['color_card_header_text'] ?? '#ffffff') ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Título del módulo (main-header)</label>
                                    <p class="text-muted small mb-2">Fondo y texto del encabezado principal de cada página (ej. "Mi perfil", "Agenda de Citas", "Gestión de Pacientes").</p>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="main_header_por_defecto" id="main_header_por_defecto" value="1" <?= (int)($usuario['main_header_por_defecto'] ?? 1) === 1 ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="main_header_por_defecto">Dejar por defecto (gradiente azul–cyan original)</label>
                                    </div>
                                    <div id="main_header_personalizado" class="<?= (int)($usuario['main_header_por_defecto'] ?? 1) === 1 ? 'd-none' : '' ?>">
                                        <div class="mb-2">
                                            <label class="form-label small mb-1">Tipo de fondo</label>
                                            <div class="d-flex gap-3 flex-wrap">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="main_header_es_gradiente" id="main_header_un_color" value="0" <?= (int)($usuario['main_header_es_gradiente'] ?? 0) === 0 ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="main_header_un_color">Un color</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="main_header_es_gradiente" id="main_header_gradiente" value="1" <?= (int)($usuario['main_header_es_gradiente'] ?? 0) === 1 ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="main_header_gradiente">Gradiente (2 colores)</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small mb-0" id="label_main_header_fondo"><?= (int)($usuario['main_header_es_gradiente'] ?? 0) === 1 ? 'Color 1' : 'Fondo' ?></label>
                                            <div class="d-flex align-items-center gap-1">
                                                <input type="color" id="color_main_header_bg" name="color_main_header_bg" value="<?= esc($usuario['color_main_header_bg'] ?? '#4A90E2') ?>" title="Fondo" style="width: 2.25rem; height: 2.25rem; padding: 2px; cursor: pointer;">
                                                <input type="text" class="form-control form-control-sm" id="color_main_header_bg_hex" maxlength="7" style="max-width: 85px;" value="<?= esc($usuario['color_main_header_bg'] ?? '#4A90E2') ?>">
                                            </div>
                                        </div>
                                        <div id="main_header_fondo_gradiente" class="mb-2 <?= (int)($usuario['main_header_es_gradiente'] ?? 0) === 0 ? 'd-none' : '' ?>">
                                            <label class="form-label small mb-0">Color 2</label>
                                            <div class="d-flex align-items-center gap-1">
                                                <input type="color" id="color_main_header_bg2" name="color_main_header_bg2" value="<?= esc($usuario['color_main_header_bg2'] ?? '#67C6E0') ?>" title="Color 2" style="width: 2.25rem; height: 2.25rem; padding: 2px; cursor: pointer;">
                                                <input type="text" class="form-control form-control-sm" id="color_main_header_bg2_hex" maxlength="7" style="max-width: 85px;" value="<?= esc($usuario['color_main_header_bg2'] ?? '#67C6E0') ?>">
                                            </div>
                                        </div>
                                        <div class="row g-2 mt-1">
                                            <div class="col-auto">
                                                <label class="form-label small mb-0">Texto</label>
                                                <div class="d-flex align-items-center gap-1">
                                                    <input type="color" id="color_main_header_text" name="color_main_header_text" value="<?= esc($usuario['color_main_header_text'] ?? '#ffffff') ?>" title="Texto" style="width: 2.25rem; height: 2.25rem; padding: 2px; cursor: pointer;">
                                                    <input type="text" class="form-control form-control-sm" id="color_main_header_text_hex" maxlength="7" style="max-width: 85px;" value="<?= esc($usuario['color_main_header_text'] ?? '#ffffff') ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm mt-2">
                                    <i class="fas fa-save me-1"></i> Guardar preferencias
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    var csrfToken = $('input[name="<?= csrf_token() ?>"]').val();

    $('#formFoto').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btnSubirFoto');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Subiendo...');
        var formData = new FormData(this);
        $.ajax({
            url: '<?= base_url('dashboard/mi-perfil/subir-foto') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
            success: function(res) {
                if (res.success) {
                    $('#previewFoto').attr('src', res.foto_url + '?t=' + Date.now());
                    toastr.success(res.message || 'Foto actualizada');
                } else {
                    toastr.error(res.error || 'Error al subir');
                }
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'Error al subir la foto';
                toastr.error(msg);
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-upload me-1"></i> Subir foto');
            }
        });
    });

    function actualizarSwatch(hex) {
        $('#color_swatch').css('background-color', hex || '#7bc143');
    }
    $('#color_primario').on('input', function() {
        var hex = $(this).val();
        $('#color_primario_hex').val(hex);
        actualizarSwatch(hex);
    });
    $('#color_primario_hex').on('input', function() {
        var v = $(this).val().replace(/[^#a-fA-F0-9]/g, '');
        if (v.indexOf('#') !== 0) v = '#' + v;
        if (/^#[a-fA-F0-9]{6}$/.test(v)) {
            $('#color_primario').val(v);
            actualizarSwatch(v);
        }
        $(this).val(v || '#');
    });

    $('#card_header_por_defecto').on('change', function() {
        $('#card_header_personalizado').toggleClass('d-none', $(this).is(':checked'));
    });
    $('input[name="card_header_es_gradiente"]').on('change', function() {
        var esGrad = $('#card_header_gradiente').is(':checked');
        $('#card_header_fondo_gradiente').toggleClass('d-none', !esGrad);
        $('#label_fondo').text(esGrad ? 'Color 1' : 'Fondo');
    });

    $('#color_card_header_bg').on('input', function() { $('#color_card_header_bg_hex').val($(this).val()); });
    $('#color_card_header_bg_hex').on('input', function() {
        var v = $(this).val().replace(/[^#a-fA-F0-9]/g, '');
        if (v.indexOf('#') !== 0) v = '#' + v;
        if (/^#[a-fA-F0-9]{6}$/.test(v)) $('#color_card_header_bg').val(v);
        $(this).val(v || '#');
    });
    $('#color_card_header_bg2').on('input', function() { $('#color_card_header_bg2_hex').val($(this).val()); });
    $('#color_card_header_bg2_hex').on('input', function() {
        var v = $(this).val().replace(/[^#a-fA-F0-9]/g, '');
        if (v.indexOf('#') !== 0) v = '#' + v;
        if (/^#[a-fA-F0-9]{6}$/.test(v)) $('#color_card_header_bg2').val(v);
        $(this).val(v || '#');
    });
    $('#color_card_header_text').on('input', function() { $('#color_card_header_text_hex').val($(this).val()); });
    $('#color_card_header_text_hex').on('input', function() {
        var v = $(this).val().replace(/[^#a-fA-F0-9]/g, '');
        if (v.indexOf('#') !== 0) v = '#' + v;
        if (/^#[a-fA-F0-9]{6}$/.test(v)) $('#color_card_header_text').val(v);
        $(this).val(v || '#');
    });

    $('#main_header_por_defecto').on('change', function() {
        $('#main_header_personalizado').toggleClass('d-none', $(this).is(':checked'));
    });
    $('input[name="main_header_es_gradiente"]').on('change', function() {
        var esGrad = $('#main_header_gradiente').is(':checked');
        $('#main_header_fondo_gradiente').toggleClass('d-none', !esGrad);
        $('#label_main_header_fondo').text(esGrad ? 'Color 1' : 'Fondo');
    });
    $('#color_main_header_bg').on('input', function() { $('#color_main_header_bg_hex').val($(this).val()); });
    $('#color_main_header_bg_hex').on('input', function() {
        var v = $(this).val().replace(/[^#a-fA-F0-9]/g, '');
        if (v.indexOf('#') !== 0) v = '#' + v;
        if (/^#[a-fA-F0-9]{6}$/.test(v)) $('#color_main_header_bg').val(v);
        $(this).val(v || '#');
    });
    $('#color_main_header_bg2').on('input', function() { $('#color_main_header_bg2_hex').val($(this).val()); });
    $('#color_main_header_bg2_hex').on('input', function() {
        var v = $(this).val().replace(/[^#a-fA-F0-9]/g, '');
        if (v.indexOf('#') !== 0) v = '#' + v;
        if (/^#[a-fA-F0-9]{6}$/.test(v)) $('#color_main_header_bg2').val(v);
        $(this).val(v || '#');
    });
    $('#color_main_header_text').on('input', function() { $('#color_main_header_text_hex').val($(this).val()); });
    $('#color_main_header_text_hex').on('input', function() {
        var v = $(this).val().replace(/[^#a-fA-F0-9]/g, '');
        if (v.indexOf('#') !== 0) v = '#' + v;
        if (/^#[a-fA-F0-9]{6}$/.test(v)) $('#color_main_header_text').val(v);
        $(this).val(v || '#');
    });

    $('#formTema').on('submit', function(e) {
        e.preventDefault();
        var tema = $('input[name="tema"]:checked').val();
        var color = $('#color_primario').val() || $('#color_primario_hex').val() || '#7bc143';
        if (color.indexOf('#') !== 0) color = '#' + color;
        var porDefecto = $('#card_header_por_defecto').is(':checked') ? 1 : 0;
        var esGradiente = $('#card_header_gradiente').is(':checked') ? 1 : 0;
        var cardHeaderBg = $('#color_card_header_bg').val() || $('#color_card_header_bg_hex').val() || '#6c757d';
        if (cardHeaderBg.indexOf('#') !== 0) cardHeaderBg = '#' + cardHeaderBg;
        var cardHeaderBg2 = '';
        if (esGradiente) {
            cardHeaderBg2 = $('#color_card_header_bg2').val() || $('#color_card_header_bg2_hex').val() || '#495057';
            if (cardHeaderBg2.indexOf('#') !== 0) cardHeaderBg2 = '#' + cardHeaderBg2;
        }
        var cardHeaderText = $('#color_card_header_text').val() || $('#color_card_header_text_hex').val() || '#ffffff';
        if (cardHeaderText.indexOf('#') !== 0) cardHeaderText = '#' + cardHeaderText;
        var mainPorDefecto = $('#main_header_por_defecto').is(':checked') ? 1 : 0;
        var mainEsGradiente = $('#main_header_gradiente').is(':checked') ? 1 : 0;
        var mainHeaderBg = $('#color_main_header_bg').val() || $('#color_main_header_bg_hex').val() || '#4A90E2';
        if (mainHeaderBg.indexOf('#') !== 0) mainHeaderBg = '#' + mainHeaderBg;
        var mainHeaderBg2 = '';
        if (mainEsGradiente) {
            mainHeaderBg2 = $('#color_main_header_bg2').val() || $('#color_main_header_bg2_hex').val() || '#67C6E0';
            if (mainHeaderBg2.indexOf('#') !== 0) mainHeaderBg2 = '#' + mainHeaderBg2;
        }
        var mainHeaderText = $('#color_main_header_text').val() || $('#color_main_header_text_hex').val() || '#ffffff';
        if (mainHeaderText.indexOf('#') !== 0) mainHeaderText = '#' + mainHeaderText;
        var payload = {
            tema: tema,
            color_primario: color,
            card_header_por_defecto: porDefecto,
            card_header_es_gradiente: esGradiente,
            color_card_header_bg: cardHeaderBg,
            color_card_header_text: cardHeaderText,
            main_header_por_defecto: mainPorDefecto,
            main_header_es_gradiente: mainEsGradiente,
            color_main_header_bg: mainHeaderBg,
            color_main_header_text: mainHeaderText,
            <?= csrf_token() ?>: csrfToken
        };
        if (esGradiente && cardHeaderBg2) payload.color_card_header_bg2 = cardHeaderBg2;
        if (mainEsGradiente && mainHeaderBg2) payload.color_main_header_bg2 = mainHeaderBg2;
        $.post('<?= base_url('dashboard/mi-perfil/guardar') ?>', payload, 'json')
            .done(function(res) {
                if (res.success) {
                    toastr.success(res.message || 'Preferencias guardadas. Recargando...');
                    if (res.csrf_token) {
                        csrfToken = res.csrf_token;
                        $('input[name="<?= csrf_token() ?>"]').val(csrfToken);
                    }
                    if (res.color_primario) {
                        $('#color_primario').val(res.color_primario);
                        $('#color_primario_hex').val(res.color_primario);
                    }
                    if (res.color_card_header_bg) {
                        $('#color_card_header_bg').val(res.color_card_header_bg);
                        $('#color_card_header_bg_hex').val(res.color_card_header_bg);
                    }
                    if (res.color_card_header_bg2) {
                        $('#color_card_header_bg2').val(res.color_card_header_bg2);
                        $('#color_card_header_bg2_hex').val(res.color_card_header_bg2);
                    }
                    if (res.color_card_header_text) {
                        $('#color_card_header_text').val(res.color_card_header_text);
                        $('#color_card_header_text_hex').val(res.color_card_header_text);
                    }
                    if (res.color_main_header_bg) {
                        $('#color_main_header_bg').val(res.color_main_header_bg);
                        $('#color_main_header_bg_hex').val(res.color_main_header_bg);
                    }
                    if (res.color_main_header_bg2) {
                        $('#color_main_header_bg2').val(res.color_main_header_bg2);
                        $('#color_main_header_bg2_hex').val(res.color_main_header_bg2);
                    }
                    if (res.color_main_header_text) {
                        $('#color_main_header_text').val(res.color_main_header_text);
                        $('#color_main_header_text_hex').val(res.color_main_header_text);
                    }
                    setTimeout(function() { location.reload(); }, 600);
                }
            })
            .fail(function(xhr) {
                toastr.error((xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'Error al guardar');
            });
    });
});
</script>
<?= $this->endSection() ?>
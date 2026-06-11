<?= $this->extend('layout/dashboard') ?>

<?= $this->section('documento/registro') ?>

<style>
    .doc-batch-header {
        background: linear-gradient(135deg, #4facfe 0%, #00c6fb 100%);
        border-radius: 12px;
        padding: 1.5rem 1.75rem;
        color: #fff;
        margin-bottom: 1.5rem;
    }
    .doc-batch-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06);
        border: 1px solid #e8edf2;
    }
    .doc-batch-card h6 {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 1rem;
    }
    .doc-dropzone {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 2rem 1.5rem;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
    }
    .doc-dropzone:hover,
    .doc-dropzone.dragover {
        border-color: #4facfe;
        background: #f0f9ff;
    }
    .doc-dropzone.disabled {
        opacity: 0.55;
        pointer-events: none;
    }
    .doc-dropzone i.fa-cloud-upload-alt {
        font-size: 2.5rem;
        color: #4facfe;
        margin-bottom: 0.75rem;
    }
    .doc-item-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        margin-bottom: 1rem;
        overflow: hidden;
        background: #fff;
    }
    .doc-item-card .doc-item-head {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .doc-item-card .doc-item-head .doc-file-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: #e0f2fe;
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .doc-item-card .doc-item-body {
        padding: 1rem;
    }
    .doc-item-card .doc-item-meta {
        font-size: 0.8rem;
        color: #64748b;
        word-break: break-all;
    }
    .doc-toolbar {
        position: sticky;
        bottom: 0;
        z-index: 20;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border-top: 1px solid #e2e8f0;
        padding: 1rem 0;
        margin-top: 1rem;
    }
    .btn-submit-batch {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: #fff;
        border: none;
        padding: 0.65rem 1.75rem;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(79, 172, 254, 0.35);
    }
    .btn-submit-batch:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .doc-empty-state {
        text-align: center;
        padding: 2rem;
        color: #94a3b8;
    }
    .doc-count-badge {
        background: #4facfe;
        color: #fff;
        font-size: 0.75rem;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        margin-left: 0.35rem;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="doc-batch-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h2 class="h4 mb-1"><i class="fas fa-file-upload me-2"></i> Cargar documentos</h2>
                        <p class="mb-0 opacity-90 small">Seleccioná el paciente, subí varios archivos y completá los datos de cada uno. Guardá todo de una vez.</p>
                    </div>
                    <a href="<?= base_url('dashboard/documento/lista') ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Volver
                    </a>
                </div>
            </div>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <?php
                    $errs = session()->getFlashdata('errors');
                    if (is_array($errs)) {
                        echo '<ul class="mb-0">';
                        foreach ($errs as $e) {
                            echo '<li>' . esc($e) . '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo esc($errs);
                    }
                    ?>
                </div>
            <?php endif; ?>

            <form id="formDocumentosLote" action="<?= base_url('dashboard/documento/registrar-lote') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="doc-batch-card">
                    <h6><span class="badge bg-primary me-2">1</span> Paciente</h6>
                    <div class="row">
                        <div class="col-md-8 col-lg-6">
                            <label class="form-label">Paciente <span class="text-danger">*</span></label>
                            <select name="paciente_id" id="paciente_id" class="form-select" required>
                                <option value="">— Seleccione un paciente —</option>
                                <?php foreach ($pacientes as $paciente) : ?>
                                    <option value="<?= $paciente->id ?>"><?= esc($paciente->nombre_completo ?? ($paciente->nombre . ' ' . $paciente->apellido)) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Todos los documentos de esta carga se asociarán a este paciente.</small>
                        </div>
                    </div>
                </div>

                <div class="doc-batch-card">
                    <h6><span class="badge bg-primary me-2">2</span> Archivos</h6>
                    <div id="dropzone" class="doc-dropzone disabled" role="button" tabindex="0" aria-label="Zona para arrastrar archivos">
                        <i class="fas fa-cloud-upload-alt d-block"></i>
                        <p class="mb-1 fw-semibold text-dark">Arrastrá archivos aquí o hacé clic para seleccionar</p>
                        <p class="mb-0 small text-muted">PDF o imágenes (JPG, PNG, WebP). Máx. 5 MB por archivo. Hasta 25 documentos.</p>
                        <input type="file" id="inputArchivos" class="d-none" multiple accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/*">
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnAgregarManual" disabled>
                            <i class="fas fa-plus me-1"></i> Agregar documento sin archivo
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm d-none" id="btnLimpiarLista">
                            <i class="fas fa-trash me-1"></i> Quitar todos
                        </button>
                    </div>
                </div>

                <div class="doc-batch-card" id="seccionDetalles" style="display:none;">
                    <h6>
                        <span class="badge bg-primary me-2">3</span> Detalle por documento
                        <span class="doc-count-badge" id="badgeCount">0</span>
                    </h6>
                    <p class="small text-muted mb-3">Completá tipo, título y fecha de cada documento. El título se sugiere desde el nombre del archivo.</p>
                    <div id="listaDocumentos"></div>
                    <div id="estadoVacio" class="doc-empty-state d-none">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        No hay documentos en la lista.
                    </div>
                </div>

                <div class="doc-toolbar">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span class="text-muted small" id="resumenToolbar">Seleccioná un paciente y agregá archivos.</span>
                        <div class="d-flex gap-2">
                            <a href="<?= base_url('dashboard/documento/lista') ?>" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-submit-batch" id="btnGuardarLote" disabled>
                                <i class="fas fa-save me-1"></i> Guardar todos (<span id="btnCount">0</span>)
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<template id="tplDocumentoItem">
    <div class="doc-item-card" data-idx="__IDX__">
        <div class="doc-item-head">
            <span class="badge bg-secondary doc-num">__NUM__</span>
            <div class="doc-file-icon"><i class="fas __ICON__"></i></div>
            <div class="flex-grow-1 min-width-0">
                <div class="fw-semibold text-truncate doc-nombre-archivo">__NOMBRE__</div>
                <div class="doc-item-meta">__META__</div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger btn-quitar-doc" title="Quitar">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="doc-item-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tipo <span class="text-danger">*</span></label>
                    <select class="form-select" name="documentos[__IDX__][tipo_documento]" required>
                        <option value="pauta_nutricional">Pauta Nutricional</option>
                        <option value="receta">Receta</option>
                        <option value="informe">Informe</option>
                        <option value="consentimiento">Consentimiento</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Título <span class="text-danger">*</span></label>
                    <input type="text" class="form-control doc-titulo" name="documentos[__IDX__][titulo]" required maxlength="200" value="__TITULO__">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha del documento <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="documentos[__IDX__][fecha_documento]" required value="__FECHA__">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha de vencimiento</label>
                    <input type="date" class="form-control" name="documentos[__IDX__][fecha_vencimiento]">
                </div>
                <div class="col-12">
                    <label class="form-label">Descripción</label>
                    <textarea class="form-control" name="documentos[__IDX__][descripcion]" rows="2" placeholder="Opcional"></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Contenido</label>
                    <textarea class="form-control" name="documentos[__IDX__][contenido]" rows="3" placeholder="Texto del documento (opcional si hay archivo adjunto)"></textarea>
                </div>
            </div>
            __FILE_INPUT__
        </div>
    </div>
</template>

<script>
(function() {
    var MAX_DOCS = 25;
    var MAX_BYTES = 5 * 1024 * 1024;
    var items = [];
    var nextIdx = 0;
    var hoy = '<?= date('Y-m-d') ?>';

    var $paciente = document.getElementById('paciente_id');
    var $dropzone = document.getElementById('dropzone');
    var $inputArchivos = document.getElementById('inputArchivos');
    var $lista = document.getElementById('listaDocumentos');
    var $seccion = document.getElementById('seccionDetalles');
    var $btnGuardar = document.getElementById('btnGuardarLote');
    var $btnManual = document.getElementById('btnAgregarManual');
    var $btnLimpiar = document.getElementById('btnLimpiarLista');
    var $form = document.getElementById('formDocumentosLote');
    var tpl = document.getElementById('tplDocumentoItem').innerHTML;

    function pacienteOk() {
        return $paciente.value && parseInt($paciente.value, 10) > 0;
    }

    function tituloDesdeNombre(name) {
        var base = name.replace(/\.[^/.]+$/, '');
        base = base.replace(/[_-]+/g, ' ').replace(/\s+/g, ' ').trim();
        if (base.length > 200) base = base.substring(0, 200);
        return base || 'Documento';
    }

    function iconoArchivo(name) {
        var ext = (name.split('.').pop() || '').toLowerCase();
        if (ext === 'pdf') return 'fa-file-pdf';
        if (['jpg', 'jpeg', 'png', 'webp'].indexOf(ext) >= 0) return 'fa-file-image';
        return 'fa-file-alt';
    }

    function formatoBytes(n) {
        if (n < 1024) return n + ' B';
        if (n < 1024 * 1024) return (n / 1024).toFixed(1) + ' KB';
        return (n / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function actualizarUI() {
        var n = items.length;
        document.getElementById('badgeCount').textContent = n;
        document.getElementById('btnCount').textContent = n;
        $seccion.style.display = n > 0 ? 'block' : 'none';
        $btnGuardar.disabled = !pacienteOk() || n === 0;
        $btnLimpiar.classList.toggle('d-none', n === 0);
        document.getElementById('estadoVacio').classList.toggle('d-none', n > 0);
        var resumen = document.getElementById('resumenToolbar');
        if (!pacienteOk()) {
            resumen.textContent = 'Seleccioná un paciente para continuar.';
        } else if (n === 0) {
            resumen.textContent = 'Agregá archivos o un documento sin adjunto.';
        } else {
            resumen.textContent = n + ' documento(s) listo(s) para guardar.';
        }
    }

    function agregarItem(file) {
        if (items.length >= MAX_DOCS) {
            alert('Máximo ' + MAX_DOCS + ' documentos por carga.');
            return;
        }
        if (file && file.size > MAX_BYTES) {
            alert('«' + file.name + '» supera 5 MB.');
            return;
        }

        var idx = nextIdx++;
        var nombre = file ? file.name : 'Sin archivo adjunto';
        var meta = file ? formatoBytes(file.size) : 'Solo texto / descripción';
        var titulo = tituloDesdeNombre(nombre);
        var icon = file ? iconoArchivo(file.name) : 'fa-file-alt';

        var fileInputHtml = '';
        if (file) {
            fileInputHtml = '<input type="file" name="archivo_' + idx + '" class="d-none doc-file-input" tabindex="-1">';
        }

        var html = tpl
            .replace(/__IDX__/g, idx)
            .replace(/__NUM__/g, String(items.length + 1))
            .replace(/__NOMBRE__/g, nombre.replace(/</g, '&lt;'))
            .replace(/__TITULO__/g, titulo.replace(/"/g, '&quot;'))
            .replace(/__FECHA__/g, hoy)
            .replace(/__ICON__/g, icon)
            .replace(/__META__/g, meta)
            .replace('__FILE_INPUT__', fileInputHtml);

        var wrap = document.createElement('div');
        wrap.innerHTML = html;
        var card = wrap.firstElementChild;
        $lista.appendChild(card);

        if (file) {
            var fi = card.querySelector('.doc-file-input');
            var dt = new DataTransfer();
            dt.items.add(file);
            fi.files = dt.files;
        }

        card.querySelector('.btn-quitar-doc').addEventListener('click', function() {
            card.remove();
            items = items.filter(function(it) { return it.idx !== idx; });
            actualizarUI();
        });

        items.push({ idx: idx, file: file || null });
        actualizarUI();
    }

    function procesarArchivos(fileList) {
        if (!pacienteOk()) {
            alert('Seleccioná primero un paciente.');
            return;
        }
        Array.prototype.forEach.call(fileList, function(f) {
            agregarItem(f);
        });
    }

    $paciente.addEventListener('change', function() {
        var ok = pacienteOk();
        $dropzone.classList.toggle('disabled', !ok);
        $btnManual.disabled = !ok;
        actualizarUI();
    });

    $dropzone.addEventListener('click', function() {
        if (pacienteOk()) $inputArchivos.click();
    });
    $dropzone.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            if (pacienteOk()) $inputArchivos.click();
        }
    });
    $dropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
        if (pacienteOk()) $dropzone.classList.add('dragover');
    });
    $dropzone.addEventListener('dragleave', function() {
        $dropzone.classList.remove('dragover');
    });
    $dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        $dropzone.classList.remove('dragover');
        if (e.dataTransfer.files.length) procesarArchivos(e.dataTransfer.files);
    });
    $inputArchivos.addEventListener('change', function() {
        if (this.files.length) procesarArchivos(this.files);
        this.value = '';
    });

    $btnManual.addEventListener('click', function() {
        if (pacienteOk()) agregarItem(null);
    });

    $btnLimpiar.addEventListener('click', function() {
        if (!items.length || !confirm('¿Quitar todos los documentos de la lista?')) return;
        $lista.innerHTML = '';
        items = [];
        actualizarUI();
    });

    $form.addEventListener('submit', function(e) {
        if (!pacienteOk() || items.length === 0) {
            e.preventDefault();
            alert('Seleccioná un paciente y agregá al menos un documento.');
            return;
        }
        var cards = $lista.querySelectorAll('.doc-item-card');
        var invalid = false;
        cards.forEach(function(card) {
            card.querySelectorAll('[required]').forEach(function(el) {
                if (!el.value.trim()) invalid = true;
            });
        });
        if (invalid) {
            e.preventDefault();
            alert('Completá tipo, título y fecha en todos los documentos.');
        }
    });

    actualizarUI();
})();
</script>

<?= $this->endSection() ?>

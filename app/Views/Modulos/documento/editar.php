<?= $this->extend('layout/dashboard') ?>

<?php
$d = $documento;
$tipos = [
    'pauta_nutricional' => 'Pauta Nutricional',
    'receta' => 'Receta',
    'informe' => 'Informe',
    'consentimiento' => 'Consentimiento',
    'otro' => 'Otro',
];
?>

<?= $this->section('documento/editar') ?>

<style>
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #4facfe;
    }
    .btn-submit {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        border: none;
        padding: 12px 35px;
        font-size: 1.05rem;
        font-weight: 600;
        border-radius: 8px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-edit me-2"></i> Editar Documento</h2>
                        <p style="color: white;"><?= esc($d->titulo) ?></p>
                    </div>
                    <a href="<?= base_url('dashboard/documento/lista') ?>" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i> Volver
                    </a>
                </div>
            </div>

            <form action="<?= base_url('dashboard/documento/update') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $d->id ?>">

                <div class="section-card">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Paciente <span class="text-danger">*</span></label>
                                <select name="paciente_id" class="form-control" required>
                                    <?php foreach ($pacientes as $paciente) : ?>
                                        <option value="<?= $paciente->id ?>" <?= (int) $paciente->id === (int) $d->paciente_id ? 'selected' : '' ?>>
                                            <?= esc($paciente->nombre_completo ?? ($paciente->nombre . ' ' . $paciente->apellido)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipo de Documento <span class="text-danger">*</span></label>
                                <select name="tipo_documento" class="form-control" required>
                                    <?php foreach ($tipos as $val => $label) : ?>
                                        <option value="<?= $val ?>" <?= $d->tipo_documento === $val ? 'selected' : '' ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="form-control" required value="<?= esc($d->titulo) ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fecha del Documento <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_documento" class="form-control" required value="<?= esc(substr((string) $d->fecha_documento, 0, 10)) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fecha de Vencimiento</label>
                                <input type="date" name="fecha_vencimiento" class="form-control" value="<?= $d->fecha_vencimiento ? esc(substr((string) $d->fecha_vencimiento, 0, 10)) : '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"><?= esc($d->descripcion ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Contenido</label>
                        <textarea name="contenido" class="form-control" rows="8"><?= esc($d->contenido ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Reemplazar archivo adjunto</label>
                        <?php if (!empty($d->archivo_nombre)) : ?>
                            <p class="text-muted small mb-1">Actual: <strong><?= esc($d->archivo_nombre) ?></strong>
                                — <a href="<?= base_url('dashboard/documento/' . $d->id . '/descargar') ?>" target="_blank">Descargar</a>
                            </p>
                        <?php endif; ?>
                        <input type="file" name="archivo" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                        <small class="text-muted">Deje vacío para conservar el archivo actual. Máx. 5 MB.</small>
                    </div>
                </div>

                <div class="text-center mt-4 mb-4">
                    <button type="submit" class="btn btn-submit"><i class="fas fa-save me-2"></i> Guardar cambios</button>
                    <a href="<?= base_url('dashboard/documento/detalle/' . $d->id) ?>" class="btn btn-secondary">Ver detalle</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

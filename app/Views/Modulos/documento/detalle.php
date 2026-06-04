<?= $this->extend('layout/dashboard') ?>

<?php
$d = $documento;
$p = $d->paciente ?? null;
$tipos = [
    'pauta_nutricional' => 'Pauta Nutricional',
    'receta' => 'Receta',
    'informe' => 'Informe',
    'consentimiento' => 'Consentimiento',
    'otro' => 'Otro',
];
$tipoLabel = $tipos[$d->tipo_documento] ?? 'Documento';
?>

<?= $this->section('documento/detalle') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-file-medical me-2"></i> <?= esc($d->titulo) ?></h2>
                        <p style="color: white;"><?= esc($tipoLabel) ?> · <?= esc(date('d/m/Y', strtotime($d->fecha_documento))) ?></p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('dashboard/documento/editar/' . $d->id) ?>" class="btn btn-light"><i class="fas fa-edit me-1"></i> Editar</a>
                        <a href="<?= base_url('dashboard/documento/lista') ?>" class="btn btn-outline-light"><i class="fas fa-arrow-left me-1"></i> Lista</a>
                    </div>
                </div>
            </div>

            <div class="section-card" style="border-left-color: #4facfe;">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Paciente</strong><br>
                        <?php if ($p) : ?>
                            <?= esc(trim(($p->nombre ?? '') . ' ' . ($p->apellido ?? ''))) ?>
                            <?php if (!empty($p->email)) : ?>
                                <br><small class="text-muted"><?= esc($p->email) ?></small>
                            <?php endif; ?>
                        <?php else : ?>
                            —
                        <?php endif; ?>
                    </div>
                    <div class="col-md-3">
                        <strong>Enviado</strong><br>
                        <?= $d->enviado ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-warning">Pendiente</span>' ?>
                        <?php if ($d->fecha_envio) : ?>
                            <br><small><?= esc(date('d/m/Y H:i', strtotime($d->fecha_envio))) ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-3">
                        <strong>Estado</strong><br>
                        <?= $d->estado === 'A' ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?>
                    </div>
                </div>

                <?php if (!empty($d->descripcion)) : ?>
                    <p><strong>Descripción</strong><br><?= nl2br(esc($d->descripcion)) ?></p>
                <?php endif; ?>

                <?php if (!empty($d->contenido)) : ?>
                    <div class="border rounded p-3 bg-light mb-3" style="white-space: pre-wrap;"><?= esc($d->contenido) ?></div>
                <?php endif; ?>

                <div class="border rounded p-3 mb-3 bg-light">
                    <label for="mensajeEnvioDetalle" class="form-label mb-1"><i class="fas fa-comment-dots me-1"></i> Mensaje para el paciente <span class="text-muted">(opcional)</span></label>
                    <textarea id="mensajeEnvioDetalle" class="form-control" rows="3" maxlength="2000" placeholder="Mensaje que verá el paciente al inicio del correo..."></textarea>
                </div>

                <?php if (!empty($d->archivo_ruta)) : ?>
                    <a class="btn btn-primary" href="<?= base_url('dashboard/documento/' . $d->id . '/descargar') ?>" target="_blank">
                        <i class="fas fa-download me-1"></i> Descargar <?= esc($d->archivo_nombre ?? 'archivo') ?>
                    </a>
                <?php endif; ?>
                <button type="button" class="btn btn-success" onclick="enviarDocumento(<?= (int) $d->id ?>)">
                    <i class="fas fa-envelope me-1"></i> Enviar por correo al paciente
                </button>
            </div>

            <?php if (!empty($historialEnvios)) : ?>
            <div class="section-card mt-3" style="border-left-color: #667eea;">
                <h5 class="mb-3"><i class="fas fa-history me-2"></i> Historial de envíos por correo</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Destino</th>
                                <th>Enviado por</th>
                                <th>Estado</th>
                                <th>Mensaje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historialEnvios as $envio) : ?>
                            <tr>
                                <td><?= esc(date('d/m/Y H:i', strtotime($envio->fcreacion))) ?></td>
                                <td><?= esc($envio->email_destino) ?></td>
                                <td><?= esc(trim(($envio->nutricionista_nombre ?? '') . ' ' . ($envio->nutricionista_apellido ?? ''))) ?: '—' ?></td>
                                <td>
                                    <?php if (($envio->estado ?? '') === 'enviado') : ?>
                                        <span class="badge bg-success">Enviado</span>
                                    <?php else : ?>
                                        <span class="badge bg-danger" title="<?= esc($envio->error_detalle ?? '') ?>">Fallido</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small"><?= !empty($envio->mensaje_personal) ? nl2br(esc($envio->mensaje_personal)) : '<span class="text-muted">—</span>' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php elseif ($d->enviado && empty($historialEnvios)) : ?>
            <div class="section-card mt-3" style="border-left-color: #667eea;">
                <p class="text-muted small mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Marcado como enviado el <?= esc(date('d/m/Y H:i', strtotime($d->fecha_envio))) ?>
                    (<?= esc($d->metodo_envio ?? 'sistema') ?>).
                    El historial detallado estará disponible tras ejecutar la migración de envíos.
                </p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function enviarDocumento(id) {
    var mensaje = document.getElementById('mensajeEnvioDetalle').value.trim();
    mostrarModalConfirmarAccion({
        titulo: '<i class="fas fa-envelope text-success me-2"></i> Enviar por correo',
        mensaje: '¿Enviar este documento por correo al paciente?',
        textoBtn: 'Enviar',
        claseBtn: 'btn-success',
        iconoBtn: 'fas fa-paper-plane',
        onConfirm: function() {
            var fd = new FormData();
            fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            fd.append('documento_ids[]', id);
            if (mensaje) {
                fd.append('mensaje_personal', mensaje);
            }
            fetch('<?= base_url('dashboard/documento/enviar-correo') ?>', {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    mostrarModalExito(data.message);
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    mostrarModalError(data.error || 'Error al enviar');
                }
            })
            .catch(function() { mostrarModalError('Error de conexión'); });
        }
    });
}
</script>

<?= $this->endSection() ?>

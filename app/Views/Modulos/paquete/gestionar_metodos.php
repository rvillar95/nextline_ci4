<?= $this->extend('layout/dashboard') ?>

<?= $this->section('paquete/gestionar_metodos') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calculator"></i> Métodos de composición: <?= esc($paquete->nombre) ?>
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/paquete/lista') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <a href="<?= base_url('dashboard/paquete/gestionar-modulos/' . $paquete->id) ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-cogs"></i> Módulos
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div id="mensajeResultado" style="display:none;"></div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Define qué métodos de cálculo corporal están incluidos en este paquete.
                        Los usuarios sin acceso verán el método bloqueado o podrán contratarlo como add-on.
                    </div>

                    <form id="formMetodos" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="paquete_id" value="<?= (int) $paquete->id ?>">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-sm btn-success" onclick="document.querySelectorAll('input[name=\'rutas[]\']').forEach(c=>c.checked=true)">
                                    Seleccionar todos
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="document.querySelectorAll('input[name=\'rutas[]\']').forEach(c=>c.checked=false)">
                                    Ninguno
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <?php foreach ($metodos as $metodo): ?>
                                <?php $ruta = '/calcular-' . $metodo->slug; ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="rutas[]"
                                                       value="<?= esc($ruta) ?>" id="metodo_<?= (int) $metodo->id ?>"
                                                       <?= in_array($ruta, $rutasAsignadas ?? [], true) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="metodo_<?= (int) $metodo->id ?>">
                                                    <strong><?= esc($metodo->nombre) ?></strong>
                                                    <br><small class="text-muted"><?= (int) $metodo->componentes ?> componentes</small>
                                                    <?php if (($metodo->es_addon ?? 'N') === 'S' && (float) $metodo->precio_mensual > 0): ?>
                                                        <br><small class="text-success">Add-on catálogo: $<?= number_format((float) $metodo->precio_mensual, 0, ',', '.') ?>/mes</small>
                                                    <?php else: ?>
                                                        <br><small class="text-info">Incluido en base</small>
                                                    <?php endif; ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar métodos
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(function($) {
    $('#formMetodos').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '<?= base_url('dashboard/paquete/guardar-metodos') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.csrf_token) {
                    $('input[name="<?= csrf_token() ?>"]').val(res.csrf_token);
                }
                const ok = res.success;
                $('#mensajeResultado').html(
                    '<div class="alert alert-' + (ok ? 'success' : 'danger') + '">' +
                    (res.message || res.error || '') + '</div>'
                ).show();
            },
            error: function(xhr) {
                let msg = 'Error al guardar';
                try { msg = xhr.responseJSON.error || msg; } catch (e) {}
                $('#mensajeResultado').html('<div class="alert alert-danger">' + msg + '</div>').show();
            }
        });
    });
});
</script>

<?= $this->endSection() ?>

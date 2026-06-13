<?= $this->extend('layout/abopech') ?>
<?= $this->section('content') ?>
<h1 class="h4 text-abopech mb-3">Contactar a <?= esc($abogado['nombres'] . ' ' . $abogado['apellidos']) ?></h1>
<form method="post" action="<?= base_url('abopech/abogado/' . (int) $abogado['id'] . '/contactar') ?>" id="form-contacto">
    <?= csrf_field() ?>
    <input type="text" name="company" value="" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Nombre</label><input type="text" name="nombre" class="form-control" required value="<?= esc(old('nombre')) ?>"></div>
        <div class="col-md-6"><label class="form-label">Apellido</label><input type="text" name="apellido" class="form-control" required value="<?= esc(old('apellido')) ?>"></div>
        <div class="col-md-6"><label class="form-label">Correo</label><input type="email" name="correo" class="form-control" required value="<?= esc(old('correo')) ?>"></div>
        <div class="col-md-6"><label class="form-label">Teléfono</label><input type="tel" name="telefono" class="form-control" required value="<?= esc(old('telefono')) ?>"></div>
        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="consentimiento" value="1" id="consent" required>
                <label class="form-check-label" for="consent">Autorizo el tratamiento de mis datos para ser contactado por el abogado.</label>
            </div>
        </div>
        <?php if (!empty($recaptcha_enabled)): ?>
            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
        <?php endif; ?>
        <div class="col-12"><button type="submit" class="btn btn-abopech">Continuar</button></div>
    </div>
</form>
<?= $this->endSection() ?>
<?php if (!empty($recaptcha_enabled)): ?>
<?= $this->section('scripts') ?>
<script src="https://www.google.com/recaptcha/api.js?render=<?= esc($recaptcha_site_key) ?>"></script>
<script>
grecaptcha.ready(function () {
    document.getElementById('form-contacto').addEventListener('submit', function (e) {
        e.preventDefault();
        grecaptcha.execute('<?= esc($recaptcha_site_key) ?>', {action: 'abopech_contacto'}).then(function (token) {
            document.getElementById('g-recaptcha-response').value = token;
            e.target.submit();
        });
    });
});
</script>
<?= $this->endSection() ?>
<?php endif; ?>

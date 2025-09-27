<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>
<div class="container py-4 page-content">
  <h1>Contáctanos</h1>

  <?php if ($msg = session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc($msg) ?></div>
  <?php endif; ?>
  <?php if ($err = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
      <?php if (is_array($err)): ?>
        <ul class="mb-0"><?php foreach ($err as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
      <?php else: ?><?= esc($err) ?><?php endif; ?>
    </div>
  <?php endif; ?>

  <form method="post" action="<?= base_url('contacto/enviar') ?>" class="row g-3">
    <?= csrf_field() ?>
    <!-- honeypot anti-bot -->
    <input type="text" name="company" value="" style="position:absolute;left:-9999px;height:1px;width:1px;opacity:0;" tabindex="-1" autocomplete="off">

    <div class="col-md-6">
      <label class="form-label">Nombre</label>
      <input type="text" name="nombre" class="form-control" required value="<?= old('nombre') ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Correo</label>
      <input type="email" name="correo" class="form-control" required value="<?= old('correo') ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Teléfono</label>
      <input type="text" name="telefono" class="form-control" value="<?= old('telefono') ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Servicio de interés *</label>
      <select name="servicio_id" class="form-select" required>
        <option value="">Seleccione...</option>
        <?php if (!empty($servicios)): foreach ($servicios as $s): ?>
          <option value="<?= (int)$s['id'] ?>" <?= old('servicio_id') == $s['id'] ? 'selected' : '' ?>>
            <?= esc($s['nombre']) ?>
          </option>
        <?php endforeach; endif; ?>
      </select>
      <small class="text-muted">Selecciona el servicio que más te interesa (obligatorio)</small>
    </div>
    <div class="col-12">
      <label class="form-label">Mensaje</label>
      <textarea name="mensaje" class="form-control" rows="5"><?= old('mensaje') ?></textarea>
    </div>
    <div class="col-12">
      <button class="btn btn-primary">Enviar</button>
    </div>
  </form>
</div>
<?= $this->endSection() ?>

<?= $this->extend('layout/dashboard') ?>

<?= $this->section('testimonio/registro') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-quote-left"></i> Registrar Nuevo Testimonio
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/testimonio/lista') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('dashboard/testimonio/registrar') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nombre del Cliente *</label>
                                    <input type="text" name="nombre" class="form-control" 
                                           value="<?= old('nombre') ?>" required>
                                    <?php if (session()->getFlashdata('errors')['nombre'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['nombre']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Cargo / Profesión</label>
                                    <input type="text" name="cargo" class="form-control" 
                                           value="<?= old('cargo') ?>" placeholder="Ej: Propietario, Arquitecto, Ingeniero, Particular...">
                                    <small class="text-muted">Dejar vacío si es un particular</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Empresa / Organización</label>
                                    <input type="text" name="empresa" class="form-control" 
                                           value="<?= old('empresa') ?>" placeholder="Ej: Casa Residencial, Empresa S.A., Particular...">
                                    <small class="text-muted">Dejar vacío si es un particular</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Calificación *</label>
                                    <select name="calificacion" class="form-control" required>
                                        <option value="">Seleccione calificación...</option>
                                        <option value="1" <?= old('calificacion') == '1' ? 'selected' : '' ?>>1 Estrella</option>
                                        <option value="2" <?= old('calificacion') == '2' ? 'selected' : '' ?>>2 Estrellas</option>
                                        <option value="3" <?= old('calificacion') == '3' ? 'selected' : '' ?>>3 Estrellas</option>
                                        <option value="4" <?= old('calificacion') == '4' ? 'selected' : '' ?>>4 Estrellas</option>
                                        <option value="5" <?= old('calificacion') == '5' ? 'selected' : '' ?>>5 Estrellas</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Proyecto Relacionado</label>
                                    <select name="proyecto_id" class="form-control">
                                        <option value="">Seleccione proyecto...</option>
                                        <?php if (!empty($proyectos)): foreach ($proyectos as $proyecto): ?>
                                            <option value="<?= $proyecto->id ?>" <?= old('proyecto_id') == $proyecto->id ? 'selected' : '' ?>>
                                                <?= esc($proyecto->nombre) ?>
                                            </option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Servicio Relacionado</label>
                                    <select name="servicio_id" class="form-control">
                                        <option value="">Seleccione servicio...</option>
                                        <?php if (!empty($servicios)): foreach ($servicios as $servicio): ?>
                                            <option value="<?= $servicio->id ?>" <?= old('servicio_id') == $servicio->id ? 'selected' : '' ?>>
                                                <?= esc($servicio->nombre) ?>
                                            </option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Testimonio *</label>
                                    <textarea name="testimonio" class="form-control" rows="5" required><?= old('testimonio') ?></textarea>
                                    <small class="text-muted">Máximo 2000 caracteres</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Fecha del Proyecto</label>
                                    <input type="date" name="fecha_proyecto" class="form-control" 
                                           value="<?= old('fecha_proyecto') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Estado *</label>
                                    <select name="estado" class="form-control" required>
                                        <option value="A" <?= old('estado', 'A') == 'A' ? 'selected' : '' ?>>Activo</option>
                                        <option value="I" <?= old('estado') == 'I' ? 'selected' : '' ?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Destacado *</label>
                                    <select name="destacado" class="form-control" required>
                                        <option value="N" <?= old('destacado', 'N') == 'N' ? 'selected' : '' ?>>Normal</option>
                                        <option value="S" <?= old('destacado') == 'S' ? 'selected' : '' ?>>Destacado</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Espaciado adicional antes de los botones -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Registrar Testimonio
                                    </button>
                                    <a href="<?= base_url('dashboard/testimonio/lista') ?>" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
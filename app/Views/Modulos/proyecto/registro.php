<?= $this->extend('layout/dashboard') ?>

<?= $this->section('proyecto/registro') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-project-diagram"></i> Registrar Nuevo Proyecto
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/proyecto/lista') ?>" class="btn btn-secondary">
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

                    <?= form_open_multipart('dashboard/proyecto/registrar') ?>
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nombre del Proyecto *</label>
                                    <input type="text" name="nombre" class="form-control" 
                                           value="<?= old('nombre') ?>" required>
                                    <?php if (session()->getFlashdata('errors')['nombre'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['nombre']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Cliente</label>
                                    <input type="text" name="cliente" class="form-control" 
                                           value="<?= old('cliente') ?>">
                                    <?php if (session()->getFlashdata('errors')['cliente'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['cliente']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tipo de Proyecto *</label>
                                    <select name="tipo_proyecto" class="form-control" required>
                                        <option value="">Seleccionar tipo</option>
                                        <option value="residencial" <?= old('tipo_proyecto') == 'residencial' ? 'selected' : '' ?>>Residencial</option>
                                        <option value="comercial" <?= old('tipo_proyecto') == 'comercial' ? 'selected' : '' ?>>Comercial</option>
                                        <option value="industrial" <?= old('tipo_proyecto') == 'industrial' ? 'selected' : '' ?>>Industrial</option>
                                        <option value="institucional" <?= old('tipo_proyecto') == 'institucional' ? 'selected' : '' ?>>Institucional</option>
                                        <option value="infraestructura" <?= old('tipo_proyecto') == 'infraestructura' ? 'selected' : '' ?>>Infraestructura</option>
                                        <option value="remodelacion" <?= old('tipo_proyecto') == 'remodelacion' ? 'selected' : '' ?>>Remodelación</option>
                                        <option value="ampliacion" <?= old('tipo_proyecto') == 'ampliacion' ? 'selected' : '' ?>>Ampliación</option>
                                        <option value="mantenimiento" <?= old('tipo_proyecto') == 'mantenimiento' ? 'selected' : '' ?>>Mantenimiento</option>
                                        <option value="reparacion" <?= old('tipo_proyecto') == 'reparacion' ? 'selected' : '' ?>>Reparación</option>
                                        <option value="otros" <?= old('tipo_proyecto') == 'otros' ? 'selected' : '' ?>>Otros</option>
                                    </select>
                                    <?php if (session()->getFlashdata('errors')['tipo_proyecto'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['tipo_proyecto']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Estado del Proyecto *</label>
                                    <select name="estado" class="form-control" required>
                                        <option value="">Seleccionar estado</option>
                                        <option value="en_progreso" <?= old('estado') == 'en_progreso' ? 'selected' : '' ?>>En Progreso</option>
                                        <option value="completado" <?= old('estado') == 'completado' ? 'selected' : '' ?>>Completado</option>
                                        <option value="en_pausa" <?= old('estado') == 'en_pausa' ? 'selected' : '' ?>>En Pausa</option>
                                        <option value="cancelado" <?= old('estado') == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                    </select>
                                    <?php if (session()->getFlashdata('errors')['estado'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['estado']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Fecha de Inicio</label>
                                    <input type="date" name="fecha_inicio" class="form-control" 
                                           value="<?= old('fecha_inicio') ?>">
                                    <?php if (session()->getFlashdata('errors')['fecha_inicio'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['fecha_inicio']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Fecha de Finalización</label>
                                    <input type="date" name="fecha_finalizacion" class="form-control" 
                                           value="<?= old('fecha_finalizacion') ?>">
                                    <?php if (session()->getFlashdata('errors')['fecha_finalizacion'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['fecha_finalizacion']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Ubicación</label>
                                    <input type="text" name="ubicacion" class="form-control" 
                                           value="<?= old('ubicacion') ?>" placeholder="Dirección del proyecto">
                                    <?php if (session()->getFlashdata('errors')['ubicacion'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['ubicacion']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Presupuesto</label>
                                    <input type="number" name="presupuesto" class="form-control" 
                                           value="<?= old('presupuesto') ?>" step="0.01" placeholder="0.00">
                                    <small class="text-muted">Presupuesto estimado del proyecto</small>
                                    <?php if (session()->getFlashdata('errors')['presupuesto'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['presupuesto']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mostrar Presupuesto</label>
                                    <select name="mostrar_presupuesto" class="form-control">
                                        <option value="0" <?= old('mostrar_presupuesto', '0') == '0' ? 'selected' : '' ?>>No</option>
                                        <option value="1" <?= old('mostrar_presupuesto') == '1' ? 'selected' : '' ?>>Sí</option>
                                    </select>
                                    <small class="text-muted">Determina si el presupuesto se muestra públicamente</small>
                                    <?php if (session()->getFlashdata('errors')['mostrar_presupuesto'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['mostrar_presupuesto']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Proyecto Destacado</label>
                                    <select name="destacado" class="form-control">
                                        <option value="0" <?= old('destacado', '0') == '0' ? 'selected' : '' ?>>No</option>
                                        <option value="1" <?= old('destacado') == '1' ? 'selected' : '' ?>>Sí</option>
                                    </select>
                                    <small class="text-muted">Los proyectos destacados aparecen en la página principal</small>
                                    <?php if (session()->getFlashdata('errors')['destacado'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['destacado']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Descripción del Proyecto</label>
                                    <textarea name="descripcion_detallada" class="form-control" rows="4" 
                                              placeholder="Descripción detallada del proyecto..."><?= old('descripcion_detallada') ?></textarea>
                                    <?php if (session()->getFlashdata('errors')['descripcion_detallada'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['descripcion_detallada']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Imágenes del Proyecto</label>
                                    <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
                                    <small class="text-muted">Puedes seleccionar múltiples imágenes del proyecto (JPG, PNG, GIF - máximo 5MB cada una)</small>
                                    <?php if (session()->getFlashdata('errors')['imagenes'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['imagenes']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>


                        <!-- Espaciado adicional antes de los botones -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Registrar Proyecto
                                    </button>
                                    <a href="<?= base_url('dashboard/proyecto/lista') ?>" class="btn btn-secondary">
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
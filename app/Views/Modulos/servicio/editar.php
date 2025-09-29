<?= $this->extend('layout/dashboard') ?>

<?= $this->section('servicio/editar') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Editar Servicio
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/servicio/lista') ?>" class="btn btn-secondary">
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

                    <?= form_open_multipart('dashboard/servicio/update') ?>
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $servicio->id ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nombre del Servicio *</label>
                                    <input type="text" name="nombre" class="form-control" 
                                           value="<?= old('nombre', $servicio->nombre) ?>" required>
                                    <?php if (session()->getFlashdata('errors')['nombre'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['nombre']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Categoría *</label>
                                    <select name="categoria_id" class="form-control" required>
                                        <option value="">Seleccionar categoría</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= $categoria->id ?>" <?= old('categoria_id', $servicio->categoria_id) == $categoria->id ? 'selected' : '' ?>>
                                                <?= esc($categoria->nombre) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (session()->getFlashdata('errors')['categoria_id'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['categoria_id']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Descripción Corta *</label>
                                    <input type="text" name="descripcionCorta" class="form-control" 
                                           value="<?= old('descripcionCorta', $servicio->descripcionCorta) ?>" required>
                                    <small class="text-muted">Descripción breve que aparecerá en las tarjetas de servicios (máximo 500 caracteres)</small>
                                    <?php if (session()->getFlashdata('errors')['descripcionCorta'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['descripcionCorta']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Descripción Larga *</label>
                                    <textarea name="descripcionLarga" class="form-control" rows="5" required><?= old('descripcionLarga', $servicio->descripcionLarga) ?></textarea>
                                    <small class="text-muted">Descripción detallada del servicio (máximo 2000 caracteres)</small>
                                    <?php if (session()->getFlashdata('errors')['descripcionLarga'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['descripcionLarga']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Características</label>
                                    <textarea name="caracteristicas" class="form-control" rows="3" 
                                              placeholder="Una característica por línea"><?= old('caracteristicas', $servicio->caracteristicas) ?></textarea>
                                    <small class="text-muted">Lista las principales características del servicio (una por línea)</small>
                                    <?php if (session()->getFlashdata('errors')['caracteristicas'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['caracteristicas']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Beneficios</label>
                                    <textarea name="beneficios" class="form-control" rows="3" 
                                              placeholder="Un beneficio por línea"><?= old('beneficios', $servicio->beneficios) ?></textarea>
                                    <small class="text-muted">Lista los beneficios que ofrece este servicio (uno por línea)</small>
                                    <?php if (session()->getFlashdata('errors')['beneficios'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['beneficios']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Tiempo Estimado</label>
                                    <input type="text" name="tiempo_estimado" class="form-control" 
                                           value="<?= old('tiempo_estimado', $servicio->tiempo_estimado) ?>" placeholder="Ej: 30 días, 2-3 meses">
                                    <?php if (session()->getFlashdata('errors')['tiempo_estimado'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['tiempo_estimado']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Garantía</label>
                                    <input type="text" name="garantia" class="form-control" 
                                           value="<?= old('garantia', $servicio->garantia) ?>" placeholder="Ej: 1 año, 6 meses">
                                    <?php if (session()->getFlashdata('errors')['garantia'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['garantia']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Orden de Visualización</label>
                                    <input type="number" name="orden" class="form-control" 
                                           value="<?= old('orden', $servicio->orden) ?>" min="0">
                                    <small class="text-muted">Número para ordenar los servicios (menor número = primero)</small>
                                    <?php if (session()->getFlashdata('errors')['orden'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['orden']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Mostrar Precio</label>
                                    <select name="mostrar_precio" class="form-control">
                                        <option value="S" <?= old('mostrar_precio', $servicio->mostrar_precio) == 'S' ? 'selected' : '' ?>>Sí</option>
                                        <option value="N" <?= old('mostrar_precio', $servicio->mostrar_precio) == 'N' ? 'selected' : '' ?>>No</option>
                                    </select>
                                    <small class="text-muted">Si seleccionas "No", aparecerá "Consultar precio"</small>
                                    <?php if (session()->getFlashdata('errors')['mostrar_precio'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['mostrar_precio']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Precio Desde</label>
                                    <input type="number" name="precio_desde" class="form-control" 
                                           value="<?= old('precio_desde', $servicio->precio_desde) ?>" step="0.01" placeholder="0.00">
                                    <small class="text-muted">Precio mínimo del servicio</small>
                                    <?php if (session()->getFlashdata('errors')['precio_desde'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['precio_desde']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Precio Hasta</label>
                                    <input type="number" name="precio_hasta" class="form-control" 
                                           value="<?= old('precio_hasta', $servicio->precio_hasta) ?>" step="0.01" placeholder="0.00">
                                    <small class="text-muted">Precio máximo del servicio</small>
                                    <?php if (session()->getFlashdata('errors')['precio_hasta'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['precio_hasta']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Estado</label>
                                    <select name="estado" class="form-control">
                                        <option value="A" <?= old('estado', $servicio->estado) == 'A' ? 'selected' : '' ?>>Activo</option>
                                        <option value="I" <?= old('estado', $servicio->estado) == 'I' ? 'selected' : '' ?>>Inactivo</option>
                                    </select>
                                    <?php if (session()->getFlashdata('errors')['estado'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['estado']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Servicio Destacado</label>
                                    <select name="destacado" class="form-control">
                                        <option value="N" <?= old('destacado', $servicio->destacado) == 'N' ? 'selected' : '' ?>>No</option>
                                        <option value="S" <?= old('destacado', $servicio->destacado) == 'S' ? 'selected' : '' ?>>Sí</option>
                                    </select>
                                    <small class="text-muted">Los servicios destacados aparecen en la página principal</small>
                                    <?php if (session()->getFlashdata('errors')['destacado'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['destacado']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Imagen del Servicio</label>
                                    <input type="file" name="foto" class="form-control" accept="image/*">
                                    <small class="text-muted">Imagen representativa del servicio (JPG, PNG, GIF - máximo 2MB)</small>
                                    <?php if ($servicio->foto): ?>
                                        <div class="mt-2">
                                            <img src="<?= base_url($servicio->foto) ?>" alt="Imagen actual" class="img-thumbnail" style="max-width: 200px;">
                                            <small class="text-muted d-block">Imagen actual</small>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (session()->getFlashdata('errors')['foto'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['foto']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Espaciado adicional antes de los botones -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Actualizar Servicio
                                    </button>
                                    <a href="<?= base_url('dashboard/servicio/lista') ?>" class="btn btn-secondary">
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
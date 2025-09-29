<?= $this->extend('layout/dashboard') ?>

<?= $this->section('proyecto/editar') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Editar Proyecto
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

                    <?= form_open_multipart('dashboard/proyecto/update') ?>
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $proyecto->id ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nombre del Proyecto *</label>
                                    <input type="text" name="nombre" class="form-control" 
                                           value="<?= old('nombre', $proyecto->nombre) ?>" required>
                                    <?php if (session()->getFlashdata('errors')['nombre'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['nombre']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Cliente</label>
                                    <input type="text" name="cliente" class="form-control" 
                                           value="<?= old('cliente', $proyecto->cliente) ?>">
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
                                        <option value="residencial" <?= old('tipo_proyecto', $proyecto->tipo_proyecto) == 'residencial' ? 'selected' : '' ?>>Residencial</option>
                                        <option value="comercial" <?= old('tipo_proyecto', $proyecto->tipo_proyecto) == 'comercial' ? 'selected' : '' ?>>Comercial</option>
                                        <option value="industrial" <?= old('tipo_proyecto', $proyecto->tipo_proyecto) == 'industrial' ? 'selected' : '' ?>>Industrial</option>
                                        <option value="institucional" <?= old('tipo_proyecto', $proyecto->tipo_proyecto) == 'institucional' ? 'selected' : '' ?>>Institucional</option>
                                        <option value="infraestructura" <?= old('tipo_proyecto', $proyecto->tipo_proyecto) == 'infraestructura' ? 'selected' : '' ?>>Infraestructura</option>
                                        <option value="remodelacion" <?= old('tipo_proyecto', $proyecto->tipo_proyecto) == 'remodelacion' ? 'selected' : '' ?>>Remodelación</option>
                                        <option value="ampliacion" <?= old('tipo_proyecto', $proyecto->tipo_proyecto) == 'ampliacion' ? 'selected' : '' ?>>Ampliación</option>
                                        <option value="mantenimiento" <?= old('tipo_proyecto', $proyecto->tipo_proyecto) == 'mantenimiento' ? 'selected' : '' ?>>Mantenimiento</option>
                                        <option value="reparacion" <?= old('tipo_proyecto', $proyecto->tipo_proyecto) == 'reparacion' ? 'selected' : '' ?>>Reparación</option>
                                        <option value="otros" <?= old('tipo_proyecto', $proyecto->tipo_proyecto) == 'otros' ? 'selected' : '' ?>>Otros</option>
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
                                        <option value="en_progreso" <?= old('estado', $proyecto->estado) == 'en_progreso' ? 'selected' : '' ?>>En Progreso</option>
                                        <option value="completado" <?= old('estado', $proyecto->estado) == 'completado' ? 'selected' : '' ?>>Completado</option>
                                        <option value="en_pausa" <?= old('estado', $proyecto->estado) == 'en_pausa' ? 'selected' : '' ?>>En Pausa</option>
                                        <option value="cancelado" <?= old('estado', $proyecto->estado) == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
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
                                           value="<?= old('fecha_inicio', $proyecto->fecha_inicio) ?>">
                                    <?php if (session()->getFlashdata('errors')['fecha_inicio'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['fecha_inicio']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Fecha de Finalización</label>
                                    <input type="date" name="fecha_finalizacion" class="form-control"
                                           value="<?= old('fecha_finalizacion', $proyecto->fecha_finalizacion) ?>">
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
                                           value="<?= old('ubicacion', $proyecto->ubicacion) ?>" placeholder="Dirección del proyecto">
                                    <?php if (session()->getFlashdata('errors')['ubicacion'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['ubicacion']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Presupuesto</label>
                                    <input type="number" name="presupuesto" class="form-control" 
                                           value="<?= old('presupuesto', $proyecto->presupuesto) ?>" step="0.01" placeholder="0.00">
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
                                        <option value="0" <?= old('mostrar_presupuesto', $proyecto->mostrar_presupuesto ?? 0) == 0 ? 'selected' : '' ?>>No</option>
                                        <option value="1" <?= old('mostrar_presupuesto', $proyecto->mostrar_presupuesto ?? 0) == 1 ? 'selected' : '' ?>>Sí</option>
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
                                        <?php 
                                        $destacadoValue = old('destacado', $proyecto->destacado ?? 0);
                                        // En BD viene como 1 o 0, convertir a S/N para el formulario
                                        $isDestacado = ($destacadoValue == 1 || $destacadoValue == '1');
                                        ?>
                                        <option value="0" <?= !$isDestacado ? 'selected' : '' ?>>No</option>
                                        <option value="1" <?= $isDestacado ? 'selected' : '' ?>>Sí</option>
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
                                              placeholder="Descripción detallada del proyecto..."><?= old('descripcion_detallada', $proyecto->descripcion_detallada) ?></textarea>
                                    <?php if (session()->getFlashdata('errors')['descripcion_detallada'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['descripcion_detallada']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Agregar Más Imágenes</label>
                                    <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
                                    <small class="text-muted">Puedes seleccionar múltiples imágenes para agregar al proyecto (JPG, PNG, GIF - máximo 5MB cada una)</small>
                                    <?php 
                                    // Buscar imagen portada
                                    $imagenPortada = null;
                                    if (!empty($imagenes)) {
                                        foreach ($imagenes as $img) {
                                            if (isset($img->es_portada) && $img->es_portada == 1) {
                                                $imagenPortada = $img;
                                                break;
                                            }
                                        }
                                        // Si no hay imagen portada, tomar la primera
                                        if (!$imagenPortada && !empty($imagenes)) {
                                            $imagenPortada = $imagenes[0];
                                        }
                                    }
                                    ?>
                                    <?php if (session()->getFlashdata('errors')['imagen_principal'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['imagen_principal']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>


                        <!-- Gestión de Imágenes del Proyecto -->
                        <?php if (!empty($imagenes)): ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Imágenes del Proyecto</label>
                                    <div class="row">
                                        <?php foreach ($imagenes as $index => $img): ?>
                                        <div class="col-md-3 mb-3">
                                            <div class="card">
                                                <img src="<?= base_url($img->ruta) ?>" class="card-img-top" style="height: 150px; object-fit: cover;" alt="Imagen <?= $index + 1 ?>">
                                                <div class="card-body p-2">
                                                    <div class="d-flex justify-content-between">
                                                        <?php if ($img->es_portada == 1): ?>
                                                            <span class="badge badge-success">Portada</span>
                                                        <?php else: ?>
                                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="setPortada(<?= $img->id ?>, <?= $proyecto->id ?>)">
                                                                <i class="fas fa-star"></i> Portada
                                                            </button>
                                                        <?php endif; ?>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarImagen(<?= $img->id ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Espaciado adicional antes de los botones -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Actualizar Proyecto
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

<!-- Modal de Confirmación para Establecer Portada -->
<div class="modal fade" id="modalConfirmarPortada" tabindex="-1" aria-labelledby="modalConfirmarPortadaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarPortadaLabel">Confirmar Portada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas establecer esta imagen como portada?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnConfirmarPortada">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Eliminar Imagen -->
<div class="modal fade" id="modalConfirmarEliminarImagen" tabindex="-1" aria-labelledby="modalConfirmarEliminarImagenLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarEliminarImagenLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar esta imagen? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminarImagen">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Información -->
<div class="modal fade" id="modalInformacion" tabindex="-1" aria-labelledby="modalInformacionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalInformacionLabel">Información</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalInformacionBody">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<script>
let imagenIdActual = null;
let proyectoIdActual = null;

function setPortada(imagenId, proyectoId) {
    imagenIdActual = imagenId;
    proyectoIdActual = proyectoId;
    $('#modalConfirmarPortada').modal('show');
}

function eliminarImagen(imagenId) {
    imagenIdActual = imagenId;
    $('#modalConfirmarEliminarImagen').modal('show');
}

function mostrarModalInformacion(titulo, mensaje) {
    $('#modalInformacionLabel').text(titulo);
    $('#modalInformacionBody').html('<p>' + mensaje + '</p>');
    $('#modalInformacion').modal('show');
}

// Confirmar establecer portada
$('#btnConfirmarPortada').on('click', function() {
    $.ajax({
        url: '<?= base_url('dashboard/proyecto/setPortada') ?>',
        type: 'POST',
        data: {
            imagen_id: imagenIdActual,
            proyecto_id: proyectoIdActual,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            $('#modalConfirmarPortada').modal('hide');
            if (response.success) {
                mostrarModalInformacion('Éxito', 'Imagen establecida como portada correctamente');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                mostrarModalInformacion('Error', response.message);
            }
        },
        error: function() {
            $('#modalConfirmarPortada').modal('hide');
            mostrarModalInformacion('Error', 'Error al establecer la imagen como portada');
        }
    });
});

// Confirmar eliminar imagen
$('#btnConfirmarEliminarImagen').on('click', function() {
    $.ajax({
        url: '<?= base_url('dashboard/proyecto/eliminarImagen') ?>',
        type: 'POST',
        data: {
            imagen_id: imagenIdActual,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            $('#modalConfirmarEliminarImagen').modal('hide');
            if (response.success) {
                mostrarModalInformacion('Éxito', 'Imagen eliminada correctamente');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                mostrarModalInformacion('Error', response.message);
            }
        },
        error: function() {
            $('#modalConfirmarEliminarImagen').modal('hide');
            mostrarModalInformacion('Error', 'Error al eliminar la imagen');
        }
    });
});
</script>

<?= $this->endSection() ?>
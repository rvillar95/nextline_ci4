<?php $this->extend('layout/dashboard') ?>

<?= $this->section("proyecto/editar") ?>

<div id="basic" class="col-lg-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <div class="mb-3">
                        <h4>Editar Proyecto</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">

            <div class="row">
                <div class="col-lg-12 col-12 ">

                    <?php if (session()->getFlashdata('success')) : ?>
                        <div class="alert alert-success" role="alert">
                            <?= session()->getFlashdata('success'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('errors')) : ?>
                        <div class="alert alert-danger" role="alert">
                            <?php if (is_array(session()->getFlashdata('errors'))) : ?>
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else : ?>
                                <?= session()->getFlashdata('errors'); ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                    <?= form_open_multipart('dashboard/proyecto/update') ?>
                        <?= csrf_field(); ?>
                        <input type="hidden" name="id" value="<?= $proyecto->id ?>">
                        
                        <!-- Información Básica -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombre del Proyecto *</label>
                                    <input type="text" id="nombre" name="nombre" value="<?= set_value('nombre', $proyecto->nombre); ?>" class="form-control" autofocus required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Cliente</label>
                                    <input type="text" id="cliente" name="cliente" value="<?= set_value('cliente', $proyecto->cliente); ?>" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tipo de Proyecto *</label>
                                    <select class="form-select" id="tipo_proyecto" name="tipo_proyecto" required>
                                        <option value="">Seleccionar tipo...</option>
                                        <?php foreach ($tipos_proyecto as $key => $value): ?>
                                            <option value="<?= $key; ?>" <?= set_select('tipo_proyecto', $key, $proyecto->tipo_proyecto == $key); ?>>
                                                <?= $value; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Estado *</label>
                                    <select class="form-select" id="estado" name="estado" required>
                                        <option value="">Seleccionar estado...</option>
                                        <?php foreach ($estados_proyecto as $key => $value): ?>
                                            <option value="<?= $key; ?>" <?= set_select('estado', $key, $proyecto->estado == $key); ?>>
                                                <?= $value; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Ubicación -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Ubicación</label>
                                    <input type="text" id="ubicacion" name="ubicacion" value="<?= set_value('ubicacion', $proyecto->ubicacion); ?>" class="form-control" placeholder="Ciudad, Región">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" id="direccion" name="direccion" value="<?= set_value('direccion', $proyecto->direccion); ?>" class="form-control" placeholder="Dirección específica">
                                </div>
                            </div>
                        </div>

                        <!-- Fechas -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Fecha de Inicio</label>
                                    <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= set_value('fecha_inicio', $proyecto->fecha_inicio); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Fecha de Finalización</label>
                                    <input type="date" id="fecha_finalizacion" name="fecha_finalizacion" value="<?= set_value('fecha_finalizacion', $proyecto->fecha_finalizacion); ?>" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Presupuesto -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Presupuesto</label>
                                    <input type="number" id="presupuesto" name="presupuesto" value="<?= set_value('presupuesto', $proyecto->presupuesto); ?>" class="form-control" step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="mostrar_presupuesto" name="mostrar_presupuesto" value="1" <?= set_checkbox('mostrar_presupuesto', '1', $proyecto->mostrar_presupuesto); ?>>
                                        <label class="form-check-label" for="mostrar_presupuesto">
                                            Mostrar presupuesto públicamente
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Descripciones -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Descripción Corta</label>
                                    <textarea id="descripcion_corta" name="descripcion_corta" class="form-control" rows="3" placeholder="Descripción breve del proyecto"><?= set_value('descripcion_corta', $proyecto->descripcion_corta); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Descripción Detallada</label>
                                    <textarea id="descripcion_detallada" name="descripcion_detallada" class="form-control" rows="5" placeholder="Descripción completa del proyecto, proceso, características especiales..."><?= set_value('descripcion_detallada', $proyecto->descripcion_detallada); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Características Técnicas -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Área Construida (m²)</label>
                                    <input type="number" id="area_construida" name="area_construida" value="<?= set_value('area_construida', $proyecto->area_construida); ?>" class="form-control" step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Materiales Principales</label>
                                    <input type="text" id="materiales_principales" name="materiales_principales" value="<?= set_value('materiales_principales', $proyecto->materiales_principales); ?>" class="form-control" placeholder="Hormigón, Acero, Madera...">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Características Técnicas</label>
                                    <textarea id="caracteristicas_tecnicas" name="caracteristicas_tecnicas" class="form-control" rows="4" placeholder="Detalles técnicos, especificaciones, sistemas utilizados..."><?= set_value('caracteristicas_tecnicas', $proyecto->caracteristicas_tecnicas); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonio del Cliente -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombre del Cliente</label>
                                    <input type="text" id="nombre_cliente" name="nombre_cliente" value="<?= set_value('nombre_cliente', $proyecto->nombre_cliente); ?>" class="form-control" placeholder="Nombre para el testimonio">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="destacado" name="destacado" value="1" <?= set_checkbox('destacado', '1', $proyecto->destacado); ?>>
                                        <label class="form-check-label" for="destacado">
                                            Proyecto Destacado
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Testimonio del Cliente</label>
                                    <textarea id="testimonio_cliente" name="testimonio_cliente" class="form-control" rows="3" placeholder="Testimonio o comentario del cliente sobre el proyecto"><?= set_value('testimonio_cliente', $proyecto->testimonio_cliente); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Imágenes del Proyecto -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="mt-4 mb-3">Imágenes del Proyecto</h5>
                                
                                <!-- Imágenes existentes -->
                                <?php if (!empty($imagenes)) : ?>
                                    <div class="row mb-3">
                                        <?php foreach ($imagenes as $imagen) : ?>
                                            <div class="col-md-3 mb-3">
                                                <div class="card">
                                                    <img src="<?= base_url($imagen->ruta) ?>" class="card-img-top" style="height: 150px; object-fit: cover;" alt="Imagen del proyecto">
                                                    <div class="card-body p-2">
                                                        <div class="d-flex justify-content-between">
                                                            <?php if ($imagen->es_portada) : ?>
                                                                <span class="badge badge-success">Portada</span>
                                                            <?php else : ?>
                                                                <button type="button" class="btn btn-sm btn-outline-primary set-portada" data-imagen-id="<?= $imagen->id ?>" data-proyecto-id="<?= $proyecto->id ?>">
                                                                    Portada
                                                                </button>
                                                            <?php endif; ?>
                                                            <button type="button" class="btn btn-sm btn-outline-danger eliminar-imagen" data-imagen-id="<?= $imagen->id ?>">
                                                                Eliminar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Subir nuevas imágenes -->
                                <div class="mb-3">
                                    <label class="form-label">Agregar Nuevas Imágenes</label>
                                    <input type="file" id="imagenes" name="imagenes[]" class="form-control" multiple accept="image/*">
                                    <small class="text-muted">Puedes seleccionar múltiples imágenes.</small>
                                </div>
                            </div>
                        </div>

                        <!-- SEO -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="mt-4 mb-3">Configuración SEO</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Meta Título</label>
                                    <input type="text" id="meta_titulo" name="meta_titulo" value="<?= set_value('meta_titulo', $proyecto->meta_titulo); ?>" class="form-control" placeholder="Título para motores de búsqueda">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Meta Descripción</label>
                                    <textarea id="meta_descripcion" name="meta_descripcion" class="form-control" rows="2" placeholder="Descripción para motores de búsqueda"><?= set_value('meta_descripcion', $proyecto->meta_descripcion); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Meta Keywords</label>
                                    <input type="text" id="meta_keywords" name="meta_keywords" value="<?= set_value('meta_keywords', $proyecto->meta_keywords); ?>" class="form-control" placeholder="Palabras clave separadas por comas">
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">Actualizar Proyecto</button>
                                    <a href="<?= base_url('dashboard/proyecto/lista') ?>" class="btn btn-secondary">Volver a Lista</a>
                                </div>
                            </div>
                        </div>

                    <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEliminarImagen" tabindex="-1" aria-labelledby="modalEliminarImagenTitle" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEliminarImagenTitle">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <p class="modal-text">¿Estás seguro de que deseas eliminar esta imagen? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light-dark _effect--ripple waves-effect waves-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="confirmarEliminarImagen" class="btn btn-danger">
                    Eliminar Imagen
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Establecer imagen como portada
$(document).on('click', '.set-portada', function() {
    var imagenId = $(this).data('imagen-id');
    var proyectoId = $(this).data('proyecto-id');
    
    $.ajax({
        url: '<?= base_url('dashboard/proyecto/setPortada') ?>',
        type: 'POST',
        data: {
            imagen_id: imagenId,
            proyecto_id: proyectoId,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error al establecer portada: ' + response.message);
            }
        },
        error: function() {
            alert('Error al establecer portada');
        }
    });
});

// Variable para almacenar el ID de la imagen a eliminar
var imagenIdAEliminar = null;

// Mostrar modal de confirmación para eliminar imagen
$(document).on('click', '.eliminar-imagen', function() {
    imagenIdAEliminar = $(this).data('imagen-id');
    $("#modalEliminarImagen").modal("show");
});

// Confirmar eliminación de imagen
$(document).on('click', '#confirmarEliminarImagen', function() {
    if (imagenIdAEliminar) {
        $.ajax({
            url: '<?= base_url('dashboard/proyecto/eliminarImagen') ?>',
            type: 'POST',
            data: {
                imagen_id: imagenIdAEliminar,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    $("#modalEliminarImagen").modal("hide");
                    location.reload();
                } else {
                    alert('Error al eliminar imagen: ' + response.message);
                }
            },
            error: function() {
                alert('Error al eliminar imagen');
            }
        });
    }
});

// Limpiar variable al cerrar modal
$('#modalEliminarImagen').on('hidden.bs.modal', function () {
    imagenIdAEliminar = null;
});
</script>

<?= $this->endSection() ?>

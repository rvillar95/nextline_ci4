<?= $this->extend('layout/dashboard') ?>

<?= $this->section('cliente/editar') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-edit"></i> Editar Cliente: <?= esc($cliente->nombre_razon_social) ?>
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/cliente/lista') ?>" class="btn btn-secondary">
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

                    <form action="<?= base_url('dashboard/cliente/update') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $cliente->id ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tipo de Cliente *</label>
                                    <select name="tipo_cliente" class="form-control" required>
                                        <option value="">Seleccionar tipo</option>
                                        <option value="particular" <?= $cliente->tipo_cliente == 'particular' ? 'selected' : '' ?>>Particular</option>
                                        <option value="empresa" <?= $cliente->tipo_cliente == 'empresa' ? 'selected' : '' ?>>Empresa</option>
                                        <option value="organizacion" <?= $cliente->tipo_cliente == 'organizacion' ? 'selected' : '' ?>>Organización</option>
                                    </select>
                                    <?php if (session()->getFlashdata('errors')['tipo_cliente'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['tipo_cliente']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nombre / Razón Social *</label>
                                    <input type="text" name="nombre_razon_social" class="form-control" 
                                           value="<?= old('nombre_razon_social', $cliente->nombre_razon_social) ?>" required>
                                    <?php if (session()->getFlashdata('errors')['nombre_razon_social'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['nombre_razon_social']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">RUT / DNI</label>
                                    <input type="text" name="rut_dni" id="rut_dni" class="form-control" 
                                           value="<?= old('rut_dni', $cliente->rut_dni) ?>" placeholder="Ej: 12.345.678-9">
                                    <small class="text-muted">Formato: 12.345.678-9</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="telefono" class="form-control" 
                                           value="<?= old('telefono', $cliente->telefono) ?>" placeholder="Ej: +56912345678">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nombre de Contacto</label>
                                    <input type="text" name="contacto_nombre" class="form-control" 
                                           value="<?= old('contacto_nombre', $cliente->contacto_nombre) ?>" placeholder="Persona de contacto">
                                    <small class="text-muted">Para empresas/organizaciones</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Cargo del Contacto</label>
                                    <input type="text" name="contacto_cargo" class="form-control" 
                                           value="<?= old('contacto_cargo', $cliente->contacto_cargo) ?>" placeholder="Ej: Gerente, Director">
                                    <small class="text-muted">Para empresas/organizaciones</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" 
                                           value="<?= old('email', $cliente->email) ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Sitio Web</label>
                                    <input type="url" name="sitio_web" class="form-control" 
                                           value="<?= old('sitio_web', $cliente->sitio_web) ?>" placeholder="https://www.ejemplo.com">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Región *</label>
                                    <select name="region_id" id="region_id" class="form-control" required>
                                        <option value="">Seleccionar región...</option>
                                        <!-- Las opciones se cargarán via AJAX -->
                                    </select>
                                    <?php if (session()->getFlashdata('errors')['region_id'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['region_id']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Comuna *</label>
                                    <select name="comuna_id" id="comuna_id" class="form-control" required disabled>
                                        <option value="">Primero seleccione una región...</option>
                                    </select>
                                    <?php if (session()->getFlashdata('errors')['comuna_id'] ?? false): ?>
                                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['comuna_id']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" name="direccion" class="form-control" 
                                           value="<?= old('direccion', $cliente->direccion) ?>" placeholder="Calle, número, depto">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Observaciones</label>
                                    <textarea name="observaciones" class="form-control" rows="3" 
                                              placeholder="Información adicional del cliente"><?= old('observaciones', $cliente->observaciones) ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Actualizar Cliente
                                    </button>
                                    <a href="<?= base_url('dashboard/cliente/lista') ?>" class="btn btn-secondary">
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

<script>
$(document).ready(function() {
    // Cargar regiones al inicializar
    cargarRegiones();
    
    // Manejar cambio de región
    $('#region_id').on('change', function() {
        var regionId = $(this).val();
        if (regionId) {
            cargarComunas(regionId);
        } else {
            $('#comuna_id').html('<option value="">Primero seleccione una región...</option>').prop('disabled', true);
        }
    });
    
    // Formatear RUT automáticamente
    $('#rut_dni').on('input', function() {
        var rut = $(this).val();
        var rutFormateado = formatearRut(rut);
        $(this).val(rutFormateado);
    });
});

function cargarRegiones() {
    $.ajax({
        url: '<?= base_url('dashboard/ubicacion/regiones') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var options = '<option value="">Seleccionar región...</option>';
                $.each(response.data, function(index, region) {
                    options += '<option value="' + region.id + '">' + region.nombre + '</option>';
                });
                $('#region_id').html(options);
                
                // Si hay un valor preseleccionado (en modo edición)
                <?php if (old('region_id') || isset($cliente->region_id)): ?>
                    var regionId = '<?= old('region_id', $cliente->region_id ?? '') ?>';
                    if (regionId) {
                        $('#region_id').val(regionId).trigger('change');
                    }
                <?php endif; ?>
            }
        },
        error: function() {
            console.error('Error al cargar regiones');
            $('#region_id').html('<option value="">Error al cargar regiones</option>');
        }
    });
}

function cargarComunas(regionId) {
    $.ajax({
        url: '<?= base_url('dashboard/ubicacion/comunas') ?>/' + regionId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var options = '<option value="">Seleccionar comuna...</option>';
                $.each(response.data, function(index, comuna) {
                    options += '<option value="' + comuna.id + '">' + comuna.nombre + '</option>';
                });
                $('#comuna_id').html(options).prop('disabled', false);
                
                // Si hay un valor preseleccionado (en modo edición)
                <?php if (old('comuna_id') || isset($cliente->comuna_id)): ?>
                    var comunaId = '<?= old('comuna_id', $cliente->comuna_id ?? '') ?>';
                    if (comunaId) {
                        $('#comuna_id').val(comunaId);
                    }
                <?php endif; ?>
            }
        },
        error: function() {
            console.error('Error al cargar comunas');
            $('#comuna_id').html('<option value="">Error al cargar comunas</option>');
        }
    });
}

// Función para validar ubicación
function validarUbicacion() {
    var regionId = $('#region_id').val();
    var comunaId = $('#comuna_id').val();
    
    if (!regionId || !comunaId) {
        return true; // Dejar que la validación HTML5 maneje esto
    }
    
    $.ajax({
        url: '<?= base_url('dashboard/ubicacion/validar') ?>',
        type: 'POST',
        data: {
            region_id: regionId,
            comuna_id: comunaId
        },
        dataType: 'json',
        success: function(response) {
            if (!response.success || !response.valid) {
                alert('La comuna seleccionada no pertenece a la región elegida.');
                $('#comuna_id').focus();
                return false;
            }
        },
        error: function() {
            console.error('Error al validar ubicación');
        }
    });
    
    return true;
}

// Agregar validación al formulario
$('form').on('submit', function(e) {
    if (!validarUbicacion()) {
        e.preventDefault();
        return false;
    }
});

// Función para formatear RUT automáticamente
function formatearRut(rut) {
    // Limpiar el RUT
    rut = rut.replace(/[^0-9kK]/g, '');
    
    if (rut.length === 0) return '';
    
    // Separar número y dígito verificador
    var numero = rut.slice(0, -1);
    var dv = rut.slice(-1).toUpperCase();
    
    // Agregar puntos cada 3 dígitos desde la derecha
    if (numero.length > 0) {
        numero = numero.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    
    return numero + '-' + dv;
}

// Función para validar RUT en tiempo real
function validarRut(rut) {
    if (!rut || rut.trim() === '') return true; // Permitir vacío
    
    // Limpiar RUT
    rut = rut.replace(/[^0-9kK]/g, '');
    
    if (rut.length < 2) return false;
    
    var numero = rut.slice(0, -1);
    var dv = rut.slice(-1).toUpperCase();
    
    // Calcular dígito verificador
    var suma = 0;
    var multiplicador = 2;
    
    for (var i = numero.length - 1; i >= 0; i--) {
        suma += parseInt(numero[i]) * multiplicador;
        multiplicador++;
        if (multiplicador > 7) multiplicador = 2;
    }
    
    var resto = suma % 11;
    var dvCalculado = 11 - resto;
    
    if (dvCalculado === 11) dvCalculado = '0';
    else if (dvCalculado === 10) dvCalculado = 'K';
    else dvCalculado = dvCalculado.toString();
    
    return dv === dvCalculado;
}
</script>

<?= $this->endSection() ?>


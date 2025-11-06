<!-- Vista: Listado de Materiales - Registro -->
<?= $this->extend('layout/dashboard') ?>

<?= $this->section('listado_material/registro') ?>
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        
        <!-- Breadcrumb -->
        <div class="page-meta">
            <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard/inicio') ?>">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard/listado-material/lista') ?>">Listado de Materiales</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nuevo</li>
                </ol>
            </nav>
        </div>

        <!-- Mensajes Flash -->
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Errores de validación:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('dashboard/listado-material/registrar') ?>" method="POST" id="formListadoMaterial">
            <?= csrf_field() ?>

            <div class="row">
                <!-- Información General -->
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
                    <div class="card">
                        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Información General
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Título del Listado <span class="text-danger">*</span></label>
                                    <input type="text" name="titulo" class="form-control" value="<?= old('titulo') ?>" required>
                                    <small class="form-text text-muted">Nombre descriptivo del listado</small>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                    <input type="date" name="fecha_listado" class="form-control" value="<?= old('fecha_listado', date('Y-m-d')) ?>" required>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Estado <span class="text-danger">*</span></label>
                                    <select name="estado" class="form-control" required>
                                        <option value="borrador" <?= old('estado') == 'borrador' ? 'selected' : '' ?>>Borrador</option>
                                        <option value="finalizado" <?= old('estado') == 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                                        <option value="enviado" <?= old('estado') == 'enviado' ? 'selected' : '' ?>>Enviado</option>
                                        <option value="archivado" <?= old('estado') == 'archivado' ? 'selected' : '' ?>>Archivado</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cliente</label>
                                    <select name="cliente_id" id="cliente_id" class="form-control">
                                        <option value="">-- Seleccione cliente (opcional) --</option>
                                        <?php 
                                        $cliente_id_seleccionado = old('cliente_id', $_GET['cliente_id'] ?? '');
                                        if (!empty($clientes)): foreach ($clientes as $cliente): 
                                        ?>
                                            <option value="<?= $cliente->id ?>" <?= $cliente_id_seleccionado == $cliente->id ? 'selected' : '' ?>>
                                                <?= esc($cliente->nombre_razon_social) ?>
                                            </option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Proyecto</label>
                                    <select name="proyecto_id" id="proyecto_id" class="form-control">
                                        <option value="">-- Seleccione proyecto (opcional) --</option>
                                        <?php if (!empty($proyectos)): foreach ($proyectos as $proyecto): ?>
                                            <option value="<?= $proyecto->id ?>" <?= old('proyecto_id') == $proyecto->id ? 'selected' : '' ?>>
                                                <?= esc($proyecto->nombre) ?>
                                            </option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Observaciones</label>
                                    <textarea name="observaciones" class="form-control" rows="3"><?= old('observaciones') ?></textarea>
                                    <small class="form-text text-muted">Notas adicionales sobre el listado</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Materiales -->
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
                    <div class="card">
                        <div class="card-header" style="background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%); color: white;">
                            <h5 class="mb-0">
                                <i class="fas fa-boxes me-2"></i>
                                Materiales
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <button type="button" class="btn btn-success" id="btnAgregarMaterial" onclick="agregarMaterial()">
                                    <i class="fas fa-plus me-1"></i> Agregar Material
                                </button>
                            </div>

                            <!-- Contenedor para cards móviles -->
                            <div id="materialesContainerMobile" class="d-block d-md-none">
                                <!-- Las cards móviles se agregarán aquí -->
                            </div>

                            <!-- Tabla para desktop -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-bordered" id="tablaMateriales">
                                    <thead style="background: #f8f9fa;">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="50%">Material <span class="text-danger">*</span></th>
                                            <th width="20%">Unidad</th>
                                            <th width="20%">Cantidad</th>
                                            <th width="5%">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="materialesContainer">
                                        <!-- Los materiales se agregarán aquí dinámicamente -->
                                    </tbody>
                                </table>
                            </div>

                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Nota:</strong> Los campos de Unidad y Cantidad son opcionales. Si no se completan, no se mostrarán en el PDF.
                </div>

                <!-- Botones -->
                            <div class="mt-4">
                    <div class="d-flex justify-content-end gap-2">
                                    <a href="<?= base_url('dashboard/listado-material/lista') ?>" class="btn btn-light btn-accion-mobile">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                                    <button type="submit" class="btn btn-primary btn-accion-mobile">
                            <i class="fas fa-save me-2"></i>Guardar Listado
                        </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
    let contadorMateriales = 0;

    // Agregar un material vacío al cargar
    document.addEventListener('DOMContentLoaded', function() {
        agregarMaterial();
    });

    function agregarMaterial() {
        contadorMateriales++;
        
        // HTML para la tabla (desktop)
        const htmlTabla = `
            <tr data-index="${contadorMateriales}">
                <td class="text-center">${contadorMateriales}</td>
                <td>
                    <textarea name="materiales[${contadorMateriales}][nombre_material]" class="form-control material-input-desktop" rows="2" data-index="${contadorMateriales}"></textarea>
                </td>
                <td>
                    <input type="text" name="materiales[${contadorMateriales}][unidad_medida]" class="form-control" placeholder="m², kg, und">
                </td>
                <td>
                    <input type="number" name="materiales[${contadorMateriales}][cantidad]" class="form-control" step="0.01" min="0">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarMaterial(${contadorMateriales})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        // HTML para card móvil
        const htmlMobile = `
            <div class="material-card-mobile mb-3" data-index="${contadorMateriales}">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Material #<span class="material-number">${contadorMateriales}</span></h5>
                            <button type="button" class="btn btn-danger btn-lg" onclick="eliminarMaterial(${contadorMateriales})">
                                <i class="fas fa-trash me-1"></i> Eliminar
                            </button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="font-size: 16px;">Nombre del Material <span class="text-danger">*</span></label>
                            <textarea name="materiales[${contadorMateriales}][nombre_material]" class="form-control form-control-lg mobile-input material-input-mobile" rows="4" data-index="${contadorMateriales}" style="font-size: 18px; min-height: 120px;"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold" style="font-size: 16px;">Unidad</label>
                                <input type="text" name="materiales[${contadorMateriales}][unidad_medida]" class="form-control form-control-lg mobile-input" placeholder="m², kg, und" style="font-size: 16px;">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold" style="font-size: 16px;">Cantidad</label>
                                <input type="number" name="materiales[${contadorMateriales}][cantidad]" class="form-control form-control-lg mobile-input" step="0.01" min="0" style="font-size: 16px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Agregar al contenedor correspondiente
        document.getElementById('materialesContainer').insertAdjacentHTML('beforeend', htmlTabla);
        document.getElementById('materialesContainerMobile').insertAdjacentHTML('beforeend', htmlMobile);
        actualizarNumeracion();
    }

    function eliminarMaterial(index) {
        // Eliminar fila de tabla (desktop)
        const row = document.querySelector(`tr[data-index="${index}"]`);
        if (row) {
            row.remove();
        }
        // Eliminar card (mobile)
        const card = document.querySelector(`.material-card-mobile[data-index="${index}"]`);
        if (card) {
            card.remove();
        }
            actualizarNumeracion();
    }

    function actualizarNumeracion() {
        // Actualizar numeración en tabla (desktop)
        const rows = document.querySelectorAll('#materialesContainer tr');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
        });
        
        // Actualizar numeración en cards (mobile)
        const cards = document.querySelectorAll('.material-card-mobile');
        cards.forEach((card, index) => {
            card.querySelector('.material-number').textContent = index + 1;
        });
    }

    // Validación antes de enviar
    document.getElementById('formListadoMaterial').addEventListener('submit', function(e) {
        const materiales = document.querySelectorAll('#materialesContainer tr');
        
        if (materiales.length === 0) {
            e.preventDefault();
            alert('Debe agregar al menos un material al listado');
            return false;
        }

        // Verificar si estamos en móvil o desktop
        const isMobile = window.innerWidth <= 768;
        
        // Si estamos en móvil, copiar los valores de los campos móviles a los campos desktop
        if (isMobile) {
            const cardsMobile = document.querySelectorAll('.material-card-mobile');
            cardsMobile.forEach(card => {
                const index = card.getAttribute('data-index');
                
                // Copiar nombre del material
                const nombreMobile = card.querySelector(`textarea[name="materiales[${index}][nombre_material]"]`);
                const nombreDesktop = document.querySelector(`#materialesContainer textarea[name="materiales[${index}][nombre_material]"]`);
                if (nombreMobile && nombreDesktop) {
                    nombreDesktop.value = nombreMobile.value;
                }
                
                // Copiar unidad
                const unidadMobile = card.querySelector(`input[name="materiales[${index}][unidad_medida]"]`);
                const unidadDesktop = document.querySelector(`#materialesContainer input[name="materiales[${index}][unidad_medida]"]`);
                if (unidadMobile && unidadDesktop) {
                    unidadDesktop.value = unidadMobile.value;
                }
                
                // Copiar cantidad
                const cantidadMobile = card.querySelector(`input[name="materiales[${index}][cantidad]"]`);
                const cantidadDesktop = document.querySelector(`#materialesContainer input[name="materiales[${index}][cantidad]"]`);
                if (cantidadMobile && cantidadDesktop) {
                    cantidadDesktop.value = cantidadMobile.value;
                }
            });
        }
        
        // Validar que todos los materiales tengan nombre
        let hayError = false;
        const materialesInputs = isMobile 
            ? document.querySelectorAll('.material-input-mobile')
            : document.querySelectorAll('.material-input-desktop');
        
        materialesInputs.forEach(input => {
            if (!input.value.trim()) {
                hayError = true;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (hayError) {
            e.preventDefault();
            alert('Por favor, complete el nombre de todos los materiales');
            return false;
        }
    });
</script>

<style>
    .gap-2 {
        gap: 0.5rem;
    }

    /* Estilos para móvil */
    @media (max-width: 768px) {
        /* Botones sticky en móvil - ajuste para estar dentro del card */
        .card-body > .mt-4:last-child {
            position: sticky;
            bottom: 0;
            z-index: 1000;
            background: white;
            padding: 15px;
            margin: 0 -20px -20px -20px !important;
            
            border-top: 1px solid #e0e6ed;
        }
        
        .btn-accion-mobile {
            flex: 1;
            padding: 15px 20px !important;
            font-size: 18px !important;
            font-weight: 600 !important;
        }
        
        /* Mejorar tamaño de inputs para adultos */
        .mobile-input {
            min-height: 50px !important;
            font-size: 18px !important;
            padding: 12px !important;
        }
        
        /* Hacer labels más grandes */
        .form-label {
            font-size: 17px !important;
            margin-bottom: 8px !important;
        }
        
        /* Botones más grandes y fáciles de presionar */
        .btn-lg {
            padding: 12px 20px !important;
            font-size: 16px !important;
            min-height: 50px !important;
        }
        
        /* Botón agregar material más grande */
        #btnAgregarMaterial {
            width: 100%;
            padding: 15px !important;
            font-size: 18px !important;
            margin-bottom: 20px;
        }
        
        /* Card headers más grandes */
        .card-header h5 {
            font-size: 20px !important;
        }
        
        /* Alert más legible */
        .alert {
            font-size: 16px !important;
            padding: 15px !important;
        }
        
        /* Inputs del formulario general */
        .form-control:not(.mobile-input) {
            min-height: 48px !important;
            font-size: 16px !important;
            padding: 10px !important;
        }
        
        /* Textarea más grande */
        textarea.form-control {
            min-height: 80px !important;
        }
        
        /* Botones de acción del formulario */
        .btn:not(.btn-lg) {
            padding: 12px 20px !important;
            font-size: 16px !important;
        }
        
        /* Espaciado entre cards */
        .card {
            margin-bottom: 20px;
        }
        
        /* Small text más legible */
        small, .form-text {
            font-size: 14px !important;
        }
        
        /* Mejorar espaciado de las cards de material */
        .material-card-mobile .card-body {
            padding: 20px !important;
        }
    }
    
    /* Mejorar contraste de los botones */
    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }
    
    /* Estilos para validación */
    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
</style>

<?= $this->endSection() ?>


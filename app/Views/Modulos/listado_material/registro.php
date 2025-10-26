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
                                <button type="button" class="btn btn-success btn-sm" onclick="agregarMaterial()">
                                    <i class="fas fa-plus me-1"></i> Agregar Material
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="tablaMateriales">
                                    <thead style="background: #f8f9fa;">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="35%">Material <span class="text-danger">*</span></th>
                                            <th width="25%">Descripción</th>
                                            <th width="15%">Unidad</th>
                                            <th width="15%">Cantidad</th>
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
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('dashboard/listado-material/lista') ?>" class="btn btn-light">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Guardar Listado
                        </button>
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
        const html = `
            <tr data-index="${contadorMateriales}">
                <td class="text-center">${contadorMateriales}</td>
                <td>
                    <input type="text" name="materiales[${contadorMateriales}][nombre_material]" class="form-control form-control-sm" required>
                </td>
                <td>
                    <textarea name="materiales[${contadorMateriales}][descripcion]" class="form-control form-control-sm" rows="1"></textarea>
                </td>
                <td>
                    <input type="text" name="materiales[${contadorMateriales}][unidad_medida]" class="form-control form-control-sm" placeholder="m², kg, und">
                </td>
                <td>
                    <input type="number" name="materiales[${contadorMateriales}][cantidad]" class="form-control form-control-sm" step="0.01" min="0">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarMaterial(${contadorMateriales})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        document.getElementById('materialesContainer').insertAdjacentHTML('beforeend', html);
        actualizarNumeracion();
    }

    function eliminarMaterial(index) {
        const row = document.querySelector(`tr[data-index="${index}"]`);
        if (row) {
            row.remove();
            actualizarNumeracion();
        }
    }

    function actualizarNumeracion() {
        const rows = document.querySelectorAll('#materialesContainer tr');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
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
    });
</script>

<style>
    .gap-2 {
        gap: 0.5rem;
    }
</style>

<?= $this->endSection() ?>


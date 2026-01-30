<?= $this->extend('layout/dashboard') ?>

<?= $this->section('pago/registro') ?>

<style>
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #30cfd0;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
        color: white;
        border: none;
        padding: 12px 35px;
        font-size: 1.05rem;
        font-weight: 600;
        border-radius: 8px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-money-bill-wave me-2"></i> Registrar Pago</h2>
                        <p style="color: white;">Registre un nuevo pago o transacción</p>
                    </div>
                    <a href="<?= base_url('dashboard/pago/lista') ?>" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i> Volver
                    </a>
                </div>
            </div>

            <form action="<?= base_url('dashboard/pago/registrar') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">1</span>
                        <span><i class="fas fa-building icon-label"></i> Información del Pago</span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Empresa <span class="text-danger">*</span></label>
                                <select name="empresa_id" class="form-control" required>
                                    <option value="">-- Seleccione una empresa --</option>
                                    <?php foreach ($empresas as $empresa) : ?>
                                        <option value="<?= $empresa->id ?>"><?= esc($empresa->nombre) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tipo de Pago <span class="text-danger">*</span></label>
                                <select name="tipo_pago" class="form-control" required>
                                    <option value="mensual">Mensual</option>
                                    <option value="setup">Setup</option>
                                    <option value="anual">Anual</option>
                                    <option value="extra">Extra</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Estado <span class="text-danger">*</span></label>
                                <select name="estado_pago" class="form-control" required>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="procesando">Procesando</option>
                                    <option value="completado">Completado</option>
                                    <option value="fallido">Fallido</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Monto <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="monto" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Moneda</label>
                                <select name="moneda" class="form-control">
                                    <option value="CLP" selected>CLP (Peso Chileno)</option>
                                    <option value="USD">USD (Dólar)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Método de Pago</label>
                                <select name="metodo_pago" class="form-control">
                                    <option value="">-- Seleccione --</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha de Pago</label>
                                <input type="date" name="fecha_pago" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha de Vencimiento</label>
                                <input type="date" name="fecha_vencimiento" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Referencia</label>
                                <input type="text" name="referencia" class="form-control" placeholder="Número de transacción">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Período Inicio</label>
                                <input type="date" name="periodo_inicio" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Período Fin</label>
                                <input type="date" name="periodo_fin" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea name="observaciones" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save me-2"></i> Registrar Pago
                    </button>
                    <a href="<?= base_url('dashboard/pago/lista') ?>" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="page-header page-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center">
                    <h1>¡Gracias por Contactarnos!</h1>
                    <div class="small-border"></div>
                    <p>Hemos recibido tu mensaje y nos pondremos en contacto contigo pronto.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Success Message -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center">
                    <div class="success-icon mb-4">
                        <i class="fa fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h2 class="mb-4">Mensaje Enviado Exitosamente</h2>
                    
                    <p class="lead mb-4">
                        Gracias por tu interés en nuestros servicios. Hemos recibido tu consulta y 
                        nuestro equipo se pondrá en contacto contigo en las próximas 24 horas.
                    </p>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <i class="fa fa-clock-o text-primary"></i>
                                <h4>Tiempo de Respuesta</h4>
                                <p>Máximo 24 horas</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <i class="fa fa-phone text-primary"></i>
                                <h4>Contacto Directo</h4>
                                <p>+56 9 1234 5678</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="<?= base_url() ?>" class="btn-custom">Volver al Inicio</a>
                        <a href="<?= base_url('servicios') ?>" class="btn-custom btn-outline">Ver Servicios</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.success-icon {
    animation: bounceIn 1s ease-in-out;
}

.info-box {
    padding: 20px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    margin-bottom: 20px;
}

.info-box i {
    font-size: 2rem;
    margin-bottom: 10px;
    display: block;
}

.info-box h4 {
    margin-bottom: 10px;
    color: #333;
}

.action-buttons {
    margin-top: 30px;
}

.action-buttons .btn-custom {
    margin: 0 10px;
}

@keyframes bounceIn {
    0% { transform: scale(0.3); opacity: 0; }
    50% { transform: scale(1.05); }
    70% { transform: scale(0.9); }
    100% { transform: scale(1); opacity: 1; }
}
</style>

<?= $this->endSection() ?>

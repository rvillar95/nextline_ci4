<?= $this->extend('layout/abopech') ?>
<?= $this->section('content') ?>
<div class="row align-items-center py-5">
    <div class="col-lg-7">
        <p class="text-uppercase text-muted small mb-2">Abogados Penalistas de Chile</p>
        <h1 class="display-5 fw-bold text-abopech mb-3">Encuentra abogados penalistas con trayectoria comprobada</h1>
        <p class="lead">ABOPECH reúne profesionales con más de mil audiencias. Busca por región, comuna o tribunal de Garantía y Juicio Oral en lo Penal.</p>
        <div class="d-flex gap-2 flex-wrap mt-4">
            <a href="<?= base_url('abopech/buscar') ?>" class="btn btn-abopech btn-lg">Buscar abogado</a>
            <a href="<?= base_url('abopech/auth/registro') ?>" class="btn btn-outline-primary btn-lg">Soy abogado penalista</a>
        </div>
    </div>
    <div class="col-lg-5 text-center d-none d-lg-block">
        <div class="p-5 rounded-3" style="background: linear-gradient(135deg, #003893 0%, #D52B1E 100%); opacity: .15; min-height: 220px;"></div>
    </div>
</div>
<?= $this->endSection() ?>

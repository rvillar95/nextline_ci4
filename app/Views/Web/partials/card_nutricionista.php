<?php
/** @var object $n */
$nombreCompleto = trim(($n->nombre ?? '') . ' ' . ($n->apellido ?? ''));
$fotoUrl = !empty($n->foto) ? base_url($n->foto) : ($placeholder_foto ?? base_url('lib/src/assets/img/profile-30.png'));
$presentacion = trim((string) ($n->presentacion ?? ''));
if ($presentacion !== '' && mb_strlen($presentacion) > 200) {
    $presentacion = mb_substr($presentacion, 0, 200) . '…';
}
?>
<div class="col-md-6 col-lg-4">
    <article class="card equipo-card h-100 border-0 shadow-sm">
        <div class="card-body d-flex flex-column p-4">
            <div class="equipo-card-foto-wrap mx-auto mb-3">
                <img src="<?= esc($fotoUrl) ?>" alt="<?= esc($nombreCompleto) ?>" class="equipo-card-foto" loading="lazy">
            </div>
            <h2 class="h5 fw-bold text-center mb-1"><?= esc($nombreCompleto) ?></h2>
            <?php if (!empty($n->titulo_profesional)): ?>
                <p class="text-center text-muted small mb-1"><?= esc($n->titulo_profesional) ?></p>
            <?php endif; ?>
            <?php if (!empty($n->especialidad)): ?>
                <p class="text-center small mb-2"><i class="fas fa-leaf text-success me-1"></i><?= esc($n->especialidad) ?></p>
            <?php endif; ?>
            <?php if (!empty($n->carrera)): ?>
                <p class="text-center small text-secondary mb-2"><i class="fas fa-graduation-cap me-1"></i><?= esc($n->carrera) ?></p>
            <?php endif; ?>
            <?php if ($presentacion !== ''): ?>
                <p class="small text-secondary flex-grow-1 mb-3"><?= esc($presentacion) ?></p>
            <?php endif; ?>
            <div class="equipo-card-contact small mb-3">
                <?php if (!empty($n->telefono)): ?>
                    <a href="tel:<?= esc(preg_replace('/\s+/', '', $n->telefono)) ?>" class="d-block mb-1 text-decoration-none">
                        <i class="fas fa-phone me-1"></i><?= esc($n->telefono) ?>
                    </a>
                <?php endif; ?>
                <?php if (!empty($n->correo)): ?>
                    <a href="mailto:<?= esc($n->correo) ?>" class="d-block text-decoration-none">
                        <i class="fas fa-envelope me-1"></i><?= esc($n->correo) ?>
                    </a>
                <?php endif; ?>
            </div>
            <div class="d-grid gap-2 mt-auto">
                <a href="<?= base_url('equipo/' . (int) $n->id) ?>" class="btn btn-outline-success btn-sm">Ver perfil</a>
                <a href="<?= base_url('reservar?nutricionista_id=' . (int) $n->id) ?>" class="btn btn-success btn-sm">Reservar hora</a>
            </div>
        </div>
    </article>
</div>

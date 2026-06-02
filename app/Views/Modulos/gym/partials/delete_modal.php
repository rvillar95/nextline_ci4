<?php
/** @var string $action URL POST eliminar */
/** @var string $message */
/** @var string $title */
$action  = $action ?? '#';
$title   = $title ?? 'Confirmar eliminación';
$message = $message ?? '¿Eliminar este registro? Esta acción no se puede deshacer.';
?>
<div class="modal fade gym-modal" id="modalEliminacion" tabindex="-1" aria-labelledby="modalEliminacionTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEliminacionTitle"><?= esc($title) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0"><?= esc($message) ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn gym-btn-outline" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="<?= esc($action) ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <input type="hidden" id="id" name="id" value="">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

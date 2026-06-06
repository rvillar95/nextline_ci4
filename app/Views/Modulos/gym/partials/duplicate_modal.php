<?php
/** @var string $action URL POST duplicar */
/** @var string $title */
/** @var string $message */
/** @var string $submitLabel */
/** @var string $module ejercicio|rutina|programa|alumno|asignacion */
$action       = $action ?? '#';
$title        = $title ?? 'Duplicar';
$message      = $message ?? 'Se creará una copia independiente. Puedes cambiar el nombre.';
$submitLabel  = $submitLabel ?? 'Duplicar';
$module       = $module ?? 'ejercicio';
$moduleClass  = 'gym-modal--' . preg_replace('/[^a-z]/', '', $module);
?>
<div class="modal fade gym-modal <?= esc($moduleClass) ?>" id="modalDuplicar" tabindex="-1" aria-labelledby="modalDuplicarTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="<?= esc($action) ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDuplicarTitle"><?= esc($title) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small"><?= esc($message) ?></p>
                    <input type="hidden" id="dup_id" name="id" value="">
                    <label class="form-label" for="dup_nombre">Nombre de la copia</label>
                    <input type="text" id="dup_nombre" name="nombre" class="form-control" maxlength="160" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn gym-btn-outline" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn gym-btn-primary"><i class="fas fa-copy me-1"></i> <?= esc($submitLabel) ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

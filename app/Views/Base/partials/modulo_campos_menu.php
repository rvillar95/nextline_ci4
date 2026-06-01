<?php
/** @var array<string, mixed>|null $modulo */
/** @var list<array<string, mixed>> $menuGrupos */
/** @var array<string, string> $iconosMenu */
$modulo = $modulo ?? [];
$iconosMenu = $iconosMenu ?? config('MenuSidebar')->iconos;
?>
<div class="section-card mt-3" style="border-left-color: #667eea;">
    <h5 class="mb-3"><i class="fas fa-bars me-2"></i> Menú lateral</h5>
    <p class="text-muted small mb-4">Controla cómo aparece este módulo en el sidebar del dashboard.</p>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="form-label">Sección del menú</label>
                <select class="form-select" name="menu_grupo_id" id="menu_grupo_id">
                    <option value="">— Sin sección —</option>
                    <?php foreach ($menuGrupos as $grupo): ?>
                        <option value="<?= (int) $grupo['id'] ?>"
                            <?= (int) ($modulo['menu_grupo_id'] ?? 0) === (int) $grupo['id'] ? 'selected' : '' ?>>
                            <?= esc($grupo['etiqueta']) ?> (<?= esc($grupo['slug']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="form-label">Icono (Feather)</label>
                <select class="form-select" name="menu_icono" id="menu_icono">
                    <?php foreach ($iconosMenu as $slug => $label): ?>
                        <option value="<?= esc($slug) ?>"
                            <?= ($modulo['menu_icono'] ?? 'circle') === $slug ? 'selected' : '' ?>>
                            <?= esc($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="form-label">Etiqueta en menú</label>
                <input type="text" name="menu_etiqueta" class="form-control"
                       value="<?= esc($modulo['menu_etiqueta'] ?? '') ?>"
                       placeholder="Vacío = usar nombre del módulo">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="form-label">Aplanar si hay un solo submenú</label>
                <select class="form-select" name="menu_aplanar">
                    <option value="S" <?= ($modulo['menu_aplanar'] ?? 'S') === 'S' ? 'selected' : '' ?>>Sí</option>
                    <option value="N" <?= ($modulo['menu_aplanar'] ?? '') === 'N' ? 'selected' : '' ?>>No</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="form-label">Ruta alterna (nutricionistas)</label>
                <input type="text" name="menu_ruta_alterna" class="form-control"
                       value="<?= esc($modulo['menu_ruta_alterna'] ?? '') ?>"
                       placeholder="Ej: dashboard/pago/cobros">
                <small class="text-muted">Si se define, usuarios no super admin verán este enlace en lugar del módulo original.</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="form-label">Etiqueta alterna</label>
                <input type="text" name="menu_etiqueta_alterna" class="form-control"
                       value="<?= esc($modulo['menu_etiqueta_alterna'] ?? '') ?>"
                       placeholder="Ej: Cobros">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="form-label">Solo visible para super admin</label>
                <select class="form-select" name="menu_solo_sa">
                    <option value="N" <?= ($modulo['menu_solo_sa'] ?? 'N') === 'N' ? 'selected' : '' ?>>No</option>
                    <option value="S" <?= ($modulo['menu_solo_sa'] ?? '') === 'S' ? 'selected' : '' ?>>Sí</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación Reutilizable -->
<div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-labelledby="modalConfirmarEliminarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarEliminarLabel">
                    <i class="fas fa-exclamation-triangle text-warning"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="modalMensaje">¿Estás seguro de que deseas eliminar este elemento?</p>
                <p class="text-muted small">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Información Reutilizable -->
<div class="modal fade" id="modalInformacion" tabindex="-1" aria-labelledby="modalInformacionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalInformacionLabel">
                    <i class="fas fa-info-circle text-info"></i> Información
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="modalMensajeInfo"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="fas fa-check"></i> Entendido
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Éxito Reutilizable -->
<div class="modal fade" id="modalExito" tabindex="-1" aria-labelledby="modalExitoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalExitoLabel">
                    <i class="fas fa-check-circle text-success"></i> Éxito
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="modalMensajeExito"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                    <i class="fas fa-check"></i> Continuar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación genérico (envío, acciones, etc.) -->
<div class="modal fade" id="modalConfirmarAccion" tabindex="-1" aria-labelledby="modalConfirmarAccionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="modalConfirmarAccionTitulo">
                    <i class="fas fa-question-circle text-primary me-2"></i> Confirmar
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body pt-2">
                <p id="modalConfirmarAccionMensaje" class="mb-1 fs-6"></p>
                <p id="modalConfirmarAccionSub" class="text-muted small mb-0 d-none"></p>
                <div id="modalConfirmarAccionCampoMensaje" class="d-none mt-3">
                    <label for="modalConfirmarAccionMensajeExtra" class="form-label small mb-1">Mensaje para el paciente (opcional)</label>
                    <textarea id="modalConfirmarAccionMensajeExtra" class="form-control" rows="3" maxlength="2000" placeholder="Ej.: Adjunto su pauta actualizada. Cualquier duda, escríbame."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnConfirmarAccion">
                    <i class="fas fa-check me-1"></i> Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Error Reutilizable -->
<div class="modal fade" id="modalError" tabindex="-1" aria-labelledby="modalErrorLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalErrorLabel">
                    <i class="fas fa-exclamation-circle text-danger"></i> Error
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="modalMensajeError"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<?php
// Partial: anamnesis clínica/alimentaria, exámenes, tendencia, recordatorio 24h (para historial/editar)
$ac = [];
if (!empty($historial->anamnesis_clinica)) {
    $ac = json_decode($historial->anamnesis_clinica, true) ?: [];
}
$ac = is_array($ac) ? $ac : [];
$aa = [];
if (!empty($historial->anamnesis_alimentaria)) {
    $aa = json_decode($historial->anamnesis_alimentaria, true) ?: [];
}
$aa = is_array($aa) ? $aa : [];
?>
                    <!-- Ficha de Ingreso: Anamnesis clínica (estructurada) -->
                    <div class="row mb-4 mt-4">
                        <div class="col-12">
                            <h6 class="text-info mb-3"><i class="fas fa-notes-medical me-2"></i> Anamnesis Clínica</h6>
                            <p class="text-muted small mb-3">Completar cada ítem según corresponda.</p>
                            <input type="hidden" name="anamnesis_clinica" id="anamnesis_clinica" value="">
                        </div>
                        <div class="col-md-6 col-lg-4 mb-2">
                            <label class="form-label small mb-0">Tabaco</label>
                            <textarea class="form-control form-control-sm" id="ac_tabaco" rows="2" placeholder="Ej: No fumador / 5 cig/día"><?= esc($ac['tabaco'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-2">
                            <label class="form-label small mb-0">Alcohol</label>
                            <textarea class="form-control form-control-sm" id="ac_alcohol" rows="2" placeholder="Ej: Ocasional / No"><?= esc($ac['alcohol'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-2">
                            <label class="form-label small mb-0">Drogas</label>
                            <textarea class="form-control form-control-sm" id="ac_drogas" rows="2" placeholder="Ej: No / Especificar"><?= esc($ac['drogas'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="form-label small mb-0">Enfermedad de base / RCV</label>
                            <textarea class="form-control form-control-sm" id="ac_enfermedad_base" rows="2" placeholder="Ej: HTA, DM2, dislipidemia, ninguno"><?= esc($ac['enfermedad_base'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="form-label small mb-0">Signos y síntomas</label>
                            <textarea class="form-control form-control-sm" id="ac_signos_sintomas" rows="2" placeholder="Ej: Cefaleas ocasionales, sin otros"><?= esc($ac['signos_sintomas'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-2">
                            <label class="form-label small mb-0">Tránsito intestinal / Bristol / diuresis</label>
                            <textarea class="form-control form-control-sm" id="ac_transito_bristol" rows="2" placeholder="Ej: Regular, Bristol 3-4"><?= esc($ac['transito_bristol'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-2">
                            <label class="form-label small mb-0">Medicamentos</label>
                            <textarea class="form-control form-control-sm" id="ac_medicamentos" rows="2" placeholder="Ej: Metformina 850 mg c/12 h"><?= esc($ac['medicamentos'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-2">
                            <label class="form-label small mb-0">Suplementos</label>
                            <textarea class="form-control form-control-sm" id="ac_suplementos" rows="2" placeholder="Ej: Vit D, omega 3 / Ninguno"><?= esc($ac['suplementos'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-2">
                            <label class="form-label small mb-0">Ingesta hídrica</label>
                            <textarea class="form-control form-control-sm" id="ac_ingesta_hidrica" rows="2" placeholder="Ej: 6-8 vasos/día"><?= esc($ac['ingesta_hidrica'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-2">
                            <label class="form-label small mb-0">Actividad física</label>
                            <textarea class="form-control form-control-sm" id="ac_actividad_fisica" rows="2" placeholder="Ej: 3 veces/semana, caminata"><?= esc($ac['actividad_fisica'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-2">
                            <label class="form-label small mb-0">Sueño</label>
                            <textarea class="form-control form-control-sm" id="ac_sueno" rows="2" placeholder="Ej: 6-7 h, insomnio ocasional"><?= esc($ac['sueno'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="form-label small mb-0">Otros / notas</label>
                            <textarea class="form-control form-control-sm" id="ac_otros" rows="3" placeholder="Cualquier dato adicional"><?= esc($ac['otros'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Anamnesis alimentaria (estructurada) -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-info mb-3"><i class="fas fa-utensils me-2"></i> Anamnesis Alimentaria</h6>
                            <p class="text-muted small mb-3">Relación familiar, quién cocina, apetito, relación con la comida, historia de peso y dietas.</p>
                            <input type="hidden" name="anamnesis_alimentaria" id="anamnesis_alimentaria" value="">
                        </div>
                        <div class="col-md-6 col-lg-4 mb-2">
                            <label class="form-label small mb-0">Relación familiar / apoyo / recursos</label>
                            <textarea class="form-control form-control-sm" id="aa_relacion_familiar" rows="2" placeholder="Ej: Vive con pareja, apoyo en compras"><?= esc($aa['relacion_familiar'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-2">
                            <label class="form-label small mb-0">Quién cocina</label>
                            <textarea class="form-control form-control-sm" id="aa_quien_cocina" rows="2" placeholder="Ej: La paciente / Pareja / Delivery"><?= esc($aa['quien_cocina'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-2">
                            <label class="form-label small mb-0">Apetito</label>
                            <textarea class="form-control form-control-sm" id="aa_apetito" rows="2" placeholder="Ej: Bueno / Variable / Bajo"><?= esc($aa['apetito'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="form-label small mb-0">Relación con la comida</label>
                            <textarea class="form-control form-control-sm" id="aa_relacion_comida" rows="2" placeholder="Ej: Come por ansiedad..."><?= esc($aa['relacion_comida'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-2">
                            <label class="form-label small mb-0">Historia de dietas restrictivas</label>
                            <textarea class="form-control form-control-sm" id="aa_dieta_restrictiva" rows="2" placeholder="Ej: Múltiples intentos, dieta keto 2024"><?= esc($aa['dieta_restrictiva'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-2">
                            <label class="form-label small mb-0">Historia de peso</label>
                            <textarea class="form-control form-control-sm" id="aa_historia_peso" rows="2" placeholder="Ej: Estable 5 años, sube en invierno"><?= esc($aa['historia_peso'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-2">
                            <label class="form-label small mb-0">Ansiedad con/sin comida</label>
                            <textarea class="form-control form-control-sm" id="aa_ansiedad_comida" rows="2" placeholder="Ej: Ansiedad en las tardes, picoteo nocturno"><?= esc($aa['ansiedad_comida'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="form-label small mb-0">Otros / notas</label>
                            <textarea class="form-control form-control-sm" id="aa_otros" rows="3" placeholder="Cualquier dato adicional"><?= esc($aa['otros'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Exámenes bioquímicos -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-info mb-3"><i class="fas fa-vial me-2"></i> Exámenes Bioquímicos</h6>
                            <p class="text-muted small">Nombre del examen, valor y fecha o interpretación.</p>
                            <input type="hidden" name="examenes_bioquimicos" id="examenes_bioquimicos_hidden" value="">
                            <div class="table-responsive mb-2">
                                <table class="table table-sm table-bordered" id="tablaExamenesBioquimicos">
                                    <thead class="table-light">
                                        <tr><th>Nombre</th><th>Valor</th><th>Fecha / Interpretación</th><th width="50"></th></tr>
                                    </thead>
                                    <tbody id="tbodyExamenesBioquimicos">
                                        <?php foreach ($examenes_bioquimicos ?? [] as $ex): ?>
                                        <tr class="fila-examen">
                                            <td><input type="text" class="form-control form-control-sm" name="examen_nombre[]" placeholder="Ej: Glicemia" value="<?= esc($ex->nombre ?? '') ?>"></td>
                                            <td><input type="text" class="form-control form-control-sm" name="examen_valor[]" placeholder="Ej: 95" value="<?= esc($ex->valor ?? '') ?>"></td>
                                            <td><input type="text" class="form-control form-control-sm" name="examen_fecha[]" placeholder="Ej: 15-01-2026 / Normal" value="<?= esc($ex->fecha_interpretacion ?? '') ?>"></td>
                                            <td><button type="button" class="btn btn-sm btn-outline-danger btn-quitar-examen" title="Quitar"><i class="fas fa-times"></i></button></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <tr class="fila-examen">
                                            <td><input type="text" class="form-control form-control-sm" name="examen_nombre[]" placeholder="Ej: Glicemia"></td>
                                            <td><input type="text" class="form-control form-control-sm" name="examen_valor[]" placeholder="Ej: 95"></td>
                                            <td><input type="text" class="form-control form-control-sm" name="examen_fecha[]" placeholder="Ej: 15-01-2026 / Normal"></td>
                                            <td><button type="button" class="btn btn-sm btn-outline-danger btn-quitar-examen" title="Quitar"><i class="fas fa-times"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarExamen"><i class="fas fa-plus me-1"></i> Agregar examen</button>
                        </div>
                    </div>

                    <!-- Tendencia de consumo -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-info mb-3"><i class="fas fa-apple-alt me-2"></i> Tendencia de Consumo / Preferencia alimentaria y Alergias</h6>
                            <p class="text-muted small mb-3">Completar preferencia y/o alergia/intolerancia por grupo.</p>
                            <input type="hidden" name="tendencia_consumo" id="tendencia_consumo" value="">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="tablaTendenciaConsumo">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="align-middle">Tendencia de consumo</th>
                                            <th colspan="1" class="text-center bg-success bg-opacity-25">Preferencia alimentaria</th>
                                            <th colspan="1" class="text-center bg-danger bg-opacity-25">Alergias / Intolerancias</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyTendenciaConsumo">
                                        <?php
                                        $tendencia_grupos = $tendencia_grupos ?? \App\Models\HistorialTendenciaConsumo::getGrupos();
                                        foreach ($tendencia_grupos as $slug => $etiqueta):
                                            $tc = $tendencia_consumo[$slug] ?? null;
                                            $pref = $tc ? ($tc->preferencia ?? '') : '';
                                            $alerg = $tc ? ($tc->alergia_intolerancia ?? '') : '';
                                        ?>
                                        <tr data-grupo="<?= esc($slug) ?>">
                                            <td class="align-middle fw-medium"><?= esc($etiqueta) ?></td>
                                            <td class="p-1 bg-success bg-opacity-10">
                                                <textarea class="form-control form-control-sm tc-preferencia" rows="2" placeholder="Ej: Alto / Bajo / 0" data-grupo="<?= esc($slug) ?>"><?= esc($pref) ?></textarea>
                                            </td>
                                            <td class="p-1 bg-danger bg-opacity-10">
                                                <textarea class="form-control form-control-sm tc-alergia" rows="2" placeholder="Ej: Sí / No / 0" data-grupo="<?= esc($slug) ?>"><?= esc($alerg) ?></textarea>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Recordatorio 24 h -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-info mb-3"><i class="fas fa-clock me-2"></i> Recordatorio 24 h</h6>
                            <p class="text-muted small">Desayuno, colación, almuerzo, once, cena: horarios y contenido.</p>
                        </div>
                        <div class="col-12 mb-3">
                            <textarea name="recordatorio_24h" id="recordatorio_24h" class="form-control" rows="5" placeholder="Ej: Desayuno 08:00: café con leche, pan integral..."><?= old('recordatorio_24h', $historial->recordatorio_24h ?? '') ?></textarea>
                        </div>
                    </div>

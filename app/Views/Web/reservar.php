<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<?php
$placeholderFoto = base_url('lib/src/assets/img/profile-30.png');
?>

<section class="py-5 reservar-hero">
    <div class="container">
        <h1 class="text-white text-center mb-2">Reservar hora</h1>
        <p class="text-white text-center opacity-90">Elija un profesional, luego las fechas y el horario disponible.</p>
    </div>
</section>

<section class="py-5" style="background: var(--bg-light);">
    <div class="container">
        <input type="hidden" id="empresa_id" value="<?= (int)($empresa_id ?? 0) ?>">
        <?= csrf_field() ?>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <?php if (empty($nutricionistas)): ?>
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5>No hay profesionales con disponibilidad</h5>
                        <p class="text-muted mb-0">En este momento no hay horarios publicados. Vuelva a intentar más tarde o contacte al consultorio.</p>
                    </div>
                </div>
                <?php else: ?>

                <!-- Paso 1: Elija un profesional (tarjetas con foto y nombre) -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">1. Elija un profesional</h5>
                        <div class="row g-3" id="nutricionistasGrid">
                            <div class="col-6 col-md-4 col-lg-2">
                                <div class="card nutricionista-card border h-100 cursor-pointer" data-id="" id="card-todos">
                                    <div class="card-body text-center p-3">
                                        <div class="nutricionista-foto-wrap mx-auto mb-2">
                                            <i class="fas fa-users fa-2x text-muted"></i>
                                        </div>
                                        <div class="small fw-semibold">Todos</div>
                                    </div>
                                </div>
                            </div>
                            <?php foreach ($nutricionistas as $n): 
                                $nombreCompleto = trim(($n->nombre ?? '') . ' ' . ($n->apellido ?? ''));
                                $fotoUrl = !empty($n->foto) ? base_url($n->foto) : $placeholderFoto;
                            ?>
                            <div class="col-6 col-md-4 col-lg-2">
                                <div class="card nutricionista-card border h-100 cursor-pointer" data-id="<?= (int)$n->id ?>" id="card-<?= (int)$n->id ?>">
                                    <div class="card-body text-center p-3">
                                        <div class="nutricionista-foto-wrap mx-auto mb-2">
                                            <img src="<?= esc($fotoUrl) ?>" alt="<?= esc($nombreCompleto) ?>" class="nutricionista-foto">
                                        </div>
                                        <div class="small fw-semibold text-break"><?= esc($nombreCompleto) ?></div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" id="nutricionista_id" value="">
                    </div>
                </div>

                <!-- Paso 2: Elija un día (visible al seleccionar profesional) -->
                <div class="card shadow-sm border-0 mb-4" id="cardFechas" style="display: none;">
                    <div class="card-body">
                        <h5 class="card-title mb-3">2. Elija el día</h5>
                        <p class="text-muted small mb-3">Seleccione el día y pulse Buscar para ver los horarios disponibles.</p>
                        <div class="row g-2 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label small mb-0">Día</label>
                                <input type="date" id="fecha_dia" class="form-control" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <label class="form-label small mb-0 d-md-block d-none">&nbsp;</label>
                                <a href="#" class="btn-buscar-horarios" id="btnBuscar" role="button">
                                    <i class="fas fa-search me-1"></i> Buscar horarios
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Días con slots (tabs) -->
                <div class="card shadow-sm border-0 mb-4" id="cardSlots" style="display: none;">
                    <div class="card-body">
                        <h5 class="card-title mb-2">3. Elija día y horario</h5>
                        <p class="text-muted small mb-3">Seleccione un día y luego el horario. Al hacer clic en "Reservar" complete sus datos.</p>
                        <ul class="nav nav-tabs mb-3" id="tabsDias" role="tablist" style="display: none;"></ul>
                        <div class="tab-content" id="panelesDias"></div>
                        <div id="slotsContainer">
                            <p class="text-muted mb-0">Elija el día y pulse Buscar horarios.</p>
                        </div>
                    </div>
                </div>

                <!-- Formulario de reserva -->
                <div class="card shadow-sm border-0 mb-4" id="cardFormulario" style="display: none;">
                    <div class="card-body">
                        <h5 class="card-title">Datos para la reserva</h5>
                        <p class="text-muted small mb-3">Horario elegido: <strong id="slotResumen"></strong></p>
                        <form id="formReserva">
                            <input type="hidden" name="detalle_agenda_id" id="detalle_agenda_id">
                            <div class="mb-3">
                                <label for="rut_dni" class="form-label">RUT <span class="text-danger">*</span></label>
                                <input type="text" name="rut_dni" id="rut_dni" class="form-control" placeholder="12.345.678-9" required maxlength="12">
                                <small class="text-muted">Formato: 12.345.678-9 (con o sin puntos y guión). Si ya está registrado se completarán nombre y teléfono.</small>
                                <div class="invalid-feedback" id="rutErrorInline">RUT inválido. Verifique el dígito verificador.</div>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" id="nombre" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
                                    <input type="text" name="apellido" id="apellido" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3 mt-2">
                                <label for="email" class="form-label">Correo <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" name="telefono" id="telefono" class="form-control" placeholder="+56 9 1234 5678">
                            </div>
                            <button type="submit" class="btn-confirmar-reserva" id="btnEnviarReserva">
                                <i class="fas fa-check me-1"></i> Confirmar reserva
                            </button>
                        </form>
                    </div>
                </div>

                <div class="alert alert-success shadow-sm border-0" id="mensajeExito" style="display: none;">
                    <i class="fas fa-check-circle me-2"></i>
                    <span id="mensajeExitoTexto"></span>
                </div>

                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
.reservar-hero {
    background: linear-gradient(135deg, var(--brand-green-primary) 0%, var(--brand-green-dark) 100%);
    padding: 80px 0 60px !important;
}
.nutricionista-card { transition: all 0.2s ease; }
.nutricionista-card:hover { border-color: var(--nutrinext-primary) !important; box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2); }
.nutricionista-card.selected { border-color: var(--nutrinext-primary) !important; background: rgba(34, 197, 94, 0.08); box-shadow: 0 0 0 2px var(--nutrinext-primary); }
.cursor-pointer { cursor: pointer; }
.nutricionista-foto-wrap {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    overflow: hidden;
    background: var(--nutrinext-muted);
    display: flex;
    align-items: center;
    justify-content: center;
}
.nutricionista-foto {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.text-break { word-break: break-word; }

/* Enlace “Buscar horarios” con aspecto de botón (sin ripple) */
a.btn-buscar-horarios {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--nutrinext-primary);
    color: #fff;
    border: 1px solid var(--nutrinext-primary);
    padding: 0.5rem 1.25rem;
    border-radius: 0.375rem;
    font-size: 1rem;
    font-weight: 500;
    text-decoration: none;
    white-space: nowrap;
    cursor: pointer;
    transition: background 0.2s, border-color 0.2s, color 0.2s;
}
a.btn-buscar-horarios:hover {
    background: var(--brand-green-dark);
    border-color: var(--brand-green-dark);
    color: #fff;
    text-decoration: none;
}
a.btn-buscar-horarios:focus {
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(34, 197, 94, 0.35);
}
a.btn-buscar-horarios .me-1 { margin-right: 0.35rem; }

/* Botón “Reservar” de cada horario (enlace sin .btn para evitar ripple) */
a.btn-reservar-slot {
    display: inline-block;
    background: var(--nutrinext-primary);
    color: #fff;
    border: 1px solid var(--nutrinext-primary);
    padding: 0.35rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: background 0.2s, border-color 0.2s;
}
a.btn-reservar-slot:hover {
    background: var(--brand-green-dark);
    border-color: var(--brand-green-dark);
    color: #fff;
    text-decoration: none;
}

/* Botón Confirmar reserva (sin .btn para evitar ripple) */
button.btn-confirmar-reserva {
    display: inline-flex;
    align-items: center;
    background: var(--nutrinext-primary);
    color: #fff;
    border: 1px solid var(--nutrinext-primary);
    padding: 0.5rem 1.25rem;
    border-radius: 0.375rem;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s, border-color 0.2s;
}
button.btn-confirmar-reserva:hover {
    background: var(--brand-green-dark);
    border-color: var(--brand-green-dark);
    color: #fff;
}
button.btn-confirmar-reserva .me-1 { margin-right: 0.35rem; }
</style>

<script>
(function() {
    var empresaId = document.getElementById('empresa_id').value;
    var slotSeleccionado = null;
    var diasSemana = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
    var meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

    /** Validador RUT chileno (módulo 11). Acepta con/sin puntos y guión. */
    function validarRutChileno(rut) {
        var s = (rut || '').trim().replace(/\./g, '').replace(/-/g, '').toUpperCase();
        if (s.length < 2) return false;
        var dv = s.slice(-1);
        var body = s.slice(0, -1);
        if (!/^\d+$/.test(body)) return false;
        if (!/^[\dK]$/.test(dv)) return false;
        var sum = 0, serie = [2, 3, 4, 5, 6, 7];
        for (var i = 0; i < body.length; i++) {
            sum += parseInt(body.charAt(body.length - 1 - i), 10) * serie[i % 6];
        }
        var rest = sum % 11;
        var expected = 11 - rest;
        if (expected === 11) expected = '0';
        else if (expected === 10) expected = 'K';
        else expected = String(expected);
        return expected === dv;
    }

    function formatearFechaParaTab(fechaStr) {
        if (!fechaStr) return fechaStr;
        var d;
        if (fechaStr.indexOf('-') !== -1) {
            var parts = fechaStr.split('-');
            if (parts[0].length === 4) d = new Date(parts[0], parts[1]-1, parts[2]);
            else d = new Date(parts[2], parts[1]-1, parts[0]);
        } else return fechaStr;
        if (isNaN(d.getTime())) return fechaStr;
        return diasSemana[d.getDay()] + ' ' + d.getDate() + ' ' + meses[d.getMonth()];
    }

    function normalizarFechaKey(fechaStr) {
        if (!fechaStr) return '';
        var parts = (fechaStr + '').trim().split('-');
        if (parts.length !== 3) return fechaStr;
        if (parts[0].length === 4) return parts[0] + '-' + parts[1] + '-' + parts[2];
        return parts[2] + '-' + parts[1] + '-' + parts[0];
    }

    function seleccionarNutricionista(card) {
        var id = card.getAttribute('data-id') || '';
        document.getElementById('nutricionista_id').value = id;
        document.querySelectorAll('.nutricionista-card').forEach(function(c) { c.classList.remove('selected'); });
        card.classList.add('selected');
        document.getElementById('cardFechas').style.display = 'block';
        document.getElementById('cardSlots').style.display = 'block';
            document.getElementById('slotsContainer').innerHTML = '<p class="text-muted mb-0">Elija el día y pulse Buscar horarios.</p>';
        document.getElementById('tabsDias').style.display = 'none';
        document.getElementById('panelesDias').innerHTML = '';
        document.getElementById('cardFormulario').style.display = 'none';
        document.getElementById('mensajeExito').style.display = 'none';
    }

    document.querySelectorAll('.nutricionista-card').forEach(function(card) {
        card.addEventListener('click', function() { seleccionarNutricionista(this); });
    });

    // Preselección desde /equipo o enlace directo ?nutricionista_id=
    var paramsUrl = new URLSearchParams(window.location.search);
    var preNutricionistaId = paramsUrl.get('nutricionista_id');
    if (preNutricionistaId) {
        var cardPre = document.getElementById('card-' + preNutricionistaId);
        if (cardPre) {
            seleccionarNutricionista(cardPre);
        }
    } else {
        // Si solo hay un nutricionista (además de "Todos"), auto-seleccionar el primero
        var cards = document.querySelectorAll('.nutricionista-card[data-id]');
        if (cards.length === 1) {
            seleccionarNutricionista(cards[0]);
        }
    }

    function fechaHoyYmd() {
        var d = new Date();
        var m = String(d.getMonth() + 1).padStart(2, '0');
        var day = String(d.getDate()).padStart(2, '0');
        return d.getFullYear() + '-' + m + '-' + day;
    }

    function esFechaPasada(fechaYmd) {
        return fechaYmd && fechaYmd < fechaHoyYmd();
    }

    function buscarSlots() {
        var nutricionistaIdEl = document.getElementById('nutricionista_id');
        var nutricionistaId = nutricionistaIdEl ? nutricionistaIdEl.value : '';
        var fechaDia = document.getElementById('fecha_dia').value;
        if (!fechaDia) {
            document.getElementById('slotsContainer').innerHTML = '<p class="text-muted mb-0">Seleccione un día.</p>';
            return;
        }
        if (esFechaPasada(fechaDia)) {
            document.getElementById('slotsContainer').innerHTML = '<p class="text-danger mb-0">No puede reservar en fechas pasadas. Elija hoy o una fecha futura.</p>';
            return;
        }
        var container = document.getElementById('slotsContainer');
        var tabsEl = document.getElementById('tabsDias');
        var panelesEl = document.getElementById('panelesDias');
        container.innerHTML = '<p class="text-muted mb-0"><i class="fas fa-spinner fa-spin me-1"></i> Cargando...</p>';
        tabsEl.style.display = 'none';
        panelesEl.innerHTML = '';

        var url = '<?= base_url('reservar/disponibilidad') ?>' + '?fecha=' + encodeURIComponent(fechaDia);
        if (nutricionistaId) url += '&nutricionista_id=' + encodeURIComponent(nutricionistaId);

        fetch(url).then(function(r) { return r.json(); }).then(function(data) {
            if (!data.slots || !data.slots.length) {
                container.innerHTML = '<p class="text-muted mb-0">No hay horarios para ese día. Elija otro día.</p>';
                return;
            }
            container.innerHTML = '';
            var porDia = {};
            data.slots.forEach(function(s) {
                var fecha = s.fecha || s.fecha_agenda || '';
                var key = normalizarFechaKey(fecha);
                if (!porDia[key]) porDia[key] = { label: formatearFechaParaTab(fecha), slots: [] };
                porDia[key].slots.push(s);
            });
            var fechasOrdenadas = Object.keys(porDia).sort();
            if (fechasOrdenadas.length === 0) {
                container.innerHTML = '<p class="text-muted mb-0">No hay horarios disponibles.</p>';
                return;
            }
            tabsEl.style.display = 'flex';
            tabsEl.innerHTML = '';
            panelesEl.innerHTML = '';
            fechasOrdenadas.forEach(function(key, idx) {
                var dia = porDia[key];
                var tabId = 'tab-' + key.replace(/-/g, '');
                var paneId = tabId + '-pane';
                var activeClass = idx === 0 ? ' active' : '';
                var li = document.createElement('li');
                li.className = 'nav-item';
                li.innerHTML = '<button class="nav-link tab-dia-btn' + activeClass + '" id="' + tabId + '-tab" type="button" role="tab" data-pane-id="' + paneId + '">' + dia.label + '</button>';
                tabsEl.appendChild(li);
                var panel = document.createElement('div');
                panel.className = 'tab-pane fade' + (idx === 0 ? ' show active' : '');
                panel.id = paneId;
                panel.setAttribute('role', 'tabpanel');
                var listHtml = '<div class="list-group list-group-flush">';
                dia.slots.forEach(function(s) {
                    var fecha = s.fecha || s.fecha_agenda || '';
                    var horaIni = s.hora_inicio ? (typeof s.hora_inicio === 'string' ? s.hora_inicio.substring(0,5) : s.hora_inicio) : '';
                    var horaFin = s.hora_fin ? (typeof s.hora_fin === 'string' ? s.hora_fin.substring(0,5) : s.hora_fin) : '';
                    var nut = (s.nutricionista_nombre || '') + ' ' + (s.nutricionista_apellido || '');
                    listHtml += '<div class="list-group-item d-flex justify-content-between align-items-center">';
                    listHtml += '<span>' + horaIni + ' - ' + horaFin + (nut ? ' <small class="text-muted">(' + nut.trim() + ')</small>' : '') + '</span>';
                    listHtml += '<a href="#" class="btn-reservar-slot" data-id="' + s.id + '" data-fecha="' + (fecha || dia.label) + '" data-hora="' + horaIni + '" role="button">Reservar</a>';
                    listHtml += '</div>';
                });
                listHtml += '</div>';
                panel.innerHTML = listHtml;
                panelesEl.appendChild(panel);
            });

            document.querySelectorAll('#tabsDias .tab-dia-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var paneId = this.getAttribute('data-pane-id');
                    if (!paneId) return;
                    document.querySelectorAll('#tabsDias .tab-dia-btn').forEach(function(b) { b.classList.remove('active'); });
                    document.querySelectorAll('#panelesDias .tab-pane').forEach(function(p) { p.classList.remove('show', 'active'); });
                    this.classList.add('active');
                    var pane = document.getElementById(paneId);
                    if (pane) { pane.classList.add('show', 'active'); }
                });
            });
        }).catch(function() {
            if (document.getElementById('slotsContainer')) document.getElementById('slotsContainer').innerHTML = '<p class="text-danger mb-0">Error al cargar. Intente de nuevo.</p>';
        });
    }

    document.getElementById('btnBuscar').addEventListener('click', function(e) {
        e.preventDefault();
        buscarSlots();
    });

    function onReservarSlotClick(btn) {
        if (!btn) return;
        slotSeleccionado = { id: btn.dataset.id, fecha: btn.dataset.fecha, hora: btn.dataset.hora };
        document.getElementById('detalle_agenda_id').value = slotSeleccionado.id;
        document.getElementById('slotResumen').textContent = slotSeleccionado.fecha + ' a las ' + slotSeleccionado.hora;
        document.getElementById('cardFormulario').style.display = 'block';
        document.getElementById('mensajeExito').style.display = 'none';
        document.getElementById('cardFormulario').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    document.getElementById('slotsContainer').addEventListener('click', function(e) {
        var btn = e.target.closest('.btn-reservar-slot');
        if (btn) { e.preventDefault(); onReservarSlotClick(btn); }
    });

    document.getElementById('panelesDias').addEventListener('click', function(e) {
        var btn = e.target.closest('.btn-reservar-slot');
        if (btn) { e.preventDefault(); onReservarSlotClick(btn); }
    });

    var rutInput = document.getElementById('rut_dni');
    rutInput.addEventListener('blur', function() {
        var rut = this.value.trim();
        if (rut.length > 0) {
            if (!validarRutChileno(rut)) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        } else {
            this.classList.remove('is-invalid');
        }
        if (rut.length < 8) return;
        var url = '<?= base_url('reservar/paciente-por-rut') ?>?rut=' + encodeURIComponent(rut);
        fetch(url).then(function(r) { return r.json(); }).then(function(data) {
            if (data.found) {
                document.getElementById('nombre').value = data.nombre || '';
                document.getElementById('apellido').value = data.apellido || '';
                document.getElementById('telefono').value = data.telefono || '';
                document.getElementById('email').value = data.email || '';
                document.getElementById('nombre').readOnly = true;
                document.getElementById('apellido').readOnly = true;
                document.getElementById('telefono').readOnly = true;
            } else {
                document.getElementById('nombre').readOnly = false;
                document.getElementById('apellido').readOnly = false;
                document.getElementById('telefono').readOnly = false;
            }
        });
    });

    rutInput.addEventListener('input', function() {
        if (this.classList.contains('is-invalid') && validarRutChileno(this.value.trim())) {
            this.classList.remove('is-invalid');
        }
    });

    document.getElementById('formReserva').addEventListener('submit', function(e) {
        e.preventDefault();
        var rutVal = document.getElementById('rut_dni').value.trim();
        if (!validarRutChileno(rutVal)) {
            document.getElementById('rut_dni').classList.add('is-invalid');
            document.getElementById('rut_dni').focus();
            return;
        }
        document.getElementById('rut_dni').classList.remove('is-invalid');
        var btn = document.getElementById('btnEnviarReserva');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Enviando...';
        var formData = new FormData(this);
        formData.append('csrf_test_name', document.querySelector('input[name="csrf_test_name"]').value);
        fetch('<?= base_url('reservar/reservar') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function(r) { return r.json(); }).then(function(data) {
            if (data.success) {
                document.getElementById('cardFormulario').style.display = 'none';
                document.getElementById('mensajeExitoTexto').textContent = data.message || 'Reserva recibida.';
                document.getElementById('mensajeExito').style.display = 'block';
                buscarSlots();
            } else {
                alert(data.error || 'Error al reservar.');
            }
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check me-1"></i> Confirmar reserva';
        }).catch(function() {
            alert('Error de conexión. Intente de nuevo.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check me-1"></i> Confirmar reserva';
        });
    });
})();
</script>

<?= $this->endSection() ?>

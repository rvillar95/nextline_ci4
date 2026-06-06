(function () {
    'use strict';

    var root = document.getElementById('nnEntrenarApp');
    if (!root) return;

    var entrenamientoId = root.dataset.entrenamientoId;
    var baseUrl = (document.body.dataset.baseUrl || '/').replace(/\/?$/, '/');
    var csrfName = root.dataset.csrfName || (window.NutriNextCsrf ? NutriNextCsrf.getName() : 'csrf_test_name');

    function getCsrf() {
        return window.NutriNextCsrf ? NutriNextCsrf.getToken() : (document.querySelector('meta[name="csrf-token"]') || {}).content || root.dataset.csrfHash || '';
    }

    var panels = Array.prototype.slice.call(root.querySelectorAll('.nn-ejercicio-panel'));
    var jumpChips = Array.prototype.slice.call(root.querySelectorAll('.nn-ej-jump-chip'));
    var progressBar = document.getElementById('nnEntrenarProgressBar');
    var labelEj = document.getElementById('nnEntrenarEjLabel');
    var restTimer = document.getElementById('nnRestTimer');
    var timerDisplay = document.getElementById('nnTimerDisplay');
    var timerLabel = document.getElementById('nnRestTimerLabel');
    var timerBar = document.getElementById('nnRestTimerBar');
    var btnSkipTimer = document.getElementById('nnBtnSkipTimer');
    var btnRestPlus = document.getElementById('nnBtnRestPlus');
    var btnRestMinus = document.getElementById('nnBtnRestMinus');
    var REST_STEP = 15;
    var timerInterval = null;
    var timerMode = 'down';
    var timerLeft = 0;
    var timerElapsed = 0;
    var timerTotal = 0;
    var saving = {};
    var savingSensacion = {};
    var prHideTimer = null;
    var scrollAnimFrame = null;

    function getScrollOffset() {
        var topbar = 56;
        try {
            var v = getComputedStyle(document.documentElement).getPropertyValue('--nn-alumno-top-h').trim();
            if (v) topbar = parseFloat(v) || topbar;
        } catch (e) { /* ignore */ }
        var jumpNav = document.querySelector('.nn-ejercicio-jump');
        var jumpH = jumpNav ? jumpNav.getBoundingClientRect().height : 48;
        return topbar + jumpH + 12;
    }

    function easeInOutCubic(t) {
        return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
    }

    function smoothScrollTo(y, duration, onDone) {
        if (scrollAnimFrame) {
            cancelAnimationFrame(scrollAnimFrame);
            scrollAnimFrame = null;
        }

        var startY = window.pageYOffset || document.documentElement.scrollTop;
        var distance = y - startY;
        if (Math.abs(distance) < 2) {
            if (typeof onDone === 'function') onDone();
            return;
        }

        duration = duration || Math.min(700, Math.max(380, Math.abs(distance) * 0.45));
        var startTime = null;

        function step(ts) {
            if (startTime === null) startTime = ts;
            var elapsed = ts - startTime;
            var progress = Math.min(elapsed / duration, 1);
            window.scrollTo(0, startY + distance * easeInOutCubic(progress));
            if (progress < 1) {
                scrollAnimFrame = requestAnimationFrame(step);
            } else {
                scrollAnimFrame = null;
                if (typeof onDone === 'function') onDone();
            }
        }

        scrollAnimFrame = requestAnimationFrame(step);
    }

    function pulseEjercicioPanel(el) {
        if (!el) return;
        el.classList.remove('is-jump-target');
        void el.offsetWidth;
        el.classList.add('is-jump-target');
        setTimeout(function () {
            el.classList.remove('is-jump-target');
        }, 900);
    }

    function formatTime(secs) {
        secs = Math.max(0, Math.floor(secs));
        var m = Math.floor(secs / 60);
        var s = secs % 60;
        return m + ':' + (s < 10 ? '0' : '') + s;
    }

    function stopTimer() {
        clearInterval(timerInterval);
        timerInterval = null;
        if (restTimer) {
            restTimer.hidden = true;
            restTimer.classList.remove('is-visible', 'is-overtime');
        }
        document.body.classList.remove('nn-rest-active');
    }

    function updateTimerDisplay() {
        if (!timerDisplay) return;
        if (timerMode === 'down') {
            timerDisplay.textContent = formatTime(timerLeft);
            if (timerLabel) timerLabel.textContent = 'Descanso';
            if (timerBar && timerTotal > 0) {
                timerBar.style.width = Math.max(0, (timerLeft / timerTotal) * 100) + '%';
            }
        } else {
            timerDisplay.textContent = '+' + formatTime(timerElapsed);
            if (timerLabel) timerLabel.textContent = 'Tiempo extra';
        }
    }

    function startTimer(seg) {
        if (!restTimer || !timerDisplay || seg <= 0) return;

        stopTimer();
        timerMode = 'down';
        timerTotal = seg;
        timerLeft = seg;
        timerElapsed = 0;

        restTimer.hidden = false;
        restTimer.classList.remove('is-overtime');
        document.body.classList.add('nn-rest-active');
        updateTimerDisplay();

        requestAnimationFrame(function () {
            restTimer.classList.add('is-visible');
        });

        timerInterval = setInterval(function () {
            if (timerMode === 'down') {
                timerLeft -= 1;
                if (timerLeft <= 0) {
                    timerMode = 'up';
                    timerElapsed = 0;
                    restTimer.classList.add('is-overtime');
                    if (timerLabel) timerLabel.textContent = '¡A entrenar!';
                    if (typeof navigator !== 'undefined' && navigator.vibrate) {
                        navigator.vibrate([120, 60, 120]);
                    }
                }
            } else {
                timerElapsed += 1;
            }
            updateTimerDisplay();
        }, 1000);
    }

    function adjustRestTimer(delta) {
        if (!restTimer || restTimer.hidden || !timerInterval) return;

        if (timerMode === 'down') {
            if (delta > 0) {
                timerLeft += delta;
                timerTotal += delta;
            } else {
                timerLeft = Math.max(0, timerLeft + delta);
                if (timerLeft <= 0) {
                    timerMode = 'up';
                    timerElapsed = 0;
                    restTimer.classList.add('is-overtime');
                }
            }
        } else {
            if (delta > 0) {
                timerMode = 'down';
                timerLeft = delta;
                timerTotal = delta;
                timerElapsed = 0;
                restTimer.classList.remove('is-overtime');
            } else {
                timerElapsed = Math.max(0, timerElapsed + delta);
                if (timerElapsed <= 0) {
                    restTimer.classList.remove('is-overtime');
                }
            }
        }
        updateTimerDisplay();
    }

    function showPrCelebration(json, serieRow) {
        var toast = document.getElementById('nnPrToast');
        if (!toast || !json) return;

        var nombre = json.ejercicio_nombre || 'este ejercicio';
        var peso = json.peso_nuevo != null ? json.peso_nuevo + ' kg' : '';
        var antes = json.peso_anterior != null ? 'Antes: ' + json.peso_anterior + ' kg' : '';
        var reps = json.repeticiones ? ' × ' + json.repeticiones + ' reps' : '';

        toast.innerHTML =
            '<div class="nn-pr-toast__icon"><i class="fas fa-trophy"></i></div>' +
            '<div class="nn-pr-toast__body">' +
                '<div class="nn-pr-toast__title">¡Nuevo récord!</div>' +
                '<div class="nn-pr-toast__detail">' + nombre +
                    (peso ? ' · <span class="nn-pr-toast__weight">' + peso + reps + '</span>' : '') +
                    (antes ? '<br>' + antes : '') +
                '</div>' +
            '</div>';

        toast.hidden = false;
        toast.classList.remove('is-hiding');
        requestAnimationFrame(function () {
            toast.classList.add('is-visible');
        });

        var sparks = [-1, 1, -1.5, 1.5, 0];
        sparks.forEach(function (dir, i) {
            var s = document.createElement('span');
            s.className = 'nn-pr-toast__spark';
            s.style.left = (50 + dir * 18) + '%';
            s.style.top = '50%';
            s.style.setProperty('--sx', (dir * 12) + 'px');
            s.style.setProperty('--sy', (-20 - i * 6) + 'px');
            s.style.animationDelay = (i * 0.05) + 's';
            toast.appendChild(s);
            setTimeout(function () { s.remove(); }, 1000);
        });

        if (serieRow) {
            serieRow.classList.remove('is-pr-flash');
            void serieRow.offsetWidth;
            serieRow.classList.add('is-pr-flash');
            setTimeout(function () { serieRow.classList.remove('is-pr-flash'); }, 1200);
        }

        clearTimeout(prHideTimer);
        prHideTimer = setTimeout(function () {
            toast.classList.add('is-hiding');
            toast.classList.remove('is-visible');
            setTimeout(function () {
                toast.hidden = true;
                toast.classList.remove('is-hiding');
            }, 320);
        }, 3800);
    }

    function updateSessionProgress() {
        var rows = root.querySelectorAll('.nn-serie-row');
        var total = rows.length;
        var done = 0;
        rows.forEach(function (row) {
            if (isSerieDone(row)) done += 1;
        });

        if (progressBar && total > 0) {
            progressBar.style.width = Math.round((done / total) * 100) + '%';
        }
        if (labelEj) {
            labelEj.textContent = done + ' / ' + total + ' series';
        }

        panels.forEach(function (panel) {
            var panelRows = panel.querySelectorAll('.nn-serie-row');
            var panelDone = 0;
            panelRows.forEach(function (r) {
                if (isSerieDone(r)) panelDone += 1;
            });
            var idx = panel.getAttribute('data-index');
            var badge = panel.querySelector('[data-ej-badge]');
            if (badge) {
                badge.textContent = panelDone + '/' + panelRows.length;
            }
            panel.classList.toggle('is-all-done', panelRows.length > 0 && panelDone >= panelRows.length);

            var chip = jumpChips.find(function (c) { return c.dataset.target === panel.id; });
            if (chip) {
                chip.dataset.done = String(panelDone);
                chip.classList.toggle('is-complete', panelRows.length > 0 && panelDone >= panelRows.length);
                var existingCheck = chip.querySelector('.nn-ej-jump-chip__check');
                if (panelDone >= panelRows.length && panelRows.length > 0) {
                    if (!existingCheck) {
                        var ic = document.createElement('i');
                        ic.className = 'fas fa-check nn-ej-jump-chip__check';
                        ic.setAttribute('aria-hidden', 'true');
                        chip.appendChild(ic);
                    }
                } else if (existingCheck) {
                    existingCheck.remove();
                }
            }
        });
    }

    function scrollToEjercicio(id, chipEl) {
        var el = document.getElementById(id);
        if (!el) return;

        jumpChips.forEach(function (c) {
            c.classList.toggle('is-current', c.dataset.target === id);
        });

        if (chipEl && chipEl.scrollIntoView) {
            chipEl.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        }

        var targetY = el.getBoundingClientRect().top + (window.pageYOffset || 0) - getScrollOffset();
        targetY = Math.max(0, targetY);

        smoothScrollTo(targetY, null, function () {
            pulseEjercicioPanel(el);
        });
    }

    function bindJumpNav() {
        jumpChips.forEach(function (chip) {
            chip.addEventListener('click', function () {
                scrollToEjercicio(chip.dataset.target, chip);
            });
        });

        if (typeof IntersectionObserver === 'undefined' || panels.length === 0) {
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                var id = entry.target.id;
                jumpChips.forEach(function (c) {
                    c.classList.toggle('is-current', c.dataset.target === id);
                });
            });
        }, {
            root: null,
            rootMargin: '-' + (56 + 64) + 'px 0px -55% 0px',
            threshold: 0,
        });

        panels.forEach(function (p) { observer.observe(p); });
    }

    function setSensacionStatus(eeId, msg, isError) {
        var el = root.querySelector('[data-sensacion-status="' + eeId + '"]');
        if (!el) return;
        el.textContent = msg || '';
        el.classList.toggle('is-error', !!isError);
        el.classList.toggle('is-saved', !!msg && !isError);
    }

    function postSensacion(eeId, text, cb) {
        if (savingSensacion[eeId]) return;
        savingSensacion[eeId] = true;
        setSensacionStatus(eeId, 'Guardando…', false);

        var body = new FormData();
        body.append('entrenamiento_ejercicio_id', eeId);
        body.append('sensacion', text);
        body.append(csrfName, getCsrf());

        fetch(baseUrl + 'alumno/entrenamiento/' + entrenamientoId + '/ejercicio-sensacion', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrf(),
            },
            body: body,
        })
            .then(function (r) { return r.json(); })
            .then(function (json) {
                if (window.NutriNextCsrf) {
                    NutriNextCsrf.applyFromJson(json);
                }
                if (!json || !json.ok) {
                    setSensacionStatus(eeId, 'No se guardó', true);
                } else {
                    setSensacionStatus(eeId, 'Guardado', false);
                    setTimeout(function () {
                        var st = root.querySelector('[data-sensacion-status="' + eeId + '"]');
                        if (st && st.textContent === 'Guardado') {
                            st.textContent = '';
                            st.classList.remove('is-saved');
                        }
                    }, 2000);
                }
                if (typeof cb === 'function') cb(json);
            })
            .catch(function () {
                setSensacionStatus(eeId, 'Error de conexión', true);
                if (typeof cb === 'function') cb({ ok: false });
            })
            .finally(function () {
                savingSensacion[eeId] = false;
            });
    }

    function bindSensacion() {
        root.querySelectorAll('.nn-inp-sensacion').forEach(function (ta) {
            var eeId = ta.dataset.eeId;
            if (!eeId) return;
            var lastSent = ta.value;

            function saveSensacion() {
                if (ta.value === lastSent) return;
                lastSent = ta.value;
                postSensacion(eeId, ta.value);
            }

            ta.addEventListener('blur', saveSensacion);
            ta.addEventListener('change', saveSensacion);
        });
    }

    function flushSensaciones() {
        var jobs = [];
        root.querySelectorAll('.nn-inp-sensacion').forEach(function (ta) {
            var eeId = ta.dataset.eeId;
            if (!eeId) return;
            jobs.push(new Promise(function (resolve) {
                postSensacion(eeId, ta.value, resolve);
            }));
        });
        return jobs.length ? Promise.all(jobs) : Promise.resolve();
    }

    function postSerie(serieId, data, cb, opts) {
        opts = opts || {};
        if (saving[serieId]) return;
        saving[serieId] = true;

        var body = new FormData();
        body.append('serie_id', serieId);
        body.append(csrfName, getCsrf());
        if (data.peso_kg !== undefined) body.append('peso_kg', data.peso_kg);
        if (data.repeticiones !== undefined) body.append('repeticiones', data.repeticiones);
        if (data.completada !== undefined) body.append('completada', data.completada ? '1' : '0');

        fetch(baseUrl + 'alumno/entrenamiento/' + entrenamientoId + '/serie', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrf(),
            },
            body: body,
        })
            .then(function (r) { return r.json(); })
            .then(function (json) {
                if (window.NutriNextCsrf) {
                    NutriNextCsrf.applyFromJson(json);
                }
                if (!json || !json.ok) {
                    if (typeof alertar === 'function') {
                        alertar((json && json.error) || 'No se pudo guardar la serie', 'error');
                    }
                } else if (json.pr && !opts.silent) {
                    showPrCelebration(json, opts.serieRow || null);
                }
                updateSessionProgress();
                if (typeof cb === 'function') cb(json);
            })
            .catch(function () {
                if (typeof alertar === 'function') {
                    alertar('Error de conexión al guardar', 'error');
                }
                if (typeof cb === 'function') cb({ ok: false });
            })
            .finally(function () {
                saving[serieId] = false;
            });
    }

    function isSerieDone(row) {
        return row.classList.contains('is-done');
    }

    function setSerieDone(row, btn, done) {
        row.classList.toggle('is-done', done);
        if (btn) {
            btn.setAttribute('aria-pressed', done ? 'true' : 'false');
            btn.setAttribute('aria-label', done
                ? btn.getAttribute('data-label-done') || 'Serie hecha — tocar para deshacer'
                : btn.getAttribute('data-label-pending') || 'Marcar serie como hecha');
        }
    }

    function bindSeries() {
        root.querySelectorAll('.nn-serie-row').forEach(function (row) {
            var serieId = row.dataset.serieId;
            var descanso = parseInt(row.dataset.descanso || '0', 10);
            var peso = row.querySelector('.nn-inp-peso');
            var reps = row.querySelector('.nn-inp-reps');
            var btnDone = row.querySelector('.nn-btn-serie-done');

            if (btnDone) {
                var num = row.querySelector('.nn-serie-row__num');
                var n = num ? num.textContent.trim() : '';
                btnDone.setAttribute('data-label-pending', 'Marcar serie ' + n + ' como hecha');
                btnDone.setAttribute('data-label-done', 'Serie ' + n + ' hecha — tocar para deshacer');
            }

            function save(opts) {
                opts = opts || {};
                var tienePeso = peso && peso.value !== '';
                var tieneReps = reps && reps.value !== '';
                var marcada = isSerieDone(row);
                postSerie(serieId, {
                    peso_kg: peso ? peso.value : '',
                    repeticiones: reps ? reps.value : '',
                    completada: marcada || tienePeso || tieneReps,
                }, opts.callback || null, Object.assign({ serieRow: row }, opts));
            }

            if (peso) {
                peso.addEventListener('change', function () { save(); });
                peso.addEventListener('blur', function () { save(); });
            }
            if (reps) {
                reps.addEventListener('change', function () { save(); });
                reps.addEventListener('blur', function () { save(); });
            }
            if (btnDone) {
                btnDone.addEventListener('click', function () {
                    var wasDone = isSerieDone(row);
                    var nowDone = !wasDone;
                    setSerieDone(row, btnDone, nowDone);
                    updateSessionProgress();
                    save({
                        callback: function (json) {
                            if (json && json.ok && nowDone && descanso > 0) {
                                startTimer(descanso);
                            }
                        },
                    });
                });
            }
        });
    }

    if (btnSkipTimer) {
        btnSkipTimer.addEventListener('click', stopTimer);
    }

    if (btnRestPlus) {
        btnRestPlus.addEventListener('click', function () {
            adjustRestTimer(REST_STEP);
        });
    }

    if (btnRestMinus) {
        btnRestMinus.addEventListener('click', function () {
            adjustRestTimer(-REST_STEP);
        });
    }

    function flushAllSeries() {
        var jobs = [];
        root.querySelectorAll('.nn-serie-row').forEach(function (row) {
            var serieId = row.dataset.serieId;
            var peso = row.querySelector('.nn-inp-peso');
            var reps = row.querySelector('.nn-inp-reps');
            var tienePeso = peso && peso.value !== '';
            var tieneReps = reps && reps.value !== '';
            var marcada = isSerieDone(row);
            if (!tienePeso && !tieneReps && !marcada) {
                return;
            }
            jobs.push(new Promise(function (resolve) {
                postSerie(serieId, {
                    peso_kg: peso ? peso.value : '',
                    repeticiones: reps ? reps.value : '',
                    completada: marcada || tienePeso || tieneReps,
                }, resolve, { silent: true });
            }));
        });
        return jobs.length ? Promise.all(jobs) : Promise.resolve();
    }

    var formFinalizar = document.getElementById('nnFormFinalizar');
    if (formFinalizar) {
        formFinalizar.addEventListener('submit', function (e) {
            if (formFinalizar.dataset.nnFlushed === '1') {
                formFinalizar.dataset.nnFlushed = '0';
                return;
            }
            e.preventDefault();
            var btn = formFinalizar.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
            flushAllSeries().then(function () {
                return flushSensaciones();
            }).finally(function () {
                if (btn) btn.disabled = false;
                formFinalizar.dataset.nnFlushed = '1';
                if (typeof formFinalizar.requestSubmit === 'function') {
                    formFinalizar.requestSubmit();
                } else {
                    formFinalizar.submit();
                }
            });
        });
    }

    bindSeries();
    bindSensacion();
    bindJumpNav();
    updateSessionProgress();
    if (jumpChips.length) {
        jumpChips[0].classList.add('is-current');
    }
})();

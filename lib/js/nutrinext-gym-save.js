/**
 * POST JSON para pantallas Gym (programa/rutina editar).
 */
(function (window) {
    'use strict';

    async function gymPostJson(url, body, csrfRef) {
        var token = window.NutriNextCsrf ? NutriNextCsrf.getToken() : (csrfRef && csrfRef.token) || '';
        var res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify(body || {}),
        });

        var json = null;
        var ct = res.headers.get('content-type') || '';
        if (ct.indexOf('application/json') !== -1) {
            json = await res.json().catch(function () { return null; });
        } else {
            var text = await res.text().catch(function () { return ''; });
            if (res.status === 403) {
                return {
                    ok: false,
                    error: 'Sesión o token expirado. Recarga la página (F5) e intenta de nuevo.',
                    json: null,
                    res: res,
                };
            }
            return {
                ok: false,
                error: 'Error del servidor (' + res.status + '). ' + (text ? text.substring(0, 120) : ''),
                json: null,
                res: res,
            };
        }

        var newToken = (json && json.csrf_token)
            || res.headers.get('X-CSRF-TOKEN')
            || res.headers.get('X-CSRF-Hash');
        if (newToken) {
            if (window.NutriNextCsrf) {
                NutriNextCsrf.setToken(newToken);
            }
            if (csrfRef) {
                csrfRef.token = newToken;
            }
        }

        if (!res.ok || !json || json.success !== true) {
            return {
                ok: false,
                error: (json && json.error) ? json.error : ('Error al guardar (' + res.status + ')'),
                json: json,
                res: res,
            };
        }

        return { ok: true, json: json, res: res };
    }

    function gymNotify(msg, type) {
        if (typeof nnNotify === 'function') {
            nnNotify(msg, type || 'info');
        } else if (typeof alert === 'function') {
            alert(msg);
        }
    }

    window.NutriNextGym = {
        postJson: gymPostJson,
        notify: gymNotify,
    };
})(window);

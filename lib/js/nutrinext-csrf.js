/**
 * CSRF centralizado — evita que botones AJAX fallen al segundo clic (token regenerado).
 */
(function (window) {
    'use strict';

    var cachedName = null;

    function metaEl() {
        return document.querySelector('meta[name="csrf-token"]');
    }

    function detectName() {
        if (cachedName) {
            return cachedName;
        }
        var inp = document.querySelector('input[name*="csrf"]');
        cachedName = inp ? inp.name : 'csrf_test_name';
        return cachedName;
    }

    function getToken() {
        var m = metaEl();
        return m ? m.getAttribute('content') || '' : '';
    }

    function setToken(token) {
        if (!token) {
            return;
        }
        var m = metaEl();
        if (m) {
            m.setAttribute('content', token);
        }
        var name = detectName();
        document.querySelectorAll('input[name="' + name + '"]').forEach(function (el) {
            el.value = token;
        });
        window.dispatchEvent(new CustomEvent('nutrinext:csrf-updated', { detail: { token: token } }));
    }

    function applyFromJson(json) {
        if (json && json.csrf_token) {
            setToken(json.csrf_token);
        }
    }

    function applyFromResponse(response, json) {
        if (json) {
            applyFromJson(json);
        }
        if (response && response.headers) {
            var h = response.headers.get('X-CSRF-TOKEN') || response.headers.get('X-CSRF-Hash');
            if (h) {
                setToken(h);
            }
        }
    }

    /** fetch POST JSON con CSRF; actualiza token automáticamente */
    function postJson(url, body) {
        var token = getToken();
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify(body || {}),
        }).then(function (response) {
            return response.json().catch(function () { return null; }).then(function (json) {
                applyFromResponse(response, json);
                return { response: response, json: json };
            });
        });
    }

    /** fetch POST FormData con CSRF */
    function postForm(url, formData) {
        var token = getToken();
        var name = detectName();
        if (formData && !formData.has(name)) {
            formData.append(name, token);
        }
        return fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token,
            },
            body: formData,
        }).then(function (response) {
            return response.json().catch(function () { return null; }).then(function (json) {
                applyFromResponse(response, json);
                return { response: response, json: json };
            });
        });
    }

    window.NutriNextCsrf = {
        getToken: getToken,
        getName: detectName,
        setToken: setToken,
        applyFromJson: applyFromJson,
        applyFromResponse: applyFromResponse,
        postJson: postJson,
        postForm: postForm,
    };
})(window);

/**
 * Header NutriNext: evita clone/smaller de designesia.js (salto brusco)
 * y usa is-scrolled con transición CSS lenta.
 */
(function (window) {
    'use strict';

    var SCROLL_THRESHOLD = 48;
    var DESIGNESIA_HEADER_CLASSES = ['clone', 'smaller', 'scrollOn', 'scrollOff', 'logo-smaller'];

    function getHeader() {
        return document.querySelector('header.header-modern');
    }

    function stripDesignesiaHeaderClasses(header) {
        if (!header) {
            return;
        }
        DESIGNESIA_HEADER_CLASSES.forEach(function (cls) {
            header.classList.remove(cls);
        });
    }

    function updateHeader() {
        var header = getHeader();
        if (!header) {
            return;
        }

        stripDesignesiaHeaderClasses(header);
        header.classList.toggle('is-scrolled', window.scrollY > SCROLL_THRESHOLD);
        document.dispatchEvent(new CustomEvent('nutrinext:header-update', { detail: { header: header } }));
    }

    function init() {
        var header = getHeader();
        if (!header) {
            return;
        }

        stripDesignesiaHeaderClasses(header);

        var observer = new MutationObserver(function () {
            stripDesignesiaHeaderClasses(header);
        });
        observer.observe(header, { attributes: true, attributeFilter: ['class'] });

        var ticking = false;
        window.addEventListener('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(function () {
                    updateHeader();
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });

        updateHeader();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})(window);

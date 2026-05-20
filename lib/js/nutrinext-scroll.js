/**
 * Scroll suave lento para el sitio público NutriNext.
 * Reemplaza scroll-behavior: smooth (demasiado rápido y sin control de duración).
 */
(function (window) {
    'use strict';

    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function easeInOutCubic(t) {
        return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
    }

    function getHeaderOffset() {
        var header = document.querySelector('.header-modern');
        return (header ? header.offsetHeight : 100) + 16;
    }

    function durationForDistance(distance) {
        var abs = Math.abs(distance);
        return Math.min(2200, Math.max(1100, 800 + abs * 0.45));
    }

    function scrollToY(targetY, duration) {
        if (prefersReducedMotion) {
            window.scrollTo(0, targetY);
            return;
        }

        var startY = window.pageYOffset || document.documentElement.scrollTop;
        var distance = targetY - startY;

        if (Math.abs(distance) < 4) {
            return;
        }

        duration = duration || durationForDistance(distance);
        var startTime = null;

        function step(timestamp) {
            if (startTime === null) {
                startTime = timestamp;
            }
            var elapsed = timestamp - startTime;
            var progress = Math.min(elapsed / duration, 1);
            window.scrollTo(0, startY + distance * easeInOutCubic(progress));
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        }

        window.requestAnimationFrame(step);
    }

    function scrollToElement(el) {
        if (!el) {
            return;
        }
        var top = el.getBoundingClientRect().top + (window.pageYOffset || document.documentElement.scrollTop);
        scrollToY(Math.max(0, top - getHeaderOffset()));
    }

    function scrollToHash(hash) {
        if (!hash || hash === '#') {
            return;
        }
        var id = hash.charAt(0) === '#' ? hash.slice(1) : hash;
        var el = document.getElementById(id);
        if (el) {
            scrollToElement(el);
        }
    }

    function parseAnchorFromHref(href) {
        try {
            var url = new URL(href, window.location.href);
            return {
                pathname: url.pathname,
                hash: url.hash,
                hostname: url.hostname,
            };
        } catch (e) {
            return null;
        }
    }

    function initAnchorClicks() {
        document.addEventListener('click', function (e) {
            var link = e.target.closest('a[href*="#"]');
            if (!link || link.getAttribute('href') === '#') {
                return;
            }

            var parsed = parseAnchorFromHref(link.href);
            if (!parsed || !parsed.hash || parsed.hash === '#') {
                return;
            }

            var samePage = parsed.hostname === window.location.hostname
                && parsed.pathname === window.location.pathname;

            if (!samePage) {
                return;
            }

            var target = document.getElementById(parsed.hash.slice(1));
            if (!target) {
                return;
            }

            e.preventDefault();
            if (history.pushState) {
                history.pushState(null, '', parsed.hash);
            } else {
                window.location.hash = parsed.hash;
            }
            scrollToHash(parsed.hash);
        }, true);
    }

    function initOnLoad() {
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }

        var hash = window.location.hash;

        if (hash && hash.length > 1) {
            window.scrollTo(0, 0);
            window.requestAnimationFrame(function () {
                setTimeout(function () {
                    scrollToHash(hash);
                }, 120);
            });
        } else {
            window.scrollTo(0, 0);
        }
    }

    window.NutrinextScroll = {
        toY: scrollToY,
        toElement: scrollToElement,
        toHash: scrollToHash,
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initOnLoad();
            initAnchorClicks();
        });
    } else {
        initOnLoad();
        initAnchorClicks();
    }
})(window);

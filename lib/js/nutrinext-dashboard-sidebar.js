/**
 * NutriNext — Sidebar dashboard: móvil usable (entrenador / gym y resto de perfiles).
 */
(function () {
    'use strict';

    if (document.body) {
        document.body.dataset.nnSidebarBound = '1';
    }

    var MOBILE_MAX = 991;

    function isMobile() {
        return window.innerWidth <= MOBILE_MAX;
    }

    function container() {
        return document.querySelector('.main-container');
    }

    function overlayEl() {
        return document.querySelector('.overlay');
    }

    function sidebarOpen() {
        var c = container();
        return c && c.classList.contains('sbar-open');
    }

    function closeSidebar() {
        var c = container();
        if (!c) return;
        c.classList.add('sidebar-closed');
        c.classList.remove('sbar-open');
        var header = document.querySelector('.header.navbar');
        if (header) header.classList.remove('expand-header');
        var ov = overlayEl();
        if (ov) ov.classList.remove('show');
        document.documentElement.classList.remove('sidebar-noneoverflow');
        document.body.classList.remove('sidebar-noneoverflow');
    }

    function openSidebar() {
        var c = container();
        if (!c) return;
        c.classList.remove('sidebar-closed');
        c.classList.add('sbar-open');
        var header = document.querySelector('.header.navbar');
        if (header) header.classList.add('expand-header');
        var ov = overlayEl();
        if (ov) ov.classList.add('show');
        document.documentElement.classList.add('sidebar-noneoverflow');
        document.body.classList.add('sidebar-noneoverflow');
        if (isMobile()) {
            expandActiveSubmenus();
        }
    }

    function toggleSidebar() {
        if (sidebarOpen()) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }

    function expandActiveSubmenus() {
        var menu = document.querySelector('#sidebar .nutrinext-sidebar-menu');
        if (!menu || typeof bootstrap === 'undefined' || !bootstrap.Collapse) {
            return;
        }
        menu.querySelectorAll('ul.submenu').forEach(function (sub) {
            var hasActive = sub.querySelector('a.active');
            var toggle = sub.previousElementSibling;
            if (!hasActive || !toggle) return;
            try {
                bootstrap.Collapse.getOrCreateInstance(sub, { toggle: false }).show();
                toggle.classList.remove('collapsed');
                toggle.setAttribute('aria-expanded', 'true');
            } catch (e) { /* ignore */ }
        });
    }

    function destroyPerfectScrollbarOnMenu() {
        var menu = document.querySelector('#sidebar .menu-categories');
        if (!menu) return;
        if (menu._psInstance && typeof menu._psInstance.destroy === 'function') {
            menu._psInstance.destroy();
            menu._psInstance = null;
        }
        menu.classList.remove('ps', 'ps--active-y');
        var rail = menu.querySelector('.ps__rail-y');
        if (rail) rail.remove();
    }

    function bindSidebarOnce() {
        if (document.body.dataset.nnSidebarClickBound === '1') {
            return;
        }
        document.body.dataset.nnSidebarClickBound = '1';

        document.addEventListener('click', function (e) {
            var toggleBtn = e.target.closest('.sidebarCollapse');
            if (toggleBtn) {
                e.preventDefault();
                e.stopPropagation();
                toggleSidebar();
                return;
            }

            if (isMobile() && sidebarOpen()) {
                var ov = overlayEl();
                if (ov && (e.target === ov || ov.contains(e.target))) {
                    e.preventDefault();
                    closeSidebar();
                    return;
                }
            }
        }, true);

        var menu = document.querySelector('#sidebar .nutrinext-sidebar-menu');
        if (menu) {
            menu.addEventListener('click', function (e) {
                if (!isMobile() || !sidebarOpen()) return;
                var link = e.target.closest('ul.submenu a[href]');
                if (link && link.getAttribute('href') && link.getAttribute('href') !== '#') {
                    setTimeout(closeSidebar, 80);
                }
                var topLink = e.target.closest('li.menu > a.sidebar-menu-link[href]');
                if (topLink && topLink.getAttribute('href') && topLink.getAttribute('href').indexOf('#') !== 0) {
                    setTimeout(closeSidebar, 80);
                }
            });
        }

        window.addEventListener('resize', function () {
            if (!isMobile() && sidebarOpen()) {
                var ov = overlayEl();
                if (ov) ov.classList.remove('show');
                document.documentElement.classList.remove('sidebar-noneoverflow');
                document.body.classList.remove('sidebar-noneoverflow');
            }
            if (isMobile()) {
                destroyPerfectScrollbarOnMenu();
            }
        });
    }

    function initMobileMenuScroll() {
        var sidebar = document.getElementById('sidebar');
        if (!sidebar) return;
        if (isMobile()) {
            destroyPerfectScrollbarOnMenu();
        }
    }

    function run() {
        document.body.dataset.nnSidebarBound = '1';
        bindSidebarOnce();
        initMobileMenuScroll();
        if (isMobile()) {
            var c = container();
            if (c && !c.classList.contains('sbar-open')) {
                c.classList.add('sidebar-closed');
            }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }

    window.addEventListener('load', function () {
        initMobileMenuScroll();
    });
})();

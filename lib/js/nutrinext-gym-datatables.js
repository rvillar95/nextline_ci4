/**
 * NutriNext — DataTables listados Gym (ejercicio, rutina, programa, alumno, asignación).
 */
(function (window, $) {
    'use strict';

    if (!$ || !$.fn || !$.fn.DataTable) {
        return;
    }

    function buildColumns(count) {
        var cols = [];
        for (var i = 0; i < count; i++) {
            cols.push({
                data: i,
                orderable: i < count - 1,
            });
        }
        return cols;
    }

    /**
     * @param {string} selector
     * @param {string} ajaxUrl URL absoluta (base_url)
     * @param {number} colCount columnas incl. acciones
     * @param {object} [extra] opciones extra para DataTable
     */
    function gymInitListTable(selector, ajaxUrl, colCount, extra) {
        var $table = $(selector);
        if (!$table.length) {
            return null;
        }

        if ($.fn.DataTable.isDataTable($table)) {
            $table.DataTable().destroy();
        }

        var opts = Object.assign({
            language: window.gymDataTableLang || {},
            processing: true,
            serverSide: false,
            autoWidth: false,
            // Sin Responsive ni scrollX (scrollX duplica cabecera = líneas vacías arriba de la tabla)
            responsive: false,
            dom: "<'gym-dt-toolbar'<'gym-dt-toolbar__length'l><'gym-dt-toolbar__filter'f>>rt<'gym-dt-footer'<'gym-dt-footer__info'i><'gym-dt-footer__pages'p>>",
            ajax: {
                url: ajaxUrl,
                type: 'GET',
                dataSrc: 'data',
                error: function (xhr) {
                    var msg = 'No se pudo cargar el listado.';
                    if (xhr.status === 403) {
                        msg += ' Sin permiso para esta acción.';
                    } else if (xhr.status === 401) {
                        msg += ' Sesión expirada; recarga la página.';
                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                        msg += ' ' + xhr.responseJSON.error;
                    }
                    console.error('Gym DataTable:', ajaxUrl, xhr.status, xhr.responseText);
                    if (typeof window.nnNotify === 'function') {
                        window.nnNotify(msg, 'error');
                    } else if (typeof window.toastr !== 'undefined') {
                        window.toastr.error(msg);
                    }
                },
            },
            columns: buildColumns(colCount),
            order: [[0, 'asc']],
            pageLength: 25,
        }, extra || {});

        return $table.DataTable(opts);
    }

    window.gymInitListTable = gymInitListTable;
})(window, window.jQuery);

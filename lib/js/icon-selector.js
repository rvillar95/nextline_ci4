// Selector de iconos para categorías de galería
// Archivo: public/js/icon-selector.js

console.log('Icon selector script loaded'); // Debug log

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing icon selector'); // Debug log
    // Lista de iconos disponibles (usando Font Awesome y Bootstrap Icons)
    const iconos = [
        { class: 'fas fa-home', name: 'Casa' },
        { class: 'fas fa-building', name: 'Edificio' },
        { class: 'fas fa-hammer', name: 'Martillo' },
        { class: 'fas fa-tools', name: 'Herramientas' },
        { class: 'fas fa-paint-brush', name: 'Pintura' },
        { class: 'fas fa-expand-arrows-alt', name: 'Expansión' },
        { class: 'fas fa-swimming-pool', name: 'Piscina' },
        { class: 'fas fa-tree', name: 'Jardín' },
        { class: 'fas fa-cut', name: 'Sierra' },
        { class: 'fas fa-bolt', name: 'Taladro' },
        { class: 'fas fa-cube', name: 'Ladrillo' },
        { class: 'fas fa-truck', name: 'Camión' },
        { class: 'fas fa-arrow-up', name: 'Grúa' },
        { class: 'fas fa-hard-hat', name: 'Casco' },
        { class: 'fas fa-file-alt', name: 'Plano' },
        { class: 'fas fa-ruler', name: 'Regla' },
        { class: 'fas fa-level-up-alt', name: 'Nivel' },
        { class: 'fas fa-tape', name: 'Cinta métrica' },
        { class: 'fas fa-screwdriver', name: 'Destornillador' },
        { class: 'fas fa-wrench', name: 'Llave' },
        { class: 'fas fa-fire', name: 'BBQ/Quincho' },
        { class: 'fas fa-car', name: 'Garaje' },
        { class: 'fas fa-door-open', name: 'Puerta' },
        { class: 'fas fa-window-maximize', name: 'Ventana' },
        { class: 'fas fa-lightbulb', name: 'Iluminación' },
        { class: 'bi bi-house', name: 'Casa (Bootstrap)' },
        { class: 'bi bi-building', name: 'Edificio (Bootstrap)' },
        { class: 'bi bi-hammer', name: 'Martillo (Bootstrap)' },
        { class: 'bi bi-tools', name: 'Herramientas (Bootstrap)' },
        { class: 'bi bi-paint-bucket', name: 'Pintura (Bootstrap)' }
    ];

    // Crear el modal del selector de iconos
    function crearModalIconos() {
        const modal = document.createElement('div');
        modal.id = 'iconSelectorModal';
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Seleccionar Icono</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row" id="iconosGrid">
                            <!-- Los iconos se cargarán aquí -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="seleccionarIcono">Seleccionar</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }

    // Cargar iconos en el modal
    function cargarIconos() {
        const grid = document.getElementById('iconosGrid');
        grid.innerHTML = '';
        
        iconos.forEach((icono, index) => {
            const col = document.createElement('div');
            col.className = 'col-md-3 col-sm-4 col-6 mb-3';
            col.innerHTML = `
                <div class="card icono-card" data-icon="${icono.class}" style="cursor: pointer;">
                    <div class="card-body text-center">
                        <i class="${icono.class}" style="font-size: 2rem; color: #333;"></i>
                        <p class="card-text small mt-2">${icono.name}</p>
                    </div>
                </div>
            `;
            grid.appendChild(col);
        });
    }

    // Inicializar el selector
    function inicializarSelector() {
        const inputIcono = document.getElementById('icono');
        console.log('Looking for icono input:', inputIcono); // Debug log
        if (!inputIcono) return;

        // Crear botón para abrir el selector
        const botonSelector = document.createElement('button');
        botonSelector.type = 'button';
        botonSelector.className = 'btn btn-outline-secondary btn-sm';
        botonSelector.innerHTML = '<i class="fas fa-icons"></i> Seleccionar Icono';
        botonSelector.style.marginTop = '5px';
        
        // Insertar el botón después del input
        inputIcono.parentNode.appendChild(botonSelector);
        console.log('Button created and added to DOM'); // Debug log

        // Crear el modal
        crearModalIconos();
        cargarIconos();

        // Event listeners
        botonSelector.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('iconSelectorModal'));
            modal.show();
        });

        // Seleccionar icono
        document.addEventListener('click', function(e) {
            if (e.target.closest('.icono-card')) {
                // Remover selección anterior
                document.querySelectorAll('.icono-card').forEach(card => {
                    card.classList.remove('border-primary', 'bg-light');
                });
                
                // Seleccionar nuevo icono
                const card = e.target.closest('.icono-card');
                card.classList.add('border-primary', 'bg-light');
                
                // Guardar el icono seleccionado
                window.iconoSeleccionado = card.dataset.icon;
            }
        });

        // Confirmar selección
        document.getElementById('seleccionarIcono').addEventListener('click', function() {
            if (window.iconoSeleccionado) {
                inputIcono.value = window.iconoSeleccionado;
                
                // Mostrar preview del icono
                mostrarPreviewIcono(window.iconoSeleccionado);
                
                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('iconSelectorModal'));
                modal.hide();
            }
        });

        // Mostrar preview del icono actual
        if (inputIcono.value) {
            mostrarPreviewIcono(inputIcono.value);
        }
    }

    // Mostrar preview del icono
    function mostrarPreviewIcono(iconoClass) {
        let preview = document.getElementById('iconoPreview');
        if (!preview) {
            preview = document.createElement('div');
            preview.id = 'iconoPreview';
            preview.className = 'mt-2';
            document.getElementById('icono').parentNode.appendChild(preview);
        }
        
        preview.innerHTML = `
            <small class="text-muted">Preview:</small><br>
            <i class="${iconoClass}" style="font-size: 1.5rem; color: #007bff;"></i>
            <span class="ms-2 text-muted">${iconoClass}</span>
        `;
    }

    // Inicializar cuando el DOM esté listo
    inicializarSelector();
    console.log('Icon selector initialization completed'); // Debug log
});

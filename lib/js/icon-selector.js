// Selector de iconos para categorías de galería
// Archivo: public/js/icon-selector.js

console.log('Icon selector script loaded'); // Debug log

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing icon selector'); // Debug log
    
    // Evitar inicialización múltiple
    if (window.iconSelectorInitialized) {
        console.log('Icon selector already initialized, skipping');
        return;
    }
    window.iconSelectorInitialized = true;
    // Lista de iconos disponibles (usando Font Awesome y Bootstrap Icons)
    const iconos = [
        // Construcción Residencial
        { class: 'fas fa-home', name: 'Casa' },
        { class: 'fas fa-house-user', name: 'Casa Familiar' },
        { class: 'fas fa-building', name: 'Edificio' },
        { class: 'fas fa-city', name: 'Ciudad' },
        
        // Construcción Comercial
        { class: 'fas fa-store', name: 'Tienda' },
        { class: 'fas fa-landmark', name: 'Monumento' },
        { class: 'fas fa-industry', name: 'Industrial' },
        { class: 'fas fa-warehouse', name: 'Bodega' },
        
        // Remodelaciones
        { class: 'fas fa-hammer', name: 'Martillo' },
        { class: 'fas fa-tools', name: 'Herramientas' },
        { class: 'fas fa-paint-brush', name: 'Pintura' },
        { class: 'fas fa-cut', name: 'Sierra' },
        
        // Ampliaciones
        { class: 'fas fa-expand-arrows-alt', name: 'Expansión' },
        { class: 'fas fa-plus-square', name: 'Agregar' },
        { class: 'fas fa-arrows-alt', name: 'Flechas' },
        { class: 'fas fa-external-link-alt', name: 'Crecimiento' },
        
        // Servicios Especializados
        { class: 'fas fa-swimming-pool', name: 'Piscina' },
        { class: 'fas fa-fire', name: 'BBQ/Quincho' },
        { class: 'fas fa-road', name: 'Pavimentación' },
        { class: 'fas fa-tree', name: 'Jardín' },
        
        // Galería y Fotografía
        { class: 'fas fa-images', name: 'Galería' },
        { class: 'fas fa-camera', name: 'Cámara' },
        { class: 'fas fa-photo-video', name: 'Fotos/Videos' },
        { class: 'fas fa-image', name: 'Imagen' },
        { class: 'fas fa-portrait', name: 'Retrato' },
        { class: 'fas fa-landscape', name: 'Paisaje' },
        
        // Instalaciones
        { class: 'fas fa-plug', name: 'Eléctrico' },
        { class: 'fas fa-bolt', name: 'Energía' },
        { class: 'fas fa-lightbulb', name: 'Iluminación' },
        { class: 'fas fa-wifi', name: 'Tecnología' },
        
        // Consultoría
        { class: 'fas fa-user-tie', name: 'Profesional' },
        { class: 'fas fa-chart-line', name: 'Análisis' },
        { class: 'fas fa-clipboard-check', name: 'Asesoría' },
        { class: 'fas fa-handshake', name: 'Consultoría' },
        
        // Permisos y Trámites
        { class: 'fas fa-file-alt', name: 'Documentos' },
        { class: 'fas fa-stamp', name: 'Permisos' },
        { class: 'fas fa-clipboard-list', name: 'Trámites' },
        { class: 'fas fa-certificate', name: 'Certificados' },
        
        // Diseño y Arquitectura
        { class: 'fas fa-drafting-compass', name: 'Diseño' },
        { class: 'fas fa-ruler', name: 'Regla' },
        { class: 'fas fa-cube', name: '3D' },
        { class: 'fas fa-pencil-ruler', name: 'Planos' },
        
        // Mantenimiento
        { class: 'fas fa-wrench', name: 'Mantenimiento' },
        { class: 'fas fa-screwdriver', name: 'Reparación' },
        { class: 'fas fa-hard-hat', name: 'Casco' },
        { class: 'fas fa-truck', name: 'Servicio' },
        
        // Otros iconos útiles
        { class: 'fas fa-cogs', name: 'Configuración' },
        { class: 'fas fa-cog', name: 'Configurar' },
        { class: 'fas fa-star', name: 'Destacado' },
        { class: 'fas fa-heart', name: 'Favorito' },
        
        // Bootstrap Icons
        { class: 'bi bi-house', name: 'Casa (Bootstrap)' },
        { class: 'bi bi-building', name: 'Edificio (Bootstrap)' },
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

        // Verificar si ya existe un botón de selector
        let botonSelector = document.querySelector('#icono + button, #icono ~ button');
        
        if (!botonSelector) {
            // Crear botón para abrir el selector solo si no existe
            botonSelector = document.createElement('button');
            botonSelector.type = 'button';
            botonSelector.className = 'btn btn-outline-secondary btn-sm';
            botonSelector.innerHTML = '<i class="fas fa-icons"></i> Seleccionar Icono';
            botonSelector.style.marginTop = '5px';
            
            // Insertar el botón después del input
            inputIcono.parentNode.appendChild(botonSelector);
            console.log('Button created and added to DOM'); // Debug log
        } else {
            console.log('Button already exists, using existing one'); // Debug log
        }

        // Event listeners
        botonSelector.addEventListener('click', function() {
            // Remover modal existente si hay uno
            const modalExistente = document.getElementById('iconSelectorModal');
            if (modalExistente) {
                modalExistente.remove();
            }
            
            // Crear nuevo modal
            crearModalIconos();
            cargarIconos();
            
            // Configurar event listeners después de crear el modal
            configurarEventListenersModal(inputIcono);
            
            const modal = new bootstrap.Modal(document.getElementById('iconSelectorModal'));
            modal.show();
        });

        // Limpiar selección temporal cuando se cierra el modal
        document.addEventListener('hidden.bs.modal', function(e) {
            if (e.target.id === 'iconSelectorModal') {
                window.iconoSeleccionado = null;
            }
        });

        // Mostrar preview del icono actual
        if (inputIcono.value) {
            mostrarPreviewIcono(inputIcono.value);
        }
    }

    // Configurar event listeners del modal
    function configurarEventListenersModal(inputIcono) {
        // Remover event listeners anteriores si existen
        if (window.iconoCardHandler) {
            document.removeEventListener('click', window.iconoCardHandler);
        }

        // Crear nuevo handler para selección de iconos
        window.iconoCardHandler = function(e) {
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
        };

        // Agregar el event listener
        document.addEventListener('click', window.iconoCardHandler);

        // Confirmar selección
        const botonSeleccionar = document.getElementById('seleccionarIcono');
        if (botonSeleccionar) {
            botonSeleccionar.onclick = function() {
                if (window.iconoSeleccionado) {
                    inputIcono.value = window.iconoSeleccionado;
                    
                    // Mostrar preview del icono
                    mostrarPreviewIcono(window.iconoSeleccionado);
                    
                    // Cerrar modal usando data-bs-dismiss
                    const modalElement = document.getElementById('iconSelectorModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Limpiar backdrop inmediatamente
                    setTimeout(() => {
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) {
                            backdrop.remove();
                        }
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                    }, 100);
                }
            };
        }
    }

    // Mostrar preview del icono
    function mostrarPreviewIcono(iconoClass) {
        // Remover preview existente si hay uno
        const previewExistente = document.getElementById('iconoPreview');
        if (previewExistente) {
            previewExistente.remove();
        }
        
        // Crear nuevo preview
        const preview = document.createElement('div');
        preview.id = 'iconoPreview';
        preview.className = 'mt-2 p-2 border rounded bg-light';
        preview.style.fontSize = '0.9rem';
        
        preview.innerHTML = `
            <small class="text-muted d-block mb-1">Preview:</small>
            <div class="d-flex align-items-center">
                <i class="${iconoClass}" style="font-size: 1.5rem; color: #007bff; margin-right: 8px;"></i>
                <span class="text-muted">${iconoClass}</span>
            </div>
        `;
        
        // Insertar después del campo de icono
        const inputIcono = document.getElementById('icono');
        if (inputIcono && inputIcono.parentNode) {
            inputIcono.parentNode.appendChild(preview);
        }
    }

    // Inicializar cuando el DOM esté listo
    inicializarSelector();
    console.log('Icon selector initialization completed'); // Debug log
});

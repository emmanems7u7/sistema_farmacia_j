<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catálogo de Farmacia</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Estilos personalizados -->
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 0 !important;
        }
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1.2rem 1rem;
            min-height: 70px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        .search-container {
            max-width: 600px;
            margin: 0 auto;
        }
        .modal-categorias .modal-body {
            max-height: 60vh;
            overflow-y: auto;
        }
        .categoria-item {
            transition: all 0.3s ease;
            border-radius: 5px;
        }
        .categoria-item:hover {
            background-color: #f0f7ff;
        }
        .navbar-brand {
            font-weight: 600;
        }
        /* Nuevos estilos para el carrusel y productos */
        .hero-carousel {
            margin-bottom: 2rem;
        }
        .carousel-item {
            height: 400px;
            background-size: cover;
            background-position: center;
        }
        .carousel-caption {
            background-color: rgba(0,0,0,0.6);
            border-radius: 10px;
            padding: 20px;
        }
        .product-card {
            transition: all 0.3s ease;
            margin-bottom: 20px;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .product-img-container {
            height: 200px;
            overflow: hidden;
        }
        .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .product-card:hover .product-img {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <!-- Menú de navegación -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container-fluid">
            <!-- Logo y botón móvil -->
            <a class="navbar-brand text-primary me-3" href="#">
                
                <img src="{{ asset('assets/img/logo2.jpeg') }}" alt="Logo" width="80" height="auto">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Contenido del navbar -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Botón para categorías (izquierda) -->
                <div class="d-flex me-3">
    <div class="dropdown">
        <!-- Botón que abre el menú (igual estilo que tu botón original) -->
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownCategorias" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-list-ul me-2"></i> Categorías
        </button>
        
        <!-- Menú desplegable (contenido adaptado de tu modal) -->
        <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="dropdownCategorias" style="width: 280px;">
            <!-- Encabezado del menú -->
            <li class="px-3 py-2 bg-primary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-list-ul me-2"></i> Todas las Categorías
                </h6>
            </li>
            
            <!-- Elementos del menú -->
            <li>
                <a href="{{ route('admin.catalogo.index') }}" class="dropdown-item d-flex align-items-center py-2">
                    <i class="fas fa-boxes me-2 text-primary"></i>
                    <span>Todas las categorías</span>
                </a>
            </li>
            
            <li><hr class="dropdown-divider"></li>
            
            @foreach($categorias as $categoria)
            <li>
                 
                <a href="{{ route('admin.catalogo.categoria', $categoria->id) }}" 
                   class="dropdown-item d-flex align-items-center py-2">
                    <i class="fas fa-pills me-2 text-muted"></i>
                    <span>{{ $categoria->nombre }}</span>
                </a>
            </li>
            @endforeach
            
            <li><hr class="dropdown-divider"></li>
            
            <!-- Opción de cerrar (similar al footer del modal) -->
            <li>
                <button class="dropdown-item text-center text-muted py-2" onclick="document.querySelector('.dropdown-toggle').click()">
                    <small>Cerrar menú</small>
                </button>
            </li>
        </ul>
    </div>
</div>


                
                
                <!-- Buscador (centro) -->
                <!-- Buscador Avanzado con Autocompletado -->
                    <!-- Buscador Avanzado Mejorado -->
                    <div class="search-container flex-grow-1 mx-3 position-relative">
    <form action="{{ route('admin.catalogo.buscar') }}" method="GET" class="w-100" id="search-form">
        @if(request()->has('categoria'))
            <input type="hidden" name="categoria" value="{{ request('categoria') }}">
        @endif
        <div class="input-group shadow-sm rounded-pill overflow-hidden">
            <input type="text" 
                   name="search" 
                   id="search-input"
                   class="form-control border-end-0 py-2 ps-4" 
                   placeholder="Buscar medicamentos..." 
                   value="{{ request('search') }}"
                   autocomplete="off"
                   aria-label="Buscar productos"
                   data-min-chars="1">
            <button class="btn btn-primary px-4" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <div id="search-suggestions" class="dropdown-menu w-100 shadow-lg" style="display: none;">
            <div class="dropdown-header d-flex justify-content-between align-items-center">
                <span class="small text-muted">Sugerencias</span>
                <span class="badge bg-primary rounded-pill" id="suggestion-count">0</span>
            </div>
            <div class="dropdown-divider"></div>
            <div id="suggestions-list" class="px-2">
                <!-- Las sugerencias se cargarán aquí -->
            </div>
            
        </div>
    </form>
</div>

<!-- Añade estos estilos -->
<style>
.search-container {
    max-width: 600px;
    margin: 0 auto;
}

#search-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid rgba(0,0,0,.15);
    border-radius: 0.5rem;
}

.dropdown-item.active {
    background-color: #f8f9fa;
    color: #212529;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.dropdown-header, .dropdown-footer {
    padding: 0.5rem 1rem;
}
</style>

<!-- Añade este JavaScript -->
<script>





document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const searchInput = document.getElementById('search-input');
    const searchSuggestions = document.getElementById('search-suggestions');
    const suggestionsList = document.getElementById('suggestions-list');
    const suggestionCount = document.getElementById('suggestion-count');
    
    // Configuración
    const minChars = parseInt(searchInput.getAttribute('data-min-chars')) || 1;
    let currentFocus = -1;
    let debounceTimer;
    let lastAbortController = null;

    // Event Listeners
    searchInput.addEventListener('input', handleInput);
    searchInput.addEventListener('keydown', handleKeyDown);
    document.addEventListener('click', handleClickOutside);
    searchInput.addEventListener('focus', handleFocus);

    // Funciones principales
    function handleInput(e) {
        clearTimeout(debounceTimer);
        const query = e.target.value.trim();
        
        if (query.length >= minChars) {
            debounceTimer = setTimeout(() => fetchSuggestions(query), 300);
        } else {
            hideSuggestions();
        }
    }

    function handleKeyDown(e) {
        const items = suggestionsList.querySelectorAll('.dropdown-item');
        
        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                currentFocus = Math.min(currentFocus + 1, items.length - 1);
                setActiveItem(items);
                break;
            case 'ArrowUp':
                e.preventDefault();
                currentFocus = Math.max(currentFocus - 1, -1);
                setActiveItem(items);
                break;
            case 'Enter':
                if (currentFocus > -1 && items[currentFocus]) {
                    e.preventDefault();
                    items[currentFocus].click();
                }
                break;
            case 'Escape':
                hideSuggestions();
                break;
        }
    }

    function handleClickOutside(e) {
        if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
            hideSuggestions();
        }
    }

    function handleFocus() {
        if (this.value.trim().length >= minChars) {
            fetchSuggestions(this.value.trim());
        }
    }

    async function fetchSuggestions(query) {
        // Cancelar petición anterior si existe
        if (lastAbortController) {
            lastAbortController.abort();
        }
        
        const controller = new AbortController();
        lastAbortController = controller;
        
        try {
            const response = await fetch(
                `{{ route('admin.catalogo.search') }}?query=${encodeURIComponent(query)}`, 
                {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    signal: controller.signal
                }
            );

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const data = await response.json();
            console.debug('Datos recibidos:', data);
            
            if (!data) {
                throw new Error('No se recibieron datos');
            }

            // Manejar diferentes formatos de respuesta
            const results = Array.isArray(data) ? data : 
                           (data.results ? data.results : []);
            
            displaySuggestions(results);
            
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Error al obtener sugerencias:', error);
                showError();
            }
        }
    }



    function displaySuggestions(items) {
    if (!items || items.length === 0) {
        showNoResults();
        return;
    }

    suggestionsList.innerHTML = '';
    
    items.forEach(item => {
        if (!item) return;
        
        const name = item.name || item.nombre || '';
        const url = item.url || '#';
        // Obtener la URL de la imagen (asegúrate que tu backend devuelva este campo)
        const imageUrl = item.image || item.imagen || item.image_url || '/img/default-product.png';
        
        const suggestionItem = document.createElement('a');
        suggestionItem.className = 'dropdown-item d-flex align-items-center gap-3 py-2';
        suggestionItem.href = url;
        
        // Estructura con imagen y nombre (sin categoría)
        suggestionItem.innerHTML = `
            <img src="${imageUrl}" 
                 alt="${name}" 
                 class="rounded" 
                 style="width: 40px; height: 40px; object-fit: cover;">
            <span>${highlightMatch(name, searchInput.value.trim())}</span>
        `;
        
        suggestionItem.addEventListener('click', function(e) {
            e.preventDefault();
            searchInput.value = name;
            hideSuggestions();
        });
        
        suggestionsList.appendChild(suggestionItem);
    });
    
    suggestionCount.textContent = items.length;
    searchSuggestions.style.display = 'block';
    currentFocus = -1;
}


    

    // Funciones auxiliares
    function highlightMatch(text, query) {
        if (!text || !query) return text;
        try {
            const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            return text.replace(regex, '<span class="text-primary fw-bold">$1</span>');
        } catch (e) {
            return text;
        }
    }

    function showNoResults() {
        suggestionsList.innerHTML = '<div class="dropdown-item text-muted">No se encontraron resultados</div>';
        suggestionCount.textContent = '0';
        searchSuggestions.style.display = 'block';
    }

    function showError() {
        suggestionsList.innerHTML = '<div class="dropdown-item text-danger">Error al cargar sugerencias</div>';
        suggestionCount.textContent = '0';
        searchSuggestions.style.display = 'block';
    }

    function hideSuggestions() {
        searchSuggestions.style.display = 'none';
        currentFocus = -1;
    }

    function setActiveItem(items) {
        items.forEach(item => item.classList.remove('active'));
        
        if (currentFocus >= 0 && items[currentFocus]) {
            items[currentFocus].classList.add('active');
            items[currentFocus].scrollIntoView({
                block: 'nearest',
                behavior: 'smooth'
            });
        }
    }
});
</script>

<!-- Agrega esto en tu sección de scripts -->







        


                
            </div>
        </div>
    </nav>

   

    <!-- Modal de Categorías -->
    <div class="modal fade" id="categoriasModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-list-ul me-2"></i> Todas las Categorías
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-categorias">
                    <div class="list-group">
                        <a href="{{ route('admin.catalogo.index') }}" class="list-group-item list-group-item-action categoria-item">
                            <i class="fas fa-boxes me-2 text-primary"></i> Todas las categorías
                        </a>
                        @foreach($categorias as $categoria)
                        <a href="{{ route('admin.catalogo.index', ['categoria' => $categoria->id]) }}" 
                           class="list-group-item list-group-item-action categoria-item">
                            <i class="fas fa-pills me-2 text-muted"></i> {{ $categoria->nombre }}
                        </a>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

   <!-- Contenido principal -->
   <main class="py-4">
        @yield('content')
    </main>

  
       <!-- Pie de página -->
       <footer class="footer">
        <div class="container text-center">
            <p class="mb-0 text-muted">
                &copy; {{ date('Y') }} Farmacia .
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script para el buscador y carrusel -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuración del carrusel automático
            const myCarousel = document.getElementById('productCarousel');
            const carousel = new bootstrap.Carousel(myCarousel, {
                interval: 3000, // Cambia cada 3 segundos
                ride: true,     // Inicia automáticamente
                wrap: true      // Vuelve al inicio después del último
            });
            
            // Pausar al pasar el ratón
            myCarousel.addEventListener('mouseenter', function() {
                carousel.pause();
            });
            
            // Reanudar al quitar el ratón
            myCarousel.addEventListener('mouseleave', function() {
                carousel.cycle();
            });
            
            // Buscador
            const searchForm = document.querySelector('form[action="{{ route('admin.catalogo.index') }}"]');
            if(searchForm) {
                searchForm.addEventListener('submit', function(e) {
                    // Validación adicional si es necesaria
                });
            }
        });
    </script>
</body>
</html>



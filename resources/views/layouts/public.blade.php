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
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

/*  PRODUCTOS Y CARRUSEL  */
.hero-carousel {
    margin-bottom: 2rem;
}

.carousel-item {
    height: 400px;
    background-size: cover;
    background-position: center;
}

.carousel-caption {
    background-color: rgba(0, 0, 0, 0.6);
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
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
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

/*  DISEÑO RESPONSIVE SOLO PARA MÓVIL */
@media (max-width: 991.98px) {
  
    #dropdownCategorias {
        font-size: 0 !important;
        padding: 0.5rem !important;
    }
    
    #dropdownCategorias i {
        font-size: 1.2rem !important;
        margin-right: 0 !important;
    }
    
  
    .btn-ingresar-text {
        display: none !important;
    }
    
    .btn-ingresar-icon {
        display: inline-block !important;
    }
    
    
    .navbar-brand h2 {
        font-size: 0.9rem !important;
        margin-bottom: 0.1rem !important;
    }
    
    .navbar-brand {
        line-height: 1.1 !important;
    }

    /* Mantener los íconos visibles */
    .navbar i,
    .navbar .fa,
    .navbar .fas,
    .navbar .fab {
        display: inline-block !important;
        font-size: 1.2rem;
    }

  
    .navbar,
    .navbar .container,
    .navbar .d-flex,
    .navbar-nav {
        display: flex !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: space-between !important;
    }

   
    .navbar .nav-item,
    .navbar .d-flex.align-items-center {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: center !important;
        flex-wrap: nowrap !important;
    }

    
    .navbar .gap-3 {
        gap: 0.3rem !important;
    }

    
    .navbar .btn,
    .navbar .nav-link {
        padding: 0.3rem 0.4rem !important;
        font-size: 1rem !important;
    }

   
    .navbar-collapse {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: flex-end !important;
        flex-wrap: nowrap !important;
    }

   
    .search-container {
        width: 150px !important;
        margin: 0 !important;
    }

    .form-control {
        font-size: 0.8rem !important;
    }

    
    .bg-white.rounded-circle {
        transform: scale(0.85);
    }
}
</style>

</head>

<body>
    <!-- Menú de navegación  -->
    <nav class="navbar navbar-expand-lg sticky-top" style="background-color: #5BC0EB;">
        <div class="container-fluid">
         
               <!-- Botón Categorías -->
             <div class="d-flex me-3">
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" id="dropdownCategorias"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-list-ul me-2"></i> Categorías
                        </button>
                        <ul class="dropdown-menu dropdown-menu-start shadow-lg" aria-labelledby="dropdownCategorias"
                            style="width: 280px;">
                            <li class="px-3 py-2" style="background-color: #0077B6; color: white;">
                                <h6 class="mb-0">
                                    <i class="fas fa-list-ul me-2"></i> Todas las Categorías
                                </h6>
                            </li>
                            <li>
                                <a href="{{ route('admin.catalogo.index') }}"
                                    class="dropdown-item d-flex align-items-center py-2 text-dark">
                                    <i class="fas fa-boxes me-2 text-dark"></i>
                                    <span>Todas las categorías</span>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            @foreach($categorias as $categoria)
                                <li>
                                    <a href="{{ route('admin.catalogo.categoria', $categoria->id) }}"
                                        class="dropdown-item d-flex align-items-center py-2 text-dark">
                                        <i class="fas fa-pills me-2 text-muted"></i>
                                        <span>{{ $categoria->nombre }}</span>
                                    </a>
                                </li>
                            @endforeach
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <button class="dropdown-item text-center text-muted py-2"
                                    onclick="document.querySelector('.dropdown-toggle').click()">
                                    <small>Cerrar menú</small>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            <div class="navbar-brand text-white d-flex flex-column align-items-start" style="line-height: 1;">
                <h2 class="mb-0" style="font-weight: 500; font-size: 1.2rem;">FARMACIA</h2>
                <h2 class="mb-0" style="font-weight: 600; font-size: 1.2rem;">MARIEL</h2>
            </div>

           <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>-->

            <!-- Contenido del navbar -->
            <div class="collapse navbar-collapse" id="navbarContent">

                

                <!-- Buscador -->
                <div class="search-container flex-grow-1 mx-3 position-relative">
                    <form action="{{ route('admin.catalogo.buscar') }}" method="GET" class="w-100" id="search-form">
                        @if(request()->has('categoria'))
                            <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                        @endif
                        <div class="input-group shadow-sm rounded-pill overflow-hidden">
                            <input type="text" name="search" id="search-input"
                                class="form-control border-end-0 py-2 ps-4" placeholder="Buscar medicamentos..."
                                value="{{ request('search') }}" autocomplete="off" aria-label="Buscar productos"
                                data-min-chars="1">
                            <button class="btn btn-light px-4" type="submit">
                                <i class="fas fa-search" style="color: #0dcaf0;"></i> 
                            </button>

                        </div>
                        <div id="search-suggestions" class="dropdown-menu w-100 shadow-lg" style="display: none;">
                            <div class="dropdown-header d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Sugerencias</span>
                                <span class="badge bg-primary rounded-pill" id="suggestion-count">0</span>
                            </div>
                            <div class="dropdown-divider"></div>
                            <div id="suggestions-list" class="px-2">
                            
                            </div>
                        </div>
                    </form>
                </div>

                <div class="d-flex align-items-center gap-3 ms-auto">
                  
                   

                    <!-- Login / Inicio -->
                    <ul class="nav navbar-nav mb-0">
                        <li class="nav-item">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/home') }}" class="btn btn-outline-light">
                                        <span class="btn-ingresar-text">Inicio</span>
                                        <i class="fas fa-home btn-ingresar-icon"></i>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline-light">
                                        <span class="btn-ingresar-text">Ingresar</span>
                                        <i class="fas fa-user btn-ingresar-icon"></i>
                                    </a>
                                @endauth
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Pie de página -->
    <footer class="footer">
        <div class="container text-center">
            <p class="mb-0 text-muted">
                &copy; {{ date('Y') }} Farmacia Mariel
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script para el buscador y carrusel -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Configuración del carrusel automático
            const myCarousel = document.getElementById('productCarousel');
            const carousel = new bootstrap.Carousel(myCarousel, {
                interval: 3000, 
                ride: true,     
                wrap: true      
            });

            // Pausar al pasar el maus
            myCarousel.addEventListener('mouseenter', function () {
                carousel.pause();
            });

            // Reanudar al quitar el masu
            myCarousel.addEventListener('mouseleave', function () {
                carousel.cycle();
            });

            // Buscador
            const searchForm = document.querySelector('form[action="{{ route('admin.catalogo.index') }}"]');
            if (searchForm) {
                searchForm.addEventListener('submit', function (e) {
                   
                });
            }
        });

        // Script del buscador )
        document.addEventListener('DOMContentLoaded', function () {
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

                switch (e.key) {
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

                    const imageUrl = item.image || item.imagen || item.image_url || '/img/default-product.png';

                    const suggestionItem = document.createElement('a');
                    suggestionItem.className = 'dropdown-item d-flex align-items-center gap-3 py-2';
                    suggestionItem.href = url;

                
                    suggestionItem.innerHTML = `
                        <img src="${imageUrl}" 
                            alt="${name}" 
                            class="rounded" 
                            style="width: 40px; height: 40px; object-fit: cover;">
                        <span>${highlightMatch(name, searchInput.value.trim())}</span>
                    `;

                    suggestionItem.addEventListener('click', function (e) {
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
</body>

</html>
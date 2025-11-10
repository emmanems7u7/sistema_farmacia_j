@extends('layouts.public')

@section('content')

 <!-- Carrusel de productos destacados -->
    <!-- Carrusel de productos destacados -->
<div id="productCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="2"></button>
    </div>

    <div class="carousel-inner">
        <div class="carousel-item active" 
             style="background-image: url('{{ asset('assets/img/banner.jpg') }}'); background-size: cover; background-position: center;">
            <div class="carousel-caption d-none d-md-block">
                <h5>Productos Destacados</h5>
                <p>Los medicamentos más vendidos este mes</p>
            </div>
            <!-- Captión para móvil -->
            <div class="carousel-caption d-block d-md-none">
                <h6>Productos Destacados</h6>
                <small>Los más vendidos este mes</small>
            </div>
        </div>
        <div class="carousel-item" 
             style="background-image: url('{{ asset('assets/img/banner2.jpg') }}'); background-size: cover; background-position: center;">
            <div class="carousel-caption d-none d-md-block">
                <h5>Nuevos Lanzamientos</h5>
                <p>Descubre nuestras novedades</p>
            </div>
            <!-- Captión para móvil -->
            <div class="carousel-caption d-block d-md-none">
                <h6>Nuevos Lanzamientos</h6>
                <small>Descubre novedades</small>
            </div>
        </div>
        <div class="carousel-item" 
             style="background-image: url('https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'); background-size: cover; background-position: center;">
            <div class="carousel-caption d-none d-md-block">
                <h5>Precios Increíbles</h5>
                <p>Aprovecha nuestros precios</p>
            </div>
            <!-- Captión para móvil -->
            <div class="carousel-caption d-block d-md-none">
                <h6>Precios Increíbles</h6>
                <small>Aprovecha ofertas</small>
            </div>
        </div>
    </div>
    
    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<style>
/* Estilos para el carrusel responsive */
.hero-carousel {
    margin-bottom: 1rem;
}

.carousel-item {
    height: 400px; /* Altura para desktop */
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

/* Ajustes para móvil */
@media (max-width: 768px) {
    .carousel-item {
        height: 200px !important; /* Mucho más bajo en móvil */
    }
    
    .hero-carousel {
        margin-bottom: 0.5rem;
    }
    
    /* Indicadores más pequeños en móvil */
    .carousel-indicators {
        bottom: 5px !important;
    }
    
    .carousel-indicators button {
        width: 8px !important;
        height: 8px !important;
        border-radius: 50% !important;
        margin: 0 3px !important;
    }
    
    /* Captions para móvil */
    .carousel-caption.d-block.d-md-none {
        bottom: 15px !important;
        padding: 8px 12px !important;
        background: rgba(0, 0, 0, 0.6) !important;
        border-radius: 8px !important;
        left: 10px !important;
        right: 10px !important;
    }
    
    .carousel-caption.d-block.d-md-none h6 {
        font-size: 0.9rem !important;
        margin-bottom: 2px !important;
        font-weight: 600;
    }
    
    .carousel-caption.d-block.d-md-none small {
        font-size: 0.75rem !important;
        opacity: 0.9;
    }
    
    /* Flechas de navegación más pequeñas */
    .carousel-control-prev,
    .carousel-control-next {
        width: 30px !important;
        height: 30px !important;
        top: 50% !important;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.3);
        border-radius: 50%;
    }
    
    .carousel-control-prev {
        left: 5px !important;
    }
    
    .carousel-control-next {
        right: 5px !important;
    }
    
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        width: 15px !important;
        height: 15px !important;
    }
}

/* Ajustes para tablets */
@media (min-width: 769px) and (max-width: 991px) {
    .carousel-item {
        height: 300px !important;
    }
}

/* Estilos para desktop (mantenemos los originales) */
@media (min-width: 992px) {
    .carousel-item {
        height: 400px !important;
    }
    
    .carousel-caption {
        background: rgba(0, 0, 0, 0.6);
        border-radius: 10px;
        padding: 20px;
    }
}
</style>
<!-- Contenedor principal con 1cm exacto a cada lado -->
<div style="margin: 0 30px;"> 
    <!-- Filtro de categoría -->
   <div style="margin: 0 30px;"> 
    <!-- FILTRO DE CATEGORÍA -->
    @if(request()->has('categoria'))
    <div class="row mb-2">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0" style="font-size: 1.5rem;">
                    <i class="fas fa-tags text-primary me-2"></i>
                    {{ $categorias->firstWhere('id', request('categoria'))->nombre }}
                </h2>
                <a href="{{ route('admin.catalogo.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-times me-1"></i> Quitar filtro
                </a>
            </div>
            <hr class="my-2">
        </div>
    </div>
    @endif

    <!-- 🖥️ VERSIÓN ESCRITORIO (visible en pantallas grandes) -->
    <div class="d-none d-md-flex flex-wrap justify-content-center gap-3 mb-4 px-3 py-4 rounded-4"
         style="background: rgba(245,245,245,0.8);">
        @foreach($categorias as $cat)
            @php
                switch (strtolower($cat->nombre)) {
                    case 'medicamentos':
                        $icono = 'fas fa-pills';
                        $iconColor = '#ffffff';
                        $gradient = 'radial-gradient(circle at top, #3a7bd5, #00d2ff)';
                        break;
                    case 'suplementos':
                        $icono = 'fas fa-capsules';
                        $iconColor = '#fff8e1';
                        $gradient = 'radial-gradient(circle at top, #f46b45, #eea849)';
                        break;
                    case 'belleza':
                        $icono = 'fas fa-magic';
                        $iconColor = '#fce4ec';
                        $gradient = 'radial-gradient(circle at top, #ff758c, #ff7eb3)';
                        break;
                    case 'salud sexual':
                        $icono = 'fas fa-heart';
                        $iconColor = '#ffebee';
                        $gradient = 'radial-gradient(circle at top, #ff416c, #ff4b2b)';
                        break;
                    case 'cuidado de piel':
                        $icono = 'fas fa-spa';
                        $iconColor = '#e8f5e9';
                        $gradient = 'radial-gradient(circle at top, #56ab2f, #a8e063)';
                        break;
                    case 'insumos medicos':
                        $icono = 'fas fa-syringe';
                        $iconColor = '#e3f2fd';
                        $gradient = 'radial-gradient(circle at top, #0575e6, #021b79)';
                        break;
                    case 'mamas y bebes':
                        $icono = 'fas fa-baby';
                        $iconColor = '#f3e5f5';
                        $gradient = 'radial-gradient(circle at top, #da22ff, #9733ee)';
                        break;
                    case 'nutricion saludable':
                        $icono = 'fas fa-apple-alt';
                        $iconColor = '#f1f8e9';
                        $gradient = 'radial-gradient(circle at top, #a8ff78, #78ffd6)';
                        break;
                    case 'cuidado e higiene':
                        $icono = 'fas fa-soap';
                        $iconColor = '#e0f7fa';
                        $gradient = 'radial-gradient(circle at top, #00c6ff, #0072ff)';
                        break;
                    case 'alimentos y bebidas':
                        $icono = 'fas fa-soap';
                        $iconColor = '#e0f7fa';
                        $gradient = 'radial-gradient(circle at top, #33b14eff, #4ebbceff)';
                        break;
                    default:
                        $icono = 'fas fa-pills';
                        $iconColor = '#ffffff';
                        $gradient = 'radial-gradient(circle at top, #3a7bd5, #00d2ff)';
                }
            @endphp

            <a href="{{ route('admin.catalogo.categoria', $cat->id) }}" 
               class="rounded-circle d-flex flex-column align-items-center justify-content-center position-relative shadow-sm"
               style="background: {{ $gradient }}; width: 85px; height: 85px; text-decoration:none;">
                <i class="{{ $icono }}" style="color: {{ $iconColor }}; font-size: 1.3rem;"></i>
                <span class="category-name text-center text-white" style="font-size: 0.8rem;">{{ $cat->nombre }}</span>
            </a>
        @endforeach
    </div>

    <!-- 📱 VERSIÓN MÓVIL (carrusel automático) -->
    <div id="categoriasCarousel" class="carousel slide d-md-none" data-bs-ride="carousel" data-bs-interval="2500">
      <div class="carousel-inner">
        @foreach($categorias->chunk(3) as $index => $grupo)
          <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
            <div class="d-flex justify-content-center gap-3 py-3">
              @foreach($grupo as $cat)
                  <a href="{{ route('admin.catalogo.categoria', $cat->id) }}" 
                     class="rounded-circle d-flex flex-column align-items-center justify-content-center shadow-sm"
                     style="background: {{ $gradient }}; width: 85px; height: 85px; text-decoration:none;">
                    <i class="{{ $icono }}" style="color: {{ $iconColor }}; font-size: 1.3rem;"></i>
                    <span class="category-name text-center text-white" style="font-size: 0.8rem;">{{ $cat->nombre }}</span>
                  </a>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>
    </div>
</div>



<style>
    .category-btn {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: 2px solid rgba(255,255,255,0.3);
        text-decoration: none;
        text-align: center;
        overflow: hidden;
    }
    
    .category-btn:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 8px 15px rgba(0,0,0,0.15) !important;
        z-index: 10;
    }
    
    .icon-container {
        transition: all 0.3s ease;
        z-index: 2;
    }
    
    .category-btn:hover .icon-container {
        transform: scale(1.15) rotate(10deg);
    }
    
    .category-name {
        font-size: 0.6rem;
        font-weight: 500;
        color: white;
        text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        z-index: 2;
        line-height: 1.1;
        display: block;
        max-width: 70px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .hover-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at center, rgba(255,255,255,0.2) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 1;
    }
    
    .category-btn:hover .hover-overlay {
        opacity: 1;
    }
    
    .pulse-effect {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background: rgba(255,255,255,0.1);
        transform: scale(0.9);
        opacity: 0;
        z-index: 0;
    }
    
    .category-btn:hover .pulse-effect {
        animation: pulse-animation 1.5s infinite;
    }
    
    @keyframes pulse-animation {
        0% {
            transform: scale(0.9);
            opacity: 1;
        }
        100% {
            transform: scale(1.3);
            opacity: 0;
        }
    }
    
    @media (max-width: 768px) {
        .category-btn {
            width: 75px !important;
            height: 75px !important;
        }
        
        .icon-container i {
            font-size: 1.1rem !important;
        }
        
        .category-name {
            font-size: 0.55rem;
        }
    }
</style>

<!-- Sección de productos más vendidos -->
@if($topProductos->isNotEmpty())
<section class="mb-5">
    <div class="container">
    <!-- Encabezado profesional para productos más vendidos -->
        <div class="card mb-5 overflow-hidden border-0" style="
            border-radius: 12px;
            background: linear-gradient(135deg, #e3f4fc 0%, #b8e2f2 100%);
            box-shadow: 0 4px 20px rgba(24, 154, 180, 0.15);
            border-left: 6px solid #189ab4;
        ">
            <div class="card-body p-4 text-center position-relative">
                <!-- Efecto de burbujas decorativas -->
                <div class="position-absolute top-0 start-0 w-100 h-100 opacity-20">
                    <div style="
                        position: absolute;
                        width: 80px;
                        height: 80px;
                        background: #189ab4;
                        border-radius: 50%;
                        top: -20px;
                        right: -20px;
                        filter: blur(8px);
                    "></div>
                    <div style="
                        position: absolute;
                        width: 60px;
                        height: 60px;
                        background: #05445e;
                        border-radius: 50%;
                        bottom: -15px;
                        left: -15px;
                        filter: blur(5px);
                    "></div>
                </div>
                
                <h2 class="mb-0 position-relative" style="
                    font-size: 1.8rem;
                    font-weight: 700;
                    color: #05445e;
                    letter-spacing: 0.5px;
                    text-transform: uppercase;
                    font-family: 'Montserrat', sans-serif;
                ">
                    <span class="me-3" style="
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        width: 50px;
                        height: 50px;
                        background: #ffd700;
                        border-radius: 12px;
                        box-shadow: 0 4px 8px rgba(255, 215, 0, 0.3);
                    ">
                        <i class="fas fa-crown" style="color: #05445e; font-size: 1.5rem;"></i>
                    </span>
                    Productos Destacados
                </h2>
                
                <p class="mt-2 position-relative mb-0" style="
                    color: #189ab4;
                    font-weight: 500;
                    letter-spacing: 0.3px;
                ">
                    Los favoritos de nuestros clientes
                </p>
            </div>
        </div>
        
       <div class="d-flex flex-nowrap overflow-auto pb-3 px-2" style="gap: 1rem; scroll-snap-type: x mandatory;">
    @foreach($topProductos as $producto)
    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
        <div class="card h-100 product-card shadow-sm border-0">
            <!-- Badge de más vendido -->
            <div class="position-absolute top-0 start-0 m-2">
                <span class="badge bg-danger">🔥 Más vendido</span>
            </div>
            
            <div class="card h-100 border-0 product-card @if($producto->stock == 0) out-of-stock @endif">
                <!-- Contenedor de imagen con efecto hover -->
                <div class="product-image-container position-relative overflow-hidden">
                    @if($producto->imagen)
                        {{-- Si la imagen existe y es de storage --}}
                        @if(Str::startsWith($producto->imagen, 'productos/'))
                            <img src="{{ asset('storage/'.$producto->imagen) }}" 
                                 class="product-image" alt="{{ $producto->nombre }}">
                        @else
                            {{-- Si la imagen existe pero no es de storage (imagen por defecto) --}}
                            <img src="{{ asset($producto->imagen) }}" 
                                 class="product-image" alt="{{ $producto->nombre }}">
                        @endif
                    @else
                        {{-- Si no hay imagen, mostrar la imagen por defecto --}}
                        <img src="{{ asset('assets/img/sinimagen.jpeg') }}" 
                             class="product-image" alt="Sin imagen">
                    @endif
                    <div class="image-overlay"></div>
                </div>

                <!-- Detalles del producto -->
                <div class="card-body p-3">
                    <h6 class="product-title mb-2">
                        {{ Str::limit($producto->nombre, 30) }}
                        @if($producto->stock == 0)
                            <span class="out-of-stock-badge">AGOTADO</span>
                        @endif
                    </h6>
                    
                    <div class="product-price mb-2">
                        <strong>Bs {{ number_format($producto->precio_minimo, 2) }}</strong>
                    </div>
                    
                    <div class="product-meta d-flex justify-content-between align-items-center">
                        <span class="stock-badge {{ $producto->stock > 10 ? 'in-stock' : ($producto->stock > 0 ? 'low-stock' : 'no-stock') }}">
                            {{ $producto->stock }}u disponible{{ $producto->stock != 1 ? 's' : '' }}
                        </span>
                    </div>
                </div>

                <!-- Botón de acción -->
                <div class="card-footer p-3 bg-white border-top">
                    @if($producto->stock == 0)
                        <button class="btn w-100 disabled out-of-stock-btn">
                            <i class="fas fa-ban me-2"></i>Agotado
                        </button>
                    @else
                        <a href="{{ route('admin.catalogo.show', $producto->id) }}" 
                        class="btn w-100 view-product-btn">
                            <i class="fas fa-eye me-2"></i>Ver detalles
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
    </div>
</section>
@else
<div class="alert alert-info">Próximamente nuestros productos más vendidos</div>
@endif

    @php
        $productosPorCategoria = $productos->groupBy('categoria_id');
    @endphp

    @forelse($productosPorCategoria as $categoriaId => $productosCategoria)
    @php
        $categoria = $productosCategoria->first()->categoria;
        $cantidadProductos = count($productosCategoria);
        // Tomar solo los primeros 6 productos de cada categoría
        $productosMostrar = $productosCategoria->take(6);
    @endphp

<!-- Encabezado de categoría -->
<div class="d-flex justify-content-between align-items-center mb-3" style="border-bottom: 2px solid #eee; padding-bottom: 8px;">
    <!-- Nombre de categoría y badge (izquierda) -->
    <div class="d-flex align-items-center">
        <h3 class="mb-0" style="font-size: 1.25rem;">
            {{ $categoria->nombre ?? 'Sin categoría' }}
        </h3>
        <span class="badge bg-secondary ms-2" style="font-size: 0.7em;">
            {{ $cantidadProductos }}
        </span>
    </div>
    
    <!-- Botón Ver Más (derecha) -->
    @if($cantidadProductos > 6)
    <a href="{{ route('admin.catalogo.categoria', $categoria->id) }}" 
       class="btn-ver-mas position-relative overflow-hidden">
        <span class="btn-text">Ver más</span>
        <span class="btn-icon">
            <i class="fas fa-arrow-right"></i>
        </span>
        <span class="hover-effect"></span>
    </a>
    @endif
</div>

<style>
    .btn-ver-mas {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1.5rem;
        font-size: 0.9rem;
        font-weight: 500;
        color: #2c7be5;
        background-color: transparent;
        border: 1px solid #2c7be5;
        border-radius: 50px;
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
    }
    
    .btn-ver-mas:hover {
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(44, 123, 229, 0.25);
    }
    
    .btn-ver-mas:hover .btn-icon {
        transform: translateX(3px);
    }
    
    .btn-text {
        transition: all 0.3s ease;
        position: relative;
        z-index: 2;
    }
    
    .btn-icon {
        margin-left: 8px;
        transition: all 0.3s ease;
        position: relative;
        z-index: 2;
        font-size: 0.8rem;
    }
    
    .hover-effect {
        position: absolute;
        top: 0;
        left: 0;
        width: 0;
        height: 100%;
        background-color: #2c7be5;
        transition: width 0.3s ease;
        z-index: 1;
        border-radius: 50px;
    }
    
    .btn-ver-mas:hover .hover-effect {
        width: 100%;
    }
    
    /* Efecto de pulso al hacer hover */
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(44, 123, 229, 0.4); }
        70% { box-shadow: 0 0 0 8px rgba(44, 123, 229, 0); }
        100% { box-shadow: 0 0 0 0 rgba(44, 123, 229, 0); }
    }
    
    .btn-ver-mas:hover {
        animation: pulse 1.5s infinite;
    }
</style>



    <!-- Lista de productos - 6 por fila -->
<div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-2">
         @foreach($productosMostrar as $producto)
    <div class="col p-2">
        <div class="card h-100 border-0 product-card @if($producto->stock == 0) out-of-stock @endif">
            <!-- Contenedor de imagen con efecto hover -->
            <div class="product-image-container position-relative overflow-hidden">
              
                
               
                <img src="{{ $producto->imagen && file_exists(public_path('storage/'.$producto->imagen)) 
                            ? asset('storage/'.$producto->imagen) 
                            : asset('assets/img/sinimagen.jpeg') }}" 
                    class="product-image" alt="{{ $producto->nombre }}">
                <div class="image-overlay"></div>
            </div>

            <!-- Detalles del producto -->
            <div class="card-body p-3">
                <h6 class="product-title mb-2">
                   <b> {{ Str::limit($producto->nombre, 30) }}</b>
                    @if($producto->stock == 0)
                        <span class="out-of-stock-badge">AGOTADO</span>
                    @endif
                </h6>
                
                <div class="product-price mb-2">
                    
                    <strong>Bs {{ number_format($producto->precio_minimo, 2) }}</strong>

                </div>
                
              
            </div>

            <!-- Botón de acción -->
            <div class="card-footer p-3 bg-white border-top">
                @if($producto->stock == 0)
                    <button class="btn w-100 disabled out-of-stock-btn">
                        <i class="fas fa-ban me-2"></i>Agotado
                    </button>
                @else
                    <a href="{{ route('admin.catalogo.show', $producto->id) }}" 
                    class="btn w-100 view-product-btn">
                        <i class="fas fa-eye me-2"></i>Ver detalles
                    </a>
                @endif
            </div>
        </div>
    </div>

<style>
    /* Estilos generales de la tarjeta */
    .product-card {
        border-radius: 12px !important;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    
    .out-of-stock {
        position: relative;
    }
    
    .out-of-stock::after {
        content: "AGOTADO";
        position: absolute;
        top: 10px;
        right: -25px;
        background: #dc3545;
        color: white;
        padding: 2px 25px;
        font-size: 0.7rem;
        font-weight: bold;
        transform: rotate(45deg);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        z-index: 2;
    }
    
    /* Estilos para la imagen */
    .product-image-container {
        height: 160px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    
    .product-image {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }
    
    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0,0,0,0.02) 0%, rgba(0,0,0,0.05) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .product-card:hover .product-image {
        transform: scale(1.05);
    }
    
    .product-card:hover .image-overlay {
        opacity: 1;
    }
    
    /* Estilos para el texto */
    .product-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: #333;
        line-height: 1.3;
        position: relative;
    }
    
    .out-of-stock-badge {
        background: #ffe6e6;
        color: #dc3545;
        font-size: 0.6rem;
        padding: 2px 6px;
        border-radius: 4px;
        margin-left: 5px;
        font-weight: bold;
    }
    
    .product-price {
        font-size: 1rem;
        font-weight: 700;
        color:rgb(112, 169, 235);
    }
    
    /* Estilos para los badges */
    .stock-badge {
        font-size: 0.7rem;
        padding: 4px 8px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    .in-stock {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }
    
    .low-stock {
        background: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }
    
    .no-stock {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    
    .product-category {
        font-size: 0.7rem;
        color: #6c757d;
        background: rgba(108, 117, 125, 0.1);
        padding: 4px 8px;
        border-radius: 50px;
    }
    
    /* Estilos para los botones */
    .view-product-btn {
        background: linear-gradient(135deg,rgb(80, 140, 219) );
        color: white;
        border: none;
        border-radius: 8px;
        padding: 8px;
        font-size: 0.8rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .view-product-btn:hover {
        background: linear-gradient(135deg,rgb(128, 179, 221) 0%,rgb(128, 179, 221) 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }
    
    .out-of-stock-btn {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border: none;
        border-radius: 8px;
        padding: 8px;
        font-size: 0.8rem;
        font-weight: 500;
        cursor: not-allowed;
    }
    
    /* Efecto de pulso para el botón de ver */
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4); }
        70% { box-shadow: 0 0 0 8px rgba(40, 167, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
    }
    
    .view-product-btn:hover {
        animation: pulse 1.5s infinite;
    }

/* SOLO para pantallas móviles */
@media (max-width: 768px) {
  /* Contenedor principal de productos */
  .row.row-cols-2.row-cols-sm-3.row-cols-md-4.row-cols-lg-6.g-2 {
    display: flex !important;
    flex-wrap: nowrap !important;
    overflow-x: auto !important; /* ✅ permite scroll horizontal */
    overflow-y: hidden;
    gap: 0.8rem;
    padding: 10px;
    scroll-snap-type: x mandatory; /* para un scroll suave */
    -webkit-overflow-scrolling: touch; /* hace más fluido el scroll en iOS */
  }

  /* Cada producto */
  .row.row-cols-2.row-cols-sm-3.row-cols-md-4.row-cols-lg-6.g-2 > .col {
    flex: 0 0 auto !important; /* evita que se estiren */
    width: 50% !important; /* ancho de cada producto */
    scroll-snap-align: center; /* hace que “salten” suavemente */
  }

  /* Estilo del scrollbar */
  .row.row-cols-2.row-cols-sm-3.row-cols-md-4.row-cols-lg-6.g-2::-webkit-scrollbar {
    height: 8px;
  }
  .row.row-cols-2.row-cols-sm-3.row-cols-md-4.row-cols-lg-6.g-2::-webkit-scrollbar-thumb {
    background-color: #bdc7c9ff;
    border-radius: 10px;
  }
  .row.row-cols-2.row-cols-sm-3.row-cols-md-4.row-cols-lg-6.g-2::-webkit-scrollbar-track {
    background: #e0f7fa;
  }
}

    
</style>
    @endforeach
</div>

    @empty
    <div class="col-12 py-3">
        <div class="alert alert-warning text-center py-2" style="font-size: 0.9rem;">
            <i class="fas fa-exclamation-circle me-2"></i>
            No se encontraron productos
        </div>
    </div>
    @endforelse

</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#load-more').click(function() {
        const button = $(this);
        const nextPage = button.data('next-page');
        const categoria = "{{ request('categoria') }}";
        
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Cargando...');
        
        $.ajax({
            url: "{{ route('admin.catalogo.index') }}",
            data: {
                page: nextPage,
                categoria: categoria,
                ajax: true
            },
            success: function(response) {
                if(response.html) {
                    $('#productos-container').append(response.html);
                    button.data('next-page', response.nextPage);
                    
                    if(!response.hasMorePages) {
                        button.remove();
                    } else {
                        button.prop('disabled', false).html('<i class="fas fa-plus-circle me-2"></i> Ver más productos');
                    }
                }
            },
            error: function() {
                button.prop('disabled', false).html('<i class="fas fa-plus-circle me-2"></i> Ver más productos');
            }
        });
    });
});
</script>
@endsection

<style>
    /* Estilos para las cards */
    .card {
        transition: all 0.2s ease;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 10px rgba(0,0,0,0.1) !important;
    }
    
    /* Responsive para 6 columnas */
    @media (min-width: 1200px) {
        .row-cols-lg-6 > * {
            flex: 0 0 16.666667%;
            max-width: 16.666667%;
        }
    }
    @media (max-width: 1199px) {
        .row-cols-lg-6 > * {
            flex: 0 0 25%;
            max-width: 25%;
        }
    }
    @media (max-width: 767px) {
        .row-cols-lg-6 > * {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
</style>
@extends('layouts.public')

@section('content')

<!-- Header del catálogo -->
<div class="container mb-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold text-primary mb-3"></h1>
        @if(request()->has('categoria') && $categoriaSeleccionada = $categorias->firstWhere('id', request('categoria')))
            <div class="d-inline-flex align-items-center gap-3">
                <p class="lead text-muted mb-0">
                    Categoría: 
                    <span class="badge bg-primary">
                        <i class="fas fa-tag me-1"></i> {{ $categoriaSeleccionada->nombre }}
                    </span>
                </p>
                <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-times me-1"></i> Limpiar filtro
                </a>
            </div>
        @else
            <p class="lead text-muted">Explora nuestra amplia selección de productos de farmacia</p>
        @endif
    </div>

    <!-- Listado de productos por categoría -->
    @php
        $productosPorCategoria = $productos->groupBy('categoria_id');
    @endphp

    @forelse($productosPorCategoria as $categoriaId => $productosCategoria)
        @php
            $categoria = $productosCategoria->first()->categoria ?? null;
            $cantidadProductos = count($productosCategoria);
            $productosMostrar = $productosCategoria->take(6);
        @endphp

        <div class="mb-5">
            <div class="d-flex align-items-center mb-3">
                <h3 class="mb-0" style="font-size: 1.25rem;">
                    {{ $categoria->nombre ?? 'Sin categoría' }}
                </h3>
                <span class="badge bg-secondary ms-2" style="font-size: 0.7em;">
                    {{ $cantidadProductos }} productos
                </span>
            </div>
            
            <!-- Productos de esta categoría -->
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
                @foreach($productosMostrar as $producto)
                <div class="col p-2">
                    <div class="card h-100 border-0 product-card @if($producto->stock == 0) out-of-stock @endif">
                        <!-- Contenedor de imagen con efecto hover -->
                        <div class="product-image-container position-relative overflow-hidden">
                            <img src="{{ $producto->imagen ? asset('storage/'.$producto->imagen) : asset('img/default-medicine.png') }}" 
                                 class="product-image" alt="{{ $producto->nombre }}">
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
                               
                                Bs {{ number_format($producto->precio_minimo, 2) }}

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
                @endforeach
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> No hay productos disponibles en esta categoría actualmente.
            </div>
        </div>
    @endforelse
</div>

<!-- Paginación mejorada -->
@if($productos->hasPages())
    <div class="container mt-5">
        <nav aria-label="Page navigation">
            {{ $productos->links('pagination::bootstrap-5') }}
        </nav>
    </div>
@endif

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
        color:rgb(112, 169, 2351);
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
    
    /* Estilos para los botones */
    .view-product-btn {
        background: linear-gradient(135deg,rgb(78, 152, 236) );
        color: white;
        border: none;
        border-radius: 8px;
        padding: 8px;
        font-size: 0.8rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .view-product-btn:hover {
        background: linear-gradient(135deg,rgb(87, 89, 221) );
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
</style>

@endsection
@extends('layouts.public')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-search me-2"></i>
            Resultados de búsqueda: "{{ $terminoBusqueda }}"
            <small class="text-muted fs-6">({{ $productos->total() }} productos)</small>
        </h2>
        <a href="{{ route('admin.catalogo.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver al catálogo
        </a>
    </div>

    @if($productos->isEmpty())
        <div class="alert alert-info text-center py-4">
            <i class="fas fa-info-circle fa-2x mb-3"></i>
            <h4>No se encontraron productos para "{{ $terminoBusqueda }}"</h4>
            <p class="mb-0">Intenta con otro término de búsqueda</p>
        </div>
    @else
    <div class="container">
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
        @forelse($productos as $producto)
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
        </style>
        @empty
            <div class="col-12 text-center py-5">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> No hay productos disponibles en esta categoría actualmente.
                </div>
            </div>
        @endforelse
    </div>
</div>


        <!-- Paginación -->
        <div class="mt-4 d-flex justify-content-center">
            {{ $productos->appends(['search' => $terminoBusqueda])->links() }}
        </div>
    @endif
</div>
@endsection

<style>
.product-image-container {
    height: 180px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    padding: 1rem;
}

.product-image-container img {
    max-height: 100%;
    width: auto;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.card:hover .product-image-container img {
    transform: scale(1.05);
}

.card {
    transition: all 0.3s ease;
    border-radius: 0.5rem;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
</style>
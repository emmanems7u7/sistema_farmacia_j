@extends('layouts.public')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8"> <!-- Aumenté el ancho para acomodar el nuevo diseño -->
            <!-- Botón para volver -->
            <a href="{{ route('admin.catalogo.index') }}" class="btn btn-outline-primary mb-4 rounded-pill">
                <i class="fas fa-arrow-left me-2"></i> Volver al catálogo
            </a>

            <!-- Tarjeta principal con diseño horizontal -->
            <div class="card border-0 shadow-lg rounded-3 overflow-hidden">
                <div class="row g-0">
                    <!-- Sección de imagen (lado izquierdo) -->
                    <div class="col-md-5">
                        <div class="product-image-container h-100 d-flex align-items-center justify-content-center p-4 bg-light">
                            <img src="{{ $producto->imagen ? asset('storage/'.$producto->imagen) : asset('img/default-medicine.png') }}" 
                                 alt="{{ $producto->nombre }}" 
                                 class="img-fluid" 
                                 style="max-height: 300px; width: auto; object-fit: contain; mix-blend-mode: multiply;">
                        </div>
                    </div>
                    
                    <!-- Sección de información (lado derecho) -->
                    <div class="col-md-7">
                        <div class="card-body p-4 h-100 d-flex flex-column">
                            <!-- Encabezado con nombre y precio -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h1 class="h2 font-weight-bold text-dark mb-2">{{ $producto->nombre }}</h1>
                                    @if($producto->laboratory)
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 mb-2">
                                        <i class="fas fa-flask me-1"></i> {{ $producto->laboratory }}
                                    </span>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <span class="h3 text-primary fw-bold">
                                        
                                        
                                        Bs {{ number_format($producto->precio_minimo, 2) }}

                                    </span>
                                    <div class="text-muted small">Precio </div>
                                </div>
                            </div>

                            <!-- Indicador de stock -->
                            <div class="alert alert-{{ $producto->stock > 10 ? 'success' : 'warning' }} d-flex align-items-center py-2 mb-4 rounded">
                                <i class="fas fa-{{ $producto->stock > 10 ? 'check-circle' : 'exclamation-triangle' }} fa-lg me-3"></i>
                                <div>
                                    <strong class="d-block">{{ $producto->stock > 10 ? 'Disponible' : 'Stock limitado' }}</strong>
                                    <span class="small">{{ $producto->stock }} unidades disponibles</span>
                                </div>
                            </div>

                            <!-- Descripción del producto -->
                            <div class="mb-4 flex-grow-1">
                                <h5 class="text-uppercase text-muted mb-3 fw-bold small">Descripción</h5>
                                <p class="card-text text-dark lh-lg">{{ $producto->descripcion }}</p>
                            </div>
                            
                            <!-- Detalles adicionales -->
                            <div class="row mt-auto">
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-barcode text-muted me-2"></i>
                                        <div>
                                            <div class="text-muted small">Código</div>
                                            <div class="fw-medium">{{ $producto->codigo }}</div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Puedes agregar más detalles aquí si es necesario -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .product-image-container {
        min-height: 300px;
        background-color: #f8f9fa;
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .badge {
        font-weight: 500;
        letter-spacing: 0.5px;
    }
    
    .rounded-3 {
        border-radius: 1rem !important;
    }
    
    .lh-lg {
        line-height: 1.7;
    }
    
    .shadow-lg {
        box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important;
    }
    
    @media (max-width: 767.98px) {
        .product-image-container {
            min-height: 200px;
        }
    }
</style>
@endsection
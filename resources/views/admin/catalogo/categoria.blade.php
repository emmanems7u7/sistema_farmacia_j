@extends('layouts.public')

@section('content')
<div class="container py-4">
    <!-- Encabezado de la categoría -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.catalogo.index') }}" class="text-decoration-none">
                            <i class="fas fa-home"></i> Inicio121
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $categoria->nombre }}
                    </li>
                </ol>
            </nav>
            
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h2 text-primary">{{ $categoria->nombre }}</h1>
                <span class="badge bg-primary fs-6">
                    {{ $productos->total() }} productos
                </span>
            </div>
            
            @if($categoria->descripcion)
            <p class="text-muted mt-2">{{ $categoria->descripcion }}</p>
            @endif
        </div>
    </div>

    <!-- Grid de Productos -->
    <div class="row">
        @if($productos->count() > 0)
            @foreach($productos as $producto)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 shadow-sm border-0 product-card">
                    <!-- Imagen del Producto -->
                    <div class="product-image-container position-relative">
                        <img src="{{ $producto->imagen && file_exists(public_path('storage/'.$producto->imagen)) 
                            ? asset('storage/'.$producto->imagen) 
                            : asset('assets/img/sinimagen.jpeg') }}" 
                             alt="{{ $producto->nombre }}"
                             class="card-img-top product-image">
                        
                        <!-- Badge de Stock -->
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge {{ $producto->stock > 10 ? 'bg-success' : 'bg-warning' }}">
                                {{ $producto->stock }} unidades
                            </span>
                        </div>
                    </div>

                    <!-- Contenido de la Tarjeta -->
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title product-name">{{ Str::limit($producto->nombre, 50) }}</h5>
                        
                        @if($producto->laboratory)
                        <p class="text-muted small mb-2">
                            <i class="fas fa-flask"></i> {{ $producto->laboratory }}
                        </p>
                        @endif

                        <p class="card-text text-muted small flex-grow-1">
                            {{ Str::limit($producto->descripcion, 80) }}
                        </p>

                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 text-primary mb-0">
                                    Bs {{ number_format($producto->precio_minimo, 2) }}
                                </span>
                            </div>
                            
                            <!-- Botón Ver Detalles -->
                            <a href="{{ route('admin.catalogo.show', $producto->id) }}" 
                               class="btn btn-outline-primary btn-sm w-100 mt-2">
                                <i class="fas fa-eye me-1"></i> Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No hay productos en esta categoría</h4>
                <p class="text-muted">Próximamente agregaremos más productos.</p>
                <a href="{{ route('admin.catalogo.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-1"></i> Volver al Catálogo
                </a>
            </div>
        @endif
    </div>

    <!-- Paginación -->
    @if($productos->hasPages())
    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-center">
            {{ $productos->links() }}
        </div>
    </div>
    @endif
</div>

<style>
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 10px;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.product-image-container {
    height: 200px;
    overflow: hidden;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

.product-image {
    height: 100%;
    object-fit: contain;
    padding: 15px;
    mix-blend-mode: multiply;
}

.product-name {
    min-height: 48px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.breadcrumb {
    background-color: transparent;
    padding: 0;
}

.breadcrumb-item a {
    color: #6c757d;
}

.breadcrumb-item.active {
    color: #495057;
}
</style>
@endsection
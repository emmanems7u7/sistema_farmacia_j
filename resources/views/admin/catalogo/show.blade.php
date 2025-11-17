@extends('layouts.public')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8"> 
            <a href="{{ route('admin.catalogo.index') }}" class="text-decoration-none text-primary fw-bold">
                Inicio
            </a>

            <span class="mx-2 fw-bold">/ {{ $producto->categoria->nombre }}</span>

           
            <div class="card border-0 shadow-lg rounded-3 overflow-hidden">
                <div class="row g-0">
                  
                    <div class="col-md-5">
                        <div class="product-image-container h-100 d-flex align-items-center justify-content-center p-4 bg-light">
                            <img src="{{ $producto->imagen && file_exists(public_path('storage/'.$producto->imagen)) 
                                ? asset('storage/'.$producto->imagen) 
                                : asset('assets/img/sinimagen.jpeg') }}" 
                                alt="{{ $producto->nombre }}"
                                class="img-fluid" 
                                style="max-height: 300px; width: auto; object-fit: contain; mix-blend-mode: multiply;">
                        </div>
                    </div>
                    
                    
                    <div class="col-md-7">
                        <div class="card-body p-4 h-100 d-flex flex-column">
                           
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

                            <!-- Indicador de stock simplificado -->
                            <div class="alert alert-info d-flex align-items-center py-2 mb-4 rounded">
                                <i class="fas fa-cubes fa-lg me-3"></i>
                                <div>
                                    <strong class="d-block">Disponible</strong>
                                    <span class="small">{{ $producto->stock }} Unidades</span>
                                </div>
                            </div>

                            <!-- Descripción del producto -->
                            <div class="mb-4 flex-grow-1">
                                <h5 class="text-uppercase text-muted mb-3 fw-bold small">Descripción</h5>
                                <p class="card-text text-dark lh-lg">{{ $producto->descripcion }}</p>
                            </div>
                            
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 <!-- Botón flotante de WhatsApp con funcionalidades -->
<div class="whatsapp-floating">
    <!-- Botón principal -->
    <div class="shadow rounded-circle p-2 whatsapp-main-btn" style="background-color: #25D366;">
    <a href="#" class="text-decoration-none d-flex align-items-center justify-content-center">
        <i class="fab fa-whatsapp fa-2x text-white"></i>
    </a>
</div>
    
    <!-- Menú de opciones  -->
    <div class="whatsapp-options" style="display: none;">
        <div class="bg-white shadow rounded p-3 mb-2">
            <h6 class="text-success mb-3">Servicios por WhatsApp</h6>
            
            <!-- Asesoramiento -->
            <a href="https://wa.me/59169917597?text=Hola! Necesito asesoramiento sobre: {{ $producto->nombre }} - Código: {{ $producto->codigo ?? 'N/A' }}" 
               class="btn btn-outline-success btn-sm w-100 mb-2" target="_blank">
               <i class="fas fa-user-md me-1"></i> Asesoramiento
            </a>
            
            <!-- Reserva  -->
            @if($producto->stock > 0 && $producto->stock <= 5)
            <a href="https://wa.me/59169917597?text=Hola! Quiero RESERVAR: {{ $producto->nombre }} - Código: {{ $producto->codigo ?? 'N/A' }} - Stock: {{ $producto->stock }} unidades" 
               class="btn btn-warning btn-sm w-100 mb-2" target="_blank">
               <i class="fas fa-lock me-1"></i> Reservar
            </a>
            @endif
            
            <!-- Entrega programada -->
            <a href="https://wa.me/59169917597?text=Hola! Quiero programar entregas de: {{ $producto->nombre }} - Código: {{ $producto->codigo ?? 'N/A' }}" 
               class="btn btn-outline-primary btn-sm w-100 mb-2" target="_blank">
               <i class="fas fa-calendar-alt me-1"></i> Programar
            </a>
            
            <!-- Equivalentes -->
            <a href="https://wa.me/59169917597?text=Hola! Consulto equivalentes de: {{ $producto->nombre }} - Código: {{ $producto->codigo ?? 'N/A' }}" 
               class="btn btn-outline-info btn-sm w-100" target="_blank">
               <i class="fas fa-exchange-alt me-1"></i> Equivalentes
            </a>
        </div>
    </div>
</div>

<style>
.whatsapp-floating {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
}

.whatsapp-options {
    position: absolute;
    bottom: 70px;
    right: 0;
    min-width: 250px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mainBtn = document.querySelector('.whatsapp-main-btn');
    const options = document.querySelector('.whatsapp-options');
    
    mainBtn.addEventListener('click', function(e) {
        e.preventDefault();
        options.style.display = options.style.display === 'none' ? 'block' : 'none';
    });
    
    // Cerrar menú al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.whatsapp-floating')) {
            options.style.display = 'none';
        }
    });
});
</script>

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
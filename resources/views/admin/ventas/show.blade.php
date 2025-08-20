@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Detalle de Venta'])

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="ni ni-money-coins me-2 text-primary"></i>
                        <h6>Detalle de Venta </h6>
                    </div>
                    <div>
                        <a href="{{ url('/admin/ventas') }}" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="ni ni-bold-left me-1"></i> Volver al listado
                        </a>
                        <a href="{{ url('/admin/ventas/pdf/' . $venta->id) }}" target="_blank" class="btn btn-sm btn-danger">
                            <i class="ni ni-single-copy-04 me-1"></i> Exportar PDF
                        </a>
                    </div>
                </div>
             </div>   
            <div class="card-body">
    <div class="row">
        <!-- Sección de productos -->
        <div class="col-md-8">
            <div class="card mb-4"> <!-- Añadido contenedor card para la tabla -->
                <div class="card-body p-0"> <!-- Añadido card-body -->
                    <div class="table-responsive">
                        <div class="card-header pb-0">
                    <h6 class="mb-0">
                        <i class="ni ni-single-02 me-1 text-primary"></i> Información del Producto
                    </h6>
                </div>
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">#</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Código</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Cantidad</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Producto</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">P. Unitario</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    $total_cantidad = 0; 
                                    $total_venta = 0; 
                                @endphp
                                
                                @foreach($venta->detallesVenta as $index => $detalle)
                                    @php
                                        // Obtener el lote con cantidad positiva más reciente
                                        $lote = $detalle->producto->lotes()->where('cantidad', '>', 0)->orderBy('fecha_ingreso', 'desc')->first();

                                        // Precio venta del lote o fallback a 0 si no existe lote
                                        $precioVenta = $lote ? $lote->precio_venta : 0;

                                        // Calcular subtotal con precio del lote
                                        $subtotal = $precioVenta * $detalle->cantidad;

                                        $total_cantidad += $detalle->cantidad;
                                        $total_venta += $subtotal;
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $index + 1 }}</span>
                                        </td>
                                        <td>
                                            <span class="text-secondary text-xs font-weight-bold">{{ $detalle->producto->codigo }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $detalle->cantidad }}</span>
                                        </td>
                                        <td>
                                            <span class="text-secondary text-xs font-weight-bold">{{ $detalle->producto->nombre }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="text-secondary text-xs font-weight-bold">Bs {{ number_format($precioVenta, 2) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="text-secondary text-xs font-weight-bold">Bs {{ number_format($subtotal, 2) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mostrar total venta --}}
                    <div class="card-footer"> <!-- Añadido card-footer para el total -->
                        <div class="d-flex justify-content-between">
                            <span class="text-sm font-weight-bold">Total venta:</span>
                            <strong class="text-success text-sm">Bs {{ number_format($total_venta, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de información del cliente y totales -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header pb-0">
                    <h6 class="mb-0">
                        <i class="ni ni-single-02 me-1 text-primary"></i> Información del Cliente
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <div class="mb-3">
                        <label class="form-label text-xs">Nombre del cliente</label>
                        <div class="form-control form-control-alternative">
                            {{ $venta->cliente ? $venta->cliente->nombre_cliente : 'Cliente no especificado' }}
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-xs">NIT/CI</label>
                        <div class="form-control form-control-alternative">
                            {{ $venta->cliente ? $venta->cliente->nit_ci : 'NIT/CI no especificado' }}
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-xs">Fecha de venta</label>
                        <div class="form-control form-control-alternative">
                            {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header pb-0">
                    <h6 class="mb-0">
                        <i class="ni ni-collection me-1 text-primary"></i> Totales
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-sm">Total productos:</span>
                        <strong class="text-sm">{{ $total_cantidad }}</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <span class="text-sm">Total venta:</span>
                        <strong class="text-success text-sm">Bs {{ number_format($total_venta, 2) }}</strong>
                    </div>
                    <div>
                        <a href="{{ url('/admin/ventas/' . $venta->id . '/edit') }}" class="btn btn-sm bg-gradient-success ">
                            <i class="ni ni-ruler-pencil me-1"></i> Editar
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
                
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">
                            <i class="ni ni-single-02 me-1"></i> Venta registrada por: {{ $venta->user->name ?? 'Usuario no disponible' }}
                        </small>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footers.auth.footer')
</div>
@endsection

@push('css')
<style>
    /* Estilos personalizados adicionales */
    .form-control-alternative {
        background-color: #f8f9fa;
        border: none;
        box-shadow: none;
        padding: 0.5rem 1rem;
        min-height: auto;
        font-size: 0.875rem;
    }
    
    .card-header h6 {
        display: flex;
        align-items: center;
    }
    
    .table thead th {
        font-size: 0.75rem;
    }
    
    .table tbody td {
        font-size: 0.8125rem;
    }
    
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .card-header > div {
            margin-top: 1rem;
            width: 100%;
        }
        
        .card-body .row {
            flex-direction: column-reverse;
        }
        
        .col-md-8, .col-md-4 {
            width: 100%;
            max-width: 100%;
        }
        
        .card.mb-3 {
            margin-top: 1.5rem;
        }
    }
</style>
@endpush

@push('js')
<script>
    // Inicialización de tooltips si es necesario
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });
</script>
@endpush
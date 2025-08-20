@extends('layouts.app', ['title' => 'Gestión de Inventario - Bajo Stock'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Gestión de Inventario'])
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle text-warning me-2 fs-5"></i>
                        <h5 class="mb-0 fw-bold">Productos con Bajo Stock</h5>
                    </div>
                    
                    @if($sucursalId > 0)
                        <span class="badge bg-primary fs-6 mt-2 mt-md-0">
                            <i class="fas fa-store me-1"></i> {{ App\Models\Sucursal::find($sucursalId)->nombre }}
                        </span>
                    @endif
                    
                    <div>
                        <a href="{{ route('admin.inventario.index') }}?sucursal={{ $sucursalId }}" 
                           class="btn btn-sm btn-outline-primary d-flex align-items-center">
                            <i class="fas fa-arrow-left me-2"></i>
                            <span>Volver al Inventario</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Filtros adicionales -->
            <div class="card shadow-sm mb-4">
                <div class="card-body py-2">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div class="d-flex align-items-center mb-2 mb-md-0">
                            <span class="me-2 fw-bold">Filtrar:</span>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ request()->fullUrlWithQuery(['alerta' => 'critico']) }}" 
                                   class="btn btn-outline-danger">
                                    <i class="fas fa-fire me-1"></i> Crítico (-5)
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['alerta' => 'advertencia']) }}" 
                                   class="btn btn-outline-warning">
                                    <i class="fas fa-exclamation me-1"></i> Advertencia (-10)
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['alerta' => 'precaucion']) }}" 
                                   class="btn btn-outline-info">
                                    <i class="fas fa-info-circle me-1"></i> Precaución (-15)
                                </a>
                                <a href="{{ request()->url() }}" 
                                   class="btn btn-outline-secondary">
                                    <i class="fas fa-broom me-1"></i> Limpiar
                                </a>
                            </div>
                        </div>
                        
                        <span class="badge bg-danger fs-6">
                            <i class="fas fa-box-open me-1"></i> {{ $productos->total() }} Productos con bajo stock
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tabla de productos -->
            <div class="card shadow border-0">
                <div class="card-header bg-white py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-list-alt me-2 text-dark"></i>
                            <h6 class="mb-0 fw-bold">LISTADO DE PRODUCTOS</h6>
                        </div>
                        <div class="text-muted small">
                            Mostrando {{ $productos->firstItem() }} - {{ $productos->lastItem() }} de {{ $productos->total() }}
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Código</th>
                                    <th>Producto</th>
                                    <th class="text-center">Stock Actual</th>
                                    <th class="text-center">Mínimo</th>
                                    
                                    <th class="text-center">Estado</th>
                                    <th class="text-center pe-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productos as $producto)
                                @php
                                    $stockActual = $producto->lotes->sum('cantidad');
                                    $diferencia = $stockActual - $producto->stock_minimo;
                                    
                                    // Definición de niveles de alerta mejorados
                                    if($stockActual <= 0) {
                                        $statusClass = 'danger';
                                        $icon = 'fa-times-circle';
                                        $nivel = 'SIN STOCK';
                                    } elseif($stockActual <= 5) {
                                        $statusClass = 'danger';
                                        $icon = 'fa-fire';
                                        $nivel = 'CRÍTICO';
                                    } elseif($stockActual <= 10) {
                                        $statusClass = 'warning';
                                        $icon = 'fa-exclamation-triangle';
                                        $nivel = 'ADVERTENCIA';
                                    } elseif($stockActual <= 15) {
                                        $statusClass = 'info';
                                        $icon = 'fa-info-circle';
                                        $nivel = 'PRECAUCIÓN';
                                    } else {
                                        $statusClass = 'success';
                                        $icon = 'fa-check-circle';
                                        $nivel = 'NORMAL';
                                    }
                                @endphp
                                <tr class="bg-{{ $statusClass }}-soft align-middle">
                                    <td class="ps-3 fw-bold">{{ $producto->codigo ?? 'N/A' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light rounded-circle me-2" style="width: 28px; height: 28px;">
                                                <i class="fas fa-pills text-{{ $statusClass }}"></i>
                                            </div>
                                            <div>
                                                <span class="d-block fw-bold">{{ $producto->nombre }}</span>
                                                <small class="text-muted">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold text-{{ $statusClass }}">
                                        {{ $stockActual }}
                                    </td>
                                    <td class="text-center">{{ $producto->stock_minimo }}</td>
                                   
                                    <td class="text-center">
                                        <span class="badge bg-{{ $statusClass }} py-1 px-2">
                                            <i class="fas {{ $icon }} me-1"></i> {{ $nivel }}
                                        </span>
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ route('admin.productos.create', $producto->id) }}" 
                                               class="btn btn-xs btn-primary mx-1" 
                                               data-bs-toggle="tooltip" 
                                               title="Reabastecer">
                                                <i class="fas fa-warehouse"></i>
                                            </a>
                                            
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-light py-2">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div class="legend mb-2 mb-md-0">
                            <span class="badge bg-danger-soft text-danger me-2">
                                <i class="fas fa-fire me-1"></i> Crítico (≤5)
                            </span>
                            <span class="badge bg-warning-soft text-warning me-2">
                                <i class="fas fa-exclamation-triangle me-1"></i> Advertencia (≤10)
                            </span>
                            <span class="badge bg-info-soft text-info me-2">
                                <i class="fas fa-info-circle me-1"></i> Precaución (≤15)
                            </span>
                            <span class="badge bg-success-soft text-success">
                                <i class="fas fa-check-circle me-1"></i> Normal (>15)
                            </span>
                        </div>
                        <div>
                            {{ $productos->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .bg-danger-soft {
        background-color: rgba(220, 53, 69, 0.08) !important;
    }
    .bg-warning-soft {
        background-color: rgba(255, 193, 7, 0.08) !important;
    }
    .bg-info-soft {
        background-color: rgba(23, 162, 184, 0.08) !important;
    }
    .bg-success-soft {
        background-color: rgba(25, 135, 84, 0.08) !important;
    }
    .table-sm th, .table-sm td {
        padding: 0.5rem;
        font-size: 0.85rem;
    }
    .btn-xs {
        padding: 0.25rem 0.4rem;
        font-size: 0.75rem;
    }
    .badge {
        font-weight: 500;
        letter-spacing: 0.3px;
    }
</style>

@endsection
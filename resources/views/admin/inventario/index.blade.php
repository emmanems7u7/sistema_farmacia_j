@extends('layouts.argon')

@section('content')

<div class="container-fluid py-4">
    <!-- Tarjeta de título mejorada -->
    <div class="row">
        <div class="col-12 mb-2">
            
                
               <!-- Filtro por Sucursal -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        
                        <div>
                            <h4 class="mb-0 text-dark font-weight-bolder">Gestión de Inventario</h4>
                            
                        </div>
                    </div>
                    <form id="filtroSucursal">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Seleccionar Sucursal</label>
                                <select class="form-select" id="selectSucursal" name="sucursal">
                                    <option value="0">Todas las Sucursales</option>
                                    @foreach($sucursales as $sucursal)
                                        <option value="{{ $sucursal->id }}" {{ $sucursalId == $sucursal->id ? 'selected' : '' }}>
                                            {{ $sucursal->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mt-md-0 mt-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ni ni-filter"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Resumen -->
    <div class="row">
        <!-- Card 1: Total Productos -->
        
        <!-- Card de Productos por Sucursal -->
<div class="col-lg-4 col-md-6 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        @if($sucursalId)
                            Productos en {{ $sucursalNombre }}
                        @else
                            Total Productos
                        @endif
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        @if($totalProductos > 0 || !$sucursalId)
                            {{ $totalProductos }}
                        @else
                            No hay productos
                        @endif
                    </div>
                </div>
                <div class="col-auto">
                    <i class="ni ni-box-2 fa-2x text-gray-300"></i>
                </div>
            </div>
            <div class="mt-3 text-center">
                @if($totalProductos > 0 || !$sucursalId)
                    <a href="{{ route('admin.productos.index') }}@if($sucursalId)?sucursal={{ $sucursalId }}@endif" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="ni ni-zoom-split-in"></i> Ver Detalles
                    </a>
                @else
                    <button class="btn btn-sm btn-secondary" disabled>
                        <i class="ni ni-fat-remove"></i> Sin productos
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

        <!-- Card 2: Productos Bajo Stock -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Productos Bajo Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $productosBajoStock }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="ni ni-notification-70 fa-2x text-gray-300"></i>
                        </div>
                    </div>
                            <div class="mt-3">
                            <a href="{{ route('admin.inventario.bajo_stock') }}?sucursal={{ $sucursalId }}" 
                            class="btn btn-sm btn-outline-warning btn-block">
                                <i class="fas fa-search mr-1"></i> Ver Listado
                            </a>
                        </div>
                        
                    
                </div>
            </div>
        </div>

        <!-- Card 3: Productos por Vencer -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Productos por Vencer (30 días)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $productosPorVencer }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="ni ni-watch-time fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.inventario.productos_porvencer', ['sucursal' => $sucursalId ?? null]) }}" 
                        class="btn btn-sm btn-outline-warning btn-block">
                            <i class="fas fa-search mr-1"></i> Ver Listado
                        </a>
                    </div>
    
                </div>
            </div>
        </div>

<div class="col-lg-4 col-md-6 mb-4">
    <div class="card border-left-primary shadow h-100">
        <!-- Card Header con Filtro -->
        <div class="card-header bg-white py-2">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-xs font-weight-bold text-primary text-uppercase">
                    Reporte de Compras
                </div>
                @if($mesesDisponibles->count() > 0)
                <form method="get" action="" class="form-inline" id="formFiltroCompras">
                    @if($sucursalId > 0)
                        <input type="hidden" name="sucursal" value="{{ $sucursalId }}">
                    @endif
                    <select name="month" class="form-control form-control-sm border-primary">
                        @foreach($mesesDisponibles as $mes)
                            <option value="{{ $mes->format('Y-m') }}" 
                                {{ $mesSeleccionado->format('Y-m') == $mes->format('Y-m') ? 'selected' : '' }}>
                                {{ $mes->translatedFormat('M Y') }}
                            </option>
                        @endforeach
                    </select>
                </form>
                @else
                <span class="badge badge-warning">No hay datos</span>
                @endif
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body">
            @if($mesTieneCompras && $cantidadCompras > 0)
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="mb-3">
                        <div class="text-xs font-weight-bold text-primary mb-1">
                            COMPRAS REALIZADAS
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $cantidadCompras }} {{ $cantidadCompras == 1 ? 'compra' : 'compras' }}
                        </div>
                    </div>
                    
                    <div class="mb-1">
                        <div class="text-xs font-weight-bold text-primary mb-1">
                            TOTAL COMPRADO
                        </div>
                        <div class="h4 font-weight-bold text-primary">
                            ${{ number_format($totalComprasMes, 2) }}
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                </div>
            </div>
            @else
            <div class="text-center py-4">
                <i class="fas fa-shopping-cart fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-gray-500">No se realizaron compras</h5>
                <p class="small text-muted">En {{ $mesSeleccionado->translatedFormat('F Y') }}</p>
            </div>
            @endif
        </div>

        <!-- Card Footer con Botón de Imprimir -->
        <div class="card-footer bg-white py-2 text-center">
            @if($mesTieneCompras && $cantidadCompras > 0)
            <button class="btn btn-sm btn-outline-primary" onclick="imprimirReporte('{{ $mesSeleccionado->format('Y-m') }}')">
                <i class="fas fa-print mr-1"></i> Imprimir Reporte
            </button>
            @else
            <button class="btn btn-sm btn-outline-secondary" disabled>
                <i class="fas fa-print mr-1"></i> Sin datos para imprimir
            </button>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar el cambio de mes
    document.querySelector('select[name="month"]').addEventListener('change', function() {
        document.getElementById('formFiltroCompras').submit();
    });

    // Función para imprimir
    function imprimirReporte(mes) {
        let url = "{{ route('admin.inventario.reporte_compras') }}?month=" + mes;
        @if($sucursalId > 0)
            url += "&sucursal={{ $sucursalId }}";
        @endif
        window.open(url, '_blank');
    }
    window.imprimirReporte = imprimirReporte;
});
</script>

        <!-- Card 5: Ventas Totales -->
<div class="col-lg-4 col-md-6 mb-4">
    <div class="card border-left-success shadow h-100">
        <!-- Card Header con Filtro -->
        <div class="card-header bg-white py-2">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-xs font-weight-bold text-success text-uppercase">
                    Reporte de Ventas
                </div>
                @if($mesesDisponibles->count() > 0)
                <form method="get" action="{{ url()->current() }}" class="form-inline" id="formFiltroVentas">
                    @if($sucursalId > 0)
                        <input type="hidden" name="sucursal" value="{{ $sucursalId }}">
                    @endif
                    <!-- Mantener otros parámetros GET existentes -->
                    @foreach(request()->except(['month', 'sucursal', '_token']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    
                    <select name="month" class="form-control form-control-sm border-success">
                        @foreach($mesesDisponibles as $mes)
                            <option value="{{ $mes->format('Y-m') }}" 
                                {{ $mesSeleccionado->format('Y-m') == $mes->format('Y-m') ? 'selected' : '' }}>
                                {{ $mes->translatedFormat('M Y') }}
                            </option>
                        @endforeach
                    </select>
                </form>
                @else
                <span class="badge badge-warning">No hay datos</span>
                @endif
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body">
            @if($mesTieneVentas && $cantidadVentas > 0)
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="mb-3">
                        <div class="text-xs font-weight-bold text-success mb-1">
                            VENTAS REALIZADAS
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $cantidadVentas }} {{ $cantidadVentas == 1 ? 'venta' : 'ventas' }}
                        </div>
                    </div>
                    
                    <div class="mb-1">
                        <div class="text-xs font-weight-bold text-success mb-1">
                            TOTAL VENDIDO
                        </div>
                        <div class="h4 font-weight-bold text-success">
                            ${{ number_format($totalVentasMes, 2) }}
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-cash-register fa-2x text-gray-300"></i>
                </div>
            </div>
            @else
            <div class="text-center py-4">
                <i class="fas fa-cash-register fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-gray-500">No se realizaron ventas</h5>
                <p class="small text-muted">En {{ $mesSeleccionado->translatedFormat('F Y') }}</p>
            </div>
            @endif
        </div>

        <!-- Card Footer con Botón de Imprimir -->
        <div class="card-footer bg-white py-2 text-center">
            @if($mesTieneVentas && $cantidadVentas > 0)
            <button class="btn btn-sm btn-outline-success" onclick="imprimirReporteVentas('{{ $mesSeleccionado->format('Y-m') }}')">
                <i class="fas fa-print mr-1"></i> Imprimir Reporte
            </button>
            @else
            <button class="btn btn-sm btn-outline-secondary" disabled>
                <i class="fas fa-print mr-1"></i> Sin datos para imprimir
            </button>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar el cambio de mes
    document.querySelector('#formFiltroVentas select[name="month"]').addEventListener('change', function() {
        document.getElementById('formFiltroVentas').submit();
    });

    // Función para imprimir reporte de ventas
    function imprimirReporteVentas(mes) {
        let url = "{{ route('admin.inventario.reporte_ventas') }}?month=" + mes;
        @if($sucursalId > 0)
            url += "&sucursal={{ $sucursalId }}";
        @endif
        window.open(url, '_blank');
    }
    window.imprimirReporteVentas = imprimirReporteVentas;
});
</script>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Reportes Generales
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                @if($sucursalId > 0)
                                    Sucursal: {{ $sucursales->firstWhere('id', $sucursalId)->nombre ?? 'Seleccionada' }}
                                @else
                                    Todas las sucursales
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.inventario.reportegeneral') }}@if($sucursalId > 0)?sucursal={{ $sucursalId }}@endif" 
                        class="btn btn-sm btn-outline-primary"
                        @if($sucursalId > 0) title="Ver reportes de {{ $sucursales->firstWhere('id', $sucursalId)->nombre ?? 'esta sucursal' }}"
                        @else title="Ver reportes globales" @endif>
                            <i class="fas fa-chart-bar mr-1"></i> Ver Reportes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
            </div>
        </div>
    </div>




@endsection

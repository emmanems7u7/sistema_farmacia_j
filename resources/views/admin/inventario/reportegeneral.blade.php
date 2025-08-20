@extends('layouts.app', ['title' => 'Gestión de Reportes'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Reportes'])

<div class="container-fluid">
    <!-- Filtro de Sucursal -->
    <div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <!-- Título con icono -->
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        
                        <div>
                            <h5 class="mb-0 text-dark fw-bold">Gestión de Reportes</h5>
                            <p class="mb-0 text-muted small">Farmacia: {{ $sucursalNombre }}</p>
                        </div>
                    </div>
                    
                    <!-- Botón de volver -->
                    <div>
                        <a href="{{ route('admin.inventario.index') }}?sucursal={{ $sucursalId }}" 
                           class="btn btn-sm btn-outline-primary d-flex align-items-center">
                            <i class="fas fa-arrow-left me-2"></i>
                            <span>Volver al Inventario</span>
                        </a>
                    </div>
                </div>
                
                
            </div>
        </div>
    </div>
</div>

<style>
    .icon-shape {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .bg-primary-soft {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }
    .border-top {
        border-top: 1px solid rgba(0, 0, 0, 0.05) !important;
    }
</style>

    <!-- Cards de Reportes -->
    <div class="row">
        <!-- Reporte de Usuarios -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Reporte de Usuarios
                            </div>
                            <div class="h6 mb-0 text-gray-800">
                                @if($sucursalId > 0)
                                    Usuarios en la farmacia {{ $sucursalNombre }}
                                @else
                                    Todos los usuarios
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.usuarios.reporte', ['tipo' => 'pdf']) }}?sucursal={{ $sucursalId }}" 
                           class="btn btn-sm btn-outline-danger"
                           title="Generar reporte en PDF"
                           target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Generar PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reporte de Categorías -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Reporte de Categorias
                            </div>
                            <div class="h6 mb-0 text-gray-800">
                                @if($sucursalId > 0)
                                    Categorias en la farmacia {{ $sucursalNombre }}
                                @else
                                    Todas las categorias
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.categorias.reporte') }}?tipo=pdf&sucursal={{ $sucursalId }}" 
                           class="btn btn-sm btn-outline-danger"
                           title="Generar reporte en PDF"
                           target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Generar PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reporte de Proveedores -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Reporte de Proveedores
                            </div>
                            <div class="h6 mb-0 text-gray-800">
                                @if($sucursalId > 0)
                                    Proveedores en la farmacia {{ $sucursalNombre }}
                                @else
                                    Todos los proveedores
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.proveedores.reporte') }}?tipo=pdf&sucursal={{ $sucursalId }}" 
                          class="btn btn-sm btn-outline-danger"
                           title="Generar reporte en PDF"
                           target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Generar PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reporte de Laboratorios -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Reporte de Laboratorios
                            </div>
                            <div class="h6 mb-0 text-gray-800">
                                Listado completo de laboratorios
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-microscope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.laboratorios.reporte') }}?tipo=pdf&sucursal={{ $sucursalId }}" 
                           class="btn btn-sm btn-outline-danger"
                           title="Generar reporte en PDF"
                           target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Generar PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reporte de Productos -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Reporte de Productos
                            </div>
                            <div class="h6 mb-0 text-gray-800">
                                @if($sucursalId > 0)
                                    Productos en la farmacia {{ $sucursalNombre }}
                                @else
                                    Todos los productos
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pills fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.productos.reporte', ['tipo' => 'pdf']) }}?sucursal={{ $sucursalId }}" 
                           class="btn btn-sm btn-outline-danger"
                           title="Generar reporte en PDF"
                           target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Generar PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reporte de Lotes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Reporte de Lote
                            </div>
                            <div class="h6 mb-0 text-gray-800">
                                @if($sucursalId > 0)
                                    Lote en la farmacia {{ $sucursalNombre }}
                                @else
                                    Todos los lotes
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.lotes.reporte') }}?tipo=pdf&sucursal={{ $sucursalId }}" 
                          class="btn btn-sm btn-outline-danger"
                           title="Generar reporte en PDF"
                           target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Generar PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reporte de Clientes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Reporte de Clientes
                            </div>
                            <div class="h6 mb-0 text-gray-800">
                                @if($sucursalId > 0)
                                    Clientes en la farmacia {{ $sucursalNombre }}
                                @else
                                    Todos los clientes
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.clientes.reporte') }}?tipo=pdf&sucursal={{ $sucursalId }}" 
                           class="btn btn-sm btn-outline-danger"
                           title="Generar reporte en PDF"
                           target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Generar PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reporte de Caja -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Reporte de Caja
                            </div>
                            <div class="h6 mb-0 text-gray-800">
                                @if($sucursalId > 0)
                                    Caja en la farmacia {{ $sucursalNombre }}
                                @else
                                    Todas las cajas
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cash-register fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.cajas.reporte', ['tipo' => 'pdf']) }}?sucursal={{ $sucursalId }}" 
                           class="btn btn-sm btn-outline-danger"
                           title="Generar reporte en PDF"
                           target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Generar PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reporte de Ventas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Reporte de Ventas
                            </div>
                            <div class="h6 mb-0 text-gray-800">
                                Historial completo de ventas
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.ventas.reporte', ['tipo' => 'pdf']) }}?sucursal={{ $sucursalId }}" 
                           class="btn btn-sm btn-outline-danger"
                           title="Generar reporte en PDF"
                           target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Generar PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reporte de Compras -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            
                                Reporte de Compras
                            </div>
                            <div class="h6 mb-0 text-gray-800">
                                Registro de compras 
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-basket fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.compras.reporte', ['tipo' => 'pdf']) }}?sucursal={{ $sucursalId }}" 
                           class="btn btn-sm btn-outline-danger"
                           title="Generar reporte en PDF"
                           target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Generar PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
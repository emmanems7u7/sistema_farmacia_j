@extends('layouts.argon')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header Principal -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center bg-white">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-boxes fa-2x me-2 text-primary"></i>
                            <strong>GESTION DE LOTES</strong>
                        </h5>
                    </div>

                    <div class="d-flex align-items-center">
                        <span class="badge bg-gradient-info me-3">
                            <i class="fas fa-database me-1"></i> {{ $lotes->count() }} Lotes
                        </span>

                        <div class="dropdown me-2">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="exportDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false"
                                title="Exportar reporte en diferentes formatos">
                                <i class="fas fa-download me-1"></i> Exportar
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.lotes.reporte') }}?tipo=pdf"
                                        title="Exportar a PDF" target="_blank">
                                        <i class="fas fa-file-pdf text-danger me-2"></i> PDF
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.lotes.reporte') }}?tipo=excel"
                                        title="Exportar a Excel">
                                        <i class="fas fa-file-excel text-success me-2"></i> Excel
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.lotes.index') }}" class="row">
                            @csrf

                            <!-- Filtro por Producto -->
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Producto</label>
                                <select name="producto_id" class="form-select">
                                    <option value="">Todos los productos</option>
                                    @foreach($productos as $producto)
                                        <option value="{{ $producto->id }}" {{ request('producto_id') == $producto->id ? 'selected' : '' }}>
                                            {{ $producto->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtro por Estado -->
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Estado</label>
                                <select name="estado" class="form-select">
                                    <option value="">Todos los estados</option>
                                    <option value="activos" {{ request('estado') == 'activos' ? 'selected' : '' }}>
                                        Activos (con stock)
                                    </option>
                                    <option value="inactivos" {{ request('estado') == 'inactivos' ? 'selected' : '' }}>
                                        Inactivos
                                    </option>
                                    <option value="vencidos" {{ request('estado') == 'vencidos' ? 'selected' : '' }}>
                                        Vencidos o agotados
                                    </option>
                                    <option value="agotados" {{ request('estado') == 'agotados' ? 'selected' : '' }}>
                                        Solo agotados
                                    </option>
                                    <option value="multiples" {{ request('estado') == 'multiples' ? 'selected' : '' }}>
                                        Productos con múltiples lotes
                                    </option>
                                </select>
                            </div>

                            <!-- Botones -->
                            <div class="col-md-4 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-1"></i> Filtrar
                                </button>
                                <a href="{{ route('admin.lotes.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-sync-alt"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listado de Lotes en Cards -->
        <div class="row">
            @forelse($lotes as $lote)
                @php
                    $fechaIngreso = $lote->fecha_ingreso ? \Carbon\Carbon::parse($lote->fecha_ingreso) : null;
                    $fechaVencimiento = $lote->fecha_vencimiento ? \Carbon\Carbon::parse($lote->fecha_vencimiento) : null;
                    $totalLotesProducto = $lote->producto ? $lote->producto->lotes->count() : 0;
                @endphp

                <div class="col-xl-4 col-lg-4 col-md-6 mb-4">
                    <div class="card border-radius-lg shadow-sm h-100">
                        <!-- Cabecera con indicador de múltiples lotes -->
                        <div class="card-header"
                            style="border-left: 4px solid {{ $fechaVencimiento && $fechaVencimiento->isPast() ? '#f5365c' : '#2dce89' }};">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><strong>Lote #{{ $lote->numero_lote }}</strong></h6>
                                @if($totalLotesProducto > 1)
                                    <span class="badge bg-info" title="Este producto tiene {{ $totalLotesProducto }} lotes">
                                        <i class="fas fa-layer-group me-1"></i> {{ $totalLotesProducto }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Cuerpo de la tarjeta -->
                        <div class="card-body p-2">
                            <div class="row g-2">
                                <!-- Columna de imagen -->
                                <div class="col-4">
                                    @if($lote->producto && $lote->producto->imagen)
                                        <img src="{{ asset('storage/' . $lote->producto->imagen) }}"
                                            alt="{{ $lote->producto->nombre ?? 'Producto' }}" class="img-fluid rounded"
                                            style="height: 80px; width: 100%; object-fit: contain;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                            style="height: 80px;">
                                            <i class="fas fa-box-open text-muted"></i>
                                        </div>
                                    @endif
                                </div>

                                <!-- Columna de información -->
                                <div class="col-8">
                                    <div style="font-size: 0.8rem;">
                                        <!-- Nombre del producto -->
                                        <p class="mb-1 fw-bold text-truncate small">
                                            {{ $lote->producto->nombre ?? 'N/A' }}
                                        </p>

                                        <!-- Datos compactos -->
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted">Cant:</span>
                                            <span>{{ $lote->cantidad }}u</span>
                                        </div>

                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted">Ingreso:</span>
                                            <span>{{ $fechaIngreso ? $fechaIngreso->format('d/m/y') : 'Sin Fecha' }}</span>
                                        </div>

                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted">Vence:</span>
                                            <span
                                                class="{{ $fechaVencimiento && $fechaVencimiento->isPast() ? 'text-danger' : '' }}">
                                                {{ $fechaVencimiento ? $fechaVencimiento->format('d/m/y') : 'Sin Fecha' }}
                                            </span>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Estado:</span>
                                            <span>
                                                @if($lote->activo && $lote->cantidad > 0)
                                                    <span class="badge bg-success py-1" style="font-size: 0.7rem;">
                                                        <i class="fas fa-check-circle me-1"></i> Activo
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary py-1" style="font-size: 0.7rem;">
                                                        <i class="fas fa-times-circle me-1"></i> Inactivo
                                                    </span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No se encontraron lotes registrados</h5>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>


    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @push('css')
        <style>
            .card {
                transition: all 0.3s ease;
                border: 1px solid #eee;
            }

            .card:hover {
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }

            .badge {
                font-size: 0.7rem;
                padding: 0.35em 0.65em;
            }

            .text-muted {
                font-size: 0.8rem;
            }

            .badge.bg-success {
                background-color: #2dce89 !important;
            }

            .badge.bg-secondary {
                background-color: #6c757d !important;
            }

            .badge i {
                font-size: 0.6em;
            }
        </style>
    @endpush

@endsection
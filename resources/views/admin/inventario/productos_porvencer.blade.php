@extends('layouts.argon')

@section('content')
    <div class="container-fluid">

        <!-- Card Header -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <!-- Card Header -->
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div>
                            <h5 class="mb-0 fw-semibold">Control de Vencimientos</h5>
                            <small class="text-muted">Productos vencidos y por vencer</small>
                        </div>
                    </div>
                    <div class="mt-2 mt-md-0">
                        <a href="{{ route('admin.inventario.index') }}?sucursal={{ $sucursalId }}"
                            class="btn btn-sm btn-outline-primary d-flex align-items-center">
                            <i class="fas fa-arrow-left me-1"></i> Volver al Inventario
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card  -->
            <div class="card-body p-4">
                <div class="row g-3 align-items-center">
                    <div class="col-md-12">
                        <label class="form-label small text-muted mb-2">Filtrar por:</label>
                        <div class="d-flex flex-wrap gap-2">
                           
                            <a href="{{ route('admin.inventario.productos_porvencer', ['vencidos' => 1, 'sucursal' => $sucursalId]) }}" 
                               class="btn {{ $mostrarVencidos ? 'btn-danger' : 'btn-outline-danger' }}">
                                <i class="fas fa-exclamation-triangle me-1"></i> Productos Vencidos
                            </a>

                            <!-- Botón 15 días -->
                            <a href="{{ route('admin.inventario.productos_porvencer', ['dias' => 15, 'sucursal' => $sucursalId]) }}" 
                               class="btn {{ !$mostrarVencidos && $diasSeleccionados == 15 ? 'btn-warning' : 'btn-outline-warning' }}">
                                <i class="fas fa-clock me-1"></i> Próximos 15 Días
                            </a>

                            <!-- Botón 30 días -->
                            <a href="{{ route('admin.inventario.productos_porvencer', ['dias' => 30, 'sucursal' => $sucursalId]) }}" 
                               class="btn {{ !$mostrarVencidos && $diasSeleccionados == 30 ? 'btn-info' : 'btn-outline-info' }}">
                                <i class="fas fa-calendar me-1"></i> Próximos 30 Días
                            </a>

                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card de resultados -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="fas {{ $mostrarVencidos ? 'fa-exclamation-triangle text-danger' : 'fa-clock' }} me-2"></i>
                        @if($mostrarVencidos)
                            Productos Vencidos
                        @elseif($diasSeleccionados == 15)
                            Productos que Vencen en 15 Días
                        @elseif($diasSeleccionados == 30)
                            Productos que Vencen en 30 Días
                        @else
                            Todos los Productos por Vencer
                        @endif
                    </h5>
                    <div>
                        <span class="badge bg-light-primary text-primary me-2">
                            {{ $productos->total() }} Registros
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tabla de productos -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="thead-light">
                        <tr>
                            <th width="100" class="ps-4" style="font-size: 0.8rem;">Código</th>
                            <th style="font-size: 0.8rem;">Producto</th>
                            <th width="120" style="font-size: 0.8rem;">Lote</th>
                            <th width="100" class="text-center" style="font-size: 0.8rem;">Stock</th>
                            <th width="150" class="text-center" style="font-size: 0.8rem;">Vencimiento</th>
                            <th width="150" class="text-center" style="font-size: 0.8rem;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productos as $item)
    @php
        $hoy = \Carbon\Carbon::now();
        $fechaVencimiento = \Carbon\Carbon::parse($item->fecha_vencimiento);
        $dias = $fechaVencimiento->diffInDays($hoy, false); // false para incluir negativos
        $dias = round($dias); // REDONDEAR A ENTERO
        $vencido = $fechaVencimiento->lt($hoy);
    @endphp
    <tr class="{{ $vencido ? 'bg-light-danger' : ($dias <= 5 ? 'bg-light-warning' : '') }}">
        <td class="ps-4 fw-bold text-muted" style="font-size: 0.82rem;">{{ $item->codigo }}</td>
        <td>
            <div class="d-flex align-items-center">
                <div class="symbol symbol-40px me-3">
                    <span class="symbol-label {{ $vencido ? 'bg-light-danger' : 'bg-light-primary' }}">
                        <i class="fas fa-box {{ $vencido ? 'text-danger' : 'text-primary' }}"
                            style="font-size: 0.9rem;"></i>
                    </span>
                </div>
                <div>
                    <div class="fw-bold" style="font-size: 0.85rem;">{{ $item->nombre }}</div>
                </div>
            </div>
        </td>
        <td>
            <span class="badge bg-light-secondary text-dark"
                style="font-size: 0.8rem;">{{ $item->numero_lote }}</span>
        </td>
        <td class="text-center fw-bold" style="font-size: 0.85rem;">{{ $item->cantidad_lote }}</td>
        <td class="text-center" style="font-size: 0.85rem;">
            <div class="d-flex flex-column">
                <span class="fw-bold {{ $vencido ? 'text-danger' : '' }}">{{ $fechaVencimiento->format('d/m/Y') }}</span>
                @if($vencido)
                    <small class="text-danger">Vencido hace {{ abs($dias) }} días</small>
                @endif
            </div>
        </td>
        <td class="text-center align-middle">
            @if($vencido)
                <span class="badge bg-danger text-white rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                    <i class="fas fa-exclamation-triangle me-1"></i> Vencido
                </span>
            @else
                @php
                    $claseColor = 'bg-success';
                    $icono = 'fa-check-circle';

                    if ($dias <= 5) {
                        $claseColor = 'bg-danger';
                        $icono = 'fa-exclamation-circle';
                    } elseif ($dias <= 15) {
                        $claseColor = 'bg-warning';
                        $icono = 'fa-clock';
                    }
                @endphp

                <div class="d-flex flex-column align-items-center">
                    <span class="badge {{ $claseColor }} text-white rounded-pill px-2 py-1 mb-1"
                        style="font-size: 0.75rem;">
                        <i class="fas {{ $icono }} me-1"></i> {{ $dias }} días
                    </span>

                    <div class="progress" style="height: 5px; width: 70px;">
                        @php
                            $porcentaje = min(100, max(0, ($dias / 30) * 100));
                        @endphp
                        <div class="progress-bar {{ $claseColor }}" role="progressbar"
                            style="width: {{ $porcentaje }}%" aria-valuenow="{{ $porcentaje }}"
                            aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>

                    <small class="text-muted mt-1" style="font-size: 0.7rem;">
                        @if($dias <= 5)
                            ¡Próximo a vencer!
                        @elseif($dias <= 15)
                            Atención requerida
                        @else
                            En buen estado
                        @endif
                    </small>
                </div>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-5">
            <div class="d-flex flex-column align-items-center">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <h5 class="text-success" style="font-size: 0.9rem;">
                    @if($mostrarVencidos)
                        ¡Excelente! No hay productos vencidos
                    @else
                        No hay productos que venzan en {{ $diasSeleccionados }} días
                    @endif
                </h5>
            </div>
        </td>
    </tr>
@endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($productos->count() > 0)
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <div class="text-muted small">
                    Mostrando {{ $productos->firstItem() }} a {{ $productos->lastItem() }} de {{ $productos->total() }}
                    registros
                </div>
                <div>
                    {{ $productos->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
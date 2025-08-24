@extends('layouts.argon')

@section('content')
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            <div class="card" style="height: auto;">
            <div class="card-header border-0 p-2"> <!-- p-2 es un padding más pequeño -->
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><b>Listado de cajas</b></h3>
                    <div class="card-header bg-transparent border-0">
                    @if ($cajaAbierto)                 
                    @else 
                    <a href="{{ url('/admin/cajas/create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo caja
                    </a>

                    
                    @endif


                    <!-- Barra de acciones -->
                <div class="d-flex gap-2 align-items-center">
                        <button class="btn btn-sm btn-outline-secondary" id="refreshTable">
                            <i class="fas fa-sync-alt me-1"></i> Actualizar
                        </button>
                        <div class="position-relative"> <!-- Contenedor relativo importante -->
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" 
                                        id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-download me-1"></i> Exportar
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg" 
                                    style="z-index: 1060; position: absolute;"
                                    aria-labelledby="exportDropdown">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" 
                                        href="{{ route('admin.cajas.reporte', ['tipo' => 'pdf']) }}?fecha_inicio={{ request('fecha_inicio') }}&fecha_fin={{ request('fecha_fin') }}&cliente_id={{ request('cliente_id') }}"
                                        target="_blank">
                                            <i class="fas fa-file-pdf text-danger me-2"></i> PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" 
                                        href="{{ route('admin.cajas.reporte', ['tipo' => 'excel']) }}?fecha_inicio={{ request('fecha_inicio') }}&fecha_fin={{ request('fecha_fin') }}&cliente_id={{ request('cliente_id') }}">
                                            <i class="fas fa-file-excel text-success me-2"></i> Excel
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" 
                                        href="{{ route('admin.cajas.reporte', ['tipo' => 'csv']) }}?fecha_inicio={{ request('fecha_inicio') }}&fecha_fin={{ request('fecha_fin') }}&cliente_id={{ request('cliente_id') }}">
                                            <i class="fas fa-file-csv text-info me-2"></i> CSV
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                </div>
                
            </div>
             </div>
            
        </div>
    </div>

   

    <!-- Card de tabla -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0">
                        <i class="ni ni-bullet-list-67 me-2 text-primary"></i>Detalle de cajas
                    </h5>
                </div>
                <!-- Tabla de Cajas -->
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table id="mitabla" class="table table-hover align-items-center mb-0 compact">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder text-center">#</th>
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder">Apertura</th>
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder text-end">Inicial</th>
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder">Cierre</th>
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder text-end">Final</th>
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder">Movimientos</th>
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cajas as $caja)
                        <tr>
                            <td class="text-xs text-center">{{ $loop->iteration }}</td>
                            <td class="text-xs">
                                <div class="d-flex flex-column">
                                    <span class="font-weight-bold">{{ date('d/m/Y', strtotime($caja->fecha_apertura)) }}</span>
                                    <small class="text-muted">{{ date('H:i', strtotime($caja->fecha_apertura)) }}</small>
                                </div>
                            </td>
                            <td class="text-xs text-end font-weight-bold text-primary">Bs {{ number_format($caja->monto_inicial, 2) }}</td>
                            <td class="text-xs">
                                @if($caja->fecha_cierre)
                                <div class="d-flex flex-column">
                                    <span class="font-weight-bold">{{ date('d/m/Y', strtotime($caja->fecha_cierre)) }}</span>
                                    <small class="text-muted">{{ date('H:i', strtotime($caja->fecha_cierre)) }}</small>
                                </div>
                                @else
                                <span class="badge bg-warning text-dark">Abierta</span>
                                @endif
                            </td>
                            <td class="text-xs text-end font-weight-bold @if($caja->monto_final) text-success @else text-muted @endif">
                                @if($caja->monto_final) Bs {{ number_format($caja->monto_final, 2) }} @else -- @endif
                            </td>
                            <td class="text-xs">
                                <div class="d-flex justify-content-around">
                                    <div class="text-center">
                                        <span class="badge bg-success text-white">Ingresos</span>
                                        <div class="font-weight-bold">Bs {{ number_format($caja->total_ingresos, 2) }}</div>
                                    </div>
                                    <div class="text-center">
                                        <span class="badge bg-danger text-white">Egresos</span>
                                        <div class="font-weight-bold">Bs {{ number_format($caja->total_egresos, 2) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
    <div class="d-flex justify-content-center gap-1">
        <!-- Botón ingresos/egresos -->
        @if(!$caja->fecha_cierre)
        <button type="button" class="btn btn-action btn-warning"
            data-bs-toggle="modal" data-bs-target="#ingresoEgresoModal{{$caja->id}}"
            style="width: 30px; height: 30px; min-width: 30px; padding: 0;"
            data-bs-tooltip="tooltip" title="Registrar Movimientos">
            <i class="fas fa-exchange-alt"></i>
        </button>
        @endif
        <!-- Botón cerrar caja -->
        @if(!$caja->fecha_cierre)
        <button type="button" class="btn btn-action btn-secondary"
            data-bs-toggle="modal" data-bs-target="#cerrarModal{{$caja->id}}"
             style="width: 30px; height: 30px; min-width: 30px; padding: 0;"
            data-bs-tooltip="tooltip" title="Cerrar Caja">
            <i class="fas fa-lock"></i>
        </button>
        @endif
        
        <!-- Botón ver -->
        <button type="button" 
        
         class="btn btn-sm bg-gradient-info text-white mx-1 d-flex align-items-center justify-content-center"
            data-bs-toggle="modal" data-bs-target="#verModal{{$caja->id}}"
             style="width: 30px; height: 30px; min-width: 30px; padding: 0;"
            data-bs-tooltip="tooltip" title="Ver Detalles">
            <i class="fas fa-eye"></i>
        </button>

        
        
        <!-- Botón editar 
        <button type="button" class="btn btn-action btn-primary"
            data-bs-toggle="modal" data-bs-target="#editarModal{{$caja->id}}"
            data-bs-tooltip="tooltip" title="Editar Caja">
            <i class="fas fa-pencil-alt"></i>
        </button>-->
        
        <!-- Botón eliminar -->
        <button type="button" class="btn btn-action btn-danger"
            onclick="confirmDelete({{$caja->id}})"
             style="width: 30px; height: 30px; min-width: 30px; padding: 0;"
            data-bs-tooltip="tooltip" title="Eliminar Caja">
            <i class="fas fa-trash-alt"></i>
        </button>
        <form id="deleteForm{{$caja->id}}" action="{{url('/admin/cajas/'.$caja->id)}}" method="POST" class="d-none">
            @csrf @method('DELETE')
        </form>
    </div>
</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
            </div>
        </div>
    </div>
</div>

<!-- Modales -->
@foreach($cajas as $caja)
<!-- Modal Ingreso/Egreso -->
<div class="modal fade" id="ingresoEgresoModal{{$caja->id}}" tabindex="-1" aria-labelledby="ingresoEgresoModalLabel{{$caja->id}}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="ingresoEgresoModalLabel{{$caja->id}}">Registro de Ingresos/Egresos</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{url('/admin/cajas/create_ingresos_egresos')}}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{$caja->id}}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Fecha de apertura</label>
                        <input type="text" class="form-control" value="{{$caja->fecha_apertura}}" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select name="tipo" class="form-select" required>
                            <option value="INGRESO">INGRESO</option>
                            <option value="EGRESO">EGRESO</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="monto" class="form-label">Monto</label>
                        <input type="number" step="0.01" class="form-control" name="monto" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <input type="text" class="form-control" name="descripcion" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Cerrar Caja -->
<div class="modal fade" id="cerrarModal{{$caja->id}}" tabindex="-1" aria-labelledby="cerrarModalLabel{{$caja->id}}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="cerrarModalLabel{{$caja->id}}">Cierre de Caja</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{url('/admin/cajas/create_cierre')}}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{$caja->id}}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Fecha de apertura</label>
                        <input type="text" class="form-control" value="{{$caja->fecha_apertura}}" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Monto inicial</label>
                        <input type="text" class="form-control" value="{{$caja->monto_inicial}}" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_cierre" class="form-label">Fecha de cierre</label>
                        <input type="datetime-local" 
                        class="form-control" 
                        name="fecha_cierre" required>
                    </div>

                    


                    <div class="mb-3">
                        <label for="monto_final" class="form-label">Monto final</label>
                        <input type="number" step="0.01" class="form-control" name="monto_final" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Cerrar Caja</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver -->
<!-- Modal Ver -->

<div class="modal fade" id="verModal{{$caja->id}}" tabindex="-1" aria-labelledby="verModalLabel{{$caja->id}}" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <!-- Encabezado mejorado -->
            <div class="modal-header bg-gradient-primary text-white">
                <div class="d-flex align-items-center">
                    <i class="ni ni-money-coins fs-3 me-2"></i>
                    <h5 class="modal-title mb-0" id="verModalLabel{{$caja->id}}">Reporte de Caja #{{$caja->id}}</h5>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-{{$caja->fecha_cierre ? 'success' : 'warning'}} me-2 fs-6">
                        {{$caja->fecha_cierre ? 'CERRADA' : 'ABIERTA'}}
                    </span>
                    <a href="{{ url('/admin/cajas/pdf/' . $caja->id) }}" target="_blank" class="btn btn-sm btn-danger shadow-sm">
                        <i class="ni ni-single-copy-04 me-1"></i> Exportar PDF
                    </a>
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Panel izquierdo - Datos de Caja -->
                    <div class="col-lg-4 border-end">
                        <div class="p-3">
                            <div class="card border-0 shadow-none">
                                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-primary">
                                        <i class="ni ni-collection me-1"></i> Información de Caja
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="timeline timeline-one-side">
                                        <!-- Item de línea de tiempo para apertura -->
                                        <div class="timeline-block mb-3">
                                            <span class="timeline-step bg-primary">
                                                <i class="ni ni-check-bold text-white"></i>
                                            </span>
                                            <div class="timeline-content">
                                                <h6 class="text-sm text-muted mb-0">Apertura</h6>
                                                <p class="text-sm fw-bold mb-0">{{$caja->fecha_apertura->format('d/m/Y H:i')}}</p>
                                                <p class="text-sm mt-1 mb-0">
                                                    <span class="badge bg-primary bg-opacity-10 text-white">Bs / {{number_format($caja->monto_inicial, 2)}}</span>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <!-- Item de línea de tiempo para cierre (si existe) -->
                                        @if($caja->fecha_cierre)
                                        <div class="timeline-block mb-3">
                                            <span class="timeline-step bg-success">
                                                <i class="ni ni-lock-circle-open text-white"></i>
                                            </span>
                                            <div class="timeline-content">
                                                <h6 class="text-sm text-muted mb-0">Cierre</h6>
                                                <p class="text-sm fw-bold mb-0">{{$caja->fecha_cierre->format('d/m/Y H:i')}}</p>
                                                <p class="text-sm mt-1 mb-0">
                                                    <span class="badge bg-success bg-opacity-10 text-white">Bs / {{number_format($caja->monto_final, 2)}}</span>
                                                </p>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        <!-- Resumen rápido -->
                                        <div class="timeline-block">
                                            <span class="timeline-step bg-info">
                                                <i class="ni ni-chart-bar-32 text-white"></i>
                                            </span>
                                            <div class="timeline-content">
                                                <h6 class="text-sm text-muted mb-0">Balance</h6>
                                                <p class="text-sm fw-bold mb-1">
                                                    @php
                                                        $totalIngresos = $caja->movimientos->where('tipo', 'INGRESO')->sum('monto');
                                                        $totalEgresos = $caja->movimientos->where('tipo', 'EGRESO')->sum('monto');
                                                        $saldoFinal = $caja->monto_inicial + $totalIngresos - $totalEgresos;
                                                    @endphp
                                                    <span class="badge bg-success bg-opacity-10 text-white me-1">
                                                        +Bs/ {{number_format($totalIngresos, 2)}}
                                                    </span>
                                                    <span class="badge bg-danger bg-opacity-10 text-white me-1">
                                                        -Bs/ {{number_format($totalEgresos, 2)}}
                                                    </span>
                                                </p>
                                                <p class="text-sm fw-bold mb-0">
                                                    <span class="badge bg-dark bg-opacity-10 text-white">
                                                        Total: Bs/ {{number_format($saldoFinal, 2)}}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Descripción -->
                                    <div class="mt-4">
                                        <label class="form-label fw-bold">Notas</label>
                                        <div class="border rounded p-3 bg-light">
                                            {{$caja->descripcion ?: 'Sin observaciones'}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Panel derecho - Detalles de movimientos -->
                    <div class="col-lg-8">
                        <div class="p-3">
                            <!-- Pestañas mejoradas -->
                            <ul class="nav nav-pills nav-fill mb-4" id="cajaTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active d-flex align-items-center justify-content-center"
            id="ingresos-tab" data-bs-toggle="pill" data-bs-target="#ingresos{{$caja->id}}"
            type="button" role="tab">
            <i class="ni ni-bold-up me-2"></i> Ingresos
            <span class="badge bg-success ms-2">{{$caja->movimientos->where('tipo', 'INGRESO')->count()}}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link d-flex align-items-center justify-content-center"
            id="egresos-tab" data-bs-toggle="pill" data-bs-target="#egresos{{$caja->id}}"
            type="button" role="tab">
            <i class="ni ni-bold-down me-2"></i> Egresos
            <span class="badge bg-danger ms-2">{{$caja->movimientos->where('tipo', 'EGRESO')->count()}}</span>
        </button>
    </li>
    
        </ul>
                            
                            <!-- Contenido de pestañas -->
                            <div class="tab-content" id="cajaTabContent">
                                <!-- Pestaña de Ingresos -->
                                <div class="tab-pane fade show active" id="ingresos{{$caja->id}}" role="tabpanel">
                                    @if($caja->movimientos->where('tipo', 'INGRESO')->count() > 0)
                                    <div class="accordion" id="ingresosAccordion{{$caja->id}}">
                                        @foreach($caja->movimientos->where('tipo', 'INGRESO')->groupBy('descripcion') as $descripcion => $ingresos)
                                        <div class="accordion-item border-0 shadow-sm mb-3">
                                            <h2 class="accordion-header" id="headingIngreso{{$loop->index}}{{$caja->id}}">
                                                <button class="accordion-button collapsed shadow-none bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseIngreso{{$loop->index}}{{$caja->id}}">
                                                    <div class="d-flex justify-content-between w-100 align-items-center">
                                                        <div>
                                                            <span class="fw-bold">{{$descripcion}}</span>
                                                            <span class="badge bg-primary bg-opacity-10 text-white ms-2">{{count($ingresos)}}</span>
                                                        </div>
                                                        <span class="badge bg-success">Bs/ {{number_format($ingresos->sum('monto'), 2)}}</span>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapseIngreso{{$loop->index}}{{$caja->id}}" class="accordion-collapse collapse" data-bs-parent="#ingresosAccordion{{$caja->id}}">
                                                <div class="accordion-body p-0">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover align-items-center mb-0">
                                                            <thead class="bg-light">
                                                                <tr>
                                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Hora</th>
                                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Detalle</th>
                                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Monto</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($ingresos as $ingreso)
                                                                <tr>
                                                                    <td class="ps-4">
                                                                        <p class="text-xs font-weight-bold mb-0">{{$ingreso->created_at->format('H:i')}}</p>
                                                                    </td>
                                                                    <td>
                                                                        @if($ingreso->venta_id)
                                                                            <a href="{{route('admin.ventas.show', $ingreso->venta_id)}}" class="text-primary text-sm font-weight-bold">
                                                                                <i class="ni ni-cart me-1"></i> Venta #{{$ingreso->venta_id}}
                                                                            </a>
                                                                        @else
                                                                            <p class="text-sm font-weight-bold mb-0">{{$ingreso->descripcion}}</p>
                                                                        @endif
                                                                    </td>
                                                                    <td class="pe-4 text-end">
                                                                        <span class="text-success text-sm font-weight-bold">Bs/ {{number_format($ingreso->monto, 2)}}</span>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @else
                                    <div class="text-center py-5">
                                        <i class="ni ni-money-coins fs-1 text-muted opacity-5"></i>
                                        <h6 class="mt-3 text-muted">No hay ingresos registrados</h6>
                                    </div>
                                    @endif
                                </div>
                                
                                <!-- Pestaña de Egresos -->
                                <div class="tab-pane fade" id="egresos{{$caja->id}}" role="tabpanel">
                                    @if($caja->movimientos->where('tipo', 'EGRESO')->count() > 0)
                                    <div class="accordion" id="egresosAccordion{{$caja->id}}">
                                        @foreach($caja->movimientos->where('tipo', 'EGRESO')->groupBy('descripcion') as $descripcion => $egresos)
                                        <div class="accordion-item border-0 shadow-sm mb-3">
                                            <h2 class="accordion-header" id="headingEgreso{{$loop->index}}{{$caja->id}}">
                                                <button class="accordion-button collapsed shadow-none bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEgreso{{$loop->index}}{{$caja->id}}">
                                                    <div class="d-flex justify-content-between w-100 align-items-center">
                                                        <div>
                                                            <span class="fw-bold">{{$descripcion}}</span>
                                                            <span class="badge bg-primary bg-opacity-10 text-white ms-2">{{count($egresos)}}</span>
                                                        </div>
                                                        <span class="badge bg-danger">Bs/ {{number_format($egresos->sum('monto'), 2)}}</span>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapseEgreso{{$loop->index}}{{$caja->id}}" class="accordion-collapse collapse" data-bs-parent="#egresosAccordion{{$caja->id}}">
                                                <div class="accordion-body p-0">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover align-items-center mb-0">
                                                            <thead class="bg-light">
                                                                <tr>
                                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Hora</th>
                                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Detalle</th>
                                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Monto</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($egresos as $egreso)
                                                                <tr>
                                                                    <td class="ps-4">
                                                                        <p class="text-xs font-weight-bold mb-0">{{$egreso->created_at->format('H:i')}}</p>
                                                                    </td>
                                                                    <td>
                                                                        @if($egreso->compra_id)
                                                                            <a href="{{route('admin.compras.show', $egreso->compra_id)}}" class="text-primary text-sm font-weight-bold">
                                                                                <i class="ni ni-box-2 me-1"></i> Compra #{{$egreso->compra_id}}
                                                                            </a>
                                                                        @else
                                                                            <p class="text-sm font-weight-bold mb-0">{{$egreso->descripcion}}</p>
                                                                        @endif
                                                                    </td>
                                                                    <td class="pe-4 text-end">
                                                                        <span class="text-danger text-sm font-weight-bold">Bs/ {{number_format($egreso->monto, 2)}}</span>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @else
                                    <div class="text-center py-5">
                                        <i class="ni ni-money-coins fs-1 text-muted opacity-5"></i>
                                        <h6 class="mt-3 text-muted">No hay egresos registrados</h6>
                                    </div>
                                    @endif
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            
        </div>
    </div>
</div>



@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verifica que los elementos canvas existan
    const ingresosCanvas = document.getElementById('ingresosChart{{$caja->id}}');
    const egresosCanvas = document.getElementById('egresosChart{{$caja->id}}');
    
    if (!ingresosCanvas || !egresosCanvas) {
        console.error('No se encontraron los elementos canvas');
        return;
    }

    // Datos para debugging
    console.log('Datos ingresos:', {!! json_encode($caja->movimientos->where('tipo', 'INGRESO')->groupBy('descripcion')->keys()) !!});
    console.log('Valores ingresos:', {!! json_encode($caja->movimientos->where('tipo', 'INGRESO')->groupBy('descripcion')->map->sum('monto')->values()) !!});

    // Gráfico de Ingresos
    try {
        new Chart(ingresosCanvas, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($caja->movimientos->where('tipo', 'INGRESO')->groupBy('descripcion')->keys()) !!},
                datasets: [{
                    data: {!! json_encode($caja->movimientos->where('tipo', 'INGRESO')->groupBy('descripcion')->map->sum('monto')->values()) !!},
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error al crear gráfico de ingresos:', error);
    }

    // Gráfico de Egresos
    try {
        new Chart(egresosCanvas, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($caja->movimientos->where('tipo', 'EGRESO')->groupBy('descripcion')->keys()) !!},
                datasets: [{
                    data: {!! json_encode($caja->movimientos->where('tipo', 'EGRESO')->groupBy('descripcion')->map->sum('monto')->values()) !!},
                    backgroundColor: ['#e74a3b', '#f6c23e', '#858796', '#5a5c69']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error al crear gráfico de egresos:', error);
    }
});
</script>
@endpush
<!-- Modal Editar -->
<div class="modal fade" id="editarModal{{$caja->id}}" tabindex="-1" aria-labelledby="editarModalLabel{{$caja->id}}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="editarModalLabel{{$caja->id}}">Editar Caja</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('admin.cajas.update', $caja->id)}}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fecha_apertura" class="form-label">Fecha Apertura</label>
                        <input type="datetime-local" class="form-control" name="fecha_apertura" value="{{$caja->fecha_apertura}}" required>
                    </div>
                    <div class="mb-3">
                        <label for="monto_inicial" class="form-label">Monto Inicial</label>
                        <input type="number" step="0.01" class="form-control" name="monto_inicial" value="{{$caja->monto_inicial}}" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <input type="text" class="form-control" name="descripcion" value="{{$caja->descripcion}}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach



<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('#mitabla').DataTable({
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "No hay registros disponibles",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            }
        });
    });

    function confirmDelete(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm'+id).submit();
            }
        })
    }
</script>
@endsection









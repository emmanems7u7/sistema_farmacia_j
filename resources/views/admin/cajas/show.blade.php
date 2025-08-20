@extends('layouts.app', ['title' => 'Productos'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Productos'])

<div class="container-fluid">
    <!-- Encabezado -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalles de Caja #{{ $caja->id }}</h1>
        <div>
            <a href="{{ route('admin.cajas.pdf', $caja->id) }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="{{ route('admin.cajas.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="verModalLabel{{$caja->id}}">Detalles de Caja #{{$caja->id}}</h5>
                <div>
                    <a href="{{ url('/admin/cajas/pdf/' . $caja->id) }}" target="_blank" class="btn btn-sm btn-danger">
                        <i class="ni ni-single-copy-04 me-1"></i> Exportar PDF
                    </a>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Datos de Caja -->
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Datos de Caja</h6>
                                <span class="badge bg-{{$caja->fecha_cierre ? 'success' : 'warning'}}">
                                    {{$caja->fecha_cierre ? 'CERRADA' : 'ABIERTA'}}
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Fecha Apertura</label>
                                    <input type="text" class="form-control" value="{{$caja->fecha_apertura->format('d/m/Y H:i')}}" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Monto Inicial</label>
                                    <input type="text" class="form-control" value="S/ {{number_format($caja->monto_inicial, 2)}}" disabled>
                                </div>
                                @if($caja->fecha_cierre)
                                <div class="mb-3">
                                    <label class="form-label">Fecha Cierre</label>
                                    <input type="text" class="form-control" value="{{$caja->fecha_cierre->format('d/m/Y H:i')}}" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Monto Final</label>
                                    <input type="text" class="form-control" value="S/ {{number_format($caja->monto_final, 2)}}" disabled>
                                </div>
                                @endif
                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea class="form-control" disabled>{{$caja->descripcion}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Resumen Financiero -->
                    <div class="col-md-8">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <ul class="nav nav-tabs card-header-tabs" id="cajaTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="ingresos-tab" data-bs-toggle="tab" href="#ingresos{{$caja->id}}" role="tab">
                                            <i class="ni ni-money-coins me-1"></i> Ingresos ({{$caja->movimientos->where('tipo', 'INGRESO')->count()}})
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="egresos-tab" data-bs-toggle="tab" href="#egresos{{$caja->id}}" role="tab">
                                            <i class="ni ni-cart me-1"></i> Egresos ({{$caja->movimientos->where('tipo', 'EGRESO')->count()}})
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="resumen-tab" data-bs-toggle="tab" href="#resumen{{$caja->id}}" role="tab">
                                            <i class="ni ni-chart-bar-32 me-1"></i> Resumen
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="cajaTabContent">
                                    <!-- Pestaña de Ingresos -->
                                    <div class="tab-pane fade show active" id="ingresos{{$caja->id}}" role="tabpanel">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="5%">#</th>
                                                        <th width="15%">Hora</th>
                                                        <th width="25%">Tipo</th>
                                                        <th width="25%">Detalle</th>
                                                        <th width="15%" class="text-end">Monto</th>
                                                        <th width="15%" class="text-center">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $ingresos = $caja->movimientos->where('tipo', 'INGRESO')->sortBy('created_at');
                                                    @endphp
                                                    
                                                    @foreach($ingresos as $ingreso)
                                                    <tr>
                                                        <td>{{$loop->iteration}}</td>
                                                        <td>{{$ingreso->created_at->format('H:i')}}</td>
                                                        <td>
                                                            @if($ingreso->venta_id)
                                                                <span class="badge bg-success">Venta</span>
                                                            @else
                                                                <span class="badge bg-info">Otro Ingreso</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($ingreso->venta_id)
                                                                Venta #{{$ingreso->venta_id}} 
                                                                @if($ingreso->venta && $ingreso->venta->cliente)
                                                                    <br><small>Cliente: {{$ingreso->venta->cliente->nombre}}</small>
                                                                @endif
                                                            @else
                                                                {{$ingreso->descripcion}}
                                                            @endif
                                                        </td>
                                                        <td class="text-end">S/ {{number_format($ingreso->monto, 2)}}</td>
                                                        <td class="text-center">
                                                            @if($ingreso->venta_id)
                                                            <button class="btn btn-sm btn-primary px-2" type="button" data-bs-toggle="collapse" data-bs-target="#detalleVenta{{$ingreso->id}}" aria-expanded="false">
                                                                <span class="d-none d-sm-inline">Ver </span>{{$ingreso->venta ? $ingreso->venta->detalles->count() : 0}} <span class="d-none d-sm-inline">Prod.</span>
                                                            </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @if($ingreso->venta_id)
                                                    <tr class="collapse" id="detalleVenta{{$ingreso->id}}">
                                                        <td colspan="6" class="p-0">
                                                            <div class="p-3 bg-light">
                                                                <h6 class="mb-3">Detalle de Venta #{{$ingreso->venta_id}}</h6>
                                                                @if($ingreso->venta)
                                                                    <div class="table-responsive">
                                                                        <table class="table table-sm table-bordered">
                                                                            <thead class="table-light">
                                                                                <tr>
                                                                                    <th>Producto</th>
                                                                                    <th width="15%" class="text-center">Cantidad</th>
                                                                                    <th width="20%" class="text-end">Precio Unit.</th>
                                                                                    <th width="20%" class="text-end">Subtotal</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach($ingreso->venta->detalles as $detalle)
                                                                                <tr>
                                                                                    <td>
                                                                                        {{$detalle->producto->nombre}}
                                                                                        @if($detalle->producto->codigo)
                                                                                            <br><small class="text-muted">Código: {{$detalle->producto->codigo}}</small>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td class="text-center">{{$detalle->cantidad}}</td>
                                                                                    <td class="text-end">S/ {{number_format($detalle->precio, 2)}}</td>
                                                                                    <td class="text-end">S/ {{number_format($detalle->cantidad * $detalle->precio, 2)}}</td>
                                                                                </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                            <tfoot>
                                                                                <tr class="table-active">
                                                                                    <th colspan="3" class="text-end">Total</th>
                                                                                    <th class="text-end">S/ {{number_format($ingreso->venta->total, 2)}}</th>
                                                                                </tr>
                                                                            </tfoot>
                                                                        </table>
                                                                    </div>
                                                                @else
                                                                    <div class="alert alert-warning mb-0">No hay detalles disponibles para esta venta</div>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endif
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="table-active">
                                                        <th colspan="4" class="text-end">TOTAL INGRESOS</th>
                                                        <th class="text-end">S/ {{number_format($ingresos->sum('monto'), 2)}}</th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <!-- Pestaña de Egresos -->
                                    <div class="tab-pane fade" id="egresos{{$caja->id}}" role="tabpanel">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="5%">#</th>
                                                        <th width="15%">Hora</th>
                                                        <th width="25%">Tipo</th>
                                                        <th width="25%">Detalle</th>
                                                        <th width="15%" class="text-end">Monto</th>
                                                        <th width="15%" class="text-center">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $egresos = $caja->movimientos->where('tipo', 'EGRESO')->sortBy('created_at');
                                                    @endphp
                                                    
                                                    @foreach($egresos as $egreso)
                                                    <tr>
                                                        <td>{{$loop->iteration}}</td>
                                                        <td>{{$egreso->created_at->format('H:i')}}</td>
                                                        <td>
                                                            @if($egreso->compra_id)
                                                                <span class="badge bg-danger">Compra</span>
                                                            @else
                                                                <span class="badge bg-warning text-dark">Otro Egreso</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($egreso->compra_id)
                                                                Compra #{{$egreso->compra_id}} 
                                                                @if($egreso->compra && $egreso->compra->proveedor)
                                                                    <br><small>Proveedor: {{$egreso->compra->proveedor->nombre}}</small>
                                                                @endif
                                                            @else
                                                                {{$egreso->descripcion}}
                                                            @endif
                                                        </td>
                                                        <td class="text-end">S/ {{number_format($egreso->monto, 2)}}</td>
                                                        <td class="text-center">
                                                            @if($egreso->compra_id)
                                                            <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#detalleCompra{{$egreso->id}}" aria-expanded="false">
                                                                <span class="d-none d-md-inline">Ver </span>{{$egreso->compra ? $egreso->compra->detalles->count() : 0}} Productos
                                                            </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @if($egreso->compra_id)
                                                    <tr class="collapse" id="detalleCompra{{$egreso->id}}">
                                                        <td colspan="6" class="p-0">
                                                            <div class="p-3 bg-light">
                                                                <h6 class="mb-3">Detalle de Compra #{{$egreso->compra_id}}</h6>
                                                                @if($egreso->compra)
                                                                    <div class="table-responsive">
                                                                        <table class="table table-sm table-bordered">
                                                                            <thead class="table-light">
                                                                                <tr>
                                                                                    <th>Producto</th>
                                                                                    <th width="15%" class="text-center">Cantidad</th>
                                                                                    <th width="20%" class="text-end">Precio Unit.</th>
                                                                                    <th width="20%" class="text-end">Subtotal</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach($egreso->compra->detalles as $detalle)
                                                                                <tr>
                                                                                    <td>
                                                                                        {{$detalle->producto->nombre}}
                                                                                        @if($detalle->producto->codigo)
                                                                                            <br><small class="text-muted">Código: {{$detalle->producto->codigo}}</small>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td class="text-center">{{$detalle->cantidad}}</td>
                                                                                    <td class="text-end">S/ {{number_format($detalle->precio, 2)}}</td>
                                                                                    <td class="text-end">S/ {{number_format($detalle->cantidad * $detalle->precio, 2)}}</td>
                                                                                </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                            <tfoot>
                                                                                <tr class="table-active">
                                                                                    <th colspan="3" class="text-end">Total</th>
                                                                                    <th class="text-end">S/ {{number_format($egreso->compra->total, 2)}}</th>
                                                                                </tr>
                                                                            </tfoot>
                                                                        </table>
                                                                    </div>
                                                                @else
                                                                    <div class="alert alert-warning mb-0">No hay detalles disponibles para esta compra</div>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endif
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="table-active">
                                                        <th colspan="4" class="text-end">TOTAL EGRESOS</th>
                                                        <th class="text-end">S/ {{number_format($egresos->sum('monto'), 2)}}</th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <!-- Pestaña de Resumen -->
                                    <div class="tab-pane fade" id="resumen{{$caja->id}}" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card border-success mb-3">
                                                    <div class="card-header bg-success text-white">
                                                        <h6 class="mb-0"><i class="ni ni-money-coins me-1"></i> Ingresos</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <table class="table table-sm">
                                                            <tbody>
                                                                <tr>
                                                                    <th>Total Ventas:</th>
                                                                    <td class="text-end">S/ {{number_format($caja->movimientos->where('tipo', 'INGRESO')->where('venta_id', '!=', null)->sum('monto'), 2)}}</td>
                                                                </tr>
                                                                @foreach($caja->movimientos->where('tipo', 'INGRESO')->where('venta_id', null)->groupBy('descripcion') as $descripcion => $ingresos)
                                                                <tr>
                                                                    <th>{{$descripcion}}:</th>
                                                                    <td class="text-end">S/ {{number_format($ingresos->sum('monto'), 2)}}</td>
                                                                </tr>
                                                                @endforeach
                                                                <tr class="table-active">
                                                                    <th>TOTAL INGRESOS:</th>
                                                                    <th class="text-end">S/ {{number_format($caja->movimientos->where('tipo', 'INGRESO')->sum('monto'), 2)}}</th>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card border-danger mb-3">
                                                    <div class="card-header bg-danger text-white">
                                                        <h6 class="mb-0"><i class="ni ni-cart me-1"></i> Egresos</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <table class="table table-sm">
                                                            <tbody>
                                                                <tr>
                                                                    <th>Total Compras:</th>
                                                                    <td class="text-end">S/ {{number_format($caja->movimientos->where('tipo', 'EGRESO')->where('compra_id', '!=', null)->sum('monto'), 2)}}</td>
                                                                </tr>
                                                                @foreach($caja->movimientos->where('tipo', 'EGRESO')->where('compra_id', null)->groupBy('descripcion') as $descripcion => $egresos)
                                                                <tr>
                                                                    <th>{{$descripcion}}:</th>
                                                                    <td class="text-end">S/ {{number_format($egresos->sum('monto'), 2)}}</td>
                                                                </tr>
                                                                @endforeach
                                                                <tr class="table-active">
                                                                    <th>TOTAL EGRESOS:</th>
                                                                    <th class="text-end">S/ {{number_format($caja->movimientos->where('tipo', 'EGRESO')->sum('monto'), 2)}}</th>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card border-primary">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0"><i class="ni ni-chart-bar-32 me-1"></i> Balance Final</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p class="mb-1"><strong>Monto Inicial:</strong> S/ {{number_format($caja->monto_inicial, 2)}}</p>
                                                        <p class="mb-1"><strong>Total Ingresos:</strong> S/ {{number_format($caja->movimientos->where('tipo', 'INGRESO')->sum('monto'), 2)}}</p>
                                                        <p class="mb-1"><strong>Total Egresos:</strong> S/ {{number_format($caja->movimientos->where('tipo', 'EGRESO')->sum('monto'), 2)}}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        @if($caja->fecha_cierre)
                                                        <p class="mb-1"><strong>Monto Final Reportado:</strong> S/ {{number_format($caja->monto_final, 2)}}</p>
                                                        @endif
                                                        <p class="mb-1"><strong>Monto Teórico:</strong> 
                                                            S/ {{number_format($caja->monto_inicial + $caja->movimientos->where('tipo', 'INGRESO')->sum('monto') - $caja->movimientos->where('tipo', 'EGRESO')->sum('monto'), 2)}}
                                                        </p>
                                                        @if($caja->fecha_cierre)
                                                        <p class="mb-0">
                                                            <strong>Diferencia:</strong> 
                                                            @php
                                                                $diferencia = $caja->monto_final - ($caja->monto_inicial + $caja->movimientos->where('tipo', 'INGRESO')->sum('monto') - $caja->movimientos->where('tipo', 'EGRESO')->sum('monto'));
                                                            @endphp
                                                            <span class="{{ $diferencia == 0 ? 'text-success' : 'text-danger' }}">
                                                                S/ {{number_format(abs($diferencia), 2)}}
                                                                ({{ $diferencia == 0 ? 'Correcto' : ($diferencia > 0 ? 'Sobrante' : 'Faltante') }})
                                                            </span>
                                                        </p>
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
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Activar pestañas
        $('#cajaTabs a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });

        // Guardar la pestaña activa
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            localStorage.setItem('activeTab', $(e.target).attr('href'));
        });

        // Recuperar pestaña activa
        var activeTab = localStorage.getItem('activeTab');
        if(activeTab){
            $('#cajaTabs a[href="' + activeTab + '"]').tab('show');
        }
    });
</script>
@endsection
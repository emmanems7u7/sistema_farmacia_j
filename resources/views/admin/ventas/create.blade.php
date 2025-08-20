@extends('layouts.app', ['title' => 'Nueva venta'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Nueva venta'])
<div class="container-fluid mt--6">
    <div class="row">

    <!-- Card Principal - Diseño Mejorado -->


            <div class="card shadow-lg border-0 rounded-lg" style="height: auto; min-height: 0;">
                <div class="card-header bg-white border-bottom py-3"> <!-- Reduje el padding vertical -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div>
                                <h class="mb-0 text-dark font-weight-bold" style="font-size: 1.1rem;">Registrar Nueva Venta</h4>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('admin.ventas.index') }}" class="btn btn-outline-dark btn-sm py-1"> <!-- Reduje padding del botón -->
                                <i class="fas fa-list me-1"></i> Ver Historial
                            </a>
                        </div>
                    </div>
                </div>
            </div>








            <hr>
        <!-- Card 1: Registro de Productos -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 text-dark font-weight-bold">
                        <i class="fas fa-boxes text-primary me-2"></i>Productos
                        </h6>
                             </div>
                <div class="card-body">
                    <!-- Formulario de búsqueda -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <label for="cantidad" class="form-label">Cantidad</label>
                            <input type="number" class="form-control form-control-lg" id="cantidad" value="1" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="codigo" class="form-label">Código</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white"><i class="fas fa-barcode"></i></span>
                                <input id="codigo" type="text" class="form-control form-control-lg" name="codigo" placeholder="Escanear código" autofocus>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="d-flex gap-2 w-100">
                                <button type="button" class="btn btn-primary flex-grow-1" data-bs-toggle="modal" data-bs-target="#verModal">
                                    <i class="fas fa-search me-2"></i>
                                </button>
                                <a href="{{url('/admin/productos/create')}}" class="btn btn-success flex-grow-1">
                                    <i class="fas fa-plus me-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>



                    



                    <!-- Tabla de productos -->
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center px-1" style="width: 3%;">#</th>
                                    <th class="text-center px-1" style="width: 8%;">Código</th>
                                    <th class="text-center px-1" style="width: 5%;">Cant.</th>
                                    <th class="px-1" style="width: 35%;">Nombre</th>
                                    <th class="text-end px-1" style="width: 10%;">Unit.</th>
                                    <th class="text-end px-1" style="width: 12%;">Subtotal</th>
                                    <th class="text-center px-1" style="width: 4%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $cont = 1; $total_cantidad = 0; $total_venta = 0;?>
                                @foreach($tmp_ventas as $tmp_venta)
                                <tr>
                                    <td class="text-center">{{$cont++}}</td>
                                    <td class="text-center small">{{$tmp_venta->producto->codigo}}</td>
                                    <td class="text-center">{{$tmp_venta->cantidad}}</td>
                                    <td class="small text-truncate" style="max-width: 200px;" title="{{$tmp_venta->producto->nombre}}">
                                        {{$tmp_venta->producto->nombre}}
                                    </td>
                                    @php
                                        $lote = \App\Models\Lote::where('producto_id', $tmp_venta->producto_id)
                                                                ->latest('id')
                                                                ->first();
                                        $precioVenta = $lote->precio_venta ?? 0;
                                        $costo = $tmp_venta->cantidad * $precioVenta;
                                    @endphp
                                    <td class="text-end">Bs {{ number_format($precioVenta, 2) }}</td>
                                    <td class="text-end">Bs {{ number_format($costo, 2) }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger border-0 py-0 px-2 delete-btn" data-id="{{$tmp_venta->id}}">
                                            
                                             <i class="fas fa-trash-alt" style="font-size: 0.75rem;"></i>
                                        </button>

                                                           


                                    </td>
                                </tr>
                                @php
                                $total_cantidad += $tmp_venta->cantidad;
                                $total_venta += $costo;
                                @endphp
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2" class="text-end small">TOTAL</th>
                                    <th class="text-center small">{{$total_cantidad}}</th>
                                    <th colspan="2" class="text-end small">TOTAL VENTA</th>
                                    <th class="text-center text-primary small">Bs {{number_format($total_venta, 2)}}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Datos del Cliente y Venta -->
        <div class="col-lg-4">
            <div class="card shadow">
                

                <div class="card-header bg-white border-bottom">
                                        <h6 class="mb-0 text-dark font-weight-bold">
                                            <i class="fas fa-user-tag me-2"></i>Datos del Cliente
                                        </h6>
                                    </div>



                <div class="card-body">
                    <form action="{{ route('admin.ventas.create') }}" id="form_venta" method="POST">
                        @csrf
                        
                        <!-- Buscador de Cliente -->
                        <div class="mb-4">
                            <label class="form-label">Seleccionar Cliente</label>
                            <div class="d-flex gap-2 mb-3">
                                <button type="button" class="btn btn-primary flex-grow-1" data-bs-toggle="modal" data-bs-target="#clienteModal">
                                    <i class="fas fa-search me-2"></i>Buscar Cliente
                                </button>
                                <button type="button" class="btn btn-success flex-grow-1" data-bs-toggle="modal" data-bs-target="#clientecrearModal">
                                    <i class="fas fa-plus me-2"></i>Nuevo
                                </button>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Nombre del Cliente</label>
                                <input type="text" class="form-control" id="nombre_cliente_select" value="S/N" disabled>
                                <input type="hidden" id="id_cliente" name="cliente_id">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">NIT/CI</label>
                                <input type="text" class="form-control" id="nit_cliente_select" value="0" disabled>
                            </div>
                        </div>

                        <!-- Fecha -->
                        <div class="mb-4">
                            <label for="fecha" class="form-label">Fecha de Venta</label>
                            <input 
                                type="date" 
                                class="form-control" 
                                name="fecha" 
                                value="{{ old('fecha', date('Y-m-d')) }}" 
                                min="{{ date('Y-m-d') }}" 
                                required
                            >
                            @error('fecha')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>


                        <!-- Total -->
                        <div class="mb-4">
                            <label class="form-label">Total a Pagar</label>
                            <input type="text" class="form-control form-control-lg text-center fw-bold text-white bg-success" 
                                   value="Bs {{number_format($total_venta, 2)}}" readonly>
                            <input type="hidden" name="precio_total" value="{{$total_venta}}">
                        </div>

                        <!-- Botón Registrar -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg py-3">
                                <i class="fas fa-save me-2"></i>REGISTRAR VENTA
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal Productos -->

<div class="modal fade" id="verModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-gradient-primary text-white border-bottom-0">
                <div class="d-flex align-items-center">
                    <h5 class="modal-title mb-0">Listado de Productos</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Cuerpo del Modal -->
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table id="mitabla" class="table table-hover align-items-center mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder ps-4">#</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder text-center">Acción</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder">Código</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder">Nombre</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder text-center">Stock</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder text-end">Precio</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder">Fecha Venc.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productos as $producto)
                            <tr>
                                <td class="text-xs font-weight-normal ps-4">{{ $loop->iteration }}</td>
                                <td class="text-center">
                                    <button type="button" 
                                            class="btn btn-sm @if($producto->stock <= 0) btn-outline-secondary disabled @else btn-outline-primary @endif seleccionar-btn" 
                                            data-id="{{$producto->codigo}}"
                                            data-nombre="{{$producto->nombre}}"
                                            @if($producto->stock <= 0) disabled @endif>
                                        <i class="fas fa-check-circle me-1"></i>
                                    </button>
                                </td>
                                <td class="text-xs font-weight-normal">
                                    <span class="badge bg-gray-200 text-dark">{{ $producto->codigo }}</span>
                                </td>
                                <td class="text-xs font-weight-normal">
                                    <strong>{{ $producto->nombre }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge 
                                        @if($producto->stock <= 0) bg-gradient-danger
                                        @elseif($producto->stock <= 5) bg-gradient-danger 
                                        @else bg-gradient-success @endif">
                                        @if($producto->stock <= 0) 0 STOCK @else {{ $producto->stock }} @endif
                                    </span>
                                </td>
                                @php
                                    $lote = $producto->lotes->sortByDesc('id')->first();
                                @endphp
                                <td class="text-end text-xs font-weight-bold text-primary">
                                    @if($lote)
                                        Bs {{ number_format($lote->precio_venta, 2) }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-xs font-weight-normal">
                                    @if($lote && $lote->fecha_vencimiento)
                                        <span class="badge 
                                            @if(\Carbon\Carbon::parse($lote->fecha_vencimiento)->isPast()) 
                                                bg-gradient-danger 
                                            @else 
                                                bg-gradient-info 
                                            @endif">
                                            {{ \Carbon\Carbon::parse($lote->fecha_vencimiento)->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="badge bg-gray-200">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pie del Modal -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.btn-outline-secondary.disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>

<!-- Modal Clientes -->

<div class="modal fade" id="clienteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content border-0 shadow-lg">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-gradient-primary text-white border-bottom-0">
                <div class="d-flex align-items-center">
                    <div class="icon icon-shape bg-white text-primary rounded-circle shadow me-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="modal-title mb-0">Listado de Clientes</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Cuerpo del Modal -->
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table id="mitabla2" class="table table-hover align-items-center mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder">#</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder">Acción</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder">Nombre</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder">NIT/CI</th>
                               
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clientes as $cliente)
                            <tr>
                                <td class="text-xs font-weight-normal ps-4">{{ $loop->iteration }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary seleccionar-btn-cliente" 
                                            data-id="{{$cliente->id}}" 
                                            data-nit="{{$cliente->nit_ci}}" 
                                            data-nombre_cliente="{{$cliente->nombre_cliente}}">
                                        <i class="fas fa-check me-1"></i> 
                                    </button>
                                </td>
                                <td class="text-xs font-weight-normal">
                                    <strong>{{ $cliente->nombre_cliente }}</strong>
                                </td>
                                <td class="text-xs font-weight-normal">
                                    <span class="badge bg-gray-200 text-dark">{{ $cliente->nit_ci }}</span>
                                </td>
                                
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pie del Modal -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Cliente -->
<div class="modal fade" id="clientecrearModal" tabindex="-1" aria-labelledby="clientecrearModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="clientecrearModalLabel">Registrar cliente</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="nombre_cliente">Nombre</label>
                            <input type="text" class="form-control" id="nombre_cliente" value="{{ old('nombre_cliente') }}">
                            @error('nombre_cliente')
                                <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                    </div>   

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nit_ci">NIT/CI</label>
                            <input type="text" class="form-control" id="nit_ci" value="{{ old('nit_ci') }}">
                            @error('nit_ci')
                                <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                    </div>  

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="celular">Celular</label>
                            <input type="text" class="form-control" id="celular" value="{{ old('celular') }}">
                            @error('celular')
                                <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                    </div>                 

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="email">Correo</label>
                            <input type="email" class="form-control" id="email" value="{{ old('email') }}">
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="guardar_cliente()" class="btn btn-primary">
                    <i class="fas fa-save"></i> Registrar
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .card {
        border: none;
        border-radius: 0.5rem;
    }
    
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
        padding: 1.25rem 1.5rem;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(87deg, #2dce89 0, #2dcecc 100%) !important;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
    }
    
    .form-control-lg {
        font-size: 1rem;
        padding: 0.75rem 1rem;
    }
    
    .btn-lg {
        padding: 0.8rem 1.5rem;
        font-size: 1.05rem;
    }
    
    .input-group-text {
        background-color: #f8f9fa;
    }
    
    .delete-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// REGISTRAR UN CLIENTE
function guardar_cliente(){
    const data = {
        nombre_cliente: $('#nombre_cliente').val(),
        nit_ci: $('#nit_ci').val(),
        celular: $('#celular').val(),
        email: $('#email').val(),
        _token: '{{csrf_token()}}' 
    };

    $.ajax({
        url: '{{route("admin.ventas.cliente.store")}}',
        type: 'POST',
        data: data,
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    position: "top-end",
                    icon: "success",
                    title: "Se agregó el cliente",
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', 'No se pudo registrar el cliente', 'error');
            }
        },
        error: function(error) {
            Swal.fire('Error', 'Ocurrió un error al registrar el cliente', 'error');
        }
    });
}

// Seleccionar cliente - VERSIÓN CORREGIDA
$(document).on('click', '.seleccionar-btn-cliente', function(){
    const id_cliente = $(this).data('id');
    const nombre_cliente = $(this).data('nombre_cliente');
    const nit_ci = $(this).data('nit');
    
    $('#nombre_cliente_select').val(nombre_cliente);
    $('#nit_cliente_select').val(nit_ci);
    $('#id_cliente').val(id_cliente);
    
    // Cerrar modal - FORMA COMPATIBLE CON BOOTSTRAP 5
    const clienteModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('clienteModal'));
    clienteModal.hide();
    
    // Limpiar posibles overlays
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
});

// Seleccionar producto - VERSIÓN CORREGIDA
$(document).on('click', '.seleccionar-btn', function(){
    const id_producto = $(this).data('id');
    $('#codigo').val(id_producto);
    
    // Cerrar modal - FORMA COMPATIBLE CON BOOTSTRAP 5
    const verModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('verModal'));
    verModal.hide();
    
    // Limpiar posibles overlays
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    
    $('#codigo').focus();
});

// Eliminar producto de la venta temporal - VERSIÓN MEJORADA
$(document).on('click', '.delete-btn', function() {
    const id = $(this).data('id');
    if (id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esta acción!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{url('/admin/ventas/create/tmp')}}/"+id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    beforeSend: function() {
                        // Mostrar loader
                        Swal.showLoading();
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Producto eliminado",
                                showConfirmButton: false,
                                timer: 1000
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message || 'Error al eliminar', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error en la conexión', 'error');
                    }
                });
            }
        });
    }
});

// Buscar producto por código (Enter) - VERSIÓN MEJORADA
$(document).ready(function() {
    $('#codigo').focus();
    
    $('#form_venta').on('keypress', function(e) {
        if(e.keyCode === 13) {   
            e.preventDefault();
        }
    });

    $('#codigo').on('keyup', function(e) {
        if (e.which === 13) {
            const codigo = $(this).val().trim();
            const cantidad = $('#cantidad').val();
            
            if(!codigo) {
                Swal.fire('Error', 'Por favor ingrese un código', 'warning');
                return;
            }

            if(cantidad <= 0) {
                Swal.fire('Error', 'La cantidad debe ser mayor a cero', 'warning');
                return;
            }

            $.ajax({
                url: "{{ route('admin.ventas.tmp_ventas') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    codigo: codigo,
                    cantidad: cantidad
                },
                beforeSend: function() {
                    Swal.showLoading();
                },
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Producto agregado",
                            showConfirmButton: false,
                            timer: 1000
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Error al agregar', 'error');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Error en la conexión';
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', errorMsg, 'error');
                }
            });
        }
    });


// Configuración DataTables
    $('#mitabla, #mitabla2').DataTable({
        "pageLength": 5,
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
        },
        "responsive": true,
        "autoWidth": false
    });






    // DataTables - CONFIGURACIÓN MEJORADA
  
});
</script>
@endsection
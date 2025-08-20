@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Modificación de Venta'])

<div class="container-fluid py-4">
    <div class="row">
        <!-- Card 1: Productos -->
        <div class="col-lg-8">
            <div class="card shadow-lg">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Productos de la Venta</h5>
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
                                    <i class="fas fa-search me-2"></i>Buscar
                                </button>
                                <a href="{{url('/admin/productos/create')}}" class="btn btn-success flex-grow-1">
                                    <i class="fas fa-plus me-2"></i>Nuevo
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de productos -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
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
                                <?php $cont = 1; $total_cantidad = 0; $total_venta = 0;?>
                                @foreach($venta->detallesVenta as $detalle)
                                <tr>
                                    <td class="text-center">
                                         <span class="text-secondary text-xs font-weight-bold">{{$cont++}}</td>
                                    <td class="text-center">
                                         <span class="text-secondary text-xs font-weight-bold">{{$detalle->producto->codigo}}</td>
                                    <td class="text-center">
                                         <span class="text-secondary text-xs font-weight-bold">{{$detalle->cantidad}}</td>
                                    <td> <span class="text-secondary text-xs font-weight-bold">{{$detalle->producto->nombre}}</td>
                                  @php
    // Obtener el lote con cantidad positiva más reciente para este producto
    $lote = $detalle->producto->lotes()->where('cantidad', '>', 0)->orderBy('fecha_ingreso', 'desc')->first();
    $precioVenta = $lote ? $lote->precio_venta : 0;
    $costo = $detalle->cantidad * $precioVenta;
@endphp

<td class="text-center">
     <span class="text-secondary text-xs font-weight-bold">Bs {{ number_format($precioVenta, 2) }}</td>
<td class="text-center">
     <span class="text-secondary text-xs font-weight-bold">Bs {{ number_format($costo, 2) }}</td>

                                    <td class="text-center">
                                        <button type="button" class="btn btn-xs btn-outline-danger delete-btn" data-id="{{$detalle->id}}">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                       
                                    </td>
                                </tr>
                                @php
                                $total_cantidad += $detalle->cantidad;
                                $total_venta += $costo;
                                @endphp
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2" class="text-end">TOTAL</th>
                                    <th class="text-center">{{$total_cantidad}}</th>
                                    <th colspan="2" class="text-end">TOTAL VENTA</th>
                                    <th class="text-center text-primary">Bs {{number_format($total_venta, 2)}}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Información del Cliente -->
        <div class="col-lg-4">
            <div class="card shadow-lg">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user-tag me-2"></i>Información de la Venta</h5>
                </div>
                <div class="card-body">
                    <form action="{{url('/admin/ventas', $venta->id) }}" id="form_venta" method="post">
                        @csrf
                        @method('PUT')

                        <!-- Buscador de Cliente -->
                        <div class="mb-4">
                            <label class="form-label">Cliente</label>
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
                                <input type="text" class="form-control" id="nombre_cliente_select" value="{{$venta->cliente->nombre_cliente ?? 'S/N'}}" disabled>
                                <input type="hidden" id="id_cliente" name="cliente_id" value="{{$venta->cliente->id ?? ''}}">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">NIT/CI</label>
                                <input type="text" class="form-control" id="nit_cliente_select" value="{{$venta->cliente->nit_ci ?? 'SIN'}}" disabled>
                            </div>
                        </div>

                        <!-- Fecha -->
                        <div class="mb-4">
                            <label for="fecha" class="form-label">Fecha de Venta</label>
                            <input type="date" class="form-control" name="fecha" value="{{old('fecha', $venta->fecha) }}" required>
                            @error('fecha')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Total -->
                        <div class="mb-4">
                            <label class="form-label">Total a Pagar</label>
                            <input type="text" class="form-control form-control-lg text-center fw-bold text-white bg-success" 
                                   value="${{number_format($total_venta, 2)}}" readonly>
                            <input type="hidden" name="precio_total" value="{{$total_venta}}">
                        </div>

                        <!-- Botón Actualizar -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg py-3">
                                <i class="fas fa-save me-2"></i>ACTUALIZAR VENTA
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Productos -->
<div class="modal fade" id="verModal" tabindex="-1" aria-labelledby="verModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="verModalLabel">Listado de Productos</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table id="mitabla" class="table table-striped table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nro</th>
                            <th>Acción</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Stock</th>
                            <th>Precio</th>
                            <th>Fecha Venc.</th>
                            <th>Imagen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $contador = 1; ?>
                        @foreach($productos as $producto)
                        <tr>
                            <td>{{ $contador++ }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-info seleccionar-btn" data-id="{{$producto->codigo}}">
                                    Seleccionar
                                </button>
                            </td>
                            <td>{{ $producto->codigo }}</td>
                            <td><strong>{{ $producto->nombre }}</strong></td>
                            <td>{{ $producto->descripcion }}</td>
                            <td style="color: red; font-weight: bold;">{{ $producto->stock }}</td>
                            <td style="color: red; font-weight: bold;">Bs {{number_format($producto->precio_venta, 2)}}</td>
                            <td style="color: red; font-weight: bold;">{{ $producto->fecha_vencimiento }}</td>
                            <td class="text-center">
                                @if($producto->imagen)
                                    <img src="{{ asset('storage/' . $producto->imagen) }}" width="80" height="80" alt="Imagen" class="img-thumbnail">
                                @else
                                    <p class="text-muted">Sin imagen</p>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Clientes -->
<div class="modal fade" id="clienteModal" tabindex="-1" aria-labelledby="clienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="clienteModalLabel">Listado de Clientes</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table id="mitabla2" class="table table-striped table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nro</th>
                            <th>Acción</th>
                            <th>Nombre</th>
                            <th>NIT/CI</th>
                            <th>Celular</th>
                            <th>Correo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $contador = 1; ?>
                        @foreach($clientes as $cliente)
                        <tr>
                            <td>{{ $contador++ }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-info seleccionar-btn-cliente" 
                                        data-id="{{$cliente->id}}" 
                                        data-nit="{{$cliente->nit_ci}}" 
                                        data-nombre_cliente="{{$cliente->nombre_cliente}}">
                                    Seleccionar
                                </button>
                            </td>
                            <td><strong>{{ $cliente->nombre_cliente }}</strong></td>
                            <td><strong>{{ $cliente->nit_ci }}</strong></td>
                            <td><strong>{{ $cliente->celular }}</strong></td>
                            <td><strong>{{ $cliente->email }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Cliente -->
<div class="modal fade" id="clientecrearModal" tabindex="-1" aria-labelledby="clientecrearModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="clientecrearModalLabel">Registrar Nuevo Cliente</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="nombre_cliente">Nombre</label>
                            <input type="text" class="form-control" id="nombre_cliente" value="{{ old('nombre_cliente') }}" placeholder="Nombre completo">
                            @error('nombre_cliente')
                                <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                    </div>   

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nit_ci">NIT/CI</label>
                            <input type="text" class="form-control" id="nit_ci" value="{{ old('nit_ci') }}" placeholder="Número de identificación">
                            @error('nit_ci')
                                <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                    </div>  

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="celular">Celular</label>
                            <input type="text" class="form-control" id="celular" value="{{ old('celular') }}" placeholder="Número de contacto">
                            @error('celular')
                                <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                    </div>                 

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="email">Correo</label>
                            <input type="email" class="form-control" id="email" value="{{ old('email') }}" placeholder="Correo electrónico">
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="guardar_cliente()" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Registrar
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
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
        beforeSend: function() {
            Swal.showLoading();
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Cliente registrado",
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', response.message || 'No se pudo registrar el cliente', 'error');
            }
        },
        error: function(xhr) {
            let errorMsg = 'Error al registrar el cliente';
            if(xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            Swal.fire('Error', errorMsg, 'error');
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
                    url: "{{url('/admin/ventas/detalle')}}/"+id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    beforeSend: function() {
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
            const id_venta = '{{$venta->id}}';
            
            if(!codigo) {
                Swal.fire('Advertencia', 'Por favor ingrese un código', 'warning');
                return;
            }

            if(cantidad <= 0) {
                Swal.fire('Error', 'La cantidad debe ser mayor a cero', 'error');
                return;
            }

            $.ajax({
                url: "{{ route('admin.detalle.ventas.store') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    codigo: codigo,
                    cantidad: cantidad,
                    id_venta: id_venta
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

    // DataTables - CONFIGURACIÓN MEJORADA
    $('#mitabla').DataTable({
        "pageLength": 5,
        "responsive": true,
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
        "dom": '<"top"f>rt<"bottom"lip><"clear">'
    });

    $('#mitabla2').DataTable({
        "pageLength": 5,
        "responsive": true,
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
        "dom": '<"top"f>rt<"bottom"lip><"clear">'
    });
});
</script>
@endpush

@section('css')
<style>
    .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
        padding: 1rem 1.5rem;
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
        background-color: #f8f9fa !important;
    }
    
    .form-control-lg {
        font-size: 1rem;
        padding: 0.75rem 1rem;
    }
    
    .btn-lg {
        padding: 0.8rem 1.5rem;
        font-size: 1.05rem;
        font-weight: 600;
    }
    
    .delete-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .modal-header {
        background-color: #2dce89;
        color: white;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(45, 206, 137, 0.1);
    }
</style>
@endsection
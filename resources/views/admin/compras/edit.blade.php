@extends('layouts.app', ['title' => 'Editar Compra'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Editar Compra #'.$compra->id])
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-12">

            <!-- Encabezado -->
            <div class="card shadow mb-4 border-0">
                <div class="card-header btn-white text-white py-3">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Editar Compra #{{ $compra->id }}</h4>
                </div>
            </div>

            <form action="{{ url('/admin/compras', $compra->id) }}" method="post" id="form_compra">
                @csrf
                @method('PUT')
                <input type="hidden" name="id_compra" value="{{ $compra->id }}">

                <div class="row">
                    <!-- Columna izquierda: Productos -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm mb-4 border-0">
                            <div class="card-header bg-gradient-success text-white">
                                <strong>Productos</strong>
                            </div>
                            <div class="card-body">
                               <div class="row g-3 mb-4 align-items-end">
                                    <!-- Campo Cantidad -->
                                    <div class="col-md-2">
                                        <div class="form-group h-100 d-flex flex-column justify-content-end">
                                            <label class="form-label mb-1">Cantidad</label>
                                            <input type="number" name="cantidad" value="1" class="form-control" required>
                                        </div>
                                    </div>
                                    
                                    <!-- Campo Código -->
                                    <div class="col-md-6">
                                        <div class="form-group h-100 d-flex flex-column justify-content-end">
                                            <label class="form-label mb-1">Código</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                                <input type="text" id="codigo" name="codigo" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Botones de Acción -->
                                    <div class="col-md-4">
                                        <div class="d-flex h-100 align-items-end gap-2">
                                            <button type="button" class="btn btn-outline-primary py-2 flex-grow-1" 
                                                    data-bs-toggle="modal" data-bs-target="#verModal">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            <a href="{{ url('/admin/productos/create') }}" 
                                            class="btn btn-outline-success py-2 flex-grow-1">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabla de productos -->
                                <div class="table-responsive">
    <table class="table table-sm table-bordered table-hover align-middle mb-0" style="font-size: 0.875rem;">
        <thead class="table-light text-center">
            <tr>
                <th class="text-center px-2 py-1" style="width: 4%;">#</th>
                <th class="text-center px-2 py-1" style="width: 12%;">Código</th>
                <th class="text-center px-2 py-1" style="width: 8%;">Cant.</th>
                <th class="px-2 py-1" style="width: 35%;">Producto</th>
                <th class="text-end px-2 py-1" style="width: 12%;">P. Unit.</th>
                <th class="text-end px-2 py-1" style="width: 15%;">Subtotal</th>
                <th class="text-center px-2 py-1" style="width: 5%;">Acción</th>
            </tr>
        </thead>
        <tbody>
            @php $cont = 1; $total_cantidad = 0; $total_compra = 0; @endphp
            @foreach($compra->detalles as $detalle)
            <tr>
                <td class="text-center px-2 py-1">{{ $cont++ }}</td>
                <td class="text-center px-2 py-1">
                    <span class="badge bg-secondary bg-opacity-10 text-dark border border-secondary border-opacity-25">{{ $detalle->producto->codigo }}</span>
                </td>
                <td class="text-center px-2 py-1">{{ $detalle->cantidad }}</td>
                <td class="px-2 py-1">{{ $detalle->producto->nombre }}</td>
                @php
                    $lote = \App\Models\Lote::where('producto_id', $detalle->producto_id)
                                        ->latest('id')
                                        ->first();
                    $precioCompra = $lote->precio_compra ?? 0;
                    $costo = $detalle->cantidad * $precioCompra;
                @endphp
                <td class="text-end px-2 py-1">Bs{{ number_format($precioCompra, 2) }}</td>
                <td class="text-end px-2 py-1 fw-semibold">Bs{{ number_format($costo, 2) }}</td>
                <td class="text-center px-2 py-1">
                    <button type="button" class="btn btn-xs btn-outline-danger delete-btn" data-id="{{ $detalle->id }}" title="Eliminar">
                        <i class="fas fa-trash-alt fa-xs"></i>
                    </button>
                </td>
            </tr>
            @php
                $total_cantidad += $detalle->cantidad;
                $total_compra += $costo;
            @endphp
            @endforeach
        </tbody>
        <tfoot class="table-light">
            <tr>
                <td colspan="2" class="text-end fw-bold px-2 py-1">Total</td>
                <td class="text-center fw-bold px-2 py-1">{{ $total_cantidad }}</td>
                <td colspan="2" class="text-end fw-bold px-2 py-1">Total Compra</td>
                <td class="text-center fw-bold px-2 py-1">Bs{{ number_format($total_compra, 2) }}</td>
                <td class="px-2 py-1"></td>
            </tr>
        </tfoot>
    </table>
</div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha: Detalles de compra -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm mb-4 border-0">
                            <div class="card-header bg-gradient-success">
                                <strong>Detalles de compra</strong>
                            </div>
                            <div class="card-body">
                               <div class="mb-3">
                                    <label class="form-label d-block mb-1">LABORATORIO</label>
                                    <div class="input-group">
                                        <button type="button" 
                                                class="btn btn-outline-primary border-end-0 rounded-end-0 py-2" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#labModal"
                                                style="min-width: 120px;">
                                            <i class="fas fa-search me-1"></i> Buscar
                                        </button>
                                        <input type="text" 
                                            class="form-control border-start-0 rounded-start-0" 
                                            value="{{ $compra->laboratorio->nombre }}" 
                                            id="nombre_laboratorio" 
                                            readonly
                                            style="height: calc(2.5rem + 2px);">
                                        <input type="hidden" name="laboratorio_id" value="{{ $compra->laboratorio->id }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Fecha</label>
                                    <input type="date" name="fecha" class="form-control" value="{{ $compra->fecha }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Comprobante</label>
                                    <select name="comprobante" class="form-select">
                                        <option value="FACTURA" {{ trim($compra->comprobante) == 'FACTURA' ? 'selected' : '' }}>FACTURA</option>
                                        <option value="RECIBO" {{ trim($compra->comprobante) == 'RECIBO' ? 'selected' : '' }}>RECIBO</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Total</label>
                                    <input type="text" class="form-control text-center bg-gradient-danger text-white fw-bold fs-5" name="precio_total" value="{{ number_format($total_compra, 2) }}" readonly>
                                 <hr>
                                    <button type="submit" class="btn bg-gradient-success">Actualizar Compra</button>
                               
                                <a href="{{url('/admin/compras')}}" class="btn btn-primary">
                                    <i class="fas fa-arrow-left me-1"></i> Volver
                                </a>
                                </div>

                                
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Modal Productos -->
<div class="modal fade" id="verModal" tabindex="-1" aria-labelledby="verModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-semibold">
                    <i class="fas fa-boxes me-2"></i>Listado de Productos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table id="mitabla" class="table table-sm table-hover mb-0" style="width:100%">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-uppercase small fw-bold" style="width: 5%;">#</th>
                                <th class="text-uppercase small fw-bold" style="width: 10%;">Acciones</th>
                                <th class="text-uppercase small fw-bold" style="width: 15%;">Código</th>
                                <th class="text-uppercase small fw-bold" style="width: 50%;">Nombre</th>
                                <th class="text-uppercase small fw-bold text-center" style="width: 20%;">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productos as $producto)
                            @php
                                // Definir clase según el nivel de stock
                                $stockClass = '';
                                $stockText = $producto->stock;
                                
                                if($producto->stock <= 0) {
                                    $stockClass = 'bg-danger bg-opacity-10 text-black';
                                    $stockText = 'AGOTADO';
                                } elseif($producto->stock < 10) {
                                    $stockClass = 'bg-warning bg-opacity-10 text-black';
                                    $stockText = ' ('.$producto->stock.')';
                                } else {
                                    $stockClass = 'bg-success bg-opacity-10 text-black';
                                }
                            @endphp
                            <tr>
                                <td class="small">{{ $loop->iteration }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary rounded-circle seleccionar-btn p-1" 
                                        data-id="{{ $producto->codigo }}" title="Seleccionar">
                                        <i class="fas fa-check" style="font-size: 0.7rem;"></i>
                                    </button>
                                </td>
                                <td class="small">
                                    <span class="badge bg-light text-dark border">{{ $producto->codigo }}</span>
                                </td>
                                <td class="small fw-semibold">{{ $producto->nombre }}</td>
                                <td class="text-center small fw-bold">
                                    <span class="badge {{ $stockClass }} rounded-pill px-2 py-1">
                                        {{ $stockText }}
                                    </span>
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


<!-- Modal Laboratorios -->
<div class="modal fade" id="labModal" tabindex="-1" aria-labelledby="labModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="labModalLabel">Listado de laboratorios</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table id="mitabla2" class="table table-striped table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nro</th>
                            <th>Acción</th>
                            <th>Laboratorio</th>
                            <th>Teléfono</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $contador = 1; ?>
                        @foreach($laboratorios as $laboratorio)
                        <tr>
                            <td>{{ $contador++ }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-info seleccionar-btn-laboratorio" 
                                        data-id="{{$laboratorio->id}}" 
                                        data-nombre="{{$laboratorio->nombre}}">
                                    Seleccionar
                                </button>
                            </td>
                            <td><strong>{{ $laboratorio->nombre }}</strong></td>
                            <td>{{ $laboratorio->telefono }}</td>
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
@endsection

@section('css')
<style>
    .table thead th {
        background-color: #f1f5f9;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(59, 130, 246, 0.05);
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Seleccionar laboratorio
$(document).on('click', '.seleccionar-btn-laboratorio', function() {
    const id_laboratorio = $(this).data('id');
    const nombre = $(this).data('nombre');
    $('#nombre_laboratorio').val(nombre);
    $('#id_laboratorio').val(id_laboratorio);
    $('#labModal').modal('hide');

    // Eliminar manualmente el backdrop si persiste
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open'); // Asegura que se pueda hacer scroll nuevamente
});

   // Seleccionar producto
$(document).on('click', '.seleccionar-btn', function() {
    const id_producto = $(this).data('id');
    $('#codigo').val(id_producto);
    $('#verModal').modal('hide');
    $('#codigo').focus();

    // Eliminar manualmente el backdrop si persiste
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
});

    // Eliminar producto
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
                confirmButtonText: 'Sí, eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{url('/admin/compras/detalle')}}/"+id,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    position: "top-end",
                                    icon: "success",
                                    title: "Producto eliminado",
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                location.reload();
                            } else {
                                Swal.fire('Error', response.message || 'Error al eliminar', 'error');
                            }
                        },
                        error: function(error) {
                            Swal.fire('Error', 'Error en la conexión', 'error');
                        }
                    });
                }
            });
        }
    });

    // Buscar producto por código
    $('#codigo').focus();
    $('#form_compra').on('keypress', function(e) {
        if(e.keyCode === 13) {   
            e.preventDefault();
        }
    });
    
    $('#codigo').on('keyup', function(e) {
        if (e.which === 13) {
            const codigo = $(this).val();
            const cantidad = $('#cantidad').val();
            const id_compra = $('#id_compra').val();
            const id_laboratorio = $('#id_laboratorio').val();

            if(codigo.length > 0) {
                $.ajax({
                    url: "{{ route('admin.detalle.compras.store') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        codigo: codigo,
                        cantidad: cantidad,
                        id_compra: id_compra,
                        id_laboratorio: id_laboratorio
                    },
                    success: function(response) {
                        if(response.success) {
                            Swal.fire({
                                position: "top-end",
                                icon: "success",
                                title: "Producto agregado",
                                showConfirmButton: false,
                                timer: 1500
                            });
                            location.reload();
                        } else {
                            Swal.fire('Error', response.message || 'Error al agregar', 'error');
                        }
                    },
                    error: function(error) {
                        Swal.fire('Error', 'Error en la conexión', 'error');
                    }
                });
            }
        }
    });

    // DataTables
    $('#mitabla').DataTable({
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
        }
    });

    $('#mitabla2').DataTable({
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
        }
    });
});
</script>
@endsection
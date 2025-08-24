@extends('layouts.argon')

@section('content')
    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col">
                <div class="card">
                    <!-- Card Header -->
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="mb-0"><i class="fas fa-shopping-cart me-2"></i> <strong>Detalle de la
                                        Compra</strong></h4>
                            </div>
                            <div class="col-4 text-end">
                                <div>

                                    <a href="{{ url('/admin/compras/pdf/' . $compra->id) }}" target="_blank"
                                        class="btn btn-sm btn-danger">
                                        <i class="ni ni-single-copy-04 me-1"></i> Exportar PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <!-- Card Body -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="modal-header bg-gradient-info text-white">
                                <h5 class="mb-0"><i class="fas fa-boxes me-2"></i> Productos Comprados</h5>
                            </div>


                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover align-middle mb-0"
                                        style="font-size: 0.875rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center px-2 py-1" style="width: 4%;">#</th>
                                                <th class="text-center px-2 py-1" style="width: 12%;">Código</th>
                                                <th class="text-center px-2 py-1" style="width: 8%;">Cantidad</th>
                                                <th class="px-2 py-1" style="width: 35%;">Producto</th>
                                                <th class="text-end px-2 py-1" style="width: 12%;">P. Unit.</th>
                                                <th class="text-end px-2 py-1" style="width: 15%;">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $cont = 1;
                                                $total_cantidad = 0;
                                            $total_compra = 0; @endphp
                                            @foreach($compra->detalles as $detalle)
                                                <tr>
                                                    <td class="text-center px-2 py-1">{{ $cont++ }}</td>
                                                    <td class="text-center px-2 py-1">
                                                        <span
                                                            class="badge bg-secondary bg-opacity-10 text-dark border border-secondary border-opacity-25">{{ $detalle->producto->codigo }}</span>
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
                                                    <td class="text-end px-2 py-1 fw-semibold">Bs{{ number_format($costo, 2) }}
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
                                                <td class="text-center fw-bold px-2 py-1">
                                                    Bs{{ number_format($total_compra, 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Columna derecha (Fecha y detalles de compra) -->
                    <div class="col-md-4">
                        <div class="card border-info mb-3">
                            <div class="modal-header bg-gradient-info text-white">
                                <h5 class="card-title mb-0"><i class="fas fa-calendar-alt me-2"></i>Información de Compra
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label for="fecha" class="form-label">Fecha</label>
                                    <input type="date" class="form-control bg-light" value="{{$compra->fecha}}" disabled>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="comprobante{{$compra->id}}" class="form-label">Comprobante</label>
                                    <select name="comprobante" id="comprobante{{$compra->id}}" class="form-control bg-light"
                                        disabled>
                                        <option value="FACTURA" {{ trim($compra->comprobante) == 'FACTURA' ? 'selected' : '' }}>FACTURA</option>
                                        <option value="RECIBO" {{ trim($compra->comprobante) == 'RECIBO' ? 'selected' : '' }}>
                                            RECIBO</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="precio_total" class="form-label">Monto Total</label>
                                    <input type="text" class="form-control text-center fw-bold text-danger bg-light"
                                        value="Bs{{number_format($total_compra, 2)}}" disabled>
                                </div>

                                <a href="{{ url('/admin/compras/' . $compra->id . '/edit') }}"
                                    class="btn btn-sm btn-icon   bg-gradient-success  mx-3" title="Editar">
                                    <i class="fas fa-pencil-alt"></i>Editar
                                </a>
                                <a href="{{url('/admin/compras')}}" class="btn btn-primary">
                                    <i class="fas fa-arrow-left me-1"></i> Volver
                                </a>

                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, .125);
        }

        .table thead th {
            background-color: #f1f5f9;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        .badge.bg-secondary {
            background-color: #6c757d !important;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(59, 130, 246, 0.05);
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }

        .card.border-info {
            border-color: #0dcaf0 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .fw-bold {
            font-weight: 600 !important;
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        //selecionar de la busqueda lab
        document.querySelectorAll('.seleccionar-btn-laboratorio').forEach(btn => {
            btn.addEventListener('click', function () {
                const id_laboratorio = this.dataset.id;
                const nombre = this.dataset.nombre;
                document.getElementById('nombre_laboratorio').value = nombre;
                document.getElementById('id_laboratorio').value = id_laboratorio;
                const labModal = bootstrap.Modal.getInstance(document.getElementById('labModal'));
                labModal.hide();
            });
        });

        //selecionar de la busqueda un producto
        document.querySelectorAll('.seleccionar-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id_producto = this.dataset.id;
                document.getElementById('codigo').value = id_producto;
                const verModal = bootstrap.Modal.getInstance(document.getElementById('verModal'));
                verModal.hide();
                document.getElementById('codigo').focus();
            });
        });

        //eliminar un compra
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                if (id) {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡No podrás revertir esto!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch("{{url('/admin/compras/create/tmp')}}/" + id, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'X-HTTP-Method-Override': 'DELETE'
                                }
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            position: 'top-end',
                                            icon: 'success',
                                            title: 'Se eliminó el producto',
                                            showConfirmButton: false,
                                            timer: 1500
                                        });
                                        location.reload();
                                    } else {
                                        Swal.fire('Error', 'No se pudo eliminar el producto', 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire('Error', 'Ocurrió un error', 'error');
                                });
                        }
                    });
                }
            });
        });

        //para que aparesca al presionar enter
        document.getElementById('codigo').focus();

        //prevenir el submit al presionar enter
        document.getElementById('form_compra').addEventListener('keypress', function (e) {
            if (e.keyCode === 13) {
                e.preventDefault();
            }
        });

        //para buscar el producto mediante un codigo
        document.getElementById('codigo').addEventListener('keyup', function (e) {
            if (e.which === 13) {
                const codigo = this.value;
                const cantidad = document.getElementById('cantidad').value;

                if (codigo.length > 0) {
                    fetch("{{ route('admin.compras.tmp_compras')}}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            codigo: codigo,
                            cantidad: cantidad
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Se registró el producto',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                location.reload();
                            } else {
                                Swal.fire('Error', data.message || 'No se encontró el producto', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Ocurrió un error', 'error');
                        });
                }
            }
        });

        // DataTables (versión compatible con Bootstrap 5)
        $(document).ready(function () {
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
                    "lengthMenu": "Mostrar _MENU_ ",
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
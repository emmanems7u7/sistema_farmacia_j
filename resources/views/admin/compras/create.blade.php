@extends('layouts.argon')

@section('content')
    <script>

        // Agrega esto en tu sección de scripts
        $(document).ready(function () {
            $('#form_compra').submit(function (e) {
                let todosTienenLote = true;
                let formularioValido = true;

                // Validar que todos los productos tengan lote seleccionado
                $('.select-lote').each(function () {
                    if (!$(this).val()) {
                        todosTienenLote = false;
                        $(this).addClass('is-invalid');
                        formularioValido = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                // Validar que haya al menos un producto
                if ($('tbody tr').length <= 1) { // Considera la fila vacía
                    alertify.error('Debes agregar al menos un producto');
                    formularioValido = false;
                }

                // Validar laboratorio seleccionado
                if (!$('#id_laboratorio').val()) {
                    alertify.error('Debes seleccionar un laboratorio');
                    formularioValido = false;
                }

                if (!formularioValido) {
                    e.preventDefault();
                    if (!todosTienenLote) {
                        alertify.error('Debes seleccionar un lote para cada producto');
                    }
                    return false;
                }
            });
        });




        // En tu sección de scripts
        $(document).ready(function () {
            // Manejar cambio de selección de lote
            $(document).on('change', '.select-lote', function () {
                const productoId = $(this).data('producto-id');
                const loteId = $(this).val();
                const precio = $(this).find('option:selected').data('precio');
                const cantidad = $(this).closest('tr').find('td:nth-child(3)').text();

                // Actualizar precios
                if (loteId && precio) {
                    const subtotal = cantidad * precio;
                    $(this).closest('tr').find('td:nth-child(6)').text('Bs ' + precio.toFixed(2));
                    $(this).closest('tr').find('td:nth-child(7)').text('Bs ' + subtotal.toFixed(2));

                    // Actualizar totales
                    calcularTotales();
                }
            });

            // Función para calcular totales
            // Reemplaza tu función calcularTotales() con esta versión mejorada
            function calcularTotales() {
                let totalCompra = 0;
                let totalCantidad = 0;

                $('tbody tr').each(function () {
                    // Excluir la fila de "no hay productos"
                    if ($(this).find('td').length > 1) {
                        const cantidad = parseInt($(this).find('td:nth-child(3)').text()) || 0;
                        const precioText = $(this).find('td:nth-child(6)').text().replace('Bs ', '') || '0';
                        const precio = parseFloat(precioText.replace(',', '')) || 0;

                        totalCantidad += cantidad;
                        totalCompra += cantidad * precio;

                        // Actualizar subtotal si es necesario
                        const subtotal = cantidad * precio;
                        $(this).find('td:nth-child(7)').text('Bs ' + subtotal.toFixed(2));
                    }
                });

                // Actualizar totales en el footer
                $('tfoot th:nth-child(3)').text(totalCantidad);
                $('tfoot th:nth-child(6)').text('Bs ' + totalCompra.toFixed(2));
                $('input[name="precio_total"]').val(totalCompra.toFixed(2));

                // Habilitar/deshabilitar botón de registro
                $('button[type="submit"]').prop('disabled', totalCompra <= 0);
            }
        });


        // En el success del AJAX del modal
        // Actualiza el código del modal de lotes
        $(document).on('click', '.btn-agregar-lote', function () {
            const productoId = $(this).data('producto-id');
            const nombreProducto = $(this).data('nombre-producto');

            $('#modalProductoId').val(productoId);
            $('#nombre-producto-modal').text(nombreProducto);
            $('#nombre-producto-alert').text(nombreProducto);

            // Resetear formulario
            $('#formLote')[0].reset();
            $('#formLote input[name="fecha_ingreso"]').val(new Date().toISOString().split('T')[0]);

            // Enfocar primer campo
            setTimeout(() => {
                $('#formLote input[name="numero_lote"]').focus();
            }, 500);
        });

        // Manejar el éxito de guardar lote
        function handleLoteGuardado(response) {
            if (response.success) {
                const select = $(`.select-lote[data-producto-id="${response.producto_id}"]`);

                // Limpiar y agregar nuevas opciones
                select.empty().append('<option value="">Seleccionar lote</option>');

                // Agregar el nuevo lote como seleccionado
                select.append(`<option value="${response.lote.id}" 
                                    data-precio="${response.lote.precio_compra}" 
                                    selected>
                                    ${response.lote.numero_lote} (Bs ${response.lote.precio_compra.toFixed(2)})
                                </option>`);

                // Cerrar modal
                $('#loteModal').modal('hide');

                // Actualizar precios
                select.trigger('change');

                alertify.success('Lote guardado correctamente');
            } else {
                alertify.error(response.message || 'Error al guardar el lote');
            }
        }







        // Reemplázalo por este código mejorado:
        // En el formulario del modal (#formLote)
        $('#formLote').submit(function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const productoId = $('#modalProductoId').val();

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        // Actualizar el select de lotes
                        const select = $(`.select-lote[data-producto-id="${response.producto_id}"]`);

                        // Limpiar y agregar nuevas opciones
                        select.find('option:not(:first)').remove();

                        // Agregar el nuevo lote como seleccionado
                        select.append(`<option value="${response.lote.id}" 
                                          data-precio="${response.lote.precio_compra}" 
                                          selected>
                                          ${response.lote.text}
                                      </option>`);

                        // Cerrar modal
                        $('#loteModal').modal('hide');

                        // Actualizar precios y totales
                        select.trigger('change');

                        alertify.success(response.message);

                    }
                },
                error: function (xhr) {
                    let errorMessage = 'Error desconocido';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alertify.error(errorMessage);
                }
            });
        });



        document.addEventListener('DOMContentLoaded', function () {
            const botonesAgregarLote = document.querySelectorAll('.btn-agregar-lote');
            const inputProductoId = document.getElementById('modalProductoId');
            const inputTmpCompraId = document.getElementById('modalTmpCompraId');
            const spanNombreProducto = document.getElementById('nombre-producto-modal');
            const alertNombreProducto = document.getElementById('nombre-producto-alert');

            botonesAgregarLote.forEach(btn => {
                btn.addEventListener('click', function () {
                    const productoId = this.getAttribute('data-producto-id');
                    const tmpCompraId = this.getAttribute('data-tmp-compra-id');
                    const nombreProducto = this.getAttribute('data-nombre-producto');

                    // Llenar los inputs ocultos y los spans del modal
                    inputProductoId.value = productoId;
                    inputTmpCompraId.value = tmpCompraId;
                    spanNombreProducto.textContent = nombreProducto;
                    alertNombreProducto.textContent = nombreProducto;
                });
            });
        });
    </script>

    <script>
        doument.getElementById('btnGuardarLote').addEventListener('click', function () {
            document.getElementById('formLote').submit();
        });


    </script>



    <script>
        $(document).ready(function () {
            // [Mantener todos tus scripts originales aquí]

            // Eliminar producto temporal

            $('.delete-btn').click(function () {
                var id = $(this).data('id');
                if (id) {
                    alertify.confirm(
                        '¿Eliminar producto?',
                        'Esta acción no se puede deshacer',
                        function () { // Confirmar
                            $.ajax({
                                url: "{{ url('/admin/compras/create/tmp') }}/" + id,
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    _method: 'DELETE'
                                },
                                success: function (response) {
                                    if (response.success) {

                                        location.reload();

                                    } else {
                                        alertify.error('No se pudo eliminar el producto')
                                            .set('onclose', function () {
                                                // opcional: recargar o no
                                            });
                                    }
                                },
                                error: function (xhr) {
                                    let response = xhr.responseJSON;
                                    let errorMessage = 'Error desconocido';

                                    if (response) {
                                        if (response.error) {
                                            errorMessage = response.error;
                                        } else {
                                            errorMessage = JSON.stringify(response, null, 4);
                                        }
                                    }

                                    alertify.alert('Error al eliminar', '<pre style="white-space: pre-wrap;">' + errorMessage + '</pre>');
                                }
                            });
                        },
                        function () { // Cancelar
                            alertify.message('Operación cancelada');
                        }
                    ).set('labels', { ok: 'Sí, eliminar', cancel: 'Cancelar' });
                }
            });




            // Búsqueda por código (Enter)
            $('#codigo').on('keypress', function (e) {
                if (e.which === 13) {
                    e.preventDefault();
                    var codigo = $(this).val();
                    var cantidad = $('#cantidad').val();

                    if (codigo.length > 0) {
                        $.ajax({
                            url: "{{ route('admin.compras.tmp_compras')}}",
                            method: 'POST',
                            data: {
                                _token: '{{csrf_token()}}',
                                codigo: codigo,
                                cantidad: cantidad
                            },
                            success: function (response) {
                                if (response.success) {

                                    location.reload();

                                } else {
                                    alertify.error('No se encontró el producto')

                                }
                            },
                            error: function (error) {
                                alertify.error('Ocurrió un error al procesar la solicitud')

                            }
                        });
                    }
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

            // Enfocar campo código al cargar
            $('#codigo').focus();
        });
    </script>



    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <!-- Card Principal - Diseño Mejorado -->
                <div class="card shadow-lg border-0 rounded-lg" style="height: auto; min-height: 0;">
                    <div class="card-header bg-white border-bottom py-3"> <!-- Reduje el padding vertical -->
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h class="mb-0 text-dark font-weight-bold" style="font-size: 1.1rem;">Registrar
                                            Nueva Compra</h4>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('admin.compras.index') }}" class="btn btn-outline-dark btn-sm py-1">
                                        <!-- Reduje padding del botón -->
                                        <i class="fas fa-list me-1"></i> Ver Historial
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card mt-3">
                    <div class="card-body">

                        <form action="{{ route('admin.compras.store') }}" id="form_compra" method="POST" autocomplete="off">
                            @csrf


                            <div class="row g-4">

                                <!-- Sección de Productos -->

                                <div class="col-lg-8">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-white border-bottom">
                                            <h6 class="mb-0 text-dark font-weight-bold">
                                                <i class="fas fa-boxes text-primary me-2"></i>Productos
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3 align-items-end mb-4">
                                                <div class="col-md-2">
                                                    <label for="cantidad"
                                                        class="form-label fw-semibold small text-muted">Cantidad</label>
                                                    <input type="number" class="form-control border-primary border-2"
                                                        id="cantidad" name="cantidad" value="1" min="1" required>
                                                    @error('cantidad')
                                                        <div class="invalid-feedback d-block small">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-5">
                                                    <label for="codigo"
                                                        class="form-label fw-semibold small text-muted">Código de
                                                        Producto</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-white border-primary border-end-0">
                                                            <i class="fas fa-barcode text-primary"></i>
                                                        </span>
                                                        <input id="codigo" type="text"
                                                            class="form-control border-primary border-start-0" name="codigo"
                                                            placeholder="Ingresar código" autofocus>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="d-flex justify-content-end gap-2 pt-3">
                                                        <button type="button" class="btn btn-primary flex-grow-1"
                                                            data-bs-toggle="modal" data-bs-target="#productosModal">
                                                            <i class="fas fa-search me-2"></i>
                                                        </button>
                                                        <a href="{{ route('admin.productos.create') }}"
                                                            class="btn btn-success flex-grow-1">
                                                            <i class="fas fa-plus me-2"></i>
                                                        </a>
                                                    </div>
                                                </div>



                                            </div>

                                            <!-- Tabla de productos seleccionados -->
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless table-hover mb-0"
                                                    style="font-size: 0.85rem;">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th class="text-center px-1" style="width: 3%;">#</th>
                                                            <th class="text-center px-1" style="width: 10%;">Código</th>
                                                            <th class="text-center px-1" style="width: 5%;">Cant.</th>
                                                            <th class="px-1" style="width: 40%;">Nombre</th>
                                                            <th class="px-1" style="width: 40%;">Lote</th>
                                                            <th class="text-end px-1" style="width: 12%;">Unit.</th>
                                                            <th class="text-end px-1" style="width: 15%;">Subtotal</th>
                                                            <th class="text-center px-1" style="width: 5%;"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $cont = 1;
                                                            $total_cantidad = 0;
                                                            $total_compra = 0;
                                                           @endphp

                                                        @forelse($tmp_compras as $tmp_compra)
                                                            <tr>
                                                                <td class="text-center">{{ $cont++ }}</td>
                                                                <td class="text-center"><span
                                                                        class="badge bg-light text-dark border">{{ $tmp_compra->producto->codigo }}</span>
                                                                </td>
                                                                <td class="text-center">{{ $tmp_compra->cantidad }}</td>
                                                                <td class="text-truncate" style="max-width: 200px;"
                                                                    title="{{ $tmp_compra->producto->nombre }}">
                                                                    {{ $tmp_compra->producto->nombre }}</td>
                                                                <!--aqui se visularia el lote de la compra -->
                                                                <td>
                                                                    <select class="form-select form-select-sm select-lote"
                                                                        name="lotes[{{ $tmp_compra->producto_id }}]"
                                                                        data-producto-id="{{ $tmp_compra->producto_id }}"
                                                                        required>
                                                                        <option value="">Ver lote</option>
                                                                        @foreach($tmp_compra->producto->lotes as $lote)
                                                                            <option value="{{ $lote->id }}"
                                                                                data-precio="{{ $lote->precio_compra }}" {{ $lote->id == optional($lotesPorProducto[$tmp_compra->producto_id]->first())->id ? 'selected' : '' }}>
                                                                                {{ $lote->numero_lote }} (Bs
                                                                                {{ number_format($lote->precio_compra, 2) }})
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-primary py-0 px-2 mt-1 btn-agregar-lote"
                                                                        data-bs-toggle="modal" data-bs-target="#loteModal"
                                                                        data-producto-id="{{ $tmp_compra->producto->id }}"
                                                                        data-nombre-producto="{{ $tmp_compra->producto->nombre }}">
                                                                        <i class="fas fa-plus-circle me-1"></i> Nuevo Lote
                                                                    </button>
                                                                </td>

                                                                @php
                                                                    $lote = isset($lotesPorProducto[$tmp_compra->producto_id])
                                                                        ? $lotesPorProducto[$tmp_compra->producto_id]->first()
                                                                        : null;

                                                                    $precio = $lote ? $lote->precio_compra : 0;
                                                                    $costo = $tmp_compra->cantidad * $precio;
                                                                   @endphp

                                                                <td class="text-end">
                                                                    @if($lote)
                                                                        Bs {{ number_format($precio, 2, '.', '') }}
                                                                    @else
                                                                        <span class="text-muted">Pendiente</span>
                                                                    @endif
                                                                </td>

                                                                <td class="text-end fw-semibold">
                                                                    Bs {{ number_format($costo, 2, '.', '') }}
                                                                </td>


                                                                <td class="text-center">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-danger border-0 py-0 px-2 delete-btn"
                                                                        data-id="{{ $tmp_compra->id }}" title="Eliminar">
                                                                        <i class="fas fa-trash-alt"
                                                                            style="font-size: 0.75rem;"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            @php
                                                                $total_cantidad += $tmp_compra->cantidad;
                                                                $total_compra += $costo;
                                                               @endphp
                                                        @empty
                                                            <tr>
                                                                <td colspan="7" class="text-center py-3 text-muted small">
                                                                    <i class="fas fa-info-circle me-2"></i>No hay productos
                                                                    agregados
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                    <tfoot class="bg-light">
                                                        <tr>
                                                            <th colspan="2" class="text-end small">Totales:</th>
                                                            <th class="text-center small">{{ $total_cantidad }}</th>
                                                            <th colspan="2" class="text-end small">Total:</th>
                                                            <th class="text-end text-success fw-bold">Bs
                                                                {{ number_format($total_compra, 2, '.', '') }}</th>
                                                            <th></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Sección de Información de Compra -->

                                @include('admin.compras.seccion_laboratorios')
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('admin.compras.selector_productos')

    <!-- Modal para creación de lote -->
    <div class="modal fade" id="loteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-boxes me-2"></i>Registro de Lote para: <span id="nombre-producto-modal"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formLote" method="POST" action="{{ route('compras.agregarLote') }}">

                        @csrf
                        <input type="hidden" name="producto_id" id="modalProductoId" value="">



                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Número de Lote*</label>
                                <input type="text" class="form-control" name="numero_lote" required
                                    placeholder="Ej: LT-2023-001" pattern="[A-Za-z0-9-]+"
                                    title="Solo letras, números y guiones">
                                <small class="text-muted">Código único para identificar este lote</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Cantidad*</label>
                                <input type="number" class="form-control" name="cantidad" min="1" required
                                    placeholder="Ej: 100" id="cantidadInput">
                                <div class="form-text">Stock inicial de este lote</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Fecha de Ingreso*</label>
                                <input type="date" class="form-control" name="fecha_ingreso" required
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Fecha de Vencimiento*</label>
                                <input type="date" class="form-control" name="fecha_vencimiento" required
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}">

                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Precio de Compra (Bs)*</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" step="0.01" class="form-control" name="precio_compra"
                                        placeholder="0.00" required min="0" id="precioCompraInput">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Precio de Venta (Bs)*</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" step="0.01" class="form-control" name="precio_venta"
                                        placeholder="0.00" required min="0" id="precioVentaInput">
                                </div>
                                <div class="form-text" id="gananciaText"></div>
                            </div>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarLote">
                        <i class="fas fa-save me-2"></i> Guardar Lote
                    </button>
                    </form>
                </div>
            </div>
        </div>
    </div>





@endsection

@section('css')
    <style>
        /* Estilos personalizados para mejorar la apariencia */
        .card {
            border-radius: 0.5rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
        }

        .form-control,
        .form-select {
            border-radius: 0.375rem;
            transition: all 0.3s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #5e72e4;
            box-shadow: 0 0 0 0.2rem rgba(94, 114, 228, 0.25);
        }

        .table {
            font-size: 0.875rem;
        }

        .table thead th {
            border-bottom-width: 1px;
        }

        .btn {
            border-radius: 0.375rem;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: #5e72e4;
            border-color: #5e72e4;
        }

        .btn-primary:hover {
            background-color: #4a5fd1;
            border-color: #4a5fd1;
        }

        .icon-shape {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endsection
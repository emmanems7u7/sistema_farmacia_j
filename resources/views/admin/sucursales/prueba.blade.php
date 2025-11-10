@extends('layouts.argon')

@section('content')

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    let stockMaximoProducto = 0;

    $(document).ready(function () {
        // === CONFIGURACIÓN INICIAL ===
        $('#codigo').focus();

        // === AGREGAR PRODUCTOS CON ENTER ===
        $('#codigo, #cantidad').on('keypress', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                agregarProductoDesdeCodigo();
            }
        });

        // === BOTÓN AGREGAR PRODUCTO ===
        $('#btn-agregar-producto').click(function() {
            agregarProductoDesdeCodigo();
        });

        // === VALIDACIÓN FORMULARIO PRINCIPAL CORREGIDA ===
        // === VALIDACIÓN FORMULARIO PRINCIPAL CORREGIDA ===
$('#form_compra').submit(function (e) {
    let formularioValido = true;
    let productosSinLote = 0;

    $('tbody tr').each(function () {
        if ($(this).find('td').length > 1) {
            // VERIFICAR DE DOS FORMAS:
            // 1. Si el botón "Crear Lote" está oculto (ya se creó lote)
            // 2. O si existe el mensaje "Lote asignado"
            const botonLote = $(this).find('.btn-agregar-lote');
            const mensajeLoteAsignado = $(this).find('.text-success'); // Buscar el mensaje que agregamos
            
            const tieneLote = botonLote.is(':hidden') || mensajeLoteAsignado.length > 0;
            
            if (!tieneLote) {
                productosSinLote++;
                formularioValido = false;
                // Resaltar la fila que falta lote
                $(this).addClass('table-danger');
            } else {
                $(this).removeClass('table-danger');
            }
        }
    });

    if ($('tbody tr').length <= 1) {
        alertify.error('Debes agregar al menos un producto');
        formularioValido = false;
    }

    if (!$('#laboratorio_id').val()) {
        alertify.error('Debes seleccionar un laboratorio');
        formularioValido = false;
    }

    if (productosSinLote > 0) {
        alertify.error('Tienes ' + productosSinLote + ' producto(s) sin lote asignado');
        formularioValido = false;
    }

    if (!formularioValido) {
        e.preventDefault();
        return false;
    }
});

        
        // === ELIMINAR PRODUCTOS ===
        $(document).on('click', '.delete-btn', function () {
            var id = $(this).data('id');
            if (id) {
                alertify.confirm(
                    '¿Eliminar producto?',
                    'Esta acción no se puede deshacer',
                    function () {
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
                                    alertify.error('No se pudo eliminar el producto');
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
                    function () {
                        alertify.message('Operación cancelada');
                    }
                ).set('labels', { ok: 'Sí, eliminar', cancel: 'Cancelar' });
            }
        });

        // === CONFIGURACIÓN DATATABLES ===
        $('#mitabla, #mitabla2, #mitabla1').DataTable({
            pageLength: 5,
            lengthMenu: [5, 10, 25, 50],
            responsive: true,
            autoWidth: false,
            dom: '<"d-flex justify-content-between mb-3"lf>t<"d-flex justify-content-between mt-3"ip>',
            language: {
                lengthMenu: "Mostrar _MENU_ registros por página",
                zeroRecords: "No se encontraron resultados",
                info: "Mostrando página _PAGE_ de _PAGES_",
                infoEmpty: "No hay registros disponibles",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                search: "🔍 Buscar:",
                paginate: {
                    first: "<i class='bi bi-chevron-bar-left'></i>",
                    last: "<i class='bi bi-chevron-bar-right'></i>",
                    next: "<i class='bi bi-chevron-right'></i>",
                    previous: "<i class='bi bi-chevron-left'></i>"
                }
            }
        });

        // === VALIDACIÓN EN TIEMPO REAL CANTIDAD ===
        $('#cantidad').on('input', function() {
            var cantidad = $(this).val();
            if (cantidad <= 0 || cantidad === "") {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
    });

    // === FUNCIÓN PARA AGREGAR PRODUCTO DESDE CÓDIGO ===
    function agregarProductoDesdeCodigo() {
        var codigo = $('#codigo').val();
        var cantidad = $('#cantidad').val();

        // Validar que la cantidad sea mayor a 0
        if (cantidad <= 0 || cantidad === "") {
            alertify.error('La cantidad de empaques debe ser mayor a 0');
            $('#cantidad').focus().select();
            return false;
        }

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
                        alertify.error('No se encontró el producto');
                    }
                },
                error: function (error) {
                    alertify.error('Ocurrió un error al procesar la solicitud');
                    console.error('Error:', error);
                }
            });
        } else {
            alertify.error('Por favor ingrese un código de producto');
            return false;
        }
    }

    // === FUNCIÓN PARA CALCULAR TOTALES ===
    function calcularTotales() {
        let totalCompra = 0;
        let totalEmpaques = 0;

        $('tbody tr').each(function () {
            if ($(this).find('td').length > 1) {
                const subtotalText = $(this).find('.subtotal').text().replace('Bs ', '') || '0';
                const subtotal = parseFloat(subtotalText.replace(',', '')) || 0;
                
                const cantidadEmpaques = parseInt($(this).find('td:nth-child(3)').text()) || 0;

                totalEmpaques += cantidadEmpaques;
                totalCompra += subtotal;
            }
        });

        $('tfoot th:nth-child(3)').text(totalEmpaques);
        $('tfoot .total-compra').text('Bs ' + totalCompra.toFixed(2));
        $('input[name="precio_total"]').val(totalCompra.toFixed(2));

        $('button[type="submit"]').prop('disabled', totalCompra <= 0);
    }

    // === FUNCIÓN PARA ABRIR MODAL DE LOTE ===
    function abrirModalLoteAutomatico(productoId, productoNombre, stockMaximo, tmpCompraId) {
        $('#modalProductoId').val(productoId);
        $('#modalTmpCompraId').val(tmpCompraId);
        $('#nombre-producto-modal').text(productoNombre);
        
        stockMaximoProducto = stockMaximo;
        $('#stock-maximo-text').text('Stock máximo: ' + stockMaximo + ' unidades');
        $('#cantidadInput').attr('max', stockMaximo);

        $('#formLote')[0].reset();
        
        $('#formLote input[name="fecha_ingreso"]').val(new Date().toISOString().split('T')[0]);
        
        const fechaVencimiento = new Date();
        fechaVencimiento.setFullYear(fechaVencimiento.getFullYear() + 1);
        $('#formLote input[name="fecha_vencimiento"]').val(fechaVencimiento.toISOString().split('T')[0]);

        $('#loteModal').modal('show');
        
        setTimeout(() => {
            $('#formLote input[name="precio_compra"]').focus();
        }, 500);
    }

    // === EVENTO CLICK PARA BOTÓN AGREGAR LOTE ===
    $(document).on('click', '.btn-agregar-lote', function () {
        const productoId = $(this).data('producto-id');
        const nombreProducto = $(this).data('nombre-producto');
        const stockMaximo = $(this).data('stock-maximo') || 50;
        const tmpCompraId = $(this).data('tmp-compra-id');

        abrirModalLoteAutomatico(productoId, nombreProducto, stockMaximo, tmpCompraId);
    });
    

    // === MANEJADOR FORMULARIO DE LOTE CORREGIDO ===
   // === MANEJADOR FORMULARIO DE LOTE MEJORADO ===
$(document).on('submit', '#formLote', function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const productoId = $('#modalProductoId').val();
    const tmpCompraId = $('#modalTmpCompraId').val();
    const cantidad = $('#cantidadInput').val();
    const stockMaximo = $('#cantidadInput').attr('max') || 50;

    if (parseInt(cantidad) > parseInt(stockMaximo)) {
        Swal.fire({
            icon: 'error',
            title: 'Stock máximo excedido',
            text: 'La cantidad no puede ser mayor a ' + stockMaximo + ' unidades.',
            confirmButtonText: 'Entendido'
        });
        return false;
    }

    // Mostrar loading en el botón
    const btnGuardar = $('#btnGuardarLote');
    btnGuardar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');

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
            console.log('Respuesta del servidor:', response);
            
            if (response.success) {
                const botonLote = $(`.btn-agregar-lote[data-tmp-compra-id="${tmpCompraId}"]`);
                
                // Ocultar botón y mostrar información del lote
                botonLote.hide();
                
                // AGREGAR UNA CLASE ESPECÍFICA PARA IDENTIFICAR QUE TIENE LOTE
                botonLote.closest('tr').addClass('lote-asignado');
                
                // Mostrar información del lote creado
                botonLote.after(`
                    <div class="mt-1 lote-info">
                        <small class="text-success">
                            <i class="fas fa-check-circle me-1"></i>
                            Lote: ${response.numero_lote}
                        </small>
                        <br>
                        <small class="text-muted">
                            ${$('#cantidadInput').val()} und - Bs ${parseFloat(response.precio_compra_unitario).toFixed(2)}
                        </small>
                    </div>
                `);

                $('#loteModal').modal('hide');

                const cantidadLote = $('#cantidadInput').val();
                const subtotal = cantidadLote * response.precio_compra_unitario;
                
                // Actualizar precios en la tabla
                botonLote.closest('tr').find('.precio-unitario').text('Bs ' + parseFloat(response.precio_compra_unitario).toFixed(2));
                botonLote.closest('tr').find('.subtotal').text('Bs ' + subtotal.toFixed(2));

                calcularTotales();

                alertify.success(response.message);

            } else {
                alertify.error(response.message || 'Error al crear el lote');
            }
        },
        error: function (xhr) {
            console.error('Error en AJAX:', xhr);
            let errorMessage = 'Error desconocido';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                errorMessage = xhr.responseText;
            }
            alertify.error(errorMessage);
        },
        complete: function() {
            // Rehabilitar botón
            btnGuardar.prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Lote');
        }
    });
});
    // === CÁLCULOS AUTOMÁTICOS MODAL LOTE ===
    document.addEventListener("DOMContentLoaded", function () {
        const cantidadInput = document.getElementById("cantidadInput");
        const precioCompraInput = document.getElementById("precioCompraInput");
        const precioUnitarioInput = document.getElementById("precioCompraUnitarioInput");
        const precioVentaInput = document.getElementById("precioVentaInput");
        const gananciaText = document.getElementById("gananciaText");

        function calcularUnitario() {
            let cantidad = parseFloat(cantidadInput.value) || 0;
            let precioCompra = parseFloat(precioCompraInput.value) || 0;

            if (cantidad > 0 && precioCompra > 0) {
                let precioUnitario = (precioCompra / cantidad).toFixed(2);
                precioUnitarioInput.value = precioUnitario;
            } else {
                precioUnitarioInput.value = "";
            }
        }

        function calcularGanancia() {
            let costoUnitario = parseFloat(precioUnitarioInput.value) || 0;
            let precioVenta = parseFloat(precioVentaInput.value) || 0;

            if (costoUnitario > 0 && precioVenta > 0) {
                let ganancia = (precioVenta - costoUnitario).toFixed(2);
                let porcentaje = ((ganancia / costoUnitario) * 100).toFixed(2);
                gananciaText.innerHTML = `Ganancia: Bs ${ganancia} (${porcentaje}%)`;
            } else {
                gananciaText.innerHTML = "";
            }
        }

        if (cantidadInput) cantidadInput.addEventListener("input", calcularUnitario);
        if (precioCompraInput) precioCompraInput.addEventListener("input", () => {
            calcularUnitario();
            calcularGanancia();
        });
        if (precioVentaInput) precioVentaInput.addEventListener("input", calcularGanancia);
    });
</script>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0 rounded-lg" style="height: auto; min-height: 0;">
                <div class="card-header bg-white border-bottom py-3"> 
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div>
                                <h4 class="mb-0 text-dark font-weight-bold" style="font-size: 1.1rem;">Registrar Nueva Compra</h4>
                            </div>
                        </div>
                        <div>
                           <a href="{{ route('admin.compras.index') }}" class="btn btn-outline-dark btn-sm py-1">
                                <i class="fas fa-list me-1"></i> Ver Historial
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

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
                                                class="form-label fw-semibold small text-muted">
                                                Cantidad de empaques
                                                <i class="fas fa-question-circle text-primary" 
                                                title="Número de cajas, bolsas, tarros, etc."></i>
                                            </label>
                                            <input type="number" 
                                                class="form-control border-primary border-2" 
                                                id="cantidad" 
                                                name="cantidad" 
                                                value="1" 
                                                min="1" 
                                                placeholder="Ej: 2"
                                                required>
                                            @error('cantidad')
                                                <div class="invalid-feedback d-block small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-5">
                                            <label for="codigo"
                                                class="form-label fw-semibold small text-muted">Código de Producto</label>
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
                                                    <i class="fas fa-search me-2"></i> Buscar
                                                </button>
                                                <button type="button" class="btn btn-success flex-grow-1" id="btn-agregar-producto">
                                                    <i class="fas fa-plus me-2"></i> Agregar
                                                </button>
                                                <a href="{{ route('admin.productos.create') }}"
                                                    class="btn btn-info flex-grow-1">
                                                    <i class="fas fa-plus me-2"></i> Nuevo
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
                                                    <th class="px-1" style="width: 35%;">Nombre</th>
                                                    <th class="px-1" style="width: 35%;">Lote</th>
                                                    <th class="text-end px-1" style="width: 12%;">Unit.</th>
                                                    <th class="text-end px-1" style="width: 15%;">Subtotal</th>
                                                    <th class="text-center px-1" style="width: 5%;"></th>
                                                </tr>
                                            </thead>
                                           
                                            <tbody>
                                                @php
                                                    $cont = 1;
                                                    $total_empaques = 0;
                                                    $total_compra = 0;
                                                @endphp

                                                @forelse($tmp_compras as $tmp_compra)
                                                    <tr>
                                                        <td class="text-center">{{ $cont++ }}</td>
                                                        <td class="text-center"><span class="badge bg-light text-dark border">{{ $tmp_compra->producto->codigo }}</span></td>
                                                        <td class="text-center">{{ $tmp_compra->cantidad }}</td>
                                                        <td class="text-truncate" style="max-width: 200px;" title="{{ $tmp_compra->producto->nombre }}">
                                                            {{ $tmp_compra->producto->nombre }}
                                                        </td>
                                                       <td>
    {{-- SOLO BOTÓN PARA CREAR LOTE --}}
    @if(is_null($tmp_compra->lote_id))
        <button type="button"
            class="btn btn-sm btn-outline-primary py-0 px-2 mt-1 btn-agregar-lote"
            data-bs-toggle="modal" 
            data-bs-target="#loteModal"
            data-producto-id="{{ $tmp_compra->producto->id }}"
            data-nombre-producto="{{ $tmp_compra->producto->nombre }}"
            data-stock-maximo="{{ $tmp_compra->producto->stock_maximo ?? 50 }}"
            data-tmp-compra-id="{{ $tmp_compra->id }}">
            <i class="fas fa-plus-circle me-1"></i> Crear Lote
        </button>
    @else
        <small class="text-success mt-1 d-block">
            <i class="fas fa-check-circle me-1"></i>Lote asignado
        </small>
    @endif
</td>

                                                        @php
                                                            $lote = $tmp_compra->lote;
                                                            if ($lote) {
                                                                $precio_unitario = $lote->precio_compra_unitario;
                                                                $cantidad_lote = $lote->cantidad;
                                                                $subtotal = $cantidad_lote * $precio_unitario;
                                                            } else {
                                                                $precio_unitario = 0;
                                                                $cantidad_lote = 0;
                                                                $subtotal = 0;
                                                            }
                                                        @endphp

                                                        <td class="text-end precio-unitario">
                                                            @if($lote)
                                                                Bs {{ number_format($precio_unitario, 2, '.', '') }}
                                                            @else
                                                                <span class="text-muted">Pendiente</span>
                                                            @endif
                                                        </td>

                                                        <td class="text-end fw-semibold subtotal">
                                                            @if($lote)
                                                                Bs {{ number_format($subtotal, 2, '.', '') }}
                                                            @else
                                                                <span class="text-muted">Pendiente</span>
                                                            @endif
                                                        </td>

                                                        <td class="text-center">
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger border-0 py-0 px-2 delete-btn"
                                                                data-id="{{ $tmp_compra->id }}" title="Eliminar">
                                                                <i class="fas fa-trash-alt" style="font-size: 0.75rem;"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @php
                                                        $total_empaques += $tmp_compra->cantidad;
                                                        $total_compra += $subtotal;
                                                    @endphp
                                                @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center py-3 text-muted small">
                                                            <i class="fas fa-info-circle me-2"></i>No hay productos agregados
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot class="bg-light">
                                                <tr>
                                                    <th colspan="2" class="text-end small">Total Empaques:</th>
                                                    <th class="text-center small">{{ $total_empaques }}</th>
                                                    <th colspan="2" class="text-end small">Total Compra:</th>
                                                    <th class="text-end text-success fw-bold total-compra">Bs {{ number_format($total_compra, 2, '.', '') }}</th>
                                                    <th colspan="2"></th>
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

@include('admin.compras.selector_productos')

<!-- Modal para creación de lote -->
<div class="modal fade" id="loteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">
                    <i class="fas fa-boxes me-2"></i>Registro de Lote para: 
                    <span id="nombre-producto-modal"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formLote" method="POST" action="{{ route('compras.agregarLote') }}">
                    @csrf
                    <input type="hidden" name="producto_id" id="modalProductoId" value="">
                    <input type="hidden" name="tmp_compra_id" id="modalTmpCompraId" value="">

                    <div class="row g-3">
                         <div class="col-md-6">
                            <label class="form-label fw-bold">Número de Lote*</label>
                            <input type="text" class="form-control" name="numero_lote" required
                                placeholder="Ej: LT-2023-001" pattern="[A-Za-z0-9-]+"
                                title="Solo letras, números y guiones">
                            <small class="text-muted">Código único para identificar este lote</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cantidad (Unidades)*</label>
                            <input type="number" class="form-control" name="cantidad" min="1" required
                                placeholder="Ej: 50" id="cantidadInput">
                            <div class="form-text" id="stock-maximo-text">Stock máximo: 0 unidades</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha de Ingreso*</label>
                            <input type="date" class="form-control" name="fecha_ingreso" required
                                min="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha de Vencimiento*</label>
                            <input type="date" class="form-control" name="fecha_vencimiento" 
                                  min="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Precio de Compra (Bs)*</label>
                            <div class="input-group">
                                <span class="input-group-text">Bs</span>
                                <input type="number" step="0.01" class="form-control" 
                                    name="precio_compra" placeholder="0.00" required min="0" 
                                    id="precioCompraInput">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Precio de Compra Unitario (Bs)</label>
                            <div class="input-group">
                                <span class="input-group-text">Bs</span>
                                <input type="number" step="0.01" class="form-control" 
                                    name="precio_compra_unitario" id="precioCompraUnitarioInput" readonly>
                            </div>
                            <small class="text-muted">Se calcula automáticamente</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Precio de Venta Unitario (Bs)*</label>
                            <div class="input-group">
                                <span class="input-group-text">Bs</span>
                                <input type="number" step="0.01" class="form-control" 
                                    name="precio_venta" placeholder="0.00" required min="0" 
                                    id="precioVentaInput">
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






























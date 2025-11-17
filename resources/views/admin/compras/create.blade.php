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
        
       
        setTimeout(calcularTotales, 100);

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

        // === VALIDACIÓN FORMULARIO PRINCIPAL ===
        $('#form_compra').submit(function (e) {
            let formularioValido = true;
            let productosSinLote = 0;

            // Verificar cada fila de producto
            $('tbody tr').each(function () {
                if ($(this).find('td').length > 1) {
                    const textoColumnaLote = $(this).find('td:nth-child(5)').text();
                    const tieneLoteAsignado = textoColumnaLote.includes('Lote asignado') || 
                                             textoColumnaLote.includes('Lote:');
                    
                    const botonLoteVisible = $(this).find('.btn-agregar-lote').is(':visible');
                    
                    if (!tieneLoteAsignado && botonLoteVisible) {
                        productosSinLote++;
                        formularioValido = false;
                    }
                }
            });

            // Otras validaciones
            if ($('tbody tr').length <= 1) {
                alertify.error('Debes agregar al menos un producto');
                formularioValido = false;
            }

            if (!$('#laboratorio_id').val()) {
                alertify.error('Debes seleccionar un laboratorio');
                formularioValido = false;
            }

            if (!formularioValido) {
                e.preventDefault();
                if (productosSinLote > 0) {
                    alertify.error('Hay ' + productosSinLote + ' producto(s) sin lote asignado');
                }
                return false;
            }

            console.log('Formulario válido, enviando compra...');
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

 // === EVENTO PARA ACTUALIZAR BOTÓN CUANDO SE SELECCIONA LABORATORIO 
    $(document).on('click', '.seleccionar-btn-laboratorio', function () {
        var idLab = $(this).data('id');
        var nombreLab = $(this).data('nombre');
        $('#laboratorio_id').val(idLab);
        $('#nombre_laboratorio').val(nombreLab);
        
        // Actualizar botón después de seleccionar laboratorio
        setTimeout(actualizarBotonRegistrar, 100);
        
        const laboratorioModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('laboratoriosModal'));
        laboratorioModal.hide();
    });

    // También agregar este evento para cuando se cierra el modal de lote
    $('#loteModal').on('hidden.bs.modal', function () {
        setTimeout(function() {
            calcularTotales();
            actualizarBotonRegistrar();
        }, 300);
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
    
  Swal
   // === FUNCIÓN MEJORADA PARA CALCULAR TOTALES 
    function calcularTotales() {
        console.log('Calculando totales...');
        
        let totalCompra = 0;
        let totalEmpaques = 0;

        $('tbody tr').each(function () {
            if ($(this).find('td').length > 1 && !$(this).find('td').text().includes('No hay productos')) {
                
                const cantidadEmpaquesText = $(this).find('td:nth-child(3)').text().trim();
                const cantidadEmpaques = parseInt(cantidadEmpaquesText) || 0;
                totalEmpaques += cantidadEmpaques;
                
                const subtotalElement = $(this).find('.subtotal');
                const subtotalText = subtotalElement.text().trim();
                
                console.log('Subtotal text:', subtotalText);

                if (subtotalText && !subtotalText.includes('Pendiente')) {
                    const subtotalMatch = subtotalText.match(/[\d,]+\.?\d*/);
                    if (subtotalMatch) {
                        const subtotalValue = parseFloat(subtotalMatch[0].replace(',', '')) || 0;
                        totalCompra += subtotalValue;
                        console.log('Subtotal encontrado:', subtotalValue);
                    }
                }
            }
        });

        console.log('Total empaques:', totalEmpaques);
        console.log('Total compra:', totalCompra);

       
        $('tfoot th:nth-child(3)').text(totalEmpaques);
        $('tfoot .total-compra').text('Bs ' + totalCompra.toFixed(2));
       
        $('input[name="precio_total"]').val(totalCompra.toFixed(2));
        
        
        actualizarBotonRegistrar();
    }

   
   //  HABILITAR/DESHABILITAR BOTÓN REGISTRAR 
    function actualizarBotonRegistrar() {
        const totalCompra = parseFloat($('input[name="precio_total"]').val()) || 0;
        const tieneLaboratorio = $('#laboratorio_id').val() !== '';
        const tieneProductos = $('tbody tr').length > 1 && !$('tbody tr td').text().includes('No hay productos');
        
        const botonRegistrar = $('#btn-registrar-compra');
        
        console.log('Actualizando botón - Total:', totalCompra, 'Lab:', tieneLaboratorio, 'Productos:', tieneProductos);
        
        if (totalCompra > 0 && tieneLaboratorio && tieneProductos) {
            botonRegistrar.prop('disabled', false);
            console.log('Botón HABILITADO');
        } else {
            botonRegistrar.prop('disabled', true);
            console.log('Botón DESHABILITADO');
        }
    }
    // === FUNCIÓN PARA ABRIR MODAL DE LOTE 
    
function abrirModalLoteAutomatico(productoId, productoNombre, stockActual, stockMaximo, disponible, tmpCompraId) {
    $('#modalProductoId').val(productoId);
    $('#modalTmpCompraId').val(tmpCompraId);
    $('#nombre-producto-modal').text(productoNombre);
    
    // MOSTRAR LA INFORMACIÓN DE STOCK
    $('#stock-maximo-text').html(`
        <div class="small">
            <div>Stock actual: <strong>${stockActual} unidades</strong></div>
            <div>Stock máximo: <strong>${stockMaximo} unidades</strong></div>
            <div class="${disponible <= 0 ? 'text-danger' : 'text-success'}">
                Puedes comprar: <strong>${disponible} unidades</strong>
            </div>
        </div>
    `);

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
    // === EVENTO CLICK PARA BOTÓN AGREGAR LOTE 
$(document).on('click', '.btn-agregar-lote', function () {
    const productoId = $(this).data('producto-id');
    const nombreProducto = $(this).data('nombre-producto');
    const stockActual = $(this).data('stock-actual') || 0;
    const stockMaximo = $(this).data('stock-maximo') || 0;
    const disponible = $(this).data('disponible') || 0;
    const tmpCompraId = $(this).data('tmp-compra-id');

    abrirModalLoteAutomatico(productoId, nombreProducto, stockActual, stockMaximo, disponible, tmpCompraId);
});

    // === MANEJADOR FORMULARIO DE LOTE 
$(document).on('submit', '#formLote', function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const productoId = $('#modalProductoId').val();
    const tmpCompraId = $('#modalTmpCompraId').val();
    const cantidad = $('#cantidadInput').val();
    const stockMaximo = $('#cantidadInput').attr('max') || 50;

    
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
        const filaProducto = botonLote.closest('tr');
        
        // Ocultar botón y mostrar información del lote
        botonLote.hide();
        
        // USAR LOS VALORES DEL RESPONSE
        const cantidadLote = parseFloat(response.cantidad_lote) || 0;
        const precioUnitario = parseFloat(response.precio_compra_unitario) || 0;
        const subtotal = cantidadLote * precioUnitario;
        const loteId = response.lote_id;

        console.log('Valores para actualizar:', {
            cantidadLote,
            precioUnitario,
            subtotal,
            loteId
        });

        // ACTUALIZAR LA COLUMNA DE LOTE
        const columnaLote = filaProducto.find('td:nth-child(5)');
        columnaLote.html(`
            <small class="text-success mt-1 d-block">
                <i class="fas fa-check-circle me-1"></i>Lote asignado
            </small>
        `);

        // ACTUALIZAR LA COLUMNA DE BOTONES 
        const columnaBotones = filaProducto.find('td:nth-child(8)');
        columnaBotones.html(`
            <button type="button"
                class="btn btn-sm btn-outline-info border-0 py-0 px-2 btn-ver-lote"
                data-lote-id="${loteId}" 
                title="Ver detalles del lote">
                <i class="fas fa-eye" style="font-size: 0.75rem;"></i>
            </button>
            <button type="button"
                class="btn btn-sm btn-outline-danger border-0 py-0 px-2 delete-btn"
                data-id="${tmpCompraId}" title="Eliminar producto"
                style="font-size: 0.7rem;">
                <i class="fas fa-trash-alt"></i>
            </button>
        `);

     
        filaProducto.find('.precio-unitario').html('Bs ' + precioUnitario.toFixed(2));
        filaProducto.find('.subtotal').html('Bs ' + subtotal.toFixed(2));

        // Cerrar modal
        $('#loteModal').modal('hide');

     
        setTimeout(function() {
            calcularTotales();
        }, 300);

        alertify.success(response.message);

    } else {
        alertify.error(response.message || 'Error al crear el lote');
    }
},
        error: function (xhr) {  
            if (xhr.status === 422) {
                
                let response = xhr.responseJSON;
                Swal.fire({
                    icon: 'error',
                    title: 'Stock Excedido',
                    html: response.message,
                    confirmButtonText: 'Entendido'
                });
            } else {
               
                let errorMessage = 'Error desconocido';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    errorMessage = xhr.responseText;
                }
                alertify.error(errorMessage);
            }
        }
    });
});
    // === CÁLCULOS AUTOMÁTICOS MODAL LOTE 
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

    // === EJECUTAR CÁLCULO DE TOTALES AL CARGAR 
    $(window).on('load', function() {
        setTimeout(calcularTotales, 500);
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
    

            <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-bottom">
            <h6 class="mb-0 text-dark font-weight-bold">
                <i class="fas fa-boxes text-primary me-2"></i>Productos
            </h6>
        </div>
        <div class="card-body p-3">
            <div class="row g-2 align-items-end mb-3">
                <div class="col-md-2">
                    <label for="cantidad" 
                        class="form-label fw-semibold small text-muted mb-1">
                        Cantidad de empaques
                        <i class="fas fa-question-circle text-primary" 
                        title="Número de cajas, bolsas, tarros, etc."></i>
                    </label>
                    <input type="number" 
                        class="form-control border-primary border-2 py-1" 
                        id="cantidad" 
                        name="cantidad" 
                        value="1" 
                        min="1" 
                        placeholder="Ej: 2"
                        style="font-size: 0.8rem;"
                        required>
                    @error('cantidad')
                        <div class="invalid-feedback d-block small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-5">
                    <label for="codigo"
                        class="form-label fw-semibold small text-muted mb-1">Código de Producto</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-primary border-end-0 py-1">
                            <i class="fas fa-barcode text-primary"></i>
                        </span>
                        <input id="codigo" type="text"
                            class="form-control border-primary border-start-0 py-1" name="codigo"
                            placeholder="Ingresar código" 
                            style="font-size: 0.8rem;"
                            autofocus>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="d-flex justify-content-end gap-1 pt-2">
                        <button type="button" class="btn btn-primary btn-sm flex-grow-1 py-1"
                            data-bs-toggle="modal" data-bs-target="#productosModal"
                            title="Buscar Producto"
                            style="font-size: 0.8rem;">
                            <i class="fas fa-search me-1"></i> 
                        </button>
                        <button type="button" class="btn btn-success btn-sm flex-grow-1 py-1" id="btn-agregar-producto"
                        title="Agregar producto"
                            style="font-size: 0.8rem;">
                            <i class="fas fa-plus me-1"></i> 
                        </button>
                        <a href="{{ route('admin.productos.create') }}"
                            class="btn btn-info btn-sm flex-grow-1 py-1"
                           
                            title="Crear producto"
                            style="font-size: 0.8rem;">
                            <i class="fas fa-plus me-1"></i> 
                        </a>
                    </div>
                </div>

                
            </div>

            <!-- Tabla de productos seleccionados -->
            <div class="table-responsive">
                <table class="table table-sm table-borderless table-hover mb-0"
                    style="font-size: 0.75rem;">
                    <thead class="bg-light-primary">
                        <tr>
                            <th class="text-center px-1 py-1" style="width: 3%;">#</th>
                            <th class="text-center px-1 py-1" style="width: 10%;">Código</th>
                            <th class="text-center px-1 py-1" style="width: 5%;">Cant.</th>
                            <th class="px-1 py-1" style="width: 28%;">Producto</th>
                            <th class="px-1 py-1" style="width: 18%;">Lote</th>
                            <th class="text-end px-1 py-1" style="width: 12%;">Unit.</th>
                            <th class="text-end px-1 py-1" style="width: 12%;">Subtotal</th>
                            <th class="text-center px-1 py-1" style="width: 12%;">Acciones</th>
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
                                <td class="text-center" style="font-size: 0.8rem;">{{ $cont++ }}</td>
                                <td class="text-center"><span class="badge bg-light text-dark border" style="font-size: 0.7rem;">{{ $tmp_compra->producto->codigo }}</span></td>
                                <td class="text-center" style="font-size: 0.8rem;">{{ $tmp_compra->cantidad }}</td>
                                <td class="text-truncate" style="max-width: 200px; font-size: 0.8rem;" title="{{ $tmp_compra->producto->nombre }}">
                                    {{ $tmp_compra->producto->nombre }}
                                </td>
                                <td>
                                    @if(is_null($tmp_compra->lote_id))
    @php
        $stockActual = $tmp_compra->producto->lotes->sum('cantidad');
        $stockMaximo = $tmp_compra->producto->stock_maximo;
        $disponible = $stockMaximo - $stockActual;
    @endphp
    <button type="button"
        class="btn btn-sm btn-outline-primary py-0 px-2 mt-1 btn-agregar-lote"
        data-bs-toggle="modal" 
        data-bs-target="#loteModal"
        data-producto-id="{{ $tmp_compra->producto->id }}"
        data-nombre-producto="{{ $tmp_compra->producto->nombre }}"
        data-stock-actual="{{ $stockActual }}"
        data-stock-maximo="{{ $stockMaximo }}"
        data-disponible="{{ $disponible }}"
        data-tmp-compra-id="{{ $tmp_compra->id }}"
        style="font-size: 0.7rem;">
        <i class="fas fa-plus-circle me-1"></i> Crear Lote
    </button>
@else
                                        <small class="text-success mt-1 d-block" style="font-size: 0.7rem;">
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

                                <td class="text-end precio-unitario" style="font-size: 0.8rem;">
                                    @if($lote)
                                        Bs {{ number_format($precio_unitario, 2, '.', '') }}
                                    @else
                                        <span class="text-muted">Pendiente</span>
                                    @endif
                                </td>

                                <td class="text-end fw-semibold subtotal" style="font-size: 0.8rem;">
                                    @if($lote)
                                        Bs {{ number_format($subtotal, 2, '.', '') }}
                                    @else
                                        <span class="text-muted">Pendiente</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($tmp_compra->lote_id)
                                        <button type="button"
                                            class="btn btn-sm btn-outline-info border-0 py-0 px-2 btn-ver-lote"
                                            data-lote-id="{{ $tmp_compra->lote_id }}" 
                                            title="Ver lote"
                                            style="font-size: 0.7rem;">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @else
                                        <span class="text-muted small" style="font-size: 0.7rem;">Sin lote</span>
                                    @endif
                                
                                
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger border-0 py-0 px-2 delete-btn"
                                        data-id="{{ $tmp_compra->id }}" title="Eliminar producto"
                                        style="font-size: 0.7rem;">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>


                            </tr>
                            @php
                                $total_empaques += $tmp_compra->cantidad;
                                $total_compra += $subtotal;
                            @endphp
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-3 text-muted" style="font-size: 0.8rem;">
                                    <i class="fas fa-info-circle me-2"></i>No hay productos agregados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="2" class="text-end fw-bold border-0" style="font-size: 0.8rem;">Total Empaques:</td>
                            <td class="text-center fw-bold text-primary border-0" style="font-size: 0.8rem;">{{ $total_empaques }}</td>
                            <td colspan="3" class="text-end fw-bold border-0" style="font-size: 0.8rem;">Total Compra:</td>
                            <td class="text-end fw-bold text-success border-0 total-compra" style="font-size: 0.8rem;">
                                Bs {{ number_format($total_compra, 2, '.', '') }}
                            </td>
                            <td class="border-0"></td>
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


<!-- Modal para ver detalles del lote -->
<div class="modal fade" id="verLoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title text-white">
                    <i class="fas fa-box me-2"></i>Detalles del Lote
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="lote-detalles-content">
                    <!-- Contenido -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2 text-muted">Cargando detalles del lote...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btn-editar-lote">
                    <i class="fas fa-edit me-2"></i> Editar Lote
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    
        let loteActual = null;

// === FUNCIÓN PARA VER DETALLES DEL LOTE 
function abrirModalVerLote(loteId) {
   
    const modal = new bootstrap.Modal(document.getElementById('verLoteModal'));
    modal.show();
    
    
    $('#lote-detalles-content').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-info" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-muted">Cargando detalles del lote...</p>
        </div>
    `);
    
    // Cargar los detalles del lote via AJAX
    $.ajax({
        url: '{{ url("admin/lotes") }}/' + loteId,
        type: 'GET',
        success: function(response) {
            console.log('Respuesta del servidor:', response); 
            if (response.success && response.data) {
                loteActual = response.data; 
                mostrarDetallesLote(loteActual);
            } else {
                $('#lote-detalles-content').html(`
                    <div class="alert alert-warning">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                            <div>
                                <h5 class="alert-heading mb-2">Lote no encontrado</h5>
                                <p class="mb-0">No se encontró la información del lote solicitado.</p>
                            </div>
                        </div>
                    </div>
                `);
            }
        },
        error: function(xhr) {
            console.error('Error al cargar lote:', xhr);
            $('#lote-detalles-content').html(`
                <div class="alert alert-danger">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                        <div>
                            <h5 class="alert-heading mb-2">Error al cargar el lote</h5>
                            <p class="mb-0">No se pudieron cargar los detalles del lote. Por favor, intente nuevamente.</p>
                        </div>
                    </div>
                </div>
            `);
        }
    });
}

//  FUNCIÓN PARA MOSTRAR DETALLES DEL LOTE
function mostrarDetallesLote(lote) {
    // Formatear fechas
    function formatearFecha(fechaString) {
        if (!fechaString) return 'N/A';
        try {
            const fecha = new Date(fechaString);
            if (isNaN(fecha.getTime())) return 'Fecha inválida';
            const año = fecha.getFullYear();
            const mes = String(fecha.getMonth() + 1).padStart(2, '0');
            const dia = String(fecha.getDate()).padStart(2, '0');
            return `${año}-${mes}-${dia}`;
        } catch (error) {
            return 'Formato inválido';
        }
    }

    // Calcular días hasta vencimiento
    function calcularDiasVencimiento(fechaVencimiento) {
        if (!fechaVencimiento) return null;
        try {
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);
            const vencimiento = new Date(fechaVencimiento);
            vencimiento.setHours(0, 0, 0, 0);
            const diffTime = vencimiento - hoy;
            return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        } catch (error) {
            return null;
        }
    }

    const fechaIngresoFormateada = formatearFecha(lote.fecha_ingreso);
    const fechaVencimientoFormateada = formatearFecha(lote.fecha_vencimiento);
    
    let estadoVencimiento = '';
    const diasVencimiento = calcularDiasVencimiento(lote.fecha_vencimiento);
    
    if (diasVencimiento !== null) {
        if (diasVencimiento < 0) {
            estadoVencimiento = `<span class="badge bg-danger">Vencido hace ${Math.abs(diasVencimiento)} días</span>`;
        } else if (diasVencimiento <= 30) {
            estadoVencimiento = `<span class="badge bg-warning text-dark">Por vencer en ${diasVencimiento} días</span>`;
        } else {
            estadoVencimiento = `<span class="badge bg-success">Vigente (${diasVencimiento} días)</span>`;
        }
    }

    // Calcular ganancia
    const precioCompraUnitario = parseFloat(lote.precio_compra_unitario) || 0;
    const precioVenta = parseFloat(lote.precio_venta) || 0;
    let gananciaInfo = '';
    
    if (precioCompraUnitario > 0 && precioVenta > 0) {
        const ganancia = precioVenta - precioCompraUnitario;
        const porcentajeGanancia = ((ganancia / precioCompraUnitario) * 100).toFixed(2);
        gananciaInfo = `
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold text-success">
                    <i class="fas fa-chart-line me-2"></i>Ganancia por Unidad
                </label>
                <p class="form-control-plaintext">
                    <span class="text-success fw-bold">
                        Bs ${ganancia.toFixed(2)} (${porcentajeGanancia}%)
                    </span>
                </p>
            </div>
        `;
    }

    let contenido = `
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold text-primary">
                    <i class="fas fa-barcode me-2"></i>Número de Lote
                </label>
                <p class="form-control-plaintext fs-6 fw-semibold bg-light p-2 rounded">
                    ${lote.numero_lote || 'N/A'}
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold text-primary">
                    <i class="fas fa-boxes me-2"></i>Cantidad Total
                </label>
                <p class="form-control-plaintext fs-6 fw-semibold bg-light p-2 rounded">
                    ${lote.cantidad || '0'} unidades
                </p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold text-primary">
                    <i class="fas fa-calendar-plus me-2"></i>Fecha de Ingreso
                </label>
                <p class="form-control-plaintext bg-light p-2 rounded">
                    ${fechaIngresoFormateada}
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold text-primary">
                    <i class="fas fa-calendar-times me-2"></i>Fecha de Vencimiento
                </label>
                <div class="d-flex align-items-center gap-2">
                    <p class="form-control-plaintext bg-light p-2 rounded mb-0 flex-grow-1">
                        ${fechaVencimientoFormateada}
                    </p>
                    ${estadoVencimiento}
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold text-primary">
                    <i class="fas fa-money-bill-wave me-2"></i>Precio de Compra Total
                </label>
                <p class="form-control-plaintext fs-6 fw-semibold text-success bg-light p-2 rounded">
                    Bs ${lote.precio_compra ? parseFloat(lote.precio_compra).toFixed(2) : '0.00'}
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold text-primary">
                    <i class="fas fa-tag me-2"></i>Precio Unitario (Compra)
                </label>
                <p class="form-control-plaintext fs-6 bg-light p-2 rounded">
                    Bs ${lote.precio_compra_unitario ? parseFloat(lote.precio_compra_unitario).toFixed(2) : '0.00'}
                </p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold text-primary">
                    <i class="fas fa-tags me-2"></i>Precio de Venta Unitario
                </label>
                <p class="form-control-plaintext fs-6 fw-semibold text-info bg-light p-2 rounded">
                    Bs ${lote.precio_venta ? parseFloat(lote.precio_venta).toFixed(2) : '0.00'}
                </p>
            </div>
            ${gananciaInfo}
        </div>
        
        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label fw-bold text-primary">
                    <i class="fas fa-cube me-2"></i>Producto Asociado
                </label>
                <p class="form-control-plaintext bg-light p-2 rounded">
                    ${lote.producto ? lote.producto.nombre : 'Producto no encontrado'}
                    ${lote.producto && lote.producto.codigo ? `(Código: ${lote.producto.codigo})` : ''}
                </p>
            </div>
        </div>
    `;
    
    $('#lote-detalles-content').html(contenido);
}

// === FUNCIÓN PARA ABRIR MODAL DE EDICIÓN 
function abrirModalEditarLote() {
    if (!loteActual) return;
    
    // Función para formatear fecha al formato YYYY-MM-DD para inputs date
    function formatearFechaParaInput(fechaString) {
        if (!fechaString) return '';
        try {
         
            if (typeof fechaString === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(fechaString)) {
                return fechaString;
            }
            
            const fecha = new Date(fechaString);
            if (isNaN(fecha.getTime())) {
                console.warn('Fecha inválida:', fechaString);
                return '';
            }
            
            const año = fecha.getFullYear();
            const mes = String(fecha.getMonth() + 1).padStart(2, '0');
            const dia = String(fecha.getDate()).padStart(2, '0');
            
            return `${año}-${mes}-${dia}`;
        } catch (error) {
            console.error('Error formateando fecha:', error);
            return '';
        }
    }

  
    console.log('Datos del lote para editar:', {
        fecha_ingreso_original: loteActual.fecha_ingreso,
        fecha_vencimiento_original: loteActual.fecha_vencimiento,
        tipo_fecha_ingreso: typeof loteActual.fecha_ingreso,
        tipo_fecha_vencimiento: typeof loteActual.fecha_vencimiento
    });

    
    $('#editarLoteId').val(loteActual.id);
    $('#editarNumeroLote').val(loteActual.numero_lote || '');
    $('#editarCantidad').val(loteActual.cantidad || '');
    
   
    $('#editarFechaIngreso').val(formatearFechaParaInput(loteActual.fecha_ingreso));
    $('#editarFechaVencimiento').val(formatearFechaParaInput(loteActual.fecha_vencimiento));
    
    $('#editarPrecioCompra').val(loteActual.precio_compra || '');
    $('#editarPrecioCompraUnitario').val(loteActual.precio_compra_unitario || '');
    $('#editarPrecioVenta').val(loteActual.precio_venta || '');
    
    
    console.log('Valores en inputs de fecha:', {
        input_ingreso: $('#editarFechaIngreso').val(),
        input_vencimiento: $('#editarFechaVencimiento').val()
    });
    
  
    calcularUnitarioEdicion();
    calcularGananciaEdicion();
    
    // Cerrar modal de ver 
    $('#verLoteModal').modal('hide');
    setTimeout(() => {
        $('#editarLoteModal').modal('show');
    }, 300);
}

// === FUNCIONES PARA CÁLCULOS
function calcularUnitarioEdicion() {
    let cantidad = parseFloat($('#editarCantidad').val()) || 0;
    let precioCompra = parseFloat($('#editarPrecioCompra').val()) || 0;

    if (cantidad > 0 && precioCompra > 0) {
        let precioUnitario = (precioCompra / cantidad).toFixed(2);
        $('#editarPrecioCompraUnitario').val(precioUnitario);
    } else {
        $('#editarPrecioCompraUnitario').val("");
    }
}

function calcularGananciaEdicion() {
    let costoUnitario = parseFloat($('#editarPrecioCompraUnitario').val()) || 0;
    let precioVenta = parseFloat($('#editarPrecioVenta').val()) || 0;

    if (costoUnitario > 0 && precioVenta > 0) {
        let ganancia = (precioVenta - costoUnitario).toFixed(2);
        let porcentaje = ((ganancia / costoUnitario) * 100).toFixed(2);
        $('#editar-ganancia-text').html(`Ganancia: Bs ${ganancia} (${porcentaje}%)`);
    } else {
        $('#editar-ganancia-text').html("");
    }
}


$(document).on('click', '.btn-ver-lote', function () {
    const loteId = $(this).data('lote-id');
    abrirModalVerLote(loteId);
});


$(document).on('click', '#btn-editar-lote', function () {
    abrirModalEditarLote();
});


$(document).on('input', '#editarCantidad', calcularUnitarioEdicion);
$(document).on('input', '#editarPrecioCompra', function() {
    calcularUnitarioEdicion();
    calcularGananciaEdicion();
});
$(document).on('input', '#editarPrecioVenta', calcularGananciaEdicion);


$(document).on('click', '#btn-actualizar-lote', function () {
    const loteId = $('#editarLoteId').val();
    const formData = new FormData(document.getElementById('formEditarLote'));

    // Mostrar los datos 
    console.log('Datos a enviar para actualizar:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }

    $.ajax({
        url: '{{ url("admin/lotes") }}/' + loteId,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            console.log('Respuesta de actualización:', response);
            if (response.success) {
                alertify.success('Lote actualizado correctamente');
                $('#editarLoteModal').modal('hide');
                
                // Recargar la página para ver los cambios
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                alertify.error(response.message || 'Error al actualizar el lote');
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
        }
    });
}); 
</script>



<!-- Modal para editar lote -->
<div class="modal fade" id="editarLoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white">
                    <i class="fas fa-edit me-2"></i>Editar Lote
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarLote" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="lote_id" id="editarLoteId">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Número de Lote*</label>
                            <input type="text" class="form-control" name="numero_lote" id="editarNumeroLote" required
                                placeholder="Ej: LT-2023-001" pattern="[A-Za-z0-9-]+"
                                title="Solo letras, números y guiones">
                            <small class="text-muted">Código único para identificar este lote</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cantidad (Unidades)*</label>
                            <input type="number" class="form-control" name="cantidad" id="editarCantidad" min="1" required
                                placeholder="Ej: 50">
                            <div class="form-text" id="editar-stock-maximo-text">Stock máximo: 0 unidades</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha de Ingreso*</label>
                            <input type="date" class="form-control" name="fecha_ingreso" id="editarFechaIngreso" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha de Vencimiento*</label>
                            <input type="date" class="form-control" name="fecha_vencimiento" id="editarFechaVencimiento">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Precio de Compra (Bs)*</label>
                            <div class="input-group">
                                <span class="input-group-text">Bs</span>
                                <input type="number" step="0.01" class="form-control" 
                                    name="precio_compra" id="editarPrecioCompra" placeholder="0.00" required min="0">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Precio de Compra Unitario (Bs)</label>
                            <div class="input-group">
                                <span class="input-group-text">Bs</span>
                                <input type="number" step="0.01" class="form-control" 
                                    name="precio_compra_unitario" id="editarPrecioCompraUnitario" readonly>
                            </div>
                            <small class="text-muted">Se calcula automáticamente</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Precio de Venta Unitario (Bs)*</label>
                            <div class="input-group">
                                <span class="input-group-text">Bs</span>
                                <input type="number" step="0.01" class="form-control" 
                                    name="precio_venta" id="editarPrecioVenta" placeholder="0.00" required min="0">
                            </div>
                            <div class="form-text" id="editar-ganancia-text"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btn-actualizar-lote">
                    <i class="fas fa-save me-2"></i> Actualizar Lote
                </button>
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
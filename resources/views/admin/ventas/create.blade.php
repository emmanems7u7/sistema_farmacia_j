@extends('layouts.argon')

@section('content')

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

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

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

// FUNCIÓN PARA AÑADIR PRODUCTO 
function agregarProducto() {
    const codigo = $('#codigo').val().trim();
    const cantidad = $('#cantidad').val();
    
    if(!codigo) {
        Swal.fire('Error', 'Por favor ingrese un código', 'warning');
        return;
    }

    if(cantidad <= 0) {
        Swal.fire('Error', 'La cantidad debe ser mayor a cero', 'warning');
        return;
    }

    // PRIMERO VERIFICAR STOCK DISPONIBLE
    $.ajax({
        url: "{{ route('admin.ventas.verificar_stock') }}", 
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
            if(response.stock_suficiente) {
                // Si hay stock suficiente, proceder a agregar el producto
                agregarProductoTmp(codigo, cantidad);
            } else {
                // Si no hay stock suficiente, mostrar alerta
                Swal.fire({
                    icon: 'error',
                    title: 'Stock Insuficiente',
                    html: `
                        <div class="text-start">
                            <p><strong>Producto:</strong> ${response.producto_nombre}</p>
                            <p><strong>Cantidad solicitada:</strong> ${cantidad}</p>
                            <p><strong>Stock disponible:</strong> ${response.stock_disponible}</p>
                            <p class="text-danger"><strong>Faltan:</strong> ${response.faltante} unidades</p>
                        </div>
                    `,
                    confirmButtonText: 'Entendido'
                });
            }
        },
        error: function(xhr) {
            Swal.fire('Error', 'Error al verificar el stock', 'error');
        }
    });
}

// FUNCIÓN PARA AGREGAR PRODUCTO A TEMPORAL
function agregarProductoTmp(codigo, cantidad) {
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

// Seleccionar cliente 
$(document).on('click', '.seleccionar-btn-cliente', function(){
    const id_cliente = $(this).data('id');
    const nombre_cliente = $(this).data('nombre_cliente');
    const nit_ci = $(this).data('nit');
    
    $('#nombre_cliente_select').val(nombre_cliente);
    $('#nit_cliente_select').val(nit_ci);
    $('#id_cliente').val(id_cliente);
    
    // Cerrar modal 
    const clienteModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('clienteModal'));
    clienteModal.hide();
    
    // Limpiar posibles overlays
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
});

// Seleccionar producto
$(document).on('click', '.seleccionar-btn', function(){
    const id_producto = $(this).data('id');
    $('#codigo').val(id_producto);
    
    // Cerrar modal 
    const verModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('verModal'));
    verModal.hide();
    
 
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    
    $('#codigo').focus();
});


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

// Buscar producto por código enter
$(document).ready(function() {
    $('#codigo').focus();
    
    $('#form_venta').on('keypress', function(e) {
        if(e.keyCode === 13) {   
            e.preventDefault();
        }
    });

    // Evento para añadir producto con Enter
    $('#codigo').on('keyup', function(e) {
        if (e.which === 13) {
            agregarProducto();
        }
    });

    // Evento para añadir producto con el botón +
    $('#btn-agregar-producto').on('click', function() {
        agregarProducto();
    });
    
    // Inicializar DataTable
    $('#mitabla, #mitabla2').DataTable({
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        responsive: true,
        autoWidth: false,
        dom: '<"d-flex justify-content-between mb-3"lf>t<"d-flex justify-content-between mt-3"ip>', // Layout moderno
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
});


// Editar producto de la venta temporal
$(document).on('click', '.edit-btn', function() {
    const id = $(this).data('id');
    const codigoActual = $(this).data('codigo');
    const cantidadActual = $(this).data('cantidad');
    
    Swal.fire({
        title: 'Editar Cantidad',
        html: `
            <div class="text-start">
                <p><strong>Producto:</strong> ${codigoActual}</p>
                <p><strong>Cantidad actual:</strong> ${cantidadActual}</p>
            </div>
            <input type="number" id="nuevaCantidad" class="form-control mt-3" value="${cantidadActual}" min="1" required>
        `,
        showCancelButton: true,
        confirmButtonText: 'Actualizar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const nuevaCantidad = document.getElementById('nuevaCantidad').value;
            if (!nuevaCantidad || nuevaCantidad <= 0) {
                Swal.showValidationMessage('La cantidad debe ser mayor a cero');
                return false;
            }
            return nuevaCantidad;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const nuevaCantidad = result.value;
            
            $.ajax({
                url: "{{url('/admin/ventas/create/tmp')}}/"+id,
                type: 'POST', 
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT', 
                    cantidad: nuevaCantidad
                },
                beforeSend: function() {
                    Swal.showLoading();
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Cantidad actualizada",
                            showConfirmButton: false,
                            timer: 1000
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Error al actualizar', 'error');
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
});


// CONFIRMACIÓN DE VENTA 
document.addEventListener('DOMContentLoaded', function() {
    const formVenta = document.getElementById('form_venta');
    
    formVenta.addEventListener('submit', function(e) {
        e.preventDefault(); 
        const totalVentaInput = document.querySelector('input[name="precio_total"]');
        const totalVenta = totalVentaInput ? parseFloat(totalVentaInput.value) : 0;
     
        const clienteNombre = document.getElementById('nombre_cliente_select').value;
        const metodoPago = document.getElementById('metodo_pago').value;
        
       
        let productosResumen = '';
        let totalItems = 0;
        
       
        const tablaVenta = document.querySelector('.table-responsive table tbody');
        const filasProductosVenta = tablaVenta ? tablaVenta.querySelectorAll('tr') : [];
        
        filasProductosVenta.forEach((fila, index) => {
            const codigo = fila.cells[1]?.textContent?.trim() || '';
            const nombreElement = fila.cells[3];
            let nombre = '';
            
            // Extraer solo el nombre del producto (sin info de lote)
            if (nombreElement) {
                const textoCompleto = nombreElement.textContent || '';
                // Eliminar la parte del lote si existe
                nombre = textoCompleto.split('(Lote:')[0].trim();
            }
            
            const cantidad = fila.cells[2]?.textContent?.trim() || '0';
            const precioUnitario = fila.cells[4]?.textContent?.trim() || '';
            const subtotal = fila.cells[5]?.textContent?.trim() || '';
            
            if (nombre && cantidad !== '0') {
                productosResumen += `<div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                    <div>
                        <small><strong>${nombre}</strong></small>
                        <br>
                        <small class="text-muted">Código: ${codigo} | Cant: ${cantidad}</small>
                    </div>
                    <div class="text-end">
                        <small class="text-success">${subtotal}</small>
                        <br>
                        <small class="text-muted">${precioUnitario} c/u</small>
                    </div>
                </div>`;
                totalItems += parseInt(cantidad) || 0;
            }
        });
        
        // Si no hay productos, mostrar mensaje
        if (productosResumen === '') {
            productosResumen = '<div class="alert alert-warning py-2 text-center">No hay productos en la venta</div>';
        }
        
        Swal.fire({
            title: 'Confirmar Venta',
            html: `
                <div class="text-start">
                    <div class="mb-3">
                        <strong><i class="fas fa-user me-2"></i>Cliente:</strong><br>
                        <span class="text-dark">${clienteNombre}</span>
                    </div>
                    
                    <div class="mb-3">
                        <strong><i class="fas fa-shopping-cart me-2"></i>Productos en la Venta:</strong>
                        <div style="max-height: 200px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 0.85rem;">
                            ${productosResumen}
                        </div>
                        <div class="mt-2 text-center">
                            <small class="text-muted"><strong>Total:</strong> ${totalItems} productos</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <strong><i class="fas fa-credit-card me-2"></i>Método de Pago:</strong><br>
                        <span class="badge bg-primary">${metodoPago.toUpperCase()}</span>
                    </div>
                    
                    <div class="mb-2 text-center">
                        <strong><i class="fas fa-money-bill-wave me-2"></i>Total a Pagar:</strong><br>
                        <span class="h4 text-success">Bs ${totalVenta.toFixed(2)}</span>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: '#2dce89',
            cancelButtonColor: '#f5365c',
            confirmButtonText: '<i class="fas fa-check me-2"></i>Confirmar Venta',
            cancelButtonText: '<i class="fas fa-times me-2"></i>Cancelar',
            width: '650px',
            customClass: {
                popup: 'border-radius-1'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar loading mientras se procesa
                Swal.fire({
                    title: 'Procesando Venta...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Si confirma, enviar el formulario
                formVenta.removeEventListener('submit', arguments.callee);
                formVenta.submit();
            }
        });
    });
});


</script>

<div class="container-fluid mt--6">
    <div class="row">
        <!-- Card Principal  -->
        <div class="card shadow-lg border-0 rounded-lg" style="height: auto; min-height: 0;">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div>
                            <h class="mb-0 text-dark font-weight-bold" style="font-size: 1.1rem;">Registrar Nueva Venta</h>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('admin.ventas.index') }}" class="btn btn-outline-dark btn-sm py-1">
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
                                <input id="codigo" type="text" class="form-control form-control-lg" name="codigo" placeholder=" Ingresar código" autofocus>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="d-flex gap-2 w-100">
                                <button type="button" class="btn btn-primary flex-grow-1" data-bs-toggle="modal" data-bs-target="#verModal">
                                    <i class="fas fa-search me-2"></i> Buscar
                                </button>
                                <!-- Botón para añadir el producto a la tabla de productos -->
                                <button type="button" class="btn btn-success flex-grow-1" id="btn-agregar-producto">
                                    <i class="fas fa-plus me-2"></i> Añadir
                                </button>
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
                <th class="text-center px-1" style="width: 8%;">Acciones</th> 
            </tr>
        </thead>
        <tbody>
            <?php $cont = 1; $total_cantidad = 0; $total_venta = 0;?>
            @foreach($tmp_ventas as $tmp_venta)
                @php
                    // Obtener los lotes disponibles para el producto ordenados por fecha de ingreso (PEPS)
                    $lotes = \App\Models\Lote::where('producto_id', $tmp_venta->producto_id)
                                              ->where('cantidad', '>', 0)
                                              ->orderBy('fecha_ingreso', 'asc')
                                              ->get();

                    $cantidad_restante = $tmp_venta->cantidad;
                @endphp

                @foreach($lotes as $lote)
                    @if($cantidad_restante <= 0)
                        @break
                    @endif

                    @php
                        $cantidad_a_mostrar = min($cantidad_restante, $lote->cantidad);
                        $subtotal = $cantidad_a_mostrar * $lote->precio_venta;
                        $cantidad_restante -= $cantidad_a_mostrar;
                    @endphp

                    <tr>
                        <td class="text-center">{{$cont++}}</td>
                        <td class="text-center small">{{$tmp_venta->producto->codigo}}</td>
                        <td class="text-center">{{$cantidad_a_mostrar}}</td>
                        <td class="small text-truncate" style="max-width: 200px;" title="{{$tmp_venta->producto->nombre}}">
                            {{$tmp_venta->producto->nombre}} (Lote: {{$lote->numero_lote}})
                        </td>
                        <td class="text-end">Bs {{ number_format($lote->precio_venta, 2) }}</td>
                        <td class="text-end">Bs {{ number_format($subtotal, 2) }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <!-- Botón Editar -->
                                <button type="button" 
                                        class="btn btn-sm btn-outline-warning border-0 py-0 px-2 edit-btn" 
                                        data-id="{{$tmp_venta->id}}"
                                        data-codigo="{{$tmp_venta->producto->codigo}}"
                                        data-cantidad="{{$tmp_venta->cantidad}}"
                                        title="Editar cantidad">
                                    <i class="fas fa-edit" style="font-size: 0.75rem;"></i>
                                </button>
                                
                                <!-- Botón Eliminar -->
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger border-0 py-0 px-2 delete-btn" 
                                        data-id="{{$tmp_venta->id}}"
                                        title="Eliminar producto">
                                    <i class="fas fa-trash-alt" style="font-size: 0.75rem;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    @php
                        $total_cantidad += $cantidad_a_mostrar;
                        $total_venta += $subtotal;
                    @endphp
                @endforeach
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
            <h6 class="mb-0 text-primary font-weight-bold">
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
                         @can('clientes.ver')
                        <button type="button" class="btn btn-primary flex-grow-1" data-bs-toggle="modal" data-bs-target="#clienteModal">
                            <i class="fas fa-search me-2"></i>Buscar Cliente
                        </button>
                        @endcan
                         @can('clientes.crear')
                        <button type="button" class="btn btn-success flex-grow-1" data-bs-toggle="modal" data-bs-target="#clientecrearModal">
                            <i class="fas fa-plus me-2"></i>Nuevo
                        </button>
                        @endcan
                    </div>
                    
                    
                    <!-- Nombre y NIT en una sola línea -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Nombre del Cliente</label>
                            <input type="text" class="form-control" id="nombre_cliente_select" value="S/N" disabled>
                            <input type="hidden" id="id_cliente" name="cliente_id">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">NIT/CI</label>
                            <input type="text" class="form-control" id="nit_cliente_select" value="0" disabled>
                        </div>
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

                

                <!-- Método de Pago  -->
                <div class="mb-4 payment-section">
                    <h6 class="mb-3 text-primary font-weight-bold">
                        <i class="fas fa-money-bill-wave me-2"></i>Método de Pago
                    </h6>
                    
                    <!-- Métodos de pago  -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="card payment-method active p-2" data-method="efectivo" style="min-height: auto;">
                                <div class="card-body text-center p-2">
                                    <i class="fas fa-money-bill-wave text-success mb-1"></i>
                                    <h6 class="card-title mb-0" style="font-size: 0.9rem;">Efectivo</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card payment-method p-2" data-method="qr" style="min-height: auto;">
                                <div class="card-body text-center p-2">
                                    <i class="fas fa-qrcode text-primary mb-1"></i>
                                    <h6 class="card-title mb-0" style="font-size: 0.9rem;">QR</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" id="metodo_pago" name="metodo_pago" value="efectivo">
                    
                    <!-- Campos para Efectivo -->
                    <div id="efectivo-fields">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="monto_recibido" class="form-label">Monto Recibido (Bs)</label>
                                <input type="number" class="form-control" id="monto_recibido" name="monto_recibido" min="0" step="0.01" placeholder="0.00">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cambio</label>
                                <div class="change-display" id="cambio_display">Bs 0.00</div>
                                <input type="hidden" id="cambio" name="cambio" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campos para QR -->
                    <div id="qr-fields" style="display: none;">
                        <div class="alert alert-info py-2">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Confirme el pago QR antes de continuar</small>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pago_confirmado" name="pago_confirmado">
                            <label class="form-check-label" for="pago_confirmado" style="font-size: 0.9rem;">
                                Pago confirmado
                            </label>
                        </div>
                    </div>

                    <!-- Total y Botón Registrar en una línea -->
                <div class="row align-items-end mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Total a Pagar</label>
                        <input type="text" class="form-control text-center fw-bold text-white bg-success" 
                               value="Bs {{number_format($total_venta, 2)}}" readonly>
                        <input type="hidden" name="precio_total" value="{{$total_venta}}">
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            REGISTRAR VENTA
                        </button>
                    </div>
                </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.payment-section {
    border-top: 1px solid #eee;
    padding-top: 1rem;
}
.change-display {
    font-size: 1rem;
    font-weight: bold;
    padding: 0.4rem;
    border-radius: 5px;
    background-color: #f8f9fa;
}
.positive-change {
    color: #28a745;
}
.negative-change {
    color: #dc3545;
}
.payment-method {
    cursor: pointer;
    transition: all 0.3s;
    border: 2px solid #dee2e6;
}
.payment-method:hover {
    transform: translateY(-1px);
    border-color: #adb5bd;
}
.payment-method.active {
    border-color: #0d6efd !important;
    background-color: #f8f9fa;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalVenta = {{$total_venta}};
    const metodoPagoInput = document.getElementById('metodo_pago');
    const efectivoFields = document.getElementById('efectivo-fields');
    const qrFields = document.getElementById('qr-fields');
    const montoRecibidoInput = document.getElementById('monto_recibido');
    const cambioDisplay = document.getElementById('cambio_display');
    const cambioInput = document.getElementById('cambio');
    const paymentMethods = document.querySelectorAll('.payment-method');
    

    paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
            
            paymentMethods.forEach(m => m.classList.remove('active'));
     
            this.classList.add('active');
            
            const selectedMethod = this.getAttribute('data-method');
            metodoPagoInput.value = selectedMethod;
            
           
            if (selectedMethod === 'efectivo') {
                efectivoFields.style.display = 'block';
                qrFields.style.display = 'none';
            } else {
                efectivoFields.style.display = 'none';
                qrFields.style.display = 'block';
            }
        });
    });
    
    // Calcular cambio cuando se ingresa el monto recibido
    montoRecibidoInput.addEventListener('input', function() {
        const montoRecibido = parseFloat(this.value) || 0;
        const cambio = montoRecibido - totalVenta;
        
        cambioInput.value = cambio.toFixed(2);
        
        if (cambio >= 0) {
            cambioDisplay.textContent = `Bs ${cambio.toFixed(2)}`;
            cambioDisplay.className = 'change-display positive-change';
        } else {
            cambioDisplay.textContent = `Bs ${Math.abs(cambio).toFixed(2)} (Falta)`;
            cambioDisplay.className = 'change-display negative-change';
        }
    });
    
    // Validación del formulario antes de enviar
    document.getElementById('form_venta').addEventListener('submit', function(e) {
        const metodoPago = metodoPagoInput.value;
        
        if (metodoPago === 'efectivo') {
            const montoRecibido = parseFloat(montoRecibidoInput.value) || 0;
            if (montoRecibido < totalVenta) {
                e.preventDefault();
                alert('El monto recibido es menor al total a pagar. Por favor, ingrese un monto suficiente.');
                montoRecibidoInput.focus();
            }
        } else if (metodoPago === 'qr') {
            const pagoConfirmado = document.getElementById('pago_confirmado').checked;
            if (!pagoConfirmado) {
                e.preventDefault();
                alert('Debe confirmar que el pago se ha realizado correctamente antes de registrar la venta.');
            }
        }
    });
});
</script>
    </div>
</div>


<!-- Modal Productos -->

<div class="modal fade" id="verModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <!-- Encabezado del Modal -->

 

            <div class="modal-header bg-primary ">
                <div class="d-flex align-items-center">

              
                   <h5 class="modal-title fw-semibold text-white">
                    <i class="fas fa-boxes me-2"></i> Listado de Productos</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Cuerpo del Modal -->
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table id="mitabla" class="table table-hover align-items-center mb-0">
                        <thead class="bg-light">


                        <style>
.encabezado-pequeno th {
    font-size: 10px;
}
</style>

                            <tr class="encabezado-pequeno">
    <th class="text-uppercase text-secondary font-weight-bolder ps-4">#</th>
    <th class="text-uppercase text-secondary font-weight-bolder text-center">Acción</th>
    <th class="text-uppercase text-secondary font-weight-bolder">Código</th>
    <th class="text-uppercase text-secondary font-weight-bolder">Nombre</th>
    <th class="text-uppercase text-secondary font-weight-bolder">Laboratorio</th>
    <th class="text-uppercase text-secondary font-weight-bolder text-center">Stock</th>
    <th class="text-uppercase text-secondary font-weight-bolder text-end">Precio</th>
    <th class="text-uppercase text-secondary font-weight-bolder">Fecha Venc.</th>
</tr>

                        </thead>
                        <tbody>
                            @foreach($productos as $producto)
                            <tr>
                                <td class="text-xs font-weight-normal ps-4">{{ $loop->iteration }}</td>
                               <td class="text-center">
                                    <button type="button"
                                            class="btn btn-xs 
                                            @if($producto->stock <= 0) btn-outline-secondary disabled @else btn-outline-primary @endif seleccionar-btn"
                                            style="
                                                width: 25px; 
                                                height: 25px; 
                                                display: flex; 
                                                align-items: center; 
                                                justify-content: center; 
                                                padding: 0;  /* elimina espacio interno */
                                            "
                                            data-id="{{$producto->codigo}}"
                                            data-nombre="{{$producto->nombre}}"
                                            @if($producto->stock <= 0) disabled @endif>
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </td>




                                <td class="text-xs font-weight-normal">
                                    <span class="badge bg-gray-200 text-dark">{{ $producto->codigo }}</span>
                                </td>
                                <td class="text-xs font-weight-normal">
                                    <strong>{{ $producto->nombre }}</strong>
                                </td>
                                <td class="text-xs font-weight-normal">
                                    <strong>{{ $producto->laboratorio->nombre }}</strong>
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
                                        
                                        // $lote = $producto->lotes->sortByDesc('id')->first();
                                        
                                        // Toma el primer lote disponible (más antiguo primero - PEPS)
                                        $lote = $producto->lotes()
                                                        ->where('cantidad', '>', 0)
                                                        ->orderBy('fecha_ingreso', 'asc')
                                                        ->orderBy('id', 'asc')
                                                        ->first();
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

            <div class="modal-header bg-primary">
                <div class="d-flex align-items-center">

                    
                    <h5 class="modal-title fw-semibold text-white">
                        <i class="fas fa-users me-2"></i>Listado de Clientes</h5>

                       
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
    <!-- Botón Seleccionar -->
    <button type="button" class="btn btn-sm btn-outline-primary seleccionar-btn-cliente" 
            data-id="{{$cliente->id}}" 
            data-nit="{{$cliente->nit_ci}}" 
            title="Seleccionar cliente"
            data-nombre_cliente="{{$cliente->nombre_cliente}}">
        <i class="fas fa-check me-1"></i>
    </button>

    <!-- Botón Editar al lado del seleccionar -->
    <button type="button" class="btn btn-sm btn-outline-success ms-1" 
            data-bs-toggle="modal" 
            title="Editar cliente"
            data-bs-target="#editModal{{ $cliente->id }}">
        <i class="fas fa-edit me-1"></i>
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
            
           
        </div>
    </div>
</div>
<!--modal crear clientes-->
<div class="modal fade" id="clientecrearModal" tabindex="-1" aria-labelledby="clientecrearModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="clientecrearModalLabel">Registrar cliente</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="nombre_cliente" class="form-label">Nombre completo *</label>
                            <input type="text" class="form-control" id="nombre_cliente" 
                                   name="nombre_cliente"
                                   placeholder="Ej: Juan Pérez García" 
                                   minlength="3" 
                                   maxlength="100"
                                   onkeypress="return soloLetras(event)"
                                   required
                                   value="{{ old('nombre_cliente') }}">
                            
                            @error('nombre_cliente')
                                <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                    </div>   

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nit_ci" class="form-label">NIT/CI *</label>
                            <input type="text" class="form-control" id="nit_ci" 
                                   name="nit_ci"
                                   placeholder="Ej: 123456789" 
                                   minlength="5" 
                                   maxlength="15"
                                   onkeypress="return soloNumeros(event)"
                                   required
                                   value="{{ old('nit_ci') }}">
                            
                            @error('nit_ci')
                                <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                    </div>  

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="celular" class="form-label">Celular *</label>
                            <input type="tel" class="form-control" id="celular" 
                                   name="celular"
                                   placeholder="Ej: 69123456" 
                                   minlength="8" 
                                   maxlength="10"
                                   onkeypress="return soloNumeros(event)"
                                   required
                                   value="{{ old('celular') }}">
                            
                            @error('celular')
                                <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                    </div>                 

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="email-prefix" 
                                       placeholder="usuario"
                                       maxlength="50">
                                <span class="input-group-text">@gmail.com</span>
                                <input type="hidden" id="email" name="email" value="{{ old('email') }}">
                            </div>
                            <div class="form-text">Escribe solo la primera parte del correo</div>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                 @can('clientes.guardar')
                <button type="button" onclick="guardar_cliente()" class="btn btn-primary">
                    <i class="fas fa-save"></i> Registrar
                </button>
                @endcan
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Función para solo letras y espacios
function soloLetras(event) {
    const charCode = event.which ? event.which : event.keyCode;
    const charStr = String.fromCharCode(charCode);
    
    // Permitir letras, espacios, ñ, Ñ y caracteres acentuados
    if (/[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/.test(charStr)) {
        return true;
    }
    
    event.preventDefault();
    return false;
}

// Función para solo números
function soloNumeros(event) {
    const charCode = event.which ? event.which : event.keyCode;
    const charStr = String.fromCharCode(charCode);
    
    // Solo permitir números
    if (/[0-9]/.test(charStr)) {
        return true;
    }
    
    event.preventDefault();
    return false;
}

// Función para actualizar el email completo
function actualizarEmailCompleto() {
    const prefix = document.getElementById('email-prefix').value.trim();
    const emailCompleto = prefix ? prefix + '@gmail.com' : '';
    document.getElementById('email').value = emailCompleto;
}


document.addEventListener('DOMContentLoaded', function() {
    const emailPrefix = document.getElementById('email-prefix');
    
    emailPrefix.addEventListener('input', function() {
        
        this.value = this.value.replace(/[^a-zA-Z0-9.-]/g, '');
        actualizarEmailCompleto();
    });
    
    emailPrefix.addEventListener('blur', function() {
        actualizarEmailCompleto();
    });
});
</script>

<style>
.form-text {
    font-size: 0.75rem;
    color: #6c757d;
    margin-top: 0.25rem;
}


.form-control:valid,
.form-control:invalid {
    border-color: #ced4da; 
}

.form-control:focus:valid,
.form-control:focus:invalid {
    border-color: #b8bcc2ff; 
    box-shadow: 0 0 0 0.2rem rgba(241, 243, 248, 1); 
}

.input-group-text {
    background-color: #f8f9fa;
    color: #6c757d;
    font-weight: 500;
}


</style>

<!-- Modales para Editar Clientes -->
@foreach($clientes as $cliente)
<div class="modal fade" id="editModal{{ $cliente->id }}" tabindex="-1" role="dialog"
    aria-labelledby="editModalLabel{{ $cliente->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-success text-white">
                <h5 class="modal-title text-white" id="editModalLabel{{ $cliente->id }}">
                    <i class="fas fa-edit me-2"></i>Editar Cliente
                </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editClienteForm{{ $cliente->id }}" action="{{ url('/admin/clientes', $cliente->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre del cliente</label>
                        <input type="text" class="form-control" value="{{ $cliente->nombre_cliente }}" name="nombre_cliente"
                            pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                            oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')">
                    </div>

                    <div class="form-group">
                        <label>NIT/CI</label>
                        <input type="text" class="form-control" value="{{ $cliente->nit_ci }}" name="nit_ci"
                            pattern="[0-9]+"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="15">
                    </div>

                    <div class="form-group">
                        <label>Celular</label>
                        <input type="text" class="form-control" value="{{ $cliente->celular }}" name="celular"
                            pattern="[0-9]+"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="8">
                    </div>

                    <div class="form-group">
                        <label>Correo</label>
                        <input type="text" class="form-control" value="{{ $cliente->email }}" name="email">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn bg-gradient-success text-white">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach



<script>
@foreach($clientes as $cliente)
$('#editClienteForm{{ $cliente->id }}').submit(function(e){
    e.preventDefault(); // Evita recargar la página
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: form.serialize(),
        success: function(response){
            // Cierra el modal
            $('#editModal{{ $cliente->id }}').modal('hide');

            // Actualiza dinámicamente los campos del cliente en la vista de crear venta
            // Supongamos que tienes un input con name="cliente_nombre" y otro name="cliente_nit"
            $('[name="cliente_nombre"]').val(response.nombre_cliente);
            $('[name="cliente_nit"]').val(response.nit_ci);
        },
        error: function(xhr){
            console.error('Error al actualizar cliente');
        }
    });
});
@endforeach
</script>





@endsection


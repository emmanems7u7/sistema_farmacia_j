@extends('layouts.app', ['title' => 'Gestión de Compras'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Gestion de Compras'])
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Card Principal - Diseño Mejorado -->
            <div class="card shadow-lg border-0 rounded-lg" style="height: auto; min-height: 0;">
                <div class="card-header bg-white border-bottom py-3"> <!-- Reduje el padding vertical -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div>
                                <h class="mb-0 text-dark font-weight-bold" style="font-size: 1.1rem;">Registrar Nueva Compra</h4>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('admin.compras.index') }}" class="btn btn-outline-dark btn-sm py-1"> <!-- Reduje padding del botón -->
                                <i class="fas fa-list me-1"></i> Ver Historial
                            </a>
                        </div>
                    </div>
                </div>
            </div>
                

                
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
                                    <label for="cantidad" class="form-label fw-semibold small text-muted">Cantidad</label>
                                    <input type="number" class="form-control border-primary border-2" 
                                        id="cantidad" name="cantidad" value="1" min="1" required>
                                    @error('cantidad')
                                        <div class="invalid-feedback d-block small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-5">
                                    <label for="codigo" class="form-label fw-semibold small text-muted">Código de Producto</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-primary border-end-0">
                                            <i class="fas fa-barcode text-primary"></i>
                                        </span>
                                        <input id="codigo" type="text" class="form-control border-primary border-start-0" 
                                            name="codigo" placeholder="Ingresar código" autofocus>
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
                                            <table class="table table-sm table-borderless table-hover mb-0" style="font-size: 0.85rem;">
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
                                                        <td class="text-center"><span class="badge bg-light text-dark border">{{ $tmp_compra->producto->codigo }}</span></td>
                                                        <td class="text-center">{{ $tmp_compra->cantidad }}</td>
                                                        <td class="text-truncate" style="max-width: 200px;" title="{{ $tmp_compra->producto->nombre }}">{{ $tmp_compra->producto->nombre }}</td>
                                                        <!--aqui se visularia el lote de la compra -->
                                                    <td>
                                                        <select class="form-select form-select-sm select-lote" 
                                                                name="lotes[{{ $tmp_compra->producto_id }}]" 
                                                                data-producto-id="{{ $tmp_compra->producto_id }}"
                                                                required>
                                                            <option value="">Ver lote</option>
                                                            @foreach($tmp_compra->producto->lotes as $lote)
                                                                <option value="{{ $lote->id }}" 
                                                                        data-precio="{{ $lote->precio_compra }}"
                                                                        {{ $lote->id == optional($lotesPorProducto[$tmp_compra->producto_id]->first())->id ? 'selected' : '' }}>
                                                                    {{ $lote->numero_lote }} (Bs {{ number_format($lote->precio_compra, 2) }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 mt-1 btn-agregar-lote"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#loteModal"
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
                                                            <button type="button" class="btn btn-sm btn-outline-danger border-0 py-0 px-2 delete-btn" 
                                                                data-id="{{ $tmp_compra->id }}" title="Eliminar">
                                                                <i class="fas fa-trash-alt" style="font-size: 0.75rem;"></i>
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
                                                            <i class="fas fa-info-circle me-2"></i>No hay productos agregados
                                                        </td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                                <tfoot class="bg-light">
                                                    <tr>
                                                        <th colspan="2" class="text-end small">Totales:</th>
                                                        <th class="text-center small">{{ $total_cantidad }}</th>
                                                        <th colspan="2" class="text-end small">Total:</th>
                                                        <th class="text-end text-success fw-bold">Bs {{ number_format($total_compra, 2, '.', '') }}</th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                          
                            <!-- Sección de Información de Compra -->
                            <div class="col-lg-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-white border-bottom">
                                        <h6 class="mb-0 text-dark font-weight-bold">
                                            <i class="fas fa-info-circle text-primary me-2"></i>Información de Compra
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <button type="button" class="btn btn-outline-primary w-100" 
                                                data-bs-toggle="modal" data-bs-target="#laboratoriosModal">
                                                <i class="fas fa-search me-1"></i> Buscar Laboratorio
                                            </button>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <label class="form-label fw-bold text-sm">Laboratorio</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="fas fa-hospital text-primary"></i>
                                                </span>
                                                <input type="text" class="form-control border" id="nombre_laboratorio" 
                                                    placeholder="Seleccione un laboratorio" disabled>
                                                <input type="hidden" id="id_laboratorio" name="laboratorio_id">
                                            </div>
                                        </div>

                                        <hr class="horizontal dark my-4">

                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <label for="fecha" class="form-label fw-bold text-sm">Fecha</label>
                                                <input 
                                                    type="date" 
                                                    class="form-control form-control-sm border" 
                                                    name="fecha" 
                                                    value="{{ old('fecha', date('Y-m-d')) }}" 
                                                    min="{{ date('Y-m-d') }}" 
                                                    required
                                                >
                                                @error('fecha')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>


                                            <div class="col-md-6">
                                                <label for="comprobante" class="form-label fw-bold text-sm">Comprobante</label>
                                                <select name="comprobante" id="comprobante" 
                                                    class="form-select form-select-sm border" required>
                                                    <option value="FACTURA" selected>FACTURA</option>
                                                    <option value="RECIBO">RECIBO</option>
                                                    <option value="NOTA">NOTA</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="precio_total" class="form-label fw-bold text-sm">Total Compra</label>
                                            <input 
                                                type="text" 
                                                class="form-control form-control-sm bg-light text-dark text-center fw-bold border" 
                                                name="precio_total" 
                                                value="{{ number_format($total_compra, 2, '.', '') }}"
                                                readonly
                                            >
                                        </div>


                                        <div class="d-grid mt-4">
                                            <button type="submit" class="btn btn-primary shadow-sm py-2" 
                                                {{ $total_compra <= 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-save me-2"></i> Registrar Compra
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para creación de lote -->
<div class="modal fade" id="loteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-boxes me-2"></i>Registro de Lote para: <span id="nombre-producto-modal"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <input type="number" step="0.01" class="form-control" 
                                    name="precio_compra" placeholder="0.00" required min="0"
                                    id="precioCompraInput">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Precio de Venta (Bs)*</label>
                            <div class="input-group">
                                <span class="input-group-text">Bs</span>
                                <input type="number" step="0.01" class="form-control" 
                                    name="precio_venta" placeholder="0.00" required min="0"
                                    id="precioVentaInput">
                            </div>
                            <div class="form-text" id="gananciaText"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardarLote">
                    <i class="fas fa-save me-2"></i> Guardar Lote
                </button>
            </div>
        </div>
    </div>
</div>



<!-- Modal de Productos  -->
<div class="modal fade" id="productosModal" tabindex="-1" aria-hidden="true">
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

<!-- Modal de Laboratorios  -->

<div class="modal fade" id="laboratoriosModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-semibold">
                    <i class="fas fa-hospital me-2"></i>Listado de Laboratorios
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table id="mitabla2" class="table table-sm table-hover mb-0" style="width:100%">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-uppercase small fw-bold" style="width: 5%;">#</th>
                                <th class="text-uppercase small fw-bold" style="width: 10%;">Acciones</th>
                                <th class="text-uppercase small fw-bold" style="width: 35%;">Laboratorio</th>
                                
                                
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($laboratorios as $laboratorio)
                            <tr>
                                <td class="small">{{ $loop->iteration }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary rounded-circle seleccionar-btn-laboratorio p-1" 
                                        data-id="{{ $laboratorio->id }}"
                                        data-nombre="{{ $laboratorio->nombre }}"
                                        title="Seleccionar">
                                        <i class="fas fa-check" style="font-size: 0.7rem;"></i>
                                    </button>
                                </td>
                                <td class="small fw-semibold">{{ $laboratorio->nombre }}</td>
                                
                               
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light justify-content-end">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
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
    
    .form-control, .form-select {
        border-radius: 0.375rem;
        transition: all 0.3s;
    }
    
    .form-control:focus, .form-select:focus {
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

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

// Agrega esto en tu sección de scripts
$(document).ready(function() {
    $('#form_compra').submit(function(e) {
        let todosTienenLote = true;
        let formularioValido = true;
        
        // Validar que todos los productos tengan lote seleccionado
        $('.select-lote').each(function() {
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
            Swal.fire('Error', 'Debes agregar al menos un producto', 'error');
            formularioValido = false;
        }
        
        // Validar laboratorio seleccionado
        if (!$('#id_laboratorio').val()) {
            Swal.fire('Error', 'Debes seleccionar un laboratorio', 'error');
            formularioValido = false;
        }
        
        if (!formularioValido) {
            e.preventDefault();
            if (!todosTienenLote) {
                Swal.fire('Error', 'Debes seleccionar un lote para cada producto', 'error');
            }
            return false;
        }
    });
});




// En tu sección de scripts
$(document).ready(function() {
    // Manejar cambio de selección de lote
    $(document).on('change', '.select-lote', function() {
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
    
    $('tbody tr').each(function() {
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
$(document).on('click', '.btn-agregar-lote', function() {
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
        
        Swal.fire('Éxito', 'Lote guardado correctamente', 'success');
    } else {
        Swal.fire('Error', response.message || 'Error al guardar el lote', 'error');
    }
}







// Reemplázalo por este código mejorado:
// En el formulario del modal (#formLote)
$('#formLote').submit(function(e) {
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
        success: function(response) {
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
                
                Swal.fire('Éxito', response.message, 'success');
            }
        },
        error: function(xhr) {
            let errorMessage = 'Error desconocido';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            Swal.fire('Error', errorMessage, 'error');
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
    document.getElementById('btnGuardarLote').addEventListener('click', function () {
        document.getElementById('formLote').submit();
    });

    
</script>



<script>
$(document).ready(function() {
    // [Mantener todos tus scripts originales aquí]
    // Seleccionar producto
    $(document).on('click', '.seleccionar-btn', function() {
        var idProducto = $(this).data('id');
        $('#codigo').val(idProducto);

        // Cerrar modal producto
        var modal = bootstrap.Modal.getInstance(document.getElementById('verModal'));
        if (modal) modal.hide();

        // Enfocar input
        setTimeout(() => { $('#codigo').focus(); }, 300);

        // Eliminar manualmente el backdrop si persiste
        const productosModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('productosModal'));
        productosModal.hide();


        // Limpiar posibles overlays
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    
    $('#codigo').focus();
        });

    // Seleccionar laboratorio
    $(document).on('click', '.seleccionar-btn-laboratorio', function() {
        var idLab = $(this).data('id');
        var nombreLab = $(this).data('nombre');

        $('#id_laboratorio').val(idLab);
        $('#nombre_laboratorio').val(nombreLab);

        // Cerrar modal laboratorio

        const laboratorioModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('laboratoriosModal'));
        laboratorioModal.hide();
       
        
            // Eliminar manualmente el backdrop si persiste
            $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');

                

    });
    
    // Eliminar producto temporal
    $('.delete-btn').click(function() {
        var id = $(this).data('id');
        if (id) {
            Swal.fire({
                title: '¿Eliminar producto?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{url('/admin/compras/create/tmp')}}/"+id,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token()}}',
                            _method: 'DELETE'
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    position: 'center',
                                    icon: 'success',
                                    title: 'Producto eliminado',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                setTimeout(() => { location.reload() }, 1500);
                            }
                        },
error: function (xhr) {
    let response = xhr.responseJSON;
    let errorMessage = 'Error desconocido';

    if (response) {
        if (response.error) {
            errorMessage = response.error;
        } else {
            // Si no hay campo error, convierte todo el objeto a texto
            errorMessage = JSON.stringify(response, null, 4);
        }
    }

    Swal.fire({
        title: 'Error al eliminar',
        html: '<pre style="text-align:left; white-space: pre-wrap;">' + errorMessage + '</pre>',
        icon: 'error',
        width: 600,
        confirmButtonText: 'Cerrar'
    });
}


                        
                    });
                }
            });
        }
    });

    // Búsqueda por código (Enter)
    $('#codigo').on('keypress', function(e) {
        if(e.which === 13) {
            e.preventDefault();
            var codigo = $(this).val();
            var cantidad = $('#cantidad').val();

            if(codigo.length > 0) {
                $.ajax({
                    url: "{{ route('admin.compras.tmp_compras')}}",
                    method: 'POST',
                    data: {
                        _token: '{{csrf_token()}}',
                        codigo: codigo,
                        cantidad: cantidad
                    },
                    success: function (response) {
                        if(response.success){
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: 'Producto agregado',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            setTimeout(() => { location.reload() }, 1500);
                        } else {
                            Swal.fire('Error', response.message || 'No se encontró el producto', 'error');
                        }
                    },
                    error: function(error) {
                        Swal.fire('Error', 'Ocurrió un error al procesar la solicitud', 'error');
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
@endsection



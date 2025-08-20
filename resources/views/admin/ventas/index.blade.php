@extends('layouts.app', ['title' => 'Historial de Ventas'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Gestion de Ventas'])
<div class="container-fluid mt--6">
    <!-- Card Superior - Encabezado y Estadísticas -->
    <div class="row mb-4">
    <div class="col">
        <!-- Tarjeta principal  -->
        <div class="card shadow-lg rounded-3 border-0 overflow-hidden">
            <!-- Encabezado con gradiente  -->
           <div class="card-header btn-white text-white py-3">
    <div class="row align-items-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <div>
                    <h4 class="mb-0 text-black">Panel de Ventas</h4>
                </div>
                
                
            </div>
        </div>
        
        <div class="col-md-4 text-end">
            @if(isset($cajaAbierto) && $cajaAbierto)
                <a href="{{ route('admin.ventas.create') }}" 
                   class="btn btn-sm rounded-pill px-3 shadow-sm border-0 bg-success text-white hover-scale">
                   <i class="fas fa-plus-circle me-1"></i> NUEVA VENTA
                </a>
            @else
                <a href="{{ route('admin.cajas.create') }}" 
                   class="btn btn-sm rounded-pill px-3 shadow-sm border-0 bg-danger text-white hover-scale">
                   <i class="fas fa-lock-open me-1"></i> ABRIR CAJA
                </a>
            @endif
        </div>
    </div>
</div>
</div>
<hr>
            <!-- Cuerpo de la tarjeta -->
            
                <!-- Estadísticas en formato moderno -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-3 h-100 hover-scale">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-receipt fs-2"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted mb-1 fs-7">Total Ventas</h6>
                                        <h2 class="mb-0 text-dark">{{ count($ventas) }}</h2>
                                        <p class="text-success mb-0 fs-8">
                                            <i class="fas fa-arrow-up me-1"></i> Resumen general
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-3 h-100 hover-scale">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-3 me-3 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-money-bill-wave fs-2"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted mb-1 fs-7">Ingresos Hoy</h6>
                                        <h2 class="mb-0 text-dark">
                                            Bs{{ number_format($ventas->filter(function($venta) {
                                                return \Carbon\Carbon::parse($venta->fecha)->isToday();
                                            })->sum('precio_total'), 2) }}
                                        </h2>
                                        
                                        <p class="text-success mb-0 fs-8">
                                            <i class="fas fa-calendar-day me-1"></i> Hoy
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-3 h-100 hover-scale">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-shape bg-info bg-opacity-10 text-info rounded-3 p-3 me-3 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-calendar-alt fs-2"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted mb-1 fs-7">Mes Actual</h6>
                                        <h2 class="mb-0 text-dark">Bs{{ number_format($ventas->whereBetween('fecha',
                                             [now()->startOfMonth(), now()->endOfMonth()])->sum('precio_total'), 2) }}</h2>
                                       
                                        <p class="text-primary mb-0 fs-8">
                                            <i class="fas fa-calendar me-1"></i> 
                                            {{ now()->locale('es')->isoFormat('MMMM YYYY') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Escuchar eventos de eliminación 
                        document.addEventListener('ventaEliminada', function() {
                            fetch('/actualizar-ventas-mes')
                                .then(response => response.json())
                                .then(data => {
                                    document.querySelector('#ventas-mes-card h2').textContent = 'Bs' + data.total.toFixed(2);
                                });
                        });
                    });
                    </script>
                </div>

                
           
        </div>
    </div>
</div>

<style>
    /* Efectos adicionales */
    .hover-scale {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-scale:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
    }
    .bg-gradient-primary {
        background: linear-gradient(135deg,rgb(221, 166, 121) 0%,rgb(240, 133, 72) 100%);
    }
    .bg-soft-light {
        background-color: #f8fafc;
    }
    .rounded-3 {
        border-radius: 0.75rem !important;
    }
    .fs-7 {
        font-size: 0.75rem !important;
    }
    .fs-8 {
        font-size: 0.65rem !important;
    }
</style>

    <!-- Card Inferior - Tabla de Ventas -->
    <div class="card shadow-lg rounded-3 border-0 overflow-hidden" style="max-width: 1020px; margin: 0 auto;">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0">
                        <i class="ni ni-bullet-list-67 me-2 text-primary"></i>Registro de Ventas
                    </h5>
                    <!-- Barra de acciones - Contenedor modificado -->
                <div class="d-flex gap-2 align-items-center ms-auto position-relative">
                    <button class="btn btn-sm btn-outline-secondary" id="refreshTable">
                        <i class="fas fa-sync-alt me-1"></i> Actualizar
                    </button>
                    
                    <!-- Dropdown mejorado -->
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                type="button" id="exportDropdown" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false">
                            <i class="fas fa-download me-1"></i> Exportar
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg" 
                            style="position: absolute; z-index: 1100;"
                            aria-labelledby="exportDropdown">
                            <li>
                                <a class="dropdown-item d-flex align-items-center" 
                                   href="{{ route('admin.ventas.reporte', ['tipo' => 'pdf']) }}?{{ http_build_query(request()->only(['fecha_inicio', 'fecha_fin', 'cliente_id'])) }}"
                                   target="_blank">
                                    <i class="fas fa-file-pdf text-danger me-2"></i> PDF
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" 
                                   href="{{ route('admin.ventas.reporte', ['tipo' => 'excel']) }}?{{ http_build_query(request()->only(['fecha_inicio', 'fecha_fin', 'cliente_id'])) }}">
                                    <i class="fas fa-file-excel text-success me-2"></i> Excel
                                </a>
                            </li>
                            
                        </ul>
                    </div>
                </div>
                </div>


                
                <div class="card-body px-0">
                    <div class="table-responsive">
                        <table id="ventasTabla" class="table table-hover align-items-center mb-0">
                            
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Detalles</th>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ventas as $index => $venta)
                                <tr>
                                    <td class="text-center align-middle">{{ $index + 1 }}</td>
                                    <td class="align-middle">
                                        <button class="btn btn-sm btn-outline-primary toggle-details" 
                                                data-target="#details-{{ $venta->id }}">
                                            <i class="fas fa-chevron-down mr-1"></i>
                                            Productos ({{ count($venta->detallesVenta) }})
                                        </button>
                                        <div id="details-{{ $venta->id }}" class="details-content bg-light rounded mt-2 p-3" style="display: none;">
                                            <table class="table table-sm table-hover mb-0">
                                                <thead>
                                                    <tr class="bg-gradient-primary text-white">
                                                        <th>Producto</th>
                                                        <th class="text-center">Cantidad</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($venta->detallesVenta as $detalle)
                                                    <tr>
                                                        <td>{{ $detalle->producto->nombre }}</td>
                                                        <td class="text-center">{{ $detalle->cantidad }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                    <td class="align-middle">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                                    <td class="align-middle text-success font-weight-bold">Bs{{ number_format($venta->precio_total, 2) }}</td>
                                    <td class="text-center align-middle">
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ url('/admin/ventas', $venta->id) }}" 
                                              class="btn btn-sm bg-gradient-success text-white mx-1 d-flex align-items-center justify-content-center"
                                                style="width: 30px; height: 30px; min-width: 30px; padding: 0;"
                                               title="Ver"
                                               data-toggle="tooltip">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                           


                                            
<form action="{{ route('admin.ventas.destroy', $venta->id) }}" 
      method="POST" 
      class="d-inline eliminar-venta-form"
      data-id="{{ $venta->id }}"
      data-nombre="{{ $venta->nombre }}">
    @csrf
    @method('DELETE')
    <button type="button"  
            class="btn btn-sm bg-gradient-danger text-white mx-1 btn-eliminar-venta"
            style="width: 30px; height: 30px; min-width: 30px; padding: 0;"
            title="Eliminar venta"
            data-bs-toggle="tooltip">
            <span class="btn-inner--icon me-1">
                <i class="fas fa-trash-alt"></i>
            </span>
            <span class="btn-inner--text"></span>
    </button>



</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmarEliminacionSucursal(event) {
    event.preventDefault();
    const form = event.target.closest('form');
    const venta = JSON.parse(form.dataset.venta || '{}');
    
    Swal.fire({
        title: `<span class="swal2-title">Confirmar Eliminación</span>`,
        html: `<div class="swal2-content-container">
                 
                 <div class="swal2-text-content">
                     <h3 class="swal2-subtitle">¿Eliminar venta permanentemente?</h3>
                     <div class="swal2-user-info mt-3">
                         <i></i> ${venta.nombre || 'Esta venta'}
                     </div>
                     <div class="swal2-warning-text">
                         <i class="fas fa-exclamation-triangle me-2"></i>
                         Esta acción no se puede deshacer
                     </div>
                 </div>
               </div>`,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: `<i class="fas fa-trash-alt me-2"></i> Confirmar Eliminación`,
        cancelButtonText: `<i class="fas fa-times me-2"></i> Cancelar`,
        buttonsStyling: false,
        customClass: {
            popup: 'swal2-container-premium',
            confirmButton: 'swal2-confirm-btn-premium',
            cancelButton: 'swal2-cancel-btn-premium',
            actions: 'swal2-actions-premium'
        },
        background: 'rgba(255,255,255,0.98)',
        showClass: {
            popup: 'animate__animated animate__zoomIn animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__zoomOut animate__faster'
        },
        allowOutsideClick: false,
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Procesando...',
                html: `<div class="swal2-loader-container">
                         <div class="swal2-loader-circle"></div>
                         <div class="swal2-loader-bar-container">
                             <div class="swal2-loader-bar"></div>
                         </div>
                       </div>`,
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    const loaderBar = document.querySelector('.swal2-loader-bar');
                    loaderBar.style.width = '100%';
                    loaderBar.style.transition = 'width 1s ease-in-out';
                }
            });
            
            setTimeout(() => {
                form.submit();
            }, 1200);
        }
    });
}

document.querySelectorAll('.btn-eliminar-venta').forEach(button => {
    button.addEventListener('click', confirmarEliminacionSucursal);
});
</script>

<style>
    /* Estilos Premium */
    .swal2-container-premium {
        border-radius: 18px !important;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.18) !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        max-width: 480px !important;
        padding: 2.5rem !important;
    }

    .swal2-icon-wrapper {
        text-align: center;
        margin: 1.5rem 0;
    }

    .swal2-icon-svg {
        width: 72px;
        height: 72px;
        opacity: 0.9;
    }

    .swal2-content-container {
        text-align: center;
        padding: 0 1.5rem;
    }

    .swal2-title {
        font-size: 1.8rem !important;
        font-weight: 600 !important;
        color: #2f3542 !important;
        letter-spacing: -0.5px;
        margin-bottom: 0 !important;
    }

    .swal2-subtitle {
        font-size: 1.25rem;
        color: #57606f;
        font-weight: 500;
        margin: 1rem 0;
    }

    .swal2-user-info {
        background: #f8f9fa;
        padding: 0.75rem;
        border-radius: 10px;
        font-size: 1.1rem;
        color: #2f3542;
        border-left: 4px solid #ff4757;
    }

    .swal2-warning-text {
        font-size: 0.95rem;
        color: #ff6b81;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px dashed #dfe4ea;
    }

    .swal2-confirm-btn-premium {
        background: linear-gradient(135deg, #ff4757, #ff6b81) !important;
        border: none !important;
        padding: 12px 28px !important;
        font-weight: 600 !important;
        font-size: 1rem !important;
        border-radius: 10px !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(255, 71, 87, 0.25) !important;
        transition: all 0.3s ease !important;
    }

    .swal2-confirm-btn-premium:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 16px rgba(255, 71, 87, 0.3) !important;
    }

    .swal2-cancel-btn-premium {
        background: white !important;
        border: 1px solid #dfe4ea !important;
        padding: 12px 28px !important;
        font-weight: 500 !important;
        font-size: 1rem !important;
        border-radius: 10px !important;
        color: #57606f !important;
        transition: all 0.3s ease !important;
    }

    .swal2-cancel-btn-premium:hover {
        background: #f8f9fa !important;
        border-color: #ced6e0 !important;
    }

    .swal2-actions-premium {
        margin: 2rem 0 0 0 !important;
        gap: 1rem !important;
    }

    /* Loader premium */
    .swal2-loader-container {
        width: 100%;
        padding: 1.5rem 0;
    }

    .swal2-loader-circle {
        width: 60px;
        height: 60px;
        border: 4px solid rgba(255, 71, 87, 0.2);
        border-top-color: #ff4757;
        border-radius: 50%;
        margin: 0 auto 1.5rem;
        animation: swal2-spin 1s linear infinite;
    }

    .swal2-loader-bar-container {
        width: 100%;
        height: 6px;
        background: rgba(255, 71, 87, 0.1);
        border-radius: 3px;
        overflow: hidden;
    }

    .swal2-loader-bar {
        height: 100%;
        width: 0;
        background: linear-gradient(90deg, #ff4757, #ff6b81);
        border-radius: 3px;
    }

    @keyframes swal2-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
                                        </div>
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
</div>
</div>




<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection

@section('css')
<style>
    .card-stats {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-stats:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .details-content {
        transition: all 0.3s ease;
    }
    .toggle-details .fa-chevron-down {
        transition: transform 0.3s ease;
    }
    .toggle-details.collapsed .fa-chevron-down {
        transform: rotate(-90deg);
    }
    .table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-top: none;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(59, 130, 246, 0.05);
    }
    .btn-sm {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection

@section('js')


<script>
    $(document).ready(function() {
        // Inicializar DataTable
        $('#ventasTabla').DataTable({
            "pageLength": 10,
            "responsive": true,
            "autoWidth": false,
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "No hay registros disponibles",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "search": "<i class='fas fa-search'></i> Buscar:",
                "paginate": {
                    "first": "<i class='fas fa-angle-double-left'></i>",
                    "last": "<i class='fas fa-angle-double-right'></i>",
                    "next": "<i class='fas fa-angle-right'></i>",
                    "previous": "<i class='fas fa-angle-left'></i>"
                }
            },
            "columnDefs": [
                { "orderable": false, "targets": [1, 4] },
                { "responsivePriority": 1, "targets": 0 },
                { "responsivePriority": 2, "targets": -1 }
            ]
        });

        // Manejar el clic en los botones de detalles
        $('.toggle-details').click(function() {
            const target = $(this).data('target');
            const content = $(target);
            
            // Cerrar todos los demás detalles abiertos
            $('.details-content').not(content).slideUp();
            $('.toggle-details').not(this).removeClass('collapsed');
            
            // Alternar el contenido actual
            content.slideToggle();
            $(this).toggleClass('collapsed');
        });

        // Inicializar tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>


@endsection



















                       
                    
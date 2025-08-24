@extends('layouts.argon')

@section('content')


    <div class="container-fluid mt--6">
        <!-- Card de Encabezado y Botones -->
        <div class="row mb-3"> <!-- Reducido mb-4 a mb-3 -->
            <div class="col">
                <div class="card border-0">
                    <div class="card-header bg-white border-0 py-2"> <!-- Reducido padding vertical -->
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h3 class="mb-0 h5"> <!-- Cambiado a h5 para menos altura -->
                                    <i class="fas fa-history me-2"></i> Historial de Compras
                                </h3>
                            </div>
                            <div class="col-md-6 text-end">
                                @if(isset($cajaAbierto) && $cajaAbierto)
                                    <a href="{{ url('/admin/compras/create') }}" class="btn btn-success btn-lg"
                                        style="border-radius: 50px; padding: 8px 20px;">
                                        <i class="fas fa-plus-circle"></i> REGISTRAR COMPRA
                                    </a>
                                @else
                                    <a href="{{ url('/admin/cajas/create') }}" class="btn btn-danger btn-lg"
                                        style="border-radius: 50px; padding: 8px 20px;">
                                        <i class="fas fa-lock-open"></i> ABRIR CAJA
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body py-2"> <!-- Reducido padding vertical -->
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="mb-0 small">Total: <span class="badge bg-primary">{{ count($compras) }}</span></p>
                            <!-- Texto más pequeño -->
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-secondary px-2" id="refreshTable">
                                    <!-- Padding horizontal reducido -->
                                    <i class="fas fa-sync-alt me-1"></i> Actualizar
                                </button>
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-outline-primary  dropdown-toggle" type="button"
                                        id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                        data-bs-toggle="tooltip" title="Exportar datos">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center"
                                                href="{{ route('admin.compras.reporte', ['tipo' => 'pdf']) }}?fecha_inicio={{ request('fecha_inicio') }}&fecha_fin={{ request('fecha_fin') }}&laboratorio_id={{ request('laboratorio_id') }}"
                                                target="_blank">
                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                                <span>Exportar a PDF</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center"
                                                href="{{ route('admin.compras.reporte', ['tipo' => 'excel']) }}?fecha_inicio={{ request('fecha_inicio') }}&fecha_fin={{ request('fecha_fin') }}&laboratorio_id={{ request('laboratorio_id') }}">
                                                <i class="fas fa-file-excel text-success me-2"></i>
                                                <span>Exportar a Excel</span>
                                            </a>
                                        </li>

                                    </ul>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card de Tabla de Compras -->
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                        <h5 class="mb-0 ">

                            <i class="ni ni-bullet-list-67 me-2 text-primary"></i></i> Registro de Compras
                        </h5>

                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="comprasTable" class="table table-hover align-items-center mb-0">

                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Detalles</th>
                                        <th class="text-center">Fecha</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Comprobante</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($compras as $index => $compra)
                                        <tr>
                                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                                            <td class="align-middle">
                                                <button class="btn btn-sm btn-outline-primary toggle-details"
                                                    data-target="#details-{{ $compra->id }}">
                                                    <i class="fas fa-chevron-down me-1"></i>
                                                    Ver productos ({{ count($compra->detalles) }})
                                                </button>
                                                <div id="details-{{ $compra->id }}" class="details-content"
                                                    style="display: none;">
                                                    <div class="mt-3 bg-soft-info p-3 rounded">
                                                        <table class="table table-sm" style="font-size: 0.85rem;">
                                                            <thead class="bg-gradient-primary text-white">
                                                                <tr>
                                                                    <th style="padding: 0.3rem;">Producto</th>
                                                                    <th class="text-center" style="padding: 0.3rem;">Cantidad
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($compra->detalles as $detalle)
                                                                    <tr>
                                                                        <td style="padding: 0.3rem;">
                                                                            {{ $detalle->producto->nombre }}
                                                                        </td>
                                                                        <td class="text-center" style="padding: 0.3rem;">
                                                                            {{ $detalle->cantidad }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                {{ \Carbon\Carbon::parse($compra->fecha)->format('d/m/Y') }}
                                            </td>
                                            <td class="align-middle text-success fw-bold">
                                                Bs {{ number_format($compra->precio_total, 0, '.', ) }}

                                            </td>
                                            <td class="align-middle">
                                                <span class="badge bg-primary">{{ $compra->comprobante }}</span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="d-flex justify-content-center">
                                                    <!--ver detalle-->
                                                    <a href="{{ url('/admin/compras', $compra->id) }}"
                                                        class="btn btn-sm bg-gradient-success text-white mx-1 d-flex align-items-center justify-content-center"
                                                        style="width: 30px; height: 30px; min-width: 30px; padding: 0;"
                                                        title="Ver detalles" data-bs-toggle="tooltip">
                                                        <i class="fas fa-eye"></i>
                                                    </a>





                                                    <form action="{{ route('admin.compras.destroy', $compra->id) }}"
                                                        method="POST" class="d-inline eliminar-compra-form"
                                                        data-id="{{ $compra->id }}" data-nombre="{{ $compra->nombre }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="btn btn-sm bg-gradient-danger text-white mx-1 btn-eliminar-compra"
                                                            style="width: 30px; height: 30px; min-width: 30px; padding: 0;"
                                                            title="Eliminar compra" data-bs-toggle="tooltip">
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
                                                            const compra = JSON.parse(form.dataset.compra || '{}');

                                                            Swal.fire({
                                                                title: `<span class="swal2-title">Confirmar Eliminación</span>`,
                                                                html: `<div class="swal2-content-container">

                                                     <div class="swal2-text-content">
                                                         <h3 class="swal2-subtitle">¿Eliminar compra permanentemente?</h3>
                                                         <div class="swal2-user-info mt-3">
                                                             <i></i> ${compra.nombre || 'Esta compra'}
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

                                                        document.querySelectorAll('.btn-eliminar-compra').forEach(button => {
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
                                                            0% {
                                                                transform: rotate(0deg);
                                                            }

                                                            100% {
                                                                transform: rotate(360deg);
                                                            }
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


    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('css')
    <style>
        .bg-soft-info {
            background-color: rgba(23, 162, 184, 0.1);
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-details .fa-chevron-down {
            transition: transform 0.3s ease;
        }

        .toggle-details.collapsed .fa-chevron-down {
            transform: rotate(-90deg);
        }

        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }

            #comprasTable tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #dee2e6;
                border-radius: 0.375rem;
            }

            #comprasTable tbody td {
                display: block;
                text-align: right;
                padding: 0.75rem;
                border-top: none;
                border-bottom: 1px solid #f1f1f1;
            }

            #comprasTable tbody td::before {
                content: attr(data-label);
                float: left;
                font-weight: bold;
                text-transform: uppercase;
                font-size: 0.8rem;
                color: #525f7f;
            }

            #comprasTable tbody td:last-child {
                border-bottom: none;
            }
        }
    </style>
@endsection

@section('js')

    <script src="{{ asset('argon/js/argon-dashboard.js') }}"></script>
    <script src="{{ asset('vendor/argon-dashboard/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('vendor/argon-dashboard/js/plugins/dataTables/dataTables.bootstrap5.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar DataTable
            const table = $('#comprasTable').DataTable({
                "language": {

                    "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
                },
                "responsive": true,
                "autoWidth": false,
                "drawCallback": function (settings) {
                    if (window.innerWidth < 768) {
                        $('#comprasTable tbody td').each(function () {
                            var header = $('#comprasTable thead th').eq($(this).index()).text();
                            $(this).attr('data-label', header);
                        });
                    }
                }
            });

            // Manejar el clic en los botones de detalles
            document.querySelectorAll('.toggle-details').forEach(button => {
                button.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-target');
                    const detailsContent = document.querySelector(targetId);
                    const isCollapsed = this.classList.contains('collapsed');

                    // Cerrar todos los demás detalles abiertos
                    document.querySelectorAll('.details-content').forEach(content => {
                        if (content.id !== targetId.replace('#', '')) {
                            content.style.display = 'none';
                            const otherButton = content.closest('tr').querySelector('.toggle-details');
                            otherButton.classList.remove('collapsed');
                        }
                    });

                    // Alternar el contenido actual
                    if (isCollapsed) {
                        detailsContent.style.display = 'none';
                        this.classList.remove('collapsed');
                    } else {
                        detailsContent.style.display = 'block';
                        this.classList.add('collapsed');
                    }
                });
            });

            // Botón de actualizar
            document.getElementById('refreshTable').addEventListener('click', function () {
                window.location.reload();
            });

            // Botón de exportar (ejemplo básico)
            document.getElementById('exportExcel').addEventListener('click', function () {
                // Aquí puedes implementar la lógica de exportación a Excel
                alert('Funcionalidad de exportación a Excel');
            });
        });
    </script>
@endsection
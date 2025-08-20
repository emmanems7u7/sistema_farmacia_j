@extends('layouts.argon')

@section('content')

    <div class="container-fluid py-4">
        <!-- Header Principal -->



        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center bg-white">
                    <div class="d-flex align-items-center">

                        <h5 class="mb-0">


                            <i class="fas fa-flask  me-3 text-primary"></i>
                            <strong>GESTION DE LABORATORIOS</strong>
                        </h5>
                    </div>

                    <div class="d-flex align-items-center">
                        <span class="badge bg-gradient-info me-3">


                            <i class="fas fa-database me-1"></i> {{ $laboratorios->count() }} Laboratorios
                        </span>

                        <div class="dropdown me-2">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="exportDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false"
                                title="Exportar reporte en diferentes formatos">
                                <i class="fas fa-download me-1"></i> Exportar
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.laboratorios.reporte') }}?tipo=pdf"
                                        title="Exportar a PDF" target="_blank">
                                        <i class="fas fa-file-pdf text-danger me-2"></i> PDF
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.laboratorios.reporte') }}?tipo=excel"
                                        title="Exportar a Excel">
                                        <i class="fas fa-file-excel text-success me-2"></i> Excel
                                    </a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                            </ul>
                        </div>

                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#modalCrear">
                            <i class="fas fa-plus-circle me-1"></i> Nuevo
                        </button>
                    </div>
                </div>
            </div>
            <hr>

            <!-- Tarjeta de lista de laboratorios -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-radius-lg shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                            <h5 class="mb-0 text-black">
                                <i class="fas fa-list-check me-2 text-primary"></i>
                                <strong>Laboratorios Registrados</strong>
                            </h5>



                        </div>

                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table id="laboratorios-table" class="table align-items-center mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xs font-weight-bolder">
                                                #</th>
                                            <th class="text-uppercase text-secondary text-xs font-weight-bolder">Laboratorio
                                            </th>
                                            <th class="text-uppercase text-secondary text-xs font-weight-bolder">Teléfono
                                            </th>
                                            <th class="text-uppercase text-secondary text-xs font-weight-bolder">Dirección
                                            </th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xs font-weight-bolder">
                                                Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($laboratorios as $laboratorio)
                                                                    <tr>
                                                                        <td class="text-center align-middle">
                                                                            <span
                                                                                class="text-secondary text-xs font-weight-bold">{{ $loop->iteration }}</span>
                                                                        </td>
                                                                        <td class="align-middle">
                                                                            <div class="d-flex align-items-center">
                                                                                <span class="badge bg-gradient-info rounded-circle me-2 p-2">
                                                                                    <i class="fas fa-flask"></i>
                                                                                </span>
                                                                                <span
                                                                                    class="text-dark text-sm font-weight-bold">{{ $laboratorio->nombre }}</span>
                                                                            </div>
                                                                        </td>
                                                                        <td class="align-middle">
                                                                            <span class="text-dark text-sm">
                                                                                <i class="fas fa-phone me-1 text-primary"></i>
                                                                                {{ $laboratorio->telefono }}
                                                                            </span>
                                                                        </td>
                                                                        <td class="align-middle">
                                                                            <span class="text-dark text-sm">
                                                                                <i class="fas fa-map-marker-alt me-1 text-primary"></i>
                                                                                {{ $laboratorio->direccion }}
                                                                            </span>
                                                                        </td>
                                                                        <td class="text-center align-middle">
                                                                            <div class="d-flex justify-content-center">
                                                                                <div class="d-inline-flex gap-2">
                                                                                    <!-- Botón Editar -->
                                                                                    <button type="button"
                                                                                        class="btn btn-sm bg-gradient-success text-white mx-1"
                                                                                        data-bs-toggle="modal"
                                                                                        style="width: 30px; height: 30px; min-width: 30px; padding: 0;"
                                                                                        data-bs-target="#editModal{{ $laboratorio->id }}"
                                                                                        title="Editar laboratorio">
                                                                                        <i class="fas fa-pen"></i>
                                                                                    </button>

                                                                                    <!-- Botón Eliminar -->



                                                                                    <form
                                                                                        action="{{ route('admin.laboratorios.destroy', $laboratorio->id) }}"
                                                                                        method="POST" class="d-inline"
                                                                                        data-laboratorio='{"nombre":"{{ $laboratorio->nombre }}"}'>
                                                                                        @csrf
                                                                                        @method('DELETE')
                                                                                        <button type="button"
                                                                                            class="btn btn-sm bg-gradient-danger text-white  mx-1  btn-eliminar-laboratorio"
                                                                                            style="width: 30px; height: 30px; min-width: 30px; padding: 0;"
                                                                                            title="Eliminar laboratorio" data-bs-toggle="tooltip">
                                                                                            <span class="btn-inner--icon me-1">
                                                                                                <i class="fas fa-trash-alt"></i>
                                                                                            </span>

                                                                                        </button>
                                                                                    </form>
                                                                                </div>
                                                                                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                                                <script>
                                                                                    function confirmarEliminacionSucursal(event) {
                                                                                        event.preventDefault();
                                                                                        const form = event.target.closest('form');
                                                                                        const laboratorio = JSON.parse(form.dataset.laboratorio || '{}');

                                                                                        Swal.fire({
                                                                                            title: `<span class="swal2-title">Confirmar Eliminación</span>`,
                                                                                            html: `<div class="swal2-content-container">

                                                 <div class="swal2-text-content">
                                                     <h3 class="swal2-subtitle">¿Eliminar laboratorio permanentemente?</h3>
                                                     <div class="swal2-user-info mt-3">
                                                         <i></i> ${laboratorio.nombre || 'Este laboratorio'}
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

                                                                                    document.querySelectorAll('.btn-eliminar-laboratorio').forEach(button => {
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

        <!-- Modal para Crear Nuevo Laboratorio -->
        <div class="modal fade" id="modalCrear" tabindex="-1" aria-labelledby="modalCrearLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-gradient-primary text-white">
                        <h5 class="modal-title text-white" id="modalCrearLabel">
                            <i class="fas fa-plus-circle me-2 text-white"></i> Nuevo Laboratorio
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ url('/admin/laboratorios/create') }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label class="form-label">Nombre del Laboratorio</label>
                                <div class="input-group input-group-outline">
                                    <span class="input-group-text"><i class="fas fa-flask"></i></span>
                                    <input type="text" class="form-control" name="nombre" value="{{ old('nombre') }}"
                                        required placeholder="Ej: Laboratorio Clínico Central">
                                </div>
                                @error('nombre')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Teléfono</label>
                                <div class="input-group input-group-outline">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" class="form-control" name="telefono" value="{{ old('telefono') }}"
                                        required placeholder=".">
                                </div>
                                @error('telefono')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Dirección</label>
                                <div class="input-group input-group-outline">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    <input type="text" class="form-control" name="direccion" value="{{ old('direccion') }}"
                                        required placeholder="Ej: Av. Montes #123">
                                </div>
                                @error('direccion')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn bg-gradient-primary">
                                <i class="fas fa-save me-1"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modales de Edición (generados dinámicamente) -->
        @foreach($laboratorios as $laboratorio)
            <div class="modal fade" id="editModal{{ $laboratorio->id }}" tabindex="-1"
                aria-labelledby="editModalLabel{{ $laboratorio->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-gradient-success text-white">
                            <h5 class="modal-title" id="editModalLabel{{ $laboratorio->id }}">
                                <i class="fas fa-edit me-2"></i> Editar Laboratorio
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form action="{{ url('/admin/laboratorios', $laboratorio->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="form-group mb-3">
                                    <label class="form-label">Nombre</label>
                                    <div class="input-group input-group-outline">
                                        <span class="input-group-text"><i class="fas fa-flask"></i></span>
                                        <input type="text" class="form-control" name="nombre"
                                            value="{{ old('nombre', $laboratorio->nombre) }}" required>
                                    </div>
                                    @error('nombre')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <div class="input-group input-group-outline">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="text" class="form-control" name="telefono"
                                            value="{{ old('telefono', $laboratorio->telefono) }}" required>
                                    </div>
                                    @error('telefono')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Dirección</label>
                                    <div class="input-group input-group-outline">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        <input type="text" class="form-control" name="direccion"
                                            value="{{ old('direccion', $laboratorio->direccion) }}" required>
                                    </div>
                                    @error('direccion')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </button>
                                <button type="submit" class="btn bg-gradient-success text-white">
                                    <i class="fas fa-save me-1"></i> Actualizar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

    @push('css')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <style>
            .card {
                border: none;
                border-radius: 12px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                transition: all 0.3s ease;
            }

            .card:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }

            .card-header {
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            }

            .bg-light {
                background-color: #f8fafc !important;
            }

            .bg-gray-100 {
                background-color: #f8f9fa !important;
            }

            .table th {
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                font-size: 0.75rem;
                color: #6c757d;
            }

            .table td {
                vertical-align: middle;
                padding: 1rem;
            }

            .input-group-outline {
                border-radius: 8px;
                border: 1px solid #dee2e6;
                transition: border-color 0.15s ease-in-out;
            }

            .input-group-outline:focus-within {
                border-color: #5e72e4;
                box-shadow: 0 0 0 0.2rem rgba(94, 114, 228, 0.25);
            }

            .input-group-text {
                background-color: transparent;
                border-right: none;
            }

            .form-control {
                border-left: none;
                background-color: transparent;
            }

            .bg-gradient-primary {
                background: linear-gradient(135deg, #5e72e4 0%, #825ee4 100%) !important;
            }

            .bg-gradient-info {
                background: linear-gradient(135deg, #11cdef 0%, #1171ef 100%) !important;
            }

            .bg-gradient-danger {
                background: linear-gradient(135deg, #f5365c 0%, #f56036 100%) !important;
            }

            .btn-sm {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
                line-height: 1.5;
                border-radius: 0.375rem;
            }

            .modal-content {
                border: none;
                border-radius: 12px;
            }

            .modal-header {
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .text-xs {
                font-size: 0.75rem;
            }

            .text-sm {
                font-size: 0.875rem;
            }

            .border-radius-lg {
                border-radius: 0.5rem;
            }
        </style>
    @endpush

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function () {
                // Configuración de DataTables
                $('#laboratorios-table').DataTable({
                    "pageLength": 10,
                    "responsive": true,
                    "autoWidth": false,
                    "language": {
                        "lengthMenu": "Mostrar _MENU_ registros por página",
                        "zeroRecords": "No se encontraron laboratorios",
                        "info": "Mostrando página _PAGE_ de _PAGES_",
                        "infoEmpty": "No hay laboratorios registrados",
                        "infoFiltered": "(filtrado de _MAX_ registros totales)",
                        "search": "Buscar:",
                        "paginate": {
                            "first": "Primero",
                            "last": "Último",
                            "next": "Siguiente",
                            "previous": "Anterior"
                        }
                    },
                    "dom": '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
                    "initComplete": function () {
                        $('.dataTables_filter input').addClass('form-control').attr('placeholder', 'Buscar laboratorio...');
                        $('.dataTables_length select').addClass('form-select');
                    }
                });

                // Confirmación antes de eliminar con SweetAlert2
                $('form[method="DELETE"]').on('submit', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡No podrás revertir esta acción!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#5e72e4',
                        cancelButtonColor: '#f5365c',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });
        </script>
    @endpush
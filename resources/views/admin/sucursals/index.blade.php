@extends('layouts.app', ['title' => 'Sucursales'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Sucursales'])

    <div class="container-fluid py-4">
        <!-- Primera tarjeta: Título y botón -->
        <div class="row">
            <!-- Card de Encabezado -->
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center bg-white">
                        <div class="d-flex align-items-center">

                            <h5 class="mb-0">
                                <i class="ni ni-shop me-3 text-primary"></i>
                                <strong>GESTION SUCURSALES</strong>
                            </h5>
                        </div>

                        <div class="d-flex align-items-center">
                            <span class="badge bg-gradient-info me-3">
                                {{ count($sucursals) }} sucursales
                            </span>

                            <div class="dropdown me-2">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                    id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                    title="Exportar reporte en diferentes formatos">
                                    <i class="fas fa-download me-1"></i> Exportar
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.sucursals.reporte') }}?tipo=pdf"
                                            title="Exportar a PDF" target="_blank">
                                            <i class="fas fa-file-pdf text-danger me-2"></i> PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.sucursals.reporte') }}?tipo=excel"
                                            title="Exportar a Excel">
                                            <i class="fas fa-file-excel text-success me-2"></i> Excel
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.sucursals.reporte') }}?tipo=csv"
                                            title="Exportar a CSV">
                                            <i class="fas fa-file-csv text-info me-2"></i> CSV
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
            </div>








            <!-- Segunda tarjeta: Tabla de sucursales -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4 border-radius-lg shadow">
                        <div class="card-header pb-0">
                            <h6 class="mb-0">
                                <i class="ni ni-bullet-list-67 me-2 text-primary"></i>
                                <strong>Listado de Sucurles</strong>
                            </h6>
                        </div>

                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                style="text-align: center">Nro</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                style="text-align: center">Imagen</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                style="text-align: center">Nombre</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                style="text-align: center">Correo</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                style="text-align: center">Dirección</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                style="text-align: center">Teléfono</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                style="text-align: center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $contador = 1; @endphp
                                        @foreach($sucursals as $sucursal)
                                                                    <tr>
                                                                        <td style="text-align: center; vertical-align: middle">{{ $contador++ }}</td>
                                                                        <td style="text-align: center; vertical-align: middle">
                                                                            @if($sucursal->imagen)
                                                                                <img src="{{ asset('storage/' . $sucursal->imagen) }}"
                                                                                    class="avatar avatar-sm me-3 border-radius-lg" alt="Imagen Sucursal">
                                                                            @else
                                                                                <span class="badge bg-gradient-secondary">Sin imagen</span>
                                                                            @endif
                                                                        </td>
                                                                        <td style="text-align: center; vertical-align: middle">{{ $sucursal->nombre }}
                                                                        </td>
                                                                        <td style="text-align: center; vertical-align: middle">{{ $sucursal->email }}
                                                                        </td>
                                                                        <td style="text-align: center; vertical-align: middle">
                                                                            {{ $sucursal->direccion }}</td>
                                                                        <td style="text-align: center; vertical-align: middle">{{ $sucursal->telefono }}
                                                                        </td>
                                                                        <td style="text-align: center; vertical-align: middle">
                                                                            <div class="btn-group" role="group">
                                                                                <div class="btn-group" role="group">
                                                                                    <!-- Botón Editar - Verde con icono -->
                                                                                    <button type="button"
                                                                                        class="btn btn-sm bg-gradient-success text-white mx-1"
                                                                                        data-bs-toggle="modal"
                                                                                        style="width: 30px; height: 30px; min-width: 30px; padding: 0;"
                                                                                        data-bs-target="#editModal{{ $sucursal->id }}"
                                                                                        title="Editar sucursal" data-bs-toggle="tooltip">
                                                                                        <span class="btn-inner--icon me-1">
                                                                                            <i class="fas fa-edit"></i>
                                                                                        </span>

                                                                                    </button>



                                                                                    <!-- Botón Eliminar - Rojo con icono -->
                                                                                    <form action="{{ route('admin.sucursals.destroy', $sucursal->id) }}"
                                                                                        method="POST" class="d-inline"
                                                                                        data-sucursal='{"nombre":"{{ $sucursal->nombre }}"}'>
                                                                                        @csrf
                                                                                        @method('DELETE')
                                                                                        <button type="button"
                                                                                            class="btn btn-sm bg-gradient-danger text-white mx-1 btn-eliminar-sucursal"
                                                                                            style="width: 30px; height: 30px; min-width: 30px; padding: 0;"
                                                                                            title="Eliminar sucursal" data-bs-toggle="tooltip">
                                                                                            <span class="btn-inner--icon me-1">
                                                                                                <i class="fas fa-trash-alt"></i>
                                                                                            </span>

                                                                                        </button>


                                                                                    </form>

                                                                                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                                                    <script>
                                                                                        function confirmarEliminacionSucursal(event) {
                                                                                            event.preventDefault();
                                                                                            const form = event.target.closest('form');
                                                                                            const sucursal = JSON.parse(form.dataset.sucursal || '{}');

                                                                                            Swal.fire({
                                                                                                title: `<span class="swal2-title">Confirmar Eliminación</span>`,
                                                                                                html: `<div class="swal2-content-container">

                                                 <div class="swal2-text-content">
                                                     <h3 class="swal2-subtitle">¿Eliminar sucursal permanentemente?</h3>
                                                     <div class="swal2-user-info mt-3">
                                                         <i class="fas fa-store me-2"></i> ${sucursal.nombre || 'Esta sucursal'}
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

                                                                                        document.querySelectorAll('.btn-eliminar-sucursal').forEach(button => {
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

        <!-- Modal para Crear Nueva Sucursal -->
        <div class="modal fade" id="modalCrear" tabindex="-1" role="dialog" aria-labelledby="modalCrearLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <!-- Encabezado del Modal -->
                    <div class="modal-header bg-gradient-primary text-white py-3">
                        <div class="d-flex align-items-center w-100">
                            <!-- Icono simple y bien alineado -->
                            <i class="ni ni-shop fs-4 me-3"></i>

                            <!-- Texto del título -->
                            <div class="flex-grow-1">
                                <h5 class="modal-title mb-0">
                                    <strong>Registrar Nueva Sucursal</strong>
                                </h5>
                                <p class="small mb-0">Complete todos los campos requeridos</p>
                            </div>

                            <!-- Botón de cerrar -->
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <form action="{{ route('admin.sucursals.store') }}" method="POST" enctype="multipart/form-data"
                        id="formCrearSucursal">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <!-- Columna Izquierda -->
                                <div class="col-md-6">
                                    <!-- Campo de Imagen con Preview -->
                                    <div class="mb-4">
                                        <label for="imagen" class="form-label fw-bold">Logo de Sucursal</label>
                                        <div class="file-upload-wrapper">
                                            <input type="file" class="form-control file-upload-input" id="imagen"
                                                name="imagen" accept=".jpg, .jpeg, .png" required>
                                            <div class="file-upload-preview mt-3 text-center">
                                                <div class="image-preview-container rounded border p-2 bg-light">
                                                    <img id="imagePreview" src="#" alt="Vista previa de imagen"
                                                        class="img-fluid d-none" style="max-height: 150px;">
                                                    <div class="no-preview-text text-muted py-4">
                                                        <i class="ni ni-image fs-1"></i>
                                                        <p class="small mb-0">Vista previa aparecerá aquí</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Formatos aceptados: JPG, JPEG, PNG (Max.
                                                2MB)</small>
                                            @error('imagen')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Campo de Teléfono -->
                                    <div class="mb-3">
                                        <label for="telefono" class="form-label fw-bold">Teléfono</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ni ni-mobile-button"></i></span>
                                            <input type="text" class="form-control" id="telefono" name="telefono"
                                                value="{{ old('telefono') }}" placeholder="Ej: +591-71234567" required>
                                        </div>
                                        @error('telefono')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Columna Derecha -->
                                <div class="col-md-6">
                                    <!-- Campo de Nombre -->
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label fw-bold">Nombre de Sucursal</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ni ni-shop"></i></span>
                                            <input type="text" class="form-control" id="nombre" name="nombre"
                                                value="{{ old('nombre') }}" placeholder="Ej: Sucursal Centro" required>
                                        </div>
                                        @error('nombre')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Campo de Email -->
                                    <div class="mb-3">
                                        <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                            <input type="email" class="form-control" id="email" name="email"
                                                value="{{ old('email') }}" placeholder="Ej: sucursal@gmail.com" required>
                                        </div>
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Campo de Dirección -->
                                    <div class="mb-3">
                                        <label for="direccion" class="form-label fw-bold">Dirección Completa</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ni ni-pin-3"></i></span>
                                            <textarea class="form-control" id="direccion" name="direccion" rows="2"
                                                required>{{ old('direccion') }}</textarea>
                                        </div>
                                        @error('direccion')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pie del Modal -->
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="ni ni-fat-remove me-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ni ni-check-bold me-1"></i> Registrar Sucursal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Script para vista previa de imagen -->
        <script>
            document.getElementById('imagen').addEventListener('change', function (e) {
                const preview = document.getElementById('imagePreview');
                const noPreview = document.querySelector('.no-preview-text');
                const file = e.target.files[0];

                if (file) {
                    const reader = new FileReader();

                    reader.onload = function (e) {
                        preview.src = e.target.result;
                        preview.classList.remove('d-none');
                        noPreview.classList.add('d-none');
                    }

                    reader.readAsDataURL(file);
                } else {
                    preview.src = '#';
                    preview.classList.add('d-none');
                    noPreview.classList.remove('d-none');
                }
            });
        </script>

        <style>
            .icon-shape {
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .icon-shape-white {
                background-color: rgba(255, 255, 255, 0.2);
            }

            .file-upload-input {
                cursor: pointer;
            }

            .file-upload-input::-webkit-file-upload-button {
                visibility: hidden;
            }

            .file-upload-input::before {
                content: 'Seleccionar archivo';
                display: inline-block;
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 4px;
                padding: 6px 12px;
                outline: none;
                white-space: nowrap;
                cursor: pointer;
                color: #495057;
            }

            .image-preview-container {
                transition: all 0.3s ease;
            }

            .image-preview-container:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .no-preview-text {
                transition: all 0.3s ease;
            }
        </style>

        <!-- Modales para Editar (se generan dinámicamente para cada sucursal) -->
        @foreach($sucursals as $sucursal)
            <div class="modal fade" id="editModal{{ $sucursal->id }}" tabindex="-1" role="dialog"
                aria-labelledby="editModalLabel{{ $sucursal->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-gradient-success">
                            <h5 class="modal-title text-white" id="editModalLabel{{ $sucursal->id }}">
                                <i class="ni ni-ruler-pencil me-2"></i>Editar Sucursal
                            </h5>
                            <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('admin.sucursals.update', $sucursal->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="imagen" class="form-label">Imagen</label>
                                        <input type="file" class="form-control" id="imagen" name="imagen"
                                            accept=".jpg, .jpeg, .png">
                                        @if($sucursal->imagen)
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/' . $sucursal->imagen) }}" class="img-thumbnail"
                                                    width="100" alt="Imagen actual">
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <label for="nombre" class="form-control-label">Nombre</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre"
                                                value="{{ $sucursal->nombre }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <label for="email" class="form-control-label">Correo</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                value="{{ $sucursal->email }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <label for="direccion" class="form-control-label">Dirección</label>
                                            <input type="text" class="form-control" id="direccion" name="direccion"
                                                value="{{ $sucursal->direccion }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <label for="telefono" class="form-control-label">Teléfono</label>
                                            <input type="text" class="form-control" id="telefono" name="telefono"
                                                value="{{ $sucursal->telefono }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="ni ni-fat-remove me-1"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="ni ni-check-bold me-1"></i> Actualizar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        @push('js')
            <script>
                // Vista previa de imagen al seleccionar
                document.getElementById('imagen').addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            document.getElementById('imagePreview').innerHTML = `
                            <img src="${e.target.result}" class="img-thumbnail" width="150" alt="Vista previa">
                        `;
                        }
                        reader.readAsDataURL(file);
                    }
                });
            </script>
            <!-- Bootstrap 5 JS Bundle with Popper -->

        @endpush

@endsection
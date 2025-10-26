@extends('layouts.argon')

@section('content')

    <div class="container-fluid py-4">
        <!-- Header Principal -->



        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header pb-0 d-flex flex-wrap justify-content-between align-items-center bg-white">
                    <div class="d-flex align-items-center">

                        <h5 class="mb-0">


                            <i class="fas fa-flask  me-3 text-primary"></i>
                            <strong>GESTION DE PROVEEDORES</strong>
                        </h5>
                    </div>

                    <div class="d-flex align-items-center">
                        <span class="badge bg-gradient-info me-3">


                            <i class="fas fa-database me-1"></i> {{ $laboratorios->count() }} Proveedores
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
                                <strong>Proveedores Registrados</strong>
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
                                            
                                            
                                            <th class="text-uppercase text-secondary text-xs font-weight-bolder">Nombre Proveedor
                                            </th>
                                            <th class="text-uppercase text-secondary text-xs font-weight-bolder">Celular
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
                                                        <i class="fas fa-address-card me-1 text-primary"></i>
                                                        {{ $laboratorio->nombre_proveedor }}
                                                    </span>
                                                </td>

                                                

                                            <td style="vertical-align: middle; text-align: center">
                                                <a href="https://wa.me/591{{ $laboratorio->celular }}" target="_blank"
                                                    class="btn btn-sm fw-bold shadow-sm transition-all"
                                                    style="background-color: #25D366; color: white; border-radius: 8px; border: none;"
                                                    onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.1)';"
                                                    onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)';"
                                                    title="Contactar por WhatsApp">
                                                    <i class="fab fa-whatsapp me-2 fs-5 align-middle"></i>
                                                    <span class="align-middle">{{ $laboratorio->celular }}</span>
                                                </a>
                                            </td>


                                                <td class="text-center align-middle">
                                                    <div class="d-flex justify-content-center">
                                                        <div class="d-inline-flex gap-2">
                                                                <!-- Botón Ver -->
                                                            
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-info mx-1 d-flex justify-content-center align-items-center"
                                                            style="width: 30px; height: 30px; min-width: 30px; padding: 0;"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#verModal{{ $laboratorio->id }}" title="Ver detalles">
                                                            <i class="fas fa-eye" style="font-size: 0.8rem;"></i>
                                                        </button>


                                                           

                                                            <!-- Botón Eliminar -->



                                                            <form
                                                                action="{{ route('admin.laboratorios.destroy', $laboratorio->id) }}"
                                                                method="POST" class="d-inline"
                                                                data-laboratorio='{"nombre":"{{ $laboratorio->nombre }}"}'>
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-danger mx-1  btn-eliminar-laboratorio"
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
                                                                   
                                                                    html: `<div class="swal2-content-container">

                                                                        <div class="swal2-text-content">
                                                                            <h3 class="swal2-subtitle"style="font-size: 1rem;">¿Eliminar laboratorio permanentemente?</h3>
                                                                            
                                                                            <div class="swal2-user-info mt-3" style="font-size: 0.9rem;">
                                                                                <i></i> ${laboratorio.nombre || 'Este laboratorio'}
                                                                            </div>
                                                                            <div class="swal2-warning-text" style="font-size: 0.85rem;">
                                                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                                                Esta acción no se puede deshacer
                                                                            </div>
                                                                        </div>
                                                                    </div>`,

                                                                     
                                                                    width: '350px',

                                                                    showCancelButton: true,
                                                                    focusConfirm: false,
                                                                    confirmButtonText: `<i class="fas fa-trash-alt me-2"></i> Eliminar`,
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
            <form action="{{ route('admin.laboratorios.store') }}" method="post">
                @csrf
                <div class="modal-body">
                    
                    <!-- Nombre -->
                    <div class="form-group mb-3">
                        <label class="form-label">Nombre del Laboratorio</label>
                        <div class="input-group input-group-outline">
                            <span class="input-group-text"><i class="fas fa-flask"></i></span>
                            <input type="text" class="form-control" name="nombre" value="{{ old('nombre') }}"
                                required placeholder="Ej: Laboratorio ">
                        </div>
                        @error('nombre')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div class="form-group mb-3">
                        <label class="form-label">Teléfono/Celular</label>
                        <div class="input-group input-group-outline">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" class="form-control" name="telefono" value="{{ old('telefono') }}"
                                required placeholder="Ej: 22445566">
                        </div>
                        @error('telefono')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dirección -->
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

                    <!-- NIT -->
                    <div class="form-group mb-3">
                        <label class="form-label">NIT</label>
                        <div class="input-group input-group-outline">
                            <span class="input-group-text"><i class="fas fa-file-invoice"></i></span>
                            <input type="text" class="form-control" name="nit" value="{{ old('nit') }}"
                                placeholder="Ej: 123456789">
                        </div>
                        @error('nit')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Correo -->
                    <div class="form-group mb-3">
                        <label class="form-label">Correo</label>
                        <div class="input-group input-group-outline">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" name="correo" value="{{ old('correo') }}"
                                placeholder="Ej: laboratorio@gmail.com">
                        </div>
                        @error('correo')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Nombre del proveedor -->
                    <div class="form-group mb-3">
                        <label class="form-label">Nombre del Proveedor</label>
                        <div class="input-group input-group-outline">
                            <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                            <input type="text" class="form-control" name="nombre_proveedor" value="{{ old('nombre_proveedor') }}"
                                placeholder="Ej: Distribuidora Farma S.R.L.">
                        </div>
                        @error('nombre_proveedor')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Celular -->
                    <div class="form-group mb-3">
                        <label class="form-label">Celular</label>
                        <div class="input-group input-group-outline">
                            <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                            <input type="text" class="form-control" name="celular" value="{{ old('celular') }}"
                                placeholder="Ej: 76543210">
                        </div>
                        @error('celular')
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


<!-- Modal Ver Laboratorio -->



@foreach($laboratorios as $laboratorio)
<div class="modal fade" id="verModal{{ $laboratorio->id }}" tabindex="-1" aria-labelledby="verModalLabel{{ $laboratorio->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- más ancho -->
        <div class="modal-content border-0 shadow-lg">
            
            <!-- Encabezado -->
            <div class="modal-header bg-gradient-info">
                <h5 class="modal-title text-white" id="verModalLabel{{ $laboratorio->id }}">
                    <i class="fas fa-search-plus me-2"></i> Detalles del Laboratorio
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo -->
            <div class="modal-body">
                <div class="row g-3"> <!-- separación uniforme -->
                    
                    <!-- Nombre -->
                    <div class="col-md-6">
                        <div class="card shadow-none border mb-2">
                            <div class="card-body p-2 d-flex align-items-center">
                                <i class="fas fa-building text-primary me-2"></i>
                                <div>
                                    <h6 class="text-primary fw-bold mb-1">Nombre</h6>
                                    <p class="mb-0">{{ $laboratorio->nombre }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- NIT -->
                    <div class="col-md-6">
                        <div class="card shadow-none border mb-2">
                            <div class="card-body p-2 d-flex align-items-center">
                                <i class="fas fa-file-invoice text-primary me-2"></i>
                                <div>
                                    <h6 class="text-primary fw-bold mb-1">NIT</h6>
                                    <p class="mb-0">{{ $laboratorio->nit }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="col-md-6">
                        <div class="card shadow-none border mb-2">
                            <div class="card-body p-2 d-flex align-items-center">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <div>
                                    <h6 class="text-primary fw-bold mb-1">Dirección</h6>
                                    <p class="mb-0">{{ $laboratorio->direccion }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Teléfono -->
                    <div class="col-md-6">
                        <div class="card shadow-none border mb-2">
                            <div class="card-body p-2 d-flex align-items-center">
                                <i class="fas fa-phone text-primary me-2"></i>
                                <div>
                                    <h6 class="text-primary fw-bold mb-1">Teléfono</h6>
                                    <p class="mb-0">{{ $laboratorio->telefono }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Correo -->
                    <div class="col-md-6">
                        <div class="card shadow-none border mb-2">
                            <div class="card-body p-2 d-flex align-items-center">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <div>
                                    <h6 class="text-primary fw-bold mb-1">Correo</h6>
                                    <p class="mb-0">{{ $laboratorio->correo }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="mt-3">Información del contacto</h6>

                    <!-- Proveedor -->
                    <div class="col-md-6">
                        <div class="card shadow-none border mb-2">
                            <div class="card-body p-2 d-flex align-items-center">
                                <i class="fas fa-user-tie text-primary me-2"></i>
                                <div>
                                    <h6 class="text-primary fw-bold mb-1">Proveedor</h6>
                                    <p class="mb-0">{{ $laboratorio->nombre_proveedor }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Celular con enlace WhatsApp -->
                    <div class="col-md-6">
                        <div class="card shadow-none border mb-2">
                            <div class="card-body p-2 d-flex align-items-center justify-content-between">
                                <h6 class="text-primary fw-bold mb-0"><i class="fas fa-mobile-alt me-1"></i> Celular</h6>
                                <a href="https://wa.me/591{{ $laboratorio->celular }}" target="_blank" class="btn btn-sm bg-gradient-success">
                                    <i class="fab fa-whatsapp me-1"></i> {{ $laboratorio->celular }}
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Pie de modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
                <button type="button" class="btn bg-gradient-success text-white" data-bs-toggle="modal" data-bs-target="#editModal{{ $laboratorio->id }}" title="Editar">
                    <i class="fas fa-edit me-1"></i> Actualizar
                </button>
            </div>
        </div>
    </div>
</div>

@endforeach
        <!-- Modales de Edición (generados dinámicamente) -->
        @foreach($laboratorios as $laboratorio)
        
<div class="modal fade" id="editModal{{ $laboratorio->id }}" tabindex="-1"
    aria-labelledby="editModalLabel{{ $laboratorio->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"> {{-- MÁS ANCHO --}}

        <div class="modal-content border-0 shadow-lg rounded-3">
            
            {{-- HEADER --}}
            <div class="modal-header bg-gradient-success text-white">
                <h5 class="modal-title text-white" id="editModalLabel{{ $laboratorio->id }}">
                    <i class="fas fa-edit me-2"></i> Editar Laboratorio
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            {{-- FORM --}}
            <form action="{{ url('/admin/laboratorios', $laboratorio->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">

                    {{-- DATOS DEL LABORATORIO --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light text-dark fw-bold">
                            <i class="fas fa-flask me-2"></i> Información del Laboratorio
                        </div>
                        <div class="card-body">
                            <div class="row">
                                {{-- Nombre --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombre</label>
                                    <div class="input-group input-group-outline">
                                        <span class="input-group-text"><i class="fas fa-flask"></i></span>
                                        <input type="text" class="form-control" name="nombre"
                                            value="{{ old('nombre', $laboratorio->nombre) }}" required>
                                    </div>
                                </div>

                                {{-- Teléfono --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <div class="input-group input-group-outline">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="text" class="form-control" name="telefono"
                                            value="{{ old('telefono', $laboratorio->telefono) }}" required>
                                    </div>
                                </div>

                                {{-- NIT --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">NIT</label>
                                    <div class="input-group input-group-outline">
                                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                        <input type="text" class="form-control" name="nit"
                                            value="{{ old('nit', $laboratorio->nit) }}">
                                    </div>
                                </div>

                                {{-- Correo --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Correo</label>
                                    <div class="input-group input-group-outline">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" name="correo"
                                            value="{{ old('correo', $laboratorio->correo) }}">
                                    </div>
                                </div>

                                {{-- Dirección (ocupa todo el ancho) --}}
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Dirección</label>
                                    <div class="input-group input-group-outline">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        <input type="text" class="form-control" name="direccion"
                                            value="{{ old('direccion', $laboratorio->direccion) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DATOS DEL PROVEEDOR --}}
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light text-dark fw-bold">
                            <i class="fas fa-user-tie me-2"></i> Información del Proveedor
                        </div>
                        <div class="card-body">
                            <div class="row">
                                {{-- Nombre Proveedor --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombre Proveedor</label>
                                    <div class="input-group input-group-outline">
                                        <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                        <input type="text" class="form-control" name="nombre_proveedor"
                                            value="{{ old('nombre_proveedor', $laboratorio->nombre_proveedor) }}">
                                    </div>
                                </div>

                                {{-- Celular --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Celular</label>
                                    <div class="input-group input-group-outline">
                                        <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                                        <input type="text" class="form-control" name="celular"
                                            value="{{ old('celular', $laboratorio->celular) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FOOTER --}}
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
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
            <script>
                
            $(document).ready(function() {
                $('#laboratorios-table').DataTable({
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

  
       
  
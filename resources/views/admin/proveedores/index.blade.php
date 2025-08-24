@extends('layouts.argon')

@section('content')

    <div class="container-fluid py-4">
        <!-- Primera tarjeta: Título y botón -->

        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center bg-white">
                    <div class="d-flex align-items-center">

                        <h5 class="mb-0">

                            <i class="ni ni-delivery-fast me-2 text-primary"></i>
                            <strong>GESTION DE PROVEEDORES</strong>
                        </h5>
                    </div>

                    <div class="d-flex align-items-center">
                        <span class="badge bg-gradient-info me-3">


                            <i class="fas fa-database me-1"></i> {{ $proveedores->count() }} Proveedores
                        </span>

                        <div class="dropdown me-2">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="exportDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false"
                                title="Exportar reporte en diferentes formatos">
                                <i class="fas fa-download me-1"></i> Exportar
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.proveedores.reporte') }}?tipo=pdf"
                                        title="Exportar a PDF" target="_blank">
                                        <i class="fas fa-file-pdf text-danger me-2"></i> PDF
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.proveedores.reporte') }}?tipo=excel"
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




        </div>

        <!-- Segunda tarjeta: Tabla de proveedores -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 border-radius-lg shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                        <h5 class="mb-0">
                            <i class="ni ni-bullet-list-67 me-2 text-primary"></i>
                            <strong>Proveedores Registrados</strong>
                        </h5>
                    </div>




                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">

                            <table id="proveedor-tabla" class="table align-items-center mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="width: 5%; text-align: center">Nro</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="text-align: center">Empresa</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="text-align: center">Dirección</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="text-align: center">Teléfono</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="text-align: center">Correo</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="text-align: center">Contacto</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="text-align: center">Celular</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="width: 12%; text-align: center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $contador = 1;?>
                                    @foreach($proveedores as $proveedor)
                                        <tr>
                                            <td style="text-align: center; vertical-align: middle">{{$contador++}}</td>
                                            <td style="vertical-align: middle">
                                                <span class="badge bg-gradient-primary p-2">
                                                    <i class="ni ni-building me-1"></i> {{$proveedor->empresa}}
                                                </span>
                                            </td>
                                            <td style="vertical-align: middle">{{$proveedor->direccion}}</td>
                                            <td style="vertical-align: middle">{{$proveedor->telefono}}</td>
                                            <td style="vertical-align: middle">{{$proveedor->email}}</td>
                                            <td style="vertical-align: middle">{{$proveedor->nombre}}</td>
                                            <td style="vertical-align: middle; text-align: center">
                                                <a href="https://wa.me/591{{ $proveedor->celular }}" target="_blank"
                                                    class="btn btn-sm fw-bold shadow-sm transition-all"
                                                    style="background-color: #25D366; color: white; border-radius: 8px; border: none;"
                                                    onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.1)';"
                                                    onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)';"
                                                    title="Contactar por WhatsApp">
                                                    <i class="fab fa-whatsapp me-2 fs-5 align-middle"></i>
                                                    <span class="align-middle">{{ $proveedor->celular }}</span>
                                                </a>
                                            </td>
                                            <td style="text-align: center; vertical-align: middle">
                                                <div class="btn-group" proveedor="group">
                                                    <!-- Contenedor para agrupar los botones  -->
                                                    <div class="d-inline-flex gap-2">
                                                        <!-- Botón Ver -->
                                                        <button type="button"
                                                            class="btn btn-sm bg-gradient-info text-white d-flex justify-content-center align-items-center"
                                                            style="width: 30px; height: 30px; min-width: 30px; padding: 0;"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#verModal{{ $proveedor->id }}" title="Ver detalles">
                                                            <i class="fas fa-eye" style="font-size: 0.8rem;"></i>
                                                        </button>

                                                        <!-- Botón Eliminar -->
                                                        <form action="{{ route('admin.proveedores.destroy', $proveedor->id) }}"
                                                            method="POST" class="d-inline"
                                                            data-proveedor='{"nombre":"{{ $proveedor->nombre }}"}'>
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="btn btn-sm bg-gradient-danger text-white d-flex justify-content-center align-items-center btn-eliminar-proveedor"
                                                                style="width: 30px; height: 30px; min-width: 30px; padding: 0;"
                                                                title="Eliminar proveedor" data-bs-toggle="tooltip">
                                                                <i class="fas fa-trash-alt" style="font-size: 0.8rem;"></i>
                                                            </button>
                                                        </form>
                                                    </div>

                                                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                    <script>
                                                        function confirmarEliminacionSucursal(event) {
                                                            event.preventDefault();
                                                            const form = event.target.closest('form');
                                                            const proveedor = JSON.parse(form.dataset.proveedor || '{}');

                                                            Swal.fire({
                                                                title: `<span class="swal2-title">Confirmar Eliminación</span>`,
                                                                html: `<div class="swal2-content-container">

                                                     <div class="swal2-text-content">
                                                         <h3 class="swal2-subtitle">¿Eliminar rol permanentemente?</h3>
                                                         <div class="swal2-user-info mt-3">
                                                             <i></i> ${proveedor.nombre || 'Este rol'}
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

                                                        document.querySelectorAll('.btn-eliminar-proveedor').forEach(button => {
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

    <!-- Modal para Crear Nuevo Proveedor -->
    <div class="modal fade" id="modalCrear" tabindex="-1" proveedor="dialog" aria-labelledby="modalCrearLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" proveedor="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-gradient-primary">
                    <h5 class="modal-title text-white" id="modalCrearLabel">
                        <i class="ni ni-fat-add me-2"></i><strong>Registrar Proveedor</strong>
                    </h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('/admin/proveedores/create') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="empresa">Empresa</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ni ni-building"></i></span>
                                        <input type="text" class="form-control" value="{{ old('empresa') }}" name="empresa"
                                            required>
                                    </div>
                                    @error('empresa')
                                        <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="direccion">Dirección</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ni ni-pin-3"></i></span>
                                        <input type="text" class="form-control" value="{{ old('direccion') }}"
                                            name="direccion" required>
                                    </div>
                                    @error('direccion')
                                        <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telefono">Teléfono</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ni ni-mobile-button"></i></span>
                                        <input type="text" class="form-control" value="{{ old('telefono') }}"
                                            name="telefono" required>
                                    </div>
                                    @error('telefono')
                                        <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Correo</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                        <input type="text" class="form-control" value="{{ old('email') }}" name="email"
                                            required>
                                    </div>
                                    @error('email')
                                        <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre">Nombre del proveedor</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ni ni-single-02"></i></span>
                                        <input type="text" class="form-control" value="{{ old('nombre') }}" name="nombre"
                                            required>
                                    </div>
                                    @error('nombre')
                                        <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="celular">Celular</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ni ni-mobile-button"></i></span>
                                        <input type="text" class="form-control" value="{{ old('celular') }}" name="celular"
                                            required>
                                    </div>
                                    @error('celular')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="ni ni-fat-remove me-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ni ni-check-bold me-1"></i> Registrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales para Ver y Editar (se generan dinámicamente para cada proveedor) -->
    @foreach($proveedores as $proveedor)
        <!-- Modal Ver -->
        <div class="modal fade" id="verModal{{ $proveedor->id }}" tabindex="-1" proveedor="dialog"
            aria-labelledby="verModalLabel{{ $proveedor->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" proveedor="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-gradient-info">
                        <h5 class="modal-title text-white" id="verModalLabel{{ $proveedor->id }}">
                            <i class="ni ni-zoom-split-in me-2"></i>Detalles del Proveedor
                        </h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-3 border-0 shadow-none">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-primary">Empresa:</label>
                                            <p class="form-control-static">{{$proveedor->empresa}}</p>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-primary">Dirección:</label>
                                            <p class="form-control-static">{{$proveedor->direccion}}</p>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-primary">Teléfono:</label>
                                            <p class="form-control-static">{{$proveedor->telefono}}</p>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-primary">Correo:</label>
                                            <p class="form-control-static">{{$proveedor->email}}</p>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-primary">Contacto:</label>
                                            <p class="form-control-static">{{$proveedor->nombre}}</p>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-primary">Celular:</label>
                                            <p class="form-control-static">
                                                <a href="https://wa.me/591{{$proveedor->celular}}" target="_blank"
                                                    class="btn btn-sm bg-gradient-success">
                                                    <i class="ni ni-send me-1"></i> {{$proveedor->celular}}
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm bg-gradient-success mx-1 text-white" data-bs-toggle="modal"
                            data-bs-target="#editModal{{ $proveedor->id }}" title="Editar">
                            <i class="fas fa-pencil-alt"></i> Editar
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="ni ni-fat-remove me-1"></i> Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Editar -->
        <div class="modal fade" id="editModal{{ $proveedor->id }}" tabindex="-1" proveedor="dialog"
            aria-labelledby="editModalLabel{{ $proveedor->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" proveedor="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-gradient-success">
                        <h5 class="modal-title text-white" id="editModalLabel{{ $proveedor->id }}">
                            <i class="ni ni-ruler-pencil me-2"></i>Editar Proveedor
                        </h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{url('/admin/proveedores', $proveedor->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="empresa">Empresa</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ni ni-building"></i></span>
                                            <input type="text" class="form-control" name="empresa"
                                                value="{{ $proveedor->empresa }}" required>
                                        </div>
                                        @error('empresa')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="direccion">Dirección</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ni ni-pin-3"></i></span>
                                            <input type="text" class="form-control" name="direccion"
                                                value="{{ $proveedor->direccion }}" required>
                                        </div>
                                        @error('direccion')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="telefono">Teléfono</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ni ni-mobile-button"></i></span>
                                            <input type="text" class="form-control" value="{{ $proveedor->telefono }}"
                                                name="telefono">
                                        </div>
                                        @error('telefono')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Correo</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                            <input type="text" class="form-control" value="{{ $proveedor->email }}" name="email"
                                                required>
                                        </div>
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre">Nombre del Contacto</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ni ni-single-02"></i></span>
                                            <input type="text" class="form-control" value="{{ $proveedor->nombre }}"
                                                name="nombre" required>
                                        </div>
                                        @error('nombre')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="celular">Celular</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ni ni-mobile-button"></i></span>
                                            <input type="text" class="form-control" value="{{ $proveedor->celular }}"
                                                name="celular" required>
                                        </div>
                                        @error('celular')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="ni ni-fat-remove me-1"></i> Cerrar
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
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .table thead th {
            background-color: #f8f9fa;
            color: #495057;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .badge-primary {
            background-color: #5e72e4;
        }

        .bg-gradient-info {
            background: linear-gradient(87deg, #11cdef 0, #1171ef 100%) !important;
        }

        .bg-gradient-success {
            background: linear-gradient(87deg, #2dce89 0, #2dcecc 100%) !important;
        }

        .bg-gradient-danger {
            background: linear-gradient(87deg, #f5365c 0, #f56036 100%) !important;
        }

        .modal-header {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }

        .bg-gradient-primary {
            background: linear-gradient(87deg, #5e72e4 0, #825ee4 100%) !important;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(94, 114, 228, 0.05);
            transition: background-color 0.2s ease;
        }

        .input-group-text {
            background-color: #f8fafc;
            border-right: none;
        }

        .form-control {
            border-left: none;
        }

        .form-control-static {
            padding: 0.375rem 0;
            margin-bottom: 0;
            line-height: 1.5;
            border-bottom: 1px solid #e9ecef;
        }

        .border-radius-lg {
            border-radius: 0.5rem;
        }

        .card {
            border: none;
            box-shadow: 0 0.5rem 1.25rem rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
        }

        .btn-group .btn {
            margin: 0 2px;
            border-radius: 4px !important;
        }

        .table td,
        .table th {
            vertical-align: middle;
            padding: 0.75rem 1.5rem;
        }

        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {


            $('#proveedor-tabla').DataTable({
                "pageLength": 10,
                "responsive": true,
                "autoWidth": false,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros por página",
                    "zeroRecords": "No se encontraron categorías",
                    "info": "Mostrando página _PAGE_ de _PAGES_",
                    "infoEmpty": "No hay categorías registradas",
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
                    $('.dataTables_filter input').addClass('form-control').attr('placeholder', 'Buscar categoría...');
                    $('.dataTables_length select').addClass('form-select');
                }
            });




        });
    </script>
@endpush
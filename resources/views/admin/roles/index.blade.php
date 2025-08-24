@extends('layouts.app', ['title' => 'Gestión de Roles'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Gestión de Roles'])
    <div class="container-fluid py-4">
        <!-- Header Principal -->

        <div class="row">
            <!-- Card de Encabezado -->
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center bg-white">
                        <div class="d-flex align-items-center">

                            <h5 class="mb-0">
                                <i class="fas fa-user-tag me-3 text-primary"></i>
                                <strong>GESTION ROLES</strong>
                            </h5>
                        </div>

                        <div class="d-flex align-items-center">
                            <span class="badge bg-gradient-info me-3">

                                <i class="fas fa-layer-group me-1"></i> {{ $roles->count() }} roles
                            </span>

                            <div class="dropdown me-2">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                    id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                    title="Exportar reporte en diferentes formatos">
                                    <i class="fas fa-download me-1"></i> Exportar
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.roles.reporte') }}?tipo=pdf"
                                            title="Exportar a PDF" target="_blank">
                                            <i class="fas fa-file-pdf text-danger me-2"></i> PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.roles.reporte') }}?tipo=excel"
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
                                data-bs-target="#crearRolModal">
                                <i class="fas fa-plus-circle me-1"></i> Nuevo
                            </button>
                        </div>
                    </div>
                </div>
            </div>





            <!-- Modal para Crear Nuevo Rol -->
            <div class="modal fade" id="crearRolModal" tabindex="-1" aria-labelledby="crearRolModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">

                        <div class="modal-header bg-gradient-primary text-white">
                            <h5 class="modal-title text-white" id="crearRolModalLabel">
                                <i class="fas fa-plus-circle me-2 text-white"></i>
                                Registrar Nuevo Rol
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <form action="{{ url('/admin/roles/create') }}" method="post">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group mb-4">
                                    <label for="name" class="form-label fw-bold text-dark mb-2">Nombre del Rol</label>
                                    <div class="input-group input-group-outline">
                                        <span class="input-group-text bg-transparent"><i class="fas fa-user-tag"></i></span>
                                        <input type="text" value="{{ old('name') }}" class="form-control border-bottom"
                                            name="name" placeholder="Ej: Administrador" required>
                                    </div>
                                    @error('name')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-2"></i> Cancelar
                                </button>
                                <button type="submit" class="btn bg-gradient-primary">
                                    <i class="fas fa-save me-2"></i> Guardar Rol
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>



            <!-- Card de Roles registrados -->



            <div class="card-body">
                <div class="row">
                    @foreach($roles as $role)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card card-hover h-100 border-0 shadow-xs">
                                    <div
                                        class="card-header bg-white d-flex justify-content-between align-items-center border-bottom pb-3">
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle me-2">
                                                <i class="fas fa-user-tag text-white opacity-10"></i>
                                            </div>
                                            <h5 class="mb-0 text-dark font-weight-bold">{{ $role->name }}</h5>
                                        </div>
                                        <span class="badge bg-gradient-primary">
                                            {{ $role->users_count }} <i class="fas fa-users ms-1"></i>
                                        </span>
                                    </div>

                                    <div class="card-body pt-3 pb-2">
                                        <h6 class="text-xs text-uppercase text-muted mb-3">Permisos asignados
                                            ({{ $role->permissions_count }})</h6>
                                        <div class="permisos-list mb-3" style="max-height: 120px; overflow-y: auto;">
                                            @forelse($role->permissions as $permiso)
                                                <span class="badge bg-gray-200 text-dark mb-1 me-1">
                                                    <i class="fas fa-shield-alt text-info me-1"></i>
                                                    {{ ucwords(str_replace('.', ' ', $permiso->name)) }}
                                                </span>
                                            @empty
                                                <span class="badge bg-gray-100 text-muted">Sin permisos</span>
                                            @endforelse
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center border-top pt-3">


                                            <!-- Contenedor de botones con espaciado -->
                                            <!-- Contenedor de botones compactos -->
                                            <!-- Contenedor de botones con espaciado controlado -->
                                            <div class="d-flex align-items-center">
                                                <!-- Botón Permisos -->
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-warning rounded-2 me-2 px-2 py-1"
                                                    data-bs-toggle="modal" data-bs-target="#asignarModal{{ $role->id }}"
                                                    title="Asignar permisos">
                                                    <i class="fas fa-key me-1"></i>
                                                    <span class="small">Permisos</span>
                                                </button>



                                                <!-- Botón Editar -->
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-success rounded-2 me-2 px-2 py-1"
                                                    data-bs-toggle="modal" data-bs-target="#editModal{{ $role->id }}"
                                                    title="Editar rol">
                                                    <i class="fas fa-pencil-alt me-1"></i>
                                                    <span class="small">Editar</span>
                                                </button>



                                                <!-- Botón Eliminar -->
                                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                                                    class="d-inline me-2" data-role='{"nombre":"{{ $role->nombre }}"}'>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1 btn-eliminar-role"
                                                        title="Eliminar rol">
                                                        <i class="fas fa-trash-alt me-1"></i>
                                                        <span class="small">Eliminar</span>
                                                    </button>
                                                </form>


                                                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                <script>
                                                    function confirmarEliminacionSucursal(event) {
                                                        event.preventDefault();
                                                        const form = event.target.closest('form');
                                                        const role = JSON.parse(form.dataset.role || '{}');

                                                        Swal.fire({
                                                            title: `<span class="swal2-title">Confirmar Eliminación</span>`,
                                                            html: `<div class="swal2-content-container">

                             <div class="swal2-text-content">
                                 <h3 class="swal2-subtitle">¿Eliminar rol permanentemente?</h3>
                                 <div class="swal2-user-info mt-3">
                                     <i></i> ${role.nombre || 'Este rol'}
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

                                                    document.querySelectorAll('.btn-eliminar-role').forEach(button => {
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
                                        </div>
                                    </div>
                                </div>
                            </div>






                            <!-- Modal Editar Rol -->
                            <div class="modal fade" id="editModal{{ $role->id }}" tabindex="-1"
                                aria-labelledby="editLabel{{ $role->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header bg-gradient-success text-white">
                                            <h5 class="modal-title" id="editLabel{{ $role->id }}">Editar Rol</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                aria-label="Cerrar"></button>
                                        </div>
                                        <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="name">Nombre del Rol</label>
                                                    <div class="input-group input-group-outline">
                                                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                        <input type="text" name="name" class="form-control"
                                                            value="{{ old('name', $role->name) }}" required>
                                                    </div>
                                                    @error('name')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="fas fa-times me-1"></i> Cancelar
                                                </button>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-save me-1"></i> Actualizar
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Asignar Permisos -->
                            <div class="modal fade" id="asignarModal{{ $role->id }}" tabindex="-1"
                                aria-labelledby="asignarLabel{{ $role->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header bg-gradient-primary text-white">
                                            <h5 class="modal-title fw-bold">
                                                <i class="fas fa-key me-2"></i> Asignar Permisos: {{ $role->name }}
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>

                                        <form action="{{ url('/admin/roles/asignar', $role->id) }}" method="POST"
                                            id="formPermisos{{ $role->id }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4">
                                                <div class="form-group mb-4">
                                                    <div class="input-group input-group-outline">
                                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                                        <input type="text" class="form-control" id="searchPermisos{{ $role->id }}"
                                                            placeholder="Buscar permisos...">
                                                    </div>
                                                </div>

                                                <div class="row permisos-container" style="max-height: 400px; overflow-y: auto;">
                                                    @foreach($permisos->chunk(ceil($permisos->count() / 3)) as $chunk)
                                                        <div class="col-md-4">
                                                            @foreach($chunk as $permiso)
                                                                <div class="form-check mb-3 permisos-item">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        id="permiso_{{ $permiso->id }}_{{ $role->id }}" name="permisos[]"
                                                                        value="{{ $permiso->id }}" {{ $role->permissions->contains($permiso->id) ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="permiso_{{ $permiso->id }}_{{ $role->id }}">
                                                                        <span class="badge bg-light border me-2">
                                                                            <i class="fas fa-shield-alt text-primary"></i>
                                                                        </span>
                                                                        {{ ucwords(str_replace('.', ' ', $permiso->name)) }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <div class="alert alert-info mt-3 mb-0 py-2">
                                                    <small>
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        <span
                                                            id="selectedCount{{ $role->id }}">{{ $role->permissions_count }}</span>
                                                        permisos seleccionados de {{ $permisos->count() }} disponibles
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                    <i class="fas fa-times me-1"></i> Cancelar
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save me-1"></i> Guardar Cambios
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Función de búsqueda para permisos
            const searchInputs = document.querySelectorAll('[id^="searchPermisos"]');
            searchInputs.forEach(input => {
                input.addEventListener('keyup', function () {
                    const modalId = this.closest('.modal').id;
                    const searchText = this.value.toLowerCase();
                    const permisosItems = document.querySelectorAll(`#${modalId} .permisos-item`);

                    permisosItems.forEach(item => {
                        const text = item.textContent.toLowerCase();
                        item.style.display = text.includes(searchText) ? '' : 'none';
                    });
                });
            });

            // Contador de permisos seleccionados
            function setupCounter(modalId) {
                const checkboxes = document.querySelectorAll(`#${modalId} input[name="permisos[]"]`);
                const countElement = document.querySelector(`#${modalId} #selectedCount${modalId.replace('asignarModal', '')}`);

                function updateCount() {
                    const selected = document.querySelectorAll(`#${modalId} input[name="permisos[]"]:checked`).length;
                    if (countElement) countElement.textContent = selected;
                }

                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', updateCount);
                });

                updateCount(); // Inicializar
            }

            // Configurar contadores para cada modal
            document.querySelectorAll('[id^="asignarModal"]').forEach(modal => {
                setupCounter(modal.id);
            });

            // Inicializar tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
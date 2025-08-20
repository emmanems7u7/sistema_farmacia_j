@extends('layouts.app', ['title' => 'Gestión de Permisos'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Gestión de Permisos'])
<div class="container-fluid py-4">
    <!-- Header Principal -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center bg-white">
                    <div class="d-flex align-items-center">
                        
                        <h5 class="mb-0">
                            
                            <i class="fas fa-user-shield  me-3 text-primary"></i>
                            <strong>GESTIONAR PERMISOS</strong></h5>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <span class="badge bg-gradient-info me-3">
                             
                            
                             <i class="fas fa-database me-1"></i> {{ $permisos->count() }} Permisos
                        </span>
                        
                       

                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrear">
                            <i class="fas fa-plus-circle me-1"></i> Nuevo
                        </button>

                        
                    </div>
                </div>
        </div>


   
</div>


                            <div id="alert">
                                @include('components.alert')
                            </div>

                            <!-- Tabla de Permisos -->
                            <div class="card shadow-lg border-0">
                                

                                    <div class="card shadow-lg border-0">
                                    <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                                        <h5 class="mb-0 text-black">
                                            <i class="fas fa-list-check me-2"></i>Permisos Registrados
                                        </h5>
                                    
                                </div>

                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table id="mitabla" class="table table-hover align-items-center mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">#</th>
                                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Nombre del Permiso</th>
                                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($permisos as $permiso)
                                                <tr>
                                                    <td class="text-center align-middle">
                                                        <span class="text-secondary text-xs font-weight-bold">{{ $loop->iteration }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon icon-shape icon-sm bg-gradient-info shadow text-center rounded-circle me-3">
                                                                <i class="fas fa-shield-alt text-white opacity-10"></i>
                                                            </div>
                                                            <div>
                                                                <span class="text-dark text-sm font-weight-bold mb-0">{{ $permiso->name }}</span>
                                                                <p class="text-xs text-secondary mb-0">Creado: {{ $permiso->created_at->format('d/m/Y') }}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <div class="btn-group" role="group">
                                                            <!-- Botón Editar - Verde -->
                                                            <button type="button" class="btn btn-sm bg-gradient-success text-white mx-1" 
                                                                    data-bs-toggle="modal" data-bs-target="#editModal{{ $permiso->id }}"
                                                                    style="width: 30px; height: 30px; min-width: 30px; padding: 0;"
                                                                    title="Editar permiso">
                                                                <i class="fas fa-pen-to-square me-1"></i> 
                                                            </button>
                                                            
                                                            <!-- Botón Eliminar - Rojo -->
                                                            


                                                            <form action="{{ route('admin.permisos.destroy', $permiso->id) }}" 
                                                            method="POST" 
                                                            class="d-inline"
                                                            data-permiso='{"nombre":"{{ $permiso->nombre }}"}'>
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" 
                                                                        class="btn btn-sm bg-gradient-danger text-white  mx-1 btn-eliminar-permiso"
                                                                        title="Eliminar permiso"
                                                                         style="width: 30px; height: 30px; min-width: 30px; padding: 0;"
                                                                        data-bs-toggle="tooltip">
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
                                                            const permiso = JSON.parse(form.dataset.permiso || '{}');
                                                            
                                                            Swal.fire({
                                                                title: `<span class="swal2-title">Confirmar Eliminación</span>`,
                                                                html: `<div class="swal2-content-container">
                                                                        
                                                                        <div class="swal2-text-content">
                                                                            <h3 class="swal2-subtitle">¿Eliminar permiso permanentemente?</h3>
                                                                            <div class="swal2-user-info mt-3">
                                                                                <i></i> ${permiso.nombre || 'Esta permiso'}
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

                                                        document.querySelectorAll('.btn-eliminar-permiso').forEach(button => {
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

<!-- Modal para Crear Nuevo Permiso -->
<div class="modal fade" id="modalCrear" tabindex="-1" aria-labelledby="modalCrearLabel" aria-hidden="true">
    
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="modalCrearLabel">
                    <i class="fas fa-plus-circle me-2"></i> Nuevo Permiso
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/admin/permisos/create') }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name" class="form-label text-muted mb-2">Nombre del Permiso</label>
                        <div class="input-group input-group-outline">
                            <span class="input-group-text bg-transparent"><i class="fas fa-key text-primary"></i></span>
                            <input type="text" class="form-control border-bottom" name="name" 
                                   value="{{ old('name') }}" required 
                                   placeholder="Ej: gestionar-usuarios">
                        </div>
                        <small class="text-muted mt-1">(ej: gestionar_usuarios)</small>
                        @error('name')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn bg-gradient-success text-white">
                        <i class="fas fa-save me-1"></i> Guardar Permiso
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modales de Edición (generados dinámicamente) -->
@foreach($permisos as $permiso)
<div class="modal fade" id="editModal{{ $permiso->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $permiso->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-success text-white">
                <h5 class="modal-title" id="editModalLabel{{ $permiso->id }}">
                    <i class="fas fa-edit me-2"></i> Editar Permiso
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/admin/permisos', $permiso->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name" class="form-label text-muted mb-2">Nombre del Permiso</label>
                        <div class="input-group input-group-outline">
                            <span class="input-group-text bg-transparent"><i class="fas fa-key text-success"></i></span>
                            <input type="text" name="name" class="form-control border-bottom" 
                                   value="{{ old('name', $permiso->name) }}" required>
                        </div>
                        @error('name')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn bg-gradient-primary text-white">
                        <i class="fas fa-save me-1"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endforeach

@endsection


 
@push('js')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {


        $('#mitabla').DataTable({
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
        
        "initComplete": function() {
            $('.dataTables_filter input').addClass('form-control').attr('placeholder', 'Buscar categoría...');
            $('.dataTables_length select').addClass('form-select');
        }
    });



        
    });
</script>
@endpush
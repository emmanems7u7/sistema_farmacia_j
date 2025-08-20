@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Gestión de Usuarios'])

<div class="container-fluid py-4">
    <div class="row">
        <!-- Card de Encabezado -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center bg-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users fa-lg me-3 text-primary"></i>
                        <h6 class="mb-0"><strong>GESTIÓN DE USUARIOS</strong></h6>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <span class="badge bg-gradient-info me-3">
                            {{ count($usuarios) }} usuarios 
                        </span>
                        
                        <div class="dropdown me-2">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" 
                                    id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                    title="Exportar reporte en diferentes formatos">
                                <i class="fas fa-download me-1"></i> Exportar
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                <li>
                                    <a class="dropdown-item" 
                                    href="{{ route('admin.usuarios.reporte', ['tipo' => 'pdf']) }}?rol={{ request('rol') }}&sucursal={{ request('sucursal') }}&estado={{ request('estado') }}"
                                    title="Exportar a PDF" target="_blank">
                                        <i class="fas fa-file-pdf text-danger me-2"></i> PDF
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" 
                                    href="{{ route('admin.usuarios.reporte', ['tipo' => 'excel']) }}?rol={{ request('rol') }}&sucursal={{ request('sucursal') }}&estado={{ request('estado') }}"
                                    title="Exportar a Excel">
                                        <i class="fas fa-file-excel text-success me-2"></i> Excel
                                    </a>
                                </li>
                                
                                <li><hr class="dropdown-divider"></li>
                            </ul>
                        </div>

                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrear">
                            <i class="fas fa-plus-circle me-1"></i> Nuevo
                        </button>
                    </div>
                </div>
        </div>
    </div>

        <!-- Cards de Usuarios -->
        @foreach($usuarios as $usuario)
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card shadow-sm border-0 h-100 hover-scale">
                <div class="card-header bg-white border-0 pb-0 position-relative">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar avatar-xl position-relative me-3">
                            @if($usuario->imagen)
                                <img src="{{ asset('storage/'.$usuario->imagen) }}" 
                                    class="rounded-circle border border-3 border-white shadow-sm" 
                                    alt="Foto de perfil">
                            @else
                                <div class="avatar-initials bg-gradient-primary rounded-circle shadow-sm d-flex align-items-center justify-content-center border border-3 border-white">
                                    <span class="text-white fw-bold fs-4">{{ substr($usuario->firstname, 0, 1) }}{{ substr($usuario->lastname ?? '', 0, 1) }}</span>
                                </div>
                            @endif
                            <span class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-2 border-white" style="width: 12px; height: 12px;"></span>
                        </div>
                        <div class="flex-grow-1 pt-2">
                            <h6 class="mb-1">{{ $usuario->firstname ?? 'Sin nombre' }} {{ $usuario->lastname ?? '' }}</h6>
                            
                            
                        </div>
                    </div>
                </div>
                <div class="card-body pt-2">
                    <ul class="list-unstyled mb-3">
                        <li class="mb-2">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            <span class="text-dark">{{ $usuario->email ?? 'Sin correo' }}</span>
                        </li>
                        @if($usuario->phone)
                        <li class="mb-2">
                            <i class="fas fa-phone text-primary me-2"></i>
                            <span class="text-dark">{{ $usuario->phone }}</span>
                        </li>
                        @endif
                        <li>
                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                            <span class="text-dark">Registrado: {{ $usuario->created_at->format('d/m/Y') }}</span>
                        </li>
                    </ul>
                </div>
                <div class="card-footer bg-white border-0 pt-0 pb-3">
                    <div class="d-flex justify-content-end">
                        <button 
                            type="button" 
                            class="btn btn-sm btn-icon btn-outline-info rounded-2 me-2" 
                            data-bs-toggle="modal" 
                            data-bs-target="#verModal{{ $usuario->id }}"
                            data-bs-toggle="tooltip" 
                            title="Ver detalles"
                        >
                            <i class="fas fa-eye"></i>
                        </button>

                        
                        
                        
                        <button 
                        type="button" 
                       class="btn btn-sm btn-icon btn-outline-success rounded-2 me-2" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editarModal{{ $usuario->id }}"
                            data-bs-toggle="tooltip" 
                            
                            data-bs-toggle="tooltip" title="Eliminar">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        
                        
                        
                        <form action="{{ route('admin.usuarios.destroy', $usuario->id) }}" 
                            method="POST" 
                            class="d-inline"
                            data-usuario='{"nombre":"{{ $usuario->full_name }}"}'>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-icon btn-outline-danger  btn-eliminar"
                                    data-bs-toggle="tooltip" title="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmarEliminacion(event) {
    event.preventDefault();
    const form = event.target.closest('form');
    const usuario = JSON.parse(form.dataset.usuario || '{}');
    
    Swal.fire({
        title: `<span class="swal2-title">Confirmar Eliminación</span>`,
        html: `<div class="swal2-content-container">
                 <div class="swal2-icon-wrapper">
                     <svg class="swal2-icon-svg" viewBox="0 0 24 24">
                         <path fill="#FF4757" d="M12,2C6.47,2,2,6.47,2,12s4.47,10,10,10s10-4.47,10-10S17.53,2,12,2z M16.707,15.293c0.391,0.391,0.391,1.023,0,1.414 C16.512,16.902,16.256,17,16,17s-0.512-0.098-0.707-0.293L12,13.414l-3.293,3.293C8.512,16.902,8.256,17,8,17s-0.512-0.098-0.707-0.293 c-0.391-0.391-0.391-1.023,0-1.414L10.586,12L7.293,8.707c-0.391-0.391-0.391-1.023,0-1.414s1.023-0.391,1.414,0L12,10.586l3.293-3.293 c0.391-0.391,1.023-0.391,1.414,0s0.391,1.023,0,1.414L13.414,12L16.707,15.293z"/>
                     </svg>
                 </div>
                 <div class="swal2-text-content">
                     <h3 class="swal2-subtitle">¿Eliminar usuario permanentemente?</h3>
                    
                     
                
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

document.querySelectorAll('.btn-eliminar').forEach(button => {
    button.addEventListener('click', confirmarEliminacion);
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

.swal2-details {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
    margin: 1.5rem 0;
}

.swal2-detail-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.95rem;
    color: #57606f;
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
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Modales para Ver Usuario -->
<!-- Modales para Ver Usuario -->
@foreach($usuarios as $usuario)
<div class="modal fade" id="verModal{{ $usuario->id }}" tabindex="-1" aria-labelledby="verModalLabel{{ $usuario->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-info text-white">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-xl me-2">
                        @if($usuario->imagen)
                            <img src="{{ asset('storage/'.$usuario->imagen) }}" 
                                class="rounded-circle border border-3 border-white shadow" 
                                alt="Foto de perfil">
                        @else
                            <div class="avatar-initials bg-white rounded-circle shadow d-flex align-items-center justify-content-center border border-3 border-white">
                                <span class="text-info fw-bold fs-4">{{ substr($usuario->firstname, 0, 1) }}{{ substr($usuario->lastname ?? '', 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="verModalLabel{{ $usuario->id }}">
                            {{ $usuario->firstname }} {{ $usuario->lastname }}
                        </h5>
                        <p class="text-white-50 small mb-0">{{ $usuario->roles->first()->name ?? 'Sin rol' }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h6 class="text-uppercase text-info mb-3">
                                <i class="fas fa-id-card me-2"></i>Información Personal
                            </h6>
                            <div class="card border-0 shadow-none">
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item border-0 px-0 py-2 d-flex align-items-center">
                                            <i class="fas fa-user-circle text-info me-3"></i>
                                            <div>
                                                <small class="text-muted">Usuario</small>
                                                <p class="mb-0 fw-bold">{{ $usuario->username }}</p>
                                            </div>
                                        </li>
                                        <li class="list-group-item border-0 px-0 py-2 d-flex align-items-center">
                                            <i class="fas fa-signature text-info me-3"></i>
                                            <div>
                                                <small class="text-muted">Nombre completo</small>
                                                <p class="mb-0 fw-bold">{{ $usuario->firstname }} {{ $usuario->lastname }}</p>
                                            </div>
                                        </li>
                                        <li class="list-group-item border-0 px-0 py-2 d-flex align-items-center">
                                            <i class="fas fa-calendar-alt text-info me-3"></i>
                                            <div>
                                                <small class="text-muted">Fecha de registro</small>
                                                <p class="mb-0 fw-bold">{{ $usuario->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h6 class="text-uppercase text-info mb-3">
                                <i class="fas fa-address-book me-2"></i>Información de Contacto
                            </h6>
                            <div class="card border-0 shadow-none">
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item border-0 px-0 py-2 d-flex align-items-center">
                                            <i class="fas fa-envelope text-info me-3"></i>
                                            <div>
                                                <small class="text-muted">Correo electrónico</small>
                                                <p class="mb-0 fw-bold">{{ $usuario->email }}</p>
                                            </div>
                                        </li>
                                        <li class="list-group-item border-0 px-0 py-2 d-flex align-items-center">
                                            <i class="fas fa-mobile-alt text-info me-3"></i>
                                            <div>
                                                <small class="text-muted">Celular</small>
                                                <p class="mb-0 fw-bold">{{ $usuario->celular ?? 'No especificado' }}</p>
                                            </div>
                                        </li>
                                        <li class="list-group-item border-0 px-0 py-2 d-flex align-items-center">
                                            <i class="fas fa-map-marker-alt text-info me-3"></i>
                                            <div>
                                                <small class="text-muted">Dirección</small>
                                                <p class="mb-0 fw-bold">{{ $usuario->address ?? 'No especificada' }}</p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cerrar
                </button>
                <button type="button" class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#editarModal{{ $usuario->id }}" data-bs-dismiss="modal">
                    <i class="fas fa-edit me-2"></i>Editar Usuario
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Modales para Editar Usuario -->
@foreach($usuarios as $usuario)
<div class="modal fade" id="editarModal{{ $usuario->id }}" tabindex="-1" aria-labelledby="editarModalLabel{{ $usuario->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-success text-white">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-xl me-3">
                        @if($usuario->imagen)
                            <img src="{{ asset('storage/'.$usuario->imagen) }}" 
                                class="rounded-circle border border-3 border-white shadow" 
                                alt="Foto de perfil">
                        @else
                            <div class="avatar-initials bg-white rounded-circle shadow d-flex align-items-center justify-content-center border border-3 border-white">
                                <span class="text-success fw-bold fs-4">{{ substr($usuario->firstname, 0, 1) }}{{ substr($usuario->lastname ?? '', 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="editarModalLabel{{ $usuario->id }}">
                            <i class="fas fa-user-edit me-2"></i>Editar Usuario
                        </h5>
                      
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-uppercase text-success mb-3 border-bottom pb-2">
                                    <i class="fas fa-user-cog me-2"></i>Información Básica
                                </h6>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Rol del Usuario</label>
                                    <select name="role" class="form-select shadow-sm" required>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" {{ $usuario->hasRole($role->name) ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nombre</label>
                                    <input type="text" class="form-control shadow-sm" value="{{ $usuario->firstname }}" name="firstname" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Apellido</label>
                                    <input type="text" class="form-control shadow-sm" value="{{ $usuario->lastname }}" name="lastname" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nombre de Usuario</label>
                                    <input type="text" class="form-control shadow-sm" value="{{ $usuario->username }}" name="username" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-uppercase text-success mb-3 border-bottom pb-2">
                                    <i class="fas fa-lock me-2"></i>Seguridad y Acceso
                                </h6>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Correo Electrónico</label>
                                    <input type="email" class="form-control shadow-sm" value="{{ $usuario->email }}" name="email" required>
                                </div>
                                
                                <div class="alert alert-warning p-2 mb-3">
                                    <small class="d-block"><i class="fas fa-info-circle me-1"></i> Complete solo si desea cambiar la contraseña</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nueva Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control shadow-sm" name="password" id="password{{ $usuario->id }}" placeholder="Nueva contraseña">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password{{ $usuario->id }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Confirmar Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control shadow-sm" name="password_confirmation" id="password_confirmation{{ $usuario->id }}" placeholder="Confirmar contraseña">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation{{ $usuario->id }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="text-uppercase text-success mb-3 border-bottom pb-2">
                                        <i class="fas fa-image me-2"></i>Imagen de Perfil
                                    </h6>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg me-3">
                                            @if($usuario->imagen)
                                                <img src="{{ asset('storage/'.$usuario->imagen) }}" 
                                                    class="rounded-circle border border-2 border-success" 
                                                    alt="Foto de perfil" id="currentImage{{ $usuario->id }}">
                                            @else
                                                <div class="avatar-initials bg-light rounded-circle d-flex align-items-center justify-content-center border border-2 border-success">
                                                    <span class="text-success fw-bold" id="currentInitials{{ $usuario->id }}">{{ substr($usuario->firstname, 0, 1) }}{{ substr($usuario->lastname ?? '', 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <label for="imageUpload{{ $usuario->id }}" class="btn btn-sm btn-outline-success mb-0">
                                                <i class="fas fa-camera me-1"></i>Cambiar Imagen
                                            </label>
                                            <input type="file" id="imageUpload{{ $usuario->id }}" name="imagen" accept="image/*" class="d-none" onchange="previewImage(this, '{{ $usuario->id }}')">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal para Crear Usuario -->
<div class="modal fade" id="modalCrear" tabindex="-1" aria-labelledby="modalCrearLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3 bg-white rounded-circle d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-plus text-primary fs-5"></i>
                    </div>
                    <div>
                           <i class=" text-white"></i>Nuevo Usuario
                        <p class="text-white-50 small mb-0">Complete todos los campos requeridos</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('admin.usuarios.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-uppercase text-primary mb-3 border-bottom pb-2">
                                    <i class="fas fa-user-tag me-2"></i>Información Personal
                                </h6>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Sucursal <span class="text-danger">*</span></label>
                                    <select name="sucursal_id" class="form-select shadow-sm" required>
                                        <option value="">Seleccionar sucursal...</option>
                                        @foreach($sucursales as $sucursal)
                                            <option value="{{ $sucursal->id }}" {{ old('sucursal_id') == $sucursal->id ? 'selected' : '' }}>
                                                {{ $sucursal->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nombre Completo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control shadow-sm" name="firstname" value="{{ old('firstname') }}" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Apellido Completo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control shadow-sm" name="lastname" value="{{ old('lastname') }}" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nombre de Usuario <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control shadow-sm" name="username" value="{{ old('username') }}" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Dirección</label>
                                    <input type="text" class="form-control shadow-sm" name="address" value="{{ old('address') }}" placeholder="Ingrese la dirección">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Teléfono/Celular</label>
                                    <input type="tel" class="form-control shadow-sm" name="celular" value="{{ old('celular') }}" placeholder="Ingrese número de contacto">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-uppercase text-primary mb-3 border-bottom pb-2">
                                    <i class="fas fa-user-shield me-2"></i>Acceso y Seguridad
                                </h6>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Rol del Usuario <span class="text-danger">*</span></label>
                                    <select name="role" class="form-select shadow-sm" required>
                                        <option value="">Seleccionar rol...</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Correo Electrónico <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control shadow-sm" name="email" value="{{ old('email') }}" required placeholder="usuario@gmail.com">
                                </div>
                                
                                <div class="alert alert-primary p-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-key me-3 fs-4"></i>
                                        <div>
                                            <h6 class="alert-heading mb-1">Contraseña Automática</h6>
                                            <p class="small mb-0">El sistema generará una contraseña segura y se enviará al correo electrónico proporcionado.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="text-uppercase text-primary mb-3 border-bottom pb-2">
                                        <i class="fas fa-image me-2"></i>Imagen de Perfil
                                    </h6>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg me-3 bg-light rounded-circle d-flex align-items-center justify-content-center border border-2 border-primary">
                                            <i class="fas fa-user text-primary" id="defaultIcon"></i>
                                            <img id="imagePreview" src="#" alt="Preview" class="rounded-circle w-100 h-100 d-none object-fit-cover">
                                        </div>
                                        <div>
                                            <label for="imageUpload" class="btn btn-sm btn-outline-primary mb-0">
                                                <i class="fas fa-camera me-1"></i>Seleccionar Imagen
                                            </label>
                                            <input type="file" id="imageUpload" name="imagen" accept="image/*" class="d-none">
                                            <p class="small text-muted mt-1 mb-0">Formatos: JPG, PNG (Max. 2MB)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Registrar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@push('css')
<style>
    .hover-scale {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-scale:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    .avatar-initials {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .input-group-glass {
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 8px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }
    .input-group-glass:focus-within {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
</style>
@endpush

@push('js')
<script>
    // Función para mostrar/ocultar contraseña
    function togglePassword(id) {
        const input = document.getElementById(id);
        if (input.type === "password") {
            input.type = "text";
        } else {
            input.type = "password";
        }
    }

    // Vista previa de imagen para crear usuario
    document.getElementById('imageUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('imagePreview');
        const defaultIcon = document.getElementById('defaultIcon');
        
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(event) {
                preview.src = event.target.result;
                preview.classList.remove('d-none');
                defaultIcon.classList.add('d-none');
            };
            
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('d-none');
            defaultIcon.classList.remove('d-none');
        }
    });

    // Vista previa de imagen para editar usuario
    function previewImage(input, userId) {
        const file = input.files[0];
        const currentImage = document.getElementById(`currentImage${userId}`);
        const currentInitials = document.getElementById(`currentInitials${userId}`);
        
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(event) {
                if (!currentImage) {
                    // Crear elemento img si no existe
                    const avatarDiv = input.closest('.d-flex').querySelector('.avatar');
                    const newImg = document.createElement('img');
                    newImg.id = `currentImage${userId}`;
                    newImg.className = 'rounded-circle border border-2 border-success';
                    newImg.src = event.target.result;
                    newImg.alt = 'Preview';
                    avatarDiv.insertBefore(newImg, avatarDiv.firstChild);
                    
                    // Ocultar iniciales
                    if (currentInitials) {
                        currentInitials.classList.add('d-none');
                    }
                } else {
                    currentImage.src = event.target.result;
                }
            };
            
            reader.readAsDataURL(file);
        }
    }

    // Inicializar tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Confirmación antes de eliminar
        document.querySelectorAll('form[action*="destroy"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
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
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
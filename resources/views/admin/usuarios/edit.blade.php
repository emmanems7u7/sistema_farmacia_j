extends('adminlte::page')

@section('title', 'Editar Rol')

@section('content_header')
    <h1><b>Editar Rol</b></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Actualizar Rol</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">Nombre del Rol</label>
                            <select name="role" id="" class="form-control" >
                             <option value="">Seleccionar un Rol</option>
                            @foreach($roles as $role)
                             <option value="{{$role->name }}" {{$role->name  == $usuario->roles->pluck('name')->implode(', ') ? 'selected':''}}>{{$role->name}}</option>
                           @endforeach
                             </select>                           
                          
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar
                            </button>
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop














@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Gestión de Usuarios'])

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Primer Card para el título y botones -->
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>
                        <i class="ni ni-single-02 text-primary me-2"></i>
                        <strong>GESTIÓN DE USUARIOS</strong>
                    </h6>
                    <div>
                        <a href="{{ url('/admin/roles/reporte') }}" target="_blank" class="btn btn-sm btn-danger me-2">
                            <i class="ni ni-single-copy-04 me-1"></i> Generar Reporte
                        </a>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrear">
                            <i class="ni ni-fat-add me-1"></i> Nuevo Usuario
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Segundo Card para la tabla de usuarios -->
            <div class="container-fluid py-4">
    <div class="row">
        <!-- Card de Encabezado -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-users me-2"></i> Gestión de Usuarios
                        </h5>
                        <p class="text-sm text-muted mb-0">Total: {{ count($usuarios) }} usuarios registrados</p>
                    </div>
                    <div class="d-flex">
                        <div class="input-group input-group-sm me-3" style="width: 200px;">
                            <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" placeholder="Buscar...">
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrear">
                            <i class="fas fa-plus me-1"></i> Nuevo Usuario
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Usuarios -->
        @foreach($usuarios as $usuario)
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
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
                            @php
                                $roleName = $usuario->roles->first()->name ?? 'Sin rol';
                                $roleColor = match($roleName) {
                                    'Admin' => 'bg-danger',
                                    'Editor' => 'bg-warning',
                                    'Usuario' => 'bg-success',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $roleColor }} bg-opacity-10 text-dark rounded-pill px-3 py-1 small">
                                {{ $roleName }}
                            </span>
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
                        <button type="button" class="btn btn-sm btn-icon btn-outline-primary rounded-circle me-2" 
                            data-bs-toggle="modal" data-bs-target="#verModal{{ $usuario->id }}"
                            data-bs-toggle="tooltip" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                        
                        <button type="button" class="btn btn-sm btn-icon btn-outline-success rounded-circle me-2" 
                            data-bs-toggle="modal" data-bs-target="#editarModal{{ $usuario->id }}"
                            data-bs-toggle="tooltip" title="Editar">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        
                        <form action="{{ route('admin.usuarios.destroy', $usuario->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-icon btn-outline-danger rounded-circle"
                                data-bs-toggle="tooltip" title="Eliminar"
                                onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

        </div>
    </div>
</div>

@foreach($usuarios as $usuario)
<div class="modal fade" id="verModal{{ $usuario->id }}" tabindex="-1" aria-labelledby="verModalLabel{{ $usuario->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-gradient-primary text-white">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-xl me-3">
                        @if($usuario->imagen)
                            <img src="{{ asset('storage/'.$usuario->imagen) }}" 
                                class="rounded-circle border border-3 border-white shadow" 
                                alt="Foto de perfil">
                        @else
                            <div class="avatar-initials bg-white rounded-circle shadow d-flex align-items-center justify-content-center border border-3 border-white">
                                <span class="text-primary fw-bold fs-4">{{ substr($usuario->firstname, 0, 1) }}{{ substr($usuario->lastname ?? '', 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="verModalLabel{{ $usuario->id }}">
                            {{ $usuario->firstname }} {{ $usuario->lastname }}
                        </h5>
                        <p class="text-white-50 small mb-0">{{ $usuario->role->name ?? 'Sin rol' }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Cuerpo del Modal -->
            <div class="modal-body p-4">
                <div class="row">
                    <!-- Columna Izquierda -->
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h6 class="text-uppercase text-primary mb-3">
                                <i class="fas fa-id-card me-2"></i>Información Personal
                            </h6>
                            <div class="card border-0 shadow-none">
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item border-0 px-0 py-2 d-flex align-items-center">
                                            <i class="fas fa-user-circle text-primary me-3"></i>
                                            <div>
                                                <small class="text-muted">Usuario</small>
                                                <p class="mb-0 fw-bold">{{ $usuario->username }}</p>
                                            </div>
                                        </li>
                                        <li class="list-group-item border-0 px-0 py-2 d-flex align-items-center">
                                            <i class="fas fa-signature text-primary me-3"></i>
                                            <div>
                                                <small class="text-muted">Nombre completo</small>
                                                <p class="mb-0 fw-bold">{{ $usuario->firstname }} {{ $usuario->lastname }}</p>
                                            </div>
                                        </li>
                                        <li class="list-group-item border-0 px-0 py-2 d-flex align-items-center">
                                            <i class="fas fa-calendar-alt text-primary me-3"></i>
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
                    
                    <!-- Columna Derecha -->
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h6 class="text-uppercase text-primary mb-3">
                                <i class="fas fa-address-book me-2"></i>Información de Contacto
                            </h6>
                            <div class="card border-0 shadow-none">
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item border-0 px-0 py-2 d-flex align-items-center">
                                            <i class="fas fa-envelope text-primary me-3"></i>
                                            <div>
                                                <small class="text-muted">Correo electrónico</small>
                                                <p class="mb-0 fw-bold">{{ $usuario->email }}</p>
                                            </div>
                                        </li>
                                        <li class="list-group-item border-0 px-0 py-2 d-flex align-items-center">
                                            <i class="fas fa-mobile-alt text-primary me-3"></i>
                                            <div>
                                                <small class="text-muted">Celular</small>
                                                <p class="mb-0 fw-bold">{{ $usuario->celular ?? 'No especificado' }}</p>
                                            </div>
                                        </li>
                                        <li class="list-group-item border-0 px-0 py-2 d-flex align-items-center">
                                            <i class="fas fa-map-marker-alt text-primary me-3"></i>
                                            <div>
                                                <small class="text-muted">Dirección</small>
                                                <p class="mb-0 fw-bold">{{ $usuario->address ?? 'No especificada' }}</p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sección de Rol -->
                        <div class="mb-3">
                            <h6 class="text-uppercase text-primary mb-3">
                                <i class="fas fa-user-shield me-2"></i>Permisos y Accesos
                            </h6>
                            <div class="card border-0 shadow-none">
                                <div class="card-body p-3 bg-light rounded">
                                    <div class="d-flex align-items-center">
                                        @php
                                            $roleColor = match($usuario->role->name ?? '') {
                                                'Admin' => 'bg-danger',
                                                'Editor' => 'bg-warning',
                                                'Usuario' => 'bg-success',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $roleColor }} bg-opacity-10 {{ str_replace('bg-', 'text-', $roleColor) }} rounded-pill px-3 py-2 me-3">
                                            <i class="fas fa-user-tag me-1"></i>{{ $usuario->role->name ?? 'Sin rol' }}
                                        </span>
                                        <small class="text-muted">Último acceso: {{ $usuario->last_login_at ? $usuario->last_login_at->format('d/m/Y H:i') : 'Nunca' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pie del Modal -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cerrar
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editarModal{{ $usuario->id }}" data-bs-dismiss="modal">
                    <i class="fas fa-edit me-2"></i>Editar Usuario
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Modal para Editar Usuario -->
@foreach($usuarios as $usuario)
<div class="modal fade" id="editarModal{{ $usuario->id }}" tabindex="-1" aria-labelledby="editarModalLabel{{ $usuario->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Encabezado del Modal -->
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
                        <p class="text-white-50 small mb-0">ID: {{ $usuario->id }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Cuerpo del Modal -->
            <form action="{{ url('/admin/usuarios', $usuario->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row">
                        <!-- Columna Izquierda -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-uppercase text-success mb-3 border-bottom pb-2">
                                    <i class="fas fa-user-cog me-2"></i>Información Básica
                                </h6>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Rol del Usuario</label>
                                    <select name="role" class="form-select shadow-sm" required>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" {{ $role->name == $usuario->roles->pluck('name')->implode(', ') ? 'selected' : '' }}>
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
                        
                        <!-- Columna Derecha -->
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
                            </div>
                            
                            <!-- Avatar Upload -->
                            <div class="mb-3">
                                <h6 class="text-uppercase text-success mb-3 border-bottom pb-2">
                                    <i class="fas fa-image me-2"></i>Imagen de Perfil
                                </h6>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-lg me-3">
                                        @if($usuario->imagen)
                                            <img src="{{ asset('storage/'.$usuario->imagen) }}" 
                                                class="rounded-circle border border-2 border-success" 
                                                alt="Foto de perfil">
                                        @else
                                            <div class="avatar-initials bg-light rounded-circle d-flex align-items-center justify-content-center border border-2 border-success">
                                                <span class="text-success fw-bold">{{ substr($usuario->firstname, 0, 1) }}{{ substr($usuario->lastname ?? '', 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <label for="imageUpload{{ $usuario->id }}" class="btn btn-sm btn-outline-success mb-0">
                                            <i class="fas fa-camera me-1"></i>Cambiar Imagen
                                        </label>
                                        <input type="file" id="imageUpload{{ $usuario->id }}" name="imagen" accept="image/*" class="d-none">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pie del Modal -->
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

<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        if (input.type === "password") {
            input.type = "text";
        } else {
            input.type = "password";
        }
    }
</script>
@endforeach

<!-- Modal para Crear Usuario -->
<!-- Modal para Crear Usuario -->
<div class="modal fade" id="modalCrear" tabindex="-1" aria-labelledby="modalCrearLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-gradient-primary text-white">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-xl me-3 bg-white rounded-circle d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-plus text-primary fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="modalCrearLabel">
                            <i class="fas fa-user-plus me-2"></i>Nuevo Usuario
                        </h5>
                        <p class="text-white-50 small mb-0">Complete todos los campos requeridos</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Cuerpo del Modal -->
            <form action="{{ route('admin.usuarios.create') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <!-- Columna Izquierda -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-uppercase text-primary mb-3 border-bottom pb-2">
                                    <i class="fas fa-user-tag me-2"></i>Información Personal
                                </h6>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Sucursal <span class="text-danger">*</span></label>
                                    <select name="sucursal" class="form-select shadow-sm" required>
                                        <option value="">Seleccionar sucursal...</option>
                                        @foreach($sucursales as $sucursal)
                                            <option value="{{ $sucursal->id }}" {{ old('sucursal') == $sucursal->id ? 'selected' : '' }}>
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
                        
                        <!-- Columna Derecha -->
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
                                    <input type="email" class="form-control shadow-sm" name="email" value="{{ old('email') }}" required placeholder="ejemplo@dominio.com">
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
                
                <!-- Pie del Modal -->
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

<script>
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
</script>
@endsection

@push('js')
<script>
    // Inicializar tooltips
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

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
</script>
@endpush







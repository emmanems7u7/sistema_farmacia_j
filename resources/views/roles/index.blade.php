@extends('layouts.argon')

@section('content')
    <div class="card shadow-lg mx-4 card-profile-bottom">
        <div class="card-body p-3">
            <p>Roles</p>
            <div class="row mt-3">
                <div class="col">

                    <a href="{{ route('roles.create') }}" class="btn btn-primary">Crear Nuevo Rol</a>

                </div>
            </div>
        </div>

    </div>
    <div class="row mt-3">
        @foreach($roles as $role)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border">
                    <div class="card-body">
                        <h5 class="card-title text-primary">
                            <i class="fas fa-user-tag me-2"></i> {{ $role->name }}
                        </h5>

                        <p class="mb-2 text-muted"><strong>Permisos:</strong></p>
                        <div style="max-height: 122px; overflow-y: auto; padding-right: 10px;">
                            @foreach($role->permissions as $permission)
                                <span class="badge bg-info me-1 mb-1">{{ $permission->name }}</span>
                            @endforeach
                        </div>


                    </div>
                    <div class="card-footer">


                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;"
                            id="delete-form-{{ $role->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-sm btn-danger"
                                onclick="confirmarEliminacion('delete-form-{{ $role->id }}' , '¿Estás seguro de eliminar este rol?')">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        </form>
                        <a href="{{ route('roles.edit', ['id' => $role->id]) }}" class="btn btn-sm btn-warning me-2">
                            <i class="fas fa-edit"></i> Editar
                        </a>

                    </div>
                </div>
            </div>
        @endforeach
    </div>


    <script>
        /*
        function confirmDelete(roleId) {
            alertify.confirm(
                'Confirmar Eliminación',
                '¿Estás seguro de eliminar este rol?',
                function () {

                    document.getElementById('delete-form-' + roleId).submit();
                },
                function () {

                    alertify.error('Eliminación cancelada');
                }
            ).set('labels', { ok: 'Eliminar', cancel: 'Cancelar' }); // Opcional: Cambia los textos de los botones
        }*/
    </script>

@endsection
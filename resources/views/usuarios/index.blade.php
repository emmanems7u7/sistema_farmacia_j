@extends('layouts.argon')

@section('content')
    <script src="{{ asset('js/app.js') }}"></script>

    <div class="card card-frame">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    Acciones
                </div>

            </div>

            <div class="row mt-3">
                <div class="col">
                    <div class="col">

                        <button type="button" class="btn btn-primary mb-3" id="modal_create_usuario_button"
                            data-bs-toggle="modal" data-bs-target="#modal_create_usuario">Crear Usuario</button>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('usuarios.create')
    @include('usuarios.edit')

    <div class="card mt-3">
        <div class="table-responsive">
            <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Usuario</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nombres</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                            Telefono</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ultimo
                            acceso
                        </th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($users as $usuario)
                        <tr>
                            <td>
                                <div class="d-flex px-2 py-1">
                                    <div>
                                        <img src="https://demos.creative-tim.com/soft-ui-design-system-pro/assets/img/team-2.jpg"
                                            class="avatar avatar-sm me-3">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-xs">{{ $usuario->name }}
                                        </h6>
                                        <p class="text-xs text-secondary mb-0">{{ $usuario->email }}</p>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <p class="text-xs font-weight-bold mb-0">{{ $usuario->usuario_nombres }}
                                    {{ $usuario->usuario_app }}
                                    {{ $usuario->usuario_apm }}
                                </p>

                            </td>

                            <td class="align-middle text-center text-sm">
                                <p class="text-xs font-weight-bold mb-0">{{ $usuario->usuario_telefono }}

                                </p>
                            </td>

                            <td class="align-middle text-center">
                                <span
                                    class="text-secondary text-xs font-weight-bold">{{ $usuario->usuario_fecha_ultimo_acceso }}</span>
                            </td>

                            <td class="align-middle">
                                <a type="button" class="text-secondary font-weight-bold text-xs" id="modal_edit_usuario_button"
                                    onclick="editarModal('{{  $usuario->id }}')">Editar Usuario</a>
                                <a type="button" class="text-secondary font-weight-bold text-xs" id="modal_edit_usuario_button"
                                    onclick="confirmarEliminar('{{ $usuario->id }}')">Eliminar Usuario</a>

                                <form id="eliminarUsuarioForm" method="POST"
                                    action="{{ route('users.destroy', ['user' => $usuario->id]) }}" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>

                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>

    <script>

        function confirmarEliminar(userId) {
            alertify.confirm('Confirmar eliminación', '¿Estás seguro de que deseas eliminar este usuario?',
                function () {
                    // Cuando el usuario confirma, enviamos el formulario.
                    document.getElementById('eliminarUsuarioForm').submit();
                },
                function () {
                    // Si el usuario cancela, mostramos un mensaje.
                    alertify.error('Eliminación cancelada');
                });
        }
        var ModalEditar = new bootstrap.Modal(document.getElementById('modal_edit_usuario'));

        function editarModal(user_id) {

            fetch(`/datos/usuario/${user_id}`)
                .then(response => response.json())
                .then(data => {

                    var actionUrl = "{{ route('users.update', ['id' => '_id_']) }}";
                    actionUrl = actionUrl.replace('_id_', user_id);

                    document.getElementById('form_edit').action = actionUrl;
                    document.getElementById('user_id').value = data.user_id;
                    document.getElementById('name_edit').value = data.name;
                    document.getElementById('email_edit').value = data.email;
                    document.getElementById('usuario_nombres_edit').value = data.usuario_nombres;
                    document.getElementById('usuario_app_edit').value = data.usuario_app;
                    document.getElementById('usuario_apm_edit').value = data.usuario_apm;
                    document.getElementById('usuario_telefono_edit').value = data.usuario_telefono;
                    document.getElementById('usuario_direccion_edit').value = data.usuario_direccion;
                    ModalEditar.show();
                })
                .catch(error => console.error('Error al obtener los datos:', error));
        }

        document.addEventListener('DOMContentLoaded', function () {

            // Verifica si hay errores de validación
            let hasErrors = @json($errors->any());
            let formAction = @json(session('form_action'));
            let user_id = @json(session('user_id'));
            if (hasErrors) {
                if (formAction == 'update') {
                    editarModal(user_id)
                }
                else {
                    document.getElementById('modal_create_usuario_button').click();
                }

            }

        });

    </script>
@endsection
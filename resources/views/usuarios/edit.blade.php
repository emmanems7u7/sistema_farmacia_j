@extends('layouts.argon')

@section('content')

    <div class="card shadow-lg mx-4 card-profile-bottom">
        <div class="card-body p-3">
            <h3 class="font-weight-bolder text-info text-gradient">Actualizar Usuario</h3>

            <div class="row mt-3">
                <div class="col">

                </div>

            </div>

        </div>

    </div>

    <div class="card mt-3">
        <div class="card-body ">
            <div class="row mt-3">
                <form method="POST" id="form_edit" action="{{ route('users.update', ['id' => $user->id, 'perfil' => 0]) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">

                        <div class="col-12 mb-3">
                            <label for="name_edit">Nombre de Usuario</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name_edit"
                                name="name" placeholder="Nombre de usuario" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label for="email_edit">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email_edit"
                                name="email" placeholder="Email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="usuario_nombres_edit">Nombres</label>
                            <input type="text" class="form-control @error('usuario_nombres') is-invalid @enderror"
                                id="usuario_nombres_edit" name="usuario_nombres" placeholder="Nombre(s)"
                                value="{{ old('usuario_nombres', $user->usuario_nombres) }}" required>
                            @error('usuario_nombres')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="usuario_app_edit">Apellido Paterno</label>
                            <input type="text" class="form-control @error('usuario_app') is-invalid @enderror"
                                id="usuario_app_edit" name="usuario_app" placeholder="Apellido Paterno"
                                value="{{ old('usuario_app', $user->usuario_app) }}" required>
                            @error('usuario_app')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="usuario_apm_edit">Apellido Materno</label>
                            <input type="text" class="form-control @error('usuario_apm') is-invalid @enderror"
                                id="usuario_apm_edit" name="usuario_apm" placeholder="Apellido Materno"
                                value="{{ old('usuario_apm', $user->usuario_apm) }}" required>
                            @error('usuario_apm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="usuario_telefono_edit">Teléfono</label>
                            <input type="tel" class="form-control @error('usuario_telefono') is-invalid @enderror"
                                id="usuario_telefono_edit" name="usuario_telefono" placeholder="Teléfono"
                                value="{{ old('usuario_telefono', $user->usuario_telefono) }}" required>
                            @error('usuario_telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label for="usuario_direccion_edit">Dirección</label>
                            <input type="text" class="form-control @error('usuario_direccion') is-invalid @enderror"
                                id="usuario_direccion_edit" name="usuario_direccion" placeholder="Dirección"
                                value="{{ old('usuario_direccion', $user->usuario_direccion) }}" required>
                            @error('usuario_direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="role">Rol</label>
                            <select name="role" id="role" class="form-control" required>
                                @foreach(\Spatie\Permission\Models\Role::all() as $role)
                                    <option value="{{ $role->name }}" {{ $user->getRoleNames()->first() === $role->name ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-12 text-center">

                            <button type="submit" class="btn btn-round bg-gradient-info btn-lg w-100 mt-4 mb-0">
                                Actualizar Usuario
                            </button>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>




@endsection
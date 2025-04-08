<div class="modal fade" id="modal_create_usuario" tabindex="-1" role="dialog" aria-labelledby="modal_create_usuario"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="card card-plain">
                    <div class="card-header pb-0 text-left">
                        <h3 class="font-weight-bolder text-info text-gradient">Crear un Nuevo Usuario</h3>
                        <p class="mb-0">Ingresa todos los datos obligatorios</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('users.store') }}">
                            @csrf
                            <div class="row">

                                <div class="col-12 mb-3">
                                    <label for="name">Nombre de Usuario</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Nombre de usuario" value="{{ old('name') }}"
                                        required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-12 mb-3">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-12 col-md-6 mb-3">
                                    <label for="usuario_nombres">Nombres</label>
                                    <input type="text"
                                        class="form-control @error('usuario_nombres') is-invalid @enderror"
                                        id="usuario_nombres" name="usuario_nombres" placeholder="Nombre(s)"
                                        value="{{ old('usuario_nombres') }}" required>
                                    @error('usuario_nombres')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-12 col-md-6 mb-3">
                                    <label for="usuario_app">Apellido Paterno</label>
                                    <input type="text" class="form-control @error('usuario_app') is-invalid @enderror"
                                        id="usuario_app" name="usuario_app" placeholder="Apellido Paterno"
                                        value="{{ old('usuario_app') }}" required>
                                    @error('usuario_app')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-12 col-md-6 mb-3">
                                    <label for="usuario_apm">Apellido Materno</label>
                                    <input type="text" class="form-control @error('usuario_apm') is-invalid @enderror"
                                        id="usuario_apm" name="usuario_apm" placeholder="Apellido Materno"
                                        value="{{ old('usuario_apm') }}" required>
                                    @error('usuario_apm')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-12 col-md-6 mb-3">
                                    <label for="usuario_telefono">Teléfono</label>
                                    <input type="tel"
                                        class="form-control @error('usuario_telefono') is-invalid @enderror"
                                        id="usuario_telefono" name="usuario_telefono" placeholder="Teléfono"
                                        value="{{ old('usuario_telefono') }}" required>
                                    @error('usuario_telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-12 mb-3">
                                    <label for="usuario_direccion">Dirección</label>
                                    <input type="text"
                                        class="form-control @error('usuario_direccion') is-invalid @enderror"
                                        id="usuario_direccion" name="usuario_direccion" placeholder="Dirección"
                                        value="{{ old('usuario_direccion') }}" required>
                                    @error('usuario_direccion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 text-center">
                                    <button type="submit"
                                        class="btn btn-round bg-gradient-info btn-lg w-100 mt-4 mb-0">Registrar
                                        Usuario</button>
                                </div>
                            </div>
                        </form>



                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
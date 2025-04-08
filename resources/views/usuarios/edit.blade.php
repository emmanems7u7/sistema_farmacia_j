<div class="modal fade" id="modal_edit_usuario" tabindex="-1" role="dialog" aria-labelledby="modal_edit_usuario"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="card card-plain">
                    <div class="card-header pb-0 text-left">
                        <h3 class="font-weight-bolder text-info text-gradient">Editar Usuario</h3>
                        <p class="mb-0">Modifica los campos que corresponden</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" id='form_edit' action="">
                        
                            @csrf
                            @method('PUT')
                            <input type="hidden" value="" id='user_id' name="user_id">
                            <div class="row">

                                <div class="col-12 mb-3">
                                    <label for="name_edit">Nombre de Usuario</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name_edit" name="name" placeholder="Nombre de usuario" value="{{ old('name_edit') }}"
                                        required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-12 mb-3">
                                    <label for="email_edit">Email</label>
                                    <input type="email_edit" class="form-control @error('email') is-invalid @enderror"
                                        id="email_edit" name="email" placeholder="Email_edit" value="{{ old('email_edit') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-12 col-md-6 mb-3">
                                    <label for="usuario_nombres_edit">Nombres</label>
                                    <input type="text"
                                        class="form-control @error('usuario_nombres') is-invalid @enderror"
                                        id="usuario_nombres_edit" name="usuario_nombres" placeholder="Nombre(s)"
                                        value="{{ old('usuario_nombres_edit') }}" required>
                                    @error('usuario_nombres')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-12 col-md-6 mb-3">
                                    <label for="usuario_app_edit">Apellido Paterno</label>
                                    <input type="text" class="form-control @error('usuario_app') is-invalid @enderror"
                                        id="usuario_app_edit" name="usuario_app" placeholder="Apellido Paterno"
                                        value="{{ old('usuario_app_edit') }}" required>
                                    @error('usuario_app')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-12 col-md-6 mb-3">
                                    <label for="usuario_apm_edit">Apellido Materno</label>
                                    <input type="text" class="form-control @error('usuario_apm') is-invalid @enderror"
                                        id="usuario_apm_edit" name="usuario_apm" placeholder="Apellido Materno"
                                        value="{{ old('usuario_apm_edit') }}" required>
                                    @error('usuario_apm')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-12 col-md-6 mb-3">
                                    <label for="usuario_telefono_edit">Teléfono</label>
                                    <input type="tel"
                                        class="form-control @error('usuario_telefono') is-invalid @enderror"
                                        id="usuario_telefono_edit" name="usuario_telefono" placeholder="Teléfono"
                                        value="{{ old('usuario_telefono_edit') }}" required>
                                    @error('usuario_telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-12 mb-3">
                                    <label for="usuario_direccion_edit">Dirección</label>
                                    <input type="text"
                                        class="form-control @error('usuario_direccion') is-invalid @enderror"
                                        id="usuario_direccion_edit" name="usuario_direccion" placeholder="Dirección"
                                        value="{{ old('usuario_direccion_edit') }}" required>
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


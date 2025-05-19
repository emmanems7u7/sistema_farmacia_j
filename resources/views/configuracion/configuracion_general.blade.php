@extends('layouts.argon')

@section('content')

        <div class="card shadow-lg mx-4 card-profile-bottom">
            <div class="card-body p-3">
                <p>Configuración General del Sistema</p>
            </div>
        </div>
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">

                        <div class="card-body">

                            <form method="POST" action="{{ route('admin.configuracion.update') }}">
                                @csrf
                                @method('PUT')
                                  <!-- API KEY IA GROQ -->
                                  <div class="mb-3">
                                    <label for="GROQ_API_KEY" class="form-label"> API KEY IA GROQ</label>
                                    <input type="text" class="form-control" id="GROQ_API_KEY"
                                        name="GROQ_API_KEY" value="{{ $config->GROQ_API_KEY }}">
                                </div>

                                <!-- Activación de 2FA -->
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="2faSwitch"
                                        name="doble_factor_autenticacion" {{ $config->doble_factor_autenticacion ? 'checked' : '' }}>
                                    <label class="form-check-label" for="2faSwitch">
                                        Activar verificación en dos pasos (2FA)
                                    </label>
                                </div>

                                <!-- Límite de sesiones -->
                                <div class="mb-3">
                                    <label for="limite_de_sesiones" class="form-label">Límite de sesiones</label>
                                    <input type="number" class="form-control" id="limite_de_sesiones"
                                        name="limite_de_sesiones" value="{{ $config->limite_de_sesiones }}">
                                </div>

                                @can('configuracion.actualizar')
                                <button type="submit" class="btn btn-primary mt-3">Guardar cambios</button>
                                @endcan

                            </form>

                        </div>

                    </div>


                </div>
            </div>

        </div>

   

@endsection
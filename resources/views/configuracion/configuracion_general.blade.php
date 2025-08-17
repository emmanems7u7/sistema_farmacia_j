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

                        <!-- Mostrar errores generales -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.configuracion.update') }}">
                            @csrf
                            @method('PUT')
                              
                            <!-- API KEY IA GROQ -->
                            <div class="mb-3">
                                <label for="GROQ_API_KEY" class="form-label"> API KEY IA GROQ</label>
                                <input type="text" 
                                       class="form-control @error('GROQ_API_KEY') is-invalid @enderror" 
                                       id="GROQ_API_KEY"
                                       name="GROQ_API_KEY" 
                                       value="{{ old('GROQ_API_KEY', $config->GROQ_API_KEY) }}">
                                @error('GROQ_API_KEY')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Activación de 2FA -->
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="2faSwitch"
                                    name="doble_factor_autenticacion" 
                                    {{ old('doble_factor_autenticacion', $config->doble_factor_autenticacion) ? 'checked' : '' }}>
                                <label class="form-check-label" for="2faSwitch">
                                    Activar verificación en dos pasos (2FA)
                                </label>
                            </div>

                            <!-- Mantenimiento -->
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="mant"
                                    name="mantenimiento" 
                                    {{ old('mantenimiento', $config->mantenimiento) ? 'checked' : '' }}>
                                <label class="form-check-label text-black" for="mant">
                                    Activar modo Mantenimiento
                                </label>
                            </div>

                            <!-- Límite de sesiones -->
                            <div class="mb-3">
                                <label for="limite_de_sesiones" class="form-label">Límite de sesiones</label>
                                <input type="number" 
                                       class="form-control @error('limite_de_sesiones') is-invalid @enderror" 
                                       id="limite_de_sesiones"
                                       name="limite_de_sesiones" 
                                       value="{{ old('limite_de_sesiones', $config->limite_de_sesiones) }}">
                                @error('limite_de_sesiones')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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

@extends('layouts.argon')

@section('content')

    <div class="card shadow-lg mx-4 card-profile-bottom">
        <div class="card-body p-3">
            <p>{{ __('ui.settings_text') }} de {{ __('ui.email_text') }}</p>
        </div>
    </div>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h3>{{ __('ui.register_text') }} {{ __('ui.settings_text') }} de {{ __('ui.email_text') }}</h3>
                        <form action="{{ route('configuracion.correo.store') }}" method="POST">
                            @csrf

                            <!-- Mostrar errores globales -->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="conf_correo_protocol" class="form-label">Protocolo</label>
                                <select id="conf_correo_protocol" name="conf_correo_protocol"
                                    class="form-select @error('conf_correo_protocol') is-invalid @enderror">
                                    <option value="-1" {{ (isset($conf_correo) && $conf_correo->conf_protocol == -1) ? 'selected' : '' }}>--Seleccionar--</option>
                                    <option value="mail" {{ (isset($conf_correo) && $conf_correo->conf_protocol == 'mail') ? 'selected' : '' }} mitext="MAIL">MAIL</option>
                                    <option value="sendmail" {{ (isset($conf_correo) && $conf_correo->conf_protocol == 'sendmail') ? 'selected' : '' }} mitext="SENDMAIL">
                                        SENDMAIL</option>
                                    <option value="smtp" {{ (isset($conf_correo) && $conf_correo->conf_protocol == 'smtp') ? 'selected' : '' }} mitext="SMTP">SMTP</option>
                                </select>

                                @error('conf_correo_protocol')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                            </div>

                            <div class="mb-3">
                                <label for="conf_smtp_host" class="form-label">Servidor SMTP</label>
                                <input type="text" class="form-control @error('conf_smtp_host') is-invalid @enderror"
                                    id="conf_smtp_host" name="conf_smtp_host"
                                    value="{{ $conf_correo->conf_smtp_host ?? '' }}" required>
                                @error('conf_smtp_host')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="conf_smtp_port" class="form-label">Puerto SMTP</label>
                                <input type="number" class="form-control @error('conf_smtp_port') is-invalid @enderror"
                                    id="conf_smtp_port" name="conf_smtp_port"
                                    value="{{ $conf_correo->conf_smtp_port ?? '' }}" required>
                                @error('conf_smtp_port')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="conf_smtp_user" class="form-label">Usuario SMTP</label>
                                <input type="text" class="form-control @error('conf_smtp_user') is-invalid @enderror"
                                    id="conf_smtp_user" name="conf_smtp_user"
                                    value="{{ $conf_correo->conf_smtp_user ?? '' }}" required>
                                @error('conf_smtp_user')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="conf_smtp_pass" class="form-label">Contraseña SMTP</label>
                                <input type="password" class="form-control @error('conf_smtp_pass') is-invalid @enderror"
                                    id="conf_smtp_pass" name="conf_smtp_pass"
                                    value="{{ $conf_correo->conf_smtp_pass ?? '' }}" required>
                                @error('conf_smtp_pass')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="conf_mailtype" class="form-label">Tipo de {{ __('ui.email_text') }}</label>
                                <select class="form-select @error('conf_mailtype') is-invalid @enderror" id="conf_mailtype"
                                    name="conf_mailtype" required>
                                    <option value="-1" {{ (isset($conf_correo) && $conf_correo->conf_mailtype == -1) ? 'selected' : '' }}>--Seleccionar--</option>
                                    <option value="html" {{ isset($conf_correo) && $conf_correo->conf_mailtype == 'html' ? 'selected' : '' }}>HTML</option>
                                    <option value="text" {{ isset($conf_correo) && $conf_correo->conf_mailtype == 'text' ? 'selected' : '' }}>Texto Plano</option>

                                </select>
                                @error('conf_mailtype')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="conf_charset" class="form-label">Charset</label>
                                <input type="text" class="form-control @error('conf_charset') is-invalid @enderror"
                                    id="conf_charset" name="conf_charset" value="{{ $conf_correo->conf_charset ?? '' }}"
                                    required>
                                @error('conf_charset')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="conf_in_background" class="form-label">¿Enviar en segundo plano?</label>
                                <select class="form-select @error('conf_in_background') is-invalid @enderror"
                                    id="conf_in_background" name="conf_in_background">
                                    <option value="-1" {{ (isset($conf_correo) && $conf_correo->conf_in_background == -1) ? 'selected' : '' }}>--Seleccionar--</option>
                                    <option value="1" {{ isset($conf_correo) && $conf_correo->conf_in_background == 1 ? 'selected' : '' }}>Sí</option>
                                    <option value="0" {{ isset($conf_correo) && $conf_correo->conf_in_background == 0 ? 'selected' : '' }}>No</option>

                                </select>
                                @error('conf_in_background')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-center">
                                @can('configuracion_correo.actualizar')
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> {{ __('ui.save_text') }} {{ __('ui.settings_text') }}
                                    </button>
                                @endcan
                            </div>
                        </form>


                    </div>

                </div>

                @if($conf_correo)
                    <div class="card mt-3">
                        <div class="card-body">
                            <p>Prueba de envio de correo</p>

                            <p onclick="enviarPrueba()" class="" style="cursor: pointer;">
                                <strong class="text-primary fw-bold"> Enviar correo de prueba a
                                </strong>{{ $conf_correo->conf_smtp_user }}
                            </p>

                        </div>

                    </div>
                @else
                @endif
            </div>
        </div>

    </div>

    <script>
        function enviarPrueba() {
            const url = "{{ route('correo.prueba') }}";

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al enviar la prueba');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Respuesta:', data);
                    alert('Correo de prueba enviado con éxito');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al enviar correo de prueba');
                });
        }
    </script>

@endsection
@extends('layouts.argon')

@section('content')

    <div class="card shadow-lg mx-4 card-profile-bottom">
        <div class="card-body p-3">
            <p>Configuración de plantillas</p>
            <label for="email" class="form-label">Seleccione el correo para editar o ver
                Configurar</label>
            <select id="email" name="email" class="form-select" onchange="actualizarConfiguracion(this.value)">
                <option value="-1" {{ (isset($conf_correo) && $conf_correo->conf_protocol == -1) ? 'selected' : '' }}>
                    --Seleccionar--</option>
                @foreach ($emails as $email)
                    <option value="{{ $email->id }}" {{ (isset($conf_correo) && $conf_correo->conf_protocol == $email->id) ? 'selected' : '' }}>
                        {{ $email->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h3>Configuración de Plantilla de Correo</h3>

                        <div id="datos_plantilla" style="display: none;">
                            <form action="" id="form_plantilla" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="nombre_plantilla" class="form-label">Nombre de Plantilla</label>
                                    <input type="text" class="form-control @error('nombre_plantilla') is-invalid @enderror"
                                        id="nombre_plantilla" name="nombre_plantilla"
                                        value="{{ $conf_correo->nombre ?? '' }}" required>
                                    @error('nombre_plantilla')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="asunto_plantilla" class="form-label">Asunto de Plantilla</label>
                                    <input type="text" class="form-control @error('asunto_plantilla') is-invalid @enderror"
                                        id="asunto_plantilla" name="asunto_plantilla"
                                        value="{{ $conf_correo->asunto ?? '' }}" required>
                                    @error('asunto_plantilla')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="contenido" class="form-label">Variables disponibles para usar</label>
                                    <div class="row">
                                        @foreach ($variablesPlantilla as $variable)
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-sm bg-gradient-info btn-tooltip w-100"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ $variable->descripcion }}">
                                                    {{ $variable->nombre }}
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>

                                </div>
                                <div class="mb-3">
                                    <label for="contenido" class="form-label">Contenido</label>
                                    <textarea id="contenido" name="contenido"
                                        class="form-control @error('contenido') is-invalid @enderror" rows="10"></textarea>
                                    @error('contenido')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @can('plantillas.actualizar')
                                    <div class="d-flex justify-content-center">
                                        <button type="submit" class="btn btn-success btn-sm">Guardar</button>
                                    </div>
                                @endcan
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Script de CKEditor -->
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <script>
        let editor; // Lo declaramos fuera para accederlo globalmente

        ClassicEditor
            .create(document.querySelector('#contenido'))
            .then(newEditor => {
                editor = newEditor; // Asignamos el editor globalmente
            })
            .catch(error => {
                console.error(error);
            });
    </script>

    <script>


        function actualizarConfiguracion(email_id) {
            fetch(`/obtener/plantilla/${email_id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status == 'success') {
                        if (editor) {

                            var actionUrl = "{{ route('plantilla.update', ['id' => '_id_']) }}";
                            actionUrl = actionUrl.replace('_id_', data.email.id);

                            document.getElementById('nombre_plantilla').value = data.email.nombre;
                            document.getElementById('asunto_plantilla').value = data.email.asunto;
                            document.getElementById('form_plantilla').action = actionUrl;
                            document.getElementById('datos_plantilla').style.display = 'block';

                            editor.setData(data.email.contenido);
                        } else {
                            con * sole.error("El editor aún no está listo.");
                        }
                    }
                    else {

                    }

                })
                .catch(error => console.error('Error al obtener los datos:', error));



        }
    </script>

@endsection
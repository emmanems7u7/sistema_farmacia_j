@extends('layouts.argon')

@section('content')
    <script>
        function cargar_menus(permiso_id, rol_id = null) {
            fetch(`/permissions/cargar/menu/${permiso_id}/-1`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        generarMenu(data.permisosPorTipo, permiso_id);
                    }
                });
        }

        function generarMenu(permisos, permiso_id) {
            const menuContainer = document.getElementById('menuc_' + permiso_id);
            menuContainer.innerHTML = '';

            permisos.forEach(permiso => {
                const div = document.createElement('div');
                div.id = `menu_${permiso.id}`;

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.id = `checkbox_${permiso.id}`;
                checkbox.name = 'permissions[]';
                checkbox.value = permiso.name;
                checkbox.checked = permiso.check;

                const label = document.createElement('label');
                label.setAttribute('for', `checkbox_${permiso.id}`);
                label.textContent = permiso.name;

                div.appendChild(checkbox);
                div.appendChild(label);

                menuContainer.appendChild(div);
            });
        }

        function verificarPermiso(checkbox, permiso_id, rol_id = null) {
            const div = document.getElementById("menuc_" + permiso_id);

            if (checkbox.checked) {
                div.style.display = "block";
                cargar_menus(permiso_id);
            } else {
                div.style.display = "none";

            }
        }
    </script>


    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="card shadow-lg mx-4 card-profile-bottom">
            <div class="card-body p-3">
                <p>Crear Rol</p>
                <div class="row mt-3">
                    <div class="form-group">
                        <label for="roleName">Nombre del rol</label>
                        <input type="text" name="name" id="roleName"
                            class="form-control @error('name') is-invalid @enderror" placeholder="Ingrese el nombre del rol"
                            value="{{ old('name') }}">
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-lg mx-4 mt-3">
            <div class="card-body p-3">
                <div class="row">
                    {{-- Columna izquierda: accesos a menú --}}
                    <div class="col-md-6">
                        @foreach ($permisosPorTipo as $tipo => $permisos)
                            @if ($tipo != 'permiso' && $permisos->count() > 0)
                                <h4>Accesos a menú</h4>
                                @foreach ($permisos as $permiso)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="{{ $permiso->name }}" id="permiso_{{ $permiso->id }}"
                                            onclick="verificarPermiso(this, '{{ $permiso->id }}')">

                                        <label class="form-check-label" for="permiso_{{ $permiso->id }}">
                                            {{ $permiso->name }}
                                        </label>

                                        <div id="menuc_{{ $permiso->id }}" style="display: none;">
                                            <div id="menu_{{ $permiso->id }}"></div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>

                    {{-- Columna derecha: permisos --}}
                    <div class="col-md-6">
                        @if (isset($permisosPorTipo['permiso']) && $permisosPorTipo['permiso']->count() > 0)
                            <h4>Permisos</h4>

                            @php
                                // Agrupar permisos por prefijos (secciones)
                                $permisosPorSeccion = $permisosPorTipo['permiso']->groupBy(function ($permiso) {
                                    // Obtenemos el prefijo (la parte antes del punto)
                                    return explode('.', $permiso->name)[0];
                                });
                            @endphp

                            @foreach ($permisosPorSeccion as $seccion => $permisos)
                                <h5>{{ ucfirst($seccion) }}</h5>
                                <div class="d-flex flex-wrap" id="seccion_permiso_{{ $seccion }}">
                                    @foreach ($permisos as $permiso)
                                        <div class="form-check mb-2 me-3" style="min-width: 180px; ">
                                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                                value="{{ $permiso->name }}" id="permiso_{{ $permiso->id }}">

                                            <label class="form-check-label" for="permiso_{{ $permiso->id }}">
                                                {{ $permiso->name }}
                                            </label>

                                            <div id="menuc_{{ $permiso->id }}" style="padding-left: 20px">
                                                <div id="menu_{{ $permiso->id }}"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <button type="submit" class="btn btn-success">Crear Rol</button>
                </div>
            </div>
        </div>
    </form>

@endsection
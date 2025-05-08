@extends('layouts.argon')

@section('content')

<style>
@keyframes animarBorde {
  0%, 100% {
    border-color: transparent;
  }
  50% {
    border-color: rgba(0, 123, 255, 0.8);
  }
}

.borde-animado {
  border: 2px solid transparent;
  border-radius: 8px;
  animation: animarBorde 2s infinite;
}
</style>
    <script>
        function cargar_menus(permiso_id, rol_id) {

            fetch(`/permissions/cargar/menu/${permiso_id}/${rol_id}`)
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
                checkbox.classList.add('form-check-input');
               

              //  if (!checkbox.checked) {
                //checkbox.onclick = function() {
                //    const id_div = 'seccion_permiso_'+ permiso.name.toLowerCase();
               // animarBorde(id_div)
                //};
             //??   }
                

                const label = document.createElement('label');
                label.setAttribute('for', `checkbox_${permiso.id}`);
                label.textContent = permiso.name;

                div.appendChild(checkbox);
                div.appendChild(label);

                
                menuContainer.appendChild(div);
            });
        }
        function verificarPermiso(checkbox, permiso_id, rol_id,seccion=0) {
            const div = document.getElementById("menuc_" + permiso_id);
            
          
            if (checkbox.checked) {
               // const id_div = 'seccion_permiso_'+ seccion;
                //animarBorde(id_div)
                div.style.display = "block";
                cargar_menus(permiso_id, rol_id);

            }
            else {
                div.style.display = "none";
            }

        }

        function animarBorde(id) {

        const div = document.getElementById(id);

        if (div) {
                div.classList.add('borde-animado');

                setTimeout(() => {
                    div.classList.remove('borde-animado');
                }, 3500);
            
            }
        }  
    </script>
   
        <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card shadow-lg mx-4 card-profile-bottom">
                <div class="card-body p-3">
                    <p>Editar Rol</p>
                    <div class="row mt-3">
                        <div class="form-group">
                            <label for="roleName">Nombre del rol</label>
                            <input type="text" name="name" id="roleName" class="form-control" value="{{ $role->name }}">
                        </div>
                    </div>

                </div>

            </div>


            <div class="card shadow-lg mx-4 mt-3">
    <div class="card-body p-3">
        <div class="row">
            {{-- Columna izquierda: permisos que NO son tipo "permiso" --}}
            <div class="col-md-6">
                @foreach ($permisosPorTipo as $tipo => $permisos)
                    @if ($tipo != 'permiso' && $permisos->count() > 0)
                        <h4>Accesos a menú</h4>
                        @foreach ($permisos as $permiso)
                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permiso->name }}"
                                    id="permiso_{{ $permiso->id }}"
                                    {{ $role->hasPermissionTo($permiso) ? 'checked' : '' }}
                                    onclick="verificarPermiso(this, '{{ $permiso->id }}', '{{ $role->id }}')">

                                @if($role->hasPermissionTo($permiso))
                                    <script>
                                        cargar_menus('{{ $permiso->id }}', '{{ $role->id }}');
                                    </script>
                                @endif

                                <label class="form-check-label" for="permiso_{{ $permiso->id }}">
                                    {{ $permiso->name }}
                                </label>

                                <div id="menuc_{{ $permiso->id }}"style="padding-left: 20px">
                                    <div id="menu_{{ $permiso->id }}"></div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                @endforeach
            </div>

       
            <div class="col-md-6">
                @if (isset($permisosPorTipo['permiso']) && $permisosPorTipo['permiso']->count() > 0)
                    <h4>Permisos</h4>
                    
                    @php
                        // Agrupar permisos por prefijos (secciones)
                        $permisosPorSeccion = $permisosPorTipo['permiso']->groupBy(function($permiso) {
                            // Obtenemos el prefijo (la parte antes del punto)
                            return explode('.', $permiso->name)[0];
                        });
                    @endphp

                    @foreach ($permisosPorSeccion as $seccion => $permisos)
                        <h5>{{ ucfirst($seccion) }}</h5>
                        <div class="d-flex flex-wrap" id="seccion_permiso_{{ $seccion }}" > 
                            @foreach ($permisos as $permiso)
                                <div class="form-check mb-2 me-3" style="min-width: 180px; ">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permiso->name }}"
                                        id="permiso_{{ $permiso->id }}"
                                        {{ $role->hasPermissionTo($permiso) ? 'checked' : '' }}
                                        onclick="verificarPermiso(this, '{{ $permiso->id }}', '{{ $role->id }}')">

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
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </div>
</div>


        </form>
 

  


@endsection
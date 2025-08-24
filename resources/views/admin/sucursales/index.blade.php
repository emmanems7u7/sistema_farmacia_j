@extends('layouts.app', ['title' => 'Sucursales'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Sucursales'])

    <div class="container-fluid py-4">
        <!-- Primera tarjeta: Título y botón -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-radius-lg shadow">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class="ni ni-shop me-2 text-primary"></i>
                                <strong>Sucursales Registradas</strong>
                            </h5>
                        </div>
                        <button type="button" class="btn bg-gradient-primary mb-0" data-bs-toggle="modal"
                            data-bs-target="#modalCrear">
                            <i class="ni ni-fat-add me-1"></i> Nueva Sucursal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segunda tarjeta: Tabla de sucursales -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 border-radius-lg shadow">
                    <div class="card-header pb-0">
                        <h6 class="mb-0">
                            <i class="ni ni-bullet-list-67 me-2 text-primary"></i>
                            <strong>Listado de Sucurles</strong>
                        </h6>
                    </div>

                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="text-align: center">Nro</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="text-align: center">Imagen</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="text-align: center">Nombre</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="text-align: center">Correo</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="text-align: center">Dirección</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="text-align: center">Teléfono</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="text-align: center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $contador = 1; @endphp
                                    @foreach($sucursals as $sucursal)
                                        <tr>
                                            <td style="text-align: center; vertical-align: middle">{{ $contador++ }}</td>
                                            <td style="text-align: center; vertical-align: middle">
                                                @if($sucursal->imagen)
                                                    <img src="{{ asset('storage/' . $sucursal->imagen) }}"
                                                        class="avatar avatar-sm me-3 border-radius-lg" alt="Imagen Sucursal">
                                                @else
                                                    <span class="badge bg-gradient-secondary">Sin imagen</span>
                                                @endif
                                            </td>
                                            <td style="text-align: center; vertical-align: middle">{{ $sucursal->nombre }}</td>
                                            <td style="text-align: center; vertical-align: middle">{{ $sucursal->email }}</td>
                                            <td style="text-align: center; vertical-align: middle">{{ $sucursal->direccion }}
                                            </td>
                                            <td style="text-align: center; vertical-align: middle">{{ $sucursal->telefono }}
                                            </td>
                                            <td style="text-align: center; vertical-align: middle">
                                                <div class="btn-group" role="group">
                                                    <!-- Botón Editar - Azul con icono -->
                                                    <button type="button"
                                                        class="btn btn-sm bg-gradient-info text-white rounded-start shadow-sm px-3"
                                                        data-bs-toggle="modal" data-bs-target="#editModal{{ $sucursal->id }}"
                                                        title="Editar sucursal" data-bs-toggle="tooltip">
                                                        <span class="btn-inner--icon me-1">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </span>
                                                        <span class="btn-inner--text">Editar</span>
                                                    </button>

                                                    <!-- Botón Eliminar - Verde con icono -->
                                                    <form action="{{ route('admin.sucursals.destroy', $sucursal->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm bg-gradient-success text-white rounded-end shadow-sm px-3"
                                                            onclick="return confirm('¿Estás seguro de eliminar esta sucursal?')"
                                                            title="Eliminar sucursal" data-bs-toggle="tooltip">
                                                            <span class="btn-inner--icon me-1">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </span>
                                                            <span class="btn-inner--text">Eliminar</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Crear Nueva Sucursal -->
    <div class="modal fade" id="modalCrear" tabindex="-1" role="dialog" aria-labelledby="modalCrearLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-gradient-primary">
                    <h5 class="modal-title text-white" id="modalCrearLabel">
                        <i class="ni ni-fat-add me-2"></i><strong>Registrar Nueva Sucursal</strong>
                    </h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.sucursals.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="imagen" class="form-label">Imagen</label>
                                <input type="file" class="form-control" id="imagen" name="imagen" accept=".jpg, .jpeg, .png"
                                    required>
                                <div class="mt-2 text-center" id="imagePreview"></div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label for="nombre" class="form-control-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre"
                                        value="{{ old('nombre') }}" required>
                                    @error('nombre')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label for="email" class="form-control-label">Correo</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email') }}" required>
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label for="direccion" class="form-control-label">Dirección</label>
                                    <input type="text" class="form-control" id="direccion" name="direccion"
                                        value="{{ old('direccion') }}" required>
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label for="telefono" class="form-control-label">Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono"
                                        value="{{ old('telefono') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="ni ni-fat-remove me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ni ni-check-bold me-1"></i> Registrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modales para Editar (se generan dinámicamente para cada sucursal) -->
    @foreach($sucursals as $sucursal)
        <div class="modal fade" id="editModal{{ $sucursal->id }}" tabindex="-1" role="dialog"
            aria-labelledby="editModalLabel{{ $sucursal->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-gradient-success">
                        <h5 class="modal-title text-white" id="editModalLabel{{ $sucursal->id }}">
                            <i class="ni ni-ruler-pencil me-2"></i>Editar Sucursal
                        </h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.sucursals.update', $sucursal->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="imagen" class="form-label">Imagen</label>
                                    <input type="file" class="form-control" id="imagen" name="imagen"
                                        accept=".jpg, .jpeg, .png">
                                    @if($sucursal->imagen)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $sucursal->imagen) }}" class="img-thumbnail" width="100"
                                                alt="Imagen actual">
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label for="nombre" class="form-control-label">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre"
                                            value="{{ $sucursal->nombre }}" required>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label for="email" class="form-control-label">Correo</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ $sucursal->email }}" required>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label for="direccion" class="form-control-label">Dirección</label>
                                        <input type="text" class="form-control" id="direccion" name="direccion"
                                            value="{{ $sucursal->direccion }}" required>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label for="telefono" class="form-control-label">Teléfono</label>
                                        <input type="text" class="form-control" id="telefono" name="telefono"
                                            value="{{ $sucursal->telefono }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="ni ni-fat-remove me-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="ni ni-check-bold me-1"></i> Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    @push('js')
        <script>
            // Vista previa de imagen al seleccionar
            document.getElementById('imagen').addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        document.getElementById('imagePreview').innerHTML = `
                            <img src="${e.target.result}" class="img-thumbnail" width="150" alt="Vista previa">
                        `;
                    }
                    reader.readAsDataURL(file);
                }
            });
        </script>
        <!-- Bootstrap 5 JS Bundle with Popper -->

    @endpush

@endsection
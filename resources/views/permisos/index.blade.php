@extends('layouts.argon')

@section('content')
    @include('permisos.create')
    @include('permisos.edit')




    <div class="card shadow-lg mx-4 card-profile-bottom">
        <div class="card-body p-3">
            <p>Permisos</p>
            <div class="row mt-3">
                <div class="col">


                    <a href="" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearPermiso">Crear
                        Nuevo Permiso</a>

                </div>
                <div class="col-md-6">
                    <form method="GET" action="{{ route('permissions.index') }}" class="mb-3 d-flex justify-content-end">
                        <input type="text" name="search" class="form-control  me-2" placeholder="Buscar permiso..."
                            value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i>
                            Buscar</button>
                    </form>
                </div>
            </div>

        </div>

    </div>


    <div class="card shadow-lg mx-4 card-profile-bottom">
        <div class="card-body p-3">
            <p>Permisos Disponibles</p>
            <div class="row mt-3">
                @foreach ($cat_permisos as $modulo)
                    <div class="col-md-2">
                        <form method="GET" action="{{ route('permissions.index') }}">
                            <input type="hidden" name="search" value="{{ $modulo }}.">
                            <button type="submit" class="btn btn-sm bg-gradient-info w-100">
                                {{ ucfirst($modulo) }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row mt-3">
        @forelse($permissions as $permiso)
            <div class="col-md-4">
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-key text-primary"></i> {{ $permiso->name }}
                        </h5>

                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-info" onclick="editarPermiso({{ $permiso->id }})">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <form action="{{ route('permissions.destroy', $permiso->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('¿Seguro que deseas eliminar este permiso?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning text-center">No hay permisos que coincidan.</div>
            </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">

            <li class="page-item {{ $permissions->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $permissions->previousPageUrl() }}" aria-label="Previous">
                    <i class="fa fa-angle-left"></i>
                    <span class="sr-only">Anterior</span>
                </a>
            </li>


            @foreach ($permissions->getUrlRange(1, $permissions->lastPage()) as $page => $url)
                <li class="page-item {{ $page == $permissions->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
            @endforeach


            <li class="page-item {{ $permissions->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link" href="{{ $permissions->nextPageUrl() }}" aria-label="Next">
                    <i class="fa fa-angle-right"></i>
                    <span class="sr-only">Siguiente</span>
                </a>
            </li>
        </ul>
    </nav>







@endsection
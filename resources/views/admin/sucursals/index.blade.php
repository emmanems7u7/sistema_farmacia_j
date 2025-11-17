@extends('layouts.app', ['title' => 'Sucursales'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Sucursales'])

    <div class="container-fluid py-4">
       
        <div class="row">
            <!-- Card de Encabezado -->
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center bg-white">
                        <div class="d-flex align-items-center">

                            <h5 class="mb-0">
                                <i class="ni ni-shop me-3 text-primary"></i>
                                <strong>GESTION SUCURSALES</strong>
                            </h5>
                        </div>

                        <div class="d-flex align-items-center">
                            <span class="badge bg-gradient-info me-3">
                                {{ count($sucursals) }} sucursales
                            </span>

                            <div class="dropdown me-2">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                    id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                    title="Exportar reporte en diferentes formatos">
                                    <i class="fas fa-download me-1"></i> Exportar
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.sucursals.reporte') }}?tipo=pdf"
                                            title="Exportar a PDF" target="_blank">
                                            <i class="fas fa-file-pdf text-danger me-2"></i> PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.sucursals.reporte') }}?tipo=excel"
                                            title="Exportar a Excel">
                                            <i class="fas fa-file-excel text-success me-2"></i> Excel
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.sucursals.reporte') }}?tipo=csv"
                                            title="Exportar a CSV">
                                            <i class="fas fa-file-csv text-info me-2"></i> CSV
                                        </a>
                                    </li>

                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                </ul>
                            </div>

                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalCrear">
                                <i class="fas fa-plus-circle me-1"></i> Nuevo
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
                                    <thead class="bg-light">
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
                                   
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    

@endsection
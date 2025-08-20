@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Configuraciones</h1>
    <hr>
@stop

@section('content')
<div class="row">
<!-- Tabla de sucursales registradas-->
<div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Sucursales registrados</h3>
                            

                <div class="card-tools">
                    
                </div>
            </div>
            <div class="card-body">


            <table class="table table-striped table-bordered table-hover table-sm">
    <thead class="thead-dark">
        <tr>
            <th scope="col" style="text-align: center">Nro</th>
            <th scope="col" style="text-align: center">Imagen</th>
            <th scope="col" style="text-align: center">Nombre</th>
            <th scope="col" style="text-align: center">Correo</th>
            <th scope="col" style="text-align: center">Dirección</th>
            <th scope="col" style="text-align: center">Teléfono</th>
            <th scope="col" style="text-align: center">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @php $contador = 1; @endphp
        @foreach($sucursals as $sucursal)
            <tr>
                <td style="text-align: center">{{ $contador++ }}</td>
                <td style="text-align: center">
                    @if($sucursal->imagen)
                        <img src="{{ asset('storage/' . $sucursal->imagen) }}" width="60" alt="Imagen">
                    @else
                        Sin imagen
                    @endif
                </td>
                <td style="text-align: center">{{ $sucursal->nombre }}</td>
                <td style="text-align: center">{{ $sucursal->email }}</td>
                <td style="text-align: center">{{ $sucursal->direccion }}</td>
                <td style="text-align: center">{{ $sucursal->telefono }}</td>
                <td style="text-align: center">
                    <!-- Botón Editar -->
                    <a href="{{ url('/admin/configuraciones/'.$sucursal->id.'/edit') }}" class="btn btn-outline-success">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <!-- Botón Eliminar -->
                    <form action="{{ url('/admin/configuraciones/'.$sucursal->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar esta sucursal?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>







@stop

@section('css')
    
@stop

@section('js')
   
@stop

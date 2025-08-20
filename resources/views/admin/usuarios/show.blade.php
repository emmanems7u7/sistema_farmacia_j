@extends('adminlte::page')
@section('content_header')
    <h1><b>Usuario Registrado</b></h1>
@endsection
@section('content')
<div class="row">
    <!-- Formulario para crear un usuario -->
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Ingrese los datos</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form action="{{url('/admin/usuarios/create')}}" method="post">
                    @csrf
                    <div class="row">
                        <!-- Campo Rol -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="role">Nombre del Rol</label>
                                <select name="role" id="" class="form-control" required>
                                    <option value="">Seleccionar un Rol</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- Campo Nombre del Usuario -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name">Nombre del usuario</label>
                                <input type="text" class="form-control" value="{{ old('name') }}" name="name" required autocomplete="off">
                                
                                @error('name')
                                <small style="color: red;">{{$message}}</small>
                                @enderror
                            </div>
                        </div>                   
                        <!-- Campo Correo -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="email">Correo</label>
                                <input type="email" class="form-control" value="{{ old('email') }}" name="email" required autocomplete="off">
                                
                                @error('email')
                                <small style="color: red;">{{$message}}</small>
                                @enderror
                            </div>
                        </div>
                        <!-- Campo CONTRASEÑA -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="password">Contraseña</label>
                                <input type="password" class="form-control" name="password" required autocomplete="new-password">
                                
                                @error('password')
                                    
                                    <small style="color: red;">{{$message}}</small>
                                @enderror
                            </div>
                        </div>
                         <!-- Campo CONTRASEÑA CONFIRMACION -->
                         <div class="col-md-3">
                            <div class="form-group">
                                <label for="password_confirmation">Confirmacion Contraseña</label>
                                <input type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                                            
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <a href="{{url('/admin/usuarios')}}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary" style="margin-left: 20px;">
                                <i class="fas fa-save"></i> Registrar
                            </button>
                            </div>
                        </div>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    
@stop

@section('js')

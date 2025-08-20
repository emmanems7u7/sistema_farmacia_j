@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Configuraciones</h1>
    <hr>
@stop

@section('content')
<div class="row">






        <div class="col-md-12">

        {{-- Card Box --}}
        <!--cambiar la forma del login estilos -->
        <div class="card card-outline card-success"
        
        style="box-shadow: 5px 5px 5px 5px #cccccc">
                <div class="card-header {{ config('adminlte.classes_auth_header', '') }}">
                    <h3 class="card-title float-none text-center">
                        <b>Datos registrados edit</b>
                    </h3>
                </div>
            {{-- Card Body --}}
            <div class="card-body {{ $auth_type ?? 'login' }}-card-body {{ config('adminlte.classes_auth_body', '') }}">
            <form action="{{url('/admin/configuraciones',$sucursal->id)}}" method="POST" enctype="multipart/form-data">
            @csrf
             @method('PUT')       
                    <div class="row">
                    <div class="col-md-3">                       
                            <div class="form-group">
                            <label for="imagen"><b>Imagen</b></label>
                                <input type="file" id="file" name="imagen" accept=".jpg, .jpeg, .png" class="form-control" >
                                <br>
                                <center><output id="list">
                                    
                                    <img src="{{ asset('storage/'.$sucursal->imagen) }}" width="80%" alt="imagen">
                                </output></center>
                                <script>
                                    function archivo(evt) {
                                        var files = evt.target.files;

                                        // Iterar sobre los archivos seleccionados
                                        for (var i = 0, f; f = files[i]; i++) {
                                            // Verificar si es una imagen
                                            if (!f.type.match('image.*')) {
                                                continue;
                                            }

                                            var reader = new FileReader();
                                            reader.onload = (function (theFile) {
                                                return function (e) {
                                                    // Insertar la imagen en el contenedor
                                                    document.getElementById("list").innerHTML = [
                                                        '<img class="thumb thumbnail" src="',
                                                        e.target.result,
                                                        '" width="70%" title="',
                                                        escape(theFile.name),
                                                        '"/>'
                                                    ].join('');
                                                };
                                            })(f);

                                            // Leer el archivo como una URL de datos
                                            reader.readAsDataURL(f);
                                        }
                                    }

                                    // Agregar evento al input de archivo
                                    document.getElementById('file').addEventListener('change', archivo, false);
                                </script>

                            </div>
                        </div>
                        <div class="col-md-3">                                               
                            <div class="form-group">
                                <label for="nombre"><b>Nombre</b></label>
                                <input type="text" value="{{$sucursal->nombre}}" name="nombre" class="form-control" required> 
                                @error('nombre')
                                <small style="color: red;">{{$message}}</small>
                                @enderror                              
                            </div>
                        </div>

                        <div class="col-md-3">                                               
                            <div class="form-group">
                                <label for="email"><b>Correo</b></label>
                                <input type="email" value="{{$sucursal->email}}" name="email" class="form-control" required> 
                                @error('email')
                                <small style="color: red;">{{$message}}</small>
                                @enderror                              
                            </div>
                        </div>

                        <div class="col-md-3">                                               
                            <div class="form-group">
                                <label for="nombre"><b>Direccion</b></label>
                                <input type="text" value="{{$sucursal->direccion}}" name="direccion" class="form-control" required>                               
                            </div>
                        </div>

                        <div class="col-md-3">                                               
                            <div class="form-group">
                                <label for="nombre"><b>Telefono</b></label>
                                <input type="text" value="{{$sucursal->telefono}}" name="telefono" class="form-control" required>                               
                            </div>
                        </div>

                         <hr>
                        <div class="row">
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-success">Actulizar</button>

                            </div>

                        </div>
                </form>

            </div>
            </div>

</div>

</div>
</div>
            {{-- Card Footer --}}
            @hasSection('auth_footer')
                <div class="card-footer {{ config('adminlte.classes_auth_footer', '') }}">
                    @yield('auth_footer')
                </div>
            @endif


@stop

@section('css')
    
@stop

@section('js')
   
@stop
@extends('adminlte::page')

@section('title', 'Editar Rol')

@section('content_header')
    <h1><b>Editar Rol</b></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title" id="exampleModalLabel">Editar Categoria</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{url('/admin/categorias', $categoria->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <!-- Campo Nombre del Usuario -->
                                                                                    
                                        <div class="form-group">
                                        <label for="nombre">Nombre de la categoria</label>
                                        <input type="text" class="form-control" value="{{$categoria->nombre}}" name="nombre"  >
                                                            
                                       @error('name')
                                        <small style="color: red;">{{$message}}</small>
                                          @enderror
                                       </div>
                                                <!-- Campo Nombre del Usuario -->
                                                                                    
                                        <div class="form-group">
                                        <label for="descripcion">Descripcion</label>
                                        <input type="text" class="form-control" value="{{$categoria->descripcion}}" name="descripcion" >
                                                            
                                       @error('name')
                                        <small style="color: red;">{{$message}}</small>
                                          @enderror
                                       </div>
                                                  
                                        
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
@stop















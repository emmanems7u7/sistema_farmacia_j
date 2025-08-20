@extends('adminlte::page')

@section('content_header')
    <h1><b>Productos registrados</b></h1>
@endsection

@section('content')
<div class="row">
    <section class="content">
        <!-- Default box -->
        <div class="card card-solid">
            <div class="col-md-12">
                <div class="card-body pb-sm">

                    <!-- MODAL PARA EDITAR PRODUCTO -->
                    <div class="modal fade" id="editarModal{{$compra->id}}" tabindex="-1" aria-labelledby="editarModalLabel{{$compra->id}}" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <!-- Header del modal -->
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title" id="editarModalLabel{{$compra->id}}"><b>Editar Producto</b></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <!-- Formulario -->
                                <form action="{{ url('/admin/compras/' . $compra->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-body">
                                        <div class="row">
                                            <!-- Selección de Categoría -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="categoria_id">Categoría</label>
                                                    <select name="categoria_id" class="form-control">
                                                        <option value="">Seleccionar una categoría</option>
                                                        @foreach($categorias as $categoria)
                                                            <option value="{{$categoria->id}}" {{$categoria->id == $compra->categoria_id ? 'selected' : ''}}>
                                                                {{$categoria->nombre}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Nombre del Producto -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="nombre">Nombre del compra</label>
                                                    <input type="text" class="form-control" name="nombre" value="{{$compra->nombre}}" required>
                                                    @error('nombre')
                                                        <small style="color: red;">{{$message}}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer del modal -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-primary">Actualizar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- FIN DEL MODAL -->





                                                <form action="{{ route('admin.compras.destroy', $compra->id) }}" 
      method="POST" 
      class="d-inline"
      data-compra='{"nombre":"{{ $compra->nombre }}"}'>
        @csrf
        @method('DELETE')
        <button type="button" 
                class="btn btn-sm bg-gradient-danger text-white rounded-end shadow-sm px-4 btn-eliminar-compra"
                title="Eliminar compra"
                data-bs-toggle="tooltip">
            <span class="btn-inner--icon me-1">
                <i class="fas fa-trash-alt"></i>
            </span>
            <span class="btn-inner--text"></span>
        </button>
    </form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmarEliminacionSucursal(event) {
    event.preventDefault();
    const form = event.target.closest('form');
    const compra = JSON.parse(form.dataset.compra || '{}');
    
    Swal.fire({
        title: `<span class="swal2-title">Confirmar Eliminación</span>`,
        html: `<div class="swal2-content-container">
                 
                 <div class="swal2-text-content">
                     <h3 class="swal2-subtitle">¿Eliminar compra permanentemente?</h3>
                     <div class="swal2-user-info mt-3">
                         <i></i> ${compra.nombre || 'Esta compra'}
                     </div>
                     <div class="swal2-warning-text">
                         <i class="fas fa-exclamation-triangle me-2"></i>
                         Esta acción no se puede deshacer
                     </div>
                 </div>
               </div>`,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: `<i class="fas fa-trash-alt me-2"></i> Confirmar Eliminación`,
        cancelButtonText: `<i class="fas fa-times me-2"></i> Cancelar`,
        buttonsStyling: false,
        customClass: {
            popup: 'swal2-container-premium',
            confirmButton: 'swal2-confirm-btn-premium',
            cancelButton: 'swal2-cancel-btn-premium',
            actions: 'swal2-actions-premium'
        },
        background: 'rgba(255,255,255,0.98)',
        showClass: {
            popup: 'animate__animated animate__zoomIn animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__zoomOut animate__faster'
        },
        allowOutsideClick: false,
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Procesando...',
                html: `<div class="swal2-loader-container">
                         <div class="swal2-loader-circle"></div>
                         <div class="swal2-loader-bar-container">
                             <div class="swal2-loader-bar"></div>
                         </div>
                       </div>`,
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    const loaderBar = document.querySelector('.swal2-loader-bar');
                    loaderBar.style.width = '100%';
                    loaderBar.style.transition = 'width 1s ease-in-out';
                }
            });
            
            setTimeout(() => {
                form.submit();
            }, 1200);
        }
    });
}

document.querySelectorAll('.btn-eliminar-compra').forEach(button => {
    button.addEventListener('click', confirmarEliminacionSucursal);
});
</script>

<style>
/* Estilos Premium */
.swal2-container-premium {
    border-radius: 18px !important;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.18) !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    max-width: 480px !important;
    padding: 2.5rem !important;
}

.swal2-icon-wrapper {
    text-align: center;
    margin: 1.5rem 0;
}

.swal2-icon-svg {
    width: 72px;
    height: 72px;
    opacity: 0.9;
}

.swal2-content-container {
    text-align: center;
    padding: 0 1.5rem;
}

.swal2-title {
    font-size: 1.8rem !important;
    font-weight: 600 !important;
    color: #2f3542 !important;
    letter-spacing: -0.5px;
    margin-bottom: 0 !important;
}

.swal2-subtitle {
    font-size: 1.25rem;
    color: #57606f;
    font-weight: 500;
    margin: 1rem 0;
}

.swal2-user-info {
    background: #f8f9fa;
    padding: 0.75rem;
    border-radius: 10px;
    font-size: 1.1rem;
    color: #2f3542;
    border-left: 4px solid #ff4757;
}

.swal2-warning-text {
    font-size: 0.95rem;
    color: #ff6b81;
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px dashed #dfe4ea;
}

.swal2-confirm-btn-premium {
    background: linear-gradient(135deg, #ff4757, #ff6b81) !important;
    border: none !important;
    padding: 12px 28px !important;
    font-weight: 600 !important;
    font-size: 1rem !important;
    border-radius: 10px !important;
    color: white !important;
    box-shadow: 0 4px 12px rgba(255, 71, 87, 0.25) !important;
    transition: all 0.3s ease !important;
}

.swal2-confirm-btn-premium:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 16px rgba(255, 71, 87, 0.3) !important;
}

.swal2-cancel-btn-premium {
    background: white !important;
    border: 1px solid #dfe4ea !important;
    padding: 12px 28px !important;
    font-weight: 500 !important;
    font-size: 1rem !important;
    border-radius: 10px !important;
    color: #57606f !important;
    transition: all 0.3s ease !important;
}

.swal2-cancel-btn-premium:hover {
    background: #f8f9fa !important;
    border-color: #ced6e0 !important;
}

.swal2-actions-premium {
    margin: 2rem 0 0 0 !important;
    gap: 1rem !important;
}

/* Loader premium */
.swal2-loader-container {
    width: 100%;
    padding: 1.5rem 0;
}

.swal2-loader-circle {
    width: 60px;
    height: 60px;
    border: 4px solid rgba(255, 71, 87, 0.2);
    border-top-color: #ff4757;
    border-radius: 50%;
    margin: 0 auto 1.5rem;
    animation: swal2-spin 1s linear infinite;
}

.swal2-loader-bar-container {
    width: 100%;
    height: 6px;
    background: rgba(255, 71, 87, 0.1);
    border-radius: 3px;
    overflow: hidden;
}

.swal2-loader-bar {
    height: 100%;
    width: 0;
    background: linear-gradient(90deg, #ff4757, #ff6b81);
    border-radius: 3px;
}

@keyframes swal2-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>









                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('css')
@endsection

@section('js')
@endsection

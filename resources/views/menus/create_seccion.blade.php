<div class="modal fade" id="crearSeccionModal" tabindex="-1" aria-labelledby="crearSeccionModalLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crearSeccionModalLabel">Crear Sección</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('secciones.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="titulo" class="form-label @error('titulo') is-invalid @enderror">Título</label>
                        <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}" class="form-control"
                            required>
                        @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="icono" class="form-label @error('icono') is-invalid @enderror">Icono</label>
                        <input type="text" name="icono" id="icono" value="{{ old('icono') }}" class="form-control"
                            required>
                        @error('icono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>



            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar sección</button>
            </div>
            </form>
        </div>
    </div>
</div>
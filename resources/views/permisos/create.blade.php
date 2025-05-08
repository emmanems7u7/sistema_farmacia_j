<div class="modal fade" id="modalCrearPermiso" tabindex="-1" aria-labelledby="modalCrearPermisoLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCrearPermisoLabel">Crear Permiso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('permissions.store') }}" method="POST">
                    @csrf

                    <div class="modal-body">
                        <label>Nombre</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success">Crear Rol</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
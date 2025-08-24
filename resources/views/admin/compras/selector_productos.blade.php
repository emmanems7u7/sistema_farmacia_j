<script>

    // Seleccionar producto
    $(document).on('click', '.seleccionar-btn', function () {
        var idProducto = $(this).data('id');
        $('#codigo').val(idProducto);

        // Cerrar modal producto
        var modal = bootstrap.Modal.getInstance(document.getElementById('verModal'));
        if (modal) modal.hide();

        // Enfocar input
        setTimeout(() => { $('#codigo').focus(); }, 300);

        // Eliminar manualmente el backdrop si persiste
        const productosModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('productosModal'));
        productosModal.hide();


        $('#codigo').focus();
    });

</script>
<!-- Modal de Productos  -->
<div class="modal fade" id="productosModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-semibold">
                    <i class="fas fa-boxes me-2"></i>Listado de Productos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table id="mitabla" class="table table-sm table-hover mb-0" style="width:100%">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-uppercase small fw-bold" style="width: 5%;">#</th>
                                <th class="text-uppercase small fw-bold" style="width: 10%;">Acciones</th>
                                <th class="text-uppercase small fw-bold" style="width: 15%;">Código</th>
                                <th class="text-uppercase small fw-bold" style="width: 50%;">Nombre</th>
                                <th class="text-uppercase small fw-bold text-center" style="width: 20%;">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productos as $producto)
                                @php
                                    // Definir clase según el nivel de stock
                                    $stockClass = '';
                                    $stockText = $producto->stock;

                                    if ($producto->stock <= 0) {
                                        $stockClass = 'bg-danger bg-opacity-10 text-black';
                                        $stockText = 'AGOTADO';
                                    } elseif ($producto->stock < 10) {
                                        $stockClass = 'bg-warning bg-opacity-10 text-black';
                                        $stockText = ' (' . $producto->stock . ')';
                                    } else {
                                        $stockClass = 'bg-success bg-opacity-10 text-black';
                                    }
                                @endphp
                                <tr>
                                    <td class="small">{{ $loop->iteration }}</td>
                                    <td class="text-center">
                                        <button type="button"
                                            class="btn btn-sm btn-primary rounded-circle seleccionar-btn p-1"
                                            data-id="{{ $producto->codigo }}" title="Seleccionar">
                                            <i class="fas fa-check" style="font-size: 0.7rem;"></i>
                                        </button>
                                    </td>
                                    <td class="small">
                                        <span class="badge bg-light text-dark border">{{ $producto->codigo }}</span>
                                    </td>
                                    <td class="small fw-semibold">{{ $producto->nombre }}</td>
                                    <td class="text-center small fw-bold">
                                        <span class="badge {{ $stockClass }} rounded-pill px-2 py-1">
                                            {{ $stockText }}
                                        </span>
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
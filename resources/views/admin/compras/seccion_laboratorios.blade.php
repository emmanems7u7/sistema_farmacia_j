<div class="col-lg-4">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-bottom">
            <h6 class="mb-0 text-dark font-weight-bold">
                <i class="fas fa-info-circle text-primary me-2"></i>Información de Compra
            </h6>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal"
                    data-bs-target="#laboratoriosModal">
                    <i class="fas fa-search me-1"></i> Buscar Laboratorio
                </button>


            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-sm">Laboratorio</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-hospital text-primary"></i>
                    </span>
                    <input type="text" class="form-control border" id="nombre_laboratorio"
                        placeholder="Seleccione un laboratorio" disabled>
                    <input type="hidden" id="laboratorio_id" name="laboratorio_id">
                </div>
            </div>

            <hr class="horizontal dark my-4">

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="fecha" class="form-label fw-bold text-sm">Fecha</label>
                    <input type="date" class="form-control form-control-sm border" name="fecha"
                        value="{{ old('fecha', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                    @error('fecha')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>


                <div class="col-md-6">
                    <label for="comprobante" class="form-label fw-bold text-sm">Comprobante</label>
                    <select name="comprobante" id="comprobante" class="form-select form-select-sm border" required>
                        <option value="FACTURA" selected>FACTURA</option>
                        <option value="RECIBO">RECIBO</option>
                        <option value="NOTA">NOTA</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label for="precio_total" class="form-label fw-bold text-sm">Total Compra</label>
                <input type="text" class="form-control form-control-sm bg-light text-dark text-center fw-bold border"
                    name="precio_total" value="{{ number_format($total_compra, 2, '.', '') }}" readonly>
            </div>


            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary shadow-sm py-2" {{ $total_compra <= 0 ? 'disabled' : '' }}>
                    <i class="fas fa-save me-2"></i> Registrar Compra
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Seleccionar laboratorio
    $(document).on('click', '.seleccionar-btn-laboratorio', function () {
        var idLab = $(this).data('id');
        var nombreLab = $(this).data('nombre');
        console.log(idLab)
        $('#laboratorio_id').val(idLab);
        $('#nombre_laboratorio').val(nombreLab);

        // Cerrar modal laboratorio

        const laboratorioModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('laboratoriosModal'));
        laboratorioModal.hide();

    });

</script>

<!-- Modal de Laboratorios  -->


<div class="modal fade" id="laboratoriosModal" tabindex="-1" role="dialog" aria-labelledby="laboratoriosModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="laboratoriosModalLabel"><i class="fas fa-hospital me-2"></i>Listado de
                    Laboratorios</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="mitabla2" class="table table-sm table-hover mb-0" style="width:100%">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-uppercase small fw-bold" style="width: 5%;">#</th>
                                <th class="text-uppercase small fw-bold" style="width: 10%;">Acciones</th>
                                <th class="text-uppercase small fw-bold" style="width: 35%;">Laboratorio</th>


                            </tr>
                        </thead>
                        <tbody>
                            @foreach($laboratorios as $laboratorio)
                                <tr>
                                    <td class="small">{{ $loop->iteration }}</td>
                                    <td class="text-center">
                                        <button type="button"
                                            class="btn btn-sm btn-primary rounded-circle seleccionar-btn-laboratorio p-1"
                                            data-id="{{ $laboratorio->id }}" data-nombre="{{ $laboratorio->nombre }}"
                                            title="Seleccionar">
                                            <i class="fas fa-check" style="font-size: 0.7rem;"></i>
                                        </button>
                                    </td>
                                    <td class="small fw-semibold">{{ $laboratorio->nombre }}</td>


                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <i class="fas fa-times me-1"></i> Cerrar

            </div>
        </div>
    </div>
</div>
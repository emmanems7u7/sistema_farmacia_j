@extends('layouts.argon')

@section('content')
<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-12">
            <div class="card shadow-sm">
                <!-- Card Header -->
                <div class="card-header bg-gradient-primary border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 text-white">
                            <i class="ni ni-fat-add mr-2"></i>Nuevo Producto
                        </h4>
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-light">
                            <i class="fas fa-arrow-left mr-1"></i> Volver
                        </a>
                    </div>
                </div>
                
                <!-- Card Body -->
                <div class="card-body px-3 py-3">
                    <form action="{{ url('/admin/productos/create') }}" method="post" enctype="multipart/form-data" id="productForm">
                        @csrf
                        
                        <div class="row">
                            <!-- Main Form Section -->
                            <div class="col-lg-8">
                                <!-- Basic Info -->
                                <div class="form-row d-flex align-items-end"> <!-- align-items-end para alinear los labels -->
                                <div class="flex-grow-1 mr-2 mb-4"> 
                                        <label class="form-control-label">Código <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="codigo" value="{{ old('codigo') }}" required>
                                        @error('codigo')
                                            <div class="invalid-feedback d-block">{{$message}}</div>
                                        @enderror
                                    </div>
                                    <div class="col-auto px-2 mb-3 d-flex align-items-center">
        <span class="text-muted"></span>
    </div>
    
                                    
                                    <div class="flex-grow-1 mb-4"> 
                                        <label class="form-control-label">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" required>
                                        @error('nombre')
                                            <div class="invalid-feedback d-block">{{$message}}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-row d-flex align-items-end"> <!-- align-items-end para alinear los labels -->
    <div class="flex-grow-1 mr-2 mb-4"> <!-- flex-grow-1 para que ocupen el espacio disponible -->
        <label class="form-control-label font-weight-bold">Categoría <span class="text-danger">*</span></label>
        <select name="categoria_id" class="form-control form-control-lg" required>
            <option value="">Seleccionar categoría</option>
            @foreach($categorias as $categoria)
                <option value="{{$categoria->id}}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                    {{$categoria->nombre}}
                </option>
            @endforeach
        </select>
        @error('categoria_id')
            <div class="invalid-feedback d-block">{{$message}}</div>
        @enderror
    </div>
    <div class="col-auto px-2 mb-3 d-flex align-items-center">
        <span class="text-muted"></span>
    </div>
    
    <div class="flex-grow-1 mb-4"> <!-- flex-grow-1 para que ocupen el mismo ancho -->
        <label class="form-control-label font-weight-bold">Laboratorio <span class="text-danger">*</span></label>
        <select name="laboratorio_id" class="form-control form-control-lg" required>
            <option value="">Seleccionar laboratorio</option>
            @foreach($laboratorios as $laboratorio)
                <option value="{{ $laboratorio->id }}" {{ old('laboratorio_id') == $laboratorio->id ? 'selected' : '' }}>
                    {{ $laboratorio->nombre }}
                </option>
            @endforeach
        </select>
        @error('laboratorio_id')
            <div class="invalid-feedback d-block">{{$message}}</div>
        @enderror
    </div>
</div>
                                
                                <!-- Dates Row - Diseño Rectangular Compacto -->
                                <div class="form-row d-flex align-items-end"> <!-- align-items-end para alinear los labels -->
                               
                                
                                <div class="col-auto px-2 mb-3 d-flex align-items-center">
                                    <span class="text-muted"></span>
                                </div>
                                
                                
                            
                            </div>
                                
                                <!-- Description -->
                                <div class="mb-3">
                                    <label class="form-control-label">Descripción</label>
                                    <textarea class="form-control" name="descripcion" rows="2">{{ old('descripcion') }}</textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback d-block">{{$message}}</div>
                                    @enderror
                                </div>
                                
                                <!-- Inventory and Pricing Cards Side by Side -->
                                <div class="row">
                                    <div class="col-md-6 pr-md-2">
                                        <div class="card border-0 shadow-sm mb-3 h-100">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="mb-0">Control de Inventario</h6>
                                            </div>
                                            <div class="card-body p-2">
                                                
                                                
                                                <div class="form-group mb-2">
                                                    <label class="form-control-label">Stock Mínimo</label>
                                                    <input type="number" class="form-control" name="stock_minimo" value="{{ old('stock_minimo', 0) }}" required>
                                                </div>
                                                
                                                <div class="form-group mb-2">
                                                    <label class="form-control-label">Stock Máximo</label>
                                                    <input type="number" class="form-control" name="stock_maximo" value="{{ old('stock_maximo', 0) }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                            </div>
                            
                            <!-- Image Section -->
                            <div class="col-lg-4">
    <div class="card h-100 border-0 shadow-hover">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0 font-weight-bold text-primary">
                <i class="fas fa-camera-retro mr-2"></i> Imagen del Producto
            </h6>
        </div>
        <div class="card-body p-4">
            <!-- Upload Area -->
            <div class="upload-area border-2 border-dashed rounded-lg p-4 mb-4 text-center">
                <div class="file-upload-wrapper">
                    <input type="file" id="file" name="imagen" accept=".jpg, .jpeg, .png" class="file-upload-input" 
                           onchange="previewImage(this)">
                    <label for="file" class="file-upload-label btn btn-sm btn-primary px-4">
                        <i class="fas fa-cloud-upload-alt mr-2"></i> Seleccionar imagen
                    </label>
                    <p class="small text-muted mt-2 mb-0">Formatos: JPG, PNG (Máx. 2MB)</p>
                </div>
            </div>
            
            <!-- Preview Area -->
            <div class="preview-container border rounded-lg overflow-hidden bg-white">
                <div id="no-image" class="empty-state text-center p-4">
                    <div class="icon-container bg-light-primary rounded-circle p-3 mb-3 d-inline-block">
                        <i class="fas fa-image text-primary fa-2x"></i>
                    </div>
                    <h6 class="text-muted mb-1">Vista previa</h6>
                    <p class="small text-muted mb-0">La imagen seleccionada aparecerá aquí</p>
                </div>
                <img id="preview" class="img-fluid w-100" style="display: none; max-height: 220px; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<style>
    .shadow-hover {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        transition: all 0.3s ease;
    }
    
    .shadow-hover:hover {
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.15);
        transform: translateY(-2px);
    }
    
    .border-2 {
        border-width: 2px !important;
    }
    
    .border-dashed {
        border-style: dashed !important;
    }
    
    .upload-area {
        border-color: #d1d3e2;
        background-color: #f8f9fe;
        transition: all 0.3s;
    }
    
    .upload-area:hover {
        border-color: #b7b9cc;
        background-color: #f0f2f7;
    }
    
    .file-upload-input {
        width: 0.1px;
        height: 0.1px;
        opacity: 0;
        overflow: hidden;
        position: absolute;
        z-index: -1;
    }
    
    .file-upload-label {
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .preview-container {
        min-height: 200px;
        position: relative;
    }
    
    .empty-state {
        transition: all 0.3s;
    }
    
    .bg-light-primary {
        background-color: rgba(78, 115, 223, 0.1);
    }
</style>

<script>
function previewImage(input) {
    const preview = document.getElementById('preview');
    const noImage = document.getElementById('no-image');
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            noImage.style.display = 'none';
        }
        
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
        noImage.style.display = 'block';
    }
}
</script>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12 text-right">
                                <button type="reset" class="btn btn-outline-secondary mr-2">
                                    <i class="fas fa-undo mr-1"></i> Limpiar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // Image Preview Script
    document.getElementById('file').addEventListener('change', function(e) {
        const preview = document.getElementById('preview');
        const noImage = document.getElementById('no-image');
        const files = e.target.files;

        if (files.length > 0) {
            const file = files[0];
            
            if (file.type.match('image.*')) {
                const reader = new FileReader();
                
                reader.onload = function(event) {
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                    noImage.style.display = 'none';
                }
                
                reader.readAsDataURL(file);
            }
        } else {
            preview.style.display = 'none';
            noImage.style.display = 'flex';
        }
    });

    // Update filename display
    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
        const fileName = e.target.files.length > 0 ? e.target.files[0].name : 'Seleccionar imagen';
        const label = this.nextElementSibling;
        label.textContent = fileName;
    });

    // Format currency inputs
    document.querySelectorAll('input[name="precio_compra"], input[name="precio_venta"]').forEach(input => {
        input.addEventListener('blur', function() {
            const value = parseFloat(this.value.replace(/,/g, ''));
            if (!isNaN(value)) {
                this.value = value.toFixed(2);
            }
        });
    });
</script>
@endpush

@push('css')
<style>
    .card-header.bg-gradient-primary {
        background: linear-gradient(87deg, #5e72e4 0, #825ee4 100%) !important;
    }
    
    .image-preview-container {
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .custom-file-label::after {
        content: "Examinar";
    }
    
    .invalid-feedback.d-block {
        display: block !important;
        font-size: 0.85rem;
    }
    
    .form-control-label {
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .card-header h6 {
        font-size: 0.95rem;
        font-weight: 600;
    }
    
    /* Ajustes para las tarjetas lado a lado */
    @media (min-width: 768px) {
        .pr-md-2 {
            padding-right: 0.5rem !important;
        }
        .pl-md-2 {
            padding-left: 0.5rem !important;
        }
    }
</style>
@endpush
@extends('layouts.app', ['title' => 'Gestion de cajas'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Cajas'])
<div class="container-fluid mt--6">
<div class="row">
        <div class="col">
            <!-- Card Superior - Versión Blanca con Texto Negro -->
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-header border-0 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-shape bg-primary text-dark rounded-circle shadow-sm me-3">
                            
                        </div>
                        <h3 class="mb-0 text-dark"><b>Registro de Nueva Caja</b></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0 text-dark">
                            <i class="fas fa-edit text-primary me-2"></i>Ingrese los datos de la caja
                        </h3>
                        <button type="button" class="btn btn-sm btn-link text-dark" data-card-widget="collapse">
                            <i class="ni ni-minimal-down fs-4"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <form action="{{url('/admin/cajas/create')}}" method="post">
                        @csrf
                        <div class="row g-4">
                            <!-- Campo Fecha de apertura -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha_apertura" class="form-label fw-bold text-dark mb-2">
                                        <i class="fas fa-calendar-alt text-primary me-2"></i>Fecha de apertura
                                    </label>
                                    <div class="input-group input-group-outline">
                                        <input type="datetime-local" class="form-control border-bottom px-0" 
                                            value="{{ old('fecha_apertura', now()->format('Y-m-d\TH:i')) }}" 
                                            name="fecha_apertura" 
                                            min="{{ now()->format('Y-m-d\TH:i') }}"
                                            required>
                                    </div>
                                    @error('fecha_apertura')
                                        <small class="text-danger mt-2 d-block">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                                                        
                            <!-- Campo Monto inicial -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="monto_inicial" class="form-label fw-bold text-dark mb-2">
                                        <i class="fas fa-money-bill-wave text-primary me-2"></i>Monto inicial
                                    </label>
                                    <div class="input-group input-group-outline">
                                        <span class="input-group-text bg-transparent">Bs</span>
                                        <input type="number" step="0.01" class="form-control border-bottom px-0" 
                                               value="{{ old('monto_inicial') }}" 
                                               name="monto_inicial" required>
                                    </div>
                                    @error('monto_inicial')
                                        <small class="text-danger mt-2 d-block">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Campo Descripción -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="descripcion" class="form-label fw-bold text-dark mb-2">
                                        <i class="fas fa-align-left text-primary me-2"></i>Descripción
                                    </label>
                                    <div class="input-group input-group-outline">
                                        <input type="text" class="form-control border-bottom px-0" 
                                               name="descripcion" required 
                                               autocomplete="new-descripcion">
                                    </div>
                                    @error('descripcion')
                                        <small class="text-danger mt-2 d-block">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-end align-items-center">
                                    <a href="{{url('/admin/cajas')}}" class="btn btn-outline-secondary me-3">
                                        <i class="fas fa-times-circle me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary shadow-sm">
                                        <i class="fas fa-save me-2"></i>Registrar Caja
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .card {
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .form-control {
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .form-control:focus {
        border-color: #5e72e4;
        box-shadow: 0 0 0 2px rgba(94, 114, 228, 0.2);
    }
    
    .input-group-outline .form-control {
        border: none;
        border-bottom: 1px solid #d2d6da;
        padding-left: 0;
    }
    
    .input-group-outline .form-control:focus {
        border-color: #5e72e4;
        box-shadow: none;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #5e72e4, #825ee4);
        border: none;
        padding: 10px 24px;
        font-weight: 600;
    }
</style>
@endsection

@section('js')
<script>
    // Puedes agregar scripts adicionales aquí si necesitas
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
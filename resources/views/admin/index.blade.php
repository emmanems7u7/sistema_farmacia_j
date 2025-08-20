@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1><b>Dashboar121d{{$sucursal->nombre}}</b></h1>
    <hr>
@stop

@section('content')
    
<div class="row">
    <!-- PRIMERA TARJETA (4 tarjetas pequeñas en vertical) -->
    <div class="col-md-3 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <!-- Tarjeta de Productos - Versión compacta -->
                    <div class="dashboard-card mb-3" style="width: 100%; height: 90px; background-color: #f0e5ff;">
                        <div class="card-content" style="padding: 10px; height: 60px; position: relative;">
                            <div class="card-value" style="font-size: 1.5rem; line-height: 1.2; margin-bottom: 2px;">{{$total_productos}}</div>
                            <div class="card-label" style="font-size: 0.8rem;">PRODUCTOS</div>
                            <div class="card-icon" onclick="animateCard(this)" style="font-size: 1.8rem; bottom: 10px; right: 10px;">
                                <i class="fas fa-pills"></i>
                            </div>
                        </div>
                        <a href="{{ url('/admin/productos/create') }}" class="card-action" style="padding: 5px 10px; font-size: 0.8rem;">
                            <span>Agregar</span>
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>

          <!-- Tarjeta de Compras - Versión compacta -->
                <div class="dashboard-card mb-3" style="width: 100%; height: 90px; background-color: #e6d6ff;">
                    <div class="card-content" style="padding: 10px; height: 60px; position: relative;">
                        <div class="card-value" style="font-size: 1.5rem; line-height: 1.2; margin-bottom: 2px;">{{$total_compras}}</div>
                        <div class="card-label" style="font-size: 0.8rem;">COMPRAS</div>
                        <div class="card-icon" onclick="animateCard(this)" style="font-size: 1.8rem; bottom: 10px; right: 10px;">
                            <i class="fas fa-shopping-basket"></i>
                        </div>
                    </div>
                    <a href="{{ url('/admin/compras/create') }}" class="card-action" style="padding: 5px 10px; font-size: 0.8rem;">
                        <span>Nueva</span>
                        <i class="fas fa-plus"></i>
                    </a>
                </div>

                <!-- Tarjeta de Ventas - Versión compacta -->
                <div class="dashboard-card mb-3" style="width: 100%; height: 90px; background-color: #d9c5f7;">
                    <div class="card-content" style="padding: 10px; height: 60px; position: relative;">
                        <div class="card-value" style="font-size: 1.5rem; line-height: 1.2; margin-bottom: 2px;">{{$total_ventas}}</div>
                        <div class="card-label" style="font-size: 0.8rem;">VENTAS</div>
                        <div class="card-icon" onclick="animateCard(this)" style="font-size: 1.8rem; bottom: 10px; right: 10px;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <a href="{{ url('/admin/ventas/create') }}" class="card-action" style="padding: 5px 10px; font-size: 0.8rem;">
                        <span>Registrar</span>
                        <i class="fas fa-plus"></i>
                    </a>
                </div>

                <!-- Tarjeta de Clientes - Versión compacta -->
                <div class="dashboard-card" style="width: 100%; height: 90px; background-color: #cdb4f0;">
                    <div class="card-content" style="padding: 10px; height: 60px; position: relative;">
                        <div class="card-value" style="font-size: 1.5rem; line-height: 1.2; margin-bottom: 2px;">{{$total_clientes}}</div>
                        <div class="card-label" style="font-size: 0.8rem;">CLIENTES</div>
                        <div class="card-icon" onclick="animateCard(this)" style="font-size: 1.8rem; bottom: 10px; right: 10px;">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <a href="{{ url('/admin/clientes') }}" class="card-action" style="padding: 5px 10px; font-size: 0.8rem;">
                        <span>Gestionar</span>
                        <i class="fas fa-user-plus"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

<!-- SEGUNDA TARJETA (Productos más vendidos) - Versión colores pastel -->
<div class="col-md-4 mb-4">
    <div class="card h-100" style="border-color: #d8bfd8; background-color: #fff5f7;">
        <div class="card-header" style="background-color: #ffdfba; border-bottom-color: #d8bfd8;">
            <h3 class="card-title" style="color: #8b5f65;">PRODUCTOS MÁS VENDIDOS</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" style="color: #8b5f65;" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" style="color: #8b5f65;" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-body" style="background-color: #fff9fb;">
            <canvas id="pieChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%; display: block;" class="chartjs-render-monitor"></canvas>
        </div>
    </div>
</div>

  <!-- TERCERA TARJETA (Alerta de productos con bajo stock) - Versión colores pastel -->
<div class="col-md-5 mb-5">
    @if($lowStockProducts->count() > 0)
    <div class="card border-left-pastel shadow-lg animate__animated animate__shakeX h-100" style="border-left-color: #ffb6c1;">
        <div class="card-header py-3" style="background-color: #ffd1dc; color: #8b5f65;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-boxes mr-2"></i>
                    <strong class="h5 mb-0">ALERTA DE INVENTARIO</strong>
                </div>
                <span class="badge badge-pill" style="background-color: #f8e8e8; color: #8b5f65;">{{ $lowStockProducts->count() }} PRODUCTOS</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead style="background-color: #f5f5f5;">
                        <tr>
                            <th class="text-uppercase small font-weight-bold" style="color: #8b5f65;">Producto</th>
                            <th class="text-uppercase small font-weight-bold text-center" style="color:#8b5f65;">Stock</th>
                            <th class="text-uppercase small font-weight-bold text-center" style="color: #8b5f65;">Estado</th>
                            <th class="text-uppercase small font-weight-bold text-center" style="color: #8b5f65;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockProducts->take(5) as $product)
                        <tr style="{{ $product->stock < 3 ? 'background-color: #ffecec;' : '' }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40 symbol-light mr-3">
                                        <span class="symbol-label" style="background-color: #fff0f5;">
                                            <i class="fas fa-pills" style="color: #d8bfd8;"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold" style="color: #8b5f65;">{{ $product->nombre }}</div>
                                        <div class="small" style="color: #b8a9a9;">Código: {{ $product->codigo ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <span class="font-weight-bold" style="{{ $product->stock < 5 ? 'color:rgb(240, 105, 117);' : 'color: #ffb347;' }}">
                                    {{ $product->stock }}
                                </span>
                                <small class="text-muted">unidades</small>
                            </td>
                            <td class="text-center align-middle">
                                @if($product->stock < 3)
                                    <span class="badge py-1 px-2" style="background-color: #ffb6c1; color: #8b5f65;">CRÍTICO</span>
                                @elseif($product->stock < 5)
                                    <span class="badge py-1 px-2" style="background-color:rgb(231, 95, 71); color:rgb(247, 240, 241);">BAJO</span>
                                @else
                                    <span class="badge py-1 px-2" style="background-color: #e0f7fa; color: #5a8b8f;">PRECAUCIÓN</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.productos.index', $product->id) }}" 
                                       class="btn btn-sm btn-icon" 
                                       style="background-color: #ffdfba; color: #8b5f65;" 
                                       data-toggle="tooltip" 
                                       title="Reabastecer">
                                        <i class="fas fa-warehouse"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-2" style="background-color: #f8f8f8;">
            <div class="d-flex justify-content-between align-items-center">
                <small style="color: #b8a9a9;">Última actualización: {{ now()->format('d/m/Y H:i') }}</small>
                <button class="btn btn-link p-0" style="color: #ff9aa2;" data-toggle="collapse" data-target="#stockExplanation">
                    <small>¿Cómo interpretar esta alerta?</small>
                </button>
            </div>
            <div id="stockExplanation" class="collapse mt-2">
                <div class="alert" style="background-color: #f8f8f8;">
                    <ul class="mb-0 pl-3">
                        <li><span class="badge" style="background-color: #ffb6c1; color: #8b5f65;">CRÍTICO</span> - Menos de 3 unidades en stock</li>
                        <li><span class="badge" style="background-color:rgb(219, 51, 45); color:rgb(248, 32, 61);">BAJO</span> - Entre 3 y 5 unidades en stock</li>
                        <li><span class="badge" style="background-color: #e0f7fa; color: #5a8b8f;">PRECAUCIÓN</span> - Menos de 10 unidades en stock</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
</div>

@stop

@section('css')

<style>
    .dashboard-card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(125, 86, 166, 0.15);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        height: 140px;
        display: flex;
        flex-direction: column;
        border: none;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(125, 86, 166, 0.2);
    }
    
    .card-content {
        padding: 20px;
        flex-grow: 1;
        position: relative;
    }
    
    .card-value {
        font-size: 2rem;
        font-weight: 700;
        color: #3a1a6a;
        margin-bottom: 5px;
        font-family: 'Segoe UI', Roboto, sans-serif;
    }
    
    .card-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #5a3377;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    
    .card-icon {
        position: absolute;
        right: 20px;
        bottom: 20px;
        font-size: 3rem;
        color: #8e6dbd;
        opacity: 0.3;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .card-icon:hover {
        opacity: 0.5;
        transform: scale(1.1);
    }
    
    .card-action {
        background-color: rgba(255, 255, 255, 0.3);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-decoration: none;
        color: #5a3377;
        font-weight: 600;
        font-size: 0.85rem;
        border-top: 1px solid rgba(90, 51, 119, 0.1);
        transition: all 0.3s ease;
    }
    
    .card-action:hover {
        background-color: rgba(255, 255, 255, 0.4);
    }
    
    .card-action i {
        transition: transform 0.3s ease;
    }
    
    .card-action:hover i {
        transform: scale(1.2);
    }
    
    /* Animación */
    @keyframes cardPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.03); }
        100% { transform: scale(1); }
    }
    
    .card-pulse {
        animation: cardPulse 0.5s ease;
    }
</style>
<style>
    .bg-danger-light {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }
    .symbol {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.42rem;
    }
    .symbol-40 {
        width: 40px;
        height: 40px;
    }
    .symbol-label {
        width: 100%;
        height: 100%;
        border-radius: 0.42rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .bg-warning-opacity-10 {
        background-color: rgba(255, 193, 7, 0.1);
    }
    .animate__shakeX {
        animation: shakeX 0.5s;
    }
    @keyframes shakeX {
        0%, 100% {transform: translateX(0);}
        20%, 60% {transform: translateX(-5px);}
        40%, 80% {transform: translateX(5px);}
    }
</style>

<style>
    .icon-micro {
        font-size: 0.9rem;
        opacity: 0.8;  /* Opacidad ajustada */
    }
    .small-box {
        min-width: 80px;
        border-radius: 6px;  /* Bordes redondeados */
        border: 1px solid rgba(90, 51, 119, 0.1);  /* Borde sutil */
    }
    .small-box .inner {
        min-height: 50px;
        padding: 2px !important;
    }
    .small-box h6 {
        font-size: 1.1rem;
        line-height: 1;
    }
    .text-truncate {
        max-width: 70px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
    }
</style>



@stop

@section('js')
@section('js')
<script>
// Configuración de colores pastel para el gráfico (opcional)
document.addEventListener('DOMContentLoaded', function() {
    var pieChart = document.getElementById('pieChart');
    if (pieChart) {
        new Chart(pieChart, {
            type: 'pie',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    data: {!! json_encode($data) !!},
                    backgroundColor: [
                        '#ffb6c1', // Rosa pastel
                        '#ffdfba', // Durazno pastel
                        '#b5ead7', // Verde menta pastel
                        '#c7ceea', // Azul lila pastel
                        '#e2f0cb'  // Verde claro pastel
                    ],
                    borderColor: '#fff5f7',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    labels: {
                        fontColor: '#8b5f65'
                    }
                }
            }
        });
    }
});
</script>
<script>
    function animateCard(element) {
        element.classList.add('card-pulse');
        setTimeout(() => {
            element.classList.remove('card-pulse');
        }, 500);
    }
</script>
<script>
    function animateIcon(element) {
        element.classList.add('pulse-animation');
        setTimeout(() => {
            element.classList.remove('pulse-animation');
        }, 500);
        
        // Aquí puedes agregar lógica adicional al hacer clic en el icono
        console.log("Icono de ventas clickeado");
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if( (($mensaje = Session::get('mensaje')) && ($icono = Session::get('icono'))) )
<script>
    Swal.fire({
        position: "top-end",
        icon: "{{$icono}}",
        title: "{{$mensaje}}",
        showConfirmButton: false,
        timer: 4000
    });
</script>
@endif

<script>
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                data: {!! json_encode($data) !!},
                backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
        }
    });
</script>
@endsection



@if( (($mensaje = Session::get('mensaje')) && ($icono = Session::get('icono'))) )
<script>
    Swal.fire({
  position: "top-end",
  icon: "{{$icono}}",
  title: "{{$mensaje}}",
  showConfirmButton: false,
  timer: 4000
});
</script>
   @endif
@stop
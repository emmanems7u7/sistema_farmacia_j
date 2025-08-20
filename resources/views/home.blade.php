@extends('layouts.argon')

@section('content')
    @if($tiempo_cambio_contraseña != 1)
        <div class="container-fluid py-4">
            <!-- Primera Fila: Tarjetas Resumen -->

            <div class="row">
                <!-- Tarjeta de Productos -->
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Productos</p>
                                        <h5 class="font-weight-bolder">
                                            {{$total_productos}}
                                        </h5>
                                        <p class="mb-0">
                                            <a href="{{ url('/admin/productos/create') }}"
                                                class="text-primary text-sm font-weight-bolder">
                                                <i class="ni ni-fat-add"></i> Agregar
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                        <i class="ni ni-box-2 text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de Compras -->
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Compras</p>
                                        <h5 class="font-weight-bolder">
                                            {{$total_compras}}
                                        </h5>
                                        <p class="mb-0">
                                            <a href="{{ url('/admin/compras/create') }}"
                                                class="text-primary text-sm font-weight-bolder">
                                                <i class="ni ni-fat-add"></i> Nueva
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                        <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de Ventas -->
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Ventas</p>
                                        <h5 class="font-weight-bolder">
                                            {{$total_ventas}}
                                        </h5>
                                        <p class="mb-0">
                                            <a href="{{ url('/admin/ventas/create') }}"
                                                class="text-primary text-sm font-weight-bolder">
                                                <i class="ni ni-fat-add"></i> Registrar
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                        <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de Clientes -->
                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Clientes</p>
                                        <h5 class="font-weight-bolder">
                                            {{$total_clientes}}
                                        </h5>
                                        <p class="mb-0">
                                            <a href="{{ url('/admin/clientes') }}"
                                                class="text-primary text-sm font-weight-bolder">
                                                <i class="ni ni-fat-add"></i> Gestionar
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                        <i class="ni ni-circle-08 text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>

            <!-- Segunda Fila: Alertas y Gráficos -->
            <div class="row">


                <div class="col-lg-7 mb-3">
                    @if($lowStockProducts->count() > 0)
                        <div class="card shadow-sm border-0 alert-card">
                            <div class="card-header bg-gradient-danger text-white py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-exclamation-triangle mr-2 fs-6"></i>
                                        <div>
                                            <h6 class="mb-0">ALERTA DE INVENTARIO</h6>
                                        </div>
                                    </div>
                                    <span class="badge badge-light badge-sm">
                                        {{ $lowStockProducts->count() }} PRODUCTOS
                                    </span>
                                </div>
                            </div>

                            <div class="card-body p-2">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle">
                                        <thead class="bg-gradient-danger-light">
                                            <tr>
                                                <th class="ps-3" style="font-size: 0.6rem;">PRODUCTO</th>
                                                <th class="text-center" style="font-size: 0.6rem;">STOCK</th>
                                                <th class="text-center" style="font-size: 0.6rem;">ESTADO</th>
                                                <th class="text-center pe-3" style="font-size: 0.6rem;">ACCIONES</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($lowStockProducts->take(4) as $product)
                                                <tr
                                                    class="{{ $product->total_cantidad < 3 ? 'bg-danger-soft' : ($product->total_cantidad < 5 ? 'bg-warning-soft' : 'bg-info-soft') }}">
                                                    <td class="ps-3">
                                                        <div class="media align-items-center">
                                                            <div class="avatar bg-danger-light text-danger rounded-circle mr-2"
                                                                style="width: 24px; height: 24px;">
                                                                <i class="fas fa-box fs-6"></i>
                                                            </div>
                                                            <div class="media-body">
                                                                <span class="mb-0"
                                                                    style="font-size: 0.85rem; font-weight: 500;">{{ $product->nombre }}</span>
                                                                <small class="d-block text-muted" style="font-size: 0.7rem;">Código:
                                                                    {{ $product->codigo ?? 'N/A' }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <span
                                                            class="font-weight-bold text-{{ $product->total_cantidad < 3 ? 'danger' : ($product->total_cantidad < 5 ? 'warning' : 'info') }}"
                                                            style="font-size: 0.85rem;">
                                                            {{ $product->total_cantidad }}
                                                        </span>
                                                        <small class="text-muted" style="font-size: 0.7rem;">unid.</small>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($product->total_cantidad < 3)
                                                            <span class="badge bg-danger py-1 px-2"
                                                                style="font-size: 0.65rem;">CRÍTICO</span>
                                                        @elseif($product->total_cantidad < 5)
                                                            <span class="badge bg-warning py-1 px-2" style="font-size: 0.65rem;">BAJO</span>
                                                        @else
                                                            <span class="badge bg-info py-1 px-2"
                                                                style="font-size: 0.65rem;">PRECAUCIÓN</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center pe-4">
                                                        <a href="{{ route('admin.productos.index', $product->id) }}"
                                                            class="btn btn-sm btn-danger shadow-sm py-0 px-1" data-toggle="tooltip"
                                                            title="Reabastecer">
                                                            <i class="fas fa-warehouse fa-sm"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card-footer bg-danger-light py-2">
                                <div id="stockExplanation" class="collapse mt-2">
                                    <div class="alert alert-light mb-0 p-2">
                                        <ul class="mb-0" style="font-size: 0.8rem;">
                                            <li class="mb-1">
                                                <span class="badge bg-danger badge-sm mr-1">CRÍTICO</span>
                                                Menos de 3 unidades
                                            </li>
                                            <li class="mb-1">
                                                <span class="badge bg-warning badge-sm mr-1">BAJO</span>
                                                3-5 unidades
                                            </li>
                                            <li>
                                                <span class="badge bg-info badge-sm mr-1">PRECAUCIÓN</span>
                                                Menos de 10 unidades
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card shadow border-0 success-card">
                            <div class="card-body text-center p-3">
                                <i class="fas fa-check-circle text-success fa-3x mb-2"></i>
                                <h6 class="mb-2 text-success">Inventario en buen estado</h6>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem;">Todos los productos tienen niveles adecuados
                                </p>
                            </div>
                        </div>
                    @endif
                </div>









                <!-- Columna de Gráfico de Productos Más Vendidos -->
                <div class="col-lg-5 mb-4">
                    <div class="card shadow h-100">
                        <!-- Encabezado -->
                        <div class="card-header bg-gradient-warning py-2"> <!-- py-1 reduce el padding vertical -->
                            <div class="row align-items-center">
                                <div class="col">
                                    <p class="mb-0 text-white fw-bold" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                                        PRODUCTOS MÁS VENDIDOS
                                    </p> <!-- Usamos <p> en lugar de <h> para más control -->
                                </div>
                            </div>
                        </div>

                        <!-- Cuerpo -->
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="topProductsChart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else

        <div class="alert alert-warning" role="alert">
            <strong>!Alerta!</strong> Debes actualizar tu contraseña
        </div>

    @endif
@endsection


@push('js')

    <script src="./assets/js/plugins/chartjs.min.js"></script>
    <script>
        var ctx1 = document.getElementById("chart-line").getContext("2d");

        var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);

        gradientStroke1.addColorStop(1, 'rgba(251, 99, 64, 0.2)');
        gradientStroke1.addColorStop(0.2, 'rgba(251, 99, 64, 0.0)');
        gradientStroke1.addColorStop(0, 'rgba(251, 99, 64, 0)');
        new Chart(ctx1, {
            type: "line",
            data: {
                labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                    label: "Mobile apps",
                    tension: 0.4,
                    borderWidth: 0,
                    pointRadius: 0,
                    borderColor: "#fb6340",
                    backgroundColor: gradientStroke1,
                    borderWidth: 3,
                    fill: true,
                    data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
                    maxBarThickness: 6

                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            padding: 10,
                            color: '#fbfbfb',
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            color: '#ccc',
                            padding: 20,
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                },
            },
        });
    </script>

    <!-- Script de ejemplo para Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Verificar si el elemento canvas existe
            var canvas = document.getElementById('topProductsChart');
            if (!canvas) {
                console.error('No se encontró el elemento canvas con ID topProductsChart');
                return;
            }

            var ctx = canvas.getContext('2d');

            // Datos desde el controlador (PHP)
            var productLabels = @json($labels);
            var productData = @json($data);

            // Colores pastel para la gráfica
            var backgroundColors = [
                '#ffb6c1', '#d8bfd8', '#ffdfba', '#b5ead7', '#c7ceea'
            ];

            // Crear la gráfica solo si hay datos
            if (productLabels.length > 0 && productData.length > 0) {
                var topProductsChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: productLabels,
                        datasets: [{
                            data: productData,
                            backgroundColor: backgroundColors,
                            borderColor: '#fff9fb',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#8b5f65',
                                    font: {
                                        family: "'Nunito', sans-serif",
                                        size: 12
                                    },
                                    padding: 20
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return `${context.label}: ${context.raw} unidades`;
                                    }
                                },
                                bodyFont: {
                                    family: "'Nunito', sans-serif"
                                }
                            }
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                });
            } else {
                // Mostrar mensaje si no hay datos
                canvas.parentElement.innerHTML = '<p class="text-center text-muted py-4">No hay datos de productos vendidos disponibles</p>';
            }
        });
    </script>
@endpush
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/apple-icon.png')  }}">
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png')  }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <!-- Nucleo Icons -->
    <link href="{{ asset('argon/css/nucleo-icons.css')  }}" rel="stylesheet" />
    <link href="{{ asset('argon/css/nucleo-svg.css')  }}" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('argon/css/argon-dashboard.css?v=2.1.0')  }}" rel="stylesheet" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" crossorigin="" />
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- jQuery (si aún no está incluido) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>


    @vite(['resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    @php
        use App\Models\Seccion;
        use Carbon\Carbon;
        use App\Models\ConfiguracionCredenciales;
        use App\Models\Configuracion;

        $secciones = Seccion::with('menus')->orderBy('posicion')->get();
        $config = ConfiguracionCredenciales::first();
        $configuracion = Configuracion::first();
       
        if (Auth::user()->usuario_fecha_ultimo_password) {
            $ultimoCambio = Carbon::parse(Auth::user()->usuario_fecha_ultimo_password);

            $diferenciaDias = (int) $ultimoCambio->diffInDays(Carbon::now());

            if ($diferenciaDias >= $config->conf_duracion_max) {
                $tiempo_cambio_contraseña = 1;
            } else {
                $tiempo_cambio_contraseña = 2;
            }
        } else {
            $tiempo_cambio_contraseña = 1;
        }

    @endphp
</head>

<body class="g-sidenav-show   bg-gray-100">
    <div class="min-height-300 bg-dark position-absolute w-100"></div>
    <aside
        class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 "
        id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
                aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href=" https://demos.creative-tim.com/argon-dashboard/pages/dashboard.html "
                target="_blank">
                <i class="fas fa-home" style="font-size: 26px;" alt="main_logo"></i>

                <span class="ms-1 font-weight-bold">{{ config('app.name', 'Laravel') }}</span>
            </a>
        </div>
        
        <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav">
            <li class="nav-item d-flex flex-column align-items-center">

            @if (Auth::user()->foto_perfil)
            <img src="{{ asset(Auth::user()->foto_perfil) }}" alt="Foto de perfil" class="rounded-circle" style="width: 115px; height: 115px; object-fit: cover;">
            @else
            <img src="{{ asset('update/imagenes/user.jpg') }}" alt="Foto de perfil" class="rounded-circle" style="width: 115px; height: 115px; object-fit: cover;">             
            @endif

            <p class="ps-3 ms-3 nav-link-text ms-1" style="font-size: 14px; text-align: center;">
                    {{ Auth::user()->usuario_nombres }} {{ Auth::user()->usuario_app }} {{ Auth::user()->usuario_apm }}
            </p>
            </li>
                @foreach(Auth::user()->roles as $role) 
                            <p class="ps-3 ms-3 nav-link-text ms-1" style="font-size: 12px;">
                                {{$role->name;}}

                            </p>
                 @endforeach

        
            <li class="nav-item">
                    <a class="nav-link active" href="{{ route('home') }}">

                        <span class="ps-3 ms-3 nav-link-text ms-1">Inicio</span>
                    </a>
                </li>
                
                <li class="nav-item">
                        <a class="nav-link" href="{{ route('user.actualizar.contraseña') }}">

                            <span class="ps-3 ms-3 nav-link-text ms-1">Actualizar contraseña</span>
                        </a>
                </li>
            @if( $tiempo_cambio_contraseña != 1)
     
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('perfil') }}">
                        <span class="ps-3 ms-3 nav-link-text ms-1">Perfil</span>
                    </a>
                </li>
                @role('admin')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('menus.index') }}">

                        <span class="ps-3 ms-3 nav-link-text ms-1">Gestión de menus</span>
                    </a>
                </li>
                @endrole

                <ul id="secciones-list" class="list-unstyled" {{ $configuracion->mantenimiento ? 'data-draggable="false"' : 'data-draggable="true"' }}>
                    @foreach ($secciones as $seccion)
                        @can($seccion->titulo)
                            <li class="seccion-item mb-3 p-2" data-id="{{ $seccion->id }}">
                                <div class="d-flex align-items-center {{ $configuracion->mantenimiento ? 'text-warning' : '' }}">
                                    <i class="{{ $seccion->icono }} me-2"></i>
                                    <h6 class="m-0 text-uppercase text-xs font-weight-bolder  {{ $configuracion->mantenimiento ? 'text-warning' : '' }}">{{ $seccion->titulo }}</h6>
                                </div>

                                <ul class="list-unstyled ms-4 mt-2">
                                    @foreach ($seccion->menus as $menu)
                                        @can($menu->nombre)
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route($menu->ruta) }}">
                                                    <span class="nav-link-text">{{ $menu->nombre }}</span>
                                                </a>
                                            </li>
                                        @endcan
                                    @endforeach
                                </ul>
                            </li>
                        @endcan
                    @endforeach
                </ul>
        @endif

        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <div
                                    class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-sign-out-alt text-dark text-sm opacity-10"></i>
                                </div>
                                <span class="nav-link-text ms-1">Salir</span>
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
        </li>
           
</ul>
        </div>
<!-- CDN de SortableJS -->
@if($configuracion->mantenimiento == 1)
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    const lista = document.getElementById('secciones-list');

    new Sortable(lista, {
        animation: 150,
        onEnd: function () {
            const orden = Array.from(document.querySelectorAll('.seccion-item'))
                .map((el, index) => ({
                    id: el.dataset.id,
                    posicion: index + 1
                }));

            fetch('{{ route("secciones.ordenar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ orden })
            }).then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alertify.success(data.message);
                } else {
                    alertify.error(data.message || 'Ocurrió un error al ordenar');
                }
            })
        }
    });


</script>
@endif
    </aside>
    <main class="main-content position-relative border-radius-lg ">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl " id="navbarBlur"
            data-scroll="false">
            <div class="container-fluid py-1 px-3">

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                        @foreach ($breadcrumb as $key => $crumb)
                            @if ($key == count($breadcrumb) - 1)
                                
                                <li class="breadcrumb-item text-sm text-white active" aria-current="page">{{ $crumb['name'] }}</li>
                            @else
                             
                                <li class="breadcrumb-item text-sm">
                                    <a class="opacity-5 text-white" href="{{ $crumb['url'] }}">{{ $crumb['name'] }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ol>
                    
                </nav>

                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                        <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                            
                        </div>
                        <ul class="navbar-nav  justify-content-end">
                            
                            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                                <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
                                    <div class="sidenav-toggler-inner">
                                        <i class="sidenav-toggler-line bg-white"></i>
                                        <i class="sidenav-toggler-line bg-white"></i>
                                        <i class="sidenav-toggler-line bg-white"></i>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item px-3 d-flex align-items-center">
                                <a href="javascript:;" class="nav-link text-white p-0">
                                    <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
                                </a>
                            </li>
                        
                        </ul>
                </div>
            </div>
        </nav>
        <!-- End Navbar -->
        <div class="container">
        <div class="main-content position-relative max-height-vh-100 h-100">
        @foreach (['status' => 'success', 'error' => 'error', 'warning' => 'warning'] as $msg => $type)
            @if(session($msg))
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        alertify.set('notifier','position', 'top-right');
                        alertify.{{ $type }}(@json(session($msg)));
                    });
                </script>
            @endif
        @endforeach

        @yield('content')
          </div>
           
        </div>

        
    </main>
    
    <div class="fixed-plugin">
        <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
            <i class="fa fa-cog py-2"> </i>
        </a>
        <div class="card shadow-lg">
            <div class="card-header pb-0 pt-3 ">
                <div class="float-start">
                    <h5 class="mt-3 mb-0">Argon Configurator</h5>
                    <p>See our dashboard options.</p>
                </div>
                <div class="float-end mt-4">
                    <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
                        <i class="fa fa-close"></i>
                    </button>
                </div>
                <!-- End Toggle Button -->
            </div>
            <hr class="horizontal dark my-1">
            <div class="card-body pt-sm-3 pt-0 overflow-auto">
                <!-- Sidebar Backgrounds -->
                <div>
                    <h6 class="mb-0">Sidebar Colors</h6>
                </div>
                <a href="javascript:void(0)" class="switch-trigger background-color">
                    <div class="badge-colors my-2 text-start">
                        <span class="badge filter bg-gradient-primary active" data-color="primary"
                            onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-dark" data-color="dark"
                            onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-info" data-color="info"
                            onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-success" data-color="success"
                            onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-warning" data-color="warning"
                            onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-danger" data-color="danger"
                            onclick="sidebarColor(this)"></span>
                    </div>
                </a>
                <!-- Sidenav Type -->
                <div class="mt-3">
                    <h6 class="mb-0">Sidenav Type</h6>
                    <p class="text-sm">Choose between 2 different sidenav types.</p>
                </div>
                <div class="d-flex">
                    <button class="btn bg-gradient-primary w-100 px-3 mb-2 active me-2" data-class="bg-white"
                        onclick="sidebarType(this)">White</button>
                    <button class="btn bg-gradient-primary w-100 px-3 mb-2" data-class="bg-default"
                        onclick="sidebarType(this)">Dark</button>
                </div>
                <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
                <!-- Navbar Fixed -->
                <div class="d-flex my-3">
                    <h6 class="mb-0">Navbar Fixed</h6>
                    <div class="form-check form-switch ps-0 ms-auto my-auto">
                        <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed"
                            onclick="navbarFixed(this)">
                    </div>
                </div>
                <hr class="horizontal dark my-sm-4">
                <div class="mt-2 mb-5 d-flex">
                    <h6 class="mb-0">Light / Dark</h6>
                    <div class="form-check form-switch ps-0 ms-auto my-auto">
                        <input class="form-check-input mt-1 ms-auto" type="checkbox" id="dark-version"
                            onclick="darkMode(this)">
                    </div>
                </div>
                <a class="btn bg-gradient-dark w-100" href="https://www.creative-tim.com/product/argon-dashboard">Free
                    Download</a>
                <a class="btn btn-outline-dark w-100"
                    href="https://www.creative-tim.com/learning-lab/bootstrap/license/argon-dashboard">View
                    documentation</a>
                <div class="w-100 text-center">
                    <a class="github-button" href="https://github.com/creativetimofficial/argon-dashboard"
                        data-icon="octicon-star" data-size="large" data-show-count="true"
                        aria-label="Star creativetimofficial/argon-dashboard on GitHub">Star</a>
                    <h6 class="mt-3">Thank you for sharing!</h6>
                    <a href="https://twitter.com/intent/tweet?text=Check%20Argon%20Dashboard%20made%20by%20%40CreativeTim%20%23webdesign%20%23dashboard%20%23bootstrap5&amp;url=https%3A%2F%2Fwww.creative-tim.com%2Fproduct%2Fargon-dashboard"
                        class="btn btn-dark mb-0 me-2" target="_blank">
                        <i class="fab fa-twitter me-1" aria-hidden="true"></i> Tweet
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=https://www.creative-tim.com/product/argon-dashboard"
                        class="btn btn-dark mb-0 me-2" target="_blank">
                        <i class="fab fa-facebook-square me-1" aria-hidden="true"></i> Share
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" crossorigin=""></script>
    <!--   Core JS Files   -->
    <script src="{{asset('argon/js/core/popper.min.js')}}"></script>
    <script src="{{asset('argon/js/core/bootstrap.min.js')}}"></script>

    <script src="{{asset('argon/js/plugins/perfect-scrollbar.min.js')}}"></script>
    <script src="{{asset('argon/js/plugins/smooth-scrollbar.min.js')}}"></script>
    <script src="{{asset('argon/js/plugins/chartjs.min.js')}}"></script>

<style>
    .alertify .ajs-modal {
    display: flex !important;
    justify-content: center;
    align-items: center;
}

.alertify .ajs-dialog {
    margin: 0 auto !important;

    transform: translateY(-40%) !important;
}
</style>
    <script>

        alertify.defaults.theme.ok = "btn btn-danger";  
        alertify.defaults.theme.cancel = "btn btn-secondary";
        alertify.defaults.theme.input = "form-control";  
        alertify.defaults.glossary.title = "Confirmar acción"; 
        alertify.defaults.transition = "zoom";             
      
        
        function confirmarEliminacion(formId, mensaje = '¿Estás seguro de que deseas eliminar este elemento?') {
            alertify.confirm(
                'Confirmar eliminación',
                mensaje,
                function () {
                    document.getElementById(formId).submit();
                },
                function () {
                    alertify.error('Eliminación cancelada');
                }
            ).set('labels', { ok: 'Eliminar', cancel: 'Cancelar' });
        }

        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }

       
    </script>

    <script>
        var ctx1 = document.getElementById("chart-line").getContext("2d");

        var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);

        gradientStroke1.addColorStop(1, 'rgba(94, 114, 228, 0.2)');
        gradientStroke1.addColorStop(0.2, 'rgba(94, 114, 228, 0.0)');
        gradientStroke1.addColorStop(0, 'rgba(94, 114, 228, 0)');
        new Chart(ctx1, {
            type: "line",
            data: {
                labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                    label: "Mobile apps",
                    tension: 0.4,
                    borderWidth: 0,
                    pointRadius: 0,
                    borderColor: "#5e72e4",
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
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{asset('argon/js/argon-dashboard.min.js?v=2.1.0')}}"></script>
</body>

</html>
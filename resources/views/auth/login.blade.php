<!--
=========================================================
* Argon Dashboard 3 - v2.1.0
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-dashboard
* Copyright 2024 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/apple-icon.png')  }}">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="{{ asset('argon/css/nucleo-icons.css')  }}" rel="stylesheet" />
    <link href="{{ asset('argon/css/nucleo-svg.css')  }}" rel="stylesheet" />

    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('argon/css/argon-dashboard.css?v=2.1.0')  }}" rel="stylesheet" />


       <!-- Font Awesome Icons -->
       <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
</head>

<body class="">
    <div class="container position-sticky z-index-sticky top-0">
        <div class="row">
            <div class="col-12">
                <!-- Navbar -->

                <!-- End Navbar -->
            </div>
        </div>
    </div>
    <main class="main-content">
        <section class="min-vh-100" style="
            background: url('{{ asset('assets/img/fondo4.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
        ">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-5 col-lg-6 col-md-8">
                        <div class="card border-0" style=" 
                            background-color: rgba(95, 87, 87, 0.5);
                            backdrop-filter: blur(10px);
                            border-radius: 15px;
                            box-shadow: 0 10px 25px rgba(95, 87, 87, 0.5);
                        ">
                            <!-- Encabezado con logo transparente -->
                            <div class="card-header bg-transparent text-center pt-4 pb-3">
                                <div class="d-flex justify-content-center mb-3">
                                    <div style="
                                        width: 120px;
                                        height: 120px;
                                        background: rgba(236, 122, 7, 0.91);
                                        border-radius: 50%;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        border: 2px solid rgba(255, 255, 255, 0.3);
                                        backdrop-filter: blur(5px);
                                    ">
                                        <img src="{{ asset('assets/img/logo3.jpeg') }}" alt="Logo" 
                                             class="img-fluid rounded-circle" 
                                             style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                </div>
                                <h4 class="mb-0 text-black" style="font-weight: 600;">INGRESAR AL SISTEMA</h4>
                            </div>
                            
                            <!-- Cuerpo del formulario transparente -->
                            <div class="card-body px-5 pt-4 pb-3">
                                <form role="form" method="POST" action="{{ route('login') }}">
                                    @csrf
                                    @method('post')
                                    
                                    <!-- Campo Email -->
                                    <div class="mb-4">
                                        <label for="email" class="form-label text-black" style="font-weight: 500;">Correo electrónico</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text" style="
                                                 background: rgba(255, 255, 255, 0.15);
                                                border: 1px solid rgba(255, 255, 255, 0.2);
                                                color: black;
                                            
                                            ">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <input type="email" name="email" id="email" 
                                                   class="form-control text-black" 
                                                   style="
                                                       background: rgba(255, 255, 255, 0.15);
                                                border: 1px solid rgba(255, 255, 255, 0.2);
                                                       border-left: 0;
                                                       backdrop-filter: blur(5px);
                                                   "
                                                   placeholder="usuario@ejemplo.com" 
                                                   value="{{ old('email') ?? '' }}" required>
                                        </div>
                                        @error('email') 
                                            <div class="text-black small mt-1" style="text-shadow: 0 0 3px rgba(255,0,0,0.5);">{{ $message }}</div> 
                                        @enderror
                                    </div>
                                    
                                    <!-- Campo Contraseña -->
                                    <div class="mb-4">
                                        <label for="password" class="form-label text-black" style="font-weight: 500;">Contraseña</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text" style="
                                                background: rgba(255, 255, 255, 0.15);
                                                border: 1px solid rgba(255, 255, 255, 0.2);
                                                color: black;
                                            ">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input type="password" name="password" id="password" 
                                                   class="form-control text-black" 
                                                   style="
                                                       background: rgba(255, 255, 255, 0.1);
                                                       border: 1px solid rgba(255, 255, 255, 0.2);
                                                       border-left: 0;
                                                       backdrop-filter: blur(5px);
                                                   "
                                                   placeholder="Ingresa tu contraseña" 
                                                   value="" required>
                                        </div>
                                        @error('password') 
                                            <div class="text-black small mt-1" style="text-shadow: 0 0 3px rgba(255,0,0,0.5);">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Recordar contraseña -->
                                    <div class="mb-4 d-flex justify-content-between align-items-center">
                                       
                                        <a href="{{ route('reset-password') }}" class="small" style="
                                            color: rgba(255, 255, 255, 0.8);
                                            text-decoration: none;
                                            transition: all 0.3s ease;
                                        ">
                                            ¿Olvidaste tu contraseña?
                                        </a>
                                    </div>
                                    
                                    <!-- Botón de Login -->
                                    <div class="d-grid mb-3">
                                        <button type="submit" class="btn btn-lg py-3" 
                                                style="
                                                    background: linear-gradient(135deg, rgba(230, 177, 5, 0.8) 0%, rgba(255,94,0,0.9) 100%);
                                                    border: none;
                                                    color: black;
                                                    font-weight: 600;
                                                    letter-spacing: 0.5px;
                                                    transition: all 0.3s ease;
                                                    box-shadow: 0 4px 15px rgba(255, 94, 0, 0.3);
                                                ">
                                            Iniciar sesión
                                        </button>
                                    </div>
                                    
                                    <!-- Registro -->
                                    <div class="text-center mt-4">
                                        <p class="small text-black-50 mb-0">¿No tienes una cuenta? 
                                            <a href="{{ route('register') }}" class="fw-bold" style="
                                                color: rgba(255, 255, 255, 0.9);
                                                text-decoration: none;
                                                transition: all 0.3s ease;
                                            ">
                                                Regístrate aquí
                                            </a>
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="{{asset('argon/js/core/popper.min.js')}}"></script>
    <script src="{{asset('argon/js/core/bootstrap.min.js')}}"></script>

    <script src="{{asset('argon/js/plugins/perfect-scrollbar.min.js')}}"></script>
    <script src="{{asset('argon/js/plugins/smooth-scrollbar.min.js')}}"></script>
    <script src="{{asset('argon/js/plugins/chartjs.min.js')}}"></script>

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
    <script src="../assets/js/argon-dashboard.min.js?v=2.1.0"></script>
</body>

</html>
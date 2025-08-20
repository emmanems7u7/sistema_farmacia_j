<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi App</title>
    <!-- Estilos y scripts -->
</head>
<body>
    <div class="container">
        @yield('content')  <!-- Aquí se carga el contenido de las vistas hijas -->
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Roles</title>
    <style>
        /* Estilos profesionales para el PDF */
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #3498db; color: white; text-align: left; padding: 8px; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <style>
    .compact-header {
        text-align: center;
        padding: 12px 0;
        margin-bottom: 15px;
        border-bottom: 1px solid #e0e0e0;
        font-family: 'Segoe UI', Arial, sans-serif;
    }

    .compact-logo {
        margin: 0 auto 10px; /* Centrado con margen inferior reducido */
    }

    .compact-logo img {
        height: 65px; 
        width: auto;
        max-width: 150px;
    }

    .compact-title {
        margin: 0;
        font-size: 18px; 
        font-weight: 600;
        color: #2d3748;
        line-height: 1.3;
    }

    .compact-subtitle {
        margin: 4px 0 0;
        font-size: 12px;
        color: #4a5568;
    }

    .compact-meta {
        margin-top: 6px;
        font-size: 11px;
        color: #718096;
    }
</style>

<div class="compact-header">
    <div class="compact-logo">
        <img src="{{ public_path('assets/img/logofarmacia.jpeg') }}" alt="Logo Farmacia">
    </div>
    
    <div>
        <h1 class="compact-title">REPORTE DE ROLES </h1>
        <p class="compact-subtitle">Farmacia Mariel</p>
        <div class="compact-meta">
            {{ $fecha_generacion }} 
        </div>
    </div>
</div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Permisos</th>
                <th>Usuarios</th>
                <th>Creado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
            <tr>
                <td>{{ $role->id }}</td>
                <td>{{ $role->name }}</td>
                <td>{{ $role->permissions->pluck('name')->implode(', ') }}</td>
                <td>{{ $role->users->pluck('name')->implode(', ') }}</td>
                <td>{{ $role->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        <p>Sistema de Gestión - {{ date('Y') }} </p>
       
    </div>
</body>
</html>

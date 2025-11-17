<div class="row">
    <div class="col-12 mb-3">
        <label for="name">Nombre de Usuario</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
            placeholder="Nombre de usuario" value="{{ old('name', $user->name ?? '') }}" required
            onkeypress="return validarNombreUsuario(event)">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 mb-3">
        <label for="email">Correo</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
            placeholder="Correo" value="{{ old('email', $user->email ?? '') }}" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label for="usuario_nombres">Nombres</label>
        <input type="text" class="form-control @error('usuario_nombres') is-invalid @enderror" id="usuario_nombres"
            name="usuario_nombres" placeholder="Nombre(s)"
            value="{{ old('usuario_nombres', $user->usuario_nombres ?? '') }}" required
            onkeypress="return validarSoloLetras(event)">
        @error('usuario_nombres')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label for="usuario_app">Apellido Paterno</label>
        <input type="text" class="form-control @error('usuario_app') is-invalid @enderror" id="usuario_app"
            name="usuario_app" placeholder="Apellido Paterno" value="{{ old('usuario_app', $user->usuario_app ?? '') }}"
            required onkeypress="return validarSoloLetras(event)">
        @error('usuario_app')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label for="usuario_apm">Apellido Materno</label>
        <input type="text" class="form-control @error('usuario_apm') is-invalid @enderror" id="usuario_apm"
            name="usuario_apm" placeholder="Apellido Materno" value="{{ old('usuario_apm', $user->usuario_apm ?? '') }}"
            required onkeypress="return validarSoloLetras(event)">
        @error('usuario_apm')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label for="usuario_telefono">Celular</label>
        <input type="tel" class="form-control @error('usuario_telefono') is-invalid @enderror" id="usuario_telefono"
            name="usuario_telefono" placeholder="70108735"
            value="{{ old('usuario_telefono', $user->usuario_telefono ?? '') }}" required
            onkeypress="return validarSoloNumeros(event)" maxlength="8">
        @error('usuario_telefono')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 mb-3">
        <label for="usuario_direccion">Dirección</label>
        <input type="text" class="form-control @error('usuario_direccion') is-invalid @enderror" id="usuario_direccion"
            name="usuario_direccion" placeholder="Dirección"
            value="{{ old('usuario_direccion', $user->usuario_direccion ?? '') }}" required>
        @error('usuario_direccion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="role">Rol</label>
        <select name="role" id="role" class="form-control" required>
            @foreach(\Spatie\Permission\Models\Role::all() as $role)
                <option value="{{ $role->name }}" {{ (isset($user) && $user->getRoleNames()->first() === $role->name) ? 'selected' : '' }}>
                    {{ ucfirst($role->name) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-round bg-gradient-info btn-lg w-100 mt-4 mb-0">
            {{ $btnText ?? 'Registrar Usuario' }}
        </button>
    </div>
</div>

<script>
// Función para validar solo letras (incluye espacios y acentos)
function validarSoloLetras(event) {
    const charCode = event.which ? event.which : event.keyCode;
    const charStr = String.fromCharCode(charCode);
    
    // Permitir letras, espacios, y caracteres con acento
    return /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]$/.test(charStr);
}

// Función para validar solo números
function validarSoloNumeros(event) {
    const charCode = event.which ? event.which : event.keyCode;
    const charStr = String.fromCharCode(charCode);
    
    // Permitir solo números
    return /^[0-9]$/.test(charStr);
}

// Función para validar nombre de usuario
function validarNombreUsuario(event) {
    const charCode = event.which ? event.which : event.keyCode;
    const charStr = String.fromCharCode(charCode);
    
    
    return /^[a-zA-Z0-9_]$/.test(charStr);
}


document.addEventListener('DOMContentLoaded', function() {
    const telefonoInput = document.getElementById('usuario_telefono');
    
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function() {
            // Remover cualquier carácter que no sea número
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Limitar a 10 dígitos
            if (this.value.length > 10) {
                this.value = this.value.slice(0, 10);
            }
        });
    }
});
</script>
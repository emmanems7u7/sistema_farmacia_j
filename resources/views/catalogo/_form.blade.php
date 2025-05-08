@csrf

<div class="mb-3">
    <label for="categoria" class="form-label">{{ __('lo.categoria') }}:</label>
    <select class="form-select" id="categoria" name="categoria" required>
        <option value="">-- {{ __('ui.select_one_text') }} {{ __('lo.categoria') }} --</option>
        @foreach ($categorias as $categoria)
            <option value="{{ $categoria->id }}" {{ old('categoria', $catalogo->catalogo_tipo_codigo ?? '') == $categoria->codigo ? 'selected' : '' }}>
                {{ $categoria->nombre }} | {{ $categoria->estado ? 'Activo' : 'Inactivo' }}
            </option>
        @endforeach
    </select>
    @error('categoria')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="catalogo_parent" class="form-label">{{ __('ui.depends_on_text') }}:</label>
    <input type="text" class="form-control" id="catalogo_parent" name="catalogo_parent"
        value="{{ old('catalogo_parent', $catalogo->catalogo_parent ?? '') }}" maxlength="5">
    @error('catalogo_parent')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="catalogo_codigo" class="form-label">{{ __('ui.code_text') }}</label>
    <input type="text" class="form-control" id="catalogo_codigo" name="catalogo_codigo"
        value="{{ old('catalogo_codigo', $catalogo->catalogo_codigo ?? '') }}" maxlength="50" required>
    @error('catalogo_codigo')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="catalogo_descripcion" class="form-label">{{ __('ui.description_text') }}</label>
    <input type="text" class="form-control" id="catalogo_descripcion" name="catalogo_descripcion"
        value="{{ old('catalogo_descripcion', $catalogo->catalogo_descripcion ?? '') }}" maxlength="100" required>
    @error('catalogo_descripcion')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="catalogo_estado" class="form-label">{{ __('ui.status_text') }}</label>
    <select class="form-select" id="catalogo_estado" name="catalogo_estado">
        <option value="1" {{ old('catalogo_estado', $catalogo->catalogo_estado ?? 1) == 1 ? 'selected' : '' }}>Activo
        </option>
        <option value="0" {{ old('catalogo_estado', $catalogo->catalogo_estado ?? 1) == 0 ? 'selected' : '' }}>Inactivo
        </option>
    </select>
    @error('catalogo_estado')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>

<div class="text-end">
    <button type="submit" class="btn btn-primary">
        {!! __('ui.save') !!}
    </button>
</div>
<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if($attributes->get('required'))
            <span class="ms-1 text-danger">*</span>
            <small class="ms-1 text-dark ">wajib diisi</small>
        @endif
    </label>
    <input type="{{ $type }}" class="form-control" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}" {{ $attributes }}>
    <span class="error invalid-feedback">{{ $errors->first($name) }}</span>
</div>

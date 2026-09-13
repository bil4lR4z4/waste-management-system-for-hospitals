<div class="mb-3 {{$divClass}}">
    @if($label)
        <label for="{{ $name }}" class="form-label fw-semibold {{ $labelClass }}">{{ $label }}</label>
    @endif

    <input 
        type="{{ $type }}" 
        name="{{ $name }}" 
        id="{{ $name }}" 
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        class="form-control {{ $class }}"
        {{ $readonly ? 'readonly' : '' }}
        {{ $required ? 'required' : '' }}
        {{ $size ? 'size="'.$size.'"' : '' }}
        {{ $step ? 'step="'.$step.'"' : '' }}
    >

    @error($name)
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

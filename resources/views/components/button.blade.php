<button 
    type="{{ $type }}" 
    {{ $attributes->merge(['class' => "btn btn-$variant " . ($size ? "btn-$size" : '')]) }}
>
    {{ $label }}
</button>

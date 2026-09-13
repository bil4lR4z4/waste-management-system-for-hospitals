<div class="mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    @endif
    <div class="position-relative">
        <input 
            type="password" 
            name="{{ $name }}" 
            id="{{ $name }}" 
            placeholder="{{ $placeholder }}"
            class="form-control pe-5 {{ $class }}"
        >

        <button type="button" 
                class="btn btn-sm position-absolute top-50 translate-middle-y end-0 me-2 border-0 text-primary" 
                onclick="togglePassword('{{ $name }}', this)">
            <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/>
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </button>
    </div>

    @error($name)
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<script>
function togglePassword(inputId, btn) {
    let input = document.getElementById(inputId);
    let icon = btn.querySelector('svg');

    // SVG for Eye (show)
    let eye = `
        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/>
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
    `;

    // SVG for Eye Slash (hide)
    let eyeSlash = `
        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M10.477 5.247A10.6 10.6 0 0112 4.5c6 0 9.75 7.5 9.75 7.5a17.8 17.8 0 01-3.04 3.86M6.23 6.23C3.88 8.09 2.25 12 2.25 12s3.75 7.5 9.75 7.5c1.24 0 2.41-.23 3.49-.66"/>
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9.88 9.88A3 3 0 0012 15a3 3 0 002.12-.88"/>
        </svg>
    `;

    if (input.type === "password") {
        input.type = "text";
        btn.innerHTML = eyeSlash;
    } else {
        input.type = "password";
        btn.innerHTML = eye;
    }
}
</script>

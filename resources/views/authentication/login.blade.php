<x-user.form action="{{ route('register') }}" method="POST" title="Register">
    @csrf

    {{-- First Name --}}
    <x-input-field 
        name="first_name" 
        label="First Name" 
        placeholder="Enter your first name" 
    />

    {{-- Last Name --}}
    <x-input-field 
        name="last_name" 
        label="Last Name" 
        placeholder="Enter your last name" 
    />

    {{-- Email --}}
    <x-input-field 
        name="email" 
        label="Email Address" 
        type="email" 
        placeholder="Enter your email" 
    />

    {{-- Password --}}
    <x-password-field 
        name="password" 
        label="Password" 
        placeholder="Enter your password" 
    />

    <x-button label="Register" variant="primary" size="lg" class="mt-3" />
</x-user.form>

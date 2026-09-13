<x-user.form action="{{ route('user.register') }}" method="POST" title="Create Accountster">
    <div class="row row-cols-2">
        <x-input-field 
            name="first_name" 
            placeholder="First name" 
            class="form-control-lg"
        />
        <x-input-field 
            name="last_name" 
            placeholder="Last name" 
            class="form-control-lg"
        />
    </div>
   
    <x-input-field 
        name="email" 
        type="email" 
        placeholder="E-email address" 
        class="form-control-lg"
    />

    <x-password-field 
        name="password" 
        placeholder="Password" 
        class="form-control-lg"
    />

    <x-button label="Register" variant="primary" size="lg" class="mt-3 w-100" />
</x-user.form>

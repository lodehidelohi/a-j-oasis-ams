@extends('layouts.app')

@section('title', 'Register')
@section('meta_description', 'Create a tenant account at A & J OASIS to book a room online in Koronadal City.')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-person-plus-fill text-primary" style="font-size: 2rem;"></i>
                    <h1 class="h4 mt-2 mb-0">Create a Tenant Account</h1>
                    <p class="text-muted small">Book a room and manage your stay online</p>
                </div>
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="form-floating mb-3">
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Full Name" value="{{ old('name') }}" required autofocus>
                        <label for="name">Full Name</label>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="name@example.com" value="{{ old('email') }}" required>
                        <label for="email">Email address</label>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone" value="{{ old('phone') }}">
                        <label for="phone">Phone</label>
                    </div>
                    <div class="form-floating mb-3 position-relative">
                        <input type="password" name="password" id="password" class="form-control pe-5" placeholder="Password" required>
                        <label for="password">Password</label>
                        <button type="button" class="toggle-password btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted p-0 me-3" data-target="password" tabindex="-1">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div class="form-floating mb-3 position-relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control pe-5" placeholder="Confirm Password" required>
                        <label for="password_confirmation">Confirm Password</label>
                        <button type="button" class="toggle-password btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted p-0 me-3" data-target="password_confirmation" tabindex="-1">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <button class="btn btn-primary w-100"><i class="bi bi-person-check me-1"></i>Register</button>
                </form>
                <p class="text-center mt-3 mb-0 small">Already have an account? <a href="{{ route('login') }}">Login</a></p>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.toggle-password').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = document.getElementById(btn.dataset.target);
            const icon = btn.querySelector('i');
            const showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            icon.classList.toggle('bi-eye', showing);
            icon.classList.toggle('bi-eye-slash', !showing);
        });
    });
</script>
@endsection

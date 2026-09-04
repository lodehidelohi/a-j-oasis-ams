@extends('layouts.app')

@section('title', 'Login')
@section('meta_description', 'Log in to your A & J OASIS tenant or admin account to manage bookings, leases, and payments.')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-house-door-fill text-primary" style="font-size: 2rem;"></i>
                    <h1 class="h4 mt-2 mb-0">Welcome back</h1>
                    <p class="text-muted small">Sign in to your A &amp; J OASIS account</p>
                </div>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-floating mb-3">
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
                        <label for="email">Email address</label>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-floating mb-3 position-relative">
                        <input type="password" name="password" id="password" class="form-control pe-5" placeholder="Password" required>
                        <label for="password">Password</label>
                        <button type="button" class="toggle-password btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted p-0 me-3" data-target="password" tabindex="-1">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <button class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right me-1"></i>Login</button>
                </form>
                <p class="text-center mt-3 mb-0 small">No account? <a href="{{ route('register') }}">Register as a tenant</a></p>
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

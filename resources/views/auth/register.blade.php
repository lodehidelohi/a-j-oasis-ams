@extends('layouts.app')

@section('title', 'Register')
@section('meta_description', 'Create a tenant account at A & J OASIS to book a room online in Koronadal City.')

@section('content')

<style>
    .avatar-upload { position: relative; width: 88px; height: 88px; flex-shrink: 0; }
    .avatar-preview {
        position: relative;
        width: 88px; height: 88px;
        border-radius: 50%;
        background: #e4e6ec;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden;
        color: #8b909a;
        font-size: 2rem;
    }
    .avatar-preview img {
        position: absolute; inset: 0;
        width: 100%; height: 100%;
        object-fit: cover;
        display: none;
    }
    .avatar-badge {
        position: absolute; right: -2px; bottom: -2px;
        width: 30px; height: 30px;
        border-radius: 50%;
        background: var(--bs-primary);
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        border: 2px solid #fff;
        cursor: pointer;
        font-size: .8rem;
    }
    .avatar-badge:hover { filter: brightness(0.9); }
</style>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="avatar-upload mx-auto">
                        <div class="avatar-preview">
                            <img id="avatarImg" alt="Profile photo">
                            <i class="bi bi-person-fill" id="avatarPlaceholder"></i>
                        </div>
                        <label class="avatar-badge" for="avatarInput" title="Add a photo">
                            <i class="bi bi-camera-fill"></i>
                        </label>
                        <input type="file" name="photo" id="avatarInput" accept="image/*" class="d-none">
                    </div>
                    <h1 class="h4 mt-3 mb-0">Create a Tenant Account</h1>
                    <p class="text-muted small">Book a room and manage your stay online</p>
                </div>
                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
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
    document.getElementById('avatarInput').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;
        const img = document.getElementById('avatarImg');
        img.src = URL.createObjectURL(file);
        img.style.display = 'block';
        document.getElementById('avatarPlaceholder').style.display = 'none';
    });

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

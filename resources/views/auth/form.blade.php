@extends('layouts.app')

@section('title', $activeTab === 'signup' ? 'Register' : 'Login')
@section('meta_description', 'Log in or create a tenant account at A & J OASIS, Koronadal City.')

@php
    // A field-level error only ever belongs to one form (e.g. "name" only exists on
    // sign up), so use that to restore the correct tab after a failed submission —
    // regardless of which URL the form actually posted to.
    $activeTab = $errors->has('name') || $errors->has('password_confirmation') ? 'signup' : old('intent', $activeTab);
@endphp

@section('content')
<style>
    .auth-a { display: flex; justify-content: center; padding: 1.5rem 0; }
    .auth-a .card { max-width: 400px; width: 100%; background: #fff; border: 1px solid #e2e5eb; border-radius: 14px; padding: 2rem; box-shadow: 0 1px 2px rgba(18,20,26,.04); }
    .auth-a .brandmark { display: flex; align-items: center; gap: 9px; margin-bottom: 1.5rem; }
    .auth-a .brandmark .mark { width: 32px; height: 32px; border-radius: 8px; background: #2954e5; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .auth-a .brandmark span { font-weight: 700; color: #12141a; }

    .auth-a .tabs { display: flex; margin-bottom: 1.5rem; }
    .auth-a .tab { flex: 1; background: none; border: none; padding: 0 0 .7rem; font-weight: 600; font-size: .92rem; color: #8b909a; border-bottom: 2px solid #e2e5eb; cursor: pointer; transition: color .15s ease, border-color .15s ease; }
    .auth-a .tab.active { color: #12141a; border-color: #2954e5; }

    .auth-a .field-float { position: relative; margin-bottom: 1rem; }
    .auth-a .field-float input { width: 100%; padding: 1.15rem .9rem .4rem; border: 1px solid #e2e5eb; border-radius: 10px; font-size: .95rem; outline: none; transition: border-color .15s ease; background: #fff; }
    .auth-a .field-float input:focus { border-color: #2954e5; }
    .auth-a .field-float.is-invalid input { border-color: #dc3545; }
    .auth-a .field-float label { position: absolute; left: .9rem; top: 50%; transform: translateY(-50%); color: #8b909a; font-size: .95rem; pointer-events: none; background: #fff; padding: 0 .25rem; transition: top .15s ease, transform .15s ease, font-size .15s ease, color .15s ease; }
    .auth-a .field-float input:focus + label,
    .auth-a .field-float input:not(:placeholder-shown) + label { top: 0; transform: translateY(-50%) scale(.8); color: #2954e5; }
    .auth-a .field-float.is-invalid input:focus + label,
    .auth-a .field-float.is-invalid input:not(:placeholder-shown) + label { color: #dc3545; }
    .auth-a .field-error { color: #dc3545; font-size: .8rem; margin: -.6rem 0 1rem; }

    .auth-a .pw-wrap { position: relative; }
    .auth-a .pw-wrap input { padding-right: 2.6rem; }
    .auth-a .pw-toggle { position: absolute; right: .5rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #8b909a; padding: .3rem; line-height: 1; cursor: pointer; }
    .auth-a .pw-toggle:hover { color: #12141a; }

    .auth-a .row-options { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; font-size: .85rem; }
    .auth-a .row-options .checkbox { display: flex; align-items: center; gap: .4rem; color: #565c69; cursor: pointer; }
    .auth-a .link-muted { color: #2954e5; text-decoration: none; }
    .auth-a .link-muted:hover { text-decoration: underline; }

    .auth-a .submit-btn { width: 100%; background: #2954e5; color: #fff; border: none; border-radius: 10px; padding: .75rem; font-weight: 600; font-size: .95rem; transition: background .15s ease; }
    .auth-a .submit-btn:hover { background: #1e40c9; }
    .auth-a .submit-btn:disabled { opacity: .7; cursor: not-allowed; }

    .auth-a .switch-line { text-align: center; font-size: .85rem; color: #8b909a; margin: 1rem 0 0; }
    .auth-a .switch-line a { color: #2954e5; text-decoration: none; font-weight: 600; }
    .auth-a .switch-line a:hover { text-decoration: underline; }

    .auth-a .avatar-upload { position: relative; width: 76px; height: 76px; margin: 0 auto 1.25rem; }
    .auth-a .avatar-preview { position: relative; width: 76px; height: 76px; border-radius: 50%; background: #e4e6ec; display: flex; align-items: center; justify-content: center; overflow: hidden; color: #8b909a; font-size: 1.75rem; }
    .auth-a .avatar-preview img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: none; }
    .auth-a .avatar-badge { position: absolute; right: -2px; bottom: -2px; width: 26px; height: 26px; border-radius: 50%; background: #2954e5; color: #fff; display: flex; align-items: center; justify-content: center; border: 2px solid #fff; cursor: pointer; font-size: .7rem; }
    .auth-a .avatar-badge:hover { background: #1e40c9; }
</style>

<div class="auth-a" id="authA" data-start-tab="{{ $activeTab }}">
    <div class="card">
        <div class="brandmark">
            <div class="mark"><i class="bi bi-house-door-fill"></i></div>
            <span>A &amp; J OASIS</span>
        </div>

        <div class="tabs">
            <button type="button" class="tab" data-tab="signin">Sign in</button>
            <button type="button" class="tab" data-tab="signup">Create account</button>
        </div>

        {{-- Sign in --}}
        <form class="auth-form" id="signinForm" method="POST" action="{{ route('login') }}">
            @csrf
            <input type="hidden" name="intent" value="signin">

            <div class="field-float {{ $errors->has('email') && $activeTab === 'signin' ? 'is-invalid' : '' }}">
                <input type="email" name="email" id="signin-email" placeholder=" " value="{{ old('email') }}" required autofocus>
                <label for="signin-email">Email</label>
            </div>
            @if ($activeTab === 'signin')
                @error('email')<p class="field-error">{{ $message }}</p>@enderror
            @endif

            <div class="field-float pw-wrap">
                <input type="password" name="password" id="signin-password" placeholder=" " required>
                <label for="signin-password">Password</label>
                <button type="button" class="pw-toggle" data-pw-toggle><i class="bi bi-eye"></i></button>
            </div>

            <div class="row-options">
                <label class="checkbox"><input type="checkbox" name="remember"> Remember me</label>
                <a href="#" class="link-muted">Forgot password?</a>
            </div>

            <button type="submit" class="submit-btn">Sign in</button>
            <p class="switch-line">Don't have an account? <a href="#" data-switch-to="signup">Sign up</a></p>
        </form>

        {{-- Create account --}}
        <form class="auth-form d-none" id="signupForm" method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="intent" value="signup">

            <div class="avatar-upload">
                <div class="avatar-preview">
                    <img id="avatarImg" alt="Profile photo">
                    <i class="bi bi-person-fill" id="avatarPlaceholder"></i>
                </div>
                <label class="avatar-badge" for="avatarInput" title="Add a photo">
                    <i class="bi bi-camera-fill"></i>
                </label>
                <input type="file" name="photo" id="avatarInput" accept="image/*" class="d-none">
            </div>

            <div class="field-float {{ $errors->has('name') ? 'is-invalid' : '' }}">
                <input type="text" name="name" id="signup-name" placeholder=" " value="{{ old('name') }}" required>
                <label for="signup-name">Full name</label>
            </div>
            @error('name')<p class="field-error">{{ $message }}</p>@enderror

            <div class="field-float {{ $errors->has('email') && $activeTab === 'signup' ? 'is-invalid' : '' }}">
                <input type="email" name="email" id="signup-email" placeholder=" " value="{{ old('email') }}" required>
                <label for="signup-email">Email</label>
            </div>
            @if ($activeTab === 'signup')
                @error('email')<p class="field-error">{{ $message }}</p>@enderror
            @endif

            <div class="field-float {{ $errors->has('phone') ? 'is-invalid' : '' }}">
                <input type="text" name="phone" id="signup-phone" placeholder=" " value="{{ old('phone') }}">
                <label for="signup-phone">Phone (optional)</label>
            </div>
            @error('phone')<p class="field-error">{{ $message }}</p>@enderror

            <div class="field-float pw-wrap {{ $errors->has('password') ? 'is-invalid' : '' }}">
                <input type="password" name="password" id="signup-password" placeholder=" " required>
                <label for="signup-password">Password</label>
                <button type="button" class="pw-toggle" data-pw-toggle><i class="bi bi-eye"></i></button>
            </div>
            @error('password')<p class="field-error">{{ $message }}</p>@enderror

            <div class="field-float pw-wrap">
                <input type="password" name="password_confirmation" id="signup-password-confirmation" placeholder=" " required>
                <label for="signup-password-confirmation">Confirm password</label>
                <button type="button" class="pw-toggle" data-pw-toggle><i class="bi bi-eye"></i></button>
            </div>

            <button type="submit" class="submit-btn">Create account</button>
            <p class="switch-line">Already have an account? <a href="#" data-switch-to="signin">Sign in</a></p>
        </form>
    </div>
</div>

<script>
    function initAuthForm(rootId) {
        const root = document.getElementById(rootId);
        const tabs = root.querySelectorAll('.tab');
        const forms = { signin: root.querySelector('#signinForm'), signup: root.querySelector('#signupForm') };

        function setTab(tab) {
            tabs.forEach(t => t.classList.toggle('active', t.dataset.tab === tab));
            Object.entries(forms).forEach(([key, form]) => form.classList.toggle('d-none', key !== tab));
        }

        tabs.forEach(t => t.addEventListener('click', () => setTab(t.dataset.tab)));
        root.querySelectorAll('[data-switch-to]').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                setTab(link.dataset.switchTo);
            });
        });
        root.querySelectorAll('[data-pw-toggle]').forEach(btn => {
            btn.addEventListener('click', function () {
                const input = btn.parentElement.querySelector('input');
                const icon = btn.querySelector('i');
                const showing = input.type === 'text';
                input.type = showing ? 'password' : 'text';
                icon.classList.toggle('bi-eye', showing);
                icon.classList.toggle('bi-eye-slash', !showing);
            });
        });

        const avatarInput = root.querySelector('#avatarInput');
        avatarInput?.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;
            const img = root.querySelector('#avatarImg');
            img.src = URL.createObjectURL(file);
            img.style.display = 'block';
            root.querySelector('#avatarPlaceholder').style.display = 'none';
        });

        setTab(root.dataset.startTab || 'signin');
    }

    initAuthForm('authA');
</script>
@endsection

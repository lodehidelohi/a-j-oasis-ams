@extends(auth()->user()->isAdmin() || auth()->user()->isStaff() ? 'layouts.admin' : 'layouts.app')

@section('title', 'My Profile')

@section('content')
<x-page-header title="My Profile" subtitle="Update your account details, photo, and password." />

<div class="row g-4" style="max-width: 720px;">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="d-flex align-items-center gap-3 mb-4">
                        @if ($user->photoUrl())
                            <img src="{{ $user->photoUrl() }}" alt="{{ $user->name }}" class="rounded-circle" style="width: 72px; height: 72px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary-subtle d-flex align-items-center justify-content-center text-secondary fw-semibold" style="width: 72px; height: 72px; font-size: 1.5rem;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <label class="form-label mb-1 small">Profile Photo</label>
                            <input type="file" name="photo" accept="image/*" class="form-control form-control-sm @error('photo') is-invalid @enderror">
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if ($user->photo)
                                <button type="submit" form="removePhotoForm" class="btn btn-link btn-sm text-danger p-0 mt-1">Remove photo</button>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <h2 class="h6 border-top pt-3 mb-3">Change Password</h2>
                    <p class="text-muted small">Leave these blank to keep your current password.</p>

                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password">
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" autocomplete="new-password">
                            @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" class="form-control" autocomplete="new-password">
                        </div>
                    </div>

                    <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
                </form>

                @if ($user->photo)
                    <form id="removePhotoForm" method="POST" action="{{ route('profile.photo.destroy') }}" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

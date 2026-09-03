<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'Dashboard') — A &amp; J OASIS</title>
    <meta name="description" content="@yield('meta_description', 'Manage your booking, lease, payments, and maintenance requests at A & J OASIS, Koronadal City.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('favicon.ico') }}" rel="icon" sizes="any">
    <link href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🏠</text></svg>" rel="icon" type="image/svg+xml">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        html { -webkit-text-size-adjust: 100%; font-size: 18px; }
        body { display: flex; flex-direction: column; min-height: 100vh; }
        main { flex: 1; }
        img { max-width: 100%; height: auto; }
        .navbar-brand { font-weight: 600; letter-spacing: .02em; }
        .nav-link.active { color: #fff !important; font-weight: 600; }
        .table-responsive { border-radius: .5rem; overflow-x: auto; overflow-y: hidden; -webkit-overflow-scrolling: touch; }
        .table { white-space: nowrap; }
        .btn:disabled { cursor: not-allowed; }

        /* Mobile / touch (iOS Safari especially) */
        a, button, .btn, .nav-link { -webkit-tap-highlight-color: transparent; touch-action: manipulation; }
        input, select, textarea, .form-control, .form-select { font-size: 1rem; } /* keeps iOS from auto-zooming on focus */
        .btn-sm { padding: .35rem .55rem; }
        .table td > .btn:not(:first-child),
        .table td > form:not(:first-child) { margin-left: .35rem; }
        .navbar.sticky-top { padding-top: calc(.5rem + env(safe-area-inset-top)); }
        footer.py-4 { padding-bottom: calc(1.5rem + env(safe-area-inset-bottom)); }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}"><i class="bi bi-house-door-fill me-1"></i>A &amp; J OASIS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('rooms.browse') ? 'active' : '' }}" href="{{ route('rooms.browse') }}"><i class="bi bi-search me-1"></i>Browse Rooms</a></li>
                    @auth
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('tenant.bookings.*') ? 'active' : '' }}" href="{{ route('tenant.bookings.index') }}"><i class="bi bi-calendar-check me-1"></i>My Bookings</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('tenant.leases.*') ? 'active' : '' }}" href="{{ route('tenant.leases.index') }}"><i class="bi bi-file-earmark-text me-1"></i>My Lease</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('tenant.payments.*') ? 'active' : '' }}" href="{{ route('tenant.payments.index') }}"><i class="bi bi-credit-card me-1"></i>My Payments</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('tenant.maintenance-requests.*') ? 'active' : '' }}" href="{{ route('tenant.maintenance-requests.index') }}"><i class="bi bi-tools me-1"></i>Maintenance</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('tenant.announcements.*') ? 'active' : '' }}" href="{{ route('tenant.announcements.index') }}"><i class="bi bi-megaphone me-1"></i>Announcements</a></li>
                    @endauth
                </ul>
                <ul class="navbar-nav align-items-lg-center">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link position-relative {{ request()->routeIs('notifications.index') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
                                <i class="bi bi-bell fs-6"></i>
                                @php($unread = auth()->user()->notifications()->whereNull('read_at')->count())
                                @if ($unread)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: .6rem;">
                                        {{ $unread > 9 ? '9+' : $unread }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                        <li class="nav-item"><a class="btn btn-primary btn-sm ms-lg-2" href="{{ route('register') }}">Register</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-4">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('status') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-start">
                    <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="border-top bg-white py-4 mt-auto">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                <div class="d-flex flex-wrap gap-3 small">
                    <a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a>
                    <a href="{{ route('rooms.browse') }}" class="text-decoration-none text-muted">Browse Rooms</a>
                </div>
                <div class="d-flex flex-wrap gap-3 small text-muted">
                    <a href="mailto:hello@ajoasis.test" class="text-decoration-none text-muted"><i class="bi bi-envelope me-1"></i>hello@ajoasis.test</a>
                    <a href="tel:+639123456789" class="text-decoration-none text-muted"><i class="bi bi-telephone me-1"></i>+63 912 345 6789</a>
                </div>
            </div>
            <div class="text-center text-muted small border-top pt-3">
                &copy; {{ now()->year }} A &amp; J OASIS &mdash; Web-Based and Mobile Rental Apartment Management System
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('#mainNav .nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                const nav = document.getElementById('mainNav');
                if (nav.classList.contains('show')) {
                    bootstrap.Collapse.getOrCreateInstance(nav).hide();
                }
            });
        });

        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                const btn = e.submitter || form.querySelector('button[type=submit], button:not([type])');
                if (btn && !btn.disabled) {
                    // Deferred: disabling the submitter synchronously in the submit event strips its
                    // name/value from the submitted form data (the browser builds the entry list right
                    // after this event fires, while the button is already disabled).
                    setTimeout(function () {
                        btn.disabled = true;
                        btn.dataset.originalHtml = btn.innerHTML;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>Please wait…';
                    }, 0);
                }
            });
        });
    </script>
</body>
</html>

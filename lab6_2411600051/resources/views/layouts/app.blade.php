<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BuildRight Hardware') - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">BuildRight Hardware</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('transactions.index') }}">Transactions</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('adjustments.create') }}">Adjustments</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('reports.index') }}">Reports</a></li>
</ul>
                @auth
           @auth
<div class="dropdown me-2">
    <a class="btn btn-sm btn-outline-light position-relative" href="#" role="button"
       data-bs-toggle="dropdown">
        🔔
        
        @if (auth()->user()->unreadNotifications->count() > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ auth()->user()->unreadNotifications->count() }}
            </span>
        @endif
    </a>
    <ul class="dropdown-menu dropdown-menu-end" style="min-width: 320px;">
        @forelse (auth()->user()->unreadNotifications->take(5) as $notification)
            <li>
                <a class="dropdown-item small" href="{{ route('products.show', $notification->data['product_id']) }}">
                    {{ $notification->data['message'] }}
                </a>
            </li>
        @empty
            <li><span class="dropdown-item-text small text-muted">No new notifications</span></li>
        @endforelse
        @if (auth()->user()->unreadNotifications->count() > 0)
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('notifications.markAllRead') }}">
                    @csrf
                    <button type="submit" class="dropdown-item small">Mark all as read</button>
                </form>
            </li>
        @endif
    </ul>
</div>
@endauth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light">Log Out</button>
                </form>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container my-4">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>

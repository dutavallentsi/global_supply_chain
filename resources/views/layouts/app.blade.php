<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Supply Chain Risk Monitor')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @stack('styles')
</head>
<body>

    <div class="app-shell">

        {{-- SIDEBAR --}}
        <aside class="app-sidebar">
            <div class="sidebar-brand">
                <span class="brand-icon"><i class="fa-solid fa-globe"></i></span>
                <div>
                    <div class="brand-title">Global Supply Chain</div>
                    <div class="brand-subtitle">RISK INTELLIGENCE</div>
                </div>
            </div>

            <nav class="sidebar-menu">
                <a href="{{ route('dashboard.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-table-columns"></i> Dashboard
                </a>
                <a href="{{ route('pages.risk-analysis') }}" class="sidebar-link {{ request()->routeIs('pages.risk-analysis') ? 'active' : '' }}">
                    <i class="fa-solid fa-triangle-exclamation"></i> Risk Analysis
                </a>
                <a href="{{ route('pages.weather') }}" class="sidebar-link {{ request()->routeIs('pages.weather') ? 'active' : '' }}">
                    <i class="fa-solid fa-cloud-sun"></i> Weather
                </a>
                <a href="{{ route('pages.currency') }}" class="sidebar-link {{ request()->routeIs('pages.currency') ? 'active' : '' }}">
                    <i class="fa-solid fa-money-bill-transfer"></i> Currency
                </a>
                <a href="{{ route('pages.news') }}" class="sidebar-link {{ request()->routeIs('pages.news') ? 'active' : '' }}">
                    <i class="fa-solid fa-newspaper"></i> News
                </a>
                <a href="{{ route('pages.port-location') }}" class="sidebar-link {{ request()->routeIs('pages.port-location') ? 'active' : '' }}">
                    <i class="fa-solid fa-location-dot"></i> Port Location
                </a>
                <a href="{{ route('pages.country-comparison') }}" class="sidebar-link {{ request()->routeIs('pages.country-comparison') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-simple"></i> Country Comparison
                </a>
            </nav>

            @auth
                <div class="sidebar-footer">
                    <div class="sidebar-username">{{ strtoupper(auth()->user()->name) }}</div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-sidebar-logout">
                            <i class="fa-solid fa-right-from-bracket"></i> Keluar
                        </button>
                    </form>
                </div>
            @endauth
        </aside>

        {{-- MAIN CONTENT --}}
        <div class="app-main">
            <div class="app-topbar">
                <div>
                    <h4 class="mb-0 fw-bold">@yield('title', 'Dashboard')</h4>
                    <div class="text-muted small">Risk Intelligence Platform</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small d-none d-md-inline" id="last-updated-label"></span>
                    <button class="btn btn-outline-secondary btn-sm" id="btn-refresh-all">
                        <i class="fa-solid fa-rotate"></i> Refresh Data
                    </button>
                </div>
            </div>

            @auth
                <h5 class="welcome-text mb-4">Selamat datang, {{ strtoupper(auth()->user()->name) }}</h5>
            @endauth

            <div class="app-content">
                @yield('content')
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Penggajian')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell"> 
        <aside class="app-sidebar border-end"> 
            <div>
                <a class="d-block text-decoration-none text-dark fw-semibold fs-5 mb-4" href="{{ route('penggajian.index') }}">
                    Sistem Penggajian
                </a>

                <nav class="nav flex-column gap-2">
                    <a href="{{ route('penggajian.index') }}"
                        class="nav-link nav-link-custom {{ request()->routeIs('penggajian.*') ? 'active' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('laporan.index') }}"
                        class="nav-link nav-link-custom {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                        Laporan
                    </a>
                </nav>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="mt-auto">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100">Logout</button>
            </form>
        </aside>

        <div class="app-main">
            <header class="app-topbar border-bottom d-md-none">
                <a class="text-decoration-none text-dark fw-semibold" href="{{ route('penggajian.index') }}">Sistem Penggajian</a>
            </header>

            <main class="app-content">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
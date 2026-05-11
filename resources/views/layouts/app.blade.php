<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FacReport')</title>
    <meta name="description" content="Sistem Pelaporan Fasilitas - FacReport">
    <meta name="user-id" content="{{ auth()->id() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <button class="mobile-menu-btn lg:hidden" id="mobile-nav-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <a href="{{ route('dashboard') }}" class="logo">FacReport</a>
        </div>
        <div class="nav-links">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Beranda</a>
            <a href="{{ route('reports.create') }}" class="{{ request()->routeIs('reports.create') ? 'active' : '' }}">Form Lapor</a>
            <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.index') ? 'active' : '' }}">Riwayat Lapor</a>
        </div>
        <div class="nav-user">
            <a href="{{ route('notifications.index') }}" class="nav-notification">
                <i class="fas fa-bell"></i>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="unread-dot"></span>
                @endif
            </a>
            
            <div class="user-dropdown">
                <div class="dropdown-trigger">
                    <div class="avatar">{{ auth()->user()->initial }}</div>
                    <span class="name">{{ auth()->user()->name }}</span>
                    <i class="fas fa-chevron-down" style="font-size: 10px; opacity: 0.7"></i>
                </div>
                <div class="dropdown-menu">
                    <div class="dropdown-header">
                        <span class="user-name">{{ auth()->user()->name }}</span>
                        <span class="user-role">{{ auth()->user()->role }}</span>
                    </div>

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                            <i class="fas fa-chart-line"></i> Dashboard Admin
                        </a>
                    @elseif(auth()->user()->isTechnician())
                        <a href="{{ route('technician.dashboard') }}" class="dropdown-item">
                            <i class="fas fa-tasks"></i> Dashboard Teknisi
                        </a>
                    @endif

                    <a href="{{ route('profile.show') }}" class="dropdown-item">
                        <i class="fas fa-user-circle"></i> Profil Saya
                    </a>

                    <div class="dropdown-divider"></div>
                    
                    <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display:none">
                        @csrf
                    </form>
                    <button type="button" onclick="document.getElementById('logout-form').submit();" class="dropdown-item logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Keluar
                    </button>
                </div>
            </div>
        </div>
    </nav>
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @yield('content')
    </div>
    <script>
        document.getElementById('mobile-nav-toggle')?.addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
        });
    </script>
</body>
</html>

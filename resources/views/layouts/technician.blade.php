<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Teknisi - FacReport')</title>
    <meta name="user-id" content="{{ auth()->id() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="logo-section">
                <span class="logo">FacReport</span>
                <span class="badge" style="background:var(--warning)">Teknisi</span>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-title">Menu Utama</div>
                <a href="{{ route('technician.dashboard') }}" class="{{ request()->routeIs('technician.dashboard') ? 'active' : '' }}"><i class="fas fa-tasks"></i> Tugas Saya</a>
                
                <div class="nav-title">Akun & Portal</div>
                <a href="{{ route('profile.show') }}" class="{{ request()->routeIs('profile.show') ? 'active' : '' }}"><i class="fas fa-user-circle"></i> Profil Saya</a>
                <a href="{{ route('dashboard') }}" class=""><i class="fas fa-home"></i> Beranda Publik</a>
                <form action="{{ route('logout') }}" method="POST" style="margin-top: 10px; border-top: 1px solid var(--gray-200); padding-top: 10px;">
                    @csrf
                    <button type="submit" style="background:none; border:none; color:var(--danger); cursor:pointer; padding:10px 16px; font-size:14px; font-weight:600; width:100%; text-align:left; display:flex; align-items:center; gap:12px;">
                        <i class="fas fa-sign-out-alt"></i> Keluar
                    </button>
                </form>
            </nav>
            <div class="sidebar-user">
                <div class="avatar" style="background:var(--warning)">T</div>
                <div class="info">
                    <div class="name">{{ auth()->user()->name }}</div>
                    <div class="email">{{ auth()->user()->email }}</div>
                </div>
            </div>
        </aside>
        <main class="admin-content">
            <div class="admin-topbar">
                <div style="display: flex; align-items: center;">
                    <button class="mobile-menu-btn admin-mobile-btn lg:hidden" id="sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2>@yield('page-title', 'Dashboard') <span class="date">— {{ now()->translatedFormat('l, j F Y') }}</span></h2>
                </div>
                <div class="search">
                    <a href="{{ route('notifications.index') }}" class="notification">
                        <i class="fas fa-bell"></i>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="dot"></span>
                        @endif
                    </a>
                </div>
            </div>
            <div class="admin-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
    <script>
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const toggle = document.getElementById('sidebar-toggle');

        function toggleSidebar() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        toggle?.addEventListener('click', toggleSidebar);
        overlay?.addEventListener('click', toggleSidebar);
    </script>
</body>
</html>

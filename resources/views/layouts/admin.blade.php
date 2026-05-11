<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - FacReport')</title>
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
                <span class="badge">Admin</span>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-title">Menu</div>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fas fa-chart-line"></i> Dashboard</a>
                <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}"><i class="fas fa-list"></i> Semua Laporan <span class="count">{{ \App\Models\Report::count() }}</span></a>
                <a href="{{ route('admin.reports') }}?status=menunggu" class=""><i class="fas fa-hourglass-half"></i> Menunggu <span class="count">{{ \App\Models\Report::where('status','menunggu')->count() }}</span></a>
                <a href="{{ route('admin.reports') }}?status=diproses" class=""><i class="fas fa-tools"></i> Diproses <span class="count">{{ \App\Models\Report::where('status','diproses')->count() }}</span></a>
                <a href="{{ route('admin.reports') }}?status=selesai" class=""><i class="fas fa-check-circle"></i> Selesai</a>
                <div class="nav-title">Sistem</div>
                <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}"><i class="fas fa-users"></i> Pengguna</a>
                <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}"><i class="fas fa-cog"></i> Pengaturan</a>
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
                <div class="avatar">A</div>
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

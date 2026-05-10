@extends(auth()->user()->isAdmin() ? 'layouts.admin' : (auth()->user()->isTechnician() ? 'layouts.technician' : 'layouts.app'))

@section('title', 'Profil Saya - FacReport')
@section('page-title', 'Profil Saya')

@section('content')
<div class="page-header" style="{{ !auth()->user()->isAdmin() && !auth()->user()->isTechnician() ? '' : 'display:none' }}">
    <div class="subtitle">Pengaturan Akun</div>
    <h1>Profil Saya</h1>
    <p>Kelola informasi pribadi dan keamanan akun Anda.</p>
</div>

<div class="detail-grid" style="grid-template-columns: 1fr 1.5fr;">
    <!-- Profile Card -->
    <div>
        <div class="form-card" style="text-align:center">
            <div class="nav-user" style="justify-content:center; margin-bottom:20px">
                <div class="avatar" style="width:80px; height:80px; font-size:32px">{{ auth()->user()->initial }}</div>
            </div>
            <h2 style="font-size:20px; font-weight:700; margin-bottom:4px">{{ auth()->user()->name }}</h2>
            <p style="color:var(--gray-500); font-size:14px; margin-bottom:20px">{{ auth()->user()->email }}</p>
            
            <div style="display:flex; flex-direction:column; gap:10px">
                <span class="status status-{{ auth()->user()->role == 'admin' ? 'success' : (auth()->user()->role == 'technician' ? 'warning' : 'info') }}" style="padding:6px 12px; border-radius:20px; font-size:12px; font-weight:600">
                    Role: {{ ucfirst(auth()->user()->role) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Edit Forms -->
    <div style="display:flex; flex-direction:column; gap:24px">
        <!-- Basic Info -->
        <div class="form-card">
            <h3 style="margin-bottom:20px">Informasi Pribadi</h3>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-actions" style="justify-content: flex-start">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>

        <!-- Security -->
        <div class="form-card">
            <h3 style="margin-bottom:20px">Keamanan Akun</h3>
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label>Password Saat Ini</label>
                    <input type="password" name="current_password" required>
                    @error('current_password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Password Baru</label>
                        <input type="password" name="password" required>
                        @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required>
                    </div>
                </div>

                <div class="form-actions" style="justify-content: flex-start">
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

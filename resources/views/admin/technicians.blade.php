@extends('layouts.admin')
@section('page-title', 'Teknisi')
@section('content')
<div class="flex-between" style="margin-bottom:16px">
    <h3>Daftar Teknisi</h3>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
    <div class="data-section">
        <table class="data-table">
            <thead><tr><th>Nama</th><th>Spesialisasi</th><th>Status</th><th>Tugas</th><th>Aksi</th></tr></thead>
            <tbody>
            @foreach($technicians as $t)
            <tr>
                <td style="font-weight:600">{{ $t->name }}</td>
                <td>{{ $t->specialization }}</td>
                <td>
                    <span style="display:inline-flex;align-items:center;gap:4px">
                        <span style="width:6px;height:6px;border-radius:50%;background:{{ $t->status=='aktif'?'var(--success)':($t->status=='sibuk'?'var(--warning)':'var(--gray-400)') }}"></span>
                        {{ ucfirst($t->status) }}
                    </span>
                </td>
                <td>{{ $t->reports_count }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.technicians.update', $t) }}" style="display:inline">
                        @csrf @method('PUT')
                        <select name="status" onchange="this.form.submit()" style="padding:4px 8px;border:1px solid var(--gray-200);border-radius:6px;font-size:12px">
                            <option value="aktif" {{ $t->status=='aktif'?'selected':'' }}>Aktif</option>
                            <option value="sibuk" {{ $t->status=='sibuk'?'selected':'' }}>Sibuk</option>
                            <option value="offline" {{ $t->status=='offline'?'selected':'' }}>Offline</option>
                        </select>
                    </form>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="form-card">
        <h3 style="font-size:14px;font-weight:700;margin-bottom:16px">Tambah Teknisi Baru</h3>
        <form method="POST" action="{{ route('admin.technicians.store') }}">
            @csrf
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name" required value="{{ old('name') }}" placeholder="Nama teknisi">
                @error('name') <small style="color:var(--danger)">{{ $message }}</small> @enderror
            </div>
            <div class="form-group">
                <label>Email (untuk Login)</label>
                <input type="email" name="email" required value="{{ old('email') }}" placeholder="email@teknisi.com">
                @error('email') <small style="color:var(--danger)">{{ $message }}</small> @enderror
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Minimal 8 karakter">
                @error('password') <small style="color:var(--danger)">{{ $message }}</small> @enderror
            </div>
            <div class="form-group"><label>Spesialisasi</label><input type="text" name="specialization" value="{{ old('specialization') }}" placeholder="Elektronik, Mekanikal, dll"></div>
            <div class="form-group"><label>No. Telepon</label><input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"></div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Teknisi & Akun</button>
        </form>
    </div>
</div>
@endsection

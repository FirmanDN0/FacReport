@extends('layouts.admin')
@section('page-title', 'Pengguna')
@section('content')

<div class="flex-between" style="margin-bottom: 24px">
    <div>
        <h2 style="font-size: 20px; font-weight: 700">Manajemen Pengguna</h2>
        <p style="color: var(--gray-500); font-size: 14px">Total {{ $users->total() }} pengguna terdaftar dalam sistem.</p>
    </div>
    <button onclick="toggleModal('modalUser')" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Pengguna</button>
</div>

<div class="data-section">
<div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Jumlah Laporan</th>
                <th>Bergabung</th>
                <th style="text-align: right">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $u)
        <tr>
            <td style="font-weight:600">
                <div style="display: flex; align-items: center; gap: 10px">
                    <div class="avatar" style="width: 32px; height: 32px; font-size: 12px">{{ $u->initial }}</div>
                    {{ $u->name }}
                </div>
            </td>
            <td>{{ $u->email }}</td>
            <td>
                <span class="status {{ $u->role=='admin'?'status-diproses':($u->role=='technician'?'status-menunggu':'status-selesai') }}" style="padding:2px 10px;border-radius:10px;font-size:12px">
                    {{ ucfirst($u->role) }}
                </span>
            </td>
            <td>{{ $u->reports_count }}</td>
            <td>{{ $u->created_at->format('d M Y') }}</td>
            <td style="text-align: right">
                <div style="display: flex; gap: 8px; justify-content: flex-end">
                    <button onclick="editUser('{{ $u->id }}', '{{ $u->name }}', '{{ $u->email }}', '{{ $u->role }}')" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></button>
                    @if($u->id !== auth()->id())
                    <form action="{{ route('admin.users.delete', $u->id) }}" method="POST" onsubmit="return confirm('Hapus pengguna ini? Tindakan ini tidak bisa dibatalkan.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </form>
                    @endif
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
</div>

<div class="pagination-wrapper" style="margin-top: 20px">
    {{ $users->links() }}
</div>

<!-- Modal User -->
<div id="modalUser" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center">
    <div class="form-card" style="width: 100%; max-width: 450px; position: relative">
        <h3 id="userModalTitle" style="margin-bottom: 20px">Tambah Pengguna</h3>
        <form id="userForm" method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div id="userMethod"></div>
            
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" id="userName" required placeholder="Nama lengkap">
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="userEmail" required placeholder="email@example.com">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" id="userRole" required>
                        <option value="user">User (Pelapor)</option>
                        <option value="technician">Technician (Teknisi)</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="userPassword" placeholder="Minimal 8 karakter">
                    <small id="passwordNote" style="color: var(--gray-400); display: none">Kosongkan jika tidak ingin mengubah password</small>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 20px">
                <button type="button" onclick="toggleModal('modalUser')" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleModal(id) {
    const modal = document.getElementById(id);
    modal.style.display = modal.style.display === 'none' ? 'flex' : 'none';
    
    if (modal.style.display === 'none') {
        document.getElementById('userModalTitle').innerText = 'Tambah Pengguna';
        document.getElementById('userForm').action = "{{ route('admin.users.store') }}";
        document.getElementById('userMethod').innerHTML = '';
        document.getElementById('userPassword').required = true;
        document.getElementById('passwordNote').style.display = 'none';
        document.getElementById('userForm').reset();
    }
}

function editUser(id, name, email, role) {
    document.getElementById('userModalTitle').innerText = 'Edit Pengguna';
    document.getElementById('userForm').action = `/admin/users/${id}`;
    document.getElementById('userMethod').innerHTML = '@method("PUT")';
    document.getElementById('userName').value = name;
    document.getElementById('userEmail').value = email;
    document.getElementById('userRole').value = role;
    document.getElementById('userPassword').required = false;
    document.getElementById('passwordNote').style.display = 'block';
    document.getElementById('modalUser').style.display = 'flex';
}
</script>

@endsection

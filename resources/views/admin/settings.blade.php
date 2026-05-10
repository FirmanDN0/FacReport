@extends('layouts.admin')
@section('page-title', 'Pengaturan')
@section('content')

<div class="flex-between" style="margin-bottom: 24px">
    <div>
        <h2 style="font-size: 20px; font-weight: 700">Manajemen Fasilitas</h2>
        <p style="color: var(--gray-500); font-size: 14px">Kelola data gedung dan ruangan yang tersedia di sistem.</p>
    </div>
    <div style="display: flex; gap: 10px">
        <button onclick="toggleModal('modalBuilding')" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Gedung</button>
        <button onclick="toggleModal('modalRoom')" class="btn btn-success"><i class="fas fa-plus"></i> Tambah Ruangan</button>
    </div>
</div>

<div style="display: grid; gap: 24px">
    @foreach($buildings as $b)
    <div class="form-card" style="padding: 24px">
        <div class="flex-between" style="margin-bottom: 16px; border-bottom: 1px solid var(--gray-100); padding-bottom: 12px">
            <div>
                <h3 style="font-size: 18px; font-weight: 700; color: var(--dark)">
                    {{ $b->name }} <span style="font-weight: 500; color: var(--gray-400); font-size: 14px">({{ $b->code }})</span>
                </h3>
            </div>
            <div style="display: flex; gap: 8px">
                <button onclick="editBuilding('{{ $b->id }}', '{{ $b->name }}', '{{ $b->code }}')" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i> Edit</button>
                <form action="{{ route('admin.buildings.delete', $b->id) }}" method="POST" onsubmit="return confirm('Hapus gedung ini? Semua ruangan di dalamnya akan terpengaruh.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px">
            @forelse($b->rooms as $room)
            <div style="padding: 12px; background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: var(--radius); display: flex; justify-content: space-between; align-items: center">
                <div>
                    <div style="font-size: 13px; font-weight: 600">{{ $room->name }}</div>
                    <div style="font-size: 11px; color: var(--gray-500)">Lantai {{ $room->floor ?? '-' }}</div>
                </div>
                <div style="display: flex; gap: 4px">
                    <button onclick="editRoom('{{ $room->id }}', '{{ $room->name }}', '{{ $room->building_id }}', '{{ $room->floor }}')" style="background:none; border:none; color:var(--primary); cursor:pointer; font-size:12px"><i class="fas fa-pencil"></i></button>
                    <form action="{{ route('admin.rooms.delete', $room->id) }}" method="POST" onsubmit="return confirm('Hapus ruangan ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none; border:none; color:var(--danger); cursor:pointer; font-size:12px"><i class="fas fa-times"></i></button>
                    </form>
                </div>
            </div>
            @empty
            <p style="grid-column: 1/-1; color: var(--gray-400); font-size: 13px; font-style: italic">Belum ada ruangan di gedung ini.</p>
            @endforelse
        </div>
    </div>
    @endforeach
</div>

<!-- Modal Building -->
<div id="modalBuilding" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center">
    <div class="form-card" style="width: 100%; max-width: 400px; position: relative">
        <h3 id="buildingModalTitle" style="margin-bottom: 20px">Tambah Gedung</h3>
        <form id="buildingForm" method="POST" action="{{ route('admin.buildings.store') }}">
            @csrf
            <div id="buildingMethod"></div>
            <div class="form-group">
                <label>Nama Gedung</label>
                <input type="text" name="name" id="buildingName" required placeholder="Contoh: Gedung Teori A">
            </div>
            <div class="form-group">
                <label>Kode Gedung</label>
                <input type="text" name="code" id="buildingCode" required placeholder="Contoh: A">
            </div>
            <div class="form-actions" style="margin-top: 20px">
                <button type="button" onclick="toggleModal('modalBuilding')" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Room -->
<div id="modalRoom" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center">
    <div class="form-card" style="width: 100%; max-width: 400px; position: relative">
        <h3 id="roomModalTitle" style="margin-bottom: 20px">Tambah Ruangan</h3>
        <form id="roomForm" method="POST" action="{{ route('admin.rooms.store') }}">
            @csrf
            <div id="roomMethod"></div>
            <div class="form-group">
                <label>Pilih Gedung</label>
                <select name="building_id" id="roomBuildingId" required>
                    @foreach($buildings as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Nama Ruangan</label>
                <input type="text" name="name" id="roomName" required placeholder="Contoh: Lab Komputer 1">
            </div>
            <div class="form-group">
                <label>Lantai</label>
                <input type="text" name="floor" id="roomFloor" placeholder="Contoh: 1">
            </div>
            <div class="form-actions" style="margin-top: 20px">
                <button type="button" onclick="toggleModal('modalRoom')" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleModal(id) {
    const modal = document.getElementById(id);
    modal.style.display = modal.style.display === 'none' ? 'flex' : 'none';
    
    // Reset forms if closing
    if (modal.style.display === 'none') {
        if (id === 'modalBuilding') {
            document.getElementById('buildingModalTitle').innerText = 'Tambah Gedung';
            document.getElementById('buildingForm').action = "{{ route('admin.buildings.store') }}";
            document.getElementById('buildingMethod').innerHTML = '';
            document.getElementById('buildingForm').reset();
        } else {
            document.getElementById('roomModalTitle').innerText = 'Tambah Ruangan';
            document.getElementById('roomForm').action = "{{ route('admin.rooms.store') }}";
            document.getElementById('roomMethod').innerHTML = '';
            document.getElementById('roomForm').reset();
        }
    }
}

function editBuilding(id, name, code) {
    document.getElementById('buildingModalTitle').innerText = 'Edit Gedung';
    document.getElementById('buildingForm').action = `/admin/settings/buildings/${id}`;
    document.getElementById('buildingMethod').innerHTML = '@method("PUT")';
    document.getElementById('buildingName').value = name;
    document.getElementById('buildingCode').value = code;
    document.getElementById('modalBuilding').style.display = 'flex';
}

function editRoom(id, name, buildingId, floor) {
    document.getElementById('roomModalTitle').innerText = 'Edit Ruangan';
    document.getElementById('roomForm').action = `/admin/settings/rooms/${id}`;
    document.getElementById('roomMethod').innerHTML = '@method("PUT")';
    document.getElementById('roomName').value = name;
    document.getElementById('roomBuildingId').value = buildingId;
    document.getElementById('roomFloor').value = floor;
    document.getElementById('modalRoom').style.display = 'flex';
}
</script>

@endsection

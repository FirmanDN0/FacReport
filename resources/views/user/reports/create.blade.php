@extends('layouts.app')
@section('title', 'Form Lapor - FacReport')
@section('content')
<a href="{{ route('dashboard') }}" class="back-link"><i class="fas fa-chevron-left"></i> Kembali ke Beranda</a>
<div class="page-header">
    <h1>Formulir Lapor Kerusakan</h1>
    <p>Isi data berikut agar laporan dapat segera ditangani.</p>
</div>

@if($errors->any())
<div class="alert alert-error">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
</div>
@endif

<form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data">
@csrf
<div class="form-card">
    <div class="form-section">
        <div class="form-section-title">Informasi Fasilitas</div>
        <div class="form-group">
            <label>Nama Fasilitas <span class="req">*</span></label>
            <input type="text" name="facility_name" value="{{ old('facility_name') }}" placeholder="Contoh: AC Kelas, Kursi, Proyektor..." required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Lokasi / Ruang <span class="req">*</span></label>
                <select name="room_id" id="room_id" required>
                    <option value="">Pilih Ruangan</option>
                    @foreach($rooms as $room)
                    <option value="{{ $room->id }}" data-building="{{ $room->building_id }}" {{ old('room_id')==$room->id ? 'selected' : '' }}>{{ $room->name }} ({{ $room->building->name }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Gedung</label>
                <select name="building_id" id="building_id" required>
                    <option value="">Pilih Gedung</option>
                    @foreach($buildings as $b)
                    <option value="{{ $b->id }}" {{ old('building_id')==$b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="form-section">
        <div class="form-section-title">Detail Kerusakan</div>
        <div class="form-group">
            <label>Tingkat Kerusakan <span class="req">*</span></label>
            <div class="severity-options">
                <label class="severity-option" id="sev-ringan" onclick="setSeverity('ringan')">
                    <span class="dot dot-green"></span> Ringan
                </label>
                <label class="severity-option active" id="sev-sedang" onclick="setSeverity('sedang')">
                    <span class="dot dot-yellow"></span> Sedang
                </label>
                <label class="severity-option" id="sev-berat" onclick="setSeverity('berat')">
                    <span class="dot dot-red"></span> Berat
                </label>
            </div>
            <input type="hidden" name="severity" id="severity" value="{{ old('severity', 'sedang') }}">
        </div>
        <div class="form-group">
            <label>Detail Kerusakan <span class="req">*</span></label>
            <textarea name="description" placeholder="Jelaskan kronologi atau kondisi barang yang rusak..." required>{{ old('description') }}</textarea>
        </div>
    </div>

    <div class="form-section">
        <div class="form-section-title">Foto Bukti</div>
        <div class="form-group">
            <label>Upload Foto Kerusakan</label>
            <label for="photo-input" class="upload-area" id="upload-area">
                <div class="icon"><i class="fas fa-plus"></i></div>
                <p id="upload-text">Klik untuk Upload Foto</p>
                <small>PNG, JPG — maks. 5MB</small>
            </label>
            <input type="file" name="photo" id="photo-input" accept="image/*" style="display:none" onchange="document.getElementById('upload-text').textContent=this.files[0]?.name||'Klik untuk Upload Foto'">
        </div>
    </div>
</div>

<div class="form-actions">
    <a href="{{ route('dashboard') }}" class="btn btn-outline">Batal</a>
    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Kirim Laporan</button>
</div>
</form>

<script>
function setSeverity(v){
    document.getElementById('severity').value=v;
    document.querySelectorAll('.severity-option').forEach(e=>e.classList.remove('active'));
    document.getElementById('sev-'+v).classList.add('active');
}
document.getElementById('room_id').addEventListener('change',function(){
    var opt=this.options[this.selectedIndex];
    if(opt.dataset.building) document.getElementById('building_id').value=opt.dataset.building;
});
</script>
@endsection

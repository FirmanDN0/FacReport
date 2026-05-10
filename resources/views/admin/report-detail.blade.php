@extends('layouts.admin')
@section('page-title', 'Detail Laporan')
@section('content')
<a href="{{ route('admin.reports') }}" class="back-link"><i class="fas fa-chevron-left"></i> Kembali</a>

<div class="detail-header">
    <div>
        <h1>{{ $report->facility_name }} — {{ $report->room->name }}</h1>
        <span class="id-badge">ID: #{{ $report->report_code }} · Pelapor: {{ $report->user->name }} · {{ $report->created_at->format('d F Y') }}</span>
    </div>
    <div class="detail-status">
        <span class="status-badge status-{{ $report->status }}">{{ $report->status_label }}</span>
    </div>
</div>

<div class="detail-grid">
    <div class="detail-info-card">
        <h3>Informasi Laporan</h3>
        <div class="info-item"><span class="info-icon"><i class="fas fa-box"></i></span><div><div class="info-label">Fasilitas</div><div class="info-value">{{ $report->facility_name }}</div></div></div>
        <div class="info-item"><span class="info-icon"><i class="fas fa-map-marker-alt"></i></span><div><div class="info-label">Lokasi</div><div class="info-value">{{ $report->room->name }} · {{ $report->building->name }}</div></div></div>
        <div class="info-item"><span class="info-icon"><i class="fas fa-exclamation-triangle"></i></span><div><div class="info-label">Tingkat</div><div class="info-value"><span class="status severity-{{ $report->severity }}" style="padding:2px 10px;border-radius:10px;font-size:12px">{{ $report->severity_label }}</span></div></div></div>
        <div class="info-item"><span class="info-icon"><i class="fas fa-file-alt"></i></span><div><div class="info-label">Deskripsi</div><div class="info-value">{{ $report->description }}</div></div></div>
        @if($report->photo)
        <div style="margin-top:12px">
            <small style="color:var(--gray-500);font-weight:600">FOTO KERUSAKAN:</small>
            <img src="{{ asset('storage/'.$report->photo) }}" style="max-width:100%;border-radius:8px;display:block;margin-top:4px" alt="Foto Kerusakan">
        </div>
        @endif
        @if($report->completion_photo)
        <div style="margin-top:16px">
            <small style="color:var(--success);font-weight:600">FOTO BUKTI SELESAI:</small>
            <img src="{{ asset('storage/'.$report->completion_photo) }}" style="max-width:100%;border-radius:8px;display:block;margin-top:4px;border:2px solid var(--success)" alt="Foto Selesai">
        </div>
        @endif
    </div>

    <div>
        <div class="form-card">
            <h3 style="font-size:14px;font-weight:700;margin-bottom:16px">Update Laporan</h3>
            <form method="POST" action="{{ route('admin.reports.update', $report) }}">
                @csrf @method('PUT')
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="menunggu" {{ $report->status=='menunggu'?'selected':'' }}>Menunggu</option>
                        <option value="diproses" {{ $report->status=='diproses'?'selected':'' }}>Diproses</option>
                        <option value="selesai" {{ $report->status=='selesai'?'selected':'' }}>Selesai</option>
                        <option value="dibatalkan" {{ $report->status=='dibatalkan'?'selected':'' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Assign Teknisi</label>
                    <select name="technician_id">
                        <option value="">— Pilih Teknisi —</option>
                        @foreach($technicians as $t)
                        <option value="{{ $t->id }}" {{ $report->technician_id==$t->id?'selected':'' }}>{{ $t->name }} ({{ ucfirst($t->status) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Catatan Teknisi</label>
                    <textarea name="technician_notes" rows="3" placeholder="Tambahkan catatan...">{{ $report->technician_notes }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>

<div class="timeline-card">
    <h3>Riwayat Penanganan</h3>
    <div class="timeline">
        @foreach($report->logs as $log)
        <div class="timeline-item {{ $log->status == 'pending' ? 'pending' : '' }}">
            <div class="dot {{ $log->status == 'completed' ? 'dot-completed' : ($log->status == 'cancelled' ? 'dot-warning-dot' : 'dot-pending') }}"></div>
            <h4>{{ $log->action }}</h4>
            <div class="time">{{ $log->created_at->format('d F Y · H:i') }}</div>
        </div>
        @endforeach
    </div>
</div>
@endsection

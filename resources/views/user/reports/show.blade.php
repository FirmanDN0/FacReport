@extends('layouts.app')
@section('title', $report->facility_name . ' - FacReport')
@section('content')
<a href="{{ route('reports.index') }}" class="back-link"><i class="fas fa-chevron-left"></i> Kembali</a>

<div class="detail-header">
    <div>
        <h1>{{ $report->facility_name }} — {{ $report->room->name }}</h1>
        <span class="id-badge">ID: #{{ $report->report_code }} · Dilaporkan {{ $report->created_at->format('d F Y') }}</span>
    </div>
    <div class="detail-status">
        <span class="status-badge status-{{ $report->status }}">{{ strtoupper($report->status == 'diproses' ? '<i class="fas fa-tools"></i> DALAM PERBAIKAN' : $report->status_label) }}</span>
        <div class="update-date">Update terakhir: {{ $report->updated_at->format('d F Y') }}</div>
    </div>
</div>

<div class="detail-grid">
    <div>
        <div class="detail-photo">
            @if($report->photo)
                <img src="{{ asset('storage/'.$report->photo) }}" alt="Foto Bukti">
            @else
                <div class="placeholder">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                    <p>Foto Bukti Kerusakan</p>
                </div>
            @endif
        </div>
        <div style="font-size:12px;color:var(--gray-400);margin-top:8px"><i class="fas fa-paperclip"></i> {{ $report->photo ? basename($report->photo) : 'Tidak ada foto' }} · {{ $report->created_at->format('d F Y') }}</div>
        
        @if($report->completion_photo)
        <div class="detail-photo" style="margin-top:20px; border-color:var(--success)">
            <img src="{{ asset('storage/'.$report->completion_photo) }}" alt="Foto Selesai">
        </div>
        <div style="font-size:12px;color:var(--success);margin-top:8px"><i class="fas fa-check-circle"></i> BUKTI PERBAIKAN SELESAI</div>
        @endif

        <div class="photo-actions">
            @if($report->photo)
            <a href="{{ asset('storage/'.$report->photo) }}" download class="btn btn-outline btn-sm" style="justify-content:center"><i class="fas fa-download"></i> Unduh Foto Awal</a>
            @endif
            @if($report->completion_photo)
            <a href="{{ asset('storage/'.$report->completion_photo) }}" download class="btn btn-success btn-sm" style="justify-content:center"><i class="fas fa-download"></i> Unduh Bukti Selesai</a>
            @endif
            @if($report->status !== 'selesai' && $report->status !== 'dibatalkan')
            <form method="POST" action="{{ route('reports.cancel', $report) }}" style="display:contents">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm" style="justify-content:center" onclick="return confirm('Yakin ingin membatalkan laporan ini?')"><i class="fas fa-ban"></i> Batalkan</button>
            </form>
            @endif
        </div>
    </div>

    <div class="detail-info-card">
        <h3>Detail Informasi</h3>
        <div class="info-item">
            <span class="info-icon"><i class="fas fa-box"></i></span>
            <div><div class="info-label">Fasilitas</div><div class="info-value">{{ $report->facility_name }}</div></div>
        </div>
        <div class="info-item">
            <span class="info-icon"><i class="fas fa-map-marker-alt"></i></span>
            <div><div class="info-label">Lokasi</div><div class="info-value">{{ $report->room->name }}</div></div>
        </div>
        <div class="info-item">
            <span class="info-icon"><i class="fas fa-file-alt"></i></span>
            <div><div class="info-label">Deskripsi</div><div class="info-value">{{ $report->description }}</div></div>
        </div>
        @if($report->technician_notes)
        <div class="tech-note">
            <div class="note-title"><i class="fas fa-clipboard-list"></i> Catatan Teknisi:</div>
            <p>"{{ $report->technician_notes }}"</p>
        </div>
        @endif
    </div>
</div>

<div class="timeline-card">
    <h3>Riwayat Penanganan</h3>
    <div class="timeline">
        @foreach($report->logs as $log)
        <div class="timeline-item {{ $log->status == 'pending' ? 'pending' : '' }}">
            <div class="dot {{ $log->status == 'completed' ? 'dot-completed' : ($log->status == 'cancelled' ? 'dot-warning-dot' : 'dot-pending') }}"></div>
            <h4>{{ $log->action }}</h4>
            <div class="time">{{ $log->created_at->format('d F Y · H:i') }}{{ $log->description ? ' — '.$log->description : '' }}</div>
        </div>
        @endforeach
    </div>
</div>
@endsection

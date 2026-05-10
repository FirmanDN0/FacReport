@extends('layouts.technician')
@section('page-title', 'Detail Laporan #' . $report->report_code)
@section('content')
<div class="back-link">
    <a href="{{ route('technician.dashboard') }}"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
</div>

<div class="detail-grid">
    <div>
        <div class="detail-photo">
            @if($report->photo)
                <img src="{{ asset('storage/' . $report->photo) }}" alt="Foto Kerusakan">
                <div class="file-info"><i class="fas fa-image"></i> Foto Kerusakan Awal</div>
            @else
                <div class="placeholder">
                    <i class="fas fa-image"></i>
                    <p>Tidak ada foto kerusakan</p>
                </div>
            @endif
        </div>

        @if($report->completion_photo)
        <div class="detail-photo" style="margin-top:20px; border-color:var(--success)">
            <img src="{{ asset('storage/' . $report->completion_photo) }}" alt="Foto Perbaikan">
            <div class="file-info" style="color:var(--success)"><i class="fas fa-check-circle"></i> Foto Bukti Perbaikan Selesai</div>
        </div>
        @endif
    </div>

    <div>
        <div class="form-card">
            <h3 style="margin-bottom:20px; display:flex; justify-content:space-between; align-items:center">
                Update Status Laporan
                <span class="status status-{{ $report->status }}" style="padding:4px 12px; border-radius:20px; font-size:12px">
                    {{ $report->status_label }}
                </span>
            </h3>

            <form method="POST" action="{{ route('technician.reports.update', $report) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label>Status Kerja</label>
                    <select name="status" class="form-control" {{ $report->status == 'selesai' ? 'disabled' : '' }}>
                        <option value="diproses" {{ $report->status == 'diproses' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                        <option value="selesai" {{ $report->status == 'selesai' ? 'selected' : '' }}>Selesai Diperbaiki</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Catatan Teknisi</label>
                    <textarea name="technician_notes" placeholder="Jelaskan tindakan yang diambil..." {{ $report->status == 'selesai' ? 'readonly' : '' }}>{{ $report->technician_notes }}</textarea>
                </div>

                @if($report->status != 'selesai')
                <div class="form-group">
                    <label>Unggah Bukti Perbaikan (Wajib jika status Selesai)</label>
                    <input type="file" name="completion_photo" accept="image/*">
                    <small style="color:var(--gray-400)">Lampirkan foto fasilitas yang sudah diperbaiki.</small>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; margin-top:10px">
                    <i class="fas fa-save"></i> Simpan Pembaruan
                </button>
                @else
                <div class="alert alert-success" style="margin-top:10px; text-align:center">
                    <i class="fas fa-check-circle"></i> Laporan ini telah diselesaikan.
                </div>
                @endif
            </form>
        </div>

        <div class="detail-info-card" style="margin-top:20px">
            <h3>Informasi Pelapor</h3>
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-user"></i></div>
                <div>
                    <div class="info-label">Nama Pelapor</div>
                    <div class="info-value">{{ $report->user->name }}</div>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div>
                    <div class="info-label">Lokasi Fasilitas</div>
                    <div class="info-value">{{ $report->building->name }} — {{ $report->room->name }}</div>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div>
                    <div class="info-label">Deskripsi Kerusakan</div>
                    <div class="info-value">{{ $report->description }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="timeline-card">
    <h3>Log Aktivitas Laporan</h3>
    <div class="timeline">
        @foreach($report->logs as $log)
        <div class="timeline-item">
            <div class="dot dot-completed"></div>
            <h4>{{ $log->action }}</h4>
            <p style="font-size:13px; color:var(--gray-600)">{{ $log->description }}</p>
            <div class="time">{{ $log->created_at->format('d M Y, H:i') }}</div>
        </div>
        @endforeach
    </div>
</div>
@endsection

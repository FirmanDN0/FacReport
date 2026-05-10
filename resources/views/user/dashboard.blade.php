@extends('layouts.app')
@section('title', 'Beranda - FacReport')
@section('content')
<div class="page-header">
    <p class="subtitle"><i class="fas fa-circle" style="font-size: 8px; vertical-align: middle;"></i> Sistem Pelaporan Fasilitas</p>
    <h1 class="hero-title">Halo, <span class="highlight">{{ $user->name }}</span>!<br>Ada Fasilitas yang<br>rusak?</h1>
    <p class="hero-desc">Laporkan kerusakan fasilitas secara mudah dan pantau progres penanganannya secara real-time.</p>
    <div style="margin-top:24px;display:flex;gap:12px">
        <a href="{{ route('reports.create') }}" class="btn btn-primary">+ Buat Laporan Baru</a>
        <a href="{{ route('reports.index') }}" class="btn btn-outline">Lihat Riwayat</a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="icon" style="color:var(--warning)"><i class="fas fa-clock"></i></div>
        <div class="number">{{ $processingReports }}</div>
        <div class="label">Laporan diproses</div>
        <span class="tag tag-warning">Sedang ditangani</span>
    </div>
    <div class="stat-card">
        <div class="icon" style="color:var(--success)"><i class="fas fa-check-circle"></i></div>
        <div class="number">{{ $completedReports }}</div>
        <div class="label">Laporan selesai</div>
        <span class="tag tag-success">Berhasil ditangani</span>
    </div>
    <div class="stat-card">
        <div class="icon" style="color:var(--info)"><i class="fas fa-phone"></i></div>
        <div class="number" style="font-size:20px">0812-1234-5678</div>
        <div class="label">Hubungi admin</div>
        <a href="#" class="detail-link" style="font-size:12px">Bantuan darurat</a>
    </div>
    <div class="stat-card">
        <div class="icon" style="color:var(--gray-500)"><i class="fas fa-envelope"></i></div>
        <div class="number" style="font-size:18px">hotel123@gmail.com</div>
        <div class="label">Feedback Fasilitas</div>
        <a href="#" class="detail-link" style="font-size:12px">Kirim feedback</a>
    </div>
</div>

<div class="flex-between" style="margin-bottom:16px">
    <h2 style="font-size:16px;font-weight:700">Laporan Terkini</h2>
    <a href="{{ route('reports.index') }}" style="font-size:13px;color:var(--primary);font-weight:500">Lihat semua <i class="fas fa-arrow-right"></i></a>
</div>

<div class="report-list">
    @forelse($recentReports as $report)
    <div class="report-item">
        <div class="report-icon">
            @if(str_contains(strtolower($report->facility_name), 'proyektor'))<i class="fas fa-desktop"></i>
            @elseif(str_contains(strtolower($report->facility_name), 'ac'))<i class="fas fa-snowflake"></i>
            @elseif(str_contains(strtolower($report->facility_name), 'kursi'))<i class="fas fa-chair"></i>
            @elseif(str_contains(strtolower($report->facility_name), 'lampu'))<i class="fas fa-lightbulb"></i>
            @elseif(str_contains(strtolower($report->facility_name), 'kran'))<i class="fas fa-faucet"></i>
            @else<i class="fas fa-tools"></i>
            @endif
        </div>
        <div class="report-info">
            <h3>{{ $report->facility_name }} — {{ $report->room->name }}</h3>
            <p class="meta">ID: #{{ $report->report_code }} · {{ $report->created_at->format('d M Y') }} · {{ $report->building->name }}</p>
        </div>
        <div class="report-actions">
            <span class="status status-{{ $report->status }}">{{ $report->status_label }}</span>
        </div>
    </div>
    @empty
    <p style="text-align:center;color:var(--gray-400);padding:40px">Belum ada laporan. Buat laporan pertamamu!</p>
    @endforelse
</div>
@endsection

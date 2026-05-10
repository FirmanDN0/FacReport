@extends('layouts.app')
@section('title', 'Riwayat Laporan - FacReport')
@section('content')
<div class="flex-between" style="margin-bottom:8px">
    <div class="page-header mb-0">
        <h1>Riwayat Laporan Saya</h1>
        <p>Semua laporan kerusakan yang pernah Anda buat.</p>
    </div>
    <a href="{{ route('reports.create') }}" class="btn btn-primary">+ Laporan Baru</a>
</div>

<div class="stats-grid">
    <div class="stat-card"><div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:var(--primary)"></span><span class="number">{{ $totalReports }}</span></div><div class="label">Total</div></div>
    <div class="stat-card"><div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:var(--warning)"></span><span class="number">{{ $processingReports }}</span></div><div class="label">Diproses</div></div>
    <div class="stat-card"><div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:var(--success)"></span><span class="number">{{ $completedReports }}</span></div><div class="label">Selesai</div></div>
    <div class="stat-card"><div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:var(--gray-400)"></span><span class="number">{{ $pendingReports }}</span></div><div class="label">Menunggu</div></div>
</div>

<div class="filters">
    <div class="filter-tabs">
        <a href="{{ route('reports.index') }}" class="{{ !request('status') || request('status')=='semua' ? 'active' : '' }}">Semua</a>
        <a href="{{ route('reports.index', ['status'=>'diproses']) }}" class="{{ request('status')=='diproses' ? 'active' : '' }}">Diproses</a>
        <a href="{{ route('reports.index', ['status'=>'selesai']) }}" class="{{ request('status')=='selesai' ? 'active' : '' }}">Selesai</a>
        <a href="{{ route('reports.index', ['status'=>'menunggu']) }}" class="{{ request('status')=='menunggu' ? 'active' : '' }}">Menunggu</a>
    </div>
    <div class="search-box">
        <form method="GET" action="{{ route('reports.index') }}">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <input type="text" name="search" placeholder="Cari laporan..." value="{{ request('search') }}">
        </form>
    </div>
</div>

<div class="report-list">
    @forelse($reports as $i => $report)
    <div class="report-item">
        <div class="report-num">#{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}</div>
        <div class="report-icon">
            @if(str_contains(strtolower($report->facility_name), 'proyektor'))<i class="fas fa-desktop"></i>
            @elseif(str_contains(strtolower($report->facility_name), 'ac'))<i class="fas fa-snowflake"></i>
            @elseif(str_contains(strtolower($report->facility_name), 'kursi'))<i class="fas fa-chair"></i>
            @elseif(str_contains(strtolower($report->facility_name), 'lampu'))<i class="fas fa-lightbulb"></i>
            @elseif(str_contains(strtolower($report->facility_name), 'kran'))<i class="fas fa-faucet"></i>
            @elseif(str_contains(strtolower($report->facility_name), 'pintu'))<i class="fas fa-door-open"></i>
            @else<i class="fas fa-tools"></i>
            @endif
        </div>
        <div class="report-info">
            <h3>{{ $report->facility_name }} — {{ $report->room->name }}</h3>
            <p class="meta">ID: #{{ $report->report_code }} · {{ $report->created_at->format('d M Y') }} · {{ $report->building->name }}</p>
        </div>
        <div class="report-actions">
            <span class="status status-{{ $report->status }}">{{ $report->status_label }}</span>
            <a href="{{ route('reports.show', $report) }}" class="detail-link">Lihat Detail <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
    @empty
    <p style="text-align:center;color:var(--gray-400);padding:40px">Tidak ada laporan ditemukan.</p>
    @endforelse
</div>

<div class="pagination-wrapper">{{ $reports->withQueryString()->links() }}</div>
@endsection

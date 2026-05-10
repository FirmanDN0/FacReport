@extends('layouts.admin')
@section('page-title', 'Semua Laporan')
@section('content')
<div class="admin-stats">
    <div class="admin-stat"><span class="dot-indicator" style="background:var(--primary)"></span><div><div class="number">{{ $totalReports }}</div><div class="label">Total</div></div></div>
    <div class="admin-stat"><span class="dot-indicator" style="background:var(--warning)"></span><div><div class="number">{{ $processingReports }}</div><div class="label">Diproses</div></div></div>
    <div class="admin-stat"><span class="dot-indicator" style="background:var(--success)"></span><div><div class="number">{{ $completedReports }}</div><div class="label">Selesai</div></div></div>
    <div class="admin-stat"><span class="dot-indicator" style="background:var(--danger)"></span><div><div class="number">{{ $pendingReports }}</div><div class="label">Menunggu</div></div></div>
</div>

<div class="data-section">
    <div style="padding:16px 20px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--gray-200)">
        <div class="filter-tabs">
            <a href="{{ route('admin.reports') }}" class="{{ !request('status') ? 'active' : '' }}">Semua</a>
            <a href="{{ route('admin.reports',['status'=>'menunggu']) }}" class="{{ request('status')=='menunggu' ? 'active' : '' }}">Menunggu</a>
            <a href="{{ route('admin.reports',['status'=>'diproses']) }}" class="{{ request('status')=='diproses' ? 'active' : '' }}">Diproses</a>
            <a href="{{ route('admin.reports',['status'=>'selesai']) }}" class="{{ request('status')=='selesai' ? 'active' : '' }}">Selesai</a>
        </div>
        <form method="GET" class="search-box">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <input type="text" name="search" placeholder="Cari laporan..." value="{{ request('search') }}">
        </form>
    </div>
    <table class="data-table">
        <thead><tr><th>ID</th><th>Fasilitas</th><th>Pelapor</th><th>Gedung</th><th>Tingkat</th><th>Status</th><th>Tanggal</th><th></th></tr></thead>
        <tbody>
        @foreach($reports as $r)
        <tr>
            <td>#{{ $r->report_code }}</td>
            <td>{{ $r->facility_name }}</td>
            <td>{{ $r->user->name }}</td>
            <td>{{ $r->building->name }}</td>
            <td><span class="status severity-{{ $r->severity }}" style="padding:2px 10px;border-radius:10px;font-size:12px">{{ $r->severity_label }}</span></td>
            <td><span class="status status-{{ $r->status }}" style="padding:2px 10px;border-radius:10px;font-size:12px">{{ $r->status_label }}</span></td>
            <td>{{ $r->created_at->format('d M Y') }}</td>
            <td><a href="{{ route('admin.reports.show', $r) }}" style="color:var(--primary);font-size:13px;font-weight:500">Detail →</a></td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="pagination-wrapper">{{ $reports->withQueryString()->links() }}</div>
@endsection

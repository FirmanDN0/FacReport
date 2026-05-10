@extends('layouts.admin')
@section('page-title', 'Dashboard')
@section('content')
<div class="admin-stats">
    <div class="admin-stat">
        <span class="dot-indicator" style="background:var(--primary)"></span>
        <div>
            <div class="number">{{ $totalReports }}</div>
            <div class="label">Total Laporan</div>
            <div class="change text-primary">+{{ $weeklyNewReports }} minggu ini</div>
        </div>
    </div>
    <div class="admin-stat">
        <span class="dot-indicator" style="background:var(--warning)"></span>
        <div>
            <div class="number">{{ $processingReports }}</div>
            <div class="label">Diproses</div>
            <div class="change text-warning">Sedang ditangani</div>
        </div>
    </div>
    <div class="admin-stat">
        <span class="dot-indicator" style="background:var(--success)"></span>
        <div>
            <div class="number">{{ $completedReports }}</div>
            <div class="label">Selesai</div>
            <div class="change text-success">+{{ $recentCompletedDays }} hari ini</div>
        </div>
    </div>
    <div class="admin-stat">
        <span class="dot-indicator" style="background:var(--danger)"></span>
        <div>
            <div class="number">{{ $pendingReports }}</div>
            <div class="label">Menunggu</div>
            <div class="change text-danger">Perlu tindakan</div>
        </div>
    </div>
</div>

<div class="charts-grid">
    <div class="data-section">
        <div class="section-tabs">
            <a href="#" class="active">Laporan Terbaru</a>
            <a href="{{ route('admin.reports') }}?severity=berat">Berat</a>
        </div>
        <div style="padding:0 20px 8px">
            <div class="filter-tabs" style="margin:12px 0 8px">
                <a href="{{ route('admin.dashboard') }}" class="{{ !request('status') ? 'active' : '' }}">Semua</a>
                <a href="{{ route('admin.dashboard', ['status'=>'menunggu']) }}" class="{{ request('status')=='menunggu' ? 'active' : '' }}">Menunggu</a>
                <a href="{{ route('admin.dashboard', ['status'=>'diproses']) }}" class="{{ request('status')=='diproses' ? 'active' : '' }}">Diproses</a>
                <a href="{{ route('admin.dashboard', ['status'=>'selesai']) }}" class="{{ request('status')=='selesai' ? 'active' : '' }}">Selesai</a>
            </div>
        </div>
        <table class="data-table">
            <thead><tr><th>ID</th><th>Fasilitas</th><th>Gedung</th><th>Tingkat</th><th>Status</th><th>Tanggal</th></tr></thead>
            <tbody>
            @foreach($reports->take(6) as $r)
            <tr style="cursor:pointer" onclick="location.href='{{ route('admin.reports.show', $r) }}'">
                <td>#{{ $r->report_code }}</td>
                <td>{{ $r->facility_name }} — {{ $r->room->name }}</td>
                <td>{{ $r->building->name }}</td>
                <td><span class="status severity-{{ $r->severity }}" style="padding:2px 10px;border-radius:10px;font-size:12px">{{ $r->severity_label }}</span></td>
                <td><span class="status status-{{ $r->status }}" style="padding:2px 10px;border-radius:10px;font-size:12px">{{ $r->status_label }}</span></td>
                <td>{{ $r->created_at->format('d M Y') }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div>
        <div class="chart-card">
            <h3>Laporan per Gedung</h3>
            <div class="bar-chart">
                @foreach($buildings as $b)
                <div class="bar-item">
                    <span class="bar-label">{{ $b->name }}</span>
                    <div class="bar-track"><div class="bar-fill" style="width:{{ $totalReports > 0 ? ($b->reports_count/$totalReports*100) : 0 }}%;background:var(--primary)"></div></div>
                    <span class="bar-value">{{ $b->reports_count }}</span>
                </div>
                @endforeach
            </div>
        </div>
        <div class="chart-card" style="margin-top:16px">
            <h3>Tingkat Kerusakan</h3>
            <div class="bar-chart">
                <div class="bar-item"><span class="bar-label">Ringan</span><div class="bar-track"><div class="bar-fill" style="width:{{ $totalReports>0?($severityData['ringan']/$totalReports*100):0 }}%;background:var(--success)"></div></div><span class="bar-value">{{ $severityData['ringan'] }}</span></div>
                <div class="bar-item"><span class="bar-label">Sedang</span><div class="bar-track"><div class="bar-fill" style="width:{{ $totalReports>0?($severityData['sedang']/$totalReports*100):0 }}%;background:var(--warning)"></div></div><span class="bar-value">{{ $severityData['sedang'] }}</span></div>
                <div class="bar-item"><span class="bar-label">Berat</span><div class="bar-track"><div class="bar-fill" style="width:{{ $totalReports>0?($severityData['berat']/$totalReports*100):0 }}%;background:var(--danger)"></div></div><span class="bar-value">{{ $severityData['berat'] }}</span></div>
            </div>
        </div>
    </div>
</div>

<div class="bottom-grid">
    <div class="bottom-card">
        <div class="card-header">
            <h3>Aktivitas Terbaru</h3>
            <a href="{{ route('admin.reports') }}">Lihat semua</a>
        </div>
        @foreach($recentLogs as $log)
        <div class="activity-item">
            <div class="act-icon" style="background:{{ $log->status=='completed'?'#dcfce7':'#fef3c7' }};color:{{ $log->status=='completed'?'var(--success)':'var(--warning)' }}">
                @if($log->status=='completed')
                    <i class="fas fa-check"></i>
                @else
                    <i class="fas fa-arrow-right"></i>
                @endif
            </div>
            <div>
                <div class="act-text">{{ $log->action }} — {{ $log->report->facility_name ?? '' }}</div>
                <div class="act-time">{{ $log->created_at->diffForHumans() }}</div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="bottom-card">
        <div class="card-header">
            <h3>Teknisi Aktif</h3>
            <a href="{{ route('admin.technicians') }}">Kelola</a>
        </div>
        @foreach($technicians as $tech)
        <div class="tech-item">
            <div class="tech-avatar">{{ $tech->initials }}</div>
            <div>
                <div class="tech-name">{{ $tech->name }}</div>
                <div class="tech-status">
                    <span style="width:6px;height:6px;border-radius:50%;background:{{ $tech->status=='aktif'?'var(--success)':($tech->status=='sibuk'?'var(--warning)':'var(--gray-400)') }}"></span>
                    {{ ucfirst($tech->status) }}
                </div>
            </div>
            <div class="tech-tasks">{{ $tech->active_tasks }} tugas</div>
        </div>
        @endforeach
    </div>
</div>
@endsection

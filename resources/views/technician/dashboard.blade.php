@extends('layouts.technician')
@section('page-title', 'Tugas Saya')
@section('content')
<div class="admin-stats">
    <div class="admin-stat">
        <span class="dot-indicator" style="background:var(--primary)"></span>
        <div>
            <div class="number">{{ $stats['total'] }}</div>
            <div class="label">Total Tugas</div>
        </div>
    </div>
    <div class="admin-stat">
        <span class="dot-indicator" style="background:var(--warning)"></span>
        <div>
            <div class="number">{{ $stats['pending'] }}</div>
            <div class="label">Menunggu</div>
        </div>
    </div>
    <div class="admin-stat">
        <span class="dot-indicator" style="background:var(--info)"></span>
        <div>
            <div class="number">{{ $stats['processing'] }}</div>
            <div class="label">Diproses</div>
        </div>
    </div>
    <div class="admin-stat">
        <span class="dot-indicator" style="background:var(--success)"></span>
        <div>
            <div class="number">{{ $stats['completed'] }}</div>
            <div class="label">Selesai</div>
        </div>
    </div>
</div>

<div class="data-section" style="margin-top:24px">
    <div class="section-tabs">
        <a href="#" class="active">Daftar Laporan yang Ditugaskan</a>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fasilitas</th>
                <th>Lokasi</th>
                <th>Tingkat</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $r)
            <tr>
                <td>#{{ $r->report_code }}</td>
                <td>{{ $r->facility_name }}</td>
                <td>{{ $r->building->name }} — {{ $r->room->name }}</td>
                <td>
                    <span class="status severity-{{ $r->severity }}" style="padding:2px 10px;border-radius:10px;font-size:12px">
                        {{ $r->severity_label }}
                    </span>
                </td>
                <td>
                    <span class="status status-{{ $r->status }}" style="padding:2px 10px;border-radius:10px;font-size:12px">
                        {{ $r->status_label }}
                    </span>
                </td>
                <td>{{ $r->created_at->format('d M Y') }}</td>
                <td>
                    <a href="{{ route('technician.reports.show', $r) }}" class="btn btn-sm btn-primary">Detail</a>
                </td>
            </tr>
            @endforeach
            @if($reports->isEmpty())
            <tr>
                <td colspan="7" style="text-align:center;padding:40px;color:var(--gray-400)">Belum ada tugas yang diberikan.</td>
            </tr>
            @endif
        </tbody>
    </table>
    <div style="padding:20px">
        {{ $reports->links() }}
    </div>
</div>
@endsection

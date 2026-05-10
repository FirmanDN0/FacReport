@extends('layouts.admin')
@section('page-title', 'Pengguna')
@section('content')
<div class="data-section">
    <table class="data-table">
        <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Jumlah Laporan</th><th>Bergabung</th></tr></thead>
        <tbody>
        @foreach($users as $u)
        <tr>
            <td style="font-weight:600">{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td><span class="status {{ $u->role=='admin'?'status-diproses':'status-selesai' }}" style="padding:2px 10px;border-radius:10px;font-size:12px">{{ ucfirst($u->role) }}</span></td>
            <td>{{ $u->reports_count }}</td>
            <td>{{ $u->created_at->format('d M Y') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="pagination-wrapper">{{ $users->links() }}</div>
@endsection

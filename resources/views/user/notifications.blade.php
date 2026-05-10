@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Notifikasi - FacReport')
@section('page-title', 'Notifikasi')

@section('content')
<div class="flex-between" style="margin-bottom:20px">
    <div>
        <h1 style="font-size:24px;font-weight:800;color:var(--dark)">Notifikasi</h1>
        <p style="color:var(--gray-500)">Informasi terbaru mengenai laporan Anda.</p>
    </div>
    @if(auth()->user()->unreadNotifications->count() > 0)
    <form action="{{ route('notifications.readAll') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline btn-sm">Tandai semua dibaca</button>
    </form>
    @endif
</div>

<div class="data-section">
    @forelse($notifications as $n)
    <div class="notification-item {{ $n->read_at ? '' : 'unread' }}" onclick="location.href='{{ route('notifications.read', $n->id) }}'" style="padding:16px 20px;border-bottom:1px solid var(--gray-100);cursor:pointer;display:flex;gap:16px;align-items:flex-start;transition:all 0.2s">
        <div class="notif-icon" style="width:40px;height:40px;border-radius:50%;background:{{ $n->read_at ? 'var(--gray-100)' : 'var(--primary-light)' }};color:{{ $n->read_at ? 'var(--gray-400)' : 'var(--primary)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fas {{ $n->data['type'] == 'status_update' ? 'fa-info-circle' : ($n->data['type'] == 'assignment' ? 'fa-user-cog' : 'fa-sticky-note') }}"></i>
        </div>
        <div style="flex:1">
            <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                <h4 style="font-size:14px;font-weight:{{ $n->read_at ? '600' : '700' }};color:var(--dark)">{{ $n->data['facility_name'] }} (#{{ $n->data['report_code'] }})</h4>
                <span style="font-size:12px;color:var(--gray-400)">{{ $n->created_at->diffForHumans() }}</span>
            </div>
            <p style="font-size:13px;color:{{ $n->read_at ? 'var(--gray-500)' : 'var(--dark)' }};margin:0">{{ $n->data['message'] }}</p>
        </div>
        @if(!$n->read_at)
        <div style="width:8px;height:8px;border-radius:50%;background:var(--primary);margin-top:6px"></div>
        @endif
    </div>
    @empty
    <div style="padding:60px;text-align:center;color:var(--gray-400)">
        <i class="fas fa-bell-slash" style="font-size:32px;margin-bottom:12px;display:block"></i>
        Tidak ada notifikasi saat ini.
    </div>
    @endforelse
</div>

<div class="pagination-wrapper" style="margin-top:20px">
    {{ $notifications->links() }}
</div>

@endsection

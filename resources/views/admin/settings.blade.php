@extends('layouts.admin')
@section('page-title', 'Pengaturan')
@section('content')
<div class="form-card">
    <h3 style="font-size:16px;font-weight:700;margin-bottom:20px">Data Gedung & Ruangan</h3>
    @foreach($buildings as $b)
    <div style="margin-bottom:20px;padding:16px;background:var(--gray-50);border-radius:var(--radius);border:1px solid var(--gray-200)">
        <h4 style="font-size:14px;font-weight:700;margin-bottom:8px">{{ $b->name }} ({{ $b->code }})</h4>
        <div style="display:flex;flex-wrap:wrap;gap:8px">
            @foreach($b->rooms as $room)
            <span style="padding:4px 12px;background:var(--white);border:1px solid var(--gray-200);border-radius:20px;font-size:12px">{{ $room->name }}</span>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection

@extends('layouts.app')
@section('title', 'Notifikasi')
@section('content')
<div class="d-flex justify-content-end mb-3">
    <form method="POST" action="{{ route('notifications.readAll') }}">@csrf
        <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-check2-all"></i> Tandai Semua Dibaca</button>
    </form>
</div>
<div class="card"><div class="card-body p-0">
@forelse($notifications as $n)
<div class="d-flex align-items-start gap-3 p-3 border-bottom {{ $n->is_read ? '' : 'bg-primary bg-opacity-10' }}">
    <div class="mt-1">
        @if($n->type === 'low_stock')
            <i class="bi bi-exclamation-triangle text-warning fs-5"></i>
        @elseif($n->type === 'warning')
            <i class="bi bi-exclamation-circle text-danger fs-5"></i>
        @else
            <i class="bi bi-info-circle text-primary fs-5"></i>
        @endif
    </div>
    <div class="flex-grow-1">
        <div class="fw-semibold">{{ $n->title }}</div>
        <div class="small">{{ $n->message }}</div>
        <div class="text-muted small">{{ $n->created_at->diffForHumans() }}</div>
    </div>
    @unless($n->is_read)
    <form method="POST" action="{{ route('notifications.read', $n) }}">@csrf
        <button class="btn btn-sm btn-outline-primary">Baca</button>
    </form>
    @endunless
</div>
@empty
<div class="text-center text-muted py-5">Tidak ada notifikasi</div>
@endforelse
</div>
@if($notifications->hasPages())<div class="card-footer bg-white">{{ $notifications->links() }}</div>@endif
</div>
@endsection

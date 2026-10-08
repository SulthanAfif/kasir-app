@extends('layouts.app')
@section('title', 'Shift Kasir')

@section('content')
@if($current)
<div class="alert alert-success d-flex justify-content-between align-items-center">
    <div>
        <i class="bi bi-check-circle me-1"></i>
        <strong>Shift aktif</strong> sejak {{ $current->opened_at->format('d/m/Y H:i') }}
        — Modal awal: Rp {{ number_format($current->opening_cash, 0, ',', '.') }}
    </div>
    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#closeShiftModal">
        <i class="bi bi-stop-circle"></i> Tutup Shift
    </button>
</div>

<!-- Close Modal -->
<div class="modal fade" id="closeShiftModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('shifts.close', $current) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tutup Shift</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Uang di Laci (Closing Cash) *</label>
                        <input type="number" name="closing_cash" class="form-control" required min="0" step="100">
                        <div class="form-text">Hitung fisik uang tunai di laci kasir.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tutup Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>
@else
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Buka Shift Baru</h5>
        <p class="text-muted">Anda harus membuka shift sebelum dapat menggunakan kasir.</p>
        <form method="POST" action="{{ route('shifts.open') }}" class="row g-3 align-items-end">
            @csrf
            <div class="col-md-4">
                <label class="form-label">Modal Awal (Opening Cash) *</label>
                <input type="number" name="opening_cash" class="form-control" required min="0" step="100" value="0">
            </div>
            <div class="col-md-4">
                <label class="form-label">Catatan</label>
                <input type="text" name="notes" class="form-control" placeholder="Opsional">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-play-circle"></i> Buka Shift
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header bg-white">
        <h6 class="mb-0">Riwayat Shift</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Kasir</th>
                        <th>Buka</th>
                        <th>Tutup</th>
                        <th>Modal</th>
                        <th>Penjualan</th>
                        <th>Selisih</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shifts as $s)
                    <tr>
                        <td class="ps-3">{{ $s->user->name }}</td>
                        <td class="small">{{ $s->opened_at->format('d/m H:i') }}</td>
                        <td class="small">{{ $s->closed_at?->format('d/m H:i') ?? '-' }}</td>
                        <td>Rp {{ number_format($s->opening_cash, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($s->total_sales, 0, ',', '.') }}</td>
                        <td class="{{ ($s->difference ?? 0) < 0 ? 'text-danger' : (($s->difference ?? 0) > 0 ? 'text-success' : '') }}">
                            @if($s->status === 'closed')
                                Rp {{ number_format($s->difference, 0, ',', '.') }}
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($s->status === 'open')
                                <span class="badge bg-success">Open</span>
                            @else
                                <span class="badge bg-secondary">Closed</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('shifts.show', $s) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada shift</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($shifts->hasPages())
    <div class="card-footer bg-white">{{ $shifts->links() }}</div>
    @endif
</div>
@endsection

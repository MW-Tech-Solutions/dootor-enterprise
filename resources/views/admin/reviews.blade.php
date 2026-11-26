@extends('layouts.dashboard')

@section('title', 'Customer Reviews Management | DOOTOR ENTERPRISES')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="h4 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-star-half" style="color: #D4AF37;"></i>
                <span>Customer Reviews Management</span>
            </h2>
            <p class="text-secondary small mb-0">Approve, feature, and curate verified client testimonials displayed on the homepage.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-1.5" style="background: rgba(212, 175, 55, 0.15); color: #AA820A; border: 1px solid rgba(212, 175, 55, 0.3);">
                <i class="bi bi-star-fill text-warning"></i>
                <span>{{ $averageRating }} / 5.0 Average Rating</span>
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 shadow-sm mb-4 small">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    <!-- Metric Counter Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <span class="text-secondary extra-small fw-bold text-uppercase">Total Reviews</span>
                <h3 class="h4 fw-bold text-dark mb-0 mt-1">{{ $totalReviews }}</h3>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <span class="text-secondary extra-small fw-bold text-uppercase">Pending Approval</span>
                <h3 class="h4 fw-bold text-warning mb-0 mt-1">{{ $pendingReviews }}</h3>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <span class="text-secondary extra-small fw-bold text-uppercase">Active On Homepage</span>
                <h3 class="h4 fw-bold text-success mb-0 mt-1">{{ $reviews->where('is_featured', true)->count() }}</h3>
            </div>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Client</th>
                        <th>Service</th>
                        <th>Rating</th>
                        <th>Review Comment</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $rev)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $rev->client_name }}</div>
                                <span class="text-secondary extra-small">{{ $rev->client_title ?? 'Verified Client' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border small fw-medium">{{ $rev->service_name ?? 'General Service' }}</span>
                            </td>
                            <td>
                                <div class="text-warning small d-flex gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $rev->rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                            </td>
                            <td style="max-width: 320px;">
                                <p class="text-secondary small mb-0 line-clamp-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.45;">
                                    "{{ $rev->comment }}"
                                </p>
                            </td>
                            <td>
                                @if($rev->is_approved)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 extra-small fw-bold">Approved</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2.5 py-1 extra-small fw-bold">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($rev->is_featured)
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2.5 py-1 extra-small fw-bold">On Homepage</span>
                                @else
                                    <span class="text-muted extra-small">Hidden</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex align-items-center gap-1.5">
                                    <!-- Toggle Approve -->
                                    <form action="{{ route('admin.reviews.toggle-approve', $rev) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1 extra-small" title="{{ $rev->is_approved ? 'Unapprove' : 'Approve' }}">
                                            <i class="bi bi-{{ $rev->is_approved ? 'slash-circle' : 'check-lg' }}"></i>
                                        </button>
                                    </form>

                                    <!-- Toggle Feature -->
                                    <form action="{{ route('admin.reviews.toggle-feature', $rev) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-1 extra-small" title="{{ $rev->is_featured ? 'Remove from Homepage' : 'Feature on Homepage' }}">
                                            <i class="bi bi-star{{ $rev->is_featured ? '-fill' : '' }}"></i>
                                        </button>
                                    </form>

                                    <!-- Delete -->
                                    <form action="{{ route('admin.reviews.delete', $rev) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this review?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 extra-small" title="Delete Review">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted small">No customer reviews submitted yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reviews->hasPages())
            <div class="p-3 border-top">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

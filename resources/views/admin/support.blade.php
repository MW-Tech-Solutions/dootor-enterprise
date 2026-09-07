@extends('layouts.dashboard')

@section('title', 'Support Desk Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: #0f172a;">Customer Support Desk</h4>
        <p class="text-muted small mb-0">Manage client enquiries, support tickets, and communication threads.</p>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Ticket ID</th>
                        <th>Client</th>
                        <th>Subject</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $t)
                    <tr>
                        <td class="ps-3">
                            <span class="font-monospace fw-bold text-primary">{{ $t->ticket_number }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $t->user->name ?? 'Client' }}</div>
                            <small class="text-muted">{{ $t->user->email ?? '' }}</small>
                        </td>
                        <td>
                            <span class="fw-semibold text-dark">{{ $t->subject }}</span>
                            @if($t->serviceRequest)
                            <div><small class="text-muted">Ref: {{ $t->serviceRequest->reference_number }}</small></div>
                            @endif
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $t->category }}</span></td>
                        <td>
                            <span class="badge bg-{{ $t->priority === 'Urgent' ? 'danger' : ($t->priority === 'High' ? 'warning' : 'secondary') }}">
                                {{ $t->priority }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $t->status === 'Open' ? 'danger' : ($t->status === 'In Progress' ? 'info' : 'success') }} bg-opacity-10 text-{{ $t->status === 'Open' ? 'danger' : ($t->status === 'In Progress' ? 'info' : 'success') }}">
                                {{ $t->status }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $t->created_at->format('M d, Y H:i') }}</td>
                        <td class="text-end pe-3">
                            <a href="{{ route('support.show', $t) }}" class="btn btn-sm text-white fw-semibold" style="background-color: #004225; border-radius: 6px;">
                                <i class="bi bi-chat-text me-1"></i> View Thread
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No support tickets submitted yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

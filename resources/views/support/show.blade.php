@extends('layouts.dashboard')

@section('title', 'Ticket #' . $ticket->ticket_number)

@section('content')
<div class="mb-4">
    <a href="{{ auth()->user()->isAdmin() ? route('support.index') : route('support.index') }}" class="text-decoration-none text-muted small fw-semibold">
        <i class="bi bi-arrow-left me-1"></i> Back to Support Tickets
    </a>
    <div class="d-flex justify-content-between align-items-center mt-2">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0f172a;">{{ $ticket->subject }}</h4>
            <div class="d-flex align-items-center gap-2">
                <span class="font-monospace fw-bold text-primary">{{ $ticket->ticket_number }}</span>
                <span class="text-muted">•</span>
                <span class="badge bg-light text-dark border">{{ $ticket->category }}</span>
                @if($ticket->serviceRequest)
                <span class="text-muted">•</span>
                <small class="text-muted">Application: <strong>{{ $ticket->serviceRequest->reference_number }}</strong></small>
                @endif
            </div>
        </div>
        <div>
            <span class="badge bg-{{ $ticket->status === 'Open' ? 'danger' : ($ticket->status === 'In Progress' ? 'info' : 'success') }} px-3 py-2 fs-6">
                {{ $ticket->status }}
            </span>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Message Discussion Thread -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3 px-4">
                <h6 class="fw-bold mb-0" style="color: #0f172a;">Message History</h6>
            </div>
            <div class="card-body p-4">
                <div class="d-flex flex-column gap-4">
                    @foreach($ticket->messages as $msg)
                    @php
                        $isSenderAdmin = $msg->user && ($msg->user->isAdmin() || in_array($msg->user->role, ['manager', 'processing_officer', 'super_admin']));
                    @endphp
                    <div class="d-flex gap-3 {{ $isSenderAdmin ? 'flex-row-reverse' : '' }}">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" style="width: 42px; height: 42px; background-color: {{ $isSenderAdmin ? '#004225' : '#475569' }};">
                                {{ strtoupper(substr($msg->user->first_name ?? 'U', 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="p-3 shadow-sm border" style="border-radius: 12px; background-color: {{ $isSenderAdmin ? 'rgba(0, 66, 37, 0.04)' : '#ffffff' }}; border-color: {{ $isSenderAdmin ? 'rgba(0, 66, 37, 0.15)' : '#e2e8f0' }} !important;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span class="fw-bold text-dark me-2">{{ $msg->user->name ?? 'User' }}</span>
                                        @if($isSenderAdmin)
                                        <span class="badge bg-success bg-opacity-20 text-success me-2">Support Staff</span>
                                        @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary me-2">Client</span>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $msg->created_at->format('M d, Y H:i') }}</small>
                                </div>
                                <p class="mb-0 text-dark" style="white-space: pre-line; line-height: 1.6;">{{ $msg->message }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Reply Form -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3" style="color: #0f172a;">Post a Reply</h6>
                <form action="{{ route('support.reply', $ticket) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <textarea name="message" class="form-control" rows="4" placeholder="Write your response..." required></textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn text-white fw-semibold px-4" style="background-color: #004225; border-radius: 8px;">
                            <i class="bi bi-send me-1"></i> Send Reply
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Ticket Meta Summary Sidebar -->
        <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
            <h6 class="fw-bold mb-3" style="color: #0f172a;">Ticket Metadata</h6>
            
            <div class="mb-3">
                <span class="small text-muted d-block">Submitted By</span>
                <span class="fw-semibold text-dark">{{ $ticket->user->name ?? 'Client' }}</span>
                <div class="small text-muted">{{ $ticket->user->email ?? '' }}</div>
            </div>

            <div class="mb-3">
                <span class="small text-muted d-block">Category</span>
                <span class="badge bg-light text-dark border">{{ $ticket->category }}</span>
            </div>

            <div class="mb-3">
                <span class="small text-muted d-block">Priority</span>
                <span class="badge bg-{{ $ticket->priority === 'Urgent' ? 'danger' : ($ticket->priority === 'High' ? 'warning' : 'secondary') }}">
                    {{ $ticket->priority }}
                </span>
            </div>

            @if($ticket->serviceRequest)
            <div class="mb-3">
                <span class="small text-muted d-block">Linked Application</span>
                <a href="{{ auth()->user()->isAdmin() ? route('admin.subscriptions') : route('client.request.details', $ticket->serviceRequest) }}" class="fw-bold text-decoration-none text-primary">
                    {{ $ticket->serviceRequest->reference_number }}
                </a>
                <div class="small text-muted">{{ $ticket->serviceRequest->service_name }}</div>
            </div>
            @endif

            <div class="mb-0">
                <span class="small text-muted d-block">Created On</span>
                <span class="small text-dark">{{ $ticket->created_at->format('F d, Y \a\t H:i') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection

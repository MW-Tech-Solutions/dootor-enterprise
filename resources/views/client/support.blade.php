@extends('layouts.dashboard')

@section('title', 'Support Tickets')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: #0f172a;">Customer Support</h4>
        <p class="text-muted small mb-0">Need help with your application or services? Create a support ticket to chat with our team.</p>
    </div>
    <button type="button" class="btn text-white fw-semibold px-4" style="background-color: #004225; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#newTicketModal">
        <i class="bi bi-plus-lg me-2"></i> New Support Ticket
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Ticket ID</th>
                        <th>Subject</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Last Updated</th>
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
                            <span class="fw-semibold text-dark">{{ $t->subject }}</span>
                            @if($t->serviceRequest)
                            <div><small class="text-muted">Ref: {{ $t->serviceRequest->reference_number }}</small></div>
                            @endif
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $t->category }}</span></td>
                        <td>
                            <span class="badge bg-{{ $t->status === 'Open' ? 'danger' : ($t->status === 'In Progress' ? 'info' : 'success') }} bg-opacity-10 text-{{ $t->status === 'Open' ? 'danger' : ($t->status === 'In Progress' ? 'info' : 'success') }}">
                                {{ $t->status }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $t->updated_at->format('M d, Y H:i') }}</td>
                        <td class="text-end pe-3">
                            <a href="{{ route('support.show', $t) }}" class="btn btn-sm text-white fw-semibold" style="background-color: #004225; border-radius: 6px;">
                                <i class="bi bi-chat-dots me-1"></i> View Discussion
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-headset fs-1 d-block mb-2 text-muted"></i>
                            You have not submitted any support tickets yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- New Support Ticket Modal -->
<div class="modal fade" id="newTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: #0f172a;">Submit Support Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('support.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Subject</label>
                        <input type="text" name="subject" class="form-control" placeholder="Brief summary of your enquiry" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Category</label>
                        <select name="category" class="form-select" required>
                            <option value="General">General Enquiry</option>
                            <option value="Payment">Payment & Billing</option>
                            <option value="Document Correction">Document Correction / Upload</option>
                            <option value="Service Status">Application Status</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Related Application (Optional)</label>
                        <select name="service_request_id" class="form-select">
                            <option value="">-- None / General --</option>
                            @foreach($requests as $req)
                            <option value="{{ $req->id }}">{{ $req->reference_number ?? ('DE-'.$req->id) }} - {{ $req->service_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Message</label>
                        <textarea name="message" class="form-control" rows="4" placeholder="Describe your question or issue in detail..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white fw-semibold px-4" style="background-color: #004225;">Submit Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

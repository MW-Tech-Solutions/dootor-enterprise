@extends('layouts.dashboard')

@section('title', 'Administrative System Audit Logs')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Administrative Audit Trail</h1>
        <p class="text-secondary small mb-0">Complete security audit record of administrative operations, price modifications, role updates, and application status changes.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3">
        <form action="{{ route('admin.audit-logs') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by staff name, action, or reference number..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary btn-sm w-100">Filter Audit Logs</button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Timestamp</th>
                        <th>User / Staff</th>
                        <th>Role</th>
                        <th>Action</th>
                        <th>Target Resource</th>
                        <th>Reference #</th>
                        <th>IP Address</th>
                        <th class="text-end pe-4">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="ps-4 text-secondary small">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="fw-semibold text-dark">{{ $log->user_name ?? 'System' }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($log->user_role ?? 'System') }}</span></td>
                            <td><span class="badge bg-success" style="background-color: #004225 !important;">{{ $log->action }}</span></td>
                            <td class="small font-monospace">{{ $log->target_type }} #{{ $log->target_id }}</td>
                            <td class="fw-bold text-success">{{ $log->reference_number ?? '-' }}</td>
                            <td class="small text-muted font-monospace">{{ $log->ip_address }}</td>
                            <td class="text-end pe-4">
                                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#logDetails{{ $log->id }}">
                                    <i class="bi bi-code-slash"></i> Inspect JSON
                                </button>
                            </td>
                        </tr>
                        <tr class="collapse" id="logDetails{{ $log->id }}">
                            <td colspan="8" class="bg-light p-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold small text-danger">Previous State (Old Values)</h6>
                                        <pre class="bg-white p-2 border rounded font-monospace text-muted" style="font-size: 11px;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold small text-success">New State (Updated Values)</h6>
                                        <pre class="bg-white p-2 border rounded font-monospace text-dark" style="font-size: 11px;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>
                                No audit log records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection

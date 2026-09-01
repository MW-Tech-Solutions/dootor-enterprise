@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES'))

@section('styles')
<style>
    .shortcut-btn {
        transition: all 0.25s ease;
        border: 1px solid #e2e8f0 !important;
        background-color: #ffffff !important;
        text-decoration: none;
    }
    .shortcut-btn:hover {
        background-color: #003b1c !important;
        border-color: #003b1c !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 59, 28, 0.15);
    }
    .shortcut-btn .shortcut-title {
        color: #1e293b !important;
        transition: color 0.25s ease;
    }
    .shortcut-btn .shortcut-desc {
        color: #64748b !important;
        transition: color 0.25s ease;
    }
    .shortcut-btn i {
        color: #64748b !important;
        transition: all 0.25s ease;
    }
    .shortcut-btn:hover .shortcut-title {
        color: #ffffff !important;
    }
    .shortcut-btn:hover .shortcut-desc {
        color: rgba(255, 255, 255, 0.8) !important;
    }
    .shortcut-btn:hover i {
        color: #d4af37 !important;
        transform: translateX(3px);
    }
</style>
@endsection

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">System Administration</h1>
    <p class="text-secondary small">Oversee clients, configure global platform rates, manage subscriptions, and configure billing options</p>
</div>

<!-- Stats row -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <span class="text-muted d-block small mb-1">Total Clients</span>
            <span class="fw-bold display-6">{{ $total_users }}</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <span class="text-muted d-block small mb-1">Active Services</span>
            <span class="fw-bold display-6 text-success">{{ $active_services }}</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <span class="text-muted d-block small mb-1">Pending Processing</span>
            <span class="fw-bold display-6 text-warning">{{ $pending_requests }}</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <span class="text-muted d-block small mb-1">Total Service Orders</span>
            <span class="fw-bold display-6 text-primary">{{ $total_requests }}</span>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Service Requests -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h5 fw-bold text-dark mb-0">Recent Service Subscriptions</h2>
                <a href="{{ route('admin.subscriptions') }}" class="btn btn-dark btn-sm rounded-pill px-3">View Subscriptions</a>
            </div>

            @if(count($recent_requests) > 0)
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-secondary small">
                                <th>Client</th>
                                <th>Service</th>
                                <th>Reference</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_requests as $request)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 11px;">
                                                {{ strtoupper(substr($request->client->first_name ?? 'C', 0, 1)) }}
                                            </div>
                                            <div>
                                                <span class="fw-semibold text-dark d-block" style="font-size: 13px;">{{ $request->client_name }}</span>
                                                <span class="text-muted small" style="font-size: 11px;">{{ $request->client_email }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="small fw-semibold text-dark">{{ $request->service_name }}</td>
                                    <td>
                                        <code class="small">#{{ sprintf('%04d', $request->id) }}</code>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match($request->status) {
                                                'Completed' => 'bg-success text-white',
                                                'Processing' => 'bg-info text-dark',
                                                'Cancelled' => 'bg-danger text-white',
                                                default => 'bg-warning text-dark'
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }} rounded-pill px-2.5 py-1" style="font-size: 10px;">{{ $request->status }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 my-auto">
                    <i class="bi bi-journal-x fs-1 text-muted"></i>
                    <p class="text-secondary mt-2 small">No service requests yet.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Configurations info -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white h-100">
            <h2 class="h5 fw-bold text-dark mb-4">Quick Shortcuts</h2>
            
            <div class="d-grid gap-2 mb-4">
                <a href="{{ route('admin.subscriptions') }}" class="btn shortcut-btn text-start p-3 rounded-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold d-block shortcut-title small">Client Subscriptions</span>
                        <span class="shortcut-desc extra-small">Search clients and manage processing statuses</span>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>

                <a href="{{ route('admin.services') }}" class="btn shortcut-btn text-start p-3 rounded-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold d-block shortcut-title small">Configure Platform Services</span>
                        <span class="shortcut-desc extra-small">Add services like passport or visa vetting</span>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>
                
                <a href="{{ route('admin.users') }}" class="btn shortcut-btn text-start p-3 rounded-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold d-block shortcut-title small">User Management Logs</span>
                        <span class="shortcut-desc extra-small">Manage roles and toggle user accounts access</span>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>

                <a href="{{ route('admin.settings') }}" class="btn shortcut-btn text-start p-3 rounded-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold d-block shortcut-title small">Global Systems Setup</span>
                        <span class="shortcut-desc extra-small">Manage branding, API gateways, and mail SMTP</span>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES'))

@section('styles')
<style>
    .stat-card-gradient {
        background: linear-gradient(135deg, #004225 0%, #006637 100%);
        color: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    .stat-card-gradient::after {
        content: '';
        position: absolute;
        top: -30%;
        right: -20%;
        width: 160px;
        height: 160px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
        pointer-events: none;
    }
    .stat-card-gradient:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 66, 37, 0.3) !important;
    }
    .stat-card-white {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card-white:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08) !important;
    }
    .icon-box-emerald {
        background-color: rgba(16, 185, 129, 0.12);
        color: #059669;
    }
    .icon-box-amber {
        background-color: rgba(245, 158, 11, 0.12);
        color: #d97706;
    }
    .icon-box-blue {
        background-color: rgba(59, 130, 246, 0.12);
        color: #2563eb;
    }
    .shortcut-btn {
        transition: all 0.25s ease;
        border: 1px solid #e2e8f0 !important;
        background-color: #ffffff !important;
        text-decoration: none;
    }
    .shortcut-btn:hover {
        background-color: #004225 !important;
        border-color: #004225 !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 66, 37, 0.18);
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
    <p class="text-secondary small">Oversee clients, review application processing stages, and manage system operations</p>
</div>

<!-- High Contrast Premium Stats Row -->
<div class="row g-4 mb-4">
    <!-- Card 1: Total Registered Clients -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 rounded-4 stat-card-gradient">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="small fw-bold text-white text-uppercase tracking-wider" style="font-size: 11px; opacity: 0.95;">Total Clients</span>
                <div class="rounded-circle p-2 bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-people-fill fs-5" style="color: #004225;"></i>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <span class="display-6 fw-bold text-white">{{ number_format($total_users) }}</span>
                <span class="badge bg-white fw-bold rounded-pill px-3 py-1.5 shadow-sm" style="color: #004225 !important; font-size: 11px;">Active Base</span>
            </div>
        </div>
    </div>

    <!-- Card 2: Active Services Catalog -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 rounded-4 stat-card-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="small fw-bold text-secondary text-uppercase tracking-wider" style="font-size: 11px;">Active Services</span>
                <div class="rounded-circle p-2 icon-box-emerald d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-gear-wide-connected fs-5"></i>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <span class="display-6 fw-bold text-dark">{{ number_format($active_services) }}</span>
                <span class="badge bg-success bg-opacity-10 text-success fw-bold rounded-pill px-3 py-1.5" style="font-size: 11px;"><i class="bi bi-check-circle-fill me-1"></i> Live</span>
            </div>
        </div>
    </div>

    <!-- Card 3: Pending Applications -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 rounded-4 stat-card-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="small fw-bold text-secondary text-uppercase tracking-wider" style="font-size: 11px;">Pending Processing</span>
                <div class="rounded-circle p-2 icon-box-amber d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-hourglass-split fs-5"></i>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <span class="display-6 fw-bold text-dark">{{ number_format($pending_requests) }}</span>
                <span class="badge bg-warning bg-opacity-10 text-warning fw-bold rounded-pill px-3 py-1.5" style="font-size: 11px;"><i class="bi bi-clock-history me-1"></i> Action Req.</span>
            </div>
        </div>
    </div>

    <!-- Card 4: Total Application Orders -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 rounded-4 stat-card-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="small fw-bold text-secondary text-uppercase tracking-wider" style="font-size: 11px;">Total Applications</span>
                <div class="rounded-circle p-2 icon-box-blue d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-journal-check fs-5"></i>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <span class="display-6 fw-bold text-dark">{{ number_format($total_requests) }}</span>
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold rounded-pill px-3 py-1.5" style="font-size: 11px;">Volume</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Recent Service Requests -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h5 fw-bold text-dark mb-0">Recent Applications</h2>
                <a href="{{ route('admin.subscriptions') }}" class="btn text-white btn-sm rounded-pill px-3 fw-semibold" style="background-color: #004225;">Manage Applications</a>
            </div>

            @if(count($recent_requests) > 0)
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-secondary small">
                                <th>Client Name</th>
                                <th>Service Requested</th>
                                <th>Reference</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_requests as $request)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 11px; background-color: #004225;">
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
                                        <span class="font-monospace small fw-bold text-primary">{{ $request->reference_number ?? ('DE-' . $request->id) }}</span>
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

    <!-- Quick Shortcuts -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white h-100">
            <h2 class="h5 fw-bold text-dark mb-4">Quick Actions</h2>
            
            <div class="d-grid gap-2 mb-2">
                <a href="{{ route('admin.subscriptions') }}" class="btn shortcut-btn text-start p-3 rounded-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold d-block shortcut-title small">Client Subscriptions</span>
                        <span class="shortcut-desc extra-small">Search clients and manage processing stages</span>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>

                <a href="{{ route('support.index') }}" class="btn shortcut-btn text-start p-3 rounded-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold d-block shortcut-title small">Support Desk</span>
                        <span class="shortcut-desc extra-small">Respond to client tickets & inquiries</span>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>

                <a href="{{ route('admin.reports') }}" class="btn shortcut-btn text-start p-3 rounded-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold d-block shortcut-title small">Reports & Analytics</span>
                        <span class="shortcut-desc extra-small">Export revenue CSV and review metrics</span>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>

                <a href="{{ route('admin.settings') }}" class="btn shortcut-btn text-start p-3 rounded-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold d-block shortcut-title small">White-Label Branding</span>
                        <span class="shortcut-desc extra-small">Upload logo, themes, and Credo keys</span>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

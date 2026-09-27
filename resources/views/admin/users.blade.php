@extends('layouts.dashboard')

@section('title', 'User Management - ' . ($settings->platform_name ?? 'Dooter Enterprises'))

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">User Management Logs</h1>
    <p class="text-secondary small">Filter profiles, modify roles/access status, and delete user records</p>
</div>

<div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
    <!-- Filter Bar -->
    <form action="{{ route('admin.users') }}" method="GET" class="row g-3 mb-4 align-items-end">
        <div class="col-sm-4">
            <label for="filter-role" class="form-label small fw-medium text-secondary">Filter by Role</label>
            <select name="role" id="filter-role" class="form-select rounded-3">
                <option value="">All Roles</option>
                <optgroup label="System Base Roles">
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin (All Staff)</option>
                    <option value="vendor" {{ request('role') === 'vendor' ? 'selected' : '' }}>Vendor</option>
                    <option value="client" {{ request('role') === 'client' ? 'selected' : '' }}>Client</option>
                </optgroup>
                @if(isset($roles) && $roles->count() > 0)
                <optgroup label="RBAC Roles (From Roles Page)">
                    @foreach($roles as $r)
                        <option value="{{ $r->slug }}" {{ request('role') === $r->slug || request('role') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                </optgroup>
                @endif
            </select>
        </div>
        <div class="col-sm-4">
            <label for="filter-status" class="form-label small fw-medium text-secondary">Filter by Status</label>
            <select name="status" id="filter-status" class="form-select rounded-3">
                <option value="">All Statuses</option>
                <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="Disabled" {{ request('status') === 'Disabled' ? 'selected' : '' }}>Disabled</option>
            </select>
        </div>
        <div class="col-sm-4 d-flex gap-2">
            <button type="submit" class="btn btn-dark rounded-pill px-4 flex-grow-1">Filter</button>
            <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary rounded-pill px-4">Reset</a>
        </div>
    </form>

    <!-- Users table -->
    @if(count($users) > 0)
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr class="text-secondary small">
                        <th>User</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($user->avatar_url)
                                        <img src="{{ app_file_url($user->avatar_url) }}" alt="avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 12px;">
                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <span class="fw-semibold text-dark d-block">{{ $user->first_name }} {{ $user->last_name }}</span>
                                        @if($user->staff_file_number)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace" style="font-size: 10px; color: #004225 !important; background-color: #e6f4ea !important;">
                                                <i class="bi bi-person-badge me-1"></i>{{ $user->staff_file_number }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? 'N/A' }}</td>
                            <td>
                                <div class="d-flex flex-wrap align-items-center gap-1">
                                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2.5 py-1 text-uppercase small fw-semibold">{{ $user->role }}</span>
                                    @foreach($user->roles as $assignedRole)
                                        <span class="badge bg-success-subtle border border-success-subtle rounded-pill px-2.5 py-1 small fw-semibold" style="color: #004225 !important; background-color: #e6f4ea !important;">
                                            <i class="bi bi-shield-check me-1"></i>{{ $assignedRole->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusBadge = match($user->status) {
                                        'Approved' => 'bg-success text-white',
                                        'Pending' => 'bg-warning text-dark',
                                        'Rejected' => 'bg-danger text-white',
                                        'Disabled' => 'bg-secondary text-white',
                                        default => 'bg-light text-dark'
                                    };
                                @endphp
                                <span class="badge {{ $statusBadge }} rounded-pill px-2.5 py-1">{{ $user->status }}</span>
                            </td>
                            <td class="small text-secondary">{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if($user->role !== 'client')
                                        <button class="btn btn-outline-success btn-sm rounded-pill px-2.5" type="button" data-bs-toggle="modal" data-bs-target="#staffPerfModal-{{ $user->id }}" style="font-size: 11px; color: #004225; border-color: #004225;">
                                            <i class="bi bi-graph-up me-1"></i> History
                                        </button>
                                    @endif
                                    <!-- Modify Button Trigger -->
                                    <button class="btn btn-outline-dark btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#editUserModal-{{ $user->id }}">Modify</button>
                                    
                                    <!-- Delete form -->
                                    <form action="{{ route('admin.user.delete', $user->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this user account permanent?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Staff Performance Modals -->
        @foreach($users as $user)
            @if($user->role !== 'client')
                <div class="modal fade" id="staffPerfModal-{{ $user->id }}" tabindex="-1" aria-labelledby="staffPerfModalLabel-{{ $user->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow-lg rounded-4">
                            <div class="modal-header text-white py-3 px-4 border-bottom" style="background-color: #004225;">
                                <div>
                                    <h5 class="modal-title fw-bold text-white fs-6" id="staffPerfModalLabel-{{ $user->id }}">
                                        <i class="bi bi-person-badge-fill me-2"></i> Staff Performance &amp; Work History
                                    </h5>
                                    <span class="badge bg-white bg-opacity-20 text-white font-monospace small">
                                        Staff File #: {{ $user->staff_file_number ?? 'DE/STF/2026/000001' }}
                                    </span>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3 border">
                                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 48px; height: 48px; background-color: #004225;">
                                        {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0 fs-5">{{ $user->name }}</h6>
                                        <span class="text-secondary small">{{ $user->email }} | {{ ucfirst($user->role) }}</span>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded-3 bg-white text-center">
                                            <span class="text-muted small fw-medium d-block mb-1">Assigned Applications</span>
                                            <h4 class="fw-bold text-dark mb-0">{{ $user->assigned_count ?? 0 }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded-3 bg-white text-center">
                                            <span class="text-success small fw-medium d-block mb-1">Completed Applications</span>
                                            <h4 class="fw-bold text-success mb-0" style="color: #004225 !important;">{{ $user->completed_count ?? 0 }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded-3 bg-white text-center">
                                            <span class="text-warning small fw-medium d-block mb-1">Pending Processing</span>
                                            <h4 class="fw-bold text-warning mb-0">{{ $user->pending_count ?? 0 }}</h4>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-1"></i> Recent Staff Activity Audit Logs</h6>
                                @if(isset($user->recent_activity) && $user->recent_activity->count() > 0)
                                    <div class="list-group list-group-flush border rounded-3">
                                        @foreach($user->recent_activity as $act)
                                            <div class="list-group-item p-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-bold text-dark small">{{ ucwords(str_replace('_', ' ', $act->action)) }}</span>
                                                    <span class="text-muted font-monospace" style="font-size: 11px;">{{ $act->created_at->format('M d, Y H:i:s') }}</span>
                                                </div>
                                                <div class="small text-secondary">
                                                    Target: {{ $act->target_type }} #{{ $act->target_id }} @if($act->reference_number) (Ref: {{ $act->reference_number }}) @endif
                                                </div>
                                                <div class="small text-muted font-monospace" style="font-size: 10px;">
                                                    IP: {{ $act->ip_address }} | Staff File #: {{ $user->staff_file_number }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-3 bg-light text-center text-muted small rounded-3">No activity logs recorded for this staff account yet.</div>
                                @endif
                            </div>
                            <div class="modal-footer border-top bg-light">
                                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        <!-- Edit User Modals (Placed outside table container to prevent stacking context bugs) -->
        @foreach($users as $user)
            <div class="modal fade" id="editUserModal-{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel-{{ $user->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header border-bottom">
                            <h5 class="modal-title fw-bold" id="editUserModalLabel-{{ $user->id }}">Modify User Settings</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.user.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="first_name-{{ $user->id }}" class="form-label small fw-medium">First Name</label>
                                    <input type="text" name="first_name" id="first_name-{{ $user->id }}" class="form-control rounded-3" value="{{ old('first_name', $user->first_name) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="last_name-{{ $user->id }}" class="form-label small fw-medium">Last Name</label>
                                    <input type="text" name="last_name" id="last_name-{{ $user->id }}" class="form-control rounded-3" value="{{ old('last_name', $user->last_name) }}" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-sm-6">
                                        <label for="country-{{ $user->id }}" class="form-label small fw-medium">Country</label>
                                        <select name="country" id="country-{{ $user->id }}" class="form-select african-country-select rounded-3" data-selected="{{ old('country', $user->country ?? 'Nigeria') }}" data-division-target="state-{{ $user->id }}" data-label-target="state_label-{{ $user->id }}">
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="state-{{ $user->id }}" id="state_label-{{ $user->id }}" class="form-label small fw-medium african-division-label">State / Region</label>
                                        <select name="state" id="state-{{ $user->id }}" class="form-select african-division-select rounded-3" data-selected="{{ old('state', $user->state) }}">
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="role-{{ $user->id }}" class="form-label small fw-medium">System &amp; RBAC Assigned Role</label>
                                    @php
                                        $userAssignedRoleIds = $user->roles->pluck('id')->toArray();
                                    @endphp
                                    <select name="role" id="role-{{ $user->id }}" class="form-select rounded-3 fw-medium">
                                        <optgroup label="System Base Roles">
                                            <option value="admin" {{ $user->role === 'admin' && empty($userAssignedRoleIds) ? 'selected' : '' }}>Admin (Default Admin)</option>
                                            <option value="vendor" {{ $user->role === 'vendor' ? 'selected' : '' }}>Vendor</option>
                                            <option value="client" {{ $user->role === 'client' ? 'selected' : '' }}>Client</option>
                                        </optgroup>
                                        @if(isset($roles) && $roles->count() > 0)
                                        <optgroup label="RBAC Roles (From Roles Page)">
                                            @foreach($roles as $r)
                                                <option value="{{ $r->id }}" {{ in_array($r->id, $userAssignedRoleIds) || ($user->role === $r->slug) ? 'selected' : '' }}>
                                                    {{ $r->name }} @if($r->description) ({{ Str::limit($r->description, 35) }}) @endif
                                                </option>
                                            @endforeach
                                        </optgroup>
                                        @endif
                                    </select>
                                    <small class="text-muted d-block mt-1">Assigning an RBAC role grants all granular action permissions configured on the RBAC Roles Page.</small>
                                </div>
                                <div class="mb-3">
                                    <label for="status-{{ $user->id }}" class="form-label small fw-medium">Access Status</label>
                                    <select name="status" id="status-{{ $user->id }}" class="form-select rounded-3">
                                        <option value="Approved" {{ $user->status === 'Approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="Pending" {{ $user->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Rejected" {{ $user->status === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="Disabled" {{ $user->status === 'Disabled' ? 'selected' : '' }}>Disabled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer border-top">
                                <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-dark rounded-pill px-4">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="d-flex justify-content-center mt-4">
            {{ $users->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-people fs-1 text-muted"></i>
            <p class="text-secondary mt-2 small">No users found matching the filter parameters.</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/location-loader.js') }}"></script>
@endpush

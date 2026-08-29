@extends('layouts.dashboard')

@section('title', 'User Management - ' . ($settings->platform_name ?? 'Umar Maher'))

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
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="vendor" {{ request('role') === 'vendor' ? 'selected' : '' }}>Vendor</option>
                <option value="client" {{ request('role') === 'client' ? 'selected' : '' }}>Client</option>
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
                                        <img src="{{ $user->avatar_url }}" alt="avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 12px;">
                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="fw-semibold text-dark">{{ $user->first_name }} {{ $user->last_name }}</span>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? 'N/A' }}</td>
                            <td>
                                <span class="text-uppercase small fw-semibold text-secondary">{{ $user->role }}</span>
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

                        <!-- Edit User Modal -->
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
                                            <div class="mb-3">
                                                <label for="role-{{ $user->id }}" class="form-label small fw-medium">System Role</label>
                                                <select name="role" id="role-{{ $user->id }}" class="form-select rounded-3">
                                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                    <option value="vendor" {{ $user->role === 'vendor' ? 'selected' : '' }}>Vendor</option>
                                                    <option value="client" {{ $user->role === 'client' ? 'selected' : '' }}>Client</option>
                                                </select>
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
                </tbody>
            </table>
        </div>

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

@extends('layouts.dashboard')

@section('title', 'Role-Based Access Control (RBAC) & Permissions')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Role-Based Access Control (RBAC)</h1>
        <p class="text-secondary small mb-0">Configure modular administrative roles, granular action permissions, and staff assignments.</p>
    </div>
    <button class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#createRoleModal" style="background-color: #004225; border: none;">
        <i class="bi bi-shield-plus me-1"></i> Create New Role
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Roles Overview Grid -->
<div class="row g-4 mb-5">
    @foreach($roles as $role)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 position-relative">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge {{ $role->slug === 'super-admin' ? 'bg-danger' : 'bg-success' }} text-white px-2.5 py-1" style="{{ $role->slug !== 'super-admin' ? 'background-color: #004225 !important;' : '' }}">
                            {{ $role->name }}
                        </span>
                        <span class="text-secondary small">{{ $role->users->count() }} Staff Assigned</span>
                    </div>
                    <p class="text-muted small mb-3 flex-grow-1">{{ $role->description ?? 'No description set.' }}</p>
                    
                    <div class="border-top pt-3 mt-auto d-flex align-items-center justify-content-between">
                        <span class="small fw-semibold text-secondary">
                            <i class="bi bi-key me-1"></i>{{ $role->slug === 'super-admin' ? 'All System Permissions' : $role->permissions->count() . ' Granular Permissions' }}
                        </span>
                        <div class="btn-group">
                            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editRoleModal{{ $role->id }}">
                                <i class="bi bi-gear me-1"></i> Edit
                            </button>
                            @if($role->slug !== 'super-admin')
                                <form action="{{ route('admin.roles.delete', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm" type="submit">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Role Modal -->
        <div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Edit Role: {{ $role->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Role Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Description</label>
                                <textarea name="description" class="form-control" rows="2">{{ $role->description }}</textarea>
                            </div>
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="act{{ $role->id }}" {{ $role->is_active ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold small" for="act{{ $role->id }}">Role Active</label>
                            </div>

                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Assigned Granular Permissions</h6>
                            @foreach($permissions as $module => $modulePerms)
                                <div class="mb-3">
                                    <span class="d-block fw-bold text-uppercase small text-success mb-2">{{ ucfirst($module) }}</span>
                                    <div class="row g-2">
                                        @foreach($modulePerms as $perm)
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="rp_{{ $role->id }}_{{ $perm->id }}" {{ $role->permissions->contains($perm->id) ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="rp_{{ $role->id }}_{{ $perm->id }}">
                                                        <strong>{{ $perm->name }}</strong>
                                                        <span class="d-block text-muted" style="font-size: 11px;">{{ $perm->slug }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success" style="background-color: #004225; border: none;">Save Role Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
</div>

<!-- Direct User Permission Assignment Section (Requirement 16) -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3">
        <h5 class="card-title fw-bold text-dark mb-0 fs-6">Direct Staff Permissions Override (Requirement 16)</h5>
        <p class="text-secondary small mb-0">Assign specific task permissions directly to individual staff members alongside their roles.</p>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Staff Member</th>
                        <th>Email</th>
                        <th>Current System Role</th>
                        <th>Assigned RBAC Roles</th>
                        <th>Direct Permissions</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allUsers->take(15) as $staff)
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">{{ $staff->name }}</td>
                            <td class="small text-secondary">{{ $staff->email }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($staff->role) }}</span></td>
                            <td>
                                @forelse($staff->roles as $r)
                                    <span class="badge bg-success text-white me-1" style="background-color: #004225 !important;">{{ $r->name }}</span>
                                @empty
                                    <span class="text-muted small">None</span>
                                @endforelse
                            </td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $staff->directPermissions->count() }} Direct Overrides</span>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#userPermModal{{ $staff->id }}">
                                    <i class="bi bi-person-gear me-1"></i> Configure Permissions
                                </button>
                            </td>
                        </tr>

                        <!-- Modal for Staff Direct Permission Assignment -->
                        <div class="modal fade" id="userPermModal{{ $staff->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <form action="{{ route('admin.user.permissions', $staff) }}" method="POST">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Configure Permissions: {{ $staff->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 text-start">
                                            <h6 class="fw-bold text-dark mb-2">1. Assign Roles</h6>
                                            <div class="row g-2 mb-4">
                                                @foreach($roles as $r)
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $r->id }}" id="ur_{{ $staff->id }}_{{ $r->id }}" {{ $staff->roles->contains($r->id) ? 'checked' : '' }}>
                                                            <label class="form-check-label small fw-semibold" for="ur_{{ $staff->id }}_{{ $r->id }}">{{ $r->name }}</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <h6 class="fw-bold text-dark border-top pt-3 mb-2">2. Direct Specific Action Permissions (Overrides)</h6>
                                            <p class="text-muted small">Select individual permissions granted directly to this user regardless of role:</p>
                                            @foreach($permissions as $module => $modulePerms)
                                                <div class="mb-3">
                                                    <span class="d-block fw-bold text-uppercase small text-success mb-1">{{ ucfirst($module) }}</span>
                                                    <div class="row g-2">
                                                        @foreach($modulePerms as $perm)
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" name="direct_permissions[]" value="{{ $perm->id }}" id="up_{{ $staff->id }}_{{ $perm->id }}" {{ $staff->directPermissions->contains($perm->id) ? 'checked' : '' }}>
                                                                    <label class="form-check-label small" for="up_{{ $staff->id }}_{{ $perm->id }}">
                                                                        {{ $perm->name }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success" style="background-color: #004225; border: none;">Save User Permissions</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Create New Role -->
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Create New Administrative Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Role Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Document Verification Officer" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Brief summary of duties and responsibilities..."></textarea>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Select Initial Permissions</h6>
                    @foreach($permissions as $module => $modulePerms)
                        <div class="mb-3">
                            <span class="d-block fw-bold text-uppercase small text-success mb-2">{{ ucfirst($module) }}</span>
                            <div class="row g-2">
                                @foreach($modulePerms as $perm)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="nrp_{{ $perm->id }}">
                                            <label class="form-check-label small" for="nrp_{{ $perm->id }}">
                                                <strong>{{ $perm->name }}</strong>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" style="background-color: #004225; border: none;">Create Role</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

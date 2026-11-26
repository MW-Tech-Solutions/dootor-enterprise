@extends('layouts.dashboard')

@section('title', 'Quick Client & Service Request — DOOTOR ENTERPRISES')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="h4 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-person-plus-fill text-success"></i>
                <span>Create Client &amp; Request Service (Admin Shortcut)</span>
            </h2>
            <p class="text-secondary small mb-0">Quickly register a new client or select an existing client and submit a service application on their behalf.</p>
        </div>
        <a href="{{ route('admin.subscriptions') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-2 fw-semibold">
            <i class="bi bi-list-check me-1"></i> View All Applications
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 small d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>{!! session('success') !!}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 small">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.proxy-booking.store') }}" method="POST" class="row g-4">
        @csrf

        <!-- Left Column: Client Selection / Creation -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h5 class="h6 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <span class="badge rounded-circle bg-success text-white" style="width:24px;height:24px;line-height:16px;">1</span>
                    <span>Client Account</span>
                </h5>

                <!-- Mode Toggle -->
                <div class="btn-group w-100 mb-4" role="group">
                    <input type="radio" class="btn-check" name="client_mode" id="modeNew" value="new" checked onchange="toggleClientMode()">
                    <label class="btn btn-outline-success fw-semibold rounded-start-pill" for="modeNew">
                        <i class="bi bi-person-plus me-1"></i> Create New Client
                    </label>
                    <input type="radio" class="btn-check" name="client_mode" id="modeExisting" value="existing" onchange="toggleClientMode()">
                    <label class="btn btn-outline-success fw-semibold rounded-end-pill" for="modeExisting">
                        <i class="bi bi-person-check me-1"></i> Select Existing Client
                    </label>
                </div>

                <!-- New Client Fields -->
                <div id="newClientBox">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" id="newFirstName" class="form-control rounded-3" value="{{ old('first_name') }}" placeholder="e.g. John">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" id="newLastName" class="form-control rounded-3" value="{{ old('last_name') }}" placeholder="e.g. Doe">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="newEmail" class="form-control rounded-3" value="{{ old('email') }}" placeholder="client@example.com">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold">Phone / WhatsApp</label>
                            <input type="text" name="phone" class="form-control rounded-3" value="{{ old('phone') }}" placeholder="+1 416 ...">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold">Temporary Password</label>
                            <input type="text" name="password" class="form-control rounded-3" value="{{ old('password', 'Dootor@' . rand(1000, 9999)) }}" placeholder="Min 8 characters">
                            <small class="text-muted" style="font-size:11px;">Client can log in immediately (auto-verified).</small>
                        </div>
                    </div>
                </div>

                <!-- Existing Client Select -->
                <div id="existingClientBox" class="d-none">
                    <label class="form-label small fw-semibold">Choose Registered Client <span class="text-danger">*</span></label>
                    <select name="existing_client_id" id="existingClientId" class="form-select rounded-3">
                        <option value="">— Select Client —</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ old('existing_client_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Right Column: Service & Assignment -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h5 class="h6 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <span class="badge rounded-circle bg-success text-white" style="width:24px;height:24px;line-height:16px;">2</span>
                    <span>Service Request Details</span>
                </h5>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Primary Service <span class="text-danger">*</span></label>
                        <select name="service_id" id="primaryServiceSelect" class="form-select rounded-3" required onchange="loadSubServices()">
                            <option value="">— Select Primary Service —</option>
                            @foreach($services as $svc)
                                <option value="{{ $svc->id }}" data-subs='@json($svc->subServices)' data-price="{{ $svc->price }}">
                                    {{ $svc->name }} ({{ $svc->subServices->count() }} options)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Sub-Service / Specific Option</label>
                        <select name="sub_service_id" id="subServiceSelect" class="form-select rounded-3">
                            <option value="">— Select Primary Service First (Optional) —</option>
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold">Country Applying From</label>
                        <input type="text" name="country_applying_from" class="form-control rounded-3" value="{{ old('country_applying_from', 'Canada') }}" placeholder="e.g. Canada, UK, USA">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold">Destination / Service Country</label>
                        <input type="text" name="country_service_requested" class="form-control rounded-3" value="{{ old('country_service_requested', 'International') }}" placeholder="e.g. Global / Consular">
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold">Payment Status</label>
                        <select name="payment_status" class="form-select rounded-3">
                            <option value="Paid">Paid (Cash / Offline / Verified)</option>
                            <option value="Unpaid">Unpaid (Client will pay online)</option>
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold">Assign To Staff (Optional)</label>
                        <select name="assigned_staff_id" class="form-select rounded-3">
                            <option value="">— Unassigned (Queue) —</option>
                            @foreach($staffMembers as $st)
                                <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->roles->pluck('name')->implode(', ') ?: 'Admin' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Admin Notes / Client Instructions</label>
                        <textarea name="notes" rows="2" class="form-control rounded-3" placeholder="Any walk-in notes, urgent deadline, or special instructions...">{{ old('notes') }}</textarea>
                    </div>

                    <div class="col-12 pt-2">
                        <button type="submit" class="btn btn-dark w-100 rounded-pill py-2.5 fw-bold shadow-sm" style="background:#004225;border-color:#004225;">
                            <i class="bi bi-send-check me-2"></i> Create Account &amp; Submit Service Request
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
function toggleClientMode() {
    const isNew = document.getElementById('modeNew').checked;
    document.getElementById('newClientBox').classList.toggle('d-none', !isNew);
    document.getElementById('existingClientBox').classList.toggle('d-none', isNew);
    document.getElementById('newFirstName').required = isNew;
    document.getElementById('newLastName').required = isNew;
    document.getElementById('newEmail').required = isNew;
    document.getElementById('existingClientId').required = !isNew;
}

function loadSubServices() {
    const sel = document.getElementById('primaryServiceSelect');
    const subSel = document.getElementById('subServiceSelect');
    const opt = sel.options[sel.selectedIndex];
    subSel.innerHTML = '<option value="">— Primary Service Only / No Sub-Service —</option>';
    if (!opt || !opt.value) return;
    try {
        const subs = JSON.parse(opt.getAttribute('data-subs') || '[]');
        subs.forEach(s => {
            const o = document.createElement('option');
            o.value = s.id;
            const p = s.price && parseFloat(s.price) > 0 ? ' ($' + parseFloat(s.price).toFixed(2) + ')' : '';
            o.textContent = s.name + p;
            subSel.appendChild(o);
        });
    } catch (e) {}
}

document.addEventListener('DOMContentLoaded', toggleClientMode);
</script>
@endsection

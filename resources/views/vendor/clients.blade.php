@extends('layouts.dashboard')

@section('title', 'Clients - ' . ($settings->platform_name ?? 'Umar Maher'))

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">Client Management</h1>
    <p class="text-secondary small">View and manage clients who have booked your storefront services</p>
</div>

<div class="row g-4">
    <!-- Active Clients -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <h2 class="h5 fw-bold text-dark mb-4">Active Clients</h2>

            @if(count($clients) > 0)
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-secondary small">
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Location</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clients as $client)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($client->avatar_url)
                                                <img src="{{ $client->avatar_url }}" alt="avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 12px;">
                                                    {{ strtoupper(substr($client->first_name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <span class="fw-semibold text-dark">{{ $client->first_name }} {{ $client->last_name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $client->email }}</td>
                                    <td>{{ $client->phone ?? 'Not Provided' }}</td>
                                    <td class="small text-secondary">{{ $client->state ?? '' }}, {{ $client->country ?? '' }}</td>
                                    <td>
                                        <form action="{{ route('vendor.client.archive', $client->id) }}" method="POST" class="m-0" onsubmit="return confirm('Archive this client folder?');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill small px-3">Archive</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-people fs-1 text-muted"></i>
                    <p class="text-secondary mt-2 small">No active clients found.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Archived Clients -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <h2 class="h6 fw-bold text-dark mb-3">Archived Clients Directory</h2>

            @if(count($archivedClients) > 0)
                <div class="list-group list-group-flush">
                    @foreach($archivedClients as $archived)
                        <div class="list-group-item px-0 py-2.5">
                            <span class="fw-semibold text-dark d-block small">{{ $archived->client_name }}</span>
                            <span class="text-secondary small">{{ $archived->client_email }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 bg-light rounded-3 small text-secondary">
                    No archived client history.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

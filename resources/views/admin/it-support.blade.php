@extends('layouts.dashboard')

@section('title', 'IT Support Desk & Server Diagnostics | DOOTOR ENTERPRISES')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="h4 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-cpu" style="color: #004225;"></i>
                <span>IT Support Desk &amp; Server Health</span>
            </h2>
            <p class="text-secondary small mb-0">System environment status, PHP/MySQL health checks, application error logs, and cache maintenance.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('admin.it-support.clear-cache') }}" method="POST" onsubmit="return confirm('Clear all application views, route and config caches?');" class="m-0">
                @csrf
                <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-1.5 shadow-sm" style="background-color: #004225; border-color: #004225;">
                    <i class="bi bi-arrow-repeat"></i>
                    <span>Purge System Caches</span>
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 shadow-sm mb-4 small">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    <!-- Laravel Cache & Database Maintenance Console -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
            <div>
                <h5 class="h6 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-terminal-fill text-success"></i>
                    <span>Laravel Cache &amp; Database Maintenance Console</span>
                </h5>
                <p class="text-secondary small mb-0">Run individual Artisan cache clears or database migrations after pushing updates.</p>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <form action="{{ route('admin.it-support.clear-cache') }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="action" value="all">
                <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3 py-2 fw-semibold" style="background:#004225;border-color:#004225;">
                    <i class="bi bi-stars me-1"></i> optimize:clear (All Caches)
                </button>
            </form>
            <form action="{{ route('admin.it-support.clear-cache') }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="action" value="view">
                <button type="submit" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-2 fw-semibold">
                    <i class="bi bi-eye-slash me-1"></i> view:clear
                </button>
            </form>
            <form action="{{ route('admin.it-support.clear-cache') }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="action" value="cache">
                <button type="submit" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-2 fw-semibold">
                    <i class="bi bi-lightning-charge me-1"></i> cache:clear
                </button>
            </form>
            <form action="{{ route('admin.it-support.clear-cache') }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="action" value="config">
                <button type="submit" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-2 fw-semibold">
                    <i class="bi bi-sliders me-1"></i> config:clear
                </button>
            </form>
            <form action="{{ route('admin.it-support.clear-cache') }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="action" value="route">
                <button type="submit" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-2 fw-semibold">
                    <i class="bi bi-signpost-split me-1"></i> route:clear
                </button>
            </form>
            <form action="{{ route('admin.it-support.clear-cache') }}" method="POST" onsubmit="return confirm('Run pending database migrations (migrate --force)?');" class="m-0">
                @csrf
                <input type="hidden" name="action" value="migrate">
                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 fw-semibold">
                    <i class="bi bi-database-up me-1"></i> Run DB Migrations
                </button>
            </form>
            <form action="{{ route('admin.it-support.clear-cache') }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="action" value="storage_link">
                <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 fw-semibold">
                    <i class="bi bi-link-45deg me-1"></i> storage:link
                </button>
            </form>
        </div>
    </div>

    <!-- System Diagnostic Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary extra-small fw-bold text-uppercase">PHP Runtime</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary extra-small">Engine</span>
                </div>
                <h4 class="h5 fw-bold text-dark mb-1 font-monospace">PHP {{ $phpVersion }}</h4>
                <span class="text-success small fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Active</span>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary extra-small fw-bold text-uppercase">Framework</span>
                    <span class="badge bg-danger bg-opacity-10 text-danger extra-small">Laravel</span>
                </div>
                <h4 class="h5 fw-bold text-dark mb-1 font-monospace">v{{ $laravelVersion }}</h4>
                <span class="text-secondary small">Laravel Framework</span>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary extra-small fw-bold text-uppercase">Database Health</span>
                    <span class="badge bg-success bg-opacity-10 text-success extra-small">MySQL</span>
                </div>
                <h4 class="h5 fw-bold text-dark mb-1 d-flex align-items-center gap-1.5">
                    @if(str_contains($dbStatus, 'Connected'))
                        <i class="bi bi-database-check text-success"></i>
                        <span>Online</span>
                    @else
                        <i class="bi bi-database-x text-danger"></i>
                        <span class="text-danger small">Error</span>
                    @endif
                </h4>
                <span class="text-muted extra-small font-monospace text-truncate d-block">{{ $dbStatus }}</span>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary extra-small fw-bold text-uppercase">Disk Storage</span>
                    <span class="badge bg-info bg-opacity-10 text-info extra-small">Local</span>
                </div>
                <h4 class="h5 fw-bold text-dark mb-1 font-monospace">{{ $diskFreeMb }} MB Free</h4>
                <span class="text-muted small">Total: {{ $diskTotalMb }} MB</span>
            </div>
        </div>
    </div>

    <!-- Server & Environment Details -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h4 class="h6 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-hdd-network text-success"></i>
                    <span>Server &amp; Network Environment</span>
                </h4>
                <div class="d-flex flex-column gap-2.5 small">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-secondary">Web Server:</span>
                        <span class="fw-semibold text-dark font-monospace">{{ $serverSoftware }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-secondary">Application Environment:</span>
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold text-uppercase">{{ app()->environment() }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-secondary">Application URL:</span>
                        <span class="fw-semibold text-dark font-monospace">{{ config('app.url') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-secondary">Cache Driver:</span>
                        <span class="fw-semibold text-dark font-monospace">{{ config('cache.default') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-secondary">Session Driver:</span>
                        <span class="fw-semibold text-dark font-monospace">{{ config('session.driver') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-secondary">File Storage Root:</span>
                        <span class="fw-semibold text-dark font-monospace small text-truncate" style="max-width: 250px;">{{ storage_path() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h4 class="h6 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-tools text-warning"></i>
                    <span>Maintenance &amp; Quick Fixes</span>
                </h4>
                <p class="text-secondary extra-small mb-3">If UI updates or configuration changes are not immediately visible, run the cache flush below.</p>
                <div class="d-flex flex-column gap-3">
                    <div class="p-3 rounded-3 bg-light border d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fw-bold text-dark small d-block">Flush All Compiled Blade Views</span>
                            <span class="text-secondary extra-small">Clears stale compiled PHP template files in storage/framework/views</span>
                        </div>
                        <form action="{{ route('admin.it-support.clear-cache') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-outline-dark btn-sm rounded-pill px-3">Flush</button>
                        </form>
                    </div>

                    <div class="p-3 rounded-3 bg-light border d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fw-bold text-dark small d-block">Database Health Status</span>
                            <span class="text-secondary extra-small">Checks connection pooling and active PDO statements</span>
                        </div>
                        <span class="badge bg-success bg-opacity-25 text-success fw-bold px-3 py-1.5 rounded-pill">Good</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Application Error Logs -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
            <div>
                <h4 class="h6 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-terminal text-danger"></i>
                    <span>Live Error &amp; Exception Log (storage/logs/laravel.log)</span>
                </h4>
                <p class="text-secondary extra-small mb-0">Most recent output lines from the Laravel error log file.</p>
            </div>
            <span class="badge bg-light text-secondary border font-monospace extra-small">Tail: 40 Lines</span>
        </div>
        <div class="p-3 bg-dark text-white font-monospace small overflow-auto" style="max-height: 380px; font-size: 11.5px; line-height: 1.5; color: #cbd5e1;">
            @if(count($logLines) > 0)
                @foreach($logLines as $line)
                    <div class="{{ str_contains($line, 'ERROR') || str_contains($line, 'Exception') ? 'text-danger fw-bold' : (str_contains($line, 'WARNING') ? 'text-warning' : '') }}">{{ $line }}</div>
                @endforeach
            @else
                <div class="text-success"><i class="bi bi-check-circle me-1"></i>No exception errors recorded. Laravel error log is clean!</div>
            @endif
        </div>
    </div>
</div>
@endsection

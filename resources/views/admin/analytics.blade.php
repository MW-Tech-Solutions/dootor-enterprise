@extends('layouts.dashboard')

@section('title', 'Traffic & Click Analytics | DOOTOR ENTERPRISES')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="h4 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-graph-up-arrow" style="color: #004225;"></i>
                <span>Traffic &amp; Click Analytics</span>
            </h2>
            <p class="text-secondary small mb-0">Real-time visitor logs, social media link engagements, and service CTA clicks.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-1.5">
                <span class="spinner-grow spinner-grow-sm text-success" style="width: 7px; height: 7px;"></span>
                <span>Live Telemetry Active</span>
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 shadow-sm mb-4 small">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    <!-- KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary extra-small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Total Page Views</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 38px; height: 38px; background: linear-gradient(135deg, #004225, #006435);">
                        <i class="bi bi-eye"></i>
                    </div>
                </div>
                <h3 class="h3 fw-bold text-dark mb-1">{{ number_format($totalVisits) }}</h3>
                <span class="text-success small fw-semibold"><i class="bi bi-arrow-up-right me-1"></i>Live Tracked</span>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary extra-small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Unique Visitors</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-dark" style="width: 38px; height: 38px; background: linear-gradient(135deg, #FFE57F, #D4AF37);">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
                <h3 class="h3 fw-bold text-dark mb-1">{{ number_format($uniqueVisitors) }}</h3>
                <span class="text-secondary small">Unique IP Addresses</span>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary extra-small fw-bold text-uppercase" style="letter-spacing: 0.5px;">CTA &amp; Link Clicks</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 38px; height: 38px; background: linear-gradient(135deg, #1d4ed8, #1e40af);">
                        <i class="bi bi-cursor-fill"></i>
                    </div>
                </div>
                <h3 class="h3 fw-bold text-dark mb-1">{{ number_format($totalClicks) }}</h3>
                <span class="text-primary small fw-semibold">Buttons, CTAs &amp; Social</span>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary extra-small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Social Media Leads</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 38px; height: 38px; background: linear-gradient(135deg, #059669, #10b981);">
                        <i class="bi bi-share"></i>
                    </div>
                </div>
                <h3 class="h3 fw-bold text-dark mb-1">{{ number_format($socialBreakdown->sum('total')) }}</h3>
                <span class="text-success small fw-semibold">WhatsApp &amp; Handles</span>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- 7-Day Traffic Trend -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h4 class="h6 fw-bold text-dark mb-1">Daily Traffic Trend (Last 7 Days)</h4>
                        <p class="text-secondary extra-small mb-0">Total visitors and page impressions over time.</p>
                    </div>
                    <span class="badge bg-light text-dark border px-2 py-1 extra-small">Daily</span>
                </div>
                <div style="height: 280px; position: relative;">
                    <canvas id="trafficTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Social Media Clicks Breakdown -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h4 class="h6 fw-bold text-dark mb-1">Social Handle Clicks</h4>
                <p class="text-secondary extra-small mb-3">Distribution of outgoing social media channel clicks.</p>
                <div style="height: 200px; position: relative;" class="mb-3">
                    <canvas id="socialClicksChart"></canvas>
                </div>
                <div class="d-flex flex-column gap-2 pt-2 border-top">
                    @forelse($socialBreakdown as $sb)
                        <div class="d-flex align-items-center justify-content-between small">
                            <span class="d-flex align-items-center gap-2 text-dark fw-medium text-capitalize">
                                <i class="bi bi-circle-fill" style="font-size: 8px; color: #004225;"></i>
                                {{ $sb->label }}
                            </span>
                            <span class="badge bg-light text-dark border fw-bold">{{ $sb->total }} clicks</span>
                        </div>
                    @empty
                        <p class="text-secondary extra-small mb-0 text-center py-2">No social clicks recorded yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Telemetry Event Logs Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
            <div>
                <h4 class="h6 fw-bold text-dark mb-1">Recent Telemetry Activity</h4>
                <p class="text-secondary extra-small mb-0">Last 20 tracked actions across the platform.</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Event Type</th>
                        <th>Category</th>
                        <th>Action / Label</th>
                        <th>IP Address</th>
                        <th>Target Page</th>
                        <th class="pe-4 text-end">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentEvents as $ev)
                        <tr>
                            <td class="ps-4">
                                @if($ev->event_type === 'page_view')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 extra-small fw-bold">Page View</span>
                                @elseif($ev->category === 'social')
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2.5 py-1 extra-small fw-bold">Social Click</span>
                                @else
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 extra-small fw-bold">Interaction</span>
                                @endif
                            </td>
                            <td class="text-secondary text-capitalize">{{ $ev->category ?? 'General' }}</td>
                            <td class="fw-semibold text-dark">{{ $ev->label ?? 'Direct Load' }}</td>
                            <td class="font-monospace small text-muted">{{ $ev->ip_address ?? '—' }}</td>
                            <td class="small text-truncate" style="max-width: 200px;">
                                <span class="text-secondary">{{ $ev->url ? parse_url($ev->url, PHP_URL_PATH) : '/' }}</span>
                            </td>
                            <td class="pe-4 text-end text-muted small">{{ $ev->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted small">No telemetry logs recorded yet. Visit the homepage to start logging activity.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js Integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Daily Trend Chart
    var trendCtx = document.getElementById('trafficTrendChart');
    if (trendCtx) {
        var trendData = @json($dailyTrend);
        var labels = Object.keys(trendData);
        var counts = Object.values(trendData);

        if (labels.length === 0) {
            labels = ['Today'];
            counts = [{{ $totalVisits }}];
        }

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Page Views',
                    data: counts,
                    borderColor: '#004225',
                    backgroundColor: 'rgba(0, 66, 37, 0.08)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#D4AF37',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

    // 2. Social Media Doughnut
    var socialCtx = document.getElementById('socialClicksChart');
    if (socialCtx) {
        var socialLabels = @json($socialBreakdown->pluck('label'));
        var socialTotals = @json($socialBreakdown->pluck('total'));

        if (socialLabels.length === 0) {
            socialLabels = ['WhatsApp', 'Facebook', 'Instagram', 'Twitter / X'];
            socialTotals = [1, 0, 0, 0];
        }

        new Chart(socialCtx, {
            type: 'doughnut',
            data: {
                labels: socialLabels,
                datasets: [{
                    data: socialTotals,
                    backgroundColor: ['#25D366', '#1877F2', '#E4405F', '#000000', '#0A66C2', '#FF0000'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '68%'
            }
        });
    }
});
</script>
@endsection

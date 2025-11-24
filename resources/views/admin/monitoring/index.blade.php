@extends('layouts.admin')

@section('title', 'API Security Monitoring')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">API Security Monitoring</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card border-left-primary shadow">
                <div class="card-body">
                    <h6>Total Request</h6>
                    <h3>{{ $totalRequest }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-danger shadow">
                <div class="card-body">
                    <h6>Failed Request (4xx/5xx)</h6>
                    <h3>{{ $failedRequest }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-warning shadow">
                <div class="card-body">
                    <h6>Suspicious (401/403)</h6>
                    <h3>{{ $suspicious }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-info shadow">
                <div class="card-body">
                    <h6>Top Endpoint</h6>
                    <h5>{{ $topEndpoints->first()->endpoint ?? '-' }}</h5>
                    <small>{{ $topEndpoints->first()->total ?? 0 }} hits</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header">Requests per hour (last 24h)</div>
                <div class="card-body">
                    <canvas id="chartHour"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header">Requests per day (last 7 days)</div>
                <div class="card-body">
                    <canvas id="chartDay"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <div class="card shadow mb-4">
        <div class="card-header">Security Alerts</div>
        <div class="card-body">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>At</th>
                        <th>IP</th>
                        <th>Type</th>
                        <th>Message</th>
                        <th>Severity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alerts as $a)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($a->created_at)->diffForHumans() }}</td>
                        <td>{{ $a->ip_address ?? '-' }}</td>
                        <td>{{ $a->type }}</td>
                        <td style="max-width:400px;">{{ \Illuminate\Support\Str::limit($a->message, 160) }}</td>
                        <td>{{ $a->severity }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.monitoring.alerts.read', $a->id) }}" style="display:inline;">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary">Mark read</button>
                            </form>

                            <!-- quick block -->
                            <form method="POST" action="{{ route('admin.monitoring.block') }}" style="display:inline;">
                                @csrf
                                <input type="hidden" name="ip_to_block" value="{{ $a->ip_address }}">
                                <input type="hidden" name="reason" value="Block from monitoring UI (alert #{{ $a->id }})">
                                <button class="btn btn-sm btn-outline-danger">Block IP</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <a href="{{ route('admin.monitoring') }}" class="btn btn-sm btn-secondary">Refresh</a>
        </div>
    </div>

    <!-- Recent logs -->
    <div class="card shadow mb-4">
        <div class="card-header">Recent API Activity</div>
        <div class="card-body">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>Endpoint</th>
                        <th>Status</th>
                        <th>User</th>
                        <th>IP</th>
                        <th>Time (ms)</th>
                        <th>At</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($recentLogs as $log)
                    <tr>
                        <td>{{ $log->method }}</td>
                        <td>{{ $log->endpoint }}</td>
                        <td>{{ $log->status_code }}</td>
                        <td>{{ $log->user_id ?? '-' }}</td>
                        <td>{{ $log->ip_address }}</td>
                        <td>{{ number_format($log->duration_ms,2) }}</td>
                        <td>{{ $log->created_at }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart hour
    fetch("{{ route('admin.monitoring.chart.hour') }}")
        .then(res => res.json())
        .then(json => {
            const ctx = document.getElementById('chartHour').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: json.labels,
                    datasets: [{
                        label: 'Requests',
                        data: json.data,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });

    // Chart day
    fetch("{{ route('admin.monitoring.chart.day') }}")
        .then(res => res.json())
        .then(json => {
            const ctx = document.getElementById('chartDay').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: json.labels,
                    datasets: [{
                        label: 'Requests',
                        data: json.data,
                        barPercentage: 0.7
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
});
</script>
@endsection

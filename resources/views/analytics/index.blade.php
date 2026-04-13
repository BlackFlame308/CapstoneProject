@extends('layouts.app')

@section('title', 'Analytics - SafeTrack')

@section('content')
    <h1 class="h3 mb-4">Analytics</h1>
    <p class="text-muted mb-4">Sitio ranking by vulnerable persons and demographic breakdown.</p>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title">Total Population</h6>
                        <p class="display-6 mb-0">{{ optional($barangayAnalytics)->total_population ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title">PWDs</h6>
                        <p class="display-6 mb-0">{{ optional($barangayAnalytics)->total_pwd ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title">Seniors</h6>
                        <p class="display-6 mb-0">{{ optional($barangayAnalytics)->total_seniors ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title">Children</h6>
                        <p class="display-6 mb-0">{{ optional($barangayAnalytics)->total_children ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Sitio Ranking by Vulnerable Persons</h5>
                        <canvas id="sitioChart" height="320"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Demographic Breakdown</h5>
                        <canvas id="demographicChart" height="320"></canvas>
                    </div>
                </div>
            </div>
        </div>

@endsection

@section('additional_scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const sitioData = @json($sitioAnalytics->map(function ($stat) {
            return [
                'sitio' => $stat->sitio,
                'vulnerable' => ($stat->total_seniors ?? 0) + ($stat->total_children ?? 0) + ($stat->total_pwd ?? 0),
            ];
        })->sortByDesc('vulnerable')->values());

        const sitioLabels = sitioData.map(item => item.sitio || 'Unknown');
        const sitioValues = sitioData.map(item => item.vulnerable);

        const demographicData = [
            {{ optional($barangayAnalytics)->total_pwd ?? 0 }},
            {{ optional($barangayAnalytics)->total_seniors ?? 0 }},
            {{ optional($barangayAnalytics)->total_children ?? 0 }},
            {{ optional($barangayAnalytics)->total_adults ?? 0 }},
        ];

        const sitioCtx = document.getElementById('sitioChart').getContext('2d');
        new Chart(sitioCtx, {
            type: 'bar',
            data: {
                labels: sitioLabels,
                datasets: [{
                    label: 'Vulnerable Persons',
                    data: sitioValues,
                    backgroundColor: '#0d6efd',
                    borderRadius: 8,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    x: { beginAtZero: true },
                    y: { ticks: { autoSkip: false } }
                }
            }
        });

        const demographicCtx = document.getElementById('demographicChart').getContext('2d');
        new Chart(demographicCtx, {
            type: 'pie',
            data: {
                labels: ['PWD', 'Seniors', 'Children', 'Adults'],
                datasets: [{
                    data: demographicData,
                    backgroundColor: ['#dc3545', '#ffc107', '#0dcaf0', '#6c757d'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>
@endsection

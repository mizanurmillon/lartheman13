@extends('backend.app')
@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold">Dashboard</h1>
                <p class="text-muted">Welcome back, here your security overview.</p>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Total Incidents -->
            <div class="col-md-6 mb-3">
                <div class="card border shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Total Incidents</h6>
                            <h4 class="card-title fw-bold">{{ $totalLastFiveWeeksIncidents }}</h4>
                            <p class="text-muted small">+5 from last week</p>
                        </div>
                        <div>
                            <i class="bi bi-exclamation-triangle-fill fs-2 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report an Incident -->
            <div class="col-md-6 mb-3">
                <div class="card bg-primary text-white shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Report an Incident</h5>
                        <a href="{{ route('admin.security_events.index') }}" class="btn btn-secondary w-100 mt-3">
                            <i class="bi bi-plus-circle me-2"></i> New Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Regional Events -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Regional Events</h5>
                        <p class="text-muted small">Verified incidents from nearby churches in your selected radius.</p>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Event Category</th>
                                        <th>Incident Type</th>
                                        <th>Location</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($latestIncidents as $incident)
                                        <tr>
                                            <td>{{ $incident->category ? $incident->category->name : 'N/A' }}</td>
                                            <td>{{ $incident->incidentType ? $incident->incidentType->name : ($incident->incident_type_other ?? 'N/A') }}</td>
                                            <td><i class="bi bi-geo-alt me-1 text-muted"></i> {{ $incident->location->name ?? 'N/A' }}</td>
                                            <td>{{ Str::limit($incident->description, 100) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <!-- Regional Alerts Filter -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Regional Alerts Filter</h5>
                        <p class="text-muted small">Adjust the distance to see incidents from nearby churches.</p>

                        <label for="distanceRange" class="form-label">Distance: 10 miles</label>
                        <input type="range" class="form-range" id="distanceRange" min="0" max="20" value="10">

                        <div class="alert alert-success mt-3 p-2">
                            <strong>2 new</strong> "Suspicious Vehicle" reports within 10 miles in the last 48 hours.
                        </div>
                        <div class="alert alert-secondary text-dark mt-2 p-2">
                            1 "Attempted Break-in" report 5 miles away, 3 days ago.
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>

        <!-- Incident Types Chart Placeholder -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Incident Types</h5>
                        <p class="text-muted small">Breakdown of all recorded incidents by category.</p>
                        <div class="text-center p-5 bg-light border rounded" style="height: 350px;">
                            <canvas id="incidentChart" style="max-height: 600px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('script')
        <!-- Chart.js CDN -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            $(document).ready(function () {
                Echo.private('chat-channel.' + 1).listen('MessageSentEvent', (e) => {
                    console.log('Message Receiver:', e);
                })

                Echo.private('conversation-channel.' + 1).listen('ConversationEvent', (e) => {
                    console.log('Conversation and Unread Message count:', e);
                })

            });

            document.addEventListener("DOMContentLoaded", function () {
                const ctx = document.getElementById('incidentChart').getContext('2d');

                // PHP data → JavaScript variable
                const categoryWiseData = @json($categoryWiseIncidents);

                // labels & data prepare for Chart.js
                const labels = categoryWiseData.map(item => item.category?.name ?? 'Unknown');
                const data = categoryWiseData.map(item => item.total);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Number of Incidents',
                            data: data,
                            backgroundColor: [
                                '#e74c3c',
                                '#f39c12',
                                '#3498db',
                                '#2ecc71',
                                '#9b59b6'
                            ],
                            borderRadius: 6,
                            borderSkipped: false,
                            barThickness: 40,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                ticks: {
                                    display: false
                                },
                                grid: {
                                    color: '#dee2e6'
                                }
                            },
                            y: {
                                ticks: {
                                    color: '#6c757d'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true
                            }
                        }
                    }
                });
            });
        </script>
    @endpush

@endsection
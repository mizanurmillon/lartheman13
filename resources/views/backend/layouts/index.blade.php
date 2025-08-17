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
                            <h4 class="card-title fw-bold">69</h4>
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
                        <a href="/events" class="btn btn-secondary w-100 mt-3">
                            <i class="bi bi-plus-circle me-2"></i> New Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Regional Events -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Regional Events</h5>
                        <p class="text-muted small">Verified incidents from nearby churches in your selected radius.</p>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Event Type</th>
                                        <th>Location</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Vehicle Break-In</td>
                                        <td><i class="bi bi-geo-alt me-1 text-muted"></i> New Hope Chapel</td>
                                        <td>Reported 3 miles away.</td>
                                    </tr>
                                    <tr>
                                        <td>Loitering</td>
                                        <td><i class="bi bi-geo-alt me-1 text-muted"></i> Grace Fellowship</td>
                                        <td>Reported 5 miles away.</td>
                                    </tr>
                                    <tr>
                                        <td>Suspicious Package</td>
                                        <td><i class="bi bi-geo-alt me-1 text-muted"></i> City Church</td>
                                        <td>Unattended bag near entrance.</td>
                                    </tr>
                                    <tr>
                                        <td>Vandalism</td>
                                        <td><i class="bi bi-geo-alt me-1 text-muted"></i> Oak Ridge Community</td>
                                        <td>Graffiti on main building.</td>
                                    </tr>
                                    <tr>
                                        <td>Medical Emergency</td>
                                        <td><i class="bi bi-geo-alt me-1 text-muted"></i> First Baptist</td>
                                        <td>Paramedics called to service.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Regional Alerts Filter -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Regional Alerts Filter</h5>
                        <p class="text-muted small">Adjust the distance to see incidents from nearby churches.</p>

                        <label for="distanceRange" class="form-label">Distance: 10 miles</label>
                        <input type="range" class="form-range" id="distanceRange" min="0" max="20"
                            value="10">

                        <div class="alert alert-success mt-3 p-2">
                            <strong>2 new</strong> "Suspicious Vehicle" reports within 10 miles in the last 48 hours.
                        </div>
                        <div class="alert alert-secondary text-dark mt-2 p-2">
                            1 "Attempted Break-in" report 5 miles away, 3 days ago.
                        </div>
                    </div>
                </div>
            </div>
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
            document.addEventListener("DOMContentLoaded", function() {
                const ctx = document.getElementById('incidentChart').getContext('2d');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [
                            'Suspicious Activity',
                            'Theft/Burglary',
                            'Vandalism',
                            'Medical',
                            'Other'
                        ],
                        datasets: [{
                            label: 'Number of Incidents',
                            data: [28, 15, 12, 9, 5], // Update with dynamic data if needed
                            backgroundColor: [
                                '#e74c3c', // Suspicious
                                '#f39c12', // Theft
                                '#3498db', // Vandalism
                                '#2ecc71', // Medical
                                '#9b59b6' // Other
                            ],
                            borderRadius: 6,
                            borderSkipped: false,
                            barThickness: 40,
                        }]
                    },
                    options: {
                        indexAxis: 'y', // Horizontal bar chart
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

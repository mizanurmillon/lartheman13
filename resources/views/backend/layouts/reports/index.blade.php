@extends('backend.app')
@section('title', 'Incident Reports')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex align-items-start gap-3 mb-8">
            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="#267fd9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-shield-alert" style="height: 24px; width: 24px;">
                    <line x1="12" x2="12" y1="20" y2="10"></line>
                    <line x1="18" x2="18" y1="20" y2="4"></line>
                    <line x1="6" x2="6" y1="20" y2="16"></line>
                </svg>
            </div>
            <div>
                <h1 class="h3 fw-bold">Incident Reports</h1>
                <p class="text-muted mb-0">Monitor local incidents and reports here.</p>
            </div>
        </div>
        <!-- Incident Types Chart Placeholder -->

        <div class="row mb-4">
            <div class="col-12">
                <form action="{{ route('admin.reports.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="incident_type_id" class="form-select">
                            <option value="">All Incident Types</option>
                            @foreach($incidentTypes as $type)
                                <option value="{{ $type->id }}" {{ request('incident_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="location_id" class="form-select">
                            <option value="">All Locations</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- <div class="col-md-3">
                        <select name="church_profile_id" class="form-select">
                            <option value="">All Churches</option>
                            @foreach($churches as $church)
                                <option value="{{ $church->id }}" {{ request('church_profile_id') == $church->id ? 'selected' : '' }}>
                                    {{ $church->church_name }}
                                </option>
                            @endforeach
                        </select>
                    </div> --}}

                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-danger">Reset</a>
                    </div>
                </form>
            </div>
        </div>

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
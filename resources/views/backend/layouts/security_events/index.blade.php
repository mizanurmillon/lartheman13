@extends('backend.app')

@push('style')
    <style>
        .card {
            border-radius: 10px;
            border: none;
        }

        .nav-tabs .nav-link.active {
            font-weight: bold;
            border-bottom: 3px solid #0A1D27;
            background-color: transparent;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 0.8rem;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-verified {
            background-color: #d4edda;
            color: #155724;
        }

        .status-archived {
            background-color: #e2e3e5;
            color: #6c757d;
        }
    </style>
@endpush

@section('title', 'Security Events')

@section('content')

    <div class="container-fluid py-4">
        <div class="d-flex align-items-start gap-3 mb-8">
            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="#267fd9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-shield-alert" style="height: 24px; width: 24px;">
                    <path
                        d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z">
                    </path>
                    <path d="M12 8v4"></path>
                    <path d="M12 16h.01"></path>
                </svg>
            </div>
            <div>
                <h1 class="h3 fw-bold">Security Events</h1>
                <p class="text-muted mb-0">Monitor local incidents and report new events.</p>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-5" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="regional-tab" data-bs-toggle="tab" data-bs-target="#regional"
                    type="button">Regional Feed</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="church-tab" data-bs-toggle="tab" data-bs-target="#church" type="button">My
                    Church Log</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="report-tab" data-bs-toggle="tab" data-bs-target="#report"
                    type="button">Report Event</button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="myTabContent">

            <!-- Regional Feed -->
            <div class="tab-pane fade" id="regional" role="tabpanel" aria-labelledby="regional-tab">
                <div class="card p-3">
                    <div class="mb-4">
                        <h3 class="font-headline text-2xl font-semibold leading-none tracking-tight"
                            style="font-size: 1.5rem;">Regional Feed</h3>
                        <p class="text-sm text-muted">Approved security events from nearby churches.</p>
                    </div>
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Details</th>
                                <th>Date</th>
                                <th>Media</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Vehicle Break-In</td>
                                <td>Reported at New Hope Chapel, 3 miles away.</td>
                                <td>7/27/2024</td>
                                <td>📎</td>
                                <td><span class="status-badge status-verified">Verified</span></td>
                            </tr>
                            <tr>
                                <td>Loitering</td>
                                <td>Loitering reported at Grace Fellowship, 5 miles away.</td>
                                <td>7/26/2024</td>
                                <td></td>
                                <td><span class="status-badge status-verified">Verified</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- My Church Log -->
            <div class="tab-pane fade" id="church" role="tabpanel" aria-labelledby="church-tab">
                <div class="card p-3">
                    <div class="mb-4">
                        <h3 class="font-headline text-2xl font-semibold leading-none tracking-tight"
                            style="font-size: 1.5rem;">Event Log for Your Church</h3>
                        <p class="text-sm text-muted">A log of security-related events for your church. Admins can approve
                            pending events here.</p>
                    </div>
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Details</th>
                                <th>Date</th>
                                <th>Media</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Medical Emergency</td>
                                <td>Child had an allergic reaction in the nursery.</td>
                                <td>7/28/2024</td>
                                <td></td>
                                <td><span class="status-badge status-pending">Pending</span></td>
                                <td><button class="btn btn-primary btn-sm">Review</button></td>
                            </tr>
                            <tr>
                                <td>Vandalism</td>
                                <td>Graffiti on north wall of the youth building.</td>
                                <td>7/25/2024</td>
                                <td>📎</td>
                                <td><span class="status-badge status-pending">Pending</span></td>
                                <td><button class="btn btn-primary btn-sm">Review</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Report Event -->
            <div class="tab-pane fade show active" id="report" role="tabpanel" aria-labelledby="report-tab">
                <div class="card p-4">

                    <div class="mb-4">
                        <h3 class="font-headline text-2xl font-semibold leading-none tracking-tight"
                            style="font-size: 1.5rem;">Report a New Security Event</h3>
                        <p class="text-sm text-muted">All submissions are confidential and reviewed by an administrator.</p>
                    </div>

                    <form>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Event Category</label>
                                <select class="form-select">
                                    <option>Select a category</option>
                                    <option>Medical Emergency</option>
                                    <option>Theft</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Incident Type</label>
                                <select class="form-select">
                                    <option>Select an incident type</option>
                                    <option>Minor</option>
                                    <option>Major</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <select class="form-select">
                                <option>Select a location</option>
                                <option>Main Hall</option>
                                <option>Parking Lot</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Detailed Description</label>
                            <textarea class="form-control" rows="3" placeholder="Describe the event..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Attach Media</label>
                            <input type="file" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date and Time of Incident</label>
                            <input type="datetime-local" class="form-control">
                        </div>
                        <button class="btn btn-primary">Submit for Review</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    @push('script')
    @endpush

@endsection

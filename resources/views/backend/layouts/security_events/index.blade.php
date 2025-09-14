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
        <div class="d-flex align-items-start gap-3 mb-4">
            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                <i class="bi bi-shield-lock fs-4"></i>
            </div>
            <div>
                <h1 class="h3 fw-bold">Security Events</h1>
                <p class="text-muted mb-0">Monitor local incidents and report new events.</p>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-5" id="securityTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link {{ request('activeTab') == 'regional' ? 'active' : (!request('activeTab') ? 'active' : '') }}"
                    id="regional-tab" data-bs-toggle="tab" data-bs-target="#regional" type="button">
                    Regional Feed
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ request('activeTab') == 'church' ? 'active' : '' }}" id="church-tab"
                    data-bs-toggle="tab" data-bs-target="#church" type="button">
                    My Church Log
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ request('activeTab') == 'report' ? 'active' : '' }}" id="report-tab"
                    data-bs-toggle="tab" data-bs-target="#report" type="button">
                    Report Event
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="securityTabContent">
            <!-- Regional Feed -->
            <div class="tab-pane fade {{ request('activeTab') == 'regional' || !request('activeTab') ? 'show active' : '' }}"
                id="regional" role="tabpanel">
                <div class="card p-3">
                    <h5>Regional Feed</h5>
                    <p class="text-muted">Approved security events from nearby churches.</p>
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Event Category</th>
                                <th>Incident Type</th>
                                <th>Details</th>
                                <th>Date</th>
                                <th>Media</th>
                                <th>Status & Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($verified as $event)
                                <tr>
                                    <td>{{ $event->category?->name ?? 'N/A' }}</td>
                                    <td>{{ $event->incidentType?->name ?? ($event->incident_type_other ?? 'N/A') }}</td>
                                    <td>{{ Str::limit($event->description, 70) }}</td>
                                    <td>{{ $event->incident_date }}</td>
                                    <td>
                                        @if ($event->media->isNotEmpty())
                                            <img src="{{ asset($event->media->first()->file_url) }}" width="100"
                                                alt="Media">
                                        @else
                                            <span>No Media</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-badge status-verified">{{ $event->status }}</span>
                                        <a href="{{ route('admin.security_events.view', $event->id) }}"
                                            class="btn btn-info btn-sm">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- My Church Log -->
            <div class="tab-pane fade {{ request('activeTab') == 'church' ? 'show active' : '' }}" id="church"
                role="tabpanel">
                <div class="card p-3">
                    <h5>Event Log for Your Church</h5>
                    <p class="text-muted">A log of security-related events for your church.</p>
                    <table id="data-table" class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Event Category</th>
                                <th>Incident Type</th>
                                <th>Details</th>
                                <th>Date</th>
                                <th>Media</th>
                                <th>Status & Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <!-- Report Event -->
            <div class="tab-pane fade {{ request('activeTab') == 'report' ? 'show active' : '' }}" id="report"
                role="tabpanel">
                <div class="card p-4">
                    <h5>Report a New Security Event</h5>
                    <form action="{{ route('admin.security_events.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-3">
                            <!-- Event Category -->
                            <div class="col-md-6">
                                <label class="form-label">Event Category</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" name="category_id"
                                    id="category_id">
                                    <option value="">Select a category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Incident Type -->
                            <div class="col-md-6">
                                <label class="form-label">Incident Type</label>
                                <select class="form-select @error('incident_type_id') is-invalid @enderror"
                                    name="incident_type_id" id="incident_type_id">
                                    <option value="">Select Incident Type</option>
                                    <!-- Populated via AJAX or Blade foreach for selected category -->
                                </select>
                                @error('incident_type_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror

                                <!-- Other Incident Type text -->
                                <input type="text" name="incident_type_other" id="incident_type_other"
                                    class="form-control mt-2" placeholder="Other (max 30 characters)" maxlength="30"
                                    style="display:none;">
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <select name="location_id" class="form-select @error('location_id') is-invalid @enderror">
                                <option value="">Select Location</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                            @error('location_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">Detailed Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3"
                                placeholder="Describe the event...">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Attach Media -->
                        <div class="mb-3">
                            <label class="form-label">Attach Media (Optional)</label>
                            <input type="file" name="file_url[]" class="form-control" accept="image/*" multiple>
                        </div>

                        <!-- Incident Date & Time -->
                        <div class="mb-3 row">
                            <div class="col-md-6">
                                <label class="form-label">Date of Incident</label>
                                <input type="date" name="incident_date"
                                    class="form-control @error('incident_date') is-invalid @enderror"
                                    value="{{ old('incident_date') }}">
                                @error('incident_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Time of Incident</label>
                                <input type="time" name="incident_time"
                                    class="form-control @error('incident_time') is-invalid @enderror"
                                    value="{{ old('incident_time') }}">
                                @error('incident_time')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <button class="btn btn-primary">Submit for Review</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="show-event-modal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Event Verification</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Event Category</label>
                            <input type="text" class="form-control" id="incident_type" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Incident Type</label>
                            <input type="text" class="form-control" id="incident_type_name" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" id="location_name" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Detailed Description</label>
                        <textarea class="form-control" id="description" rows="3" readonly></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Attached Media</label>
                        <div id="preview-media" class="d-flex flex-wrap gap-2 mt-2"></div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Date of Incident</label>
                            <input type="date" class="form-control" id="incident_date" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Time of Incident</label>
                            <input type="time" class="form-control" id="incident_time" readonly>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="verify-btn">Verify</button>
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            $(document).ready(function() {
                let dTable = $('#data-table').DataTable({
                    order: [],
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"]
                    ],
                    processing: true,
                    responsive: true,
                    serverSide: true,

                    language: {
                        processing: `<div class="text-center">
                                     <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>`
                    },

                    scroller: {
                        loadingIndicator: false
                    },
                    pagingType: "full_numbers",
                    dom: "<'row justify-content-between table-topbar'<'col-md-2 col-sm-4 px-0'l><'col-md-2 col-sm-4 px-0'f>>tipr",
                    ajax: {
                        url: "{{ route('admin.security_events.index') }}",
                        type: "get",
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'event_category',
                            name: 'event_category'
                        },
                        {
                            data: 'incident_type',
                            name: 'incident_type'
                        },
                        {
                            data: 'description',
                            name: 'description'
                        },
                        {
                            data: 'incident_date',
                            name: 'incident_date'
                        },
                        {
                            data: 'media',
                            name: 'media',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'status_actions',
                            name: 'status_actions',
                            orderable: false,
                            searchable: false
                        },
                    ]

                });
                // Page load: check if activeTab stored
                var activeTab = localStorage.getItem('activeTab');
                if (activeTab) {
                    var tabTrigger = new bootstrap.Tab(document.querySelector(`#${activeTab}-tab`));
                    tabTrigger.show();
                    localStorage.removeItem('activeTab');
                }
                // });

                // Open modal and show incident data
                $('body').on('click', '.show-event', function() {
                    let id = $(this).data('id');
                    $('#verify-btn').attr('data-id', id);

                    let url = "{{ route('admin.security_events.edit', ':id') }}".replace(':id', id);

                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(res) {
                            if (res.success) {
                                let incident = res.data;
                                // console.log(incident);

                                $('#incident_type').val(incident.incident_type || 'N/A');
                                $('#incident_type_name').val(incident.incident_type_name || 'N/A');
                                $('#location_name').val(incident.location_name || 'N/A');
                                $('#description').val(incident.description || '');
                                $('#incident_date').val(incident.incident_date || '');
                                $('#incident_time').val(incident.incident_time || '');

                                // Media
                                let previewDiv = $('#preview-media');
                                previewDiv.empty();
                                if (incident.media && incident.media.length > 0) {
                                    incident.media.forEach(m => {
                                        previewDiv.append(
                                            `<img src="${m.file_url}" style="width:120px; height:auto;" class="img-thumbnail">`
                                        );
                                    });
                                }

                                $('#show-event-modal').modal('show');
                            } else {
                                toastr.error('Something went wrong. Try again later.');
                            }
                        },
                        error: function() {
                            toastr.error('Request failed! Please try again.');
                        }
                    });
                });

                // Verify button
                $(document).on('click', '#verify-btn', function() {
                    let id = $(this).data('id');

                    $.ajax({
                        url: "{{ route('admin.security_events.verify', ':id') }}".replace(':id', id),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#show-event-modal').modal('hide');
                            toastr.success(response.message);
                            // Reload DataTable
                            $('#data-table').DataTable().ajax.reload();
                        },
                        error: function() {
                            toastr.error('Something went wrong!');
                        }
                    });
                });



                // Fetch Incident Types by Category
                $('#category_id').on('change', function() {
                    let categoryId = $(this).val();
                    let incidentSelect = $('#incident_type_id');
                    incidentSelect.empty().append('<option value="">Select Incident Type</option>');

                    $('#incident_type_other').hide();

                    if (categoryId) {
                        $.ajax({
                            url: '{{ route('admin.incident_types.by_category') }}',
                            type: 'GET',
                            data: {
                                category_id: categoryId
                            },
                            success: function(data) {
                                data.forEach(function(item) {
                                    incidentSelect.append(
                                        `<option value="${item.id}" data-share="${item.share_regionally ? 'Yes' : 'No'}">${item.name}</option>`
                                    );
                                });
                                incidentSelect.append('<option value="other">Other</option>');
                            }
                        });
                    }
                });

                // Show Other field
                $('#incident_type_id').on('change', function() {
                    if ($(this).val() === 'other') {
                        $('#incident_type_other').show();
                    } else {
                        $('#incident_type_other').hide();
                    }
                });

            });
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let activeTab = @json(session('activeTab'));
                if (activeTab) {
                    const triggerEl = document.querySelector(`button[data-bs-target="#${activeTab}"]`);
                    if (triggerEl) {
                        new bootstrap.Tab(triggerEl).show();
                    }
                }
            });
        </script>
    @endpush
@endsection

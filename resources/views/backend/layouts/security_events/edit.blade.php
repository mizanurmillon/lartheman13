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
        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Report Event -->
            <div class="tab-pane fade show active" id="report" role="tabpanel" aria-labelledby="report-tab">
                <div class="card p-4">
                    <div class="mb-4">
                        <h3 class="font-headline text-2xl font-semibold">Report a New Security Event</h3>
                        <p class="text-sm text-muted">All submissions are confidential and reviewed by an administrator.</p>
                    </div>

                    <form action="{{ route('admin.security_events.update', $incident->id) }}?tab=report') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-3">
                            <!-- Event Category -->
                            <div class="col-md-6">
                                <label class="form-label">Event Category</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" name="category_id"
                                    id="category_id">
                                    <option value="">Select a category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            @if ($incident->category_id == $category->id) selected @endif>{{ $category->name }}</option>
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
                                    @foreach ($incidentTypes as $type)
                                        <option value="{{ $type->id }}"
                                            @if ($incident->incident_type_id == $type->id) selected @endif>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                    <option value="other" @if ($incident->incident_type_id == null && $incident->incident_type_other) selected @endif>
                                        Other
                                    </option>
                                </select>
                                @error('incident_type_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror

                                <!-- Other Incident Type text -->
                                <input type="text" name="incident_type_other" id="incident_type_other"
                                    class="form-control mt-2" placeholder="Other (max 30 characters)" maxlength="30"
                                    value="{{ old('incident_type_other', $incident->incident_type_other) }}"
                                    style="display:none;">
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <select name="location_id" class="form-select @error('location_id') is-invalid @enderror">
                                <option value="">Select Location</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}" {{ $incident->location_id == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
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
                                placeholder="Describe the event...">{{ $incident->description ?? old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Attach Media -->
                        <div class="mb-3">
                            <label class="form-label">Attach Media (Optional)</label>
                            <input type="file" name="file_url[]" class="form-control" accept="image/*" multiple>
                            @if($incident->media->count() > 0)
                                <div class="mt-2">
                                    @foreach($incident->media as $media)
                                        <img src="{{ asset($media->file_url) }}" alt="" class="img-thumbnail me-2 mb-2" style="width: 100px; height: 100px; object-fit: cover;">
                                    @endforeach
                                </div>
                            @endif
                            @error('file_url')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Incident Date & Time -->
                        <div class="mb-3 row">
                            <div class="col-md-6">
                                <label class="form-label">Date of Incident</label>
                                <input type="date" name="incident_date"
                                    class="form-control @error('incident_date') is-invalid @enderror"
                                    value="{{ $incident->incident_date ?? old('incident_date') }}">
                                @error('incident_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Time of Incident</label>
                                <input type="time" name="incident_time"
                                    class="form-control @error('incident_time') is-invalid @enderror"
                                    value="{{ $incident->incident_time ?? old('incident_time') }}">
                                @error('incident_time')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <button class="btn btn-primary">Submit for Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    @push('script')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const select = document.getElementById('incident_type_id');
                const otherInput = document.getElementById('incident_type_other');

                function toggleOtherField() {
                    if (select.value === 'other') {
                        otherInput.style.display = 'block';
                    } else {
                        otherInput.style.display = 'none';
                        otherInput.value = '';
                    }
                }
                toggleOtherField();

                // On change
                select.addEventListener('change', toggleOtherField);
            });
        </script>
    @endpush


@endsection

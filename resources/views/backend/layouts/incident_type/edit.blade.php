@extends('backend.app')
@section('title', 'Edit Incident Type')
@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class=" container-fluid  d-flex flex-stack flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-start gap-3 mb-8">
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"viewBox="0 0 24 24" fill="none"
                        stroke="#267fd9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-shield-alert">
                        <path d="M3 5a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-14z" />
                        <path d="M12 8v4" />
                        <path d="M12 16h.01" />
                    </svg>
                </div>
                <div>
                    <h1 class="h3 fw-bold">Edit Incident Type</h1>
                    <p class="text-muted mb-0">Modify the details of the incident type.</p>
                </div>
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Toolbar-->
    <section>
        <div class="container-fluid">
            <div class="card card-body">
                <h3>Edit Incident Type</h3>
                <form action="{{ route('admin.incident_types.update', $incidentType->id) }}" method="POST">
                    @csrf

                    <div class="mt-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $incidentType->category_id == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Name</label>
                        <input name="name" value="{{ old('name', $incidentType->name) }}" type="text"
                            class="form-control @error('name') is-invalid @enderror" required>
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" id="share_regionally" name="share_regionally" {{ $incidentType->share_regionally ? 'checked' : '' }}>
                        <label class="form-check-label" for="share_regionally">Share Regionally</label>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-primary">Update</button>
                        <a href="{{ route('admin.incident_types.index') }}" class="btn btn-danger btn-lg">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
@extends('backend.app')
@section('title', 'Add Location')
@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class=" container-fluid  d-flex flex-stack flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-start gap-3 mb-8">
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="#267fd9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-shield-alert">
                        <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                        <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="h3 fw-bold">Add Location</h1>
                    <p class="text-muted mb-0">Create a new location.</p>
                </div>
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Toolbar-->

    <section>
        <div class="container-fluid">
            <div class="card card-body">
                <h3>Add Location</h3>
                <form action="{{ route('admin.locations.store') }}" method="POST">
                    @csrf
                    <div class="mt-3">
                        <label class="form-label">Name</label>
                        <input name="name" value="{{ old('name') }}" type="text"
                            class="form-control @error('name') is-invalid @enderror" required>
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="mt-4">
                        <button class="btn btn-primary">Submit</button>
                        <a href="{{ route('admin.locations.index') }}" class="btn btn-danger btn-lg">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
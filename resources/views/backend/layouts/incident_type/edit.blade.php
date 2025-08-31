@extends('backend.app')
@section('title', 'Edit Incident Type')
@section('content')
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
                                    {{ $cat->name }}</option>
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
@extends('backend.app')
@section('title', 'Add Location')
@section('content')
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
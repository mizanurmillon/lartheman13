@extends('backend.app')
@section('title', 'Training Programs')
@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class=" container-fluid  d-flex flex-stack flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-start gap-3 mb-8">
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="#267fd9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-shield-alert" style="height: 24px; width: 24px;">
                        <path d="M12 7v14"></path>
                        <path
                            d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h1 class="h3 fw-bold">Training Programs</h1>
                    <p class="text-muted mb-0">Update training programs.</p>
                </div>
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Toolbar-->

    <section>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-body">
                        <h1 class="mb-4">Edit Training Program</h1>
                        <form action="{{ route('admin.training_programs.update', $data->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="mt-4">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    name="title" id="title" value="{{ $data->title ?? old('title') }}"
                                    placeholder="Enter Training Program title" />
                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mt-4">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                    placeholder="Enter Description" rows="4">{{ $data->description ?? old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mt-4">
                                <label for="file_url" class="form-label">Upload File (pdf & docx)</label>
                                <input type="file" class="form-control @error('file_url') is-invalid @enderror"
                                    name="file_url" id="file_url" value="{{ old('file_url') }}" placeholder="Enter File" accept="file_url/*" />
                                @if($data->file_url)
                                    <a href="{{ asset($data->file_url) }}" target="_blank" class="mt-2 d-block">View Current File</a>
                                @endif
                                @error('file_url')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mt-4">
                                <label for="thumbnail" class="form-label">Video Thumbnail</label>
                                <input type="file" class="dropify form-control @error('thumbnail') is-invalid @enderror"
                                    name="thumbnail" id="thumbnail" value="{{ old('thumbnail') }}" placeholder="Enter File" accept="image/*" data-default-file="{{ asset( $data->thumbnail ?? 'backend/images/placeholder/image_placeholder.png') }}" />
                                @error('thumbnail')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mt-4">
                                <label for="video" class="form-label">Video</label>
                                <input type="file" class="form-control @error('video') is-invalid @enderror"
                                    name="video" id="video" value="{{ old('video') }}" placeholder="Enter Video"
                                    accept="video/*" />
                                @error('video')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <video width="220" height="80" controls class="mt-2">
                                    <source src="{{ asset($data->video) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>

                            <div class="mt-4">
                                <input type="submit" class="btn btn-primary btn-lg" value="Submit">
                                <a href="{{ route('admin.training_programs.index') }}"
                                    class="btn btn-danger btn-lg">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('backend.app')

@section('title', 'Incident Details')

@push('style')
    <style>
        .incident-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease;
        }

        .incident-card:hover {
            transform: translateY(-4px);
        }

        .incident-header {
            background: linear-gradient(45deg, #267fd9, #4da8f5);
            color: white;
            padding: 20px;
        }

        .incident-title {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .incident-meta span {
            display: inline-block;
            margin-right: 15px;
            font-size: 0.9rem;
            color: #f1f1f1;
        }

        .incident-body {
            padding: 20px;
        }

        .incident-body p {
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .incident-body .label {
            font-weight: 600;
            color: #333;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
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

        .incident-gallery {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        .incident-gallery img {
            width: 150px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #f1f1f1;
            transition: transform 0.2s ease;
        }

        .incident-gallery img:hover {
            transform: scale(1.05);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex align-items-start gap-3 mb-8">
            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                <i class="bi bi-shield-lock fs-4"></i>
            </div>
            <div>
                <h1 class="h3 fw-bold">Incident Details</h1>
                <p class="text-muted mb-0">Full information about the reported incident.</p>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
                <a href="{{ route('admin.security_events.index', ['activeTab' => 'church']) }}"
                    class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left-circle"></i> Back to Security Events
                </a>
            </div>

        <!-- Incident Card -->
        <div class="incident-card mb-4">
            
            <!-- Header -->
            <div class="incident-header">
                <h2 class="incident-title">{{ $incident->category->name ?? 'Unknown Category Event' }}</h2>
                <div class="incident-meta mt-2">
                    <span><i class="bi bi-tag"></i> {{ $incident->incidentType->name ?? 'Unknown Type' }}</span>
                    <span><i class="bi bi-geo-alt"></i> {{ $incident->location->name ?? 'Unknown Location' }}</span>
                    <span><i class="bi bi-clock"></i> {{ $incident->created_at->format('d M Y, h:i A') }}</span>
                </div>
            </div>

            <!-- Body -->
            <div class="incident-body">
                <p><span class="label">Incident Type:</span> {{ $incident->incidentType->name ?? 'N/A' }}</p>
                <p><span class="label">Description:</span> {{ $incident->description ?? 'No description available.' }}</p>

                <p>
                    <span class="label">Status:</span>
                    @if ($incident->status == 'pending')
                        <span class="status-badge status-pending">Pending</span>
                    @elseif($incident->status == 'approved')
                        <span class="status-badge status-verified">Verified</span>
                    @else
                        <span class="status-badge status-archived">Archived</span>
                    @endif
                </p>

                @if ($incident->media && $incident->media->count() > 0)
                    <div class="mt-3">
                        <h6 class="fw-bold">Incident Media:</h6>
                        <div class="incident-gallery">
                            @foreach ($incident->media as $file)
                                <img src="{{ asset($file->file_url) }}" alt="Incident Media">
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection

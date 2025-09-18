@extends('backend.app')
@section('title', 'Member Details')
@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar py-5 bg-light border-bottom mb-5">
        <div class="container-fluid d-flex flex-wrap align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="#267fd9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-shield-alert" style="height: 24px; width: 24px;">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="h3 fw-bold mb-0">Team</h1>
                    <p class="text-muted mb-0">View Member Details.</p>
                </div>
            </div>
            <a href="{{ route('admin.team.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left-circle"></i> Back to Team
            </a>
        </div>
    </div>
    <!--end::Toolbar-->

    <section class="container-fluid">
        <div class="row g-4">
            <!-- User Info -->
            <div class="col-lg-5">
                <div class="custom-card shadow-sm">
                    <div class="card-header bg-gradient-primary text-white">
                        <h4 class="mb-0 text-white"><i class="bi bi-person-circle me-2 text-white"></i> Church Member Info</h4>
                    </div>
                    <div class="card-body text-center mt-5 mb-5">
                        <img src="{{ asset($teamMember->user->avatar ?? 'backend/images/placeholder/image_placeholder.png') }}"
                             class="profile-img shadow-sm"
                             alt="{{ $teamMember->user->name }}">
                        <h5 class="mt-3 fw-bold">{{ $teamMember->user->name }}</h5>
                        <p class="text-muted mb-1"><i class="bi bi-envelope"></i> {{ $teamMember->user->email }}</p>
                        <p class="text-muted mb-1"><i class="bi bi-telephone"></i> {{ $teamMember->user->phone ?? 'N/A' }}</p>
                        <p class="text-muted"><i class="bi bi-geo-alt"></i> {{ $teamMember->user->address ?? 'N/A' }}</p>

                        <div class="mt-3">
                            @if($teamMember->user->role == 'leader')
                                <span class="badge bg-info">Team Leader</span>
                            @else
                                <span class="badge bg-secondary">Team Member</span>
                            @endif

                            @if($teamMember->user->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Church Info -->
            <div class="col-lg-7">
                <div class="custom-card shadow-sm">
                    <div class="card-header bg-gradient-success text-white">
                        <h4 class="mb-0 text-white"><i class="bi bi-building me-2 text-white"></i> Church Info</h4>
                    </div>
                    <div class="card-body m-5">
                        <ul class="list-group list-group-flush text-muted bg-transparent">
                            <li class="list-group-item"><span class="fw-bold">Church ID:</span> {{ $teamMember->churchProfile->unique_id }}</li>
                            <li class="list-group-item"><span class="fw-bold">Church Name:</span> {{ $teamMember->churchProfile->church_name }}</li>
                            <li class="list-group-item"><span class="fw-bold">Church Type:</span> {{ $teamMember->churchProfile->user_name }}</li>
                            <li class="list-group-item"><span class="fw-bold">Email:</span> {{ $teamMember->churchProfile->email }}</li>
                            <li class="list-group-item"><span class="fw-bold">Phone:</span> {{ $teamMember->churchProfile->phone }}</li>
                            <li class="list-group-item"><span class="fw-bold">Denomination:</span> {{ $teamMember->churchProfile->denomination->name }}</li>
                            <li class="list-group-item"><span class="fw-bold">Address:</span> {{ $teamMember->churchProfile->address }}</li>
                            <li class="list-group-item"><span class="fw-bold">City:</span> {{ $teamMember->churchProfile->city->name }}</li>
                            <li class="list-group-item"><span class="fw-bold">State:</span> {{ $teamMember->churchProfile->state->name ?? 'N/A' }}</li>
                        </ul>

                        <div class="mt-3">
                            @if($teamMember->churchProfile->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('style')
        <style>
            .icon-box {
                background: #eaf3ff;
                color: #267fd9;
                font-size: 24px;
                padding: 12px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .custom-card {
                border: none;
                border-radius: 12px;
                overflow: hidden;
            }
            .card-header {
                padding: 15px;
            }
            .bg-gradient-primary {
                background: linear-gradient(45deg, #267fd9, #4da8f5);
            }
            .bg-gradient-success {
                background: linear-gradient(45deg, #198754, #28c76f);
            }
            .profile-img {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                object-fit: cover;
                border: 3px solid #fff;
            }
            .list-group-item {
                font-size: 15px;
                border: none;
                border-bottom: 1px solid #f1f1f1;
                padding: 10px 0;
            }
        </style>
    @endpush
@endsection

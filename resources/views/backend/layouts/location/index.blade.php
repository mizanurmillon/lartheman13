@extends('backend.app')
@section('title', 'Locations')
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
                    <h1 class="h3 fw-bold">Locations</h1>
                    <p class="text-muted mb-0">View and manage locations.</p>
                </div>
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Toolbar-->
    <section>
        <div class="container-fluid">
            <div class="bg-white p-5">
                <div class="d-flex justify-content-start mb-4">
                    <a href="{{ route('admin.locations.create') }}" class="btn btn-primary">Add Location</a>
                </div>

                <div class="table-wrapper table-responsive mt-4">
                    <table id="data-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    @push('script')
        <script>
            $(function () {
                $.ajaxSetup({ headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") } });

                if (!$.fn.DataTable.isDataTable('#data-table')) {
                    $('#data-table').DataTable({
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
                        ajax: { url: "{{ route('admin.locations.index') }}", type: "GET" },
                        columns: [
                            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                            { data: 'name', name: 'name' },
                            { data: 'action', name: 'action', orderable: false, searchable: false },
                        ],
                    });
                }
            });

            function showDeleteConfirm(id) {
                event.preventDefault();
                Swal.fire({
                    title: 'Delete?',
                    text: 'This cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true
                }).then((res) => {
                    if (res.isConfirmed) deleteItem(id);
                });
            }

            function deleteItem(id) {
                let url = "{{ route('admin.locations.destroy', ':id') }}".replace(':id', id);
                $.post(url, { _method: 'POST' }, function (resp) {
                    $('#data-table').DataTable().ajax.reload();
                    if (resp.success) toastr.success(resp.message);
                });
            }
        </script>
    @endpush
@endsection
@extends('backend.app')
@section('title', 'Incident Types')
@section('content')
    <section>
        <div class="container-fluid">
            <div class="bg-white p-5">
                <div class="d-flex justify-content-start mb-4">
                    <a href="{{ route('admin.incident_types.create') }}" class="btn btn-primary">Add Incident Type</a>
                </div>

                <div class="table-wrapper table-responsive mt-4">
                    <table id="data-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Share Regionally</th>
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
                        ajax: { url: "{{ route('admin.incident_types.index') }}", type: "GET" },
                        columns: [
                            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                            { data: 'name', name: 'name' },
                            { data: 'category', name: 'category' },
                            { data: 'share_regionally', name: 'share_regionally', orderable: false, searchable: false },
                            { data: 'action', name: 'action', orderable: false, searchable: false },
                        ],
                    });
                }
            });

            function showShareToggleConfirm(id) {
                event.preventDefault();
                Swal.fire({
                    title: 'Change sharing?',
                    text: 'Toggle region sharing for this item?',
                    icon: 'question',
                    showCancelButton: true
                }).then((res) => {
                    if (res.isConfirmed) shareToggle(id);
                });
            }

            function shareToggle(id) {
                let url = "{{ route('admin.incident_types.toggleShare', ':id') }}".replace(':id', id);
                $.post(url, {}, function (resp) {
                    $('#data-table').DataTable().ajax.reload();
                    if (resp.success) toastr.success(resp.message);
                });
            }

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
                let url = "{{ route('admin.incident_types.destroy', ':id') }}".replace(':id', id);
                $.post(url, { _method: 'POST' }, function (resp) {
                    $('#data-table').DataTable().ajax.reload();
                    if (resp.success) toastr.success(resp.message);
                });
            }
        </script>
    @endpush
@endsection
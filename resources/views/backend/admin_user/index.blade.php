@extends('backend.layouts.app')
@section('adminuser', 'mm-active')
@section('main_title', 'Admin User Management')
@section('content')
    <div class="row">
       <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="card-title fs-4">
                    Admin User Lists
                </div>
                <div>
                    <a href="{{route('admin_user.create')}}" class="btn btn-primary fw-bold" style="font-size: 13px;">Create Admin</a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered text-center w-100 display nowrap"  id="usertable" >
                    <thead>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th class="no-sort">IP Address</th>
                        <th class="no-sort">User Agents</th>
                        <th>Created at</th>
                        <th>Updated at</th>
                        <th class="no-sort">Actions</th>
                    </thead>
                </table>
            </div>
        </div>
       </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            var table = $('#usertable').DataTable({
                mark: true,
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{{route('admin_user.ssd')}}',
                columns: [
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'ip_address',
                        name: 'ip_address'
                    },
                    {
                        data: 'user_agents',
                        name: 'user_agents'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    },
                ],
                order: [[6, 'desc']],
                columnDefs:[{
                    targets : 'no-sort',
                    orderable:false,
                    searchable:false
                }],
            });
            @if (session('successMsg'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success...',
                    text: '{{ session('successMsg') }}',
                });
            @endif
            $(document).on('click', '.delete_btn', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure you want to Delete?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                                method: "Delete",
                                url:'/admin/admin_user/'+ id,
                            })
                            .done(function(msg) {
                                table.ajax.reload();
                                Swal.fire(
                                    'Deleted!',
                                    'Your are successfully deleted.',
                                    'success'
                                )
                            });

                    }
                })
            })
        });
    </script>
@endsection

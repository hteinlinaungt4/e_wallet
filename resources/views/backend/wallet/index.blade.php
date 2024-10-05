@extends('backend.layouts.app')
@section('wallet', 'mm-active')
@section('main_title', 'User Wallet')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title fs-4">
                        User Wallet
                    </div>
                    <div>
                        <a href="{{ route('addamount') }}" class="btn btn-success"> <i class="fa fa-plus-circle"
                                aria-hidden="true"></i> Add amount</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-center w-100 display nowrap" id="usertable">
                        <thead>
                            <th>Account Number</th>
                            <th>Account Person</th>
                            <th>Ammount (MMK)</th>
                            <th>Created at</th>
                            <th>Updated at</th>
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

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success...',
                    text: '{{ session('success') }}',
                });
            @endif
            @if (session('fail'))
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "{{ session('fail') }}",
                });
            @endif

            var table = $('#usertable').DataTable({
                mark: true,
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{{ route('wallet.ssd') }}',
                columns: [{
                        data: 'account_number',
                        name: 'account_number'
                    },
                    {
                        data: 'account_preson',
                        name: 'account_preson'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },

                ],
                columnDefs: [{
                    targets: 'no-sort',
                    orderable: false,
                    searchable: false
                }],
            });
        });
    </script>
@endsection

@extends('frontend.layouts.plainapp')
@section('title', 'Transfer Comfirmation')
@section('content')
    <div class="container mt-5">
        <div class="card mt-3">
            <div class="card-body">
                <h4>From</h4>
                <h6 class="text-muted">{{ $user->name }}</h6>
                <h6 class="text-muted">{{ $user->phone }}</h6>
                <form id="complete" action="{{route('user.transfer_complete')}}" method="POST">
                    @csrf
                    <input type="hidden" name="from_phone" value="{{$user->phone}}">
                    <input type="hidden" name="hash_value" value="{{$hash_value}}">
                    <input type="hidden" name="to_phone" value="{{$to_phone}}">
                    <input type="hidden" name="amount" value="{{$amount}}">
                    <input type="hidden" name="description" value="{{$description}}">

                    <div class="form-group mb-3">
                        <label for="" class="mb-2">To</label>
                        <h5 class="text-muted">{{$to_user}}</h5>
                        <h5 class="text-muted">{{ $to_phone }}</h5>
                    </div>
                    <div class="form-group mb-3">
                        <label for="" class="mb-2">Amount(MMK)</label>
                        <h5 class="text-muted">{{ number_format($amount, 2) }}</h5>
                    </div>
                    @isset($description)
                        <div class="form-group mb-3">
                            <label for="" class="mb-2">Description</label>
                            <h5 class="text-muted">{{ $description }}</h5>
                        </div>
                    @endisset
                    <button class="btn btn-primary block w-100" id="continue">Continue</button>
                </form>
            </div>
        </div>

    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#continue').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "Password!",
                    input: "password",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Comfirm"
                }).then((result) => {
                    if (result.isConfirmed) {
                        const password = result.value;

                        $.ajax({
                                method: "GET",
                                url: '/user/password_check',
                                data: {
                                    password : password
                                },
                            })
                            .then(function(res) {
                                if(res.status == "success"){
                                    $('#complete').submit();
                                }else{
                                    Swal.fire({
                                        icon: "error",
                                        title: "Oops...",
                                        text: "Password incorrect!",
                                    });
                                }
                            });
                    }
                });

            })
        })
    </script>
@endsection

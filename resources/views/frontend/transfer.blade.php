@extends('frontend.layouts.plainapp')
@section('title', 'Transfer')
@section('content')
    <div class="container mt-5">
        <div class="card mt-3">
            <div class="card-body">
                <h4>From</h4>
                <h6 class="text-muted">{{ $user->name }}</h6>
                <h6 class="text-muted">{{ $user->phone }}</h6>
                <form action="{{ route('user.transferComfirmScan') }}" method="POST" id="transfer_form">
                    @csrf
                    <input type="hidden" name="hash_value" id="hash">
                    <div class="form-group mb-3">
                        <label for="">To <span id="result" class="text-success fw-bold"></span> </label>
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-primary text-white" id="check"><i
                                    class="fa fa-check-circle" aria-hidden="true"></i></span>
                            <input id="to_phone" type="number" value="{{ old('to_phone') }}"
                                class="form-control @error('to_phone') is-invalid @enderror" name="to_phone">

                        </div>
                        @error('to_phone')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Amount(MMK)</label>
                        <input id="amount" type="number" class="form-control" name="amount"
                            value="{{ old('amount') }}">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <button class="btn btn-primary block w-100 submit" type="button" >Continue</button>
                </form>
            </div>
        </div>

    </div>
@endsection
@section('script')
    {!! JsValidator::formRequest('App\Http\Requests\TransferFormValidate', '#transfer_form') !!}
    <script>
        $(document).ready(function() {
            $("#check").on("click", function(e) {
                e.preventDefault();
                var to_phone = $('#to_phone').val();
                $.ajax({
                        method: "GET",
                        url: '/user/verify_account',
                        data: {
                            to_phone
                        },
                    })
                    .then(function(res) {
                        if (res.status === "success") {
                            $('#result').html('')
                            $('#result').append('( ' + res.message + ' )');
                        } else {
                            $('#result').html('')
                            $('#result').append('( ' + res.message + ' )');
                        }
                    });
            });


            $(".submit").on("click", function(e) {
                e.preventDefault();
                var to_phone = $('#to_phone').val();
                var amount = $('#amount').val();
                var description = $('#description').val();
                $.ajax({
                        method: "GET",
                        url: '/transactionHash',
                        data: {
                            to_phone,
                            amount,
                            description
                        }
                    })
                    .done(function(res) {
                        if (res.status === "success") {
                            $('#hash').val(res.hash_value);
                            $('#transfer_form').submit();
                        }
                    });
            });

        })
    </script>
@endsection

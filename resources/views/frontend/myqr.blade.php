@extends('frontend.layouts.plainapp')
@section('title', 'Receive Qr')
@section('content')
    <div class="container mt-3">
       <div class="card">
        <div class="card-body text-center">
            <h3 class="my-5">QR Scan to Pay Me</h3>
            <div class="text-center mt-5">
                <img class="my-2" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(200)->generate($user->phone)) !!} ">
            </div>
            <div class="mt-5">
                <h4>{{$user->name}}</h4>
                <h4>{{$user->phone}}</h4>
            </div>
        </div>
       </div>
    </div>
@endsection

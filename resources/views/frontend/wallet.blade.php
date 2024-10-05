@extends('frontend.layouts.plainapp')
@section('title', 'Wallet')
@section('content')
    <div class="container mt-3">
        <div class="card mt-3 wallet" >
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-uppercase">Balance</h6>
                    <h3>{{$user->wallet ? number_format($user->wallet->amount,2) : '_' }} MMK</h3>
                </div>
                <div class="mb-3">
                    <h6 class="text-uppercase">Account Number</h6>
                    <h3>{{$user->wallet ? $user->wallet->account_number : '_' }}</h3>
                </div>
                <h3>{{$user->wallet ? $user->name : '_' }}</h3>
            </div>
        </div>

    </div>
@endsection

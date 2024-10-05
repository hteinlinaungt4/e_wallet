@extends('frontend.layouts.plainapp')
@section('title', 'Home')
@section('content')
    <div class="container mt-3">
        <div class="profile my-3">
            <img src="https://ui-avatars.com/api/?size=110&background=3d4ad4&color=fff&name={{ Auth::guard('web')->user()->name }}"
                alt="">
            <h2 class="my-2">{{ $user->name }}</h2>
            <h5 class="my-3">{{ $user->wallet ? number_format($user->wallet->amount, 2) : '_' }} MMK</h5>
        </div>

        <div class="d-flex justify-content-between mt-3">
            <div class="card">
                <div class="card-body">
                   <a href="{{route('payqr')}}">
                        <img class="qrimg" src="{{ asset('frontend/scanner.png') }}" alt="">
                        <span>Scan & Pay</span>
                   </a>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <a href="{{route('receiveqr')}}">
                        <img class="qrimg" src="{{ asset('frontend/qr-code.png') }}" alt="">
                        <span>Receive QR</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-body">
                <a href="{{ route('user.transfer') }}">
                    <div class="row">
                        <div class="d-flex justify-content-between">
                            <div>
                                <img class="qrimg" src="{{ asset('frontend/money-transfer.png') }}" alt="">
                                <span>
                                    Transfer
                                </span>
                            </div>
                            <span>
                                <i class="fa-solid fa-caret-right"></i>
                            </span>
                        </div>
                    </div>
                </a>
                <hr>
                <a href="{{ route('user.wallet') }}">
                    <div class="row">
                        <div class="d-flex justify-content-between">
                            <div>
                                <img class="qrimg" src="{{ asset('frontend/wallet.png') }}" alt="">
                                <span>
                                    Wallet
                                </span>
                            </div>
                            <span>
                                <i class="fa-solid fa-caret-right"></i>
                            </span>
                        </div>
                    </div>
                </a>
                <hr>
                <a href="{{ route('user.transaction') }}">
                    <div class="row">
                        <div class="d-flex justify-content-between">
                            <div>
                                <img class="qrimg" src="{{ asset('frontend/transaction-history.png') }}" alt="">
                                <span>
                                    Transaction
                                </span>
                            </div>
                            <span>
                                <i class="fa-solid fa-caret-right"></i>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success...',
                text: '{{ session('success') }}',
            });
        @endif
    </script>

@endsection

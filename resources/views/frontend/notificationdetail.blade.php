@extends('frontend.layouts.plainapp')
@section('title', 'Notification Detail')
@section('content')
    <div class="container mt-3 ">
        <div class="infinite-scroll" >
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="text-center">
                            <div>
                                <img src="{{asset('frontend/notification-57.png')}}" width="350px" alt="">
                            </div>
                            <h3 class="mb-3">{{ $notification->data['title'] }}</h3>
                            <h5 class="mb-3">{{ $notification->data['message'] }}</h5>
                            <h6 class="mb-3">{{ Carbon\Carbon::parse($notification->created_at)->format('Y-m-d h:i:m A') }}</h6>
                            <a href="{{$notification->data['web_link']}}" class="btn btn-primary">Continue</a>
                        </div>
                    </div>
                </div>
        </div>
    </div>
@endsection


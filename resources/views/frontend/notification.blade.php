@extends('frontend.layouts.plainapp')
@section('title', 'Notification')
@section('content')
    <div class="container mt-3 ">
        <div class="infinite-scroll" >
            @foreach ($notifications as $notification)
            <a href="{{ route('user.notificationshow', $notification->id) }}">
                <div class="card mt-3">
                    <div class="card-body">
                        <div>
                            <h3><span><i class="fa-solid fa-bell @if(is_null($notification->read_at)) text-danger @endif" style="margin-right:10px; font-size:20px;"></i></span>{{ $notification->data['title'] }}</h3>
                            <h5>{{ Str::limit($notification->data['message'], 20, '...') }}</h5>
                            <small>{{ Carbon\Carbon::parse($notification->created_at)->format('Y-m-d h:i:m A') }}</small>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
            {{ $notifications->links() }}
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        $('ul.pagination').hide();
        $(function() {
            $('.infinite-scroll').jscroll({
                autoTrigger: true,
                // loadingHtml: '<img class="center-block" src="{{ asset('frontend/jscroll.png') }}" alt="Loading..." />',
                padding: 0,
                nextSelector: '.pagination li.active + li a',
                contentSelector: 'div.infinite-scroll',
                callback: function() {
                    $('ul.pagination').remove();
                }
            });
        });


    </script>
@endsection

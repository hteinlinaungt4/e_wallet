<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset('frontend/style.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body>
    <div class="container-fluid shadow-lg p-3">
       <div class="d-flex align-items-center justify-content-between">
            <h3>@yield('title')</h3>


            <a href="{{route('user.notification')}}" type="button" class="btn position-relative">
                <i class="fa-solid fa-bell fs-4"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{$noticount}}
                <span class="visually-hidden">unread messages</span>
                </span>
            </a>
       </div>
    </div>
   <div id="content">
    @yield('content')
   </div>
    <div id="footer" class="container-fluid shadow-lg">
        <div class="container mx-auto">
            <a href="{{route('payqr')}}">
               <div class="scan-tab">
                    <div class="inside">
                        <i class="fa-solid fa-qrcode fs-4"></i>
                    </div>
               </div>
            </a>
            <div class="row p-2 text-center">
                <div class="col-3">
                    <a href="{{route('user.home')}}">
                        <i class="fa-solid fa-home fs-5"></i>
                        <div>Home</div>
                   </a>
                </div>
                <div class="col-3">
                    <a href="{{route('user.wallet')}}">
                        <i class="fa-solid fa-wallet fs-5"></i>
                        <div>Wallet</div>
                   </a>
                </div>
                <div class="col-3">
                    <a href="{{route('user.transaction')}}">
                        <i class="fa-solid fs-5 fa-arrow-right-arrow-left"></i>
                        <div>Transaction</div>
                    </a>
                </div>
                <div class="col-3">
                   <a href="{{route('user.profile')}}">
                        <i class="fa-solid fa-user fs-5"></i>
                        <div>Profile</div>
                   </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    @yield('script')
    <script type="text/javascript" src="{{ url('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    <script src="{{asset('frontend/jsscroll/scroll.js')}}"></script>
    <script>
        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        })
    </script>
</body>
</html>

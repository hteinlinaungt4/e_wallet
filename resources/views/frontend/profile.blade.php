@extends('frontend.layouts.plainapp')
@section('title', 'Profile')
@section('content')
    <div class="container mt-3">
        <div class="profile">
            <img src="https://ui-avatars.com/api/?size=110&background=3d4ad4&color=fff&name={{ Auth::guard('web')->user()->name }}"
                alt="">
        </div>
        <div class="card mt-3">
            <div class="card-body">
                <div class="row">
                    <div class="d-flex justify-content-between">
                        <span>
                            UserName
                        </span>
                        <span>
                            {{ $user->name }}
                        </span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="d-flex justify-content-between">
                        <span>
                            Phone
                        </span>
                        <span>
                            {{ $user->phone }}
                        </span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="d-flex justify-content-between">
                        <span>
                            Email
                        </span>
                        <span>
                            {{ $user->email }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-body">
                <a href="{{ route('user.changepasswordpage') }}">
                    <div class="row">
                        <div class="d-flex justify-content-between">
                            <span>
                                Update Password
                            </span>
                            <span>
                                <i class="fa-solid fa-caret-right"></i>
                            </span>
                        </div>
                    </div>
                </a>
                <hr>
                <div class="row" id="logout">
                    <div class="d-flex justify-content-between">
                        <span>
                            <a href="#" tabindex="0" class="dropdown-item">Logout</a> <!-- Removed type="button" and changed href to "#" -->

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>

                        </span>
                        <span>
                            <i class="fa-solid fa-caret-right"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
    $(document).on('click', '#logout', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Are you sure you want to Logout?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logout-form').submit();
            // Remove the .done() method as it does not apply here
        }
    })
});

    </script>
@endsection

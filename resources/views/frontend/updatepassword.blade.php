@extends('frontend.layouts.plainapp')
@section('title', 'Profile')
@section('content')
    <div class="container mt-3">
        <div class="row align-items-center" style="min-height: 60vh">
          <div class="card">
            <div class="card-title">
                @session('fail')
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error </strong> {{session('fail')}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endsession
                <div class="text-center">
                    <img id="passwordupdate" src="{{asset('frontend/authentication-2-81.svg')}}">
                </div>
            </div>
            <div class="card-body">
                <form id="update" action="{{route('user.changepassword')}}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="">Old Password</label>
                        <input type="password" name="oldpassword" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">New Password</label>
                        <input type="password" name="newpassword" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Comfrim Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <button class="btn btn-primary inline-block w-100">Change Password</button>
                </form>
            </div>
          </div>
        </div>
    </div>
@endsection
@section('script')
    {!! JsValidator::formRequest('App\Http\Requests\UpdateUserPassword','#update') !!}

@endsection

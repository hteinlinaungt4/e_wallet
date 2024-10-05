@extends('backend.layouts.app')
@section('adminuser', 'mm-active')
@section('main_title', 'Admin User Management')
@section('content')
    <div class="row">
       <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    Admin User Create Form
                </div>
            </div>
            <div class="card-body">
                <form action="{{route('admin_user.store')}}" method="post" id="create">
                    @csrf
                    <div class="form-group">
                        <label for="">Name</label>
                        <input type="text" class="form-control" name="name" placeholder="Enter Name...">
                    </div>
                    <div class="form-group">
                        <label for="">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Enter Email...">
                    </div>
                    <div class="form-group">
                        <label for="">Phone</label>
                        <input type="number" class="form-control" name="phone" placeholder="Enter Phone...">
                    </div>
                    <div class="form-group">
                        <label for="">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Enter Password...">
                    </div>
                    <div>
                        <button type="button" class="btn btn-dark" id="cancel">Cancel</button>
                        <button class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
       </div>
    </div>
@endsection
@section('script')
{!! JsValidator::formRequest('App\Http\Requests\StoreAdminUser','#create') !!}
    <script>
        $(document).ready(function(){
            $('#cancel').on("click",function(){
                window.history.go(-1);
                return false;
            })
        })
    </script>
@endsection

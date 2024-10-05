@extends('backend.layouts.app')
@section('user', 'mm-active')
@section('main_title', 'Add Amount')
@section('content')
    <div class="row">
       <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                     Add Amount
                </div>
            </div>
            <div class="card-body">
                <form action="{{route('addamountstore')}}" method="post" id="adminedit">
                    @csrf
                    <div class="form-group">
                        <label for="">Name</label>
                       <select  name="user" id="" class="select form-control">
                            @foreach ($users as $user)
                                <option value="{{$user->id}}">{{$user->name}}</option>
                            @endforeach
                       </select>
                    </div>
                    <div class="form-group">
                        <label for="">Amount</label>
                        <input type="text" class="form-control" name="amount">
                    </div>
                    <div class="form-group">
                        <label for="">Note</label>
                        <textarea name="description"  rows="3" class="form-control"></textarea>
                    </div>

                    <div>
                        <button type="button" class="btn btn-dark" id="cancel">Cancel</button>
                        <button class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
       </div>
    </div>
@endsection
@section('script')
<script>
    $(document).ready(function() {
        $('.select').select2();
    });
</script>
@endsection

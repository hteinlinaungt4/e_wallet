@extends('frontend.layouts.plainapp')
@section('title', 'Transaction Detail')
@section('content')
    <div class="container mt-3" style="overflow-y:auto;min-height:100vh;">
        <div class="card">
            <div class="card-body">
                <div class="text-center my-3">
                    <img class="checkpng" src="{{asset('frontend/checked.png')}}" alt="">
                </div>
                <h3 class="text-center">
                    {!! ($transaction->type == "1")
                        ? '<span class="text-success">' . number_format($transaction->amount) . '  MMK</span>'
                        : '<span class="text-danger">- ' . number_format($transaction->amount) . '  MMK</span>'
                    !!}
                </h3>
                <div class="d-flex justify-content-between">
                    <h5 class="text-muted">Trx ID</h5>
                    <span>{{$transaction->trx_id}}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <h5 class="text-muted">Reference Number</h5>
                    <span>{{$transaction->ref_no}}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <h5 class="text-muted">Type</h5>
                    <span>
                        @if ($transaction->type == "1")
                            <span class="badge text-bg-success fs-6"> income </span>
                        @else
                            <span class="badge text-bg-danger fs-6"> expense </span>
                        @endif
                    </span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <h5 class="text-muted">Amount</h5>
                    <span>{{$transaction->amount}} MMK</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <h5 class="text-muted">Date and Time</h5>
                    <span>{{$transaction->created_at}}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <h5 class="text-muted">To</h5>
                    @if ($transaction->source_id == 0)
                        <span>Bank</span>
                    @else
                    <span>{{$transaction->source->name}}</span>
                    @endif
                </div>
                <hr>
                @if ($transaction->description)
                    <div class="d-flex justify-content-between">
                        <h5 class="text-muted">Note</h5>
                        <span>{{$transaction->description}}</span>
                    </div>
                    <hr>
                @endif

            </div>
        </div>
    </div>
@endsection

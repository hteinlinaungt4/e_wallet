@extends('frontend.layouts.plainapp')
@section('title', 'Pay Qr')
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <img src="{{ asset('frontend/qr-code-89.png') }}" width="300px" alt="">
                    <h6>Click button,put QR code in the frame and pay.</h6>
                </div>
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Scan
                </button>


                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <video id="scanner" width="350px" height="400px"></video>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('qr/qr-scanner.umd.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            var videoElem = document.getElementById('scanner');
            const qrScanner = new QrScanner(
                videoElem,
                function(result) {
                    console.log(result);
                    if (result) {
                        $('#exampleModal').modal('hide');
                        qrScanner.stop();
                        var to_phone = result;
                        window.location.replace(`user/transferScan?to_phone=${to_phone}`)
                    }
                }
            );

            $('#exampleModal').on('shown.bs.modal', function(event) {
                qrScanner.start();
            });

            $('#exampleModal').on('hidden.bs.modal', function(event) {
                qrScanner.stop();
            });

            @if ($errors->has('fail'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: "{{ $errors->first('fail') }}",
                    });
            @endif


        })
    </script>
@endsection

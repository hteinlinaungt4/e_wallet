@extends('frontend.layouts.plainapp')
@section('title', 'Transaction')
@section('content')
    <div class="container mt-3 ">
        <h4>Filter</h4>
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-between">
                    <div class=" text-end my-3">
                        <a href="" id="pdfDownload" class="text-decoration-none text-white btn btn-sm btn-dark ">
                            <i class="fa-solid fa-file-pdf"></i> Download
                        </a>
                    </div>
                    <div class="col-6">
                        <div class="input-group mb-3">
                            <label class="input-group-text p-1" for="inputGroupSelect02">Date</label>
                            <input type="text" class="form-control p-1" value="{{ request()->date ?? '' }}" id="datepicker" placeholder="All">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group mb-3">
                            <label class="input-group-text p-1" for="inputGroupSelect02">Types</label>
                            <select class="form-select type p-1" id="inputGroupSelect02">
                                <option value="">All</option>
                                <option value="1" @if (request()->type == '1') selected @endif>Income</option>
                                <option value="2" @if (request()->type == '2') selected @endif>Expense</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h4 class="mt-4">Transactions</h4>
        <div class="infinite-scroll">
            @foreach ($transactions as $transaction)
                <a href="{{ route('user.transactionDetail', $transaction->trx_id) }}">
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h6>Trx Id: <span> {{ $transaction->trx_id }}</span></h6>
                                <h6>
                                    {!! $transaction->type == '1'
                                        ? '<span class="text-success">' . number_format($transaction->amount) . ' MMK </span>'
                                        : '<span class="text-danger"> -' . number_format($transaction->amount) . ' MMK </span>' !!}
                                </h6>
                            </div>
                            <div>
                                <h6>
                                    @if ($transaction->type == 2)
                                        To
                                    @elseif ($transaction->type == 1)
                                        From
                                    @endif

                                    <span>{{ $transaction->source->name }}</span>
                                </h6>
                                <h6><span>{{ $transaction->created_at }}</span> </h6>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
            {{ $transactions->links() }}
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
        $('#datepicker').daterangepicker({
            "singleDatePicker": true,
            "autoApply": false,
            'autoUpdateInput':false,
            "locale": {
                "format": "YYYY-MM-DD",
            },
        });

        $('#datepicker').on('apply.daterangepicker', function(ev, picker) {

            $(this).val(picker.startDate.format("YYYY-MM-DD"));
            var date = $('#datepicker').val();
            var type = $('.type').val();
            history.pushState(null, '', `?date=${date}&type=${type}`);
            window.location.reload();
        });

        $('#datepicker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');

            var date = $('#datepicker').val();
            var type = $('.type').val();
            history.pushState(null, '', `?date=${date}&type=${type}`);
            window.location.reload();
        });


        // $('.type').change(function(e) {
        //     e.preventDefault();
        //     var date = $('#datepicker').val();
        //     var type = $('.type').val();
        //     history.pushState(null, '', `?date=${date}&type=${type}`);
        //     window.location.reload();
        // })
        $('.type').change(function(e) {
    e.preventDefault();

    // Get the type value
    var type = $('.type').val();

    // Get the date value
    var date = $('#datepicker').val();

    // Check if the date field has a value and if it's today's date
    var today = moment().format('YYYY-MM-DD');

    var query = [];

    // Only include the date if it's not empty and not today's date (assuming empty means no interaction)
    if (date && date !== today) {
        query.push(`date=${date}`);
    }

    // Include the type in the query
    if (type) {
        query.push(`type=${type}`);
    }

    if (!type) {
        query.push(`type=${type}`);
    }


    // Update the URL only if there's a query
    if (query.length > 0) {
        history.pushState(null, '', `?${query.join('&')}`);
    }

    // Reload the page
    window.location.reload();
});


        @if (session('message'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ session('message') }}",
                });
        @endif


        $('#pdfDownload').click(function(event) {
            event.preventDefault(); // prevent the default action of the <a> tag

            var date = $('#datepicker').val();
            var type = $('.type').val();

            // Open the PDF file in a new tab with the specified parameters
            var url = '/pdf?date=' + date + '&type=' + type;
            window.open(url, '_blank'); // Open the generated PDF in a new tab
        });
    </script>
@endsection

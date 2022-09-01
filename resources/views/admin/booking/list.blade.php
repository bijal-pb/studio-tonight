@extends('layouts.admin.master')
@section('content')
    <div class="subheader">
        <h1 class="subheader-title">
            <i class='subheader-icon fal fa-user-music'></i> Bookings <span class='fw-300'></span> <sup
                class='badge badge-primary fw-500'></sup>
            {{-- <small>
            Insert page description or punch line
        </small> --}}
        </h1>
    </div>
    <!-- Your main content goes below here: -->
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr">
                    <h2>
                        Bookings <span class="fw-300"><i></i></span>
                    </h2>
                    <div class="panel-toolbar">
                        <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10"
                            data-original-title="Collapse"></button>
                        <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip"
                            data-offset="0,10" data-original-title="Fullscreen"></button>
                        {{-- <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button> --}}
                    </div>
                </div>
                <div class="panel-container show">
                    <!-- <button type="button" id="btn-add" class="btn btn-primary float-right m-3" data-toggle="modal" data-target="#default-example-modal">Add Wholeseller</button> -->
                    <div class="panel-content">
                        <div id="categoryData">
                            {{-- <div class="category-filter">
                            <select id="categoryFilter" class="form-control">
                              <option value="">All</option>
                              <option value="1">Published</option>
                              <option value="0">Unpublished</option>
                            </select>
                        </div> --}}
                            <table id="booking-table"
                                class="table table-bordered table-hover table-striped w-100 dataTable dtr-inline">
                                <thead class="bg-primary-600">
                                    <tr>
                                        <th>Id </th>
                                        <th>User Name</th>
                                        <th>User Email</th>
                                        <th>Studio Title</th>
                                        <th>Booking Date</th>
                                        <th>Booking Price</th>
                                        <th>Action</th>
                                        <!-- <th>Action</th> -->
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.booking.detail')
    </div>
@endsection

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#booking-table').DataTable({
                responsive: true,
                serverSide: true,
                ajax: "{{ route('admin.booking.list') }}",
                columns: [{
                        data: 'id',
                        name: 'Id'
                    },
                    {
                        data: 'user_name',
                        name: 'User Name'
                    },
                    {
                        data: 'user_email',
                        name: 'User Email'
                    },
                    {
                        data: 'studio_name',
                        name: 'Studio Title'
                    },
                    {
                        data: 'booking_date',
                        name: 'Booking Date'
                    },
                    {
                        data: 'amount',
                        name: 'Booking Price'
                    },
                    {
                        data: 'action',
                        name: 'Action',
                        orderable: false,
                        searchable: true
                    },
                    //{data: 'action', name: 'Action', orderable: false, searchable: false},
                ],
                order: [0, 'desc'],
                lengthChange: true,
                dom: '<"float-left"B><"float-right"f>rt<"row"<"col-sm-4"l><"col-sm-4"i><"col-sm-4"p>>',
                // "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
                // "<'row'<'col-sm-12'tr>>" +
                // "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [{
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        titleAttr: 'Generate PDF',
                        className: 'btn-outline-primary btn-sm mr-1'
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        titleAttr: 'Generate Excel',
                        className: 'btn-outline-primary btn-sm mr-1'
                    },
                    {
                        extend: 'csvHtml5',
                        text: 'CSV',
                        titleAttr: 'Generate CSV',
                        className: 'btn-outline-primary btn-sm mr-1'
                    },
                    {
                        extend: 'copyHtml5',
                        text: 'Copy',
                        titleAttr: 'Copy to clipboard',
                        className: 'btn-outline-primary btn-sm mr-1'
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        titleAttr: 'Print Table',
                        className: 'btn-outline-primary btn-sm'
                    }
                ]
            });


            $(document).on("click", ".detail-booking", function() {

                var ajaxurl = $(this).data('url');
                $.ajax({
                    type: "GET",
                    url: ajaxurl,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        $('#user_name').val(data.data.user_name);
                        $('#studio_name').val(data.data.studio_name);
                        $('#booking_date').val(data.data.booking_date);
                        $('#start_time').val(data.data.start_time);
                        $('#end_time').val(data.data.end_time);
                        $('#discount').val('$ ' +data.data.discount);
                        $('#amount').val('$ '+data.data.amount);
                        $('#default-example-modal').modal('show');
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });

            });

        });
    </script>
@endsection

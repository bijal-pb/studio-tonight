@extends('layouts.admin.master')
@section('content')
<div class="subheader">
    <h1 class="subheader-title">
        <i class='subheader-icon fal fa-dollar-sign'></i> Transactions <span class='fw-300'></span> <sup class='badge badge-primary fw-500'></sup>
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
                    Transactions <span class="fw-300"><i></i></span>
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
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
                        <table id="booking-table" class="table table-bordered table-hover table-striped w-100 dataTable dtr-inline">
                            <thead class="bg-primary-600">
                                <tr>
                                    <th>Id </th>
                                    <th>User Name</th>
                                    <th>User Email</th>
                                    <th>Studio Title</th>
                                    <th>Booking Date</th>
                                    <th>Booking Price</th>
                                    <!-- <th>Status</th> -->
                                    <th><select id="statusFilter" class="form-control" style="width:100px">
                                            <option value="">All</option>
                                            <option value="1">Paid</option>
                                            <option value="0">Pay</option>
                                        </select></th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_js')
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#booking-table').DataTable({
            responsive: true,
            serverSide: true,
            ajax: {
                        url: "{{ route('admin.transaction.list') }}",
                        data: {
                            paid_to: function() { return $('#statusFilter').val() },
                        }
                    },
            // ajax: "{{ route('admin.transaction.list') }}",
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
                    data: 'paid_to',
                    name: 'Status',
                    orderable: false
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
        $('#statusFilter').change(function(){
            table.ajax.reload(null,false);
        });

    });
</script>
@endsection
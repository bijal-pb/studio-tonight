@extends('layouts.admin.master')
@section('content')
<ol class="breadcrumb page-breadcrumb">
    {{-- <li class="breadcrumb-item"><a href="javascript:void(0);">SmartAdmin</a></li>
    <li class="breadcrumb-item">Datatables</li>
    <li class="breadcrumb-item active">Basic</li> --}}
    <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
</ol>
<div class="subheader">
    <h1 class="subheader-title">
        <i class='subheader-icon fal fa-list-music'></i> Transaction Details<span class='fw-300'></span> <sup class='badge badge-primary fw-500'></sup>
    </h1>
    <div class="d-inline-flex flex-column justify-content-center mr-3">
        <span class="fw-300 fs-xl d-block opacity-50">
            <small>Booking Date</small>
        </span>
        <span class="fw-500 float-right fs-xxl d-block color-primary-500">
            {{$t_booking->booking_date}}
        </span>
    </div>
</div>
<!-- Your main content goes below here: -->
<div class="row">
    <div class="col-xl-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    Booking Details <span class="fw-300"><i></i></span>&nbsp;&nbsp;&nbsp;&nbsp; <span style="color:blue">{{$t_booking->booking_id}}</span>
                </h2>
                <div class="panel-toolbar">
                    <button type="button" id="{{ $t_booking->paid_to == 1 ? "paid" :"pay" }}"  class="paid btn btn-md btn-outline-primary active float-right m-3">{{ $t_booking->paid_to == 1 ? "Paid" :"Pay" }}</button>
                    <!-- <button type="button" id="pay" class="btn btn-md btn-outline-warning float-right m-3">Pay</button> -->
                    <input type="hidden" id="booking_id" value="{{$t_booking->id}}" />
                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    {{-- <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button> --}}
                </div>
            </div>
            <div class="panel-container show">
                {{-- <button type="button" id="btn-add" class="btn btn-primary float-right m-3" data-toggle="modal" data-target="#default-example-modal">Add User</button> --}}
                <div class="panel-content">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="p-3 bg-warning-500 rounded overflow-hidden position-relative text-white mb-g">
                                <h5 class="m-0 l-h-n">
                                    Customer Name
                                </h5>
                                <h3>
                                    <div class="number count">
                                        {{$t_booking->user_name}}
                                    </div>
                                </h3>
                                <i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="p-3 bg-warning-900 rounded overflow-hidden position-relative text-white mb-g">
                                <h5 class="m-0 l-h-n">
                                    Start Time
                                </h5>
                                <h3>
                                    <div class="number count">
                                        {{$t_booking->start_time}}
                                    </div>
                                </h3>
                                <i class="fal fa-clock position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="p-3 bg-danger-300 rounded overflow-hidden position-relative text-white mb-g">
                                <h5 class="m-0 l-h-n">
                                    End Time
                                </h5>
                                <h3>
                                    <div class="number count">
                                        {{$t_booking->end_time}}
                                    </div>
                                </h3>
                                <i class="fal fa-clock position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="p-3 bg-primary-300 rounded overflow-hidden position-relative text-white mb-g">
                                <h5 class="m-0 l-h-n">
                                    Total Hour
                                </h5>
                                <h3>
                                    <div class="number count">
                                        {{$t_booking->total_hour}}
                                    </div>
                                </h3>
                                <i class="fal fa-clock position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="p-3 bg-info-300 rounded overflow-hidden position-relative text-white mb-g">
                                <h5 class="m-0 l-h-n">
                                    Discount
                                </h5>
                                <h3>
                                    <div class="number count">
                                        ${{$t_booking->discount}}
                                    </div>
                                </h3>
                                <i class="fal fa-percent position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="p-3 bg-fusion-300 rounded overflow-hidden position-relative text-white mb-g">
                                <h5 class="m-0 l-h-n">
                                Fees
                                </h5>
                                <h3>
                                    <div class="number count">
                                        ${{$t_booking->fees}}
                                    </div>
                                </h3>
                                <i class="fal fa-inr position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="p-3 bg-warning-400 rounded overflow-hidden position-relative text-white mb-g">
                                <h5 class="m-0 l-h-n">
                                Amount
                                </h5>
                                <h3>
                                    <div class="number count">
                                        ${{$t_booking->amount}}
                                    </div>
                                </h3>
                                <i class="fal fa-inr position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="p-3 bg-danger-50 rounded overflow-hidden position-relative text-white mb-g">
                                <h5 class="m-0 l-h-n">
                                Studio Title
                                </h5>
                                <h3>
                                    <div class="number count">
                                        {{$t_booking->studio_name}}
                                    </div>
                                </h3>
                                <i class="fal fa-music position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_js')
<script type="text/javascript">
    $(document).ready(function(){


        $("#pay").click(function (e) {
            var formData = {
                booking_id: $('#booking_id').val(),
            };
            var ajaxurl = "{{ route('admin.transaction.studio') }}";
            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: formData,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                },
                success: function (data) {
                    if(data.status == 'success')
                    {
                        toastr['success'](data.message);
                        $('#pay').html("Paid");
                        $('#pay').removeClass("btn-outline-primary").addClass("btn-outline-warning active"); 
                    }
                    if(data.status == 'error')
                    {
                        toastr['error'](data.message);
                    }
                },
                error: function (data) {
                    toastr['error']('Something went wrong, Please try again!');
                    console.log('Error:', data);
                }
            });
        });
    });
    


</script>
@endsection

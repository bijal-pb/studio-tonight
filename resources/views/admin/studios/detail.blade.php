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
        <i class='subheader-icon fal fa-list-music'></i> Studio Details<span class='fw-300'></span> <sup class='badge badge-primary fw-500'></sup>
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
                    Studio Informations <span class="fw-300"><i></i></span>
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    {{-- <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button> --}}
                </div>
            </div>
            <div class="panel-container show">
                {{-- <button type="button" id="btn-add" class="btn btn-primary float-right m-3" data-toggle="modal" data-target="#default-example-modal">Add User</button> --}}
                <div class="panel-content">
                    <table class="table table-bordered table-hover m-0">
                        <tbody>
                            <tr>
                                <td style="width:200px">
                                    <span class="primary-link form-label">Title Of Studio</span>
                                </td>
                                <td>
                                    {{$studio->title}}
                                </td>
                            </tr>
                            <tr>
                                <td style="width:200px">
                                    <span class="primary-link form-label">Select Type of Studio</span>
                                </td>
                                <td>
                                    {{$studio->studio_type->name}}
                                </td>
                            </tr>
                            <tr>
                                <td style="width:200px">
                                    <span class="primary-link form-label">Price/Hour</span>
                                </td>
                                <td>
                                    $ {{$studio->price}}
                                </td>
                            </tr>
                            <tr>
                                <td style="width:200px">
                                    <span class="primary-link form-label">Description of Studio</span>
                                </td>
                                <td>
                                    {{$studio->description}}
                                </td>
                            </tr>
                            <tr>
                                <td style="width:200px">
                                    <span class="primary-link form-label">Rules</span>
                                </td>
                                <td>
                                    {{$studio->rules}}
                                </td>
                            </tr>
                            <tr>
                                <td style="width:200px">
                                    <span class="primary-link form-label">Cancellation Policy</span>
                                </td>
                                <td>
                                    {{$studio->cancel_policy}}
                                </td>
                            </tr>
                            <tr>
                                <td style="width:200px">
                                    <span class="primary-link form-label">Refund Policy</span>
                                </td>
                                <td>
                                    {{$studio->refund_policy}}
                                </td>
                            </tr>
                            <tr>
                                <td style="width:200px">
                                    <span class="primary-link form-label">Availability</span>
                                </td>
                                <td>
                                    {{$studio->availability}}
                                </td>
                            </tr>
                            <tr>
                                <td style="width:200px">
                                    <span class="primary-link form-label">Advance Booking Request</span>
                                </td>
                                <td>
                                @if($studio->timing->advance_booking == null)
                                  {{$studio->timing->advance_booking = '-'}}
                                @else
                                {{$studio->timing->advance_booking}}
                                @endif
                                </td>
                            </tr>
                            <tr>
                                <td style="width:200px">
                                    <span class="primary-link form-label">Minimum Booking Hour</span>
                                </td>
                                <td>
                                @if($studio->timing->min_booking == null )
                                 {{$studio->timing->min_booking = '-'}}
                                @else
                                {{$studio->timing->min_booking}}
                                @endif
                                    
                                </td>
                            </tr>
                            <tr>
                                <td style="width:200px">
                                    <span class="primary-link form-label">Maximum Studio Capacity</span>
                                </td>
                                <td>
                                @if($studio->timing->max_capacity == null)
                                {{$studio->timing->max_capacity = '-'}}
                                @else
                                {{$studio->timing->max_capacity}}
                                @endif
                                </td>
                            </tr>
                            <tr>
                                <td style="width:200px">
                                    <span class="primary-link form-label">Studio bookings</span>
                                </td>
                                <td>
                                    {{$studio->studio_booking->count()}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    Studio Amenities<span class="fw-300"><i></i></span>
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    {{-- <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button> --}}
                </div>
            </div>
            <div class="panel-container show">
                {{-- <button type="button" id="btn-add" class="btn btn-primary float-right m-3" data-toggle="modal" data-target="#default-example-modal">Add User</button> --}}
                <div class="panel-content">
                    <table class="table table-bordered table-hover m-0">
                        <tbody>
                            <tr>
                                <td style="width:200px">
                                    <span class="primary-link form-label">Amenities of Studio</span>
                                </td>
                                <td>
                                    @if(!$studio->studio_amenities->isEmpty())
                                    @php $tempName = []; @endphp
                                    @foreach($studio->studio_amenities as $k => $val)
                                    <?php
                                    $tempName[] = '<span class="badge badge-warning">' . $val->amenities->name . '</span>';
                                    ?>
                                    @endforeach
                                    @php $amName = implode(' , ', $tempName); @endphp
                                    {!! $amName !!}
                                    @else
                                    -
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    Studio Timing<span class="fw-300"><i></i></span>
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    {{-- <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button> --}}
                </div>
            </div>
            <div class="panel-container show">
                {{-- <button type="button" id="btn-add" class="btn btn-primary float-right m-3" data-toggle="modal" data-target="#default-example-modal">Add User</button> --}}
                <div class="panel-content">
                    <table class="table table-bordered table-hover m-0">
                        <tbody>
                            <td>
                                <table>
                                    <thead>
                                        <th>Mon</th>
                                        <th>Tue</th>
                                        <th>Wed</th>
                                        <th>Thu</th>
                                        <th>Fri</th>
                                        <th>Sat</th>
                                        <th>Sun</th>
                                    </thead>
                                    <tr>
                                        @if($studio->timing->mon_start_time == null || $studio->timing->mon_end_time == null)
                                        <td>Closed</td>
                                        @else
                                        <td>{{ date('h:i A',strtotime($studio->timing->mon_start_time)) }} - {{ date('h:i A',strtotime($studio->timing->mon_end_time)) }}</td>
                                        @endif
                                        @if($studio->timing->tue_start_time == null || $studio->timing->tue_end_time == null)
                                        <td>Closed</td>
                                        @else
                                        <td>{{ date('h:i A',strtotime($studio->timing->tue_start_time)) }} - {{ date('h:i A',strtotime($studio->timing->tue_end_time)) }}</td>
                                        @endif
                                        @if($studio->timing->wed_start_time == null || $studio->timing->wed_end_time == null)
                                        <td>Closed</td>
                                        @else
                                        <td>{{ date('h:i A',strtotime($studio->timing->wed_start_time)) }} - {{ date('h:i A',strtotime($studio->timing->wed_end_time)) }}</td>
                                        @endif
                                        @if($studio->timing->thu_start_time == null || $studio->timing->thu_end_time == null)
                                        <td>Closed</td>
                                        @else
                                        <td>{{ date('h:i A',strtotime($studio->timing->thu_start_time)) }} - {{ date('h:i A',strtotime($studio->timing->thu_end_time)) }}</td>
                                        @endif
                                        @if($studio->timing->fri_start_time == null || $studio->timing->fri_end_time == null)
                                        <td>Closed</td>
                                        @else
                                        <td>{{ date('h:i A',strtotime($studio->timing->fri_start_time)) }} - {{ date('h:i A',strtotime($studio->timing->fri_end_time)) }}</td>
                                        @endif
                                        @if($studio->timing->sat_start_time == null || $studio->timing->sat_end_time == null)
                                        <td>Closed</td>
                                        @else
                                        <td>{{ date('h:i A',strtotime($studio->timing->sat_start_time)) }} - {{ date('h:i A',strtotime($studio->timing->sat_end_time)) }}</td>
                                        @endif
                                        @if($studio->timing->sun_start_time == null || $studio->timing->sun_end_time == null)
                                        <td>Closed</td>
                                        @else
                                        <td>{{ date('h:i A',strtotime($studio->timing->sun_start_time)) }} - {{ date('h:i A',strtotime($studio->timing->sun_end_time)) }}</td>
                                        @endif
                                    </tr>
                                </table>
                            </td>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    Photos<span class="fw-300"><i></i></span>
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    {{-- <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button> --}}
                </div>
            </div>
            <div class="panel-container show">
                {{-- <button type="button" id="btn-add" class="btn btn-primary float-right m-3" data-toggle="modal" data-target="#default-example-modal">Add User</button> --}}
                <div class="panel-content">
                    <div class="row">
                        @if(!$studio->studio_photos->isEmpty())
                        @foreach($studio->studio_photos as $key => $val)
                        <div class="col-md-3">
                            <img src="{{ $val->photo }}" width="180" style="margin-bottom:10px" height="180" />
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    Location<span class="fw-300"><i></i></span>
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    {{-- <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button> --}}
                </div>
            </div>
            <div class="panel-container show">
                {{-- <button type="button" id="btn-add" class="btn btn-primary float-right m-3" data-toggle="modal" data-target="#default-example-modal">Add User</button> --}}
                <div class="panel-content">
                    <div class="row">
                        <table class="table table-bordered table-hover m-0">
                            <thead>
                                <th>city</th>
                                <th>address</th>
                                <th>area</th>
                                <th>state</th>
                                <th>country</th>
                            </thead>
                            <tr>
                                <td>{{$studio->city}}</td>
                                <td>{{$studio->address}}</td>
                                <td>{{$studio->area}}</td>
                                <td>{{$studio->state}}</td>
                                <td>{{$studio->country}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    Additional Services<span class="fw-300"><i></i></span>
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    {{-- <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button> --}}
                </div>
            </div>
            <div class="panel-container show">
                {{-- <button type="button" id="btn-add" class="btn btn-primary float-right m-3" data-toggle="modal" data-target="#default-example-modal">Add User</button> --}}
                <div class="panel-content">
                    <div class="row">
                        <table class="table table-bordered table-hover m-0">
                            <tbody>
                                <tr>
                                    <th>id</th>
                                    <th>service name</th>
                                    <th>fees</th>
                                    <th>description</th>
                                </tr>
                                @if(!$studio->services->isEmpty())
                                @foreach($studio->services as $key => $val)
                                <tr>
                                    <td>{{$val->id}}</td>
                                    <td>{{$val->name}}</td>
                                    <td>${{$val->fees}}</td>
                                    <td>{{$val->description}}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    About You<span class="fw-300"><i></i></span>
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    {{-- <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button> --}}
                </div>
            </div>
            <div class="panel-container show">
                {{-- <button type="button" id="btn-add" class="btn btn-primary float-right m-3" data-toggle="modal" data-target="#default-example-modal">Add User</button> --}}
                <div class="panel-content">
                    <div class="row">
                        <table class="table table-bordered table-hover m-0">
                            <tbody>
                                <tr>
                                    <td style="width:200px">
                                        <span class="primary-link form-label">User Name </span>
                                    </td>
                                    <td>
                                        {{$studio->studioUser->name}}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:200px">
                                        <span class="primary-link form-label">Mobile Number </span>
                                    </td>
                                    <td>
                                        {{$studio->studioUser->phone}}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:200px">
                                        <span class="primary-link form-label">Email</span>
                                    </td>
                                    <td>
                                        {{$studio->studioUser->email}}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:200px">
                                        <span class="primary-link form-label">verification type</span>
                                    </td>
                                    <td>
                                        {{$studio->studioUser->verification->name}}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:200px">
                                        <span class="primary-link form-label">About</span>
                                    </td>
                                    <td>
                                        {{$studio->studioUser->about}}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:200px">
                                        <span class="primary-link form-label">verification documents</span>
                                    </td>
                                    <td>
                                        <div class="row">
                                            @if(!empty($studio->verification_image))
                                            @foreach($studio->verification_image as $key => $val)
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <img src="{{ $val->image }}" width="100" style="margin:10px" height="100" />
                                                </div>
                                            </div>
                                            @endforeach
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
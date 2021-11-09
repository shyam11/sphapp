@extends('template.main')

@section('title', $title)


@section('content_title',__("Dashboard"))
@section('content_description',__("Operate All The Things Here"))
@section('breadcrumbs')
<ol class="breadcrumb">
    <li><a href="#"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
    <li class="active">Here</li>
</ol>

@endsection

@section('main_content')

@if(Auth::user()->user_type=='admin')
<div class="row">
    <form method="post" action="{{ route('filteradmin') }}">
         @csrf
    <div class="col-md-2">
        <div class="form-group">
            <label for="bill-type">{{ __('Bill Type') }} <span class="text-red">*</span></label>
            <select id="bill-type" type="select" class="form-control" name="bill_type">
                <option value="">Please Select Doctor</option>
                @foreach($bill_types as $bill_type => $bt)
                    <option value="{{$bill_type}}">{{$bt}}</option>
                @endforeach
            </select>
        </div>

    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="doctor-name">{{ __('Doctor Name') }} <span class="text-red">*</span></label>
            <select id="doctor-name" type="select" class="form-control" name="doctor_name">
                <option value="">Please Select Doctor</option>
                @foreach($doctors as $doctor)
                    <option value="{{$doctor->id}}">{{$doctor->name}}</option>
                @endforeach
            </select>
        </div>

    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="from-date">{{ __('From Date') }} <span class="text-red">*</span></label>
            <input  type="date" class="form-control" name="fromdate">
        </div>

    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="to-date">{{ __('To Date') }} <span class="text-red">*</span></label>
            <input  type="date" class="form-control" name="todate">
        </div>

    </div>
     <div class="col-md-2">
        <div class="form-group">
        <input type="submit" class="btn btn-info" id="submit" value="{{__('Search')}}">
        </div>
    </div>
    </form>
</div>
@endif

<div class="row">
    <div class="m-0 col-md-12">
        <div class="pl-0 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fas fa-user-md"></i></span>
                <div class="info-box-content">
                    <h3><b><span class="info-box-text">{{__('Doctors')}}</span></b></h3>
                    <span class="info-box-number">{{$doctorcnt}}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fas fa-id-card-alt"></i></span>

                <div class="info-box-content">
                    <h3><b><span class="info-box-text">{{__('General Staff')}}</span></b></h3>
                    <span class="info-box-number">{{$generalcnt}}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fas fa-briefcase-medical"></i></span>

                <div class="info-box-content">
                    <h3><b><span class="info-box-text">{{__('Pharmacists')}}</span></b></h3>
                    <span class="info-box-number">{{$pharmacistcnt}}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 pr-0 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fas fa-user-injured"></i></span>

                <div class="info-box-content">
                    <h3><b><span class="info-box-text">{{__('In Patients')}}</span></b></h3>
                    <span class="info-box-number">{{$inpatientcnt}}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
    </div>
</div>

{{--<div class="row">--}}
{{--    <div class="col-md-3">--}}
{{--        <div class="box box-info">--}}
{{--            <div class="box-header with-border">--}}
{{--                <h3 class="box-title">{{__('Quick Reports')}}</h3>--}}
{{--            </div>--}}
{{--            <div class="box-body list-group">--}}
{{--                @if(Auth::user()->user_type=='doctor' || Auth::user()->user_type=='admin')--}}
{{--                <a href="{{route('mon_stat_report')}}" class="list-group-item list-group-item-action btn btn-danger">--}}
{{--                   {{ __('Monthly Statistic Report')}}--}}
{{--                </a>--}}
{{--                @endif--}}
{{--                @if(Auth::user()->user_type=='doctor' || Auth::user()->user_type=='admin')--}}
{{--                <a href="{{route('stats')}}" class="list-group-item mt-4 list-group-item-action btn btn-warning">--}}
{{--                    {{__('Statistics')}}--}}
{{--                </a>--}}
{{--                @endif--}}

{{--                <a href="{{route('attendance_report')}}"--}}
{{--                    class="list-group-item mt-4 list-group-item-action btn btn-success">--}}
{{--                    {{__('Attendance Report')}}--}}
{{--                </a>--}}
{{--                @if(Auth::user()->user_type!='pharmacist')--}}
{{--                <a href="{{route('clinic_reports')}}" class="list-group-item mt-4 list-group-item-action btn btn-info">--}}
{{--                    {{__('Clinic Report')}}--}}
{{--                </a>--}}
{{--                @endif--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <div class="col-md-9">--}}
{{--        <div class="box box-default col-md-12">--}}

{{--            <div class="box-header with-border">--}}
{{--                <h3 class="box-title">{{__('Quick Links')}}</h3>--}}
{{--            </div>--}}

{{--            <div class="box-body">--}}
{{--                @if(Auth::user()->user_type!='pharmacist')--}}
{{--                <div class="col-sm-2">--}}
{{--                    <a href="{{route('patient')}}" class="btn btn-app">--}}
{{--                        <i class="ion ion-person-add"></i> {{__('Register out-patient')}}--}}
{{--                    </a>--}}
{{--                </div>--}}


{{--                <!-- ./col -->--}}
{{--                <div class="col-sm-2">--}}
{{--                    <a href="{{route('searchPatient')}}" class="btn btn-app">--}}
{{--                        <i class="ion ion-stats-bars"></i>{{__('Search Patient')}}--}}
{{--                    </a>--}}
{{--                </div>--}}


{{--                <!-- ./col -->--}}
{{--                <div class="col-sm-2">--}}
{{--                    <a href="{{route('register_in_patient_view')}}" class="btn btn-app">--}}
{{--                        <i class="fa fa-procedures"></i> {{__('Register in-Patient')}}--}}
{{--                    </a>--}}
{{--                </div>--}}

{{--                @if(Auth::user()->user_type!='general')--}}
{{--                <div class="col-sm-2">--}}
{{--                    <a href="{{route('check_patient_view')}}" class="btn btn-app">--}}
{{--                        <i class="fa fa-heartbeat"></i> {{__('Check Patient')}}--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--                @endif--}}
{{--                --}}


{{--                <!-- ./col -->--}}
{{--                <div class="col-sm-2">--}}
{{--                    <a href="{{route('create_channel_view')}}" class="btn btn-app">--}}
{{--                        <i class="fa fa-plus-square"></i> {{__('Create Appointment')}}--}}
{{--                    </a>--}}
{{--                </div>--}}

{{--                @endif--}}

{{--                @if(Auth::user()->user_type=='pharmacist' || Auth::user()->user_type=='admin')--}}
{{--                <!-- ./col -->--}}
{{--                <div class="col-sm-2">--}}
{{--                    <a href="{{route('issueMedicineView')}}" class="btn btn-app">--}}
{{--                        <i class="fa fa-medkit"></i> {{__('Issue Medicine')}}--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--                @endif--}}
{{--            </div>--}}
{{--            <div class="box-body">--}}

{{--                <div class="col-sm-2">--}}
{{--                    <a href="{{route('myattend')}}" class="btn btn-app">--}}
{{--                        <i class="fa fa-user"></i> {{__('My Attendance')}}--}}
{{--                    </a>--}}
{{--                </div>--}}

{{--                @if(Auth::user()->user_type=='admin')--}}
{{--                <!-- ./col -->--}}
{{--                <div class="col-sm-2">--}}
{{--                    <a href="{{route('newuser')}}" class="btn btn-app">--}}
{{--                        <i class="fa fa-user-plus"></i> {{__('Register User')}}--}}
{{--                    </a>--}}
{{--                </div>--}}


{{--                <!-- ./col -->--}}
{{--                <div class="col-sm-2">--}}
{{--                    <a href="{{route('regfinger')}}" class="btn btn-app">--}}
{{--                        <i class="fa fa-fingerprint"></i> {{__('Register Fingerprints')}}--}}
{{--                    </a>--}}
{{--                </div>--}}

{{--                <div class="col-sm-2">--}}
{{--                    <a href="{{route('resetuser')}}" class="btn btn-app">--}}
{{--                        <i class="fa fa-user-edit"></i> {{__('Reset Users')}}--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--                @endif--}}

{{--                <!-- ./col -->--}}
{{--                <div class="col-sm-2">--}}
{{--                    <a href="{{route('profile')}}" class="btn btn-app">--}}
{{--                        <i class="fa fa-home"></i> {{__('User Profile')}}--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--                @if(Auth::user()->user_type=='admin')--}}
{{--                <div class="col-sm-2">--}}
{{--                    <a href="{{route('createnoticeview')}}" class="btn btn-app">--}}
{{--                        <i class="fa fa-commenting"></i> {{__('Notices')}}--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--                @endif--}}
{{--            </div>--}}

{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
 @if(Auth::user()->user_type!='general')
<div class="row">
    <div class="col-md-9">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">{{__('Noticeboard')}}</h3>
            </div>
            <div class="box-body">

                @foreach ($notices as $note)
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                        <div class="d-flex w-100 justify-content-between">
                            <b>
                                <h4 class="mb-1">{{$note->subject}}</h4>
                            </b>
                            <small>{{$note->time}}</small>
                        </div>
                        <p class="mb-1">{{$note->description}}</p>
                        <small>By {{$note->name}} ({{$note->user_type}})</small>
                    </a>
                </div>
                @endforeach
                @if (count($notices)==0)
                <h3 class="text-center"><i class="fas fa-angle-double-left"></i>..........Empty..........<i
                        class="fas fa-angle-double-right"></i></h3>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">

        <!-- Calendar -->
        <div class="box box-solid bg-green-gradient">
            <div class="box-header">
                <i class="fa fa-calendar"></i>

                <h3 class="box-title">Calendar</h3>
                <!-- tools box -->
                <div class="pull-right box-tools">
                    <!-- button with a dropdown -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bars"></i></button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li><a href="#">Add new event</a></li>
                            <li><a href="#">Clear events</a></li>
                            <li class="divider"></li>
                            <li><a href="#">View calendar</a></li>
                        </ul>
                    </div>
                    <button type="button" class="btn btn-success btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-success btn-sm" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                </div>
                <!-- /. tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
                <!--The calendar -->
                <div id="calendar" style="width: 100%"></div>
            </div>
            <!-- /.box-body -->

        </div>
        <!-- /.box -->

    </div>
</div>
@endif

@if(Auth::user()->user_type=='general')
<div class="row">
    <div class="col-md-9">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">{{__('New Registration')}}</h3>
            </div>
            <div class="box-body">
                <div class="col-sm-12">
                    <br>
                    <table id="example1" class="table table-bordered table-striped dataTable"
                           role="grid" aria-describedby="example1_info">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($patients as $patient)
                            <tr>
                                <td>{{$patient->name}}</td>
                                <td>{{\Carbon\Carbon::parse($patient->bod)->diff(\Carbon\Carbon::now())->format('%y years')}}</td>
                                <td>{{$patient->sex}}</td>
                                <td>{{$patient->address}}</td>
                                <td><a href="patientedit/{{$patient->id}}">{{__('Edit')}}</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if (count($patients)==0)
                <h3 class="text-center"><i class="fas fa-angle-double-left"></i>..........No Record Found..........<i
                        class="fas fa-angle-double-right"></i></h3>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">

        <!-- Calendar -->
        <div class="box box-solid bg-green-gradient">
            <div class="box-header">
                <i class="fa fa-calendar"></i>

                <h3 class="box-title">Calendar</h3>
                <!-- tools box -->
                <div class="pull-right box-tools">
                    <!-- button with a dropdown -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bars"></i></button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li><a href="#">Add new event</a></li>
                            <li><a href="#">Clear events</a></li>
                            <li class="divider"></li>
                            <li><a href="#">View calendar</a></li>
                        </ul>
                    </div>
                    <button type="button" class="btn btn-success btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-success btn-sm" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                </div>
                <!-- /. tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
                <!--The calendar -->
                <div id="calendar" style="width: 100%"></div>
            </div>
            <!-- /.box-body -->

        </div>
        <!-- /.box -->

    </div>
</div>

<div class="row">
    <div class="col-md-9">
    <table class="table">
  <thead>
    <tr>
      <th>S.No</th>
      <th>Bill Type</th>
      <th>Doctor</th>
      <th>Count</th>
      <th>Sum</th>
    </tr>
  </thead>
  <tbody>
    @php($count=0)
    @foreach($generaldata as $data)
    @php($count++)
    <tr>
      <td>{{$count}}</td>
      <td>{{ucfirst($data->bill_type)}}</td>
      @foreach($users as $user)
      @if( $data->doctor_id == $user->id)
      <td>{{$user->name}}</td>
      @endif
      @endforeach
      <td>{{$data->total}}</td>
      <td>{{$data->total_amount}}</td>
    </tr>
    @endforeach
  </tbody>
</table>
</div>
</div>
@endif
@endsection
@section('optional_scripts')
<script>
    // The Calender
    $('#calendar').datepicker();
</script>
@endsection

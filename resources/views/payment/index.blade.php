@extends('template.main')

@section('title', $title)

@section('content_title',__('Payment List'))
{{--@section('content_description',__('Discharge In-Patients Here.'))--}}
@section('breadcrumbs')

    <ol class="breadcrumb">
        <li><a href="{{route('dash')}}"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
        <li class="active">Here</li>
    </ol>
@endsection

@section('main_content')
    {{--  discharge patient  --}}

{{--    <div @if (session()->has('regpsuccess') || session()->has('regpfail')) style="margin-bottom:0;margin-top:3vh" @else--}}
{{--    style="margin-bottom:0;margin-top:8vh" @endif class="row">--}}
{{--        <div class="col-md-1"></div>--}}
{{--        <div class="col-md-10">--}}
{{--            @if (session()->has('regpsuccess'))--}}
{{--                <div class="alert alert-success alert-dismissible">--}}
{{--                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>--}}
{{--                    <h4><i class="icon fa fa-check"></i> Success!</h4>--}}
{{--                    {{session()->get('regpsuccess')}}--}}
{{--                </div>--}}
{{--            @endif--}}
{{--            @if (session()->has('regpfail'))--}}
{{--                <div class="alert alert-danger alert-dismissible">--}}
{{--                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>--}}
{{--                    <h4><i class="icon fa fa-ban"></i> Error!</h4>--}}
{{--                    {{session()->get('regpfail')}}--}}
{{--                </div>--}}
{{--            @endif--}}

{{--        </div>--}}
{{--        <div class="col-md-1"></div>--}}

{{--    </div>--}}







    <div class="box box-info" id="disinpatient2" style="display:none">

        <div class="box-header with-border">
            <h3 class="box-title">{{__('Medical Officer - Abstract of Case')}}</h3>
        </div>

    </div>

    <div class="row mt-5 pt-5">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <div class="box box-info" id="disinpatient1">
{{--                <div class="box-header with-border">--}}
{{--                    <h3 class="box-title">{{__('Enter Registration No. Or Scan the bar code')}}</h3>--}}
{{--                </div>--}}
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="tab-pane @if (!session('unsuccess')&&!session('success')||session('successnotice')) active @endif"
                         id="activity">
                        <div class="box">
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                    <div class="row">
                                        @if (session('successnotice'))
                                            <div class="alert alert-success">
                                                {{ session('successnotice') }}
                                            </div>
                                        @endif

                                        <div class="col-md-2"></div>

                                        <div class="col-sm-12">
                                            <br>
                                            <table id="example1" class="table table-bordered table-striped dataTable"
                                                   role="grid" aria-describedby="example1_info">
                                                <thead>
                                                <tr>
                                                    <th>Patient ID</th>
                                                    <th>Patient Name</th>
                                                    <th>Doctor Name</th>
                                                    <th>Created BY</th>
                                                    <th>Bill Type</th>
                                                    <th>Amount</th>
                                                    <th>Payment Date</th>

                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($payments as $payment)
                                                    <tr>

                                                        <td>{{$payment->pid}}</td>
                                                        <td>{{$payment->name}}</td>
                                                        <td>{{isset($payment->dn) ? $payment->dn : ''}}</td>
                                                        <td>{{\App\Helpers\Traits\GetName::getName($payment->created_by)}}</td>
                                                        <td>{{$payment->bill_type}}</td>
                                                        <td>{{$payment->total_amount}}</td>
                                                        <td>{{\Carbon\Carbon::parse($payment->payment_date)->format('d-M-Y')}}</td>
                                                        {{--                                                        <td>{{$patient->sex}}</td>--}}
                                                        {{--                                                        <td>{{\Carbon\Carbon::parse($patient->bod)->format('d/M/Y')}}</td>--}}
                                                        {{--                                                        <td>--}}
                                                        {{--                                                            <a href="{{$patient->payment_id ? route('billpdf',[$patient->id, $patient->doctor_id,$patient->ipd,$patient->payment_id]) : route('pbill',[$patient->id])}}">{{$patient->payment_id ? 'Recipt' : 'Bill Genrate' }}</a>--}}

                                                        {{--                                                        </td>--}}
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                                <tfoot>
                                                <th>Patient ID</th>
                                                <th>Patient Name</th>
                                                <th>Doctor Name</th>
                                                <th>Bill Type</th>
                                                <th>Amount</th>
                                                <th>Payment Date</th>
                                                {{--                                                <th>Gender</th>--}}
                                                {{--                                                <th>Date of birth</th>--}}
                                                {{--                                                <th>Action</th>--}}
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>

                    {{--                    <div class="form-group">--}}
{{--                        <label for="pid" class="control-label" style="font-size:18px">{{__('Registration No:')}}</label>--}}
{{--                        <input type="number" required class="form-control" onchange="dischargeinpatientfunction()"--}}
{{--                               id="pid"--}}
{{--                               placeholder="Enter Registration No"/>--}}
{{--                    </div>--}}
{{--                    <div class="form-group">--}}
{{--                        <button type="button" class="btn btn-info"--}}
{{--                                onclick="dischargeinpatientfunction()">{{__('Enter')}}</button>--}}
{{--                    </div>--}}

                </div>
            </div>
        </div>

        <!-- /.box-footer -->

    </div>

    {{--    </div>--}}
    </div>

    <script>
        $(function () {

            $('#example1').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': false,
                'info': true,
                'autoWidth': false,
                'orderCellsTop': true,
                'fixedHeader': true,
                // this.api().columns( 5 ).every( function () {
                //
                // })
            })

        })

    </script>
@endsection

@extends('template.main')

@section('title', $title)

@section('content_title',__("Search Patient"))
@section('content_description',__("Search,View & Update Patient Details"))
@section('breadcrumbs')
<ol class="breadcrumb">
    <li><a href="#"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
    <li class="active">Here</li>
</ol>

@endsection

@section('main_content')

<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <form action={{route('searchData')}} method="GET" role="search">
            @csrf

            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
            @if (session('unsuccess'))
            <div class="alert alert-danger">
                {{ session('unsuccess') }}
            </div>
            @endif
            <div class="callout callout-info">
                <label class="h4">{{__('Search Patient With ...')}}</label>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-5">

                        <label class="mr-2">
                            <input onchange="changeFunc('Name');" style="display:inline-block" checked type="radio"
                                name="cat" id="cat" value="name">
                            {{__('Name')}}
                        </label>


                        <label class="ml-2 mr-4">
                            <input onchange="changeFunc('Telephone Number');" style="display:inline-block" type="radio"
                                name="cat" id="cat" value="telephone">
                            {{__('Telephone')}}
                        </label>


                        <label>
                            <input onchange="changeFunc('UHID No/Registration No');" style="display:inline-block" type="radio"
                                name="cat" id="cat" value="id">
                            {{__('UHID No/Registration No')}}
                        </label>
                    </div>
                    <div class="col-md-1"></div>
                </div>
                <script>
                    function changeFunc(txt){
                        document.getElementById("keyword").placeholder ="Enter Patient " +txt;
                    }
                </script>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <div class="input-group">
                            <input required type="text" value="{{$old_keyword}}" class="form-control" id="keyword" name="keyword"
                                placeholder="Enter Patient">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-default">
                                    <span class="glyphicon glyphicon-search"></span>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>

            </div>
    </div>
    <div class="col-md-1"></div>
</div>

</form>
@if(!$search_result)
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
{{--                    <div class="col-md-10">--}}
{{--                        <br>--}}
{{--                        <form class="form-inline" method="POST" action="{{route('addnotice')}}">--}}
{{--                            @csrf--}}
{{--                            <div class="form-group mb-2">--}}
{{--                                <input type="text" class="form-control" name="subject"--}}
{{--                                       placeholder="enter subject">--}}
{{--                            </div>--}}
{{--                            <div class="form-group mx-sm-3 mb-2">--}}
{{--                                <input type="text" class="form-control" name="description"--}}
{{--                                       placeholder="enter description">--}}
{{--                            </div>--}}
{{--                            <button type="submit" class="btn btn-warning mb-2">Add</button>--}}
{{--                        </form>--}}
{{--                    </div>--}}
                    <div class="col-md-2"></div>

                    <div class="col-sm-12">
                        <br>
                        <table id="example1" class="table table-bordered table-striped dataTable"
                               role="grid" aria-describedby="example1_info">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Gender</th>
                                <th>Date of birth</th>
                                <th>Action</th>

                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($patients as $patient)
                                <tr>
                                    <td>{{$patient->name}}</td>
                                    <td>{{$patient->address}}</td>
                                    <td>{{$patient->sex}}</td>
                                    <td>{{\Carbon\Carbon::parse($patient->bod)->format('d/M/Y')}}</td>
                                    <td><a href="{{route('mbill',[$patient->id])}}">Bill genrate</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Gender</th>
                            <th>Date of birth</th>
                            <th>Action</th>

                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
    </div>
</div>
@endif
@if($search_result)
@if(!$search_result->isEmpty())

@foreach($search_result as $patient)
{{-- Search Results --}}
<div class="row">
    <!-- right column -->
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <!-- Horizontal Form -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">{{__('Search Results')}}</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->

            <form class="form-horizontal" action="{{route('editpatient')}}" method="POST">
                @csrf
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">{{__('Patient ID')}}</label>
                        <div class="col-sm-10">
                            <input readonly value="{{$patient->id}}" type="text" required class="form-control"
                                name="reg_pname">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">{{__('Full Name')}}</label>
                        <div class="col-sm-10">
                            <input readonly value="{{$patient->name}}" type="text" required class="form-control"
                                name="reg_pname" placeholder="Enter Patient Full Name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">{{__('Adhar')}}</label>
                        <div class="col-sm-10">
                            <input readonly value="{{$patient->nic}}" type="text" required class="form-control"
                                name="reg_pnic" placeholder="National Identity Card Number">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">{{__('Address')}}</label>
                        <div class="col-sm-10">
                            <input readonly type="text" value="{{$patient->address}}" required class="form-control"
                                name="reg_paddress" placeholder="Enter Patient Address ">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">{{__('Telephone')}}</label>
                        <div class="col-sm-10">
                            <input readonly value="{{$patient->telephone}}" type="tel" class="form-control"
                                name="reg_ptel" placeholder="Patient Telephone Number">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">{{__('Occupation')}}</label>
                        <div class="col-sm-10">
                            <input readonly value="{{$patient->occupation}}" type="text" required class="form-control"
                                name="reg_poccupation" placeholder="Enter Patient Occupation ">
                        </div>
                    </div>
                    <!-- select -->
                    <div class="form-group">

                        <label class="col-sm-2 control-label">{{__('Sex')}}</label>
                        <div class="col-sm-2 mr-0 pr-0">
                            <input readonly value="{{$patient->sex}}" type="text" required class="form-control"
                                name="reg_poccupation" placeholder="Enter Patient Occupation ">
                        </div>

                        <label class="col-sm-2 control-label">{{__('DOB')}}</label>
                        <div class="col-sm-3">
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input readonly value="{{$patient->bod}}" type="text" class="form-control pull-right"
                                    name="reg_pbd" placeholder="Birthday">
                                <input readonly value="{{$patient->id}}" type="text" class="form-control pull-right"
                                    name="reg_pid" style="display:none">

                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="btn-group pull-right" role="group" aria-label="Button group">
                                <a type="button" href="{{url('patient/'.$patient->id)}}" class="btn bg-navy"><i class="far fa-id-card"></i> {{__('Profile')}}</a>
                            <button @if($patient->trashed()) type="button" disabled @endif class="btn btn-warning"><i class="fas fa-edit"></i> {{__('Edit')}}</button>
                            </div>

                        </div>



                    </div>
                </div>

            </form>
        </div>
    </div>

    <div class="col-md-1"></div>
</div>
@endforeach
<script>
    function go(pid){
        window.location.href = "/patient/"+pid;
    }
</script>
@else
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <h4>{{__('No results found...')}}</h4>
    </div>
    <div class="col-md-1"></div>
</div>

@endif
@endif
<script>
    $(function () {

        $('#example1').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': false,
            'info': true,
            'autoWidth': false
        })
    })

</script>
@endsection

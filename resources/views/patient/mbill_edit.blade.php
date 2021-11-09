@extends('template.main')

@section('title', $title)

@section('content_title',__('Patient Bill'))

{{--@section('content_description',__("Register New Out Patients Here"))--}}
@section('breadcrumbs')

    <ol class="breadcrumb">
        <li><a href="{{route('dash')}}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li class="active">Here</li>
    </ol>
@endsection

@section('main_content')
    <div class="main-container">
    <div class="container">
{{--        <div class="text-center logo">Logo</div>--}}
    <form class="med-form" method="post"  action="{{ route('bill_payment') }}">
        {{csrf_field()}}
        <div class="row">
            <div class="col-lg-8">


                <div class="form-row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" value="{{$patient->name}}" name="name" class="form-control" id="name" placeholder="Enter Full Name">
                            <input type="hidden" value="{{$patient->id}}" name="pid" class="form-control" id="name" placeholder="Enter Full Name">

                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="age">Age</label>
                            <input type="text" class="form-control" readonly name="age" value="{{\Carbon\Carbon::parse($patient->bod)->age}}" id="age" placeholder="Enter Age">
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="age">Gender</label>
                            <input type="text" class="form-control" readonly name="gender" value="{{$patient->sex}}" id="age" placeholder="Enter Age">
{{--                            <input type="hidden" class="form-control" readonly name="gender" value="{{$inpatient->sex}}" id="age" placeholder="Enter Age">--}}

                        </div>
                    </div>

{{--                    <div class="col-lg-4">--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="name">IPO No.</label>--}}
{{--                            <input type="text" class="form-control" readonly id="name" name="ipo" placeholder="Enter IPO No.">--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="g-name">Mobile Number</label>
                            <input type="text" class="form-control" readonly value="{{$patient->telephone}}" id="telephone" placeholder="Enter Mobile Number">
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="g-name">Consultant Name</label>
{{--                            <select required class="form-control" name="doctor_id">--}}
{{--                                <option  value="_none">Select</option>--}}
{{--                                @foreach ($doctors as $doctor)--}}
{{--                                    <option  value="{{$doctor->id}}">{{$doctor->name}}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
                            <input type="text" class="form-control" readonly value="{{$doctor}}" id="doctor" placeholder="Doctor">
                            <input type="hidden" name="doctor_id" class="form-control" readonly value="{{$doctorId}}" id="doctor" placeholder="Doctor">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="name">Bill Type</label>
                            <select class="form-control" required name="bill_type">
                                <option value="_none">Select</option>
                                <option value="x-ray">X-ray</option>
                                <option value="medicine">Medicine</option>
                                <option value="general">General</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="form-group">
                    <label for="g-name">Address</label>
                    <textarea class="form-control" id="age" value="{{$patient->address}}" readonly placeholder="Enter Address">{{$patient->address}}</textarea>
                </div>
            </div>
        </div>

        <div class="form-row">
{{--            <div class="col-lg-2">--}}
{{--                <div class="form-group">--}}
{{--                    <label for="name">Admit Date</label>--}}
{{--                    <input type="date" required  class="form-control pull-right"--}}
{{--                           name="admit_date" placeholder="Admit Date">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-lg-2">--}}
{{--                <div class="form-group">--}}
{{--                    <label for="name">Guardian Name</label>--}}
{{--                    <input type="text" required  class="form-control pull-right"--}}
{{--                           value="{{$patient->guardian}}" readonly placeholder="Guardian Name">--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>


{{--            <div class="col-lg-2">--}}
{{--                <div class="form-group">--}}
{{--                    <label for="name">UHID No.</label>--}}
{{--                    <input type="text" class="form-control" id="name" placeholder="Enter UHID No.">--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="col-lg-2">--}}
{{--                <div class="form-group">--}}
{{--                    <label for="name">IPO No.</label>--}}
{{--                    <input type="text" class="form-control" id="name" placeholder="Enter IPO No.">--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="col-lg-2">--}}
{{--                <div class="form-group">--}}
{{--                    <label for="name">Room No.</label>--}}
{{--                    <input type="text" class="form-control" id="name" placeholder="Enter Room No.">--}}
{{--                </div>--}}
{{--            </div>--}}



{{--            <div class="col-lg-2">--}}
{{--                <div class="form-group">--}}
{{--                    <label for="name">Patient Status</label>--}}
{{--                    <select class="form-control">--}}
{{--                        <option>Improved</option>--}}
{{--                    </select>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="col-lg-3">--}}
{{--                <div class="form-group">--}}
{{--                    <label for="name">Bill Date.</label>--}}
{{--                    <input type="text" class="form-control datepicker" id="name" placeholder="Enter Bill Date" autocomplete="off">--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="col-lg-3">--}}
{{--                <div class="form-group">--}}
{{--                    <label for="name">Admit Date.</label>--}}
{{--                    <input type="text" class="form-control datepicker" id="name" placeholder="Enter Bill Date" autocomplete="off">--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="col-lg-3">--}}
{{--                <div class="form-group">--}}
{{--                    <label for="name">Dis. Date</label>--}}
{{--                    <input type="text" class="form-control datepicker" id="name" placeholder="Enter Bill Date" autocomplete="off">--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="col-lg-3">--}}
{{--                <div class="form-group">--}}
{{--                    <label for="name">No of Days</label>--}}
{{--                    <input type="text" class="form-control" id="name" placeholder="Enter Bill Date" autocomplete="off">--}}
{{--                </div>--}}
{{--            </div>--}}

            <div class="col-lg-12">
                <div class="d-flex justify-content-between mt-4">
                    <h3>Add Items</h3>

                    <button type="button" id="addrow" class="btn btn-primary btn-sm">Add More</button>
                </div>
                <table id="items" class="table detail-table">

                    <tr>
                        <th>S.No.</th>
                        <th>Service</th>

                        <th>Rate</th>
                        <th>QTY.</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>

                    <tr class="item-row">
                        <td></td>
                        <td class="description"><input required type="text" class="form-control" name="service[0][desc]"></td>
                        <td><input required type="number" class="form-control reg_rate" name="service[0][rate]"></td>
                        <td><input required type="number" class="form-control reg_unit" name="service[0][unit]"></td>
                        <td><input required type="number" class="form-control reg_amount" name="service[0][amount]"></td>
                        <td><button type="button" class="delete btn btn-sm btn-danger">Remove</button></td>
                    </tr>

{{--                    <tr class="item-row">--}}
{{--                        <td></td>--}}
{{--                        <td class="description"><input type="text" class="form-control"></td>--}}
{{--                        <td><input type="text" class="form-control"></td>--}}
{{--                        <td><input type="text" class="form-control"></td>--}}
{{--                        <td><input type="text" class="form-control"></td>--}}
{{--                        <td><button type="button" class="delete btn btn-sm btn-danger">Remove</button></td>--}}
{{--                    </tr>--}}

                </table>
            </div>


            <div class="col-lg-12 text-center">
                <div class="bnt-group mt-4">
{{--                    <button type="button" class="btn btn-lg btn-outline-success">Preview</button>--}}

                    <button type="submit" class="btn btn-success btn-lg">Submit</button>
                </div>
            </div>

        </div>
    </form>
    </div>
    </div>
    <script>
        var today, datepicker;
        today = new Date(
            new Date().getFullYear(),
            new Date().getMonth(),
            new Date().getDate()
        );
        $('.datepicker').each(function(){
            $(this).datepicker({
                todayHighlight:true,
                format:'yyyy-mm-dd',
                autoclose:true,
                minDate: today,
                header: true
            })
        });
    </script>

    <script>
        $(document).ready(function() {
        var count = 0;
            $("#addrow").click(function(){
                count++;
                $(".item-row:last").after('<tr class="item-row"><td></td><td class="description"><input required type="text" class="form-control" name="service['+count+'][desc]"></td><td><input required type="number" class="form-control reg_rate" name="service['+count+'][rate]"></td><td><input required type="number" class="form-control reg_unit" name="service['+count+'][unit]"></td><td><input required type="number" class="form-control reg_amount" name="service['+count+'][amount]"></td><td><button type="button" class="delete btn btn-sm btn-danger">Remove</button></td></tr>');
                if ($(".delete").length > 0) $(".delete").show();
            });

            $(".delete").on('click',function(){
                $(this).parents('.item-row').remove();
                if ($(".delete").length < 2) $(".delete").hide();
            });

            $(document).on('focusout','.reg_rate', function(){
                var rate = $(this).val();
                var unit = $(this).parent().next().children().val();
                $(this).parent().next().next().children().val(rate*unit);
            });
            $(document).on('focusout','.reg_unit', function(){
                var unit = $(this).val();
                var rate = $(this).parent().prev().children().val();
                $(this).parent().next().children().val(rate*unit);
            });
        });
    </script>
@endsection

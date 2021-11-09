@extends('template.main')

@section('title', $title)

@section('content_title',__('Patient Discharge Form'))

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
    <form class="med-form" method="post"  action="{{ route('bill_payment_update',[$payment->id]) }}">
        {{csrf_field()}}
        <div class="row">
            <div class="col-lg-8">


                <div class="form-row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" readonly value="{{$patient->name}}" name="name" class="form-control" id="name" placeholder="Enter Full Name">
                            <input type="hidden"  value="{{$patient->id}}" name="pid" class="form-control" id="name" placeholder="Enter Full Name">

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
                            <input  type="text" class="form-control" readonly value="{{$patient->telephone}}" id="telephone" placeholder="Enter Mobile Number">
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
                            <input type="text" class="form-control" readonly value="{{$doctor->name}}" id="doctor" placeholder="Doctor">
                            <input type="hidden" name="doctor_id" class="form-control" readonly value="{{$doctor->id}}" id="doctor" placeholder="Doctor">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="name">Guardian Name</label>
                            <input type="text" required  class="form-control pull-right"
                                   value="{{$patient->guardian}}" readonly placeholder="Guardian Name">
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

            <div class="col-lg-4">
                <div class="form-group">
                    <label for="g-name">DOA</label>
                        <input type="datetime-local" name="admit_date" class="" value="<?php echo date('Y-m-d\TH:i', strtotime($inpatient->created_at)); ?>">
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="g-name">DOD</label>
                        <input type="datetime-local" name="discharged_date" class="datetime-local" value="<?php echo date('Y-m-d\TH:i', strtotime($inpatient->discharged_date)); ?>" required>
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
{{--                    <label for="name">Room Category</label>--}}
{{--                    <select class="form-control">--}}
{{--                        <option>General Ward</option>--}}
{{--                    </select>--}}
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
                        <th>Date</th>
                        <th>Service</th>
                        <th>Rate</th>
                        <th>QTY.</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>

                    @php($count=0)
                    <?php $service_name1 = json_decode($payment->service_name);
                        $service_name = (array) $service_name1;
                        // print_r($service_name);die;
                     ?>
                    <?php for($k=0;$k<count($service_name['desc']);$k++) { ?>
                    
                        @php($count++)
                        <tr class="item-row">
                            <td>
                            </td>
                            <td class="description col-sm-2"><input required type="date" class="form-control" name="service[date][]" value="<?php echo $service_name['date'][$k]; ?>"></td>
                            <td class="description col-sm-2"><input required type="text" class="form-control" value="<?php echo $service_name['desc'][$k]; ?>" name="service[desc][]"></td>

                            <td class="col-sm-2"><input required type="number" class="form-control reg_rate" value="<?php echo $service_name['rate'][$k]; ?>" name="service[rate][]"></td>
                            <td class="col-sm-1"><input required type="number" class="form-control reg_unit" value="<?php echo $service_name['unit'][$k]; ?>" name="service[unit][]"></td>

                            <td class="col-sm-2"><input required type="number" value="<?php echo $service_name['amount'][$k]; ?>" class="form-control reg_amount" name="service[amount][]"></td>
                            <td><button type="button" class="delete btn btn-sm btn-danger">Remove</button></td>

                        </tr>
                    <?php }?>
{{--                    <tr class="item-row">--}}
{{--                        <td></td>--}}
{{--                        <td class="description col-sm-2"><input required type="date" class="form-control" name="service[date][]"></td>--}}
{{--                        <td class="description col-sm-2"><input required type="text" class="form-control" name="service[desc][]"></td>--}}
{{--                        <td class="col-sm-2"><input required type="number" class="form-control reg_rate" name="service[rate][]"></td>--}}
{{--                        <td class="col-sm-1"><input required type="number" class="form-control reg_unit" name="service[unit][]"></td>--}}
{{--                        <td class="col-sm-2"><input required type="number" class="form-control reg_amount" name="service[amount][]"></td>--}}
{{--                        <td><button type="button" class="delete btn btn-sm btn-danger">Remove</button></td>--}}
{{--                    </tr>--}}

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
                $(".item-row:last").after('<tr class="item-row"><td></td><td class="description col-sm-2"><input required type="date" class="form-control" name="service[date][]"></td><td class="description col-sm-2"><input required type="text" class="form-control" name="service[desc][]"></td><td class="col-sm-2"><input required type="number" class="form-control reg_rate" name="service[rate][]"></td><td class="col-sm-1"><input required type="number" class="form-control reg_unit" name="service[unit][]"></td><td class="col-sm-2"><input required type="number" class="form-control reg_amount" name="service[amount][]"></td><td><button type="button" class="delete btn btn-sm btn-danger">Remove</button></td></tr>');
                if ($(".delete").length > 0) $(".delete").show();
            });

            $(document).on('click','.delete', function(){
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

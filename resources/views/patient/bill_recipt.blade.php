@extends('template.main')

@section('title', $title)

@section('content_title',__('Patient Registration'))

@section('content_description',__("Register New Out Patients Here"))
@section('breadcrumbs')

    <ol class="breadcrumb">
        <li><a href="{{route('dash')}}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li class="active">Here</li>
    </ol>
@endsection

@section('main_content')
    <div style="max-width: 800px; width: 100%; margin: 0 auto;">
        <table style="border-collapse: collapse;width: 100%;">
            <tr>
                <td colspan="2" align="center" style="font-size: 30px;">
                    <strong>Shree Pradhan Healthcare private limited</strong>
                    <h5>Reg. Off-At -Khabra P.S-Sadar,Musahari, Muzaffarpur Bihar PIN-842001</h5>
                    <h5>CIN-U85110BR2020PTCO47423</h5>
                    <h5>Email:shreepradhanhospital@gmail.com | Ph. 9199654999</h5>
                    <h5><strong>Discharge Receipt</strong></h5>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid;padding: 10px;">
                    <table>
                        <tr>
                            <td><strong>Name</strong></td>
                            <td>: <strong>{{$patient->name}} </strong></td>
                        </tr>
                        <tr>
                            <td><strong>Age & Gender</strong></td>
                            <td>: <strong> <span style="padding-left: 10px;">{{\Carbon\Carbon::parse($patient->bod)->age}} Years | {{$patient->sex}}</span></strong></td>
                        </tr>
                        <tr>
                            <td><strong>Guardian Name</strong></td>
                            <td>: <strong>{{$patient->guardian}} </strong></td>
                        </tr>

                        <tr>
                            <td><strong>Address</strong></td>
                            <td>: {{$patient->address}}</td>
                        </tr>

                        <tr>
                            <td><strong>Mobile No.</strong></td>
                            <td>: {{$patient->telephone}}</td>
                        </tr>

                        <tr>
                            <td><strong>Consultant Name</strong></td>
                            <td>: DR. {{$doctor->name}}</td>
                        </tr>
                    </table>
                </td>
                <td style="border: 1px solid;padding: 10px;">                    <table>
                        <tr>
                            <td><strong>Bill No.</strong></td>
                            <td>: {{$payment->id}}</td>
                        </tr>
                        <tr>
                            <td><strong>IPD</strong></td>
                            <td>: {{$inpatient->id}}</td>
                        </tr>

                        <tr>

                            <td><strong>Bill Date</strong></td>
                            <td>: {{\Carbon\Carbon::parse($payment->created_at)->format('d-M-Y')}}</td>
{{--                            <td>: {{\Carbon\Carbon::parse($payment->created_at)->format('d-M-Y')}}</td>--}}
                        </tr>

                        <tr>
                            <td>UHID No.</td>
                            <td>: <strong>{{$patient->id}}</strong></td>
                        </tr>

                        <tr>
                            <td><strong>DOA</strong></td>
                            <td>: {{\Carbon\Carbon::parse($inpatient->admit_date)->format('d-M-Y h:i a')}}</td>
                        </tr>

                        <tr>
                            <td><strong>DOD</strong></td>
                            <td>: {{isset($inpatient->discharged_date) ? \Carbon\Carbon::parse($inpatient->discharged_date)->format('d-M-Y h:i a') : ''}}</td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>

        <table style="border-collapse: collapse;width: 100%; border: 1px solid; text-align: center;">+

            <tr>
                <td style="border-bottom: 1px solid;"><strong>S.No.</strong></td>
                <td style="border-bottom: 1px solid;"><strong>Date</strong></td>
                <td style="border-bottom: 1px solid;"><strong>Description</strong></td>
                <td style="border-bottom: 1px solid;"><strong>Rate</strong></td>
                <td style="border-bottom: 1px solid;"><strong>QTY.</strong></td>
                <td style="border-bottom: 1px solid;"><strong>Amount</strong></td>
            </tr>
            @php($count=0)
            <?php $service_name1 = json_decode($payment->service_name);
                $service_name = (array) $service_name1;
                //print_r($service_name);die;
             ?>
            <?php for($k=0;$k<count($service_name['desc']);$k++) { ?>
            
                @php($count++)
                <tr>
                    <td>{{$count}}
                    </td>
                    <td style="text-align: left;">
                        <?php echo date('d/m/Y',strtotime($service_name['date'][$k])); ?>
                    </td>
                    <td style="text-align: left;">
                        <?php echo $service_name['desc'][$k]; ?>
                    </td>
                    <td>
                            <?php echo $service_name['rate'][$k]; ?>
                    </td>
                    <td>
                        <?php echo $service_name['unit'][$k]; ?>
                    </td>

                    <td>
                        <?php echo $service_name['amount'][$k]; ?>
                    </td>
                </tr>
            <?php }?>
           

            <tr>
                <td colspan="3" rowspan="1" style="border-top:1px solid; text-align: left;">
                    <div><strong style="border-bottom: 1px solid;"></strong></div>
                    <div style="padding-top: 10px;"></div>
{{--                    <div style="max-width: 80%; padding-top: 10px;">dslajf ldsajf; jasl;f jlksa jfdjdsaf lkjdsa f;lkdsajf ;jdsaf ;lks jfda;sjaf;l dsaf</div>--}}
                </td>

                <td style="border-top:1px solid;"><strong>Total Amount :</strong></td>
                <td style="border-top:1px solid;"><strong>{{number_format($total_amount, 2)}}</strong></td>
            </tr>
{{--            <tr>--}}
{{--                <td>Discount: </td>--}}
{{--                <td>6457</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td><strong>Net Amount : </strong></td>--}}
{{--                <td>432432</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Payment Recd</td>--}}
{{--                <td>43243</td>--}}
{{--            </tr>--}}
        </table>

        <table style="width: 100%;">
            <tr>
                <td style="height: 60px;"></td>
            </tr>
            <tr>

                <td style="text-align: right;">
                    (Authorized Signatory)
                </td>
            </tr>
        </table>
        <div>
            <button onclick="window.print()" class="btn no-print btn-lg btn-info">Print <i class="fas fa-print"></i></button>
        </div>
    </div>
@endsection

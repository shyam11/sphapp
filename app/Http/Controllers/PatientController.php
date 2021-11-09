<?php

namespace App\Http\Controllers;

use App\Appointment;
use App\Clinic;
use App\DoctorPatient;
use App\Http\Controllers\Redirect;
use App\inpatient;
use App\Medicine;
use App\Patients;
use App\Payment;
use App\Prescription;
use App\Prescription_Medicine;
use App\Ward;
use Carbon\Carbon;
use DB;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpParser\Comment\Doc;
use stdClass;

class PatientController extends Controller
{
    protected $wardArray;

    public function __construct()
    {
        $this->middleware('auth');
        $this->wardList = ['' => 'Select Ward No'] + Ward::pluck('id', 'ward_no')->all();
    }

    public function inPatientReport()
    {

        return view('patient.inpatient.inpatients', ["date" => null, "title" => "Inpatient Details", "data_count" => 0]);

    }

    public function inPatientReportData(Request $request)
    {
        $data = DB::table('inpatients')->whereDate('created_at', '=', $request->date)->get();
        if ($data->count() > 0) {
            return view('patient.inpatient.inpatients', ["title" => "Inpatient Details", "date" => $request->date, "data_count" => $data->count(), "data" => $data]);

        } else {
            return redirect(route("inPatientReport"))->with('fail', "No Results Found");
        }

    }

    public function index()
    {
        $user = Auth::user();
        $doctors = User::where('user_type', 'doctor')->get();
        return view('patient.register_patient', ['title' => $user->name, 'doctors' => $doctors]);
    }

    public function patientHistory($id)
    {
        $prescs = Prescription::where('patient_id', $id)->orderBy('created_at', 'desc')->get();
        $title = "Patient History ($id)";

        $patient = Patients::withTrashed()->find($id);
        $hospital_visits = 1;
        $status = "Active";
        $last_seen = explode(" ", $patient->updated_at)[0];
        if ($patient->trashed()) {
            $status = "Inactive";
        }
        $hospital_visits += Prescription::where('patient_id', $patient->id)->count();

        return view('patient.history.index', compact('prescs', 'patient', 'title', 'hospital_visits', 'status', 'last_seen'));
    }

    public function patientProfileIntro(Request $request)
    {
        if ($request->has('pid')) {
            return redirect()->route('patientProfile', $request->pid);
        } else {
            return view('patient.profile.intro', ['title' => "Patient Profile"]);
        }
    }

    public function patientDelete($id, $action)
    {
        if ($action == "delete") {
            Patients::find($id)->delete();
        }
        if ($action == 'restore') {
            Patients::withTrashed()->find($id)->restore();
        }
        return redirect()->route('patientProfile', $id);
    }

    public function patientProfile($id)
    {
        $patient = Patients::withTrashed()->find($id);
        $hospital_visits = 1;
        $status = "Active";
        $last_seen = explode(" ", $patient->updated_at)[0];
        if ($patient->trashed()) {
            $status = "Inactive";
        }
        $hospital_visits += Prescription::where('patient_id', $patient->id)->count();

        return view('patient.profile.profile',
            [
                'title' => $patient->name,
                'patient' => $patient,
                'status' => $status,
                'last_seen' => $last_seen,
                'hospital_visits' => $hospital_visits,

            ]);
    }

    public function searchPatient(Request $request)
    {
        $patients = Patients::query()->orderBy('created_at', 'DESC')->get();
        return view('patient.search_patient_view', ['title' => "Search Patient", "old_keyword" => null, "search_result" => "", 'patients' => $patients]);
    }

    public function patientData(Request $request)
    {
        if ($request->cat == "name") {
            $result = Patients::withTrashed()->where('name', 'LIKE', '%' . $request->keyword . '%')->get();
        }
        if ($request->cat == "id") {
            $result = Patients::withTrashed()->where('id', 'LIKE', '%' . $request->keyword . '%')->get();

        }
        if ($request->cat == "telephone") {
            $result = Patients::withTrashed()->where('telephone', 'LIKE', '%' . $request->keyword . '%')->get();
        }
        return view('patient.search_patient_view', ["title" => "Search Results", "old_keyword" => $request->keyword, "search_result" => $result]);
    }

    public function registerPatient(Request $request)
    {
        try {
            $patient = new Patients;
            $today_regs = (int)Patients::whereDate('created_at', date("Y-m-d"))->count();

            $number = $today_regs + 1;
            $year = date('Y') % 100;
            $month = date('m');
            $day = date('d');

            $reg_num = $year . $month . $day . $number;

            $date = date_create($request->reg_pbd);
            $age_year = $request->age_year;
            if($age_year > 0){
                $date = date_create(Carbon::now()->subYear($age_year));
            }
            $patient->id = $reg_num;
            $patient->name = $request->reg_pname;
            $patient->address = $request->reg_paddress;
            $patient->occupation = $request->reg_poccupation;
            $patient->sex = $request->reg_psex;
            $patient->bod = date_format($date, "Y-m-d");
            $patient->telephone = $request->reg_ptel;
            $patient->nic = $request->reg_pnic;
            $patient->image = $reg_num . ".png";

            $patient->save();
            session()->flash('regpsuccess', 'Patient ' . $request->reg_pname . ' Registered Successfully !');
            session()->flash('pid', "$reg_num");
            session()->flash('did', "$request->doctor_id");

            $image = $request->regp_photo; // your base64 encoded
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            \Storage::disk('local')->put("public/" . $reg_num . ".png", base64_decode($image));

            $doctor_patient = new DoctorPatient();
            $doctor_patient->doctor_id = $request->doctor_id;
            $doctor_patient->patient_id = $patient->id;
            $doctor_patient->registration_date = Carbon::now()->toDateTimeString();
            $doctor_patient->valid_till = Carbon::now()->addDay(19)->toDateTimeString();
            $doctor_patient->save();


            $payment = new Payment();
            $user = Auth::user();
            $payment->updated_by = $user->id;
            $payment->created_by = $user->id;
            $payment->doctor_id = $request->doctor_id;
            $payment->patient_id = $patient->id;
            $payment->service_name = json_encode(array("Consultant Fee" => $request->amount,/* "Registration Fee" => 50*/));
            $payment->bill_type = 'registration';
            $payment->total_amount = $request->amount;
            // $payment->total_amount = $request->amount + 50;
            $payment->save();
            $app = new Appointment;
            $num = DB::table('appointments')->select('id')->whereRaw(DB::raw("date(created_at)=CURDATE()"))->count() + 1;
            $pid = $patient->id;
            $patient = Patients::find($patient->id);

            $app->number = $num;
            $app->payment_id = $payment->id;
            $app->patient_id = $patient->id;
            $app->doctor_id = $request->doctor_id;
            $app->save();

            // Log Activity
            activity()->performedOn($patient)->withProperties(['Patient ID' => $reg_num])->log('Patient Registration Success');
            $user = Auth::user();
            $appointments = DB::table('appointments')->join('patients', 'appointments.patient_id', '=', 'patients.id')->join('users', 'users.id', '=', 'appointments.doctor_id')->select('users.id as doctor_id', 'users.name as uname', 'patients.name', 'appointments.number', 'appointments.patient_id', 'appointments.payment_id')->whereRaw(DB::Raw('Date(appointments.created_at)=CURDATE()'))->orderBy('appointments.created_at', 'desc')->get();
            $doctors = User::where('user_type', 'doctor')->get();

            return view('patient.create_channel_view', ['title' => "Channel Appointments", 'appointments' => $appointments, 'doctors' => $doctors]);
//            return redirect()->back();
        } catch (\Exception $e) {
            // do task when error
            $error = $e->getCode();
            // log activity
            activity()->performedOn($patient)->withProperties(['Error Code' => $error, 'Error Message' => $e->getMessage()])->log('Patient Registration Failed');

            if ($error == '23000') {
                session()->flash('regpfail', 'Patient ' . $request->reg_pname . ' Is Already Registered..');
                return redirect()->back();
            }
        }
    }

    public function validateAppNum(Request $request)
    {
        $num = $request->number;
        $numlength = strlen((string)$num);
        if ($numlength < 5) { // this means the appointment number has entered
            $rec = DB::table('appointments')
                ->join('patients', 'appointments.patient_id', '=', 'patients.id')
                ->select('patients.name as name', 'appointments.number as num', 'appointments.patient_id as pnum')
                ->whereRaw(DB::Raw("Date(appointments.created_at)=CURDATE() and appointments.number='$num'"))->first();
            if ($rec) {
                return response()->json([
                    "exist" => true,
                    "name" => $rec->name,
                    "appNum" => $rec->num,
                    "pNum" => $rec->pnum,
                    "finger" => Auth::user()->fingerprint,
                ]);
            } else {
                return response()->json([
                    "exist" => false,
                ]);
            }
        } else { //this means the patient registration number has entered
            $rec = DB::table('appointments')->join('patients', 'appointments.patient_id', '=', 'patients.id')->select('patients.name as name', 'appointments.number as num', 'appointments.patient_id as pnum')->whereRaw(DB::Raw("Date(appointments.created_at)=CURDATE() and completed='NO' and appointments.patient_id='$num'"))->first();
            if ($rec) {
                return response()->json([
                    "exist" => true,
                    "name" => $rec->name,
                    "appNum" => $rec->num,
                    "pNum" => $rec->pnum,
                ]);
            } else {
                return response()->json([
                    "exist" => false,
                ]);
            }
        }
    }

    public function checkPatientView()
    {
        $user = Auth::user();
        return view('patient.check_patient_intro', ['title' => "Check Patient"]);
    }

    public function checkPatient(Request $request)
    {
        //to get the latest appointment number for the day
        $appointment = Appointment::where('number', $request->appNum)->where('created_at', '>=', date('Y-m-d') . ' 00:00:00')->where('patient_id', $request->pid)->orderBy('created_at', 'desc')->first();

        if ($appointment->completed == "YES") {
            return redirect()->route('check_patient_view')->with('fail', "This Appointment Has Already Been Channeled.");
        }

        $patient = Patients::find($appointment->patient_id);

        $user = Auth::user();

        //need to get the latest issued prescription to fetch the patient bp,sugar,cholestrol to be displayed in the checkpatient
        $prescriptions = Prescription::where('patient_id', $appointment->patient_id)->orderBy('created_at', 'DESC')->get();

        //creates thress objects to store these data
        //sometimes thses may get blank so use the flag to resolve this issue if flag is false these will not be displayed in the view
        $pBloodPressure = new stdClass;
        $pBloodPressure->flag = false;

        $pBloodSugar = new stdClass;
        $pBloodSugar->flag = false;

        $pCholestrol = new stdClass;
        $pCholestrol->flag = false;

        foreach ($prescriptions as $prescription) {

            if (!$pBloodPressure->flag == true) {
                $bp = json_decode($prescription->bp)->value;
                if ($bp != null) {
                    $pBloodPressure->sys = explode("/", $bp)[0];
                    $pBloodPressure->dia = explode("/", $bp)[1];
                    $pBloodPressure->date = json_decode($prescription->bp)->updated;
                    $pBloodPressure->flag = true;

                }
            }

            if (!$pCholestrol->flag == true) {
                $cholestrol = json_decode($prescription->cholestrol)->value;
                if ($cholestrol != null) {
                    $pCholestrol->value = $cholestrol;
                    $pCholestrol->date = json_decode($prescription->cholestrol)->updated;
                    $pCholestrol->flag = true;
                }
            }

            if (!$pBloodSugar->flag == true) {
                $sugar = json_decode($prescription->blood_sugar)->value;
                if ($sugar != null) {
                    $pBloodSugar->value = $sugar;
                    $pBloodSugar->date = json_decode($prescription->blood_sugar)->updated;
                    $pBloodSugar->flag = true;
                }
            }

        }

        $updated = "No Previous Visits";
        if ($prescriptions->count() > 0) {
            $updated = explode(" ", $prescriptions[0]->created_at)[0];
        }
        // $updated = explode(" ", $prescriptions[0]->created_at)[0];

        $pHistory = new stdClass;

        $assinged_clinics = Patients::find($request->pid)->clinics;

        $clinics = Clinic::all();

        return view('patient.check_patient_view', [
            'title' => "Check Patient",
            'appNum' => $request->appNum,
            'appID' => $appointment->id,
            'pName' => $appointment->patient->name,
            'pSex' => $appointment->patient->sex,
            'pAge' => $patient->getAge(),
            'pCholestrol' => $pCholestrol,
            'pBloodSugar' => $pBloodSugar,
            'pBloodPressure' => $pBloodPressure,
            // 'pHistory' => $pHistory,
            'inpatient' => $appointment->admit,
            'pid' => $appointment->patient->id,
            'medicines' => Medicine::all(),
            'updated' => $updated,
            'assinged_clinics' => $assinged_clinics,
            'clinics' => $clinics,
        ]);
    }

    public function addToClinic(Request $request)
    {
        foreach ($request->clinic as $clinic) {
            $c = Clinic::find($clinic);
            $c->addPatientToClinic($request->pid);
        }
        $assinged_clinics = Patients::find($request->pid)->clinics;
        $clinics = Clinic::all();
        $pid = $request->pid;
        $html_list = view('patient.patinet_clinic', compact('pid', 'assinged_clinics', 'clinics'))->render();
        $html_already = view('patient.patient_clinic_registered', compact('assinged_clinics', 'clinics'))->render();
        return response()->json([
            'code' => 200,
            'html_already' => $html_already,
            'html_list' => $html_list,
        ]);

    }

    public function markInPatient(Request $request)
    {
        $pid = $request->pid;
        $app_num = $request->app_num;
        $user = Auth::user();
        $appointment = Appointment::where('number', $app_num)->where('created_at', '>=', date('Y-m-d') . ' 00:00:00')->where('patient_id', $pid)->first();
        if ($appointment->admit == "NO") {
            $appointment->admit = "YES";
            $appointment->doctor_id = $user->id;
            $appointment->save();
            return response()->json([
                'success' => true,
                'appid' => $appointment->id,
                'pid' => $pid,
                'app_num' => $app_num,
            ]);
        }
    }

    public function checkPatientSave(Request $request)
    {

        $user = Auth::user();
        $presc = new Prescription;
        $presc->doctor_id = $user->id;
        $presc->patient_id = $request->patient_id;
        $presc->diagnosis = $request->diagnosis;
        $presc->appointment_id = $request->appointment_id;

        $presc->medicines = json_encode($request->medicines);

        $bp = new stdClass;
        $bp->value = $request->pressure;
        $bp->updated = Carbon::now()->toDateTimeString();
        $presc->bp = json_encode($bp);

        $gloucose = new stdClass;
        $gloucose->value = $request->glucose;
        $gloucose->updated = Carbon::now()->toDateTimeString();
        $presc->blood_sugar = json_encode($gloucose);

        $cholestrol = new stdClass;
        $cholestrol->value = $request->cholestrol;
        $cholestrol->updated = Carbon::now()->toDateTimeString();
        $presc->cholestrol = json_encode($cholestrol);

        $presc->save();

        $appointment = Appointment::find($request->appointment_id);
        $appointment->completed = "YES";
        $appointment->doctor_id = $user->id;
        $appointment->save();

        foreach ($request->medicines as $medicine) {
            $med = Medicine::where('name_english', strtolower($medicine['name']))->first();
            $pres_med = new Prescription_Medicine;
            $pres_med->medicine_id = $med->id;
            $pres_med->prescription_id = $presc->id;
            $pres_med->note = $medicine['note'];
            $pres_med->save();
        }

        // Log Activity
        activity()->performedOn($presc)->withProperties(['Patient ID' => $request->patient_id, 'Doctor ID' => $user->id, 'Prescription ID' => $presc->id, 'Appointment ID' => $request->appointment_id, 'Medicines' => json_encode($request->medicines)])->log('Check Patient Success');

        return http_response_code(200);
    }

    public function create_channel_view()
    {
        $user = Auth::user();
        $appointments = DB::table('appointments')->join('patients', 'appointments.patient_id', '=', 'patients.id')->join('users', 'users.id', '=', 'appointments.doctor_id')->select('users.id as doctor_id', 'users.name as uname', 'patients.name', 'appointments.number', 'appointments.patient_id', 'appointments.payment_id')->whereRaw(DB::Raw('Date(appointments.created_at)=CURDATE()'))->orderBy('appointments.created_at', 'desc')->get();
        $doctors = User::where('user_type', 'doctor')->get();

        return view('patient.create_channel_view', ['title' => "Channel Appointments", 'appointments' => $appointments, 'doctors' => $doctors]);
    }

    public function regcard($id)
    {
        $patient = Patients::find($id);
        $url = Storage::url($id . '.png');
        $data = [
            'name' => $patient->name,
            'address' => $patient->address,
            'sex' => $patient->sex,
            'id' => $patient->id,
            'reg' => explode(" ", $patient->created_at)[0],
            'dob' => $patient->bod,
            'url' => $url,
        ];
        return view('patient.patient_reg_card', $data);
    }

    public function register_in_patient_view()
    {
        $user = Auth::user();
        $patients = DB::table('patients')
            ->join('inpatients', 'patients.id', '=', 'inpatients.patient_id')
            ->select('patients.id as id', 'patients.name as name', 'patients.sex as sex', 'patients.address as address', 'patients.occupation as occ', 'patients.telephone as tel', 'patients.nic as nic', 'patients.bod as bod', 'patients.updated_at')
            ->whereRaw(DB::Raw("inpatients.discharged!='YES'"))
            ->orderBy('patients.created_at', 'DESC')
            ->get();
        $data = DB::table('wards')
            ->select('*')
            ->join('users', 'wards.doctor_id', '=', 'users.id')
            ->get();
        // dd($data);
        return view('patient.register_in_patient_view', ['title' => "Register Inpatient", 'data' => $data, 'patients' => $patients]);
    }

    public function regInPatientValid(Request $request)
    {
        $pNum = $request->pNum;
        $pNumLen = strlen((string)$pNum);

        if ($pNumLen < 5) //if appointemnt number have been given
        {
            $patient = DB::table('patients')
                ->join('appointments', 'patients.id', '=', 'appointments.patient_id')
                ->select('patients.id as id', 'patients.name as name', 'patients.sex as sex', 'patients.address as address', 'patients.occupation as occ', 'patients.telephone as tel', 'patients.nic as nic', 'appointments.admit as ad', 'patients.bod as bod', 'appointments.number as appnum', 'appointments.doctor_id as D1', 'patients.updated_at')
                ->whereRaw(DB::Raw("appointments.admit='YES' and appointments.number='$pNum'"))
                ->first();
        } else {
            $patient = DB::table('patients')
                ->join('appointments', 'patients.id', '=', 'appointments.patient_id')
                ->select('patients.id as id', 'patients.name as name', 'patients.sex as sex', 'patients.address as address', 'patients.occupation as occ', 'patients.telephone as tel', 'patients.nic as nic', 'appointments.admit as ad', 'patients.bod as bod', 'appointments.number as appnum', 'appointments.doctor_id as D1')
                ->whereRaw(DB::Raw("appointments.admit='YES' and patients.id='$pNum'"))
                ->first();
        }
        if ($patient) {
            $patients_exist = DB::table('patients')
                ->join('inpatients', 'patients.id', '=', 'inpatients.patient_id')
                ->select('patients.id as id', 'patients.name as name', 'patients.sex as sex', 'patients.address as address', 'patients.occupation as occ', 'patients.telephone as tel', 'patients.nic as nic', 'patients.bod as bod', 'patients.updated_at')
                ->whereRaw(DB::Raw("patients.id='$patient->id'"))
                ->first();

            $patients_discharged = DB::table('patients')
                ->join('inpatients', 'patients.id', '=', 'inpatients.patient_id')
                ->select('patients.id as id', 'patients.name as name', 'patients.sex as sex', 'patients.address as address', 'patients.occupation as occ', 'patients.telephone as tel', 'patients.nic as nic', 'patients.bod as bod', 'patients.updated_at')
                ->whereRaw(DB::Raw("inpatients.discharged='YES'"))
                ->whereRaw(DB::Raw("patients.id='$patient->id'"))
                ->first();
            if ($patients_exist) {
                if ($patients_discharged) {
                    return response()->json([
                        'exist' => true,
                        'name' => $patient->name,
                        'sex' => $patient->sex,
                        'address' => $patient->address,
                        'occupation' => $patient->occ,
                        'telephone' => $patient->tel,
                        'nic' => $patient->nic,
                        'age' => Patients::find($patient->id)->getAge(),
                        'id' => $patient->id,
                    ]);
                } else {
                    return response()->json([
                        'exist' => false,
                    ]);
                }
            } else {
                return response()->json([
                    'exist' => true,
                    'name' => $patient->name,
                    'sex' => $patient->sex,
                    'address' => $patient->address,
                    'occupation' => $patient->occ,
                    'telephone' => $patient->tel,
                    'nic' => $patient->nic,
                    'age' => Patients::find($patient->id)->getAge(),
                    'id' => $patient->id,
                ]);
            }
        } else { //if patient registration number have been given
            return response()->json([
                'exist' => 'sss',
            ]);
        }

    }

    public function store_inpatient(Request $request)
    {
        // print_r($request->all());die;
        $pid = $request->reg_pid;
        $Ptable = Patients::find($pid);
        $INPtable = new inpatient;

        $Ptable->civil_status = $request->reg_ipcondition;
        $Ptable->birth_place = $request->reg_ipbirthplace;
        $Ptable->nationality = $request->reg_ipnation;
        $Ptable->religion = $request->reg_ipreligion;
        $Ptable->income = $request->reg_inpincome;
        $Ptable->guardian = $request->reg_ipguardname;
        $Ptable->guardian_address = $request->reg_ipguardaddress;

        $INPtable->patient_id = $request->reg_pid;
        $INPtable->ward_id = $request->reg_ipwardno;
        $INPtable->patient_inventory = $request->reg_ipinventory;

        $INPtable->house_doctor = $request->reg_iphousedoc;
        $INPtable->approved_doctor = $request->reg_ipapprovedoc;
        $INPtable->disease = $request->reg_admitofficer1;
        $INPtable->duration = $request->reg_admitofficer2;
        $INPtable->condition = $request->reg_admitofficer3;
        $INPtable->certified_officer = $request->reg_admitofficer4;

        $Ptable->save();
        $INPtable->save();

        // decrement bed count by 1
        $getFB = Ward::where('ward_no', $request->reg_ipwardno)->first();
        $newFB = $getFB->free_beds -= 1;
        Ward::where('ward_no', $request->reg_ipwardno)->update(['free_beds' => $newFB]);


        return redirect()->back()->with('regpsuccess', "Inpatient Successfully Registered");
    }

    public function get_ward_list()
    {
        $wardList = $this->wardList;
        $data = DB::table('wards')->join('users', 'wards.doctor_id', '=', 'users.id')->select('*')->get();
        return view('register_in_patient_view', ['data' => $data]);
        // $wards = Ward::all();
        // dd($wardss);
        // return view('register_in_patient_view', compact(['wards']));
    }

    public function discharge_inpatient()
    {
        $user = Auth::user();
        $patients = DB::table('patients')
            ->join('inpatients', 'patients.id', '=', 'inpatients.patient_id')
            ->join('doctor_patients', 'doctor_patients.patient_id', '=', 'patients.id')
            ->select('patients.id as id', 'patients.name as name', 'patients.sex as sex', 'patients.address as address', 'patients.occupation as occ', 'patients.telephone as tel', 'patients.nic as nic', 'patients.bod as bod', 'patients.updated_at', 'inpatients.payment_id', 'inpatients.id as ipd', 'inpatients.discharged_officer', 'doctor_patients.doctor_id')
            ->whereRaw(DB::Raw("inpatients.discharged='YES'"))
            ->orderBy('patients.created_at', 'DESC')
            ->get();
        return view('patient.discharge_inpatient_view', ['title' => "Discharge Inpatient", 'patients' => $patients]);
    }

    public function disInPatientValid(Request $request)
    {
        $pNum = $request->pNum;
        $inpatient = DB::table('patients')
            ->join('inpatients', 'patients.id', '=', 'inpatients.patient_id')
            ->select('inpatients.patient_id as id', 'patients.name as name', 'patients.address as address', 'patients.telephone as tel', 'inpatients.discharged as dis')
            ->whereRaw(DB::Raw("inpatients.patient_id='$pNum' and inpatients.discharged='NO'"))
            ->first();

        if ($inpatient) {

            return response()->json([
                'exist' => true,
                'name' => $inpatient->name,
                'address' => $inpatient->address,
                'telephone' => $inpatient->tel,
                'id' => $inpatient->id,
            ]);
        } else {
            return response()->json([
                'exist' => false,
            ]);
        }
    }

    public function store_disinpatient(Request $request)
    {
        // try{
        $pid = $request->reg_pid;
        $INPtableUpdate = Inpatient::where('patient_id', $pid)->first();

        $timestamp = now();
        $INPtableUpdate->discharged = 'YES';
        $INPtableUpdate->discharged_date = $timestamp;
        $INPtableUpdate->description = $request->reg_medicalofficer1;
        $INPtableUpdate->discharged_officer = $request->reg_medicalofficer2;

        $INPtableUpdate->save();

        // increment bed count by 1
        $wardNo = $INPtableUpdate->ward_id;
        $getFB = Ward::where('ward_no', $wardNo)->first();
        $newFB = $getFB->free_beds += 1;
        Ward::where('ward_no', $wardNo)->update(['free_beds' => $newFB]);

        return view('patient.discharge_recipt', compact('INPtableUpdate'))->with('regpsuccess', "Inpatient Successfully Discharged");;
        // }
        // catch(\Throwable $th){
        //     return redirect()->back()->with('error',"Unkown Error Occured");
        // }
    }

    public function getPatientData(Request $request)
    {
        $regNum = $request->regNum;
        $patient = Patients::find($regNum);

        $doc_pat = DoctorPatient::where('patient_id', $regNum)->first();
        if ($patient) {

            $num = DB::table('appointments')->select('id')->whereRaw(DB::raw("date(created_at)=CURDATE()"))->count() + 1;
            $day_left = Carbon::parse(Carbon::now())->diffInDays($doc_pat->valid_till, false);
            $validity = 1;
            if ($day_left < 0) {
                $validity = 0;
            }
            return response()->json([
                'exist' => true,
                'name' => $patient->name,
                'sex' => $patient->sex,
                'address' => $patient->address,
                'occupation' => $patient->occupation,
                'telephone' => $patient->telephone,
                'nic' => $patient->nic,
                'age' => $patient->getAge(),
                'id' => $patient->id,
                'doctor_id' => isset($doc_pat) ? $doc_pat->doctor_id : '',
                'appNum' => $num,
                'valid_from' => Carbon::parse($doc_pat->registration_date)->format('d/M/Y'),
                'valid_till' => Carbon::parse($doc_pat->valid_till)->format('d/M/Y'),
                'day' => $day_left,
                'validity' => $validity
            ]);
        } else {
            return response()->json([
                'exist' => false,
            ]);
        }
    }

    public function addChannel(Request $request)
    {
        $user = Auth::user();
        $exist = Appointment::where('doctor_id', $request->doctor_id)->where('patient_id', $request->id)->whereDate('created_at', '=', Carbon::today()->toDateString())->first();
        if ($exist) {
            return response()->json([
                'error' => 'error',
            ]);
        }
        $pid = $request->id;
        $payment = new Payment();
        $payment->doctor_id = $request->doctor_id;
        $payment->patient_id = $pid;
        $payment->updated_by = $user->id;
        $payment->created_by = $user->id;
        $payment->total_amount = $request->fees;
        $payment->service_name = json_encode(array('Consultant Fee' => $request->fees));
        if($request->bill_type == "Appointment")
            $payment->bill_type = "Old Patient";
        else
        $payment->bill_type = $request->bill_type;
        $payment->paid_amount = $request->fees;
        $payment->payment_status = 'Complete';

        $payment->save();

        $app = new Appointment;
        $num = DB::table('appointments')->select('id')->whereRaw(DB::raw("date(created_at)=CURDATE()"))->count() + 1;

        $patient = Patients::find($pid);

        $app->number = $num;
        $app->patient_id = $pid;
        $app->payment_id = $payment->id;
        $app->doctor_id = $request->doctor_id;
        $app->save();
        if ($request->bill_type == "Appointment") {
            $doctor_patient = DoctorPatient::where('patient_id', $pid)->first();
            $doctor_patient->registration_date = Carbon::now()->toDateTimeString();
            $doctor_patient->valid_till = Carbon::now()->addDay(19)->toDateTimeString();
            $doctor_patient->save();
        }
//        $doctor_patient = new DoctorPatient();
//        $doctor_patient->doctor_id = $request->doctor_id;
//        $doctor_patient->patient_id = $pid;
//        $doctor_patient->registration_date = Carbon::now()->toDateTimeString();
//        $doctor_patient->registration_date = Carbon::now()->addDay(20)->toDateTimeString();
//        $doctor_patient->save();


        try {
            $app->save();
            return response()->json([
                'exist' => true,
                'name' => $patient->name,
                'id' => $patient->id,
                'appID' => $app->id,
                'appNum' => $num,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'exist' => false,
            ]);
        }
    }

    public function editPatientview(Request $request)
    {
        // dd($request->reg_pid);
        $user = Auth::user();
        // $data = DB::table('patients')->select('*')->where('id',$request->reg_pid)->first();
        $data = Patients::find($request->reg_pid);
        return view('patient.edit_patient_view', ['title' => "Edit Patient", 'patient' => $data]);
    }

    public function updatePatient(Request $result)
    {
        // dd($result->reg_pbd);
        $user = Auth::user();

        $query = DB::table('patients')
            ->where('id', $result->reg_pid)
            ->update(array(
                'name' => $result->reg_pname,
                'address' => $result->reg_paddress,
                'sex' => $result->reg_psex,
                'bod' => $result->reg_pbd,
                'occupation' => $result->reg_poccupation,
                'nic' => $result->reg_pnic,
                'telephone' => $result->reg_ptel,
            ));

        if ($query) {
            //activity log
            activity()->performedOn($user)->log('Patient details updated!');
            return redirect()
                ->route('searchPatient')
                ->with('success', 'You have successfully updated patient details.');
        } else {
            return redirect()
                ->route('searchPatient')
                ->with('unsuccess', 'Error in Updating details !!!');
        }

    }

    function bill($id)
    {
        $patient = Patients::find($id);
        $doc_pat = DoctorPatient::where('patient_id', $id)->first();
        $doctor = User::where('user_type', 'doctor')->where('id', $doc_pat->doctor_id)->first();
        $inpatient = inpatient::where('patient_id', $id)->whereNull('payment_id')->first();
        return view('patient.bill', ['title' => "Edit Patient", 'patient' => $patient, 'doctor' => $doctor->name,'doctorId' => $doctor->id, 'inpatient' => $inpatient]);
    }

    public function billPayment(Request $request)
    {
        //print_r($request->service['amount']);
        $total_amount = 0;
        foreach ($request->service as $service) {
            //$total_amount = $service['amount'] + $total_amount;
        }
        $total_amount = array_sum($request->service['amount']);
        
        $payment = new Payment();
        $user = Auth::user();
        $payment->updated_by = $user->id;
        $payment->created_by = $user->id;
        $payment->doctor_id = $request->doctor_id;
        $payment->patient_id = $request->pid;
        $payment->bill_type = 'Discharge';
        $payment->payment_type = 'Cash';
        $payment->payment_status = 'Complete';
        $payment->service_name = json_encode($request->service);
        $payment->uhid = Carbon::now()->timestamp;
        $payment->admit_date = $request->admit_date;
        $payment->total_amount = $total_amount;
        $payment->paid_amount = $total_amount;
        $payment->save();

//        $doctor_patient = new DoctorPatient();
//        $doctor_patient->doctor_id = $request->doctor_id;
//        $doctor_patient->patient_id = $request->pid;
//        $doctor_patient->registration_date = Carbon::now()->toDateTimeString();
//        $doctor_patient->registration_date = Carbon::now()->addDay(20)->toDateTimeString();
//        $doctor_patient->save();

        $patient = Patients::find($request->pid);
        $doctor = User::find($request->doctor_id);
        $inpatient = inpatient::where('patient_id', $request->pid)->whereNull('payment_id')->first();
        $inpatient->payment_id = $payment->id;
        $inpatient->admit_date =$request->admit_date;
        $inpatient->discharged_date =$request->discharged_date;
        $inpatient->save();
        // print_r($payment);die;
        return view('patient.bill_recipt', ['title' => "Bill Recipt", 'patient' => $patient, 'doctor' => $doctor, 'payment' => $payment, 'total_amount' => $total_amount, 'inpatient' => $inpatient]);

    }

    /**
     * @param Patients $patient
     * @param User $doctor
     * @param inpatient $inpatient
     * @param Payment $payment
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function billPaymentPdf(Patients $patient, User $doctor, inpatient $inpatient, Payment $payment)
    {
        $total_amount = $payment->total_amount;
        return view('patient.bill_recipt', ['title' => "Bill Recipt", 'patient' => $patient, 'doctor' => $doctor, 'payment' => $payment, 'total_amount' => $total_amount, 'inpatient' => $inpatient]);
    }

    /**
     * @param Patients $patient
     * @param User $doctor
     * @param inpatient $inpatient
     * @param Payment $payment
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function billPaymentEdit(Patients $patient, User $doctor, inpatient $inpatient, Payment $payment)
    {
        $total_amount = $payment->total_amount;
        return view('patient.bill_edit', ['title' => "Bill Recipt", 'patient' => $patient, 'doctor' => $doctor, 'payment' => $payment, 'total_amount' => $total_amount, 'inpatient' => $inpatient]);
    }

    /**
     * @param Patients $patient
     * @param User $doctor
     * @param inpatient $inpatient
     * @param Payment $payment
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function regbillPaymentPdf(Patients $patient, User $doctor, Payment $payment)
    {
        $total_amount = $payment->total_amount;
        $doc_pat = DoctorPatient::where('patient_id', $patient->id)->first();
        return view('patient.reg_bill_recipt', ['title' => "Bill Recipt", 'patient' => $patient, 'doctor' => $doctor, 'payment' => $payment, 'total_amount' => $total_amount, 'doc_pat' => $doc_pat]);
    }

    public function mbillPayment(Request $request)
    {
        $total_amount = 0;
        foreach ($request->service as $service) {
            $total_amount = $service['amount'] + $total_amount;
        }
        $payment = new Payment();
        $user = Auth::user();
        $payment->updated_by = $user->id;
        $payment->created_by = $user->id;
        $payment->doctor_id = $request->doctor_id;
        $payment->patient_id = $request->pid;
        $payment->bill_type = $request->bill_type;
        $payment->payment_type = 'Cash';
        $payment->payment_status = 'Complete';
        $payment->service_name = json_encode($request->service);
        $payment->uhid = Carbon::now()->timestamp;
        $payment->admit_date = $request->admit_date;
        $payment->total_amount = $total_amount;
        $payment->paid_amount = $total_amount;
        $payment->save();
        $patient = Patients::find($request->pid);
        $doctor = User::find($request->doctor_id);
        $type = $request->bill_type;
        return view('patient.mbill_recipt', ['title' => "Bill Recipt", 'patient' => $patient, 'doctor' => $doctor, 'payment' => $payment, 'total_amount' => $total_amount, 'type' => $type]);

    }

    function mbill($id)
    {
        $patient = Patients::find($id);
        $doc_pat = DoctorPatient::where('patient_id', $id)->first();
        $doctor = User::where('user_type', 'doctor')->where('id', $doc_pat->doctor_id)->first();
//        $inpatient = inpatient::where('patient_id', $id)->whereNull('payment_id')->first();
        return view('patient.mbill', ['title' => "Bill Recipt", 'patient' => $patient, 'doctor' => $doctor->name,'doctorId' => $doctor->id]);
    }


    public function billPaymentUpdate(Request $request, Payment $payment)
    {

        $total_amount = 0;
        // dd($request->service);
        // foreach ($request->service as $service) {
        //     $total_amount = $service['amount'] + $total_amount;
        // }
        $total_amount = array_sum($request->service['amount']);
        
        $user = Auth::user();
        $payment->service_name = json_encode($request->service);
        $payment->total_amount = $total_amount;
        $payment->paid_amount = $total_amount;
        $payment->save();



        $patient = Patients::find($request->toArray())->first();
        $doctor = User::find($request->doctor_id);
        $inpatient = inpatient::where('patient_id', $request->pid)->first();
        $inpatient->admit_date =$request->admit_date;
        $inpatient->discharged_date =$request->discharged_date;
        
        return view('patient.bill_recipt', ['title' => "Bill Recipt", 'patient' => $patient, 'doctor' => $doctor, 'payment' => $payment, 'total_amount' => $total_amount, 'inpatient' => $inpatient]);

    }
}

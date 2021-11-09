<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Patients;
use App\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $notice = DB::table('noticeboards')
            ->join('users', 'noticeboards.user_id', '=', 'users.id')
            ->select('noticeboards.subject', 'noticeboards.description', 'noticeboards.time', 'users.user_type', 'users.name')
            ->orderByDesc('time')
            ->get();

        $doctorcnt = DB::table('users')->where('user_type', '=', 'doctor')->count(DB::raw('distinct id'));
        $generalcnt = DB::table('users')->where('user_type', '=', 'general')->count(DB::raw('distinct id'));
        $pharmacistcnt = DB::table('users')->where('user_type', '=', 'pharmacist')->count(DB::raw('distinct id'));
        $inpatientcnt = DB::table('inpatients')->where('discharged', '=', 'NO')->count(DB::raw('distinct id'));
        $patients = DB::table('patients')->whereRaw(DB::Raw('Date(patients.created_at)=CURDATE()'))->get();
        $payments = DB::table('payments')->select('bill_type','service_name')->groupBy('bill_type')->get();
        // echo '<pre>';print_r($payments);die;
        $generaldata = DB::table('payments')
             ->select('bill_type','doctor_id','total_amount',DB::raw('SUM(total_amount) AS total_amount'),DB::raw('count(bill_type) as total'))
             ->whereRaw(DB::Raw('Date(payments.created_at)=CURDATE()'))
             ->groupBy('bill_type','doctor_id')
             ->get();
        $users = DB::table('users')->where('user_type', '=', 'doctor')->get();
        // echo '<pre>';print_r($users);die;
        $bill_types = array('registration'=>'Registration','discharge'=>'Discharge');
        $doctors = DB::table('users')->select('id','name')->where('user_type', '=', 'doctor')->get();
        return view('dash', [
            'title' => 'Dashboard',
            'notices' => $notice,
            'doctorcnt'=> $doctorcnt,
            'generalcnt'=> $generalcnt,
            'pharmacistcnt'=> $pharmacistcnt,
            'inpatientcnt'=> $inpatientcnt,
            'patients' => $patients,
            'generaldata' => $generaldata,
            'users' => $users,
            'bill_types' =>$bill_types,
            'doctors' =>$doctors,
        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        $currentcontactnumber = $user->contactnumber;
        $log = DB::table('activity_log')->select('description', 'subject_id', 'subject_type', 'causer_type', 'properties', 'created_at', 'updated_at')->where('causer_id','=',$user->id)->orderBy('created_at', 'desc')->get();
        // ->whereRaw(DB::Raw('Date(created_at)=CURDATE()'))
        return view('profile', [
            'title' => $user->name,
            'activity' => $log,
            'currentno' => $currentcontactnumber,
            'crntmail' => $user->email,
            'education' => $user->education,
            'location' => $user->location,
            'skills' => $user->skills,
            'notes' => $user->notes
        ]);
    }

    public function setLocale($lan)
    {
        \Session::put('locale', $lan);
        return redirect()->back();
    }

    public function edit(Request $request)
    {
        $user = Auth::user();
        // $data = DB::table('patients')->select('*')->where('id',$request->reg_pid)->first();
        $data = Patients::find($request->id);
        return view('editpatient', ['title' => "Edit Patient", 'patient' => $data]);
    }

    public function updatePatient(Request $result)
    {
        // dd($result->all());
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
                ->route('dash')
                ->with('success', 'You have successfully updated patient details.');
        } else {
            return redirect()
                ->route('dash')
                ->with('unsuccess', 'Error in Updating details !!!');
        }

    }

    public function filterAdmin(Request $request)
    {
        $doctorcnt = DB::table('users')->where('user_type', '=', 'doctor')->count(DB::raw('distinct id'));
        $generalcnt = DB::table('users')->where('user_type', '=', 'general')->count(DB::raw('distinct id'));
        $pharmacistcnt = DB::table('users')->where('user_type', '=', 'pharmacist')->count(DB::raw('distinct id'));
        $inpatientcnt = DB::table('inpatients')->where('discharged', '=', 'NO')->count(DB::raw('distinct id'));
        $patients = DB::table('patients')->whereRaw(DB::Raw('Date(patients.created_at)=CURDATE()'))->get();
        $payments = DB::table('payments')->select('bill_type','service_name')->groupBy('bill_type')->get();
        // echo '<pre>';print_r($payments);die;
        $query = DB::table('payments')
             ->select('bill_type','doctor_id','total_amount',DB::raw('SUM(total_amount) AS total_amount'),DB::raw('count(bill_type) as total'));
             $query->whereRaw(DB::Raw('Date(payments.created_at)=CURDATE()'));
             $query->groupBy('bill_type','doctor_id');
            $generaldata= $query->get();
        $users = DB::table('users')->where('user_type', '=', 'doctor')->get();
        echo '<pre>';print_r($generaldata);die;
       
        $bill_types = array('registration'=>'Registration','discharge'=>'Discharge');
        $doctors = DB::table('users')->select('id','name')->where('user_type', '=', 'doctor')->get();
    }
}

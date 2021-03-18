<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Patient;
use App\PatientRecover;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    public function verify_email($token)
    {
        $hash = $token;
        $patient = Patient::where('hash', '=', $hash)
            ->first();

        if ($patient && $patient->email_verified_at != null)
            return view('patient/auth/expired-token')->with('error', "Expired page");

        if ($hash == null || $patient == null) {
            return view('patient/auth/verify-email')->with('error', "Invalid Token");
        } else {
            $patient->email_verified_at = Carbon::now()->toDateTimeString();
            $patient->save();
            return view('patient/auth/verify-email')->with('success', "Email Verified Successfully.");
        }
    }

    public function get_reset($token)
    {
        $patientRecover = PatientRecover::where('hash', '=', $token)->first();
        if ($token == null || $patientRecover == null) {
            return view('patient/auth/expired-token')->with('error', "Invalid or expired Token");
        }

        return view('patient/auth/reset-password')->with('token', $token);
    }

    public function post_reset(Request $request)
    {
        $v = Validator::make($request->all(), [
            'password' => 'required|confirmed|min:6',
        ]);

        if ($v->fails()) {
            return redirect()->back()->withInput($request->only('email'))
                ->withErrors($v->errors());
        }

        $password = $request["password"];
        $hash = $request["token"];

        $patientRecover = PatientRecover::where('hash', '=', $hash)->first();
        if ($hash == null || $patientRecover == null) {
            return redirect()->back()->with('error', "Invalid or expired Token");
        } else {
            $patientRecover->delete();
            $email = $patientRecover->email;
            $patient = Patient::where('email', '=', $email)->first();
            $patient->password = md5($password);
            $patient->save();

            return view('patient/auth/success')->with(['token' => $hash, 'message' => "Password changed successfully!"]);
        }
    }

}

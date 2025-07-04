<?php
namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class OtpController extends Controller
{
    public function showForm()
    {
        return view('auth.otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = User::find(session('otp_user_id'));

        if (! $user) {
            return redirect()->route('register')->withErrors('User tidak ditemukan.');
        }

        if ($user->otp_code === $request->otp && now()->lt($user->otp_expires_at)) {
            $user->email_verified_at = now();
            $user->otp_code          = null;
            $user->otp_expires_at    = null;
            $user->save();

            Session::forget('otp_user_id');

            return redirect()->route('login')->with('success', 'Kode OTP kamu benar. Silakan login.');
        }

        return back()->withErrors(['otp' => 'Kode OTP salah atau sudah kedaluwarsa']);
    }

    public function resend()
    {
        $user = User::find(session('otp_user_id'));

        if (! $user) {
            return redirect()->route('register')->withErrors('Sesi tidak valid.');
        }

        $otp                  = rand(100000, 999999);
        $user->otp_code       = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        Mail::to($user->email)->send(new \App\Mail\SendOtpMail($otp));

        return back()->with('success', 'Kode OTP berhasil dikirim ulang.');
    }
}

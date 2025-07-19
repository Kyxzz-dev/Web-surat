<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;
use Carbon\Carbon;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('pages.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'nip'      => 'required|string|unique:users,nip',
            'password' => 'required|min:6|confirmed',
        ]);

        $otp = rand(100000, 999999);

        // Simpan user
        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'nip'               => $request->nip,
            'password'          => Hash::make($request->password),
            'otp_code'          => $otp,
            'otp_expires_at'    => Carbon::now()->addMinutes(10),
            'role'              => 'staff', // <== Set otomatis jadi staff
        ]);

        Mail::to($user->email)->send(new SendOtpMail($otp));

        session(['otp_user_id' => $user->id]);

        return redirect()->route('otp.form');





        // // Redirect ke dashboard/home setelah register
        // return redirect()->route('home')->with('success', 'Registrasi berhasil!');
    }
}
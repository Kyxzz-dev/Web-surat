<?php

namespace App\Actions;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // Cek apakah user sudah verifikasi email
        if (! auth()->user()->email_verified_at) {
            session(['otp_user_id' => auth()->id()]); 
            auth()->logout();

          return redirect()->route('otp.form')
                ->withErrors(['otp' => 'Silakan verifikasi email terlebih dahulu.']);
        }


        return redirect()->intended(config('fortify.home'));
    }
}

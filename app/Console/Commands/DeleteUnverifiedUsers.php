<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class DeleteUnverifiedUsers extends Command
{
    protected $signature = 'users:delete-unverified';

    protected $description = 'Menghapus user yang belum verifikasi OTP setelah 10 menit';

    public function handle()
    {
        $deleted = User::whereNull('email_verified_at')
            ->whereNotNull('otp_expires_at')
            ->where('otp_expires_at', '<', Carbon::now())
            ->delete();

        $this->info("Berhasil menghapus $deleted user yang belum verifikasi.");
    }
}

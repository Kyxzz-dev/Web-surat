<?php

namespace App\Jobs;

use App\Models\Letter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreIncomingLetterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $letterData;
    protected $userId;
    protected $uploadedPaths; // ← Hanya path, bukan file

    public function __construct(array $letterData, $userId, array $uploadedPaths = [])
    {
        $this->letterData = $letterData;
        $this->userId = $userId;
        $this->uploadedPaths = $uploadedPaths;
    }

    public function handle()
    {
        Log::info('✉️ Job StoreIncomingLetter DIMULAI', ['user_id' => $this->userId]);

        DB::transaction(function () {
            $this->letterData['user_id'] = $this->userId;

            // Simpan surat ke database
            $letter = Letter::create($this->letterData);

            // Simpan file attachment jika ada
            foreach ($this->uploadedPaths as $path) {
                $letter->attachments()->create([
                    'file_path' => $path,
                    'uploaded_by' => $this->userId
                ]);
            }
        });

        Log::info('✅ Job StoreIncomingLetter SELESAI', ['user_id' => $this->userId]);
    }
}

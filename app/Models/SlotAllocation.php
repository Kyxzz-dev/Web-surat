<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SlotAllocation extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'start_number', 'end_number'];

    // Accessor opsional: Format tanggal untuk ditampilkan
    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->date)->locale('id')->translatedFormat('d F Y');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterNumberPool extends Model
{
    protected $fillable = ['date', 'number', 'is_used'];
}

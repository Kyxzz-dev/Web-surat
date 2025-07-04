<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubClassification extends Model
{
    protected $fillable = ['classification_id', 'code', 'description'];

    public function classification()
    {
        return $this->belongsTo(Classification::class);
    }
}
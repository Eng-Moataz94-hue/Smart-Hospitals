<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function scopeWithSpecialty($query, $specialty)
    {
        return $query->where('specialty', $specialty);
    }
}

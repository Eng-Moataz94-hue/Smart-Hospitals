<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patients extends Model
{
    //
    protected $primaryKey = 'id';
    public $incrementing = false;

    use SoftDeletes;

    public function getAge()
    {
        return Carbon::parse($this->attributes['bod'])->age;
    }

    public function clinics()
    {
        return $this->belongsToMany('App\Clinic', "clinic_patient");
    }

    public static function regsMonth($year, $month, $sex)
    {
        $sex = ucfirst(strtolower($sex));
        $c = DB::table('patients')
            ->where('patients.sex', $sex)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->count();

        return $c;
    }

    public static function totalRegs($year, $month)
    {
        $c = DB::table('patients')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->count();

        return $c;
    }
}

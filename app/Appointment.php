<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Appointment extends Model
{
    //

    public function patient()
    {
        return $this->belongsTo('\App\Patients');
    }

    public static function getMonthCount($year, $month, $sex,$type)
    {
        if($type=="IN"){
            $type="YES";
        }else if($type=="OUT"){
            $type="NO";
        }
        $sex=ucfirst(strtolower($sex));
        $c = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('patients.sex', $sex)
            ->whereMonth('appointments.created_at', $month)
            ->whereYear('appointments.created_at', $year)
            ->where('appointments.admit', $type)
            ->count('appointments.id');

        return $c;
    }

    public static function getTotalCount($year, $month,$type)
    {
        if($type=="IN"){
            $type="YES";
        }else if($type=="OUT"){
            $type="NO";
        }
        $c = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('appointments.admit', $type)
            ->whereMonth('appointments.created_at', $month)
            ->whereYear('appointments.created_at', $year)
            ->count();
        return $c;
    }
}

// select count(appointments.id) from appointments,patients where sex='Male' and year(appointments.created_at)=2020 and month(appointments.created_at)=2 and appointments.patient_id=patients.id;

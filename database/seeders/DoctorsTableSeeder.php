<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DoctorsTableSeeder extends Seeder
{
    public function run()
    {
        $specialties = ['قلب', 'عظام', 'أطفال', 'باطنة', 'جراحة عامة', 'جلدية', 'نساء وتوليد', 'عيون'];
        $now = Carbon::now();

        $doctors = DB::table('users')->where('user_type', 'doctor')->get();

        foreach ($doctors as $user) {
            $specialty = $specialties[array_rand($specialties)];
            $years = rand(3, 25);
            DB::table('doctors')->updateOrInsert(
                ['user_id' => $user->id],
                [
                    'specialty' => $specialty,
                    'license_number' => 'LIC-' . date('Y') . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    'years_experience' => $years,
                    'bio' => "طبيب مختص بـ {$specialty} مع {$years} سنوات خبرة",
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}

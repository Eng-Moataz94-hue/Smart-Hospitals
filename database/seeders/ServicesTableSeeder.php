<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServicesTableSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        $services = [
            ['code' => 'CONSULT', 'name_ar' => 'كشف طبي', 'name_en' => 'Consultation', 'default_price' => 5000.00, 'unit' => 'كشف'],
            ['code' => 'MED', 'name_ar' => 'دواء', 'name_en' => 'Medicine', 'default_price' => 500.00, 'unit' => 'وحدة'],
            ['code' => 'WARD', 'name_ar' => 'يومية سرير', 'name_en' => 'Ward Daily', 'default_price' => 10000.00, 'unit' => 'يوم'],
            ['code' => 'APPOINTMENT', 'name_ar' => 'حجز موعد', 'name_en' => 'Appointment Booking', 'default_price' => 1000.00, 'unit' => 'حجز'],
        ];
        foreach ($services as $s) {
            DB::table('services')->updateOrInsert(
                ['code' => $s['code']],
                array_merge($s, ['created_at' => $now, 'updated_at' => $now])
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(AttendancesTableSeeder::class);
        $this->call(MedicinesTableSeeder::class);
        $this->call(MedicineStocksTableSeeder::class);
        $this->call(PatientTableSeeder::class);
        $this->call(WardTableSeeder::class);
        $this->call(ClinicsTableSeeder::class);
        $this->call(NoticeboardsTableSeeder::class);
        $this->call(DemoDataSeeder::class);
    }
}

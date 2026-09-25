<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MedicineStocksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Repeat-safe: remove only seeder-managed batches, keep real receipts.
        DB::table('medicine_stocks')->where('batch_number', 'like', 'INIT-%')->delete();

        $now = Carbon::now();
        $medicineIds = DB::table('medicines')->orderBy('id')->pluck('id');

        foreach ($medicineIds as $medicineId) {
            $firstQty = rand(50, 150);
            $secondQty = rand(50, 150);
            DB::table('medicine_stocks')->insert([
                [
                    'medicine_id' => $medicineId,
                    'batch_number' => 'INIT-' . $now->year . '-' . $medicineId . '-1',
                    'initial_quantity' => $firstQty,
                    'quantity' => $firstQty,
                    'expiry_date' => $now->copy()->addMonths(6)->toDateString(),
                    'supplier' => null,
                    'created_at' => $now->toDateTimeString(),
                    'updated_at' => $now->toDateTimeString(),
                ],
                [
                    'medicine_id' => $medicineId,
                    'batch_number' => 'INIT-' . $now->year . '-' . $medicineId . '-2',
                    'initial_quantity' => $secondQty,
                    'quantity' => $secondQty,
                    'expiry_date' => $now->copy()->addMonths(12)->toDateString(),
                    'supplier' => null,
                    'created_at' => $now->toDateTimeString(),
                    'updated_at' => $now->toDateTimeString(),
                ],
            ]);
        }
    }
}

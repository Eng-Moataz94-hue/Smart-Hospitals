<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TeamMembersSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $team = [
            [
                'name' => 'معتز بشير محمد مصلح',
                'email' => 'mtz360926@gmail.com',
                'contactnumber' => 776830711,
            ],
            [
                'name' => 'صقر نبيل قاسم أحمد',
                'email' => 'engsaqrnabil@gmail.com',
                'contactnumber' => 775742987,
            ],
            [
                'name' => 'محمد سيف عبده مسعد قايد',
                'email' => 'mohammed.developer.pr@gmail.com',
                'contactnumber' => 781234712,
            ],
            [
                'name' => 'أنس محمد أمين طه الإدريسي',
                'email' => 'anas.idrisi@shifa-hospital.com',
                'contactnumber' => 778415554,
            ],
        ];

        foreach ($team as $member) {
            DB::table('users')->updateOrInsert(
                ['email' => $member['email']],
                [
                    'name' => $member['name'],
                    'password' => Hash::make((string) $member['contactnumber']),
                    'user_type' => 'admin',
                    'education' => 'فريق تطوير النظام',
                    'location' => 'فريق تطوير النظام',
                    'skills' => 'فريق تطوير النظام',
                    'contactnumber' => $member['contactnumber'],
                    'notes' => 'عضو فريق تطوير النظام',
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        $this->command->info('Team Members Seeded: ' . count($team));
    }
}

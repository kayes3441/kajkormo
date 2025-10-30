<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NotificationsTableSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = '04105162-d022-4325-80ae-286a8b3ee84d';

        for ($i = 0; $i < 20; $i++) {
            DB::table('notifications')->insert([
                'user_id' => $userIds,
                'title' => 'Notification ' . ($i + 1),
                'message' => 'This is a sample notification message #' . ($i + 1),
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

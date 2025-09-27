<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class NotificationMessagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id'        => Str::uuid(),
                'user_type' => 'user',
                'key'       => 'post_verification',
                'message'   => 'Your post has been submitted and is awaiting verification.',
                'status'    => true,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'id'        => Str::uuid(),
                'user_type' => 'user',
                'key'       => 'chatting_notification',
                'message'   => 'You have received a new chat message.',
                'status'    => true,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'id'        => Str::uuid(),
                'user_type' => 'user',
                'key'       => 'new_service_added',
                'message'   => 'A new service has been added. Check it out!',
                'status'    => true,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
        ];

        DB::table('notification_messages')->insert($data);
    }
}

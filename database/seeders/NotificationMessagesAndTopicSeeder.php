<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class NotificationMessagesAndTopicSeeder extends Seeder
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

        ];

        DB::table('notification_messages')->insert($data);

        $topicData = [
            [
                'key'       => 'new_service_added',
                'topic'       => 'category_id_location_id',
                'message'   => 'New {category} added in your {location}',
                'status'    => true,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
            'key'       => 'custom_topic',
            'topic'       => 'loklagbe_topic',
            'message'   => 'Send you custom topic',
            'status'    => true,
            'created_at'=> now(),
            'updated_at'=> now(),
        ]
        ];
        DB::table('notification_topics')->insert($topicData);
    }
}

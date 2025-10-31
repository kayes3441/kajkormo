<?php

namespace App\Trait;

use App\Models\NotificationMessage;
use App\Models\NotificationTopic;
use App\Utils\Helpers;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use function App\Utils\getConfigurationData;

trait PushNotificationTrait
{
    /**
     * push notification variable message format
     */
    protected function textVariableDataFormat($value, $key = null, $userName = null,$categoryName = null,$locationName =null)
    {
        $data = $value;
        if ($data) {
            $data = $userName ? str_replace("{userName}", $userName, $data) : $data;
            $data = $categoryName ? str_replace("{categoryName}", $categoryName, $data) : $data;
            $data = $locationName ? str_replace("{locationName}", $locationName, $data) : $data;
        }
        return $data;
    }

    /**
     * push notification variable message
     * @param string $key
     * @param string $lang
     * @return false|int|mixed|void
     */
    protected function pushNotificationMessage(string $key, string $lang)
    {
        try {
            $notificationKey = [
                'post_verification' => 'post_verification',
                'chatting_notification' => 'chatting_notification',
                'new_service_added' => 'new_service_added',
            ];
            $data = NotificationMessage::with(['translations' => function ($query) use ($lang) {
                $query->where('locale', $lang);
            }])->where(['key' => $notificationKey[$key]])->first() ?? ["status" => 0, "message" => "", "translations" => []];
            if ($data) {
                if ($data['status'] == 0) {
                    return false;
                }
                return count($data->translations) > 0 ? $data->translations[0]->value : $data['message'];
            } else {
                return false;
            }
        } catch (\Exception $exception) {

        }
    }

    protected function pushNotificationTopic(string $key, string $lang)
    {
        try {
            $notificationKey = [
                'new_service_added' => 'new_service_added',
                'custom_topic' => 'custom_topic',
            ];
            $data = NotificationTopic::with(['translations' => function ($query) use ($lang) {
                $query->where('locale', $lang);
            }])->where(['key' => $notificationKey[$key]])->first() ?? ["status" => 0, "message" => "", "translations" => []];
            if ($data) {
                if ($data['status'] == 0) {
                    return false;
                }
                return count($data->translations) > 0 ? $data->translations[0]->value : $data['message'];
            } else {
                return false;
            }
        } catch (\Exception $exception) {

        }
    }

    /**
     * Device wise notification send
     * @param string $fcmToken
     * @param array $data
     * @return bool|string
     */

    protected function sendPushNotificationToDevice(string $fcmToken, array $data): bool|string
    {
        $postData = [
            'message' => [
                'token' => $fcmToken,
                'data' => [
                    'title' => (string)$data['title'],
                    'body' => (string)$data['description'],
                    'image' => $data['image'],
                    'type' => (string)$data['type'],
                    'is_read' => '0',
                    'message_key' => (string)($data['message_key'] ?? ''),
                    'notification_key' => (string)($data['notification_key'] ?? ''),
                    'notification_from' => (string)($data['notification_from'] ?? ''),
                ],
                'notification' => [
                    'title' => (string)$data['title'],
                    'body' => (string)$data['description'],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ]
                    ]
                ]
            ]
        ];
        return $this->sendNotificationToHttp($postData);
    }

    /**
     * Device wise notification send
     * @param array|object $data
     * @param string $topic
     * @return bool|string
     */
    protected function sendPushNotificationToTopic(array|object $data, string $topic = 'loklagbe_topic'): bool|string
    {
        $postData = [
            'message' => [
                'topic' => $topic,
                'data' => [
                    'title' => (string)($data['title'] ?? ''),
                    'body' => (string)($data['description'] ?? ''),
                    'image' => $data['image'] ?? '',
                    'type' => (string)($data['type'] ?? ''),
                    'is_read' => '0'
                ],
                'notification' => [
                    'title' => (string)($data['title'] ?? ''),
                    'body' => (string)($data['description'] ?? ''),
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ]
                    ]
                ]
            ]
        ];
        return $this->sendNotificationToHttp($postData);
    }

    protected function sendNotificationToHttp(array|null $data): bool|string|null
    {
        try {
            $key = (array) getConfigurationData(name:'firebase_config');
            $key = json_decode($key[0], true);
            if (isset($key['project_id'])) {
                $url = 'https://fcm.googleapis.com/v1/projects/' . $key['project_id'] . '/messages:send';
                $headers = [
                    'Authorization' => 'Bearer ' . $this->getAccessToken($key),
                    'Content-Type' => 'application/json',
                ];
                return Http::withHeaders($headers)->post($url, $data);
            }
           return null;
        } catch (\Exception $exception) {
            return false;
        }
    }



    protected function getAccessToken($key): ?string
    {
        try {
            $jwtToken = [
                'iss' => $key['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => time() + 3600,
                'iat' => time(),
            ];
            $jwt = JWT::encode($jwtToken, $key['private_key'], 'RS256');
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }
            return null;

        } catch (\Exception $exception) {
            return $exception;
        }
    }

    /**
     * chatting related push notification
     * @param string $key
     * @param object $receiverData
     * @param object $messageForm
     * @return void
     */
    protected function chattingNotification(string $key,  object $receiverData, object $messageForm): void
    {
        try {
            $deviceToken =  $receiverData?->device_token;
            if ($deviceToken) {
                $lang = $receiverData?->app_language ?? Helpers::getDefaultLang();
                $value = $this->pushNotificationMessage($key, $lang);
                if ($value) {
                    $value = $this->textVariableDataFormat(
                        value: $value,
                        key: $key,
                        userName: "{$messageForm?->first_name} ",
                    );
                    $data = [
                        'title' => 'Message',
                        'description' => $value,
                        'image' => '',
                        'type' => 'chatting',
                        'message_key' => $key,
                        'notification_key' => $key,
                        'notification_from' => 'User',
                    ];
                    $this->sendChattingPushNotificationToDevice($deviceToken, $data);
                }
            }
        } catch (\Exception $exception) {

        }

    }
    protected function postVerificationNotification(string $key,  object $userData): void
    {
        try {
            $deviceToken =  $userData?->device_token;
            $userID =  $userData?->id;
            if ($deviceToken) {
                $lang = $userData?->app_language ?? Helpers::getDefaultLang();
                $value = $this->pushNotificationMessage($key, $lang);
                if ($value) {
                    $data = [
                        'title' => 'Message',
                        'description' => $value,
                        'image' => '',
                        'type' => 'post verification',
                        'message_key' => $key,
                        'user_id'  => $userID,
                        'notification_key' => $key,
                        'notification_from' => 'User',
                    ];
                    $this->sendChattingPushNotificationToDevice($deviceToken,$data);
                }
            }
        } catch (\Exception $exception) {

        }

    }


    /**
     * Device wise notification send
     * @param string $fcmToken
     * @param array $data
     * @return bool|string
     */

    protected function sendChattingPushNotificationToDevice(string $fcmToken,array $data): bool|string
    {
        $postData = [
            'message' => [
                'token' => $fcmToken,
                'data' => [
                    'title' => (string)$data['title'],
                    'body' => (string)$data['description'],
                    'image' => $data['image'],
                    'is_read' => '0',
                    'user_id' => $data['user_id'],
                    'type' => (string)$data['type'],
                    'message_key' => (string)($data['message_key'] ?? ''),
                    'notification_key' => (string)($data['notification_key'] ?? ''),
                    'notification_from' => (string)($data['notification_from'] ?? ''),
                ],
                'notification' => [
                    'title' => (string)$data['title'],
                    'body' => (string)$data['description'],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ]
                    ]
                ]
            ]
        ];
        return $this->sendNotificationToHttp($postData);
    }
    protected function sendNewServiceAddedTopic(string $key,string|int $topic ,string|int $categoryName, string|int $locationName): void
    {
        try {
            $lang = Helpers::getDefaultLang();
            $value = $this->pushNotificationTopic($key, $lang);
            if ($value) {
                $value = $this->textVariableDataFormat(
                    value: $value,
                    key: $key,
                    categoryName: "{$categoryName} ",
                    locationName: "{$locationName} ",
                );
                $data = [
                    'title' => $key,
                    'description' => $value,
                    'image' => '',
                    'notification_key' => $key,
                    'notification_from' => 'User',
                ];
                $this->sendPushNotificationToTopic($data, $topic);
            }
        } catch (\Exception $exception) {

        }

    }
    protected function sendCustomTopic(string $key): void
    {
        try {
            $lang = Helpers::getDefaultLang();
            $value = $this->pushNotificationTopic($key, $lang);
            if ($value) {
                $value = $this->textVariableDataFormat(
                    value: $value,
                    key: $key,
                );
                $data = [
                    'title' => $key,
                    'description' => $value,
                    'image' => '',
                    'notification_key' => $key,
                    'notification_from' => 'User',
                ];

                $this->sendPushNotificationToTopic($data);
            }
        } catch (\Exception $exception) {

        }

    }
}

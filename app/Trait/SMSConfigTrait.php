<?php

namespace App\Trait;


trait SMSConfigTrait
{

    public function bulk_sms_send($message,$phoneNumber)
    {
        $url = "https://sms.onecodesoft.com/api/send-bulk-sms";
        $api_key = "laGkqMDJKy8BrWakzpUyQP3TtsXwLMZQZjltfpj2";
        $senderid = "8809617626047";
        $messages = json_encode( [
            [
                "to" => "880".$phoneNumber,
                "message" => $message
            ]
        ]);
        $data = [
            "api_key" => $api_key,
            "senderid" => $senderid,
            "messages" => $messages
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}

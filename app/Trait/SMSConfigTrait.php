<?php

namespace App\Trait;

use function App\Utils\getConfigurationData;

trait SMSConfigTrait
{
    public function sendSMS($phoneNumber, $otpCode): bool|string|null|array
    {
        try {
            $url = getConfigurationData('sms_url');
            $apiKey = getConfigurationData('sms_api_key');
            $senderID = getConfigurationData('sms_senderid');

            $message = $this->textVariableDataFormat($otpCode);

            $payload = [
                "api_key" => $apiKey,
                "senderid" => $senderID,
                "MessageParameters" => [
                    [
                        "Number" => "880" . $phoneNumber,
                        "Text" => $message
                    ]
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET'); // matches your curl command
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

            $response = curl_exec($ch);
            curl_close($ch);

            return $response;
        } catch (\Exception $exception) {
            return false;
        }
    }

    protected function textVariableDataFormat($otpCode = null): string
    {
        $data = getConfigurationData('sms_template') ?? 'Your OTP code is {otp}';
        return $otpCode ? str_replace("{otp}", $otpCode, $data) : $data;
    }
}

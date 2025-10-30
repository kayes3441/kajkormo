<?php

namespace App\Trait;


use function App\Utils\getConfigurationData;

trait SMSConfigTrait
{

    public function sendSMS($phoneNumber,$otpCode):bool|string|null|array
    {

        try {
            $url = getConfigurationData('sms_url');
            $apiKey = getConfigurationData('sms_api_key');
            $senderID = getConfigurationData('sms_senderid');

            $message = $this->textVariableDataFormat(otpCode: $otpCode);

            $messages = json_encode( [
                [
                    "to" => "880".$phoneNumber,
                    "message" => $message
                ]
            ]);
            $data = [
                "api_key" => $apiKey,
                "senderid" => $senderID,
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
        }catch (\Exception $exception){
            return false;
        }

    }

    protected function textVariableDataFormat( $otpCode = null):string
    {
        $data = getConfigurationData('sms_template') ?? 'Your OTP code  {otp} }';
        if ($data) {
            $data = $otpCode ? str_replace("{otp}", $otpCode, $data) : $data;
        }
        return $data;
    }
}

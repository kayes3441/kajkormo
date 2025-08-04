<?php

namespace App\Trait;


trait ResponseHandler
{

    public function errorProcessor($validator): array
    {
        $errors = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            $errors[] = ['error_field' => $index, 'message' => $error[0]];
        }
        return $errors;
    }

}

<?php

namespace App\Rules;

use GuzzleHttp\Client;
use Illuminate\Contracts\Validation\Rule;

class ReCaptcha implements Rule
{
    public function passes($attribute, $value)
    {
        $guzzle = new Client();
        
        $response = $guzzle->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' =>
            [
                'secret' => env('CAPTCHA_KEY'),
                'response' => $value
            ]
        ]);

        $responseBody = json_decode((string)$response->getBody());

        return $responseBody->success;
    }
    
    public function message()
    {
        return 'Oh no! You are a robot!';
    }
}
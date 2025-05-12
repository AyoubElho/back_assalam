<?php

namespace App\Http\Controllers;

use Twilio\Rest\Client;


class SmsController extends Controller
{
    public function sendSms($number, $message)
    {
        $sid = getenv("TWILIO_SID");
        $token = getenv("TWILIO_AUTH_TOKEN");
        $sender = getenv("TWILIO_PHONE_NUMBER");
        $twilio = new Client($sid, $token);

        $message = $twilio->messages->create(
            $number,
            [
                "body" =>
                    $message,
                "from" => $sender,
            ]
        );
    }
}

<?php

namespace App\Http\Controllers;

use App\Mail\StatusMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StatusMailController extends Controller
{
    public function sendEmailToUser($user, $customMessage, $status)
    {
        try {
            Mail::to($user->email)->send(new StatusMail($user->name, $customMessage, $status));
        } catch (\Exception $e) {
            dd($e->getMessage());
            Log::error("Email failed: " . $e->getMessage());
        }
    }

}

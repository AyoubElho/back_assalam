<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $secret
            );
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            if ($session->payment_status === 'paid') {
                $donation = Donation::create([
                        'session_id' => $session->id,
                        'user_id' => $this->getUserIdByEmail($session->customer_email),
                        'amount' => $session->amount_total / 10,
                    ]
                );
                Log::info('Donation created:', ['donation' => $donation]);
                Log::info("Donation recorded for session", ['session_id' => $session->id]);
            }
        }

        return response('Webhook handled', 200);
    }

    private function getUserIdByEmail($email)
    {
        $user = User::where('email', $email)->first();
        return $user ? $user->id : null;
    }
}

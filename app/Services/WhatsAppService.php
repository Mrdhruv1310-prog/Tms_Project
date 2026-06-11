<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(
            env('TWILIO_SID'),
            env('TWILIO_AUTH_TOKEN')
        );
    }

    public function send($phone, $message)
    {
        try {

            Log::info('WhatsApp Send Start');

            Log::info([
                'to' => $phone,
                'from' => env('TWILIO_WHATSAPP_FROM'),
            ]);

            $response = $this->client->messages->create(
                "whatsapp:{$phone}",
                [
                    'from' => env('TWILIO_WHATSAPP_FROM'),
                    'body' => $message,
                ]
            );

            Log::info(
                'WhatsApp Sent SID : ' .
                    $response->sid
            );

            return true;
        } catch (\Exception $e) {

            Log::error(
                'WhatsApp Error : ' .
                    $e->getMessage()
            );

            return false;
        }
    }
}

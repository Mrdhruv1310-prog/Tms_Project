<?php

// namespace App\Livewire;

// use App\Models\Task;
// use App\Models\User;
// use Exception;
// use Illuminate\Http\Request;
// use Livewire\Component;
namespace App\Livewire;

use App\Models\Task;
use Exception;
use Illuminate\Http\Request;
use Livewire\Component;
use Twilio\Rest\Client;

class Whatsapp extends Component
{
    public function render()
    {
        return view('livewire.whatsapp');
    }

    public static function sendMessage($phone_number, $message)
    {
        try {

            $twilioSid = env('TWILIO_SID');
            $twilioAuthToken = env('TWILIO_AUTH_TOKEN');
            $twilioWhatsappNumber = env('TWILIO_WHATSAPP_NUMBER');

            if (
                empty($twilioSid) ||
                empty($twilioAuthToken) ||
                empty($twilioWhatsappNumber)
            ) {

                \Log::error('Twilio Configuration Missing', [
                    'sid_exists' => !empty($twilioSid),
                    'token_exists' => !empty($twilioAuthToken),
                    'number_exists' => !empty($twilioWhatsappNumber),
                ]);

                return false;
            }

            $client = new Client(
                $twilioSid,
                $twilioAuthToken
            );

            $response = $client->messages->create(
                'whatsapp:' . $phone_number,
                [
                    'from' => 'whatsapp:' . $twilioWhatsappNumber,
                    'body' => $message,
                ]
            );

            \Log::info('WhatsApp Sent Successfully', [
                'phone' => $phone_number,
                'sid' => $response->sid,
            ]);

            return true;
        } catch (\Throwable $e) {

            \Log::error('WhatsApp Error', [
                'phone' => $phone_number,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    function store(Request $request)
    {

        $twilioSid = env('TWILIO_SID');
        $twilioAuthToken = env('TWILIO_AUTH_TOKEN');
        $twilioWhatsappNumber = 'whatsapp:' . env('TWILIO_WHATSAPP_NUMBER');
        $to = 'whatsapp:' . $request->phone_number;
        $message = $request->message;
        $client = new Client($twilioSid, $twilioAuthToken);
        try {
            $message = $client->messages->create(
                $to,
                array(
                    'from' => $twilioWhatsappNumber,
                    'body' => $message
                )
            );

            // Return success message with the message SID for reference
            \Log::info('Twilio Config Check', [
                'sid' => env('TWILIO_SID'),
                'number' => env('TWILIO_WHATSAPP_NUMBER'),
            ]);
            return "Message sent successfully! SID: " . $message->sid;
        } catch (Exception $e) {
            // Catch any errors and return the error message
            return "Error sending message: " . $e->getMessage();
        }
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class WhatsAppService
{
    private string $accessToken;
    private string $phoneNumberId;
    private string $apiVersion;
    private string $templateLanguage;

    public function __construct()
    {
        $this->accessToken = (string) config('services.whatsapp.access_token');
        $this->phoneNumberId = (string) config('services.whatsapp.phone_number_id');
        $this->apiVersion = (string) config('services.whatsapp.api_version', 'v25.0');
        $this->templateLanguage = (string) config('services.whatsapp.template_language', 'en_US');

        if (blank($this->accessToken)) {
            throw new RuntimeException('WHATSAPP_ACCESS_TOKEN missing in configuration.');
        }

        if (blank($this->phoneNumberId)) {
            throw new RuntimeException('WHATSAPP_PHONE_NUMBER_ID missing in configuration.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | TASK ASSIGNED WHATSAPP
    |--------------------------------------------------------------------------
    */
    public function sendTaskAssigned(
        string $phoneNumber,
        string $userName,
        string $taskTitle,
        string $priority,
        string $dueDate,
        string $status = 'Pending'
    ): array {
        return $this->sendTemplate(
            $phoneNumber,
            config('services.whatsapp.task_assigned_template', 'task_assigned'),
            [
                $userName,
                $taskTitle,
                $priority,
                $dueDate,
                $status,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TASK DUE DATE REMINDER WHATSAPP
    |--------------------------------------------------------------------------
    */
    public function sendTaskDueDate(
        string $phoneNumber,
        string $userName,
        string $taskTitle,
        string $priority,
        string $dueDate,
        string $status
    ): array {
        return $this->sendTemplate(
            $phoneNumber,
            config('services.whatsapp.task_due_template', 'task_due_reminder'),
            [
                $userName,
                $taskTitle,
                $priority,
                $dueDate,
                $status,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | META CLOUD API SENDER
    |--------------------------------------------------------------------------
    */
    public function sendTemplate(
        string $phoneNumber,
        string $templateName,
        array $bodyParameters = []
    ): array {
        $token = trim((string) config('services.whatsapp.access_token'));
        $version = trim((string) config('services.whatsapp.api_version', 'v25.0'));
        $phoneId = trim((string) config('services.whatsapp.phone_number_id'));
        $language = trim((string) config('services.whatsapp.template_language', 'en_US'));

        $phoneNumber = $this->normalizePhoneNumber($phoneNumber);

        $url = 'https://graph.facebook.com/' . $version . '/' . $phoneId . '/messages';

        $parameters = [];
        foreach ($bodyParameters as $value) {
            $parameters[] = [
                'type' => 'text',
                'text' => (string) $value,
            ];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phoneNumber,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $language,
                ],
            ],
        ];

        if (! empty($parameters)) {
            $payload['template']['components'] = [
                [
                    'type' => 'body',
                    'parameters' => $parameters,
                ],
            ];
        }

        Log::info('Meta WhatsApp Send Started', [
            'sender_phone_number_id' => $phoneId,
            'recipient' => $this->maskPhone($phoneNumber),
            'template' => $templateName,
        ]);

        $response = Http::withToken($token)
            ->acceptJson()
            ->asJson()
            ->post($url, $payload);

        if ($response->successful()) {
            $data = $response->json();

            Log::info('Meta WhatsApp Accepted', [
                'template' => $templateName,
                'recipient' => $this->maskPhone($phoneNumber),
                'response' => $data,
            ]);

            return [
                'success' => true,
                'message_id' => data_get($data, 'messages.0.id'),
                'response' => $data,
            ];
        }

        $error = $response->json();

        Log::error('Meta WhatsApp Failed', [
            'status' => $response->status(),
            'template' => $templateName,
            'recipient' => $this->maskPhone($phoneNumber),
            'error_code' => data_get($error, 'error.code'),
            'error_subcode' => data_get($error, 'error.error_subcode'),
            'error_message' => data_get($error, 'error.message'),
            'response' => $error,
        ]);

        return [
            'success' => false,
            'status' => $response->status(),
            'message' => data_get($error, 'error.message', 'Unknown Meta WhatsApp error'),
            'response' => $error,
        ];
    }

    private function normalizePhoneNumber(string $phoneNumber): string
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber) ?? '';

        if (strlen($phoneNumber) === 10) {
            $phoneNumber = '91' . $phoneNumber;
        } elseif (strlen($phoneNumber) === 11 && str_starts_with($phoneNumber, '0')) {
            $phoneNumber = '91' . substr($phoneNumber, 1);
        }

        return $phoneNumber;
    }

    private function maskPhone(string $phone): string
    {
        if (strlen($phone) <= 6) {
            return $phone;
        }

        return substr($phone, 0, 4)
            . str_repeat('*', max(0, strlen($phone) - 6))
            . substr($phone, -2);
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class WhatsAppService
{
    private string $accessToken;
    private string $phoneNumberId;
    private string $businessNumberId;
    private string $apiVersion;
    private string $templateLanguage;

    public function __construct()
    {
        $this->accessToken = trim(
            (string) config('services.whatsapp.access_token')
        );

        $this->phoneNumberId = trim(
            (string) config('services.whatsapp.phone_number_id')
        );

        $this->businessNumberId = trim(
            (string) config('services.whatsapp.business_number_id')
        );

        $this->apiVersion = trim(
            (string) config(
                'services.whatsapp.api_version',
                'v25.0'
            )
        );

        $this->templateLanguage = trim(
            (string) config(
                'services.whatsapp.template_language',
                'en_US'
            )
        );

        if (blank($this->accessToken)) {
            throw new RuntimeException(
                'WHATSAPP_ACCESS_TOKEN missing in configuration.'
            );
        }

        if (blank($this->phoneNumberId)) {
            throw new RuntimeException(
                'WHATSAPP_PHONE_NUMBER_ID missing in configuration.'
            );
        }

        if (blank($this->businessNumberId)) {
            Log::warning(
                'WHATSAPP_BUSINESS_ACCOUNT_ID missing. Template verification will be skipped.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | TASK ASSIGNED
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

        $templateName = trim(
            (string) config(
                'services.whatsapp.task_assigned_template',
                'assigned_task'
            )
        );

        return $this->sendTemplate(
            $phoneNumber,
            $templateName,
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
    | TASK DUE DATE
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

        $templateName = trim(
            (string) config(
                'services.whatsapp.task_due_template',
                'task_due_reminder'
            )
        );

        return $this->sendTemplate(
            $phoneNumber,
            $templateName,
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
    | SEND META TEMPLATE
    |--------------------------------------------------------------------------
    */

    public function sendTemplate(
        string $phoneNumber,
        string $templateName,
        array $bodyParameters = []
    ): array {

        $phoneNumber = $this->normalizePhoneNumber($phoneNumber);

        if (blank($phoneNumber)) {
            return [
                'success' => false,
                'message' => 'Recipient phone number is empty.',
            ];
        }

        if (blank($templateName)) {
            return [
                'success' => false,
                'message' => 'WhatsApp template name is empty.',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve actual approved template language
        |--------------------------------------------------------------------------
        |
        | If business account ID is configured, this checks Meta template
        | information and tries to use an approved translation.
        |
        */

        $language = $this->resolveTemplateLanguage(
            $templateName,
            $this->templateLanguage
        );

        $url = sprintf(
            'https://graph.facebook.com/%s/%s/messages',
            $this->apiVersion,
            $this->phoneNumberId
        );

        $parameters = [];

        foreach ($bodyParameters as $value) {
            $parameters[] = [
                'type' => 'text',
                'text' => filled($value)
                    ? (string) $value
                    : '-',
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
            'sender_phone_number_id' => $this->phoneNumberId,

            'business_number_id' => $this->businessNumberId,

            'recipient' => $this->maskPhone($phoneNumber),

            'template' => $templateName,

            'language' => $language,

            'parameter_count' => count($parameters),
        ]);

        try {

            $response = Http::withToken($this->accessToken)
                ->acceptJson()
                ->asJson()
                ->timeout(30)
                ->post($url, $payload);
        } catch (\Throwable $e) {

            Log::error('Meta WhatsApp Connection Error', [
                'template' => $templateName,

                'recipient' => $this->maskPhone($phoneNumber),

                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,

                'message' => 'Meta WhatsApp connection failed: '
                    . $e->getMessage(),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        if ($response->successful()) {

            $data = $response->json();

            Log::info('Meta WhatsApp Accepted', [
                'template' => $templateName,

                'language' => $language,

                'recipient' => $this->maskPhone($phoneNumber),

                'message_id' => data_get(
                    $data,
                    'messages.0.id'
                ),
            ]);

            return [
                'success' => true,

                'message_id' => data_get(
                    $data,
                    'messages.0.id'
                ),

                'response' => $data,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | ERROR
        |--------------------------------------------------------------------------
        */

        $error = $response->json();

        $errorCode = data_get(
            $error,
            'error.code'
        );

        $errorMessage = data_get(
            $error,
            'error.message',
            'Unknown Meta WhatsApp error'
        );

        $errorDetails = data_get(
            $error,
            'error.error_data.details'
        );

        Log::error('Meta WhatsApp Failed', [

            'http_status' => $response->status(),

            'template' => $templateName,

            'language' => $language,

            'business_number_id' => $this->businessNumberId,

            'recipient' => $this->maskPhone($phoneNumber),

            'error_code' => $errorCode,

            'error_subcode' => data_get(
                $error,
                'error.error_subcode'
            ),

            'error_message' => $errorMessage,

            'error_details' => $errorDetails,

            'response' => $error,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Special handling for 132001
        |--------------------------------------------------------------------------
        */

        if ((int) $errorCode === 132001) {

            Log::critical(
                'WhatsApp Template Error 132001: Check exact template name, approved status and language.',
                [
                    'template' => $templateName,

                    'language' => $language,

                    'business_number_id' => $this->businessNumberId,
                ]
            );
        }

        return [
            'success' => false,

            'status' => $response->status(),

            'error_code' => $errorCode,

            'message' => $errorMessage,

            'details' => $errorDetails,

            'response' => $error,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RESOLVE TEMPLATE LANGUAGE
    |--------------------------------------------------------------------------
    */

    private function resolveTemplateLanguage(
        string $templateName,
        string $preferredLanguage
    ): string {

        if (blank($this->businessNumberId)) {
            return $preferredLanguage;
        }

        $cacheKey =
            'whatsapp_template_language:'
            . md5(
                $this->businessNumberId
                    . '|'
                    . $templateName
            );

        return Cache::remember(
            $cacheKey,
            now()->addMinutes(30),
            function () use (
                $templateName,
                $preferredLanguage
            ) {

                try {

                    $url = sprintf(
                        'https://graph.facebook.com/%s/%s/message_templates',
                        $this->apiVersion,
                        $this->businessNumberId
                    );

                    $response = Http::withToken(
                        $this->accessToken
                    )
                        ->acceptJson()
                        ->timeout(20)
                        ->get(
                            $url,
                            [
                                'name' => $templateName,

                                'fields' =>
                                'name,status,language',

                                'limit' => 100,
                            ]
                        );

                    if (! $response->successful()) {

                        Log::warning(
                            'Unable to check WhatsApp template language.',
                            [
                                'template' => $templateName,

                                'status' => $response->status(),

                                'response' => $response->json(),
                            ]
                        );

                        return $preferredLanguage;
                    }

                    $templates = collect(
                        $response->json('data', [])
                    );

                    /*
                     * Exact template name + approved only.
                     */

                    $approvedTemplates =
                        $templates
                        ->filter(
                            fn($template) =>
                            data_get(
                                $template,
                                'name'
                            ) === $templateName
                                &&
                                strtoupper(
                                    (string) data_get(
                                        $template,
                                        'status'
                                    )
                                ) === 'APPROVED'
                        );

                    if ($approvedTemplates->isEmpty()) {

                        Log::error(
                            'WhatsApp approved template not found.',
                            [
                                'template' => $templateName,

                                'business_number_id' =>
                                $this->businessNumberId,

                                'available_results' =>
                                $templates->values()->all(),
                            ]
                        );

                        return $preferredLanguage;
                    }

                    /*
                     * First try configured language.
                     */

                    $preferredTemplate =
                        $approvedTemplates
                        ->first(
                            fn($template) =>
                            data_get(
                                $template,
                                'language'
                            ) === $preferredLanguage
                        );

                    if ($preferredTemplate) {
                        return $preferredLanguage;
                    }

                    /*
                     * Otherwise use available approved language.
                     */

                    $availableLanguage =
                        data_get(
                            $approvedTemplates->first(),
                            'language'
                        );

                    if (filled($availableLanguage)) {

                        Log::warning(
                            'Configured WhatsApp language unavailable. Using approved template language.',
                            [
                                'template' => $templateName,

                                'configured_language' =>
                                $preferredLanguage,

                                'using_language' =>
                                $availableLanguage,
                            ]
                        );

                        return (string) $availableLanguage;
                    }
                } catch (\Throwable $e) {

                    Log::warning(
                        'WhatsApp template lookup failed.',
                        [
                            'template' => $templateName,

                            'error' => $e->getMessage(),
                        ]
                    );
                }

                return $preferredLanguage;
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | NORMALIZE PHONE NUMBER
    |--------------------------------------------------------------------------
    */

    private function normalizePhoneNumber(
        string $phoneNumber
    ): string {

        $phoneNumber = preg_replace(
            '/[^0-9]/',
            '',
            $phoneNumber
        ) ?? '';

        if (strlen($phoneNumber) === 10) {

            $phoneNumber =
                '91'
                . $phoneNumber;
        } elseif (
            strlen($phoneNumber) === 11
            &&
            str_starts_with(
                $phoneNumber,
                '0'
            )
        ) {

            $phoneNumber =
                '91'
                . substr(
                    $phoneNumber,
                    1
                );
        }

        return $phoneNumber;
    }

    /*
    |--------------------------------------------------------------------------
    | MASK PHONE FOR LOG
    |--------------------------------------------------------------------------
    */

    private function maskPhone(
        string $phone
    ): string {

        if (strlen($phone) <= 6) {
            return $phone;
        }

        return substr(
            $phone,
            0,
            4
        )
            . str_repeat(
                '*',
                max(
                    0,
                    strlen($phone) - 6
                )
            )
            . substr(
                $phone,
                -2
            );
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ChatbotController
{
    public function ask(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'reply' => 'Session expired. Please refresh the page and log in.'
                ], 200);
            }

            $message = $request->input('message');
            if (empty($message)) {
                return response()->json([
                    'reply' => 'Please provide a valid question.'
                ], 200);
            }

            $aiServiceUrl = env('AI_SERVICE_URL', 'http://127.0.0.1:8001');

            $response = Http::timeout(15)->post($aiServiceUrl . '/api/chat', [
                'user_id' => $user->id,
                'role'    => $user->role ?? 'employee',
                'message' => $message,
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            Log::error('FastAPI error response: ' . $response->body());
            return response()->json([
                'reply' => 'Elektro AI service error (' . $response->status() . '): ' . $response->body()
            ], 200);

        } catch (Throwable $e) {
            Log::error('Chatbot Controller exception: ' . $e->getMessage());
            return response()->json([
                'reply' => 'Controller Error: ' . $e->getMessage()
            ], 200);
        }
    }
}
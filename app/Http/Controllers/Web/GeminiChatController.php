<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeminiChatController extends Controller
{
    /**
     * Handle Gemini AI chat message (public endpoint).
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message'  => 'required|string|max:1000',
            'history'  => 'nullable|array',
            'history.*.role'  => 'in:user,model',
            'history.*.parts' => 'array',
        ]);

        // Prefer DB-stored key, fall back to env/config
        $setting = SystemSetting::first();
        $apiKey  = ($setting && !empty($setting->gemini_api_key))
            ? $setting->gemini_api_key
            : (config('app.gemini_api_key') ?: env('GEMINI_API_KEY', ''));

        // Respect admin's chatbot_enabled toggle
        if ($setting && $setting->chatbot_enabled === false) {
            return response()->json([
                'reply' => 'The AI Assistant is currently disabled. Please contact us via WhatsApp or email.',
            ]);
        }

        if (empty($apiKey) || $apiKey === 'your_gemini_api_key_here') {
            return response()->json([
                'reply' => 'AI Assistant is currently offline or awaiting configuration in System Settings. Please contact us via WhatsApp or live chat for immediate assistance.',
            ]);
        }

        // Use admin-configured system prompt, fall back to default strictly bounded prompt
        $defaultPrompt = "You are the official AI assistant for DOOTOR ENTERPRISES, an international administrative documentation and consular liaison consultancy specializing in passports, travel documents, visa processing guidance, NIN/BVN documentation, court affidavits, legal attestations, identity records, and consular services.

STRICT BOUNDARY & RULES:
- You must ONLY answer questions directly related to DOOTOR ENTERPRISES, its services, processes, document requirements, and client application workflows.
- If a user asks about anything unrelated to DOOTOR ENTERPRISES or its services (e.g., general trivia, science, coding, politics, personal questions, unrelated news), politely decline: 'I am the DOOTOR ENTERPRISES assistant and can only answer questions related to our services and document processing. How can I assist you with your document or application today?'
- Never provide legal advice; always advise consulting a certified legal or consular professional.
- For pricing questions, advise: 'Pricing and fee schedules are accessible directly inside your client dashboard upon login or in our Services Catalog.'
- For urgent human intervention, direct the user to our WhatsApp support or click 'Chat with Agent' to connect to Live Support.
- Always maintain a warm, polite, and professional tone.";

        $systemContext = ($setting && !empty($setting->chatbot_system_prompt))
            ? $setting->chatbot_system_prompt
            : $defaultPrompt;

        // Build conversation history
        $contents = [];

        // Add prior history if provided
        if (!empty($request->history)) {
            foreach ($request->history as $turn) {
                $contents[] = [
                    'role'  => $turn['role'],
                    'parts' => $turn['parts'],
                ];
            }
        }

        // Add current user message
        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $request->message]],
        ];

        $candidateModels = [
            'gemini-3-flash-preview',
            'gemini-3.8-flash',
            'gemini-3.5-flash',
            'gemini-flash-latest',
            'gemini-3.1-flash-lite',
            'gemini-2.5-flash',
        ];

        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $systemContext]],
            ],
            'contents'           => $contents,
            'generationConfig'   => [
                'temperature'     => 0.7,
                'maxOutputTokens' => 512,
            ],
            'safetySettings' => [
                ['category' => 'HARM_CATEGORY_HARASSMENT',        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ['category' => 'HARM_CATEGORY_HATE_SPEECH',       'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
            ],
        ];

        $lastError = null;

        foreach ($candidateModels as $model) {
            try {
                $response = Http::timeout(12)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if (!empty($reply)) {
                        return response()->json(['reply' => trim($reply)]);
                    }
                }

                $status = $response->status();
                $body = $response->json();
                $msg = $body['error']['message'] ?? $response->body();
                $lastError = "Model {$model} returned {$status}: {$msg}";
                \Log::warning("Gemini model {$model} unavailable ({$status}), trying next fallback... {$msg}");

                // If invalid API key (400), don't keep looping models
                if ($status === 400 && str_contains(strtolower($response->body()), 'api_key_invalid')) {
                    \Log::error('Gemini API key is invalid.');
                    return response()->json([
                        'reply' => 'The configured Gemini API key is invalid. Please check your API key in Admin System Settings.',
                    ]);
                }
            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                \Log::warning("Gemini model {$model} exception: " . $e->getMessage());
            }
        }

        \Log::error('All Gemini candidate models failed: ' . $lastError);

        return response()->json([
            'reply' => 'I\'m having trouble connecting right now. Please try again in a moment or use our WhatsApp support.',
        ]);
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private ?string $apiKey;
    private string $model;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = config('ai.gemini_key') ?: env('GEMINI_API_KEY');
        $this->model  = env('GEMINI_MODEL', 'gemini-2.5-flash');
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Generate a JSON-structured response from Gemini.
     */
    public function generateJSON(string $prompt, string $systemInstruction = ''): array
    {
        if (!$this->isAvailable()) {
            throw new \Exception('Gemini API key not configured. Please set GEMINI_API_KEY in your .env file.');
        }

        $fullPrompt = $systemInstruction
            ? "{$systemInstruction}\n\nUser request:\n{$prompt}\n\nIMPORTANT: Return ONLY valid JSON, no markdown, no explanation."
            : "{$prompt}\n\nIMPORTANT: Return ONLY valid JSON, no markdown, no explanation.";

        $response = Http::timeout(45)->post(
            "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}",
            [
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $fullPrompt]]]
                ],
                'generationConfig' => [
                    'temperature'     => 0.7,
                    'maxOutputTokens' => 6000,
                    'responseMimeType' => 'application/json',
                ],
            ]
        );

        if (!$response->successful()) {
            $body = $response->json();
            $errMsg = $body['error']['message'] ?? 'Gemini API error ' . $response->status();
            Log::error('Gemini API error', ['status' => $response->status(), 'body' => $body]);
            throw new \Exception($errMsg);
        }

        $text = $response->json('candidates.0.content.parts.0.text', '{}');
        // Strip any accidental markdown fences
        $text = preg_replace('/^```json\s*/i', '', trim($text));
        $text = preg_replace('/```\s*$/', '', $text);

        $decoded = json_decode(trim($text), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Gemini JSON parse error', ['raw' => $text]);
            throw new \Exception('AI returned invalid JSON. Please try again.');
        }

        return $decoded;
    }

    /**
     * Generate plain text from Gemini (for chatbot).
     */
    public function generateText(string $prompt, string $systemInstruction = ''): string
    {
        if (!$this->isAvailable()) {
            return $this->offlineFallback($prompt);
        }

        $fullPrompt = $systemInstruction
            ? "{$systemInstruction}\n\nUser: {$prompt}"
            : $prompt;

        $response = Http::timeout(30)->post(
            "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}",
            [
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $fullPrompt]]]
                ],
                'generationConfig' => [
                    'temperature'     => 0.8,
                    'maxOutputTokens' => 1024,
                ],
            ]
        );

        if (!$response->successful()) {
            Log::error('Gemini text API error', ['status' => $response->status()]);
            return 'Sorry, I am having trouble connecting to the AI right now. Please try again shortly.';
        }

        return $response->json('candidates.0.content.parts.0.text', 'I could not generate a response. Please try again.');
    }

    /**
     * Offline fallback when no API key is configured.
     */
    private function offlineFallback(string $prompt): string
    {
        $q = strtolower($prompt);
        if (str_contains($q, 'substitute') || str_contains($q, 'alternative') || str_contains($q, 'instead')) {
            return "Here are some great substitutions!\n\n**For Heavy Cream:** Greek Yogurt (1:1) or Coconut Cream.\n**For Garlic:** 1/4 tsp Garlic Powder per clove.\n**For Eggs:** 1 tbsp ground Chia Seeds + 3 tbsp water.\n\nAsk me about a specific ingredient for tailored suggestions! 🍳";
        }
        if (str_contains($q, 'biryani') || str_contains($q, 'spice') || str_contains($q, 'karahi')) {
            return "**ChefAI Tip for Pakistani Cuisine** 🌶️\n\nFor rich Biryani aroma:\n• Toast whole spices (cumin, cardamom, cloves) in warm ghee first.\n• Use **dum** technique — seal the pot and steam on low heat.\n• Add saffron soaked in warm milk for golden color.\n\nHappy cooking!";
        }
        return "Hello! I am **ChefAI**, your AI culinary companion. 🍳\n\nI can help you with:\n• Custom recipe ideas\n• Ingredient substitutions\n• Cooking tips & techniques\n• Nutritional guidance\n\n*Note: Set your GEMINI_API_KEY in .env for full AI responses!*";
    }
}

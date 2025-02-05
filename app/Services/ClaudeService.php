<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ClaudeService
{
    private $apiKey;
//    private $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';

    private string $apiUrl = 'https://api.anthropic.com/v1/messages';
    private string $model = 'claude-3-5-sonnet-20241022';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.api_key');
//        $this->apiKey = config('services.google.gemini_key');
    }

    /**
     * Send a message to Claude API and get response
     *
     * @param string $message User's input message
     * @return string Claude's response
     */

    public function chat(string $message, array $previousMessages = []): array
    {
        $messages = $previousMessages;


        if (!empty($message)) {
            $messages[] = [
                "role" => "user",
                "content" => [
                [
                    "type"=> "text",
                    "text"=> $message,
                    "cache_control"=> ["type" => "ephemeral"]
                ],
                ]
            ];
        }

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
            'cache-control' => 'max-age=3000'
        ])->post($this->apiUrl, [
            'model' => $this->model,
            'max_tokens' => 1024,

            'messages' => $messages

        ]);

        if ($response->successful()) {
            $result = $response->json();
            return [
                'message' => $result['content'][0]['text'],
                'input_tokens' => $result['usage']['input_tokens'],
                'output_tokens' => $result['usage']['output_tokens'],
                'cache_creation' => $result['usage']['cache_creation_input_tokens'],
                'cache_read' => $result['usage']['cache_read_input_tokens']

            ];
        }
        throw new \Exception('API request failed: ' . $response->body());
    }
}

<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use OpenAI;
use OpenAI\Client;

class TokenCounterService
{
    private $client;

    public function __construct()
    {
        $this->client = OpenAI::client(config('app.openai.api_key'));
    }

    /**
     * Send a message to Claude API and get response
     *
     * @param string $message User's input message
     * @return string Claude's response
     */

    public function countTokens($text)
    {
        try {
            $encoding = $this->client->encodings()->get('cl100k_base');
            return count($encoding->encode($text));
        } catch (Exception $e) {
            \Log::error('Token counting failed: ' . $e->getMessage());
            return $this->roughEstimate($text);
        }
    }
    private function roughEstimate($text)
    {
        return ceil(mb_strlen($text) * 0.5);
    }
}

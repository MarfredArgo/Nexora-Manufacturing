<?php

namespace Modules\BusinessIntelligence\Services\AI\Providers;

use Modules\BusinessIntelligence\Services\AI\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAIProvider implements AIProviderInterface
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->apiKey = (string) config('ai.providers.openai.api_key');
        $this->model = (string) config('ai.providers.openai.model');
        $this->baseUrl = rtrim((string) config('ai.providers.openai.base_url'), '/');
        $this->timeout = (int) config('ai.providers.openai.timeout', 60);
    }

    public function generate(
        string $systemPrompt,
        string $userPrompt,
        bool $jsonMode = false,
        string $thinkingLevel = 'low'
    ): array {
        try {
            if (empty($this->apiKey)) {
                throw new RuntimeException('OPENAI_API_KEY is not set. Add it to your .env file.');
            }

            $url = "{$this->baseUrl}/chat/completions";

            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ];

            $body = [
                'model' => $this->model,
                'messages' => $messages,
                'temperature' => $thinkingLevel === 'low' ? 0.3 : ($thinkingLevel === 'medium' ? 0.5 : 0.7),
            ];

            if ($jsonMode) {
                $body['response_format'] = ['type' => 'json_object'];
            }

            $response = Http::withoutVerifying()
                ->retry(2, 300, throw: false)
                ->timeout($this->timeout)
                ->acceptJson()
                ->withToken($this->apiKey)
                ->post($url, $body);

            if ($response->failed()) {
                throw new RuntimeException(
                    'OpenAI API request failed (' . $response->status() . '): ' . $response->body()
                );
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? null;

            if ($content === null) {
                throw new RuntimeException('OpenAI API returned no content: ' . json_encode($data));
            }

            return [
                'content' => $content,
                'input_tokens' => (int) ($data['usage']['prompt_tokens'] ?? 0),
                'output_tokens' => (int) ($data['usage']['completion_tokens'] ?? 0),
            ];
        } catch (\Throwable $e) {
            \Log::error('OpenAI Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            throw $e;
        }
    }
}
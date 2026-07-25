<?php

namespace Modules\BusinessIntelligence\Services\AI\Providers;

use Illuminate\Support\Facades\Http;
use Modules\BusinessIntelligence\Services\AI\Contracts\AIProviderInterface;
use RuntimeException;

/**
 * DigitalOcean's serverless inference exposes an OpenAI-compatible
 * /chat/completions endpoint, so this is the OpenAI provider pointed at
 * DO's base URL with a DO key/model. Select it by setting
 * AI_PROVIDER=digitalocean (plus DO_INFERENCE_API_KEY and DO_INFERENCE_MODEL)
 * in .env — no other code changes needed.
 */
class DigitalOceanProvider implements AIProviderInterface
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->apiKey = (string) config('ai.providers.digitalocean.api_key');
        $this->model = (string) config('ai.providers.digitalocean.model');
        $this->baseUrl = rtrim((string) config('ai.providers.digitalocean.base_url'), '/');
        $this->timeout = (int) config('ai.providers.digitalocean.timeout', 60);
    }

    public function generate(
        string $systemPrompt,
        string $userPrompt,
        bool $jsonMode = false,
        string $thinkingLevel = 'low'
    ): array {
        try {
            if (empty($this->apiKey)) {
                throw new RuntimeException('DO_INFERENCE_API_KEY is not set. Add it to your .env file.');
            }
            if (empty($this->model)) {
                throw new RuntimeException('DO_INFERENCE_MODEL is not set. Add it to your .env file.');
            }

            $body = [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
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
                ->post("{$this->baseUrl}/chat/completions", $body);

            if ($response->failed()) {
                throw new RuntimeException(
                    'DigitalOcean inference request failed (' . $response->status() . '): ' . $response->body()
                );
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? null;

            if (is_array($content)) {
                $content = collect($content)->pluck('text')->filter()->implode("\n");
            }

            if ($content === null || $content === '') {
                throw new RuntimeException('DigitalOcean inference returned no content: ' . json_encode($data));
            }

            return [
                'content' => $content,
                'input_tokens' => (int) ($data['usage']['prompt_tokens'] ?? 0),
                'output_tokens' => (int) ($data['usage']['completion_tokens'] ?? 0),
            ];
        } catch (\Throwable $e) {
            \Log::error('DigitalOcean inference error: ' . $e->getMessage());

            throw $e;
        }
    }
}

<?php

namespace Modules\BusinessIntelligence\Services\AI\Providers;

use Modules\BusinessIntelligence\Services\AI\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiProvider implements AIProviderInterface
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->apiKey = (string) config('ai.providers.gemini.api_key');
        $this->model = (string) config('ai.providers.gemini.model');
        $this->baseUrl = rtrim((string) config('ai.providers.gemini.base_url'), '/');
        $this->timeout = (int) config('ai.providers.gemini.timeout', 60);
    }

    public function generate(
        string $systemPrompt,
        string $userPrompt,
        bool $jsonMode = false,
        string $thinkingLevel = 'low'
    ): array {
        try {
            if (empty($this->apiKey)) {
                throw new RuntimeException('GEMINI_API_KEY is not set. Add it to your .env file.');
            }

            $url = "{$this->baseUrl}/models/{$this->model}:generateContent"
                . '?key=' . urlencode($this->apiKey);

            $generationConfig = [
                'temperature' => $thinkingLevel === 'low' ? 0.3 : ($thinkingLevel === 'medium' ? 0.5 : 0.7),
            ];

            if ($jsonMode) {
                $generationConfig['responseMimeType'] = 'application/json';
            }

            $body = [
                'system_instruction' => [
                    'parts' => [['text' => $systemPrompt]],
                ],
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [['text' => $userPrompt]],
                    ],
                ],
                'generationConfig' => $generationConfig,
            ];

            $response = Http::withoutVerifying()
                ->retry(2, 300, throw: false)
                ->timeout($this->timeout)
                ->acceptJson()
                ->post($url, $body);

            if ($response->failed()) {
                throw new RuntimeException(
                    'Gemini API request failed (' . $response->status() . '): ' . $response->body()
                );
            }

            $data = $response->json();

            $finishReason = $data['candidates'][0]['finishReason'] ?? null;
            if ($finishReason === 'SAFETY' || $finishReason === 'RECITATION') {
                throw new RuntimeException(
                    "Gemini API blocked the response (finishReason: {$finishReason}): " . json_encode($data)
                );
            }

            $parts = $data['candidates'][0]['content']['parts'] ?? null;
            $content = null;
            if (is_array($parts)) {
                $content = collect($parts)
                    ->pluck('text')
                    ->filter()
                    ->implode('');
            }

            if ($content === null || $content === '') {
                throw new RuntimeException('Gemini API returned no content: ' . json_encode($data));
            }

            return [
                'content' => $content,
                'input_tokens' => (int) ($data['usageMetadata']['promptTokenCount'] ?? 0),
                'output_tokens' => (int) ($data['usageMetadata']['candidatesTokenCount'] ?? 0),
            ];
        } catch (\Throwable $e) {
            \Log::error('Gemini Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            throw $e;
        }
    }
}
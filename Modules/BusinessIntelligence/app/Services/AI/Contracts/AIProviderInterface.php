<?php

namespace Modules\BusinessIntelligence\Services\AI\Contracts;

/**
 * Every AI provider (Gemini, OpenAI, ...) must implement this. AIRouter
 * depends only on this interface, never on a concrete provider, so
 * swapping providers is a config/env change plus one new class — nothing
 * that calls AIRouter needs to change.
 */
interface AIProviderInterface
{
    /**
     * Send a system + user prompt to the provider and get back the raw
     * text response plus token usage. Set $jsonMode = true when the
     * prompt requires a strict JSON object back (report generation);
     * leave it false for free-text answers (chatbot replies).
     *
     * @return array{content: string, input_tokens: int, output_tokens: int}
     */
    // app/Services/AI/Contracts/AIProviderInterface.php
    public function generate(
        string $systemPrompt,
        string $userPrompt,
        bool $jsonMode = false,
        string $thinkingLevel = 'low'
    ): array;
}

<?php

namespace Modules\BusinessIntelligence\Services\AI;

use Modules\BusinessIntelligence\Services\AI\Contracts\AIProviderInterface;
use RuntimeException;

/**
 * Resolves the currently configured AI provider (config('ai.default'))
 * out of the container. Nothing else in the app — not AIManager, not
 * ChatService, not the Jobs — talks to Gemini directly; everything goes
 * through AIRouter::provider(). To switch providers, change AI_PROVIDER
 * in .env (and register the new driver in config/ai.php); no other file
 * needs to change.
 */
class AIRouter
{
    public function provider(): AIProviderInterface
    {
        $name = config('ai.default');
        $driverClass = config("ai.providers.{$name}.driver");

        if (!$driverClass || !class_exists($driverClass)) {
            throw new RuntimeException(
                "AI provider [{$name}] is not configured. Check config/ai.php and AI_PROVIDER in .env."
            );
        }

        $provider = app($driverClass);

        if (!$provider instanceof AIProviderInterface) {
            throw new RuntimeException(
                get_class($provider) . ' must implement ' . AIProviderInterface::class
            );
        }

        return $provider;
    }

    public function providerName(): string
    {
        return (string) config('ai.default');
    }

    public function modelName(): string
    {
        return (string) config('ai.providers.' . $this->providerName() . '.model');
    }
}
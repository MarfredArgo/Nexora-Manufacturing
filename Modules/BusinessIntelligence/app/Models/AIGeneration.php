<?php

namespace Modules\BusinessIntelligence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $client_id
 * @property int $generation_number
 * @property string $status
 * @property string $triggered_by
 * @property string|null $trigger_reason
 * @property string|null $provider
 * @property string|null $model
 * @property int $input_tokens
 * @property int $output_tokens
 * @property bool $is_current
 * @property Carbon|null $started_at
 * @property Carbon|null $completed_at
 * @property string|null $error_message
 */
class AIGeneration extends Model
{
    protected $connection = 'business_intelligence';

    protected $table = 'bi_ai_generations';

    protected $fillable = [
        'client_id',
        'generation_number',
        'status',
        'triggered_by',
        'trigger_reason',
        'provider',
        'model',
        'input_tokens',
        'output_tokens',
        'is_current',
        'started_at',
        'completed_at',
        'error_message',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
            'input_tokens' => 'integer',
            'output_tokens' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function totalTokens(): int
    {
        return $this->input_tokens + $this->output_tokens;
    }
}

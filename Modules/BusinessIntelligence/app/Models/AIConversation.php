<?php

namespace Modules\BusinessIntelligence\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $client_id
 * @property int|null $employee_id
 * @property string $role
 * @property string $message
 * @property bool $used_ai
 */
class AIConversation extends Model
{
    protected $connection = 'business_intelligence';

    protected $table = 'bi_ai_conversations';

    protected $fillable = [
        'client_id',
        'employee_id',
        'role',
        'message',
        'used_ai',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'used_ai' => 'boolean',
        ];
    }
    public function scopeForClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId)->orderBy('created_at');
    }
}

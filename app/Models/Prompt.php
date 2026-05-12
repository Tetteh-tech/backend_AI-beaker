<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prompt extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'type',
        'agent_target',
        'response_time',
        'tokens_used',
        'ai_confidence',
        'caused_contradiction',
        'memory_break',
        'ai_response'
    ];

    protected $casts = [
        'ai_response' => 'array',
        'caused_contradiction' => 'boolean',
        'memory_break' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
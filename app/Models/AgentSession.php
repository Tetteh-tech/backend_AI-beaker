<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentSession extends Model
{
    protected $fillable = [
        'user_id',
        'session_token',
        'memory_context',
        'active_agents',
        'stress_level',
        'status'
    ];

    protected $casts = [
        'memory_context' => 'array',
        'active_agents' => 'array',
        'stress_level' => 'integer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
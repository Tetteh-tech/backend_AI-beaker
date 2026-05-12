<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'score',
        'total_attacks',
        'successful_breaks',
        'badges',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'badges' => 'array',
        'score' => 'integer',
    ];

    public function prompts()
    {
        return $this->hasMany(Prompt::class);
    }

    public function agentSessions()
    {
        return $this->hasMany(AgentSession::class);
    }

    public function getRankAttribute()
    {
        return self::where('score', '>', $this->score)->count() + 1;
    }

    public function addBadge($badge)
    {
        $badges = $this->badges ?? [];
        if (!in_array($badge, $badges)) {
            $badges[] = $badge;
            $this->badges = $badges;
            $this->save();
        }
    }
}
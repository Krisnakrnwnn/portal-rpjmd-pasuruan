<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatAnalytic extends Model
{
    protected $fillable = [
        'date',
        'total_sessions',
        'total_messages',
        'avg_messages_per_session',
        'avg_response_time',
        'total_likes',
        'total_dislikes',
        'top_questions',
    ];

    protected $casts = [
        'date' => 'date',
        'avg_messages_per_session' => 'decimal:2',
        'avg_response_time' => 'decimal:2',
        'top_questions' => 'array',
    ];
}

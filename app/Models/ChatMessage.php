<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'session_id',
        'role',
        'message',
    ];

    /**
     * Get the session that owns the message.
     */
    public function session()
    {
        return $this->belongsTo(ChatSession::class, 'session_id', 'session_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    protected $fillable = [
        'session_id',
        'user_ip',
    ];

    /**
     * Get the messages for the chat session.
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'session_id', 'session_id');
    }
}

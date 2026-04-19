<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentChunk extends Model
{
    protected $fillable = [
        'document_name',
        'page_number',
        'chunk_text',
        'embedding',
    ];

    protected $casts = [
        'embedding' => 'array',
    ];
}

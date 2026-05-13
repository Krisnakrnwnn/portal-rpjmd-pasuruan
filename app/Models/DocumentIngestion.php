<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentIngestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'original_name',
        'total_pages',
        'processed_pages',
        'status',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function getProgressPercentageAttribute()
    {
        if ($this->total_pages <= 0) return 0;
        return round(($this->processed_pages / $this->total_pages) * 100);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'document_category_id',
        'category', // keeping it for backwards compatibility if needed during transition
        'year',
        'file_url',
    ];

    public function documentCategory()
    {
        return $this->belongsTo(DocumentCategory::class, 'document_category_id');
    }
}

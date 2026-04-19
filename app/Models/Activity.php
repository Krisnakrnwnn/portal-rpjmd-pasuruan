<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = ['user_id', 'type', 'action', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log($type, $action, $description)
    {
        self::create([
            'user_id' => auth()->id(),
            'type' => $type,
            'action' => $action,
            'description' => $description,
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon', 'theme_color'];

    public function indicators()
    {
        return $this->hasMany(Indicator::class);
    }
}

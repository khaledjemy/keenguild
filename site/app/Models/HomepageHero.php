<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageHero extends Model
{
    protected $fillable = [
        'kicker_ar', 'kicker_en', 'headline_ar', 'headline_en', 'accent_ar', 'accent_en',
        'description_ar', 'description_en', 'published',
    ];

    protected function casts(): array
    {
        return ['published' => 'boolean'];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class InquirySetting extends Model
{
    protected $fillable = ['intake_requested'];

    protected function casts(): array
    {
        return ['intake_requested' => 'boolean'];
    }

    public static function current(): ?self
    {
        return Schema::hasTable('inquiry_settings') ? self::query()->first() : null;
    }
}

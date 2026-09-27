<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingSetting extends Model
{
    public const FACTOR_DEFAULTS = [
        'complexity_standard' => 1.0,
        'complexity_moderate' => 1.2,
        'complexity_high' => 1.4,
        'urgency_flexible' => 1.0,
        'urgency_urgent' => 1.15,
    ];

    public const LABELS = [
        'complexity_standard' => 'تعقيد عادي',
        'complexity_moderate' => 'تعقيد متوسط',
        'complexity_high' => 'تعقيد مرتفع',
        'urgency_flexible' => 'موعد مرن',
        'urgency_urgent' => 'موعد مستعجل',
        'estimate_range_percent' => 'هامش النطاق الاسترشادي (%)',
    ];

    public $incrementing = false;

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    protected function casts(): array
    {
        return ['value' => 'decimal:3'];
    }
}

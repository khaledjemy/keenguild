<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceOption extends Model
{
    protected $fillable = ['package_id', 'code', 'label_ar', 'label_en', 'calculation_type', 'amount_egp', 'percent', 'max_quantity', 'published', 'sort_order'];

    protected function casts(): array
    {
        return ['published' => 'boolean', 'amount_egp' => 'integer', 'percent' => 'integer', 'max_quantity' => 'integer'];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}

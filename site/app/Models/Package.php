<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $fillable = ['service_id', 'slug', 'name_ar', 'name_en', 'description_ar', 'description_en', 'base_price_egp', 'pricing_mode', 'included_pages', 'included_features', 'included_features_en', 'excluded_costs', 'excluded_costs_en', 'published', 'featured_on_home', 'sort_order'];

    protected function casts(): array
    {
        return ['included_features' => 'array', 'included_features_en' => 'array', 'excluded_costs' => 'array', 'excluded_costs_en' => 'array', 'published' => 'boolean', 'featured_on_home' => 'boolean', 'base_price_egp' => 'integer'];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(PriceOption::class);
    }
}

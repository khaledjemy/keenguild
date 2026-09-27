<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteRequest extends Model
{
    protected $fillable = ['package_id', 'name', 'email', 'phone', 'project_brief', 'selected_options', 'indicative_total_egp', 'status'];

    protected function casts(): array
    {
        return ['selected_options' => 'array', 'indicative_total_egp' => 'integer'];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}

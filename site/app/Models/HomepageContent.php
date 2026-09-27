<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HomepageContent extends Model
{
    protected $fillable = ['content', 'logo_path', 'favicon_path', 'orbit_logo_path', 'contact_background_path'];

    protected function casts(): array
    {
        return ['content' => 'array'];
    }

    public function publicImageUrl(string $field, string $directory): ?string
    {
        if (! in_array($field, ['logo_path', 'favicon_path', 'orbit_logo_path', 'contact_background_path'], true)) {
            return null;
        }

        $path = $this->{$field};
        if (! is_string($path) || preg_match('~^'.preg_quote($directory, '~').'/[A-Za-z0-9/_-]+\.(?:avif|jpe?g|png|webp)$~i', $path) !== 1
            || str_contains($path, '..') || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        return asset('storage/'.$path);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    private const PUBLIC_COPY_FIELDS = ['title_ar', 'title_en', 'summary_ar', 'summary_en', 'body_ar', 'body_en'];

    protected $fillable = ['slug', 'title_ar', 'title_en', 'summary_ar', 'summary_en', 'scope_ar', 'scope_en', 'body_ar', 'body_en', 'cover_path', 'gallery_paths', 'technologies', 'demo_url', 'demo_status', 'project_type', 'display_permission_confirmed', 'featured', 'featured_in_demos', 'published', 'sort_order'];

    protected function casts(): array
    {
        return ['gallery_paths' => 'array', 'technologies' => 'array', 'featured' => 'boolean', 'featured_in_demos' => 'boolean', 'published' => 'boolean', 'display_permission_confirmed' => 'boolean'];
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        $query->where('published', true);
        foreach (self::PUBLIC_COPY_FIELDS as $field) {
            $query->whereRaw("TRIM({$field}) <> ''");
        }

        return $query->where(function (Builder $query): void {
            $query->where('project_type', 'concept')
                ->orWhere(fn (Builder $query) => $query->where('project_type', 'client')->where('display_permission_confirmed', true));
        });
    }

    public function isPubliclyVisible(): bool
    {
        if (! $this->published || ($this->project_type === 'client' && ! $this->display_permission_confirmed)
            || ! in_array($this->project_type, ['concept', 'client'], true)) {
            return false;
        }

        foreach (self::PUBLIC_COPY_FIELDS as $field) {
            if (trim((string) $this->{$field}) === '') {
                return false;
            }
        }

        return true;
    }

    public function hasLiveDemo(): bool
    {
        return $this->demo_status === 'ready' && self::isPublicDemoUrl($this->demo_url);
    }

    public function tourImages(): array
    {
        return array_values(array_filter(array_merge(
            $this->cover_path ? [$this->cover_path] : [],
            is_array($this->gallery_paths) ? $this->gallery_paths : [],
        ), fn ($path): bool => is_string($path) && str_starts_with($path, 'projects/')
            && preg_match('/^[a-zA-Z0-9_\/.-]+\.(?:avif|jpe?g|png|webp)$/i', $path) === 1
            && ! str_contains($path, '..') && Storage::disk('public')->exists($path)));
    }

    public static function isPublicDemoUrl(?string $url): bool
    {
        if (! $url) {
            return false;
        }

        $parts = parse_url($url);
        if (! is_array($parts)) {
            return false;
        }

        $host = strtolower(rtrim($parts['host'] ?? '', '.'));

        return ($parts['scheme'] ?? null) === 'https'
            && filter_var($url, FILTER_VALIDATE_URL) !== false
            && ! array_key_exists('user', $parts)
            && ! array_key_exists('pass', $parts)
            && filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false
            && str_contains($host, '.')
            && $host !== 'localhost'
            && ! str_ends_with($host, '.local')
            && ! str_ends_with($host, '.localhost')
            && ! str_ends_with($host, '.internal')
            && ! str_ends_with($host, '.test')
            && ! str_ends_with($host, '.example')
            && ! str_ends_with($host, '.invalid')
            && ! in_array($host, ['example.com', 'example.net', 'example.org'], true)
            && ! str_ends_with($host, '.example.com')
            && ! str_ends_with($host, '.example.net')
            && ! str_ends_with($host, '.example.org')
            && filter_var($host, FILTER_VALIDATE_IP) === false;
    }
}

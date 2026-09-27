<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HomepageVideo extends Model
{
    protected $fillable = ['video_path', 'video_url', 'published'];

    protected function casts(): array
    {
        return ['published' => 'boolean'];
    }

    public function publicUrl(): ?string
    {
        if ($this->video_path && Storage::disk('public')->exists($this->video_path)) {
            return Storage::disk('public')->url($this->video_path);
        }

        $url = trim((string) $this->video_url);
        if ($url !== '' && str_starts_with($url, 'https://')
            && filter_var($url, FILTER_VALIDATE_URL) !== false
            && str_ends_with(strtolower((string) parse_url($url, PHP_URL_PATH)), '.mp4')) {
            return $url;
        }

        return null;
    }
}

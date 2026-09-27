<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactChannel extends Model
{
    public const PLATFORMS = ['LinkedIn', 'Instagram', 'Facebook', 'X', 'Behance', 'Dribbble', 'GitHub', 'YouTube', 'WhatsApp', 'Email'];

    private const HOSTS = [
        'LinkedIn' => ['linkedin.com', 'www.linkedin.com'],
        'Instagram' => ['instagram.com', 'www.instagram.com'],
        'Facebook' => ['facebook.com', 'www.facebook.com', 'm.facebook.com'],
        'X' => ['x.com', 'www.x.com', 'twitter.com'],
        'Behance' => ['behance.net', 'www.behance.net'],
        'Dribbble' => ['dribbble.com', 'www.dribbble.com'],
        'GitHub' => ['github.com', 'www.github.com'],
        'YouTube' => ['youtube.com', 'www.youtube.com'],
        'WhatsApp' => ['wa.me'],
    ];

    protected $fillable = ['platform', 'url', 'published', 'sort_order'];

    protected function casts(): array
    {
        return ['published' => 'boolean', 'sort_order' => 'integer'];
    }

    public function publicUrl(): ?string
    {
        if (! $this->published || ! is_string($this->url) || trim($this->url) === '') {
            return null;
        }

        if ($this->platform === 'Email') {
            $email = str_starts_with($this->url, 'mailto:') ? substr($this->url, 7) : $this->url;

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return null;
            }

            $domain = strtolower(substr(strrchr($email, '@'), 1));
            foreach (['example.com', 'example.net', 'example.org', 'test', 'example', 'invalid', 'localhost'] as $reserved) {
                if ($domain === $reserved || str_ends_with($domain, '.'.$reserved)) {
                    return null;
                }
            }

            return 'mailto:'.$email;
        }

        $parts = parse_url($this->url);
        if (! is_array($parts)) {
            return null;
        }

        $host = strtolower($parts['host'] ?? '');
        $path = trim($parts['path'] ?? '', '/');

        return ($parts['scheme'] ?? null) === 'https'
            && in_array($host, self::HOSTS[$this->platform] ?? [], true)
            && $path !== '' && empty($parts['user']) && empty($parts['pass'])
            && filter_var($this->url, FILTER_VALIDATE_URL)
            ? $this->url : null;
    }
}

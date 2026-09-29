<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Tests\TestCase;

class ContentTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_round_trip_excludes_accounts_and_refuses_overwrite(): void
    {
        Service::create([
            'slug' => 'sample-transfer', 'title_ar' => 'خدمة',
            'title_en' => 'Service', 'category' => 'web',
        ]);
        User::factory()->create(['email' => 'private-transfer@example.com']);
        $path = storage_path('app/content-transfer-test-'.Str::random(12).'.json');

        try {
            $this->assertSame(0, Artisan::call('keenguild:content-transfer', [
                'direction' => 'export', 'path' => $path,
            ]));
            $json = file_get_contents($path);
            $this->assertStringNotContainsString('private-transfer@example.com', $json);
            $this->assertStringNotContainsString('"users"', $json);

            $this->assertSame(1, Artisan::call('keenguild:content-transfer', [
                'direction' => 'import', 'path' => $path,
            ]));

            Service::query()->delete();
            User::query()->delete();
            $result = Artisan::call('keenguild:content-transfer', [
                'direction' => 'import', 'path' => $path,
                '--replace-migration-defaults' => true,
            ]);
            $this->assertSame(0, $result, Artisan::output());
            $this->assertDatabaseHas('services', ['slug' => 'sample-transfer']);
            $this->assertSame(0, User::count());
        } finally {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }
}

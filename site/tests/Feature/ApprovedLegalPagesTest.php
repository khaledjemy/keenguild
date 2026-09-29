<?php

namespace Tests\Feature;

use App\Models\InquirySetting;
use App\Models\LegalPage;
use Database\Seeders\ApprovedLegalPagesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovedLegalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_approved_copy_is_bilingual_and_enables_inquiries(): void
    {
        $this->seed(ApprovedLegalPagesSeeder::class);

        $this->assertSame(2, LegalPage::publiclyVisible()->count());
        $this->assertTrue(InquirySetting::current()->intake_requested);
        $this->get('/ar/legal/privacy')->assertOk()->assertSee('12 شهرًا');
        $this->get('/en/legal/privacy')->assertOk()->assertSee('12 months');
        $this->get('/ar/legal/terms')->assertOk()->assertSee('KeenGuild');
        $this->get('/en/legal/terms')->assertOk()->assertSee('info@keenguild.com');
    }

    public function test_seeder_preserves_existing_owner_edits(): void
    {
        LegalPage::create([
            'type' => 'privacy', 'title_ar' => 'مخصص', 'title_en' => 'Custom',
            'body_ar' => 'محتوى مخصص', 'body_en' => 'Custom content', 'published' => false,
        ]);

        $this->seed(ApprovedLegalPagesSeeder::class);

        $this->assertSame('Custom content', LegalPage::where('type', 'privacy')->first()->body_en);
        $this->assertFalse(InquirySetting::current()?->intake_requested ?? false);
    }
}

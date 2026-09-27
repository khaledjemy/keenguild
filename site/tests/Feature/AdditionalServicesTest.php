<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Service;
use Database\Seeders\AdditionalServicesSeeder;
use Database\Seeders\ApprovedWebPackagesSeeder;
use Database\Seeders\CatalogDraftSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class AdditionalServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_additional_services_are_public_without_invented_prices(): void
    {
        $this->seed(CatalogDraftSeeder::class);
        $this->seed(ApprovedWebPackagesSeeder::class);
        $this->seed(AdditionalServicesSeeder::class);

        $this->assertSame(7, Service::where('published', true)->count());
        $this->assertSame(4, Package::where('published', true)->count());
        $this->assertDatabaseHas('packages', ['slug' => 'custom', 'pricing_mode' => 'custom_quote', 'base_price_egp' => null, 'published' => true]);
        $this->get('/ar/services')->assertOk()->assertSee('تطبيقات الموبايل')->assertSee('التكاملات وواجهات API');
        $this->get('/ar/services/web-apps')->assertOk()->assertSee('لا يوجد سعر ثابت');
        $this->get('/ar/services/mobile-apps')->assertOk()
            ->assertSee('ليس لها سعر بداية معتمد')
            ->assertSee('/ar/pricing#how-pricing-works')
            ->assertDontSee('احكِ لنا عن مشروعك');
        $this->get('/en/services/ux-ui')->assertOk()
            ->assertSee('no approved starting price')
            ->assertSee('/en/pricing#how-pricing-works');
        $this->get('/ar/pricing')->assertOk()
            ->assertSee('تطبيقات الموبايل')->assertSee('تصميم UX/UI')
            ->assertSee('التكاملات وواجهات API')->assertSee('الصيانة والتحسينات')
            ->assertSee('لا يوجد سعر بداية معتمد لهذه الخدمات حتى تُراجع المتطلبات.')
            ->assertSee('/ar/services/mobile-apps')
            ->assertDontSee('8,000–18,000');
        $this->get('/en/pricing')->assertOk()
            ->assertSee('Mobile apps')->assertSee('UX/UI design')
            ->assertSee('Integrations & APIs')->assertSee('Maintenance & optimisation')
            ->assertSee('No starting price is approved for these services until requirements are reviewed.');
    }

    public function test_reseeding_does_not_override_later_admin_changes(): void
    {
        $this->seed(CatalogDraftSeeder::class);
        $this->seed(AdditionalServicesSeeder::class);

        Service::where('slug', 'mobile-apps')->update(['published' => false, 'body_ar' => 'نص بعد تعديل المدير']);
        $this->seed(AdditionalServicesSeeder::class);

        $this->assertDatabaseHas('services', ['slug' => 'mobile-apps', 'published' => false, 'body_ar' => 'نص بعد تعديل المدير']);
        $this->assertSame(1, DB::table('catalog_approvals')->where('key', 'additional_services_v1')->count());
    }

    public function test_partial_approval_is_not_overwritten(): void
    {
        $this->seed(CatalogDraftSeeder::class);
        Service::where('slug', 'mobile-apps')->update(['published' => true]);

        $this->expectException(RuntimeException::class);
        $this->seed(AdditionalServicesSeeder::class);
    }

    public function test_draft_catalog_must_exist_first(): void
    {
        $this->expectException(RuntimeException::class);
        $this->seed(AdditionalServicesSeeder::class);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\PriceOption;
use App\Models\Service;
use Database\Seeders\ApprovedWebPackagesSeeder;
use Database\Seeders\CatalogDraftSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class ApprovedPackagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_approval_publishes_only_three_scoped_bases_once_and_preserves_admin_edits(): void
    {
        $this->seed(CatalogDraftSeeder::class);
        $this->seed(ApprovedWebPackagesSeeder::class);

        $this->assertSame(3, Package::where('published', true)->count());
        $this->assertSame(0, PriceOption::where('published', true)->count());
        $this->assertDatabaseHas('packages', ['slug' => 'landing', 'base_price_egp' => 6000, 'included_pages' => 1, 'published' => true]);
        $this->assertDatabaseHas('packages', ['slug' => 'company', 'base_price_egp' => 14000, 'included_pages' => 5, 'published' => true]);
        $this->assertDatabaseHas('packages', ['slug' => 'store', 'base_price_egp' => 50000, 'published' => true]);
        $this->get('/ar/pricing')->assertOk()
            ->assertSee('6,000')->assertSee('14,000')->assertSee('50,000')
            ->assertSee('صفحة هبوط واحدة')->assertSee('حتى خمس صفحات')
            ->assertSee('عرض المنتجات')->assertSee('سلة شراء')->assertSee('طلبات أساسية')
            ->assertSee('ربط بوابات الدفع وشركات الشحن')
            ->assertSee('سعر بداية استرشادي، وليس عرضًا نهائيًا');
        $this->get('/en/pricing')->assertOk()
            ->assertSee('One landing page')->assertSee('Up to five pages')
            ->assertSee('Product catalog')->assertSee('Shopping cart')->assertSee('Basic orders')
            ->assertSee('Payment gateway and shipping integrations')
            ->assertSee('Indicative starting price, not a final quote');
        $this->get('/ar/services/ecommerce')->assertOk()
            ->assertSee('يبدأ من 50,000 ج.م')
            ->assertSee('سعر استرشادي، وليس عرضًا نهائيًا')
            ->assertSee('سلة شراء')->assertSee('ربط بوابات الدفع وشركات الشحن');
        $this->get('/en/services/ecommerce')->assertOk()
            ->assertSee('From 50,000 EGP')
            ->assertSee('Indicative price, not a final quote')
            ->assertSee('Shopping cart')->assertSee('Payment gateway and shipping integrations');
        $this->get('/ar/services/websites')->assertOk()->assertSee('صفحة الهبوط تعرض فكرة واحدة');
        $this->get('/en/services/websites')->assertOk()->assertSee('A landing page focuses on one message');
        $this->get('/ar/services/ecommerce')->assertOk()->assertSee('ربط بوابات الدفع وشركات الشحن ليس ضمن سعر البداية');
        $this->get('/en/services/ecommerce')->assertOk()->assertSee('Payment gateway and shipping integrations are outside the starting price');

        Package::where('slug', 'company')->update(['base_price_egp' => 31000, 'published' => false]);
        $this->seed(ApprovedWebPackagesSeeder::class);

        $this->assertDatabaseHas('packages', ['slug' => 'company', 'base_price_egp' => 31000, 'published' => false]);
        $this->assertSame(1, DB::table('catalog_approvals')->where('key', 'approved_web_packages_v2')->count());
        $this->assertSame(1, DB::table('catalog_approvals')->where('key', 'web_service_details_v1')->count());
    }

    public function test_web_service_details_preserve_existing_admin_copy(): void
    {
        $this->seed(CatalogDraftSeeder::class);
        Service::where('slug', 'websites')->update(['body_ar' => 'نص المالك المعتمد']);

        $this->seed(ApprovedWebPackagesSeeder::class);

        $service = Service::where('slug', 'websites')->firstOrFail();
        $this->assertSame('نص المالك المعتمد', $service->body_ar);
        $this->assertNotEmpty($service->body_en);

        $service->update(['body_en' => 'Owner-approved English copy']);
        $this->seed(ApprovedWebPackagesSeeder::class);
        $this->assertSame('Owner-approved English copy', $service->fresh()->body_en);
    }

    public function test_approval_requires_draft_catalog_first(): void
    {
        $this->expectException(RuntimeException::class);
        $this->seed(ApprovedWebPackagesSeeder::class);
    }

    public function test_existing_published_packages_are_not_silently_labeled_approved_when_price_or_scope_differs(): void
    {
        $this->seed(CatalogDraftSeeder::class);
        $this->seed(ApprovedWebPackagesSeeder::class);
        DB::table('catalog_approvals')->where('key', 'approved_web_packages_v2')->delete();
        Package::where('slug', 'company')->update(['base_price_egp' => 31000]);

        try {
            $this->seed(ApprovedWebPackagesSeeder::class);
            $this->fail('A changed published price must require manual review.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('Review them manually', $exception->getMessage());
        }

        $this->assertSame(0, DB::table('catalog_approvals')->where('key', 'approved_web_packages_v2')->count());
        Package::where('slug', 'company')->update(['base_price_egp' => 14000, 'included_pages' => 8]);

        $this->expectException(RuntimeException::class);
        $this->seed(ApprovedWebPackagesSeeder::class);
    }

    public function test_existing_unchanged_published_packages_can_record_prior_approval_without_overwrite(): void
    {
        $this->seed(CatalogDraftSeeder::class);
        $this->seed(ApprovedWebPackagesSeeder::class);
        DB::table('catalog_approvals')->where('key', 'approved_web_packages_v2')->delete();

        $this->seed(ApprovedWebPackagesSeeder::class);

        $this->assertSame(1, DB::table('catalog_approvals')->where('key', 'approved_web_packages_v2')->count());
        $this->assertDatabaseHas('packages', ['slug' => 'company', 'base_price_egp' => 14000, 'published' => true]);
    }

    public function test_equal_admin_sort_orders_show_lower_starting_price_first_but_explicit_order_wins(): void
    {
        $this->seed(CatalogDraftSeeder::class);
        $this->seed(ApprovedWebPackagesSeeder::class);

        $html = $this->get('/ar/pricing')->assertOk()->getContent();
        $this->assertLessThan(strpos($html, '<h3>موقع شركة</h3>'), strpos($html, '<h3>صفحة هبوط</h3>'));

        Package::where('slug', 'landing')->update(['sort_order' => 20]);
        Package::where('slug', 'company')->update(['sort_order' => 10]);
        $html = $this->get('/ar/pricing')->assertOk()->getContent();
        $this->assertLessThan(strpos($html, '<h3>صفحة هبوط</h3>'), strpos($html, '<h3>موقع شركة</h3>'));
    }
}

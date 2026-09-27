<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\PriceOption;
use App\Models\Service;
use App\Services\PriceEstimator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class PriceEstimatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_estimate_uses_package_price_options_and_factors(): void
    {
        $service = Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites']);
        $package = Package::create([
            'service_id' => $service->id, 'slug' => 'company', 'name_ar' => 'شركة', 'name_en' => 'Company',
            'base_price_egp' => 27000, 'pricing_mode' => 'estimate',
        ]);
        PriceOption::create([
            'package_id' => $package->id, 'code' => 'extra_page', 'label_ar' => 'صفحة إضافية',
            'label_en' => 'Extra page', 'calculation_type' => 'per_unit', 'amount_egp' => 2000,
            'max_quantity' => 10, 'published' => true,
        ]);

        $result = (new PriceEstimator)->estimate($package, ['extra_page' => 2], 1.2);

        $this->assertSame(27000, $result['base']);
        $this->assertSame(4000, $result['extras'][0]['total']);
        $this->assertSame(37000, $result['total']);
        $this->assertSame(33500, $result['low']);
        $this->assertSame(40500, $result['high']);
    }

    public function test_unpublished_option_cannot_be_priced(): void
    {
        $service = Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites']);
        $package = Package::create([
            'service_id' => $service->id, 'slug' => 'company', 'name_ar' => 'شركة', 'name_en' => 'Company',
            'base_price_egp' => 27000,
        ]);
        PriceOption::create([
            'package_id' => $package->id, 'code' => 'draft', 'label_ar' => 'مسودة',
            'label_en' => 'Draft', 'calculation_type' => 'fixed', 'amount_egp' => 5000,
            'published' => false,
        ]);

        $this->expectException(InvalidArgumentException::class);
        (new PriceEstimator)->estimate($package, ['draft' => 1]);
    }

    public function test_indicative_range_never_drops_below_the_approved_starting_price(): void
    {
        $service = Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites']);
        $package = Package::create([
            'service_id' => $service->id, 'slug' => 'company', 'name_ar' => 'شركة', 'name_en' => 'Company',
            'base_price_egp' => 27000, 'pricing_mode' => 'estimate',
        ]);

        $result = (new PriceEstimator)->estimate($package, []);

        $this->assertSame(27000, $result['base']);
        $this->assertSame(27000, $result['total']);
        $this->assertSame(27000, $result['low']);
        $this->assertSame(29500, $result['high']);
    }

    public function test_fixed_option_cannot_be_multiplied_and_labels_follow_locale(): void
    {
        $service = Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites']);
        $package = Package::create(['service_id' => $service->id, 'slug' => 'landing', 'name_ar' => 'هبوط', 'name_en' => 'Landing', 'base_price_egp' => 12000, 'pricing_mode' => 'estimate']);
        PriceOption::create(['package_id' => $package->id, 'code' => 'analytics', 'label_ar' => 'تحليلات', 'label_en' => 'Analytics', 'calculation_type' => 'fixed', 'amount_egp' => 3000, 'max_quantity' => 3, 'published' => true]);

        $estimator = new PriceEstimator;
        $this->assertSame('Analytics', $estimator->estimate($package, ['analytics' => 1], locale: 'en')['extras'][0]['label']);

        $this->expectException(InvalidArgumentException::class);
        $estimator->estimate($package, ['analytics' => 2]);
    }
}

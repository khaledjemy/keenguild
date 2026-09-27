<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\PriceOption;
use App\Models\PricingSetting;
use App\Models\Service;
use Illuminate\Database\Seeder;

class CatalogDraftSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PricingSetting::FACTOR_DEFAULTS as $key => $value) {
            PricingSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        $services = [
            ['websites', 'web', 'تصميم وتطوير المواقع', 'Website design & development', 'مواقع سريعة وواضحة ومناسبة لعملائك، من صفحة هبوط حتى موقع شركة متكامل.', 'Fast, clear websites built around your customers.', true],
            ['ecommerce', 'web', 'المتاجر الإلكترونية', 'E-commerce', 'متاجر تعرض المنتجات وتسهّل الطلب والدفع والإدارة.', 'Stores that simplify products, orders, payments and operations.', true],
            ['web-apps', 'software', 'تطبيقات الويب وأنظمة SaaS', 'Web apps & SaaS', 'منصات ولوحات إدارة مخصصة لعمليات العمل.', 'Custom platforms and dashboards for business workflows.', true],
            ['mobile-apps', 'software', 'تطبيقات الموبايل', 'Mobile apps', 'حلول موبايل تُحدد تقنيتها وتكلفتها بعد فهم النطاق.', 'Mobile solutions scoped after requirements discovery.', false],
            ['ux-ui', 'design', 'تصميم UX/UI', 'UX/UI design', 'تجارب وواجهات واضحة قابلة للاختبار والتطوير.', 'Clear, testable experiences and interfaces.', false],
            ['integrations', 'software', 'التكاملات وواجهات API', 'Integrations & APIs', 'ربط الأنظمة والخدمات وتبادل البيانات بأمان.', 'Connect systems and services with reliable APIs.', false],
            ['maintenance', 'support', 'الصيانة والتحسينات', 'Maintenance & optimisation', 'متابعة تقنية وتحسينات دورية وفق نطاق متفق عليه.', 'Technical care and improvements within an agreed scope.', false],
        ];

        foreach ($services as [$slug, $category, $ar, $en, $summaryAr, $summaryEn, $featured]) {
            Service::firstOrCreate(['slug' => $slug], [
                'category' => $category, 'title_ar' => $ar, 'title_en' => $en,
                'summary_ar' => $summaryAr, 'summary_en' => $summaryEn,
                'featured' => $featured, 'published' => false,
            ]);
        }

        $draftPackages = [
            ['websites', 'landing', 'صفحة هبوط', 'Landing page', 6000, 1],
            ['websites', 'company', 'موقع شركة', 'Company website', 14000, 5],
            ['ecommerce', 'store', 'متجر إلكتروني', 'Online store', 50000, 0],
            ['web-apps', 'custom', 'تطبيق ويب مخصص', 'Custom web app', null, 0],
        ];

        foreach ($draftPackages as [$serviceSlug, $slug, $ar, $en, $price, $pages]) {
            $service = Service::where('slug', $serviceSlug)->firstOrFail();
            Package::firstOrCreate(['service_id' => $service->id, 'slug' => $slug], [
                'name_ar' => $ar, 'name_en' => $en, 'base_price_egp' => $price,
                'pricing_mode' => $price === null ? 'custom_quote' : 'estimate',
                'included_pages' => $pages, 'published' => false,
                'excluded_costs' => ['الدومين والاستضافة والتراخيص الخارجية إلا إذا ذُكرت في العرض'],
                'excluded_costs_en' => ['Domain, hosting and third-party licences unless explicitly included in the proposal'],
            ]);
        }

        $optionsByPackage = [
            'landing' => [
                ['second_language', 'لغة ثانية', 'Second language', 'percent', 0, 20, 1],
                ['advanced_form', 'نموذج تواصل متقدم', 'Advanced contact form', 'fixed', 3000, 0, 1],
                ['custom_animation', 'حركات مخصصة', 'Custom animations', 'fixed', 4000, 0, 1],
                ['analytics', 'إعداد التحليلات', 'Analytics setup', 'fixed', 3000, 0, 1],
                ['content_page', 'كتابة محتوى الصفحة', 'Page copywriting', 'fixed', 1200, 0, 1],
            ],
            'company' => [
                ['extra_page', 'صفحة إضافية', 'Extra page', 'per_unit', 2000, 0, 20],
                ['second_language', 'لغة ثانية', 'Second language', 'percent', 0, 20, 1],
                ['blog', 'مدونة ولوحة مقالات', 'Blog and article editor', 'fixed', 8000, 0, 1],
                ['booking', 'نظام حجز', 'Booking system', 'fixed', 12000, 0, 1],
                ['integration', 'تكامل خارجي', 'External integration', 'per_unit', 8000, 0, 5],
                ['content_page', 'كتابة محتوى لصفحة', 'Page copywriting', 'per_unit', 1200, 0, 20],
            ],
            'store' => [
                ['second_language', 'لغة ثانية', 'Second language', 'percent', 0, 20, 1],
                ['product_import', 'استيراد 50 منتجاً', 'Import 50 products', 'per_unit', 2500, 0, 20],
                ['payment_gateway', 'ربط بوابة دفع', 'Payment gateway integration', 'fixed', 8000, 0, 1],
                ['shipping_integration', 'ربط شركة شحن', 'Shipping integration', 'fixed', 7000, 0, 1],
                ['inventory_sync', 'مزامنة المخزون', 'Inventory sync', 'fixed', 12000, 0, 1],
                ['integration', 'تكامل خارجي إضافي', 'Extra external integration', 'per_unit', 8000, 0, 5],
            ],
        ];
        foreach ($optionsByPackage as $packageSlug => $options) {
            $package = Package::where('slug', $packageSlug)->firstOrFail();
            foreach ($options as [$code, $ar, $en, $type, $amount, $percent, $max]) {
                PriceOption::firstOrCreate(['package_id' => $package->id, 'code' => $code], [
                    'label_ar' => $ar, 'label_en' => $en, 'calculation_type' => $type,
                    'amount_egp' => $amount, 'percent' => $percent, 'max_quantity' => $max,
                    'published' => false,
                ]);
            }
        }
    }
}

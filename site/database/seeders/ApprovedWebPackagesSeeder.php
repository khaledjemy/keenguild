<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ApprovedWebPackagesSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            'landing' => [
                'base_price_egp' => 6000,
                'included_pages' => 1,
                'description_ar' => 'صفحة واحدة لعرض فكرتك وقيادة الزائر إلى خطوة واضحة.',
                'description_en' => 'One page to present your idea and guide visitors to a clear next step.',
                'included_features' => ['صفحة هبوط واحدة'],
                'included_features_en' => ['One landing page'],
                'excluded_costs' => ['الدومين والاستضافة والتراخيص الخارجية', 'الصفحات الإضافية والوظائف الخاصة'],
                'excluded_costs_en' => ['Domain, hosting and third-party licences', 'Extra pages and custom features'],
            ],
            'company' => [
                'base_price_egp' => 14000,
                'included_pages' => 5,
                'description_ar' => 'موقع تعريفي للشركة حتى خمس صفحات.',
                'description_en' => 'A company website with up to five pages.',
                'included_features' => ['حتى خمس صفحات'],
                'included_features_en' => ['Up to five pages'],
                'excluded_costs' => ['الدومين والاستضافة والتراخيص الخارجية', 'الصفحات والوظائف الإضافية'],
                'excluded_costs_en' => ['Domain, hosting and third-party licences', 'Extra pages and features'],
            ],
            'store' => [
                'base_price_egp' => 50000,
                'included_pages' => 0,
                'description_ar' => 'عرض المنتجات وسلة الشراء وإدارة الطلبات الأساسية.',
                'description_en' => 'Product catalog, cart and basic order management.',
                'included_features' => ['عرض المنتجات', 'سلة شراء', 'طلبات أساسية'],
                'included_features_en' => ['Product catalog', 'Shopping cart', 'Basic orders'],
                'excluded_costs' => ['ربط بوابات الدفع وشركات الشحن', 'الدومين والاستضافة والتراخيص ورسوم مزودي الخدمة'],
                'excluded_costs_en' => ['Payment gateway and shipping integrations', 'Domain, hosting, licences and provider fees'],
            ],
        ];

        DB::transaction(function () use ($packages): void {
            $approvalKey = 'approved_web_packages_v2';
            if (DB::table('catalog_approvals')->where('key', $approvalKey)->exists()) {
                return;
            }

            $expectedServices = ['landing' => 'websites', 'company' => 'websites', 'store' => 'ecommerce'];
            $records = [];
            foreach ($packages as $slug => $content) {
                $record = Package::query()->where('slug', $slug)->whereHas('service', fn ($query) => $query->where('slug', $expectedServices[$slug]))->first();
                if (! $record) {
                    throw new RuntimeException('Run CatalogDraftSeeder before approving web packages.');
                }
                $records[$slug] = $record;
            }

            // An older database may already contain the approved public packages.
            // Do not label arbitrary published prices or scopes as owner-approved.
            if (collect($records)->every(fn (Package $package) => $package->published)) {
                foreach ($records as $slug => $record) {
                    $approved = $packages[$slug];
                    if ($record->pricing_mode !== 'estimate'
                        || $record->base_price_egp !== $approved['base_price_egp']
                        || $record->included_pages !== $approved['included_pages']
                        || $record->included_features !== $approved['included_features']
                        || $record->included_features_en !== $approved['included_features_en']
                        || $record->excluded_costs !== $approved['excluded_costs']
                        || $record->excluded_costs_en !== $approved['excluded_costs_en']) {
                        throw new RuntimeException('Published package prices or scope differ from the owner-approved version. Review them manually.');
                    }
                }
                DB::table('catalog_approvals')->insert(['key' => $approvalKey, 'created_at' => now()]);

                return;
            }

            foreach ($records as $slug => $record) {
                if ($record->published || $record->base_price_egp !== $packages[$slug]['base_price_egp']) {
                    throw new RuntimeException('Packages were edited or partially published. Review them in the admin panel instead of reapplying the approval seed.');
                }
            }

            Service::query()->whereIn('slug', ['websites', 'ecommerce'])->update(['published' => true]);
            Service::query()->where('slug', 'ecommerce')->update([
                'summary_ar' => 'متاجر تعرض المنتجات وتدير السلة والطلبات الأساسية؛ ربط الدفع والشحن يُحسب منفصلًا.',
                'summary_en' => 'Stores with products, cart and basic orders; payment and shipping integrations are separate.',
            ]);

            foreach ($records as $slug => $record) {
                $record->update($packages[$slug] + ['published' => true]);
            }

            DB::table('catalog_approvals')->insert(['key' => $approvalKey, 'created_at' => now()]);
        });

        $this->call(WebServiceDetailsSeeder::class);
    }
}

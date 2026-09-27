<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WebServiceDetailsSeeder extends Seeder
{
    public function run(): void
    {
        $copy = [
            'websites' => [
                'body_ar' => "نبدأ بتحديد هدف الموقع والجمهور والصفحات التي تخدمه. صفحة الهبوط تعرض فكرة واحدة وتقود الزائر إلى خطوة واضحة، بينما موقع الشركة يرتب المعلومات والخدمات وطريقة التواصل حتى خمس صفحات ضمن باقة البداية.\n\nنحدد في العرض النهائي ما يشمله التصميم والتطوير والمحتوى والتسليم. الصفحات والوظائف الإضافية، والدومين والاستضافة والتراخيص الخارجية تُحسب منفصلة ما لم تُذكر صراحة.",
                'body_en' => "We start by defining the website goal, audience and pages. A landing page focuses on one message and a clear next step; a company website structures its information, services and contact route across up to five pages in the starting package.\n\nThe final proposal states what design, development, content and handover include. Extra pages and features, domain, hosting and third-party licences are separate unless expressly included.",
            ],
            'ecommerce' => [
                'body_ar' => "نحدد طريقة عرض المنتجات وخطوات السلة والطلبات الأساسية وفق احتياج النشاط. باقة البداية تغطي هذه الوظائف الأساسية؛ عدد المنتجات والتخصيصات والتكاملات يتحدد بعد مراجعة المتطلبات.\n\nربط بوابات الدفع وشركات الشحن ليس ضمن سعر البداية، وكذلك الدومين والاستضافة والتراخيص ورسوم مزودي الخدمة؛ تُذكر هذه البنود منفصلة في العرض النهائي.",
                'body_en' => "We define how products are presented and how the cart and basic orders should work for the business. The starting package covers those core functions; product volume, customisation and integrations follow a requirements review.\n\nPayment gateway and shipping integrations are outside the starting price. Domain, hosting, licences and provider fees are also quoted separately in the final proposal.",
            ],
        ];

        DB::transaction(function () use ($copy): void {
            $key = 'web_service_details_v1';
            if (DB::table('catalog_approvals')->where('key', $key)->exists()) {
                return;
            }

            $services = Service::query()->whereIn('slug', array_keys($copy))->get()->keyBy('slug');
            if ($services->count() !== count($copy)) {
                throw new RuntimeException('Run CatalogDraftSeeder before adding web service details.');
            }

            foreach ($copy as $slug => $fields) {
                $updates = [];
                foreach ($fields as $field => $value) {
                    if (blank($services[$slug]->{$field})) {
                        $updates[$field] = $value;
                    }
                }
                if ($updates !== []) {
                    $services[$slug]->update($updates);
                }
            }

            DB::table('catalog_approvals')->insert(['key' => $key, 'created_at' => now()]);
        });
    }
}

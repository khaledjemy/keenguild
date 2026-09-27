<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AdditionalServicesSeeder extends Seeder
{
    public function run(): void
    {
        $copy = [
            'web-apps' => [
                'body_ar' => 'نحدد سير العمل والمستخدمين والصلاحيات والبيانات قبل اختيار التقنية. يمكن أن يشمل النطاق لوحة إدارة أو تطبيق ويب أو منتج SaaS، ويصدر عرض تفصيلي بعد مراجعة المتطلبات؛ لا يوجد سعر ثابت لهذه الفئة.',
                'body_en' => 'We define workflows, users, permissions and data before choosing the technology. The scope may include an admin panel, web app or SaaS product. A detailed proposal follows requirements review; this category has no fixed price.',
            ],
            'mobile-apps' => [
                'body_ar' => 'نراجع هدف التطبيق والمنصات المطلوبة والتكاملات ومحتوى النسخة الأولى. نحدد التقنية والجدول والتكلفة بعد توضيح النطاق، وليس من اسم «تطبيق موبايل» وحده.',
                'body_en' => 'We review the app goal, target platforms, integrations and first-release scope. Technology, schedule and cost are defined after scoping, not from the phrase “mobile app” alone.',
            ],
            'ux-ui' => [
                'body_ar' => 'نرتب رحلة المستخدم وتدفق المهام والواجهات وفق هدف المنتج. يحدد العرض ما إذا كان العمل بحثًا، نماذج أولية، نظام تصميم، أو واجهات جاهزة للتطوير.',
                'body_en' => 'We shape user journeys, task flows and interfaces around the product goal. The proposal specifies whether the work covers research, prototypes, a design system, or development-ready screens.',
            ],
            'integrations' => [
                'body_ar' => 'نراجع الأنظمة المطلوب ربطها، صلاحيات الوصول، شكل البيانات، وحدود واجهات API. أي تكلفة خدمة خارجية أو اشتراك طرف ثالث تُذكر منفصلة في العرض.',
                'body_en' => 'We review systems to connect, access permissions, data formats and API limits. Third-party service fees or subscriptions are quoted separately.',
            ],
            'maintenance' => [
                'body_ar' => 'تُحدد الصيانة وفق حالة النظام، وتيرة التحديث، مستوى الاستجابة، وحدود الدعم المتفق عليها. لا نفترض وجود دعم مفتوح أو ضمان غير مكتوب في الاتفاق.',
                'body_en' => 'Maintenance is scoped by system condition, update frequency, response level and agreed support limits. We do not imply unlimited support or an unwritten warranty.',
            ],
        ];

        DB::transaction(function () use ($copy): void {
            $key = 'additional_services_v1';
            if (DB::table('catalog_approvals')->where('key', $key)->exists()) {
                return;
            }

            $services = Service::query()->whereIn('slug', array_keys($copy))->get()->keyBy('slug');
            $custom = Package::query()->where('slug', 'custom')->whereHas('service', fn ($query) => $query->where('slug', 'web-apps'))->first();
            if ($services->count() !== count($copy) || ! $custom) {
                throw new RuntimeException('Run CatalogDraftSeeder before publishing additional services.');
            }

            $allPublished = $services->every(fn (Service $service) => $service->published) && $custom->published;
            if ($allPublished) {
                DB::table('catalog_approvals')->insert(['key' => $key, 'created_at' => now()]);

                return;
            }

            if ($services->contains(fn (Service $service) => $service->published) || $custom->published || $custom->base_price_egp !== null || $custom->pricing_mode !== 'custom_quote') {
                throw new RuntimeException('Additional services were edited or partially published. Review them in the admin panel instead.');
            }

            foreach ($copy as $slug => $fields) {
                $services[$slug]->update($fields + ['published' => true]);
            }

            $custom->update([
                'description_ar' => 'تطبيق ويب أو نظام عمل يُحدد نطاقه بعد مراجعة المتطلبات.',
                'description_en' => 'A web application or workflow system scoped after requirements review.',
                'published' => true,
            ]);

            DB::table('catalog_approvals')->insert(['key' => $key, 'created_at' => now()]);
        });
    }
}

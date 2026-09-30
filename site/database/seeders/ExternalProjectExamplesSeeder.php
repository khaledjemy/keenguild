<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ExternalProjectExamplesSeeder extends Seeder
{
    public function run(): void
    {
        $examples = [
            ['store', 'متجر إلكتروني', 'E-commerce store', 'كتالوج منتجات، بحث، صفحة منتج وسلة. مثال مناسب لفكرة متجر قابل للتوسع.', 'Product catalogue, search, product pages and cart: a scalable store concept.', 'Vercel', 'https://demo.vercel.store/', 'https://github.com/vercel/commerce'],
            ['restaurant', 'طلبات مطعم وإدارة التشغيل', 'Restaurant ordering and operations', 'قائمة طعام وطلبات توصيل أو استلام وحجوزات، مع إدارة منفصلة للمطعم.', 'Menu, pickup or delivery orders and reservations, with a separate management area.', 'KitchenAsty', 'https://demo.kitchenasty.com/', 'https://github.com/mighty840/kitchenasty'],
            ['booking', 'حجز مواعيد وطاولات', 'Booking and reservations', 'واجهة حجز عامة مع إتاحة المواعيد، وفكرة لوحة لإدارة الحجوزات والعملاء.', 'Public booking with available slots and an owner-side reservation workflow.', 'Reservations', 'https://bookatable.vercel.app/', 'https://github.com/jenniferzliang/reservations'],
            ['real-estate', 'منصة عقارات', 'Real-estate platform', 'عروض عقارية وبحث وفلاتر وخرائط وصفحات للوكلاء والمكاتب.', 'Property listings, filters, map search and agent or agency pages.', 'Havenlytics', 'https://demo.havenlytics.com/', 'https://github.com/havenlytics/Havenlytics-Realty'],
            ['learning', 'منصة دورات تعليمية', 'Online learning platform', 'كتالوج دورات ودروس وتقدم المتعلم واختبارات ولوحة إدارة للمحتوى.', 'Course catalogue, lessons, learner progress, quizzes and content administration.', 'KnowSphere', 'https://knowsphere.billalbenz.com/', 'https://github.com/billalben/KnowSphere'],
            ['analytics', 'لوحة تحليلات SaaS', 'SaaS analytics dashboard', 'لوحة عامة لاستكشاف الزيارات والمصادر والأحداث بدل الاكتفاء بصورة ثابتة.', 'Public dashboard for exploring traffic, sources and events rather than a static mockup.', 'Plausible', 'https://plausible.io/plausible.io', 'https://github.com/plausible/analytics'],
        ];

        foreach ($examples as $order => [$slug, $titleAr, $titleEn, $summaryAr, $summaryEn, $sourceName, $demoUrl, $sourceUrl]) {
            Project::query()->firstOrCreate(['slug' => 'external-'.$slug], [
                'title_ar' => $titleAr,
                'title_en' => $titleEn,
                'summary_ar' => $summaryAr,
                'summary_en' => $summaryEn,
                'body_ar' => 'هذا مثال خارجي من '.$sourceName.'، ولم تنفذه KeenGuild. '.$summaryAr,
                'body_en' => 'This is a third-party example by '.$sourceName.', not a project built by KeenGuild. '.$summaryEn,
                'illustration_path' => 'assets/demos/'.$slug.'.png',
                'source_name' => $sourceName,
                'source_url' => $sourceUrl,
                'demo_url' => $demoUrl,
                'demo_status' => 'ready',
                'project_type' => 'external',
                'featured' => $order < 2,
                'featured_in_demos' => $order < 3,
                'published' => true,
                'sort_order' => $order + 1,
            ]);
        }
    }
}

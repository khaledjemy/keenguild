<?php

namespace App\Support;

final class ExternalDemoExamples
{
    public static function all(): array
    {
        return [
            [
                'kind' => 'store',
                'image' => 'assets/demos/store.png',
                'title_ar' => 'متجر إلكتروني',
                'title_en' => 'E-commerce store',
                'description_ar' => 'كتالوج منتجات، بحث، صفحة منتج وسلة. مثال مناسب لفكرة متجر قابل للتوسع.',
                'description_en' => 'Product catalogue, search, product pages and cart: a scalable store concept.',
                'author' => 'Vercel',
                'demo_url' => 'https://demo.vercel.store/',
                'source_url' => 'https://github.com/vercel/commerce',
            ],
            [
                'kind' => 'restaurant',
                'image' => 'assets/demos/restaurant.png',
                'title_ar' => 'طلبات مطعم وإدارة التشغيل',
                'title_en' => 'Restaurant ordering and operations',
                'description_ar' => 'قائمة طعام وطلبات توصيل أو استلام وحجوزات، مع إدارة منفصلة للمطعم.',
                'description_en' => 'Menu, pickup or delivery orders and reservations, with a separate management area.',
                'author' => 'KitchenAsty',
                'demo_url' => 'https://demo.kitchenasty.com/',
                'source_url' => 'https://github.com/mighty840/kitchenasty',
            ],
            [
                'kind' => 'booking',
                'image' => 'assets/demos/booking.png',
                'title_ar' => 'حجز مواعيد وطاولات',
                'title_en' => 'Booking and reservations',
                'description_ar' => 'واجهة حجز عامة مع إتاحة المواعيد، وفكرة لوحة لإدارة الحجوزات والعملاء.',
                'description_en' => 'Public booking with available slots and an owner-side reservation workflow.',
                'author' => 'Reservations',
                'demo_url' => 'https://bookatable.vercel.app/',
                'source_url' => 'https://github.com/jenniferzliang/reservations',
            ],
            [
                'kind' => 'real-estate',
                'image' => 'assets/demos/real-estate.png',
                'title_ar' => 'منصة عقارات',
                'title_en' => 'Real-estate platform',
                'description_ar' => 'عروض عقارية وبحث وفلاتر وخرائط وصفحات للوكلاء والمكاتب.',
                'description_en' => 'Property listings, filters, map search and agent or agency pages.',
                'author' => 'Havenlytics',
                'demo_url' => 'https://demo.havenlytics.com/',
                'source_url' => 'https://github.com/havenlytics/Havenlytics-Realty',
            ],
            [
                'kind' => 'learning',
                'image' => 'assets/demos/learning.png',
                'title_ar' => 'منصة دورات تعليمية',
                'title_en' => 'Online learning platform',
                'description_ar' => 'كتالوج دورات ودروس وتقدم المتعلم واختبارات ولوحة إدارة للمحتوى.',
                'description_en' => 'Course catalogue, lessons, learner progress, quizzes and content administration.',
                'author' => 'KnowSphere',
                'demo_url' => 'https://knowsphere.billalbenz.com/',
                'source_url' => 'https://github.com/billalben/KnowSphere',
            ],
            [
                'kind' => 'analytics',
                'image' => 'assets/demos/analytics.png',
                'title_ar' => 'لوحة تحليلات SaaS',
                'title_en' => 'SaaS analytics dashboard',
                'description_ar' => 'لوحة عامة لاستكشاف الزيارات والمصادر والأحداث بدل الاكتفاء بصورة ثابتة.',
                'description_en' => 'Public dashboard for exploring traffic, sources and events rather than a static mockup.',
                'author' => 'Plausible',
                'demo_url' => 'https://plausible.io/plausible.io',
                'source_url' => 'https://github.com/plausible/analytics',
            ],
        ];
    }
}

@extends('layouts.marketing')

@section('title', $locale === 'ar' ? 'من نحن' : 'About us')

@section('content')
<section class="hero"><div class="shell">
    <span class="eyebrow">{{ $locale === 'ar' ? 'عن KeenGuild' : 'About KeenGuild' }}</span>
    <h1>{{ \App\Models\PageContent::text('about', 'heading', $locale, $locale === 'ar' ? 'نبدأ من هدفك، ثم نبني ما يخدمه.' : 'Your goal comes first. The right product follows.') }}</h1>
    <p>{{ \App\Models\PageContent::text('about', 'intro', $locale, $locale === 'ar' ? 'KeenGuild استوديو لتصميم وتطوير المنتجات الرقمية. تركيزنا الأساسي على المواقع والمتاجر، ونحدد الحلول البرمجية الأخرى حسب احتياج كل مشروع.' : 'KeenGuild is a digital product design and development studio. Our main focus is websites and online stores, with other software scoped around each project’s needs.') }}</p>
</div></section>
<section class="section"><div class="shell">
    <div class="section-head"><div><span class="eyebrow">{{ $locale === 'ar' ? 'طريقتنا' : 'Our approach' }}</span><h2>{{ $locale === 'ar' ? 'وضوح قبل التنفيذ' : 'Clarity before implementation' }}</h2></div></div>
    <div class="grid">
        <div class="card"><span class="pill">01</span><h3>{{ \App\Models\PageContent::extra('about', 'about_1_title', $locale, $locale === 'ar' ? 'نفهم الهدف' : 'Understand the goal') }}</h3><p>{{ \App\Models\PageContent::extra('about', 'about_1_description', $locale, $locale === 'ar' ? 'نراجع الجمهور وما يجب أن يحققه المنتج قبل تحديد الصفحات والوظائف.' : 'We review the audience and desired outcome before defining pages and features.') }}</p></div>
        <div class="card"><span class="pill">02</span><h3>{{ \App\Models\PageContent::extra('about', 'about_2_title', $locale, $locale === 'ar' ? 'نحدد النطاق' : 'Define the scope') }}</h3><p>{{ \App\Models\PageContent::extra('about', 'about_2_description', $locale, $locale === 'ar' ? 'نوضح ما يدخل في التنفيذ وما يُحسب منفصلًا، ثم نقدّم تقديرًا مناسبًا.' : 'We make included work and separate costs clear, then prepare an appropriate estimate.') }}</p></div>
        <div class="card"><span class="pill">03</span><h3>{{ \App\Models\PageContent::extra('about', 'about_3_title', $locale, $locale === 'ar' ? 'نصمم ونطوّر' : 'Design and build') }}</h3><p>{{ \App\Models\PageContent::extra('about', 'about_3_description', $locale, $locale === 'ar' ? 'نرتب التجربة والتفاصيل التقنية حول استخدام حقيقي، ونراجع النتيجة قبل الإطلاق.' : 'We shape the experience and technical details around real use, then review the result before launch.') }}</p></div>
    </div>
</div></section>
<section class="section" style="padding-top:0"><div class="shell">
    <div class="section-head"><div><span class="eyebrow">{{ $locale === 'ar' ? 'مجالات العمل' : 'What we do' }}</span><h2>{{ $locale === 'ar' ? 'خدمات منشورة حاليًا' : 'Currently listed services' }}</h2></div></div>
    @if($services->isEmpty())
        <div class="empty">{{ $locale === 'ar' ? 'يجري تجهيز تفاصيل الخدمات للنشر.' : 'Service details are being prepared.' }}</div>
    @else
        <div class="grid">
            @foreach($services as $service)
                <article class="card"><h3>{{ $locale === 'ar' ? $service->title_ar : $service->title_en }}</h3><p>{{ $locale === 'ar' ? $service->summary_ar : $service->summary_en }}</p><a class="button ghost" href="{{ route('service', ['locale' => $locale, 'slug' => $service->slug]) }}">{{ $locale === 'ar' ? 'تفاصيل الخدمة ←' : 'Explore service →' }}</a></article>
            @endforeach
        </div>
    @endif
    <div class="hero-actions"><a class="button primary" href="{{ route('pricing', ['locale' => $locale]) }}">{{ $locale === 'ar' ? 'شاهد أسعار البداية' : 'See starting prices' }}</a><a class="button ghost" href="{{ route('work', ['locale' => $locale]) }}">{{ $locale === 'ar' ? 'استكشف الأعمال' : 'Explore work' }}</a></div>
</div></section>
@endsection

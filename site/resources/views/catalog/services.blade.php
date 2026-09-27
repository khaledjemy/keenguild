@extends('layouts.marketing')
@section('title', $locale === 'ar' ? 'خدماتنا' : 'Our services')
@section('content')
<section class="hero"><div class="shell">
    <span class="eyebrow">{{ $locale === 'ar' ? 'KeenGuild / الخدمات' : 'KeenGuild / Services' }}</span>
    <h1>{{ \App\Models\PageContent::text('services', 'heading', $locale, $locale === 'ar' ? 'مواقع تبني حضورك. وبرمجيات تخدم نموك.' : 'Websites that build your presence. Software that supports growth.') }}</h1>
    <p>{{ \App\Models\PageContent::text('services', 'intro', $locale, $locale === 'ar' ? 'نبدأ بفهم الهدف، ثم نحدد نطاقًا واضحًا للتصميم والتطوير. تطوير المواقع هو تخصصنا الأساسي؛ والخدمات الأخرى تُحدد وفق احتياج كل مشروع.' : 'We start with your goal, then define a clear design and development scope. Websites are our core focus; other services are scoped to each project.') }}</p>
</div></section>
<section class="section"><div class="shell">
    @if($services->isEmpty())
        <div class="empty">{{ $locale === 'ar' ? 'الخدمات قيد المراجعة قبل النشر.' : 'Services are being reviewed before publication.' }}</div>
    @else
        <div class="grid">
            @foreach($services as $service)
                <article class="card {{ $service->featured ? 'featured' : '' }}">
                    <span class="meta">{{ $service->category === 'web' ? ($locale === 'ar' ? 'مواقع' : 'Web') : ($locale === 'ar' ? 'حلول رقمية' : 'Digital solutions') }}</span>
                    <h3>{{ $locale === 'ar' ? $service->title_ar : $service->title_en }}</h3>
                    <p>{{ $locale === 'ar' ? $service->summary_ar : $service->summary_en }}</p>
                    <a class="button {{ $service->featured ? 'primary' : 'ghost' }}" href="{{ route('service', ['locale' => $locale, 'slug' => $service->slug]) }}">{{ $locale === 'ar' ? 'اعرف التفاصيل ←' : 'Explore service →' }}</a>
                </article>
            @endforeach
        </div>
    @endif
</div></section>
@endsection

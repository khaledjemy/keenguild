@extends('layouts.marketing')
@section('title', $locale === 'ar' ? $service->title_ar : $service->title_en)
@php($metaDescription = $locale === 'ar' ? $service->summary_ar : $service->summary_en)
@section('content')
<section class="hero"><div class="shell">
    @if($preview ?? false)<div class="notice" role="status">{{ $locale === 'ar' ? 'معاينة خاصة للمشرف — قد تتضمن هذه الصفحة باقات ومسودات غير منشورة للزوار.' : 'Private admin preview — this page may include unpublished service and package drafts.' }}</div>@endif
    <a class="eyebrow" href="{{ route('services', ['locale' => $locale]) }}">{{ $locale === 'ar' ? 'الخدمات / العودة' : 'Services / Back' }}</a>
    <h1>{{ $locale === 'ar' ? $service->title_ar : $service->title_en }}</h1>
    <p>{{ $locale === 'ar' ? $service->summary_ar : $service->summary_en }}</p>
</div></section>
<section class="section"><div class="shell">
    @if($locale === 'ar' ? $service->body_ar : $service->body_en)
        <div class="body-copy">{{ $locale === 'ar' ? $service->body_ar : $service->body_en }}</div>
    @endif
    @if($service->packages->isNotEmpty())
        <div class="section-head" style="margin-top:42px"><h2>{{ $locale === 'ar' ? 'الباقات المتاحة' : 'Available packages' }}</h2></div>
        <div class="grid">
            @foreach($service->packages as $package)
                <article class="card"><h3>{{ $locale === 'ar' ? $package->name_ar : $package->name_en }}</h3>
                    @if(($preview ?? false) && ! $package->published)<span class="pill">{{ $locale === 'ar' ? 'باقة مسودة' : 'Draft package' }}</span>@endif
                    <p>{{ $locale === 'ar' ? $package->description_ar : $package->description_en }}</p>
                    @if($package->pricing_mode === 'estimate' && $package->base_price_egp !== null)
                        <span class="price">{{ $locale === 'ar' ? 'يبدأ من ' : 'From ' }}{{ number_format($package->base_price_egp) }} {{ $locale === 'ar' ? 'ج.م' : 'EGP' }}</span>
                        <span class="small">{{ $locale === 'ar' ? 'سعر استرشادي، وليس عرضًا نهائيًا' : 'Indicative price, not a final quote' }}</span>
                    @else
                        <span class="price">{{ $locale === 'ar' ? 'حسب المتطلبات' : 'Custom quote' }}</span>
                    @endif
                    @php($features = $locale === 'ar' ? $package->included_features : $package->included_features_en)
                    @php($exclusions = $locale === 'ar' ? $package->excluded_costs : $package->excluded_costs_en)
                    @if($features)
                        <ul>@foreach($features as $feature)<li>{{ $feature }}</li>@endforeach</ul>
                    @endif
                    @if($exclusions)
                        <p class="small">{{ $locale === 'ar' ? 'يُحسب منفصلًا:' : 'Quoted separately:' }} {{ implode($locale === 'ar' ? '، ' : ', ', $exclusions) }}</p>
                    @endif
                    <a class="button ghost" href="{{ route('pricing', ['locale' => $locale]) }}">{{ $locale === 'ar' ? 'تفاصيل الأسعار' : 'Pricing details' }}</a>
                </article>
            @endforeach
        </div>
        <p class="small" style="margin-top:18px">{{ $locale === 'ar' ? 'الأسعار استرشادية فقط، والعرض النهائي يتحدد بعد مراجعة نطاق العمل.' : 'Prices are indicative; final quotes depend on the agreed scope.' }}</p>
    @else
        <div class="card" style="max-width:850px;margin-top:42px">
            <span class="eyebrow">{{ $locale === 'ar' ? 'الخطوة التالية' : 'Next step' }}</span>
            <h2>{{ $locale === 'ar' ? 'نحدد النطاق قبل السعر' : 'Scope first, then price' }}</h2>
            <p>{{ $locale === 'ar' ? 'هذه الخدمة ليس لها سعر بداية معتمد. نراجع الهدف والوظائف المطلوبة والتكاملات والمدة، ثم نحدد عرضًا مناسبًا بدل رقم عام قد لا يناسب مشروعك.' : 'This service has no approved starting price. We review the goal, required features, integrations and timeline before preparing a relevant proposal.' }}</p>
            <div class="hero-actions">
                <a class="button ghost" href="{{ route('pricing', ['locale' => $locale]) }}#how-pricing-works">{{ $locale === 'ar' ? 'كيف نحسب التكلفة؟' : 'How pricing works' }}</a>
                @if(app(\App\Services\InquiryAvailability::class)->enabled($locale))
                    <a class="button primary" href="{{ route('quote.create', ['locale' => $locale]) }}">{{ $locale === 'ar' ? 'احكِ لنا عن مشروعك' : 'Tell us about your project' }}</a>
                @endif
            </div>
        </div>
    @endif
</div></section>
@endsection

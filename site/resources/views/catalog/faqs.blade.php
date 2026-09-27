@extends('layouts.marketing')

@section('title', $locale === 'ar' ? 'الأسئلة الشائعة' : 'Frequently asked questions')

@section('content')
<section class="hero"><div class="shell">
    <span class="eyebrow">{{ $locale === 'ar' ? 'إجابات واضحة' : 'Clear answers' }}</span>
    <h1>{{ \App\Models\PageContent::text('faqs', 'heading', $locale, $locale === 'ar' ? 'الأسئلة الشائعة' : 'Frequently asked questions') }}</h1>
    <p>{{ \App\Models\PageContent::text('faqs', 'intro', $locale, $locale === 'ar' ? 'معلومات تساعدك تفهم نطاق العمل قبل طلب عرض تفصيلي.' : 'Helpful details to understand the scope before requesting a detailed proposal.') }}</p>
</div></section>
<section class="section"><div class="shell" style="max-width:900px">
    @forelse($faqs as $faq)
        <details class="card" style="margin-bottom:14px">
            <summary style="cursor:pointer;font-size:19px;font-weight:700">{{ $locale === 'ar' ? $faq->question_ar : $faq->question_en }}</summary>
            <p class="body-copy" style="margin-bottom:0">{{ $locale === 'ar' ? $faq->answer_ar : $faq->answer_en }}</p>
        </details>
    @empty
        <div class="empty">{{ $locale === 'ar' ? 'نعمل على تجهيز إجابات واضحة للأسئلة المتكررة.' : 'We are preparing clear answers to common questions.' }}</div>
    @endforelse
</div></section>
@endsection

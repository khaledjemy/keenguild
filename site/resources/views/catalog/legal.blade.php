@extends('layouts.marketing')
@section('title', $locale === 'ar' ? $page->title_ar : $page->title_en)
@section('content')
<section class="hero"><div class="shell">
    @if($preview ?? false)<div class="notice" role="status">{{ $locale === 'ar' ? 'معاينة خاصة للمشرف — قد تكون هذه الصفحة مسودة وغير منشورة للزوار.' : 'Private admin preview — this legal page may be a draft and is not public.' }}</div>@endif
    <span class="eyebrow">KeenGuild / {{ $locale === 'ar' ? 'معلومات قانونية' : 'Legal information' }}</span>
    <h1>{{ $locale === 'ar' ? $page->title_ar : $page->title_en }}</h1>
    <p>{{ $locale === 'ar' ? 'آخر تحديث: ' : 'Last updated: ' }}{{ $page->updated_at->locale($locale)->translatedFormat('d F Y') }}</p>
</div></section>
<section class="section"><div class="shell">
    <div class="card body-copy">{{ $locale === 'ar' ? $page->body_ar : $page->body_en }}</div>
</div></section>
@endsection

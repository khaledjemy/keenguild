@extends('layouts.marketing')

@section('title', ($locale === 'ar' ? 'جولة تصميم: ' . $project->title_ar : 'Design walkthrough: ' . $project->title_en))
@php($metaDescription = $locale === 'ar' ? $project->summary_ar : $project->summary_en)

@section('content')
@php($images = $project->tourImages())
<section class="hero"><div class="shell">
    <a class="eyebrow" href="{{ $preview && request()->routeIs('preview.*') ? route('preview.project', ['locale' => $locale, 'slug' => $project->slug]) : route('project', ['locale' => $locale, 'slug' => $project->slug]) }}">{{ $locale === 'ar' ? '← صفحة المشروع' : '← Project page' }}</a>
    <h1>{{ $locale === 'ar' ? 'جولة تصميم: ' . $project->title_ar : 'Design walkthrough: ' . $project->title_en }}</h1>
    <p>{{ $locale === 'ar' ? 'تصفح صور المشروع لفهم التجربة بصريًا. هذه جولة صور وليست ديمو وظيفيًا أو نسخة من المنتج.' : 'Explore the project screens visually. This is an image walkthrough, not a functional demo or product copy.' }}</p>
    @if(request()->routeIs('preview.*'))<div class="notice" style="margin-top:22px">{{ $locale === 'ar' ? 'معاينة خاصة بالمشرف — المسودة غير متاحة للزوار.' : 'Admin-only preview — drafts are not public.' }}</div>@endif
</div></section>
<section class="section"><div class="shell">
    <div class="card" style="padding:16px">
        <img id="tourMainImage" src="{{ asset('storage/' . $images[0]) }}" alt="{{ $locale === 'ar' ? $project->title_ar : $project->title_en }}" style="display:block;width:100%;max-height:min(70vh,680px);object-fit:contain;border-radius:14px;background:#eaf2f5">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-top:14px">
            <button id="tourPrevious" type="button" class="button ghost">{{ $locale === 'ar' ? 'السابق' : 'Previous' }}</button>
            <span id="tourCounter" role="status" aria-live="polite">1 / {{ count($images) }}</span>
            <button id="tourNext" type="button" class="button primary">{{ $locale === 'ar' ? 'التالي' : 'Next' }}</button>
        </div>
    </div>
    <div style="display:flex;gap:10px;overflow-x:auto;margin-top:16px;padding-bottom:8px" aria-label="{{ $locale === 'ar' ? 'صور الجولة' : 'Walkthrough images' }}">
        @foreach($images as $image)
            <button type="button" class="tour-thumb" data-src="{{ asset('storage/' . $image) }}" data-index="{{ $loop->index }}" aria-label="{{ $locale === 'ar' ? 'اعرض الصورة ' : 'Show image ' }}{{ $loop->iteration }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}" style="flex:none;padding:3px;border:2px solid {{ $loop->first ? '#12c4ea' : '#d7e4e9' }};border-radius:12px;background:#fff;cursor:pointer"><img src="{{ asset('storage/' . $image) }}" alt="" loading="lazy" style="width:110px;height:72px;object-fit:cover;border-radius:8px"></button>
        @endforeach
    </div>
</div></section>
@endsection

@push('scripts')
<script>
(() => {
    const thumbs = [...document.querySelectorAll('.tour-thumb')];
    const main = document.getElementById('tourMainImage');
    const counter = document.getElementById('tourCounter');
    const previous = document.getElementById('tourPrevious');
    const next = document.getElementById('tourNext');
    let index = 0;
    const show = target => {
        index = Math.max(0, Math.min(thumbs.length - 1, target));
        main.src = thumbs[index].dataset.src;
        counter.textContent = `${index + 1} / ${thumbs.length}`;
        previous.disabled = index === 0;
        next.disabled = index === thumbs.length - 1;
        thumbs.forEach((thumb, i) => {
            thumb.setAttribute('aria-pressed', i === index ? 'true' : 'false');
            thumb.style.borderColor = i === index ? '#12c4ea' : '#d7e4e9';
        });
    };
    thumbs.forEach((thumb, i) => thumb.addEventListener('click', () => show(i)));
    previous.addEventListener('click', () => show(index - 1));
    next.addEventListener('click', () => show(index + 1));
    document.addEventListener('keydown', event => {
        if (event.key === 'ArrowLeft') show(index - 1);
        if (event.key === 'ArrowRight') show(index + 1);
    });
    show(0);
})();
</script>
@endpush

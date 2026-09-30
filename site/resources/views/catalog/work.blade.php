@extends('layouts.marketing')

@section('title', $locale === 'ar' ? 'الأعمال' : 'Work')

@section('content')
<section class="hero"><div class="shell">
    <span class="eyebrow">{{ $locale === 'ar' ? 'أعمال وديموهات' : 'Projects and demos' }}</span>
    <h1>{{ \App\Models\PageContent::text('work', 'heading', $locale, $locale === 'ar' ? 'شوف التجربة، مش الصورة بس.' : 'Explore the experience, not just the image.') }}</h1>
    <p>{{ \App\Models\PageContent::text('work', 'intro', $locale, $locale === 'ar' ? 'تجد هنا أعمال KeenGuild ونماذج تصميمية وأمثلة خارجية مميزة بوضوح باسم أصحابها.' : 'Explore KeenGuild work, design concepts and clearly attributed third-party examples.') }}</p>
</div></section>
<section class="section"><div class="shell"><div class="section-head"><div><span class="eyebrow">{{ $locale === 'ar' ? 'مشاريع وأمثلة' : 'Projects and examples' }}</span><h2>{{ $locale === 'ar' ? 'تصفح الأعمال والأمثلة' : 'Browse work and examples' }}</h2></div></div>
    @if($projects->isEmpty())
        <div class="empty">{{ $locale === 'ar' ? 'المشاريع الحقيقية قيد التجهيز للمراجعة قبل النشر. لن نعرض نماذج على أنها أعمال منفذة.' : 'Real projects are being prepared for review. We do not present mockups as completed client work.' }}</div>
    @else
        <div class="grid">
            @foreach($projects as $project)
                <article class="card work-card">
                    @if($project->coverUrl())<img class="work-cover" src="{{ $project->coverUrl() }}" alt="{{ $locale === 'ar' ? $project->title_ar : $project->title_en }}">@else<div class="work-cover" aria-hidden="true" style="display:grid;place-items:center"><img src="{{ asset('assets/brand/favicon-new2.png') }}" alt="" width="58" height="58" style="object-fit:contain"></div>@endif
                    <div class="copy"><span class="pill">{{ $project->project_type === 'client' ? ($locale === 'ar' ? 'مشروع عميل' : 'Client project') : ($project->project_type === 'external' ? ($locale === 'ar' ? 'مثال خارجي — ليس من أعمالنا' : 'Third-party example — not our work') : ($locale === 'ar' ? 'نموذج تصميمي' : 'Design concept')) }}</span> @if($project->hasLiveDemo())<span class="pill">{{ $locale === 'ar' ? 'ديمو متاح' : 'Demo available' }}</span>@endif<h3>{{ $locale === 'ar' ? $project->title_ar : $project->title_en }}</h3><p>{{ $locale === 'ar' ? $project->summary_ar : $project->summary_en }}</p>@if($project->project_type === 'external')<p>{{ $locale === 'ar' ? 'المصدر: ' : 'By: ' }}{{ $project->source_name }}</p>@endif<div style="display:flex;flex-wrap:wrap;gap:10px"><a class="button ghost" href="{{ route('project', ['locale' => $locale, 'slug' => $project->slug]) }}">{{ $locale === 'ar' ? 'تفاصيل المشروع ←' : 'View project →' }}</a>@if($project->tourImages() !== [])<a class="button ghost" href="{{ route('project.tour', ['locale' => $locale, 'slug' => $project->slug]) }}">{{ $locale === 'ar' ? 'جولة التصميم ←' : 'Design walkthrough →' }}</a>@endif @if($project->hasLiveDemo())<a class="button primary" href="{{ $project->demo_url }}" target="_blank" rel="noopener noreferrer">{{ $locale === 'ar' ? 'جرّب الديمو ↗' : 'Try the demo ↗' }}</a>@endif @if($project->publicSourceUrl())<a class="button ghost" href="{{ $project->publicSourceUrl() }}" target="_blank" rel="noopener noreferrer">{{ $locale === 'ar' ? 'المصدر الأصلي ↗' : 'Original source ↗' }}</a>@endif</div></div>
                </article>
            @endforeach
        </div>
    @endif
</div></section>
<section class="section" style="padding-top:0"><div class="shell">
    <div class="section-head"><div><span class="eyebrow">{{ $locale === 'ar' ? 'نماذج من تصميم KeenGuild' : 'KeenGuild design studies' }}</span><h2>{{ $locale === 'ar' ? 'جرّب التصورات التفاعلية' : 'Try interactive concepts' }}</h2><p>{{ $locale === 'ar' ? 'هذه تجارب واجهة افتراضية، وليست أعمال عملاء أو منتجات متاحة للشراء.' : 'These are fictional interface studies, not client projects or products for sale.' }}</p></div></div>
    <div class="grid">
        @foreach($concepts as $slug => $concept)
            <article class="card"><span class="pill">{{ $locale === 'ar' ? 'نموذج تصميمي' : 'Design concept' }}</span><h3>{{ $concept['title'] }}</h3><p>{{ $locale === 'ar' ? $concept['ar'] : $concept['en'] }}</p><a class="button ghost" href="{{ route('concept.demo', ['locale' => $locale, 'slug' => $slug]) }}">{{ $locale === 'ar' ? 'افتح التجربة ←' : 'Open concept →' }}</a></article>
        @endforeach
    </div>
</div></section>
@endsection

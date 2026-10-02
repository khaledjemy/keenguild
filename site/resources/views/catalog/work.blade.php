@extends('layouts.marketing')

@section('title', $locale === 'ar' ? 'الأعمال' : 'Work')

@section('content')
<section class="hero"><div class="shell">
    <span class="eyebrow">{{ $locale === 'ar' ? 'أعمال KeenGuild' : 'KeenGuild work' }}</span>
    <h1>{{ \App\Models\PageContent::text('work', 'heading', $locale, $locale === 'ar' ? 'شوف التجربة، مش الصورة بس.' : 'Explore the experience, not just the image.') }}</h1>
    <p>{{ \App\Models\PageContent::text('work', 'intro', $locale, $locale === 'ar' ? 'تصفح أعمال العملاء والتصورات التي أنشأتها KeenGuild، ثم أمثلة خارجية مستقلة في قسم منفصل.' : 'Browse KeenGuild client work and design concepts, followed by independent third-party examples in a separate section.') }}</p>
</div></section>
<section class="section"><div class="shell"><div class="section-head"><div><span class="eyebrow">{{ $locale === 'ar' ? 'من تنفيذ KeenGuild' : 'By KeenGuild' }}</span><h2>{{ $locale === 'ar' ? 'أعمال العملاء والتصورات' : 'Client work and design concepts' }}</h2></div></div>
    @if($projects->isEmpty())
        <div class="empty">{{ $locale === 'ar' ? 'لا توجد أعمال عملاء أو تصورات منشورة في هذه القائمة بعد. يمكنك استكشاف النماذج التفاعلية بالأسفل.' : 'No client work or managed concepts are published in this list yet. Explore the interactive concepts below.' }}</div>
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
@if($concepts !== [])
<section class="section" style="padding-top:0"><div class="shell">
    <div class="section-head"><div><span class="eyebrow">{{ $locale === 'ar' ? 'نماذج من تصميم KeenGuild' : 'KeenGuild design studies' }}</span><h2>{{ $locale === 'ar' ? 'جرّب التصورات التفاعلية' : 'Try interactive concepts' }}</h2><p>{{ $locale === 'ar' ? 'هذه تجارب واجهة افتراضية، وليست أعمال عملاء أو منتجات متاحة للشراء.' : 'These are fictional interface studies, not client projects or products for sale.' }}</p></div></div>
    <div class="grid">
        @foreach($concepts as $slug => $concept)
            <article class="card"><span class="pill">{{ $locale === 'ar' ? 'نموذج تصميمي' : 'Design concept' }}</span><h3>{{ $concept['name_'.$locale] }}</h3><p>{{ $concept['copy_'.$locale] }}</p><a class="button ghost" href="{{ route('concept.demo', ['locale' => $locale, 'slug' => $slug]) }}">{{ $locale === 'ar' ? 'افتح التجربة ←' : 'Open concept →' }}</a></article>
        @endforeach
    </div>
</div></section>
@endif
@if($externalExamples->isNotEmpty())
<section class="section" style="padding-top:0"><div class="shell">
    <div class="section-head"><div><span class="eyebrow">{{ $locale === 'ar' ? 'إلهام من جهات أخرى' : 'Independent inspiration' }}</span><h2>{{ $locale === 'ar' ? 'أمثلة خارجية — ليست من أعمالنا' : 'Third-party examples — not our work' }}</h2><p>{{ $locale === 'ar' ? 'مشاريع تابعة لأصحابها الأصليين. نعرضها للإلهام مع ذكر المصدر، ولا ندّعي تنفيذها.' : 'Projects by their original creators, shown for inspiration with attribution. KeenGuild did not build them.' }}</p></div></div>
    <div class="grid">
        @foreach($externalExamples as $project)
            <article class="card work-card">
                @if($project->coverUrl())<img class="work-cover" src="{{ $project->coverUrl() }}" alt="{{ $locale === 'ar' ? $project->title_ar : $project->title_en }}" loading="lazy">@endif
                <div class="copy"><span class="pill">{{ $locale === 'ar' ? 'مثال خارجي — ليس من أعمالنا' : 'Third-party example — not our work' }}</span><h3>{{ $locale === 'ar' ? $project->title_ar : $project->title_en }}</h3><p>{{ $locale === 'ar' ? $project->summary_ar : $project->summary_en }}</p><p>{{ $locale === 'ar' ? 'صاحب المشروع: ' : 'By: ' }}{{ $project->source_name }}</p><div style="display:flex;flex-wrap:wrap;gap:10px"><a class="button ghost" href="{{ route('project', ['locale' => $locale, 'slug' => $project->slug]) }}">{{ $locale === 'ar' ? 'تفاصيل المثال ←' : 'View example →' }}</a>@if($project->hasLiveDemo())<a class="button primary" href="{{ $project->demo_url }}" target="_blank" rel="noopener noreferrer">{{ $locale === 'ar' ? 'جرّب الديمو ↗' : 'Try the demo ↗' }}</a>@endif @if($project->publicSourceUrl())<a class="button ghost" href="{{ $project->publicSourceUrl() }}" target="_blank" rel="noopener noreferrer">{{ $locale === 'ar' ? 'المصدر الأصلي ↗' : 'Original source ↗' }}</a>@endif</div></div>
            </article>
        @endforeach
    </div>
</div></section>
@endif
@endsection

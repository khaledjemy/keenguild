@extends('layouts.marketing')

@section('title', $locale === 'ar' ? $project->title_ar : $project->title_en)
@php($metaDescription = $locale === 'ar' ? $project->summary_ar : $project->summary_en)

@section('content')
<section class="hero"><div class="shell">
    @if($preview)<div class="notice" style="margin-bottom:28px;border-color:#f3c5a3;background:#fff5ed;color:#8b4418" role="status">{{ $locale === 'ar' ? 'معاينة للمشرف فقط — هذا المشروع قد لا يكون منشورًا للزوار.' : 'Admin-only preview — this project may not be published.' }} <a href="{{ url('/admin/projects') }}" style="text-decoration:underline">{{ $locale === 'ar' ? 'العودة لإدارة المشاريع' : 'Back to project management' }}</a></div>@endif
    <a class="eyebrow" href="{{ route('work', ['locale' => $locale]) }}">{{ $locale === 'ar' ? '← كل الأعمال' : '← All work' }}</a>
    <div style="margin-top:14px"><span class="pill">{{ $project->project_type === 'client' ? ($locale === 'ar' ? 'مشروع عميل' : 'Client project') : ($locale === 'ar' ? 'نموذج تصميمي / تجربة' : 'Design concept / demo') }}</span></div>
    <h1>{{ $locale === 'ar' ? $project->title_ar : $project->title_en }}</h1>
    <p>{{ $locale === 'ar' ? $project->summary_ar : $project->summary_en }}</p>
    @if($project->hasLiveDemo() || $project->tourImages() !== [])
        <div class="hero-actions">
            @if($project->tourImages() !== [])<a class="button ghost" href="{{ route($preview ? 'preview.project.tour' : 'project.tour', ['locale' => $locale, 'slug' => $project->slug]) }}">{{ $locale === 'ar' ? 'جولة التصميم ←' : 'Design walkthrough →' }}</a>@endif
            @if($project->hasLiveDemo())<a class="button primary" href="{{ $project->demo_url }}" target="_blank" rel="noopener noreferrer">{{ $locale === 'ar' ? 'افتح الديمو ↗' : 'Open live demo ↗' }}</a>@endif
        </div>
    @endif
</div></section>
<section class="section"><div class="shell">
    @if($project->cover_path)<img class="detail-cover" src="{{ asset('storage/' . $project->cover_path) }}" alt="{{ $locale === 'ar' ? $project->title_ar : $project->title_en }}">@endif
    @php($scope = $locale === 'ar' ? $project->scope_ar : $project->scope_en)
    @if($scope)
        <h2>{{ $locale === 'ar' ? 'نطاق العمل' : 'Scope of work' }}</h2>
        <p class="body-copy">{{ $scope }}</p>
    @endif
    <p class="body-copy">{{ $locale === 'ar' ? $project->body_ar : $project->body_en }}</p>
    @if($project->technologies)<div class="tags">@foreach($project->technologies as $technology)<span class="pill">{{ $technology }}</span>@endforeach</div>@endif
    @if($project->gallery_paths)<div class="grid" style="margin-top:30px">@foreach($project->gallery_paths as $image)<img class="work-cover" src="{{ asset('storage/' . $image) }}" alt="{{ $locale === 'ar' ? $project->title_ar : $project->title_en }}">@endforeach</div>@endif
</div></section>
@endsection

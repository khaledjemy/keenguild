@extends('layouts.marketing')

@section('title', $selectedCategory ? ($locale === 'ar' ? $selectedCategory->name_ar : $selectedCategory->name_en) : ($locale === 'ar' ? 'المقالات' : 'Articles'))
@php($metaDescription = $selectedCategory ? ($locale === 'ar' ? $selectedCategory->description_ar : $selectedCategory->description_en) : null)

@section('content')
<section class="hero"><div class="shell"><a class="eyebrow" href="{{ route('articles', ['locale' => $locale]) }}">{{ $locale === 'ar' ? 'المقالات' : 'Articles' }}</a><h1>{{ $selectedCategory ? ($locale === 'ar' ? $selectedCategory->name_ar : $selectedCategory->name_en) : \App\Models\PageContent::text('articles', 'heading', $locale, $locale === 'ar' ? 'أفكار تساعدك تبني بشكل أفضل.' : 'Ideas for building better.') }}</h1><p>{{ $selectedCategory ? (($locale === 'ar' ? $selectedCategory->description_ar : $selectedCategory->description_en) ?: ($locale === 'ar' ? 'مقالات هذا التصنيف.' : 'Articles in this category.')) : \App\Models\PageContent::text('articles', 'intro', $locale, $locale === 'ar' ? 'مقالات عن المواقع والتجربة والمنتجات الرقمية.' : 'Articles on websites, experience and digital products.') }}</p></div></section>
<section class="section"><div class="shell">
    @if($categories->isNotEmpty())
        <nav aria-label="{{ $locale === 'ar' ? 'تصنيفات المقالات' : 'Article categories' }}" class="tags" style="margin-bottom:28px">
            <a class="pill" href="{{ route('articles', ['locale' => $locale]) }}" @if(!$selectedCategory)aria-current="page"@endif>{{ $locale === 'ar' ? 'كل المقالات' : 'All articles' }}</a>
            @foreach($categories as $category)
                <a class="pill" href="{{ route('articles.category', ['locale' => $locale, 'slug' => $category->slug]) }}" @if($selectedCategory?->id === $category->id)aria-current="page"@endif>{{ $locale === 'ar' ? $category->name_ar : $category->name_en }}</a>
            @endforeach
        </nav>
    @endif
    @if($articles->isEmpty())
        <div class="empty">{{ $locale === 'ar' ? 'المقالات قيد المراجعة قبل النشر.' : 'Articles are being reviewed before publication.' }}</div>
    @else
        <div class="grid">
            @foreach($articles as $article)
                <article class="card work-card">
                    @if($article->cover_path)<img class="work-cover" src="{{ asset('storage/'.$article->cover_path) }}" alt="">@endif
                    <div class="copy"><span class="meta">{{ $article->published_at?->translatedFormat('d F Y') }}</span>@if($article->category)<a class="pill" href="{{ route('articles.category', ['locale' => $locale, 'slug' => $article->category->slug]) }}">{{ $locale === 'ar' ? $article->category->name_ar : $article->category->name_en }}</a>@endif<h2 style="font-size:23px"><a href="{{ route('article', ['locale' => $locale, 'slug' => $article->slug]) }}">{{ $locale === 'ar' ? $article->title_ar : $article->title_en }}</a></h2><p>{{ $locale === 'ar' ? $article->summary_ar : $article->summary_en }}</p><a class="button ghost" href="{{ route('article', ['locale' => $locale, 'slug' => $article->slug]) }}">{{ $locale === 'ar' ? 'اقرأ المقال ←' : 'Read article →' }}</a></div>
                </article>
            @endforeach
        </div>
        <div style="margin-top:28px">{{ $articles->links() }}</div>
    @endif
</div></section>
@endsection

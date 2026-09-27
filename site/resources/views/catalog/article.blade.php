@extends('layouts.marketing')

@section('title', $locale === 'ar' ? $article->title_ar : $article->title_en)
@php($metaDescription = $locale === 'ar' ? $article->summary_ar : $article->summary_en)

@section('content')
<article><header class="hero"><div class="shell">@if($preview ?? false)<div class="notice" role="status">{{ $locale === 'ar' ? 'معاينة خاصة للمشرف — هذا المقال قد يكون مسودة أو مجدولًا ولم يُنشر للزوار.' : 'Private admin preview — this article may be a draft or scheduled and is not yet public.' }}</div>@endif<a class="eyebrow" href="{{ route('articles', ['locale' => $locale]) }}">{{ $locale === 'ar' ? '← كل المقالات' : '← All articles' }}</a>@if($article->category)<div style="margin-top:14px">@if($preview ?? false)<span class="pill">{{ $locale === 'ar' ? $article->category->name_ar : $article->category->name_en }}</span>@else<a class="pill" href="{{ route('articles.category', ['locale' => $locale, 'slug' => $article->category->slug]) }}">{{ $locale === 'ar' ? $article->category->name_ar : $article->category->name_en }}</a>@endif</div>@endif<h1>{{ $locale === 'ar' ? $article->title_ar : $article->title_en }}</h1><p>{{ $locale === 'ar' ? $article->summary_ar : $article->summary_en }}</p><span class="small">{{ $article->published_at?->translatedFormat('d F Y') }}</span></div></header>
<div class="section shell" style="max-width:900px">@if($article->cover_path)<img class="detail-cover" src="{{ asset('storage/'.$article->cover_path) }}" alt="" style="margin-bottom:32px">@endif<div class="body-copy">{{ $locale === 'ar' ? $article->body_ar : $article->body_en }}</div></div></article>
@endsection

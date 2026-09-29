@extends('layouts.marketing')

@section('title', $page->{'title_'.$locale})

@php($metaDescription = $page->{'meta_description_'.$locale} ?: $page->{'summary_'.$locale})

@section('content')
    <section class="hero"><div class="shell">
        <span class="eyebrow">KeenGuild</span>
        <h1>{{ $page->{'title_'.$locale} }}</h1>
        @if(filled($page->{'summary_'.$locale}))<p>{{ $page->{'summary_'.$locale} }}</p>@endif
    </div></section>
    <section class="section"><div class="shell"><article class="card body-copy" style="max-width:900px;white-space:pre-line">{{ $page->{'body_'.$locale} }}</article></div></section>
@endsection

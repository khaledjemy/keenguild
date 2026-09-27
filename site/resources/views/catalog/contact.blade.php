@extends('layouts.marketing')

@section('title', $locale === 'ar' ? 'تواصل معنا' : 'Contact')

@section('content')
<section class="hero"><div class="shell"><span class="eyebrow">{{ $locale === 'ar' ? 'تواصل رسمي' : 'Official channels' }}</span><h1>{{ \App\Models\PageContent::text('contact', 'heading', $locale, $locale === 'ar' ? 'خلّينا نبدأ محادثة واضحة.' : 'Let’s start a clear conversation.') }}</h1><p>{{ \App\Models\PageContent::text('contact', 'intro', $locale, $locale === 'ar' ? 'استخدم إحدى القنوات الرسمية المنشورة أدناه. لا ترسل كلمات مرور أو بيانات دفع في رسالة أولية.' : 'Use one of the official channels below. Do not include passwords or payment details in an initial message.') }}</p></div></section>
<section class="section"><div class="shell">
    @if($channels->isEmpty())
        <div class="empty">{{ $locale === 'ar' ? 'قنوات التواصل الرسمية قيد التحقق. يمكنك الاطلاع على الخدمات والأسعار الآن.' : 'Official contact channels are being verified. You can explore services and pricing meanwhile.' }}</div>
    @else
        <div class="grid">@foreach($channels as $channel)<div class="card"><span class="eyebrow">{{ $channel->platform }}</span><h2 style="font-size:22px">{{ $channel->platform }}</h2><a class="button ghost" href="{{ $channel->publicUrl() }}" @if($channel->platform !== 'Email') target="_blank" rel="noopener noreferrer" @endif>{{ $locale === 'ar' ? 'افتح القناة ↗' : 'Open channel ↗' }}</a></div>@endforeach</div>
    @endif
</div></section>
@endsection

<!doctype html>
<html lang="{{ $locale }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    @php
        $currentRoute = request()->route()?->getName();
        $routeParameters = array_filter(['slug' => request()->route('slug'), 'type' => request()->route('type')]);
        $otherLocale = $locale === 'ar' ? 'en' : 'ar';
        $isPreview = $preview ?? false;
        $langRoute = $currentRoute ?? 'pricing';
        $selectedPackage = request()->query('package');
        $languageParameters = ['locale' => $otherLocale, ...$routeParameters];
        $pagination = in_array($currentRoute, ['articles', 'articles.category'], true) && request()->integer('page', 1) > 1
            ? ['page' => request()->integer('page')] : [];
        $languageParameters = [...$languageParameters, ...$pagination];
        if ($currentRoute === 'quote.create' && is_string($selectedPackage) && ctype_digit($selectedPackage)) {
            $languageParameters['package'] = $selectedPackage;
        }
        $defaultDescription = $locale === 'ar'
            ? 'KeenGuild: تصميم وتطوير المواقع والحلول البرمجية حول أهداف عملك.'
            : 'KeenGuild: websites and software shaped around your business goals.';
        $seo = \App\Models\SeoSetting::current();
        $branding = \Illuminate\Support\Facades\Schema::hasTable('homepage_contents')
            ? \App\Models\HomepageContent::query()->first() : null;
        $footerTagline = trim((string) (($branding?->content ?? [])['footer_tagline_'.$locale] ?? ''));
        $pageSettings = \App\Models\PageContent::forPage((string) $currentRoute);
        $managedPageTitle = trim((string) ($pageSettings?->{'heading_'.$locale} ?? ''));
        $managedPageDescription = trim((string) ($pageSettings?->{'meta_description_'.$locale} ?? ''));
        $defaultDescription = $seo && filled($seo->{'default_description_'.$locale})
            ? $seo->{'default_description_'.$locale} : $defaultDescription;
        $routeDescriptions = [
            'work' => ['ar' => 'تصفح أعمال ومشاريع KeenGuild، مع توضيح المشاريع المنفذة والأمثلة الخارجية المتاحة للتجربة.', 'en' => 'Explore KeenGuild projects and work, with client work and third-party demos clearly identified.'],
            'articles' => ['ar' => 'اقرأ مقالات KeenGuild عن تخطيط المواقع وتصميمها وتطوير المنتجات الرقمية.', 'en' => 'Read KeenGuild articles about website planning, design, and digital product development.'],
            'services' => ['ar' => 'اكتشف خدمات KeenGuild في تصميم المواقع وتطويرها وبناء المنتجات الرقمية.', 'en' => 'Explore KeenGuild services for website design, development, and digital products.'],
            'pricing' => ['ar' => 'تعرف على باقات KeenGuild وخيارات الأسعار لتصميم المواقع والمنتجات الرقمية.', 'en' => 'Compare KeenGuild packages and pricing options for websites and digital products.'],
        ];
        $pageDescription = trim($metaDescription ?? '') ?: ($managedPageDescription ?: ($routeDescriptions[$currentRoute][$locale] ?? $defaultDescription));
        $pageTitle = ($managedPageTitle ?: trim($__env->yieldContent('title'))).' — KeenGuild';
        $canonicalUrl = $currentRoute ? route($currentRoute, ['locale' => $locale, ...$routeParameters, ...$pagination]) : url()->current();
        $socialImage = $seo?->social_image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($seo->social_image_path)
            ? asset('storage/'.$seo->social_image_path) : null;
        $navigationItems = \Illuminate\Support\Facades\Schema::hasTable('navigation_items')
            ? \App\Models\NavigationItem::query()->where('published', true)->orderBy('sort_order')->orderBy('id')->get()->groupBy('location')
            : collect();
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $pageDescription }}">
    @if($isPreview || ($noindex ?? false) || $seo?->allow_indexing === false || ($currentRoute === 'quote.create' && !($enabled ?? false)))
        <meta name="robots" content="noindex,nofollow">
    @elseif($currentRoute)
        <link rel="canonical" href="{{ $canonicalUrl }}">
        <link rel="alternate" hreflang="ar" href="{{ route($currentRoute, ['locale' => 'ar', ...$routeParameters, ...$pagination]) }}">
        <link rel="alternate" hreflang="en" href="{{ route($currentRoute, ['locale' => 'en', ...$routeParameters, ...$pagination]) }}">
    @endif
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="KeenGuild">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta name="twitter:card" content="{{ $socialImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    @if($socialImage)<meta property="og:image" content="{{ $socialImage }}"><meta name="twitter:image" content="{{ $socialImage }}">@endif
    <title>{{ $pageTitle }}</title>
    <link rel="icon" href="{{ $branding?->publicImageUrl('favicon_path', 'branding') ?? asset('assets/brand/favicon-new2.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Tajawal:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{color-scheme:light;--ink:#10243a;--cyan:#12c4ea;--muted:#567086;--line:#d7e4e9;--paper:#f5f9fa}
        *{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;background:var(--paper);color:var(--ink);font-family:'Tajawal',Arial,sans-serif}html[lang="en"] body{font-family:'Manrope','Tajawal',Arial,sans-serif}a{color:inherit;text-decoration:none}button,input,textarea{font:inherit}
        .shell{width:min(1200px,calc(100% - 40px));margin-inline:auto}.nav-wrap{position:sticky;top:0;z-index:20;background:rgba(255,255,255,.94);border-bottom:1px solid var(--line);backdrop-filter:blur(16px)}
        .nav{min-height:76px;display:flex;align-items:center;gap:clamp(12px,2vw,24px)}.brand{display:inline-flex;align-items:center;gap:10px;white-space:nowrap;flex:none;font:700 18px 'Manrope',sans-serif}.brand img{width:40px;height:40px;object-fit:contain}.nav-links{display:flex;align-items:center;gap:clamp(12px,1.5vw,20px);margin-inline-start:auto;font-size:13px;font-weight:700;white-space:nowrap}.nav-links a{flex:none}.nav-links a:hover,.nav-links .current{color:#078cb0}.lang{border:1px solid var(--line);padding:8px 12px;border-radius:10px;font-size:13px;font-weight:700;flex:none}.mobile-nav{display:none}
        .hero{padding:76px 0 50px;background:radial-gradient(circle at 12% 22%,rgba(18,196,234,.15),transparent 35%),linear-gradient(135deg,#fff,#eef8fa)}.eyebrow{display:inline-block;color:#009fca;font-size:13px;font-weight:700;letter-spacing:.04em}.hero h1{font-size:clamp(38px,5vw,68px);line-height:1.17;letter-spacing:-.045em;margin:16px 0}.hero p{max-width:750px;color:#4f677d;font-size:18px;line-height:1.9;margin:0}.hero-actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:27px}.button{display:inline-flex;justify-content:center;align-items:center;min-height:43px;padding:10px 18px;border-radius:11px;font-weight:700;font-size:14px}.button.primary{background:var(--cyan);color:#06263b}.button.ghost{border:1px solid #b9dce5;background:#fff}
        .section{padding:66px 0}.section-head{display:flex;justify-content:space-between;align-items:end;gap:18px;margin-bottom:24px}.section-head h2{font-size:clamp(27px,3vw,40px);margin:6px 0}.section-head p{margin:0;color:var(--muted)}.section-head p{line-height:1.8}.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.card{background:#fff;border:1px solid var(--line);border-radius:22px;padding:27px;box-shadow:0 16px 45px rgba(16,36,58,.04)}.card.featured{background:#102d49;color:#fff;border-color:#102d49}.card h3{font-size:23px;line-height:1.35;margin:8px 0}.card p{line-height:1.75;color:var(--muted)}.card.featured p,.card.featured .meta{color:#b7d5e2}.meta{font-size:12px;color:#627d90}.price{display:block;font:700 32px 'Manrope','Tajawal',sans-serif;color:#079dc3;margin:22px 0 8px}.card.featured .price{color:#3cdafa}.small{font-size:13px;color:#627d90}.card.featured .small{color:#c1dae6}.card ul{padding-inline-start:20px;line-height:1.9}.card .button{margin-top:18px}
        .notice{background:#e9f5f8;border:1px solid #c8e7ed;border-radius:18px;padding:20px 24px;line-height:1.85;color:#36556c}.empty{padding:50px 24px;background:#fff;border:1px dashed #b9d8e2;border-radius:20px;text-align:center;color:#547087}.work-cover{width:100%;height:200px;object-fit:cover;border-radius:15px;background:#dce5ff}.work-card{padding:16px}.work-card .copy{padding:9px 8px 12px}.work-card h3{margin:4px 0 6px}.work-card p{margin:0 0 18px}.pill{display:inline-flex;padding:6px 10px;border-radius:99px;background:#e6f8fc;color:#067f9e;font-size:12px;font-weight:700}.detail-cover{width:100%;max-height:520px;object-fit:cover;border-radius:24px;background:#dce5ff}.body-copy{white-space:pre-line;line-height:2;color:#3f596f;max-width:850px}.tags{display:flex;flex-wrap:wrap;gap:8px}.footer{background:#0b1d2e;color:#d6ebf2;padding:42px 0;margin-top:40px}.footer .shell{display:flex;justify-content:space-between;gap:20px;flex-wrap:wrap}.footer small{color:#95b0bf}
        @media(max-width:850px){.grid{grid-template-columns:repeat(2,minmax(0,1fr))}.nav{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:8px 12px;padding:8px 0}.brand{grid-column:1;grid-row:1}.lang{grid-column:2;grid-row:1}.nav-links{grid-column:1/-1;grid-row:2;width:100%;min-width:0;margin:0;display:flex;justify-content:flex-start;flex-wrap:nowrap;gap:18px;overflow-x:auto;white-space:nowrap;scrollbar-width:thin;scrollbar-color:#b9dce5 transparent;padding:2px 0 8px}.nav-links a{flex:none}}@media(max-width:600px){.shell{width:min(100% - 28px,1200px)}.nav{min-height:66px;grid-template-columns:minmax(0,1fr) auto auto;gap:8px}.brand{font-size:15px;gap:5px}.brand img{width:32px;height:32px}.nav-links{display:none}.mobile-nav{display:block;grid-column:2;grid-row:1;position:relative}.mobile-nav summary{list-style:none;cursor:pointer;border:1px solid var(--line);border-radius:10px;padding:8px 10px;font-size:13px;font-weight:700;background:#fff}.mobile-nav summary::-webkit-details-marker{display:none}.mobile-nav[open] .mobile-nav-links{position:absolute;inset-inline-end:0;top:calc(100% + 12px);width:min(280px,calc(100vw - 28px));max-height:65vh;overflow-y:auto;display:grid;gap:2px;padding:8px;background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:0 18px 40px rgba(16,36,58,.2);z-index:25}.mobile-nav-links a{display:block;padding:11px 12px;border-radius:8px;font-size:14px;font-weight:700}.mobile-nav-links a:hover,.mobile-nav-links a:focus-visible,.mobile-nav-links .current{background:#e9f7fa;color:#078cb0}.lang{grid-column:3;grid-row:1}.hero{padding:46px 0}.hero h1{font-size:38px}.hero p{font-size:16px}.grid{grid-template-columns:1fr}.section{padding:44px 0}}
    </style>
</head>
<body>
<header class="nav-wrap"><nav class="nav shell" aria-label="{{ $locale === 'ar' ? 'التنقل الرئيسي' : 'Main navigation' }}">
    <a class="brand" href="{{ $locale === 'en' ? route('home.en') : route('home') }}"><img src="{{ $branding?->publicImageUrl('logo_path', 'branding') ?? asset('assets/brand/favicon-new2.png') }}" alt="">KeenGuild</a>
    <div class="nav-links">@foreach(($navigationItems->get('header') ?? collect()) as $item)@if($url = $item->publicUrl($locale))<a class="{{ request()->routeIs($item->target) ? 'current' : '' }}" href="{{ $url }}">{{ $locale === 'ar' ? $item->label_ar : $item->label_en }}</a>@endif @endforeach</div>
    <details class="mobile-nav"><summary>{{ $locale === 'ar' ? 'القائمة' : 'Menu' }} ☰</summary><div class="mobile-nav-links">@foreach(($navigationItems->get('header') ?? collect()) as $item)@if($url = $item->publicUrl($locale))<a class="{{ request()->routeIs($item->target) ? 'current' : '' }}" href="{{ $url }}">{{ $locale === 'ar' ? $item->label_ar : $item->label_en }}</a>@endif @endforeach</div></details>
    <a class="lang" href="{{ route($langRoute, $languageParameters) }}">{{ $locale === 'ar' ? 'EN' : 'عربي' }}</a>
</nav></header>
<main>@yield('content')</main>
<footer class="footer"><div class="shell"><div><strong>KeenGuild</strong><br><small>{{ $footerTagline !== '' ? $footerTagline : ($locale === 'ar' ? 'نصمم ونبني حول هدفك.' : 'We design and build around your goal.') }}</small></div><nav aria-label="{{ $locale === 'ar' ? 'روابط الموقع' : 'Site links' }}" style="display:flex;gap:16px;flex-wrap:wrap">@foreach(($navigationItems->get('footer') ?? collect()) as $item)@if($url = $item->publicUrl($locale))<a href="{{ $url }}">{{ $locale === 'ar' ? $item->label_ar : $item->label_en }}</a>@endif @endforeach @foreach(\App\Models\LegalPage::query()->publiclyVisible()->whereIn('type', ['privacy', 'terms'])->get() as $legalPage)<a href="{{ route('legal', ['locale' => $locale, 'type' => $legalPage->type]) }}">{{ $locale === 'ar' ? $legalPage->title_ar : $legalPage->title_en }}</a>@endforeach</nav><small>© {{ date('Y') }} KeenGuild</small></div></footer>
@stack('scripts')
</body>
</html>

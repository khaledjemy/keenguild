@extends('layouts.marketing')

@section('title', $locale === 'ar' ? 'الأسعار' : 'Pricing')

@section('content')
@php
    $packageRows = $services->flatMap(fn ($service) => $service->packages->map(fn ($package) => ['service' => $service, 'package' => $package]));
    $pricedRows = $packageRows->filter(fn ($row) => $row['package']->pricing_mode === 'estimate' && $row['package']->base_price_egp !== null)->values();
    $customRows = $packageRows->reject(fn ($row) => $row['package']->pricing_mode === 'estimate' && $row['package']->base_price_egp !== null)->values();
    $unpackagedServices = $services->filter(fn ($service) => $service->packages->isEmpty());
@endphp
<style>
    .pricing-page{background:#f5f9fa}.pricing-page .shell{width:min(1200px,calc(100% - 40px))}
    .pricing-hero{position:relative;overflow:hidden;background:#10243a;color:#fff;padding:64px 0 70px;isolation:isolate}
    .pricing-hero:before{content:"";position:absolute;inset:0;z-index:-1;background:radial-gradient(circle at 80% 25%,rgba(18,196,234,.22),transparent 34%),linear-gradient(120deg,transparent 58%,rgba(18,196,234,.06) 58%,rgba(18,196,234,.06) 58.1%,transparent 58.1%);background-size:auto,54px 54px}
    .pricing-hero-grid{display:grid;grid-template-columns:minmax(0,1.3fr) minmax(260px,.7fr);align-items:center;gap:60px}.pricing-hero-grid>*{min-width:0}
    .pricing-hero .eyebrow{color:#5edff9}.pricing-hero h1{font-size:clamp(40px,5.2vw,68px);line-height:1.13;letter-spacing:-.045em;margin:18px 0 20px;max-width:790px}
    .pricing-hero p{color:#c0d4df;font-size:clamp(16px,1.55vw,19px);line-height:1.85;max-width:700px;margin:0}
    .pricing-hero .button.primary{background:#12c4ea;color:#09273c}.pricing-hero .button.ghost{border-color:#638094;background:transparent;color:#fff}
    .pricing-hero-art{position:relative;min-height:270px;border:1px solid rgba(255,255,255,.2);border-radius:28px;padding:28px;background:linear-gradient(145deg,rgba(255,255,255,.1),rgba(255,255,255,.025));box-shadow:0 26px 70px rgba(0,0,0,.2)}
    .pricing-hero-art:after{content:"";position:absolute;width:155px;height:155px;right:-48px;top:-42px;border:1px solid rgba(18,196,234,.48);border-radius:50%;box-shadow:0 0 0 26px rgba(18,196,234,.065),0 0 0 52px rgba(18,196,234,.035)}
    .pricing-hero-art small{display:block;color:#8bdcf0;font-weight:700;letter-spacing:.12em;text-transform:uppercase}.pricing-hero-art strong{display:block;font:700 clamp(48px,6vw,76px)/1 'Manrope',sans-serif;margin:28px 0 8px}.pricing-hero-art p{font-size:14px;color:#bed5df;line-height:1.6}.pricing-hero-art .art-lines{display:flex;gap:9px;margin-top:32px}.pricing-hero-art .art-lines i{display:block;height:8px;flex:1;border-radius:99px;background:#12c4ea}.pricing-hero-art .art-lines i:nth-child(2){background:#62dbf5;flex:1.4}.pricing-hero-art .art-lines i:nth-child(3){background:#46647a;flex:.55}
    .pricing-main{padding:74px 0 86px}.pricing-section-heading{display:flex;align-items:end;justify-content:space-between;gap:24px;margin-bottom:28px}.pricing-section-heading h2{font-size:clamp(30px,3.7vw,46px);line-height:1.16;letter-spacing:-.035em;margin:10px 0 0}.pricing-section-heading p{max-width:370px;margin:0;color:#5d7485;line-height:1.7}
    .pricing-package-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.pricing-package{display:flex;flex-direction:column;min-width:0;background:#fff;border:1px solid #d7e4e9;border-radius:24px;padding:27px;box-shadow:0 15px 40px rgba(16,36,58,.04);transition:transform .2s,box-shadow .2s,border-color .2s}.pricing-package:hover{transform:translateY(-4px);box-shadow:0 23px 55px rgba(16,36,58,.1);border-color:#95dce9}.pricing-package:first-child{border-top:5px solid #12c4ea;padding-top:23px}
    .pricing-package-top{display:flex;align-items:center;justify-content:space-between;gap:12px}.pricing-index{font:700 14px 'Manrope',sans-serif;color:#0995b8}.pricing-type{font-size:11px;font-weight:700;color:#4b687d;background:#e9f5f8;border-radius:99px;padding:6px 10px}.pricing-package h3{font-size:clamp(21px,2vw,27px);line-height:1.22;margin:30px 0 9px}.pricing-package .package-description{font-size:14px;line-height:1.75;color:#5b7283;margin:0 0 18px;min-height:48px}.pricing-package ul{padding-inline-start:20px;margin:3px 0 18px;color:#2d5065;line-height:1.8;font-size:13px}.pricing-package .exclusions{font-size:12px;line-height:1.6;color:#667e8d;margin:0 0 20px}.pricing-package-bottom{margin-top:auto;padding-top:19px;border-top:1px solid #e1eaed}.pricing-package .price-label{display:block;color:#647e90;font-size:12px;font-weight:700}.pricing-package .price-number{display:block;margin-top:4px;font:700 clamp(29px,3vw,38px)/1.2 'Manrope','Tajawal',sans-serif;color:#0a91b5;white-space:nowrap}.pricing-package .price-number small{font:600 15px 'Tajawal',sans-serif}.pricing-package .price-note{display:block;margin-top:6px;font-size:11px;color:#698093}
    .estimate-details{margin-top:19px;border:1px solid #c5e9f0;border-radius:13px;background:#f3fbfd}.estimate-details summary{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:13px 15px;cursor:pointer;color:#087f9d;font-size:13px;font-weight:800;list-style:none}.estimate-details summary::-webkit-details-marker{display:none}.estimate-details[open] summary{border-bottom:1px solid #c5e9f0}.estimate-details summary span{font-size:18px}.estimate-form{padding:16px}.estimate-form h4{margin:0 0 12px;font-size:15px}.estimate-option{display:flex;align-items:center;gap:8px;margin:10px 0;font-size:12px;line-height:1.5}.estimate-option input[type=checkbox]{accent-color:#12c4ea}.estimate-option input[type=number]{width:56px;margin-inline-start:auto;border:1px solid #b8cfda;border-radius:8px;padding:5px}.estimate-selects{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin:16px 0}.estimate-selects label{font-size:12px}.estimate-selects select{width:100%;border:1px solid #b8cfda;border-radius:9px;background:#fff;color:#10243a;padding:8px;margin-top:5px}.estimate-output{margin-top:12px;min-height:25px;line-height:1.6;font-weight:700;color:#087e9b;white-space:pre-line;font-size:13px}.estimate-form .button{width:100%}
    .pricing-custom{background:#eaf4f7;padding:64px 0 78px;border-top:1px solid #d9e8ed}.pricing-custom-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.pricing-custom-card{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:10px 20px;align-items:start;background:#fff;border:1px solid #d9e7eb;border-radius:18px;padding:21px 23px}.pricing-custom-card small{font-weight:700;color:#0c91af;font-size:11px;letter-spacing:.08em}.pricing-custom-card h3{font-size:19px;line-height:1.25;margin:7px 0}.pricing-custom-card p{font-size:13px;color:#647b89;line-height:1.7;margin:0}.pricing-custom-card .custom-badge{padding:6px 10px;border-radius:99px;background:#edf6f8;color:#27566b;font-size:11px;font-weight:800;white-space:nowrap}.pricing-custom-card a{display:inline-flex;margin-top:10px;font-size:12px;color:#008cac;font-weight:800;text-decoration:underline;text-underline-offset:3px}.pricing-custom-note{margin-top:18px;color:#5e7483;font-size:13px;line-height:1.7}
    .pricing-method{padding:72px 0 80px;background:#fff}.pricing-method-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.pricing-step{border-left:3px solid #12c4ea;background:#f5f9fa;border-radius:0 18px 18px 0;padding:24px}.pricing-step .step-number{font:700 15px 'Manrope',sans-serif;color:#00a7ce}.pricing-step h3{font-size:20px;margin:18px 0 7px}.pricing-step p{font-size:14px;color:#5e7484;line-height:1.75;margin:0}.pricing-method .notice{margin-top:22px}
    html[dir=rtl] .pricing-step{border-left:0;border-right:3px solid #12c4ea;border-radius:18px 0 0 18px}
    @media(max-width:1000px){.pricing-hero-grid{gap:28px}.pricing-package-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:700px){.pricing-page .shell{width:calc(100% - 28px)}.pricing-hero{padding:45px 0 50px}.pricing-hero-grid{grid-template-columns:minmax(0,1fr)}.pricing-hero h1{font-size:clamp(34px,9vw,46px);overflow-wrap:anywhere}.pricing-hero-art{min-width:0;min-height:160px}.pricing-hero-art p{overflow-wrap:anywhere}.pricing-hero-art strong{margin:16px 0 4px}.pricing-hero-art .art-lines{margin-top:18px}.pricing-main,.pricing-method{padding:52px 0}.pricing-section-heading{display:block}.pricing-section-heading p{margin-top:10px}.pricing-package-grid,.pricing-custom-grid,.pricing-method-grid{grid-template-columns:1fr}.pricing-package{padding:22px}.pricing-package:first-child{padding-top:18px}.pricing-package .package-description{min-height:0}.pricing-custom{padding:48px 0}.pricing-custom-card{padding:18px}}
</style>
<div class="pricing-page">
    <section class="pricing-hero"><div class="shell pricing-hero-grid">
        <div>
            @if($preview)<div class="notice" style="margin-bottom:25px;border-color:#f3c5a3;background:#fff5ed;color:#8b4418" role="status">{{ $locale === 'ar' ? 'معاينة للمشرف فقط — تحتوي على أسعار وخيارات مسودة، وليست عرضًا منشورًا للزوار.' : 'Admin-only preview — includes draft prices and options, not a public offer.' }} <a href="{{ url('/admin/packages') }}" style="text-decoration:underline">{{ $locale === 'ar' ? 'العودة لإدارة الباقات' : 'Back to package management' }}</a></div>@endif
            <span class="eyebrow">{{ $locale === 'ar' ? 'KeenGuild / الأسعار' : 'KeenGuild / Pricing' }}</span>
            <h1>{{ \App\Models\PageContent::text('pricing', 'heading', $locale, $locale === 'ar' ? 'بداية واضحة. حلّ معمول على مقاسك.' : 'A clear start. A solution shaped for you.') }}</h1>
            <p>{{ \App\Models\PageContent::text('pricing', 'intro', $locale, $locale === 'ar' ? 'اختار نقطة البداية الأقرب لفكرتك. الأسعار استرشادية، والعرض النهائي يتحدد بعد ما نفهم الصفحات والوظائف والتكاملات المطلوبة.' : 'Choose the starting point closest to your idea. Prices are indicative; the final proposal follows a review of your pages, features and integrations.') }}</p>
            <div class="hero-actions"><a class="button primary" href="#packages">{{ $locale === 'ar' ? 'شوف باقات البداية' : 'Explore starting packages' }}</a><a class="button ghost" href="#how-pricing-works">{{ $locale === 'ar' ? 'إزاي بنحسب؟' : 'How pricing works' }}</a></div>
        </div>
        <div class="pricing-hero-art" aria-label="{{ $locale === 'ar' ? 'عدد الباقات ذات سعر بداية منشور' : 'Number of published starting-price packages' }}"><small>{{ $locale === 'ar' ? 'نقاط انطلاق' : 'Starting points' }}</small><strong>{{ $pricedRows->count() }}</strong><p>{{ $locale === 'ar' ? 'باقات بسعر بداية واضح، مع إمكانية تقدير نطاق مشروعك.' : 'Clear starting prices with an optional project-range estimate.' }}</p><div class="art-lines" aria-hidden="true"><i></i><i></i><i></i></div></div>
    </div></section>

    <section id="packages" class="pricing-main"><div class="shell">
        <div class="pricing-section-heading"><div><span class="eyebrow">01 / {{ $locale === 'ar' ? 'باقات البداية' : 'Starting packages' }}</span><h2>{{ $locale === 'ar' ? 'اختار نقطة انطلاق' : 'Find your starting point' }}</h2></div><p>{{ $locale === 'ar' ? 'الأساسيات واضحة هنا؛ تقدر تفتح الحاسبة داخل الباقة لو محتاج تقديرًا أوليًا.' : 'See what is included, then open the estimator inside a package if you need an initial range.' }}</p></div>
        @if($pricedRows->isNotEmpty())
            <div class="pricing-package-grid">
                @foreach($pricedRows as $row)
                    @php($service = $row['service']) @php($package = $row['package'])
                    <article class="pricing-package">
                        <div class="pricing-package-top"><span class="pricing-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><span class="pricing-type">{{ $locale === 'ar' ? $service->title_ar : $service->title_en }}</span></div>
                        <h3>{{ $locale === 'ar' ? $package->name_ar : $package->name_en }}</h3>
                        <p class="package-description">{{ $locale === 'ar' ? $package->description_ar : $package->description_en }}</p>
                        @php($features = $locale === 'ar' ? $package->included_features : $package->included_features_en)
                        @php($exclusions = $locale === 'ar' ? $package->excluded_costs : $package->excluded_costs_en)
                        @if($features)<ul>@foreach($features as $feature)<li>{{ $feature }}</li>@endforeach</ul>@endif
                        @if($exclusions)<p class="exclusions">{{ $locale === 'ar' ? 'خارج سعر البداية:' : 'Quoted separately:' }} {{ implode($locale === 'ar' ? '، ' : ', ', $exclusions) }}</p>@endif
                        <div class="pricing-package-bottom"><span class="price-label">{{ $locale === 'ar' ? 'يبدأ من' : 'Starting at' }}</span><strong class="price-number">{{ number_format($package->base_price_egp) }} <small>{{ $locale === 'ar' ? 'ج.م' : 'EGP' }}</small></strong><span class="price-note">{{ $locale === 'ar' ? 'سعر بداية استرشادي، وليس عرضًا نهائيًا' : 'Indicative starting price, not a final quote' }}</span></div>
                        @include('catalog.partials.estimate-form', ['package' => $package])
                        @if(!$preview && app(\App\Services\InquiryAvailability::class)->enabled($locale))<a class="button ghost" href="{{ route('quote.create', ['locale' => $locale, 'package' => $package->id]) }}">{{ $locale === 'ar' ? 'اطلب عرضًا تفصيليًا' : 'Request a detailed quote' }}</a>@endif
                    </article>
                @endforeach
            </div>
        @else
            <div class="empty">{{ $locale === 'ar' ? 'باقات البداية قيد المراجعة. تقدر تتصفح الخدمات التي تُسعَّر حسب نطاق المشروع أدناه.' : 'Starting packages are under review. Browse the services priced by project scope below.' }}</div>
        @endif
    </div></section>

    @if($customRows->isNotEmpty() || $unpackagedServices->isNotEmpty())
        <section class="pricing-custom"><div class="shell">
            <div class="pricing-section-heading"><div><span class="eyebrow">02 / {{ $locale === 'ar' ? 'حلول مخصصة' : 'Custom solutions' }}</span><h2>{{ $locale === 'ar' ? 'نحددها حسب مشروعك' : 'Scoped around your project' }}</h2></div><p>{{ $locale === 'ar' ? 'هذه الخدمات لا نضع لها سعر بداية من غير فهم تفاصيل التنفيذ.' : 'These services need a requirements review before we can price them responsibly.' }}</p></div>
            <div class="pricing-custom-grid">
                @foreach($customRows as $row)
                    @php($service = $row['service']) @php($package = $row['package'])
                    <article class="pricing-custom-card"><div><small>{{ $locale === 'ar' ? $service->title_ar : $service->title_en }}</small><h3>{{ $locale === 'ar' ? $package->name_ar : $package->name_en }}</h3><p>{{ $locale === 'ar' ? $package->description_ar : $package->description_en }}</p>@if(!$preview && $service->published)<a href="{{ route('service', ['locale' => $locale, 'slug' => $service->slug]) }}">{{ $locale === 'ar' ? 'تفاصيل الخدمة ↗' : 'Service details ↗' }}</a>@endif</div><span class="custom-badge">{{ $locale === 'ar' ? 'حسب النطاق' : 'Custom quote' }}</span></article>
                @endforeach
                @foreach($unpackagedServices as $service)
                    <article class="pricing-custom-card"><div><small>{{ strtoupper($service->category) }}</small><h3>{{ $locale === 'ar' ? $service->title_ar : $service->title_en }}</h3><p>{{ $locale === 'ar' ? $service->summary_ar : $service->summary_en }}</p>@if(!$preview && $service->published)<a href="{{ route('service', ['locale' => $locale, 'slug' => $service->slug]) }}">{{ $locale === 'ar' ? 'تفاصيل الخدمة ↗' : 'Service details ↗' }}</a>@endif</div><span class="custom-badge">{{ $locale === 'ar' ? 'حسب النطاق' : 'Custom quote' }}</span></article>
                @endforeach
            </div>
            <p class="pricing-custom-note">{{ $locale === 'ar' ? 'لا يوجد سعر بداية معتمد لهذه الخدمات حتى تُراجع المتطلبات.' : 'No starting price is approved for these services until requirements are reviewed.' }}</p>
        </div></section>
    @endif

    <section id="how-pricing-works" class="pricing-method"><div class="shell">
        <div class="pricing-section-heading"><div><span class="eyebrow">03 / {{ $locale === 'ar' ? 'طريقة التسعير' : 'The pricing method' }}</span><h2>{{ $locale === 'ar' ? 'كل رقم له سبب' : 'Every number has a reason' }}</h2></div></div>
        <div class="pricing-method-grid">
            <div class="pricing-step"><span class="step-number">01 / {{ $locale === 'ar' ? 'الأساس' : 'Base' }}</span><h3>{{ \App\Models\PageContent::extra('pricing', 'pricing_1_title', $locale, $locale === 'ar' ? 'نحدد الأساس' : 'Start with the core') }}</h3><p>{{ \App\Models\PageContent::extra('pricing', 'pricing_1_description', $locale, $locale === 'ar' ? 'نوع المنتج، عدد الصفحات الأساسية، وما يشمله التنفيذ.' : 'Product type, core pages and what is included.') }}</p></div>
            <div class="pricing-step"><span class="step-number">02 / {{ $locale === 'ar' ? 'الإضافات' : 'Extras' }}</span><h3>{{ \App\Models\PageContent::extra('pricing', 'pricing_2_title', $locale, $locale === 'ar' ? 'نضيف المطلوب فقط' : 'Add only what matters') }}</h3><p>{{ \App\Models\PageContent::extra('pricing', 'pricing_2_description', $locale, $locale === 'ar' ? 'لغات، محتوى، حجز، دفع أو تكاملات حسب الحاجة الفعلية.' : 'Languages, content, booking, payments or integrations as needed.') }}</p></div>
            <div class="pricing-step"><span class="step-number">03 / {{ $locale === 'ar' ? 'المراجعة' : 'Review' }}</span><h3>{{ \App\Models\PageContent::extra('pricing', 'pricing_3_title', $locale, $locale === 'ar' ? 'نراجع النطاق' : 'Confirm the scope') }}</h3><p>{{ \App\Models\PageContent::extra('pricing', 'pricing_3_description', $locale, $locale === 'ar' ? 'التعقيد والوقت والتكاليف الخارجية تظهر بوضوح قبل العرض النهائي.' : 'Complexity, timing and third-party costs are clear before the final proposal.') }}</p></div>
        </div>
        <div class="notice">{{ $locale === 'ar' ? 'الأسعار المعروضة، إن وُجدت، تقديرية بالجنيه المصري. الدومين والاستضافة والتراخيص ورسوم بوابات الدفع والصيانة لا تُعد مشمولة إلا إذا ورد ذلك صراحة في عرض السعر.' : 'Displayed prices, when available, are indicative in EGP. Domain, hosting, licences, payment-provider fees and maintenance are separate unless the proposal explicitly includes them.' }}</div>
    </div></section>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.estimate-form').forEach(form => form.addEventListener('submit', async event => {
    event.preventDefault();
    const output = form.querySelector('.estimate-output');
    const options = {};
    form.querySelectorAll('[data-option]:checked').forEach(input => {
        const quantity = form.querySelector(`[data-quantity="${input.dataset.option}"]`);
        options[input.dataset.option] = quantity ? Number(quantity.value) : 1;
    });
    output.textContent = @json($locale === 'ar' ? 'جاري الحساب…' : 'Calculating…');
    try {
        const response = await fetch(form.dataset.estimateUrl, {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token())},
            body: JSON.stringify({options, complexity: form.elements.complexity.value, urgency: form.elements.urgency.value}),
        });
        if (!response.ok) throw new Error('Estimate unavailable');
        const result = await response.json();
        const format = number => new Intl.NumberFormat(@json($locale === 'ar' ? 'ar-EG' : 'en-US')).format(number);
        const currency = @json($locale === 'ar' ? ' ج.م' : ' EGP');
        const details = [@json($locale === 'ar' ? 'سعر الباقة: ' : 'Base package: ') + format(result.base) + currency];
        result.extras.forEach(extra => details.push(extra.label + (extra.quantity > 1 ? ' × ' + extra.quantity : '') + ': ' + format(extra.total) + currency));
        details.push(@json($locale === 'ar' ? 'معامل التعقيد: ×' : 'Complexity: ×') + result.complexity);
        details.push(@json($locale === 'ar' ? 'معامل الاستعجال: ×' : 'Urgency: ×') + result.urgency);
        details.push(@json($locale === 'ar' ? 'الإجمالي التقديري: ' : 'Estimated total: ') + format(result.total) + currency);
        details.push(@json($locale === 'ar' ? 'النطاق الاسترشادي: ' : 'Indicative range: ') + format(result.low) + ' – ' + format(result.high) + currency);
        output.textContent = details.join('\n');
    } catch {
        output.textContent = @json($locale === 'ar' ? 'تعذّر حساب التقدير. راجع الاختيارات وحاول مرة أخرى.' : 'Could not calculate the estimate. Check your choices and try again.');
    }
}));
</script>
@endpush

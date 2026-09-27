<section id="pricing" aria-labelledby="home-pricing-title">
    <style>
        #pricing{height:auto;min-height:100dvh;overflow:visible;padding:92px 28px 40px;background:radial-gradient(circle at 85% 18%,rgba(18,196,234,.2),transparent 34%),#10243a;color:#fff}
        #pricing .pricing-inner{width:min(1160px,100%);margin:auto}
        #pricing .pricing-head{display:flex;justify-content:space-between;align-items:end;gap:28px;margin-bottom:25px}
        #pricing .pricing-head h2{font-size:clamp(2.1rem,4vw,4rem)!important;line-height:1.08!important;margin:10px 0 0}
        #pricing .pricing-head p{max-width:440px;color:#bfd2dd;line-height:1.8;margin:0}
        #pricing .pricing-cards{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:15px}
        #pricing .pricing-card{display:flex;flex-direction:column;min-height:210px;padding:22px;border:1px solid rgba(255,255,255,.2);border-radius:21px;background:rgba(255,255,255,.06)}
        #pricing .pricing-card h3{font-size:1.3rem;font-weight:800;margin:5px 0}
        #pricing .pricing-card p{color:#bbd0dc;font-size:.84rem;line-height:1.65;margin:0}
        #pricing .pricing-card strong{display:block;color:#52ddff;font-size:1.65rem;line-height:1.2;margin-top:auto;padding-top:12px}
        #pricing .pricing-card a{display:inline-flex;align-self:flex-start;color:#fff;font-weight:700;font-size:.82rem;margin-top:8px;text-decoration:underline;text-underline-offset:4px}
        #pricing .pricing-foot{display:flex;justify-content:space-between;align-items:center;gap:20px;flex-wrap:wrap;margin-top:22px;color:#b6cdd9;font-size:.8rem}
        #pricing .pricing-foot a{display:inline-flex;align-items:center;padding:10px 15px;border-radius:10px;background:#12c4ea;color:#082438;font-weight:800}
        @media(max-width:760px){#pricing{padding:80px 20px 32px}#pricing .pricing-head{display:block;margin-bottom:18px}#pricing .pricing-head p{margin-top:10px}#pricing .pricing-cards{grid-template-columns:1fr;gap:9px}#pricing .pricing-card{min-height:0;padding:14px}#pricing .pricing-card h3{margin:3px 0}#pricing .pricing-card strong{padding-top:4px;font-size:1.4rem}#pricing .pricing-card a{margin-top:4px}}
    </style>
    <div class="pricing-inner">
        <div class="pricing-head">
            @php
                $pricingHeadingAr = filled($homeCopy['pricing_heading_ar'] ?? null) && filled($homeCopy['pricing_heading_en'] ?? null) ? $homeCopy['pricing_heading_ar'] : 'وضوح في السعر، من أول خطوة.';
                $pricingHeadingEn = filled($homeCopy['pricing_heading_ar'] ?? null) && filled($homeCopy['pricing_heading_en'] ?? null) ? $homeCopy['pricing_heading_en'] : 'Clarity on price, from the start.';
                $pricingKickerAr = filled($homeCopy['pricing_kicker_ar'] ?? null) && filled($homeCopy['pricing_kicker_en'] ?? null) ? $homeCopy['pricing_kicker_ar'] : 'أسعار البداية';
                $pricingKickerEn = filled($homeCopy['pricing_kicker_ar'] ?? null) && filled($homeCopy['pricing_kicker_en'] ?? null) ? $homeCopy['pricing_kicker_en'] : 'Starting prices';
                $pricingDescriptionAr = filled($homeCopy['pricing_description_ar'] ?? null) && filled($homeCopy['pricing_description_en'] ?? null) ? $homeCopy['pricing_description_ar'] : 'اختَر نقطة البداية المناسبة، ثم نحدد التكلفة النهائية بعد فهم تفاصيل مشروعك.';
                $pricingDescriptionEn = filled($homeCopy['pricing_description_ar'] ?? null) && filled($homeCopy['pricing_description_en'] ?? null) ? $homeCopy['pricing_description_en'] : 'Choose a starting point; the final proposal follows a review of your project scope.';
            @endphp
            <div><span class="text-sm font-black text-[#52ddff]" data-home-ar="{{ $pricingKickerAr }}" data-home-en="{{ $pricingKickerEn }}">{{ $pricingKickerAr }}</span><h2 id="home-pricing-title" data-home-ar="{{ $pricingHeadingAr }}" data-home-en="{{ $pricingHeadingEn }}">{{ $pricingHeadingAr }}</h2></div>
            <p data-home-ar="{{ $pricingDescriptionAr }}" data-home-en="{{ $pricingDescriptionEn }}">{{ $pricingDescriptionAr }}</p>
        </div>
        @if($packages->isNotEmpty())
            <div class="pricing-cards">
                @foreach($packages as $package)
                    <article class="pricing-card">
                        <h3 data-home-ar="{{ $package->name_ar }}" data-home-en="{{ $package->name_en }}">{{ $package->name_ar }}</h3>
                        <p data-home-ar="{{ $package->description_ar }}" data-home-en="{{ $package->description_en }}">{{ $package->description_ar }}</p>
                        <strong><span data-home-ar="يبدأ من" data-home-en="From">يبدأ من</span> {{ number_format($package->base_price_egp) }} <span data-home-ar="ج.م" data-home-en="EGP">ج.م</span></strong>
                        <a href="{{ route('pricing', ['locale' => 'ar']) }}" data-home-route="pricing" data-home-ar="شاهد النطاق والتفاصيل ↗" data-home-en="See scope and details ↗">شاهد النطاق والتفاصيل ↗</a>
                    </article>
                @endforeach
            </div>
        @else
            <p data-home-ar="الباقات قيد المراجعة؛ تعرّف على طريقة التسعير في الصفحة المخصصة." data-home-en="Packages are under review; explore how pricing works on the pricing page.">الباقات قيد المراجعة؛ تعرّف على طريقة التسعير في الصفحة المخصصة.</p>
        @endif
        <div class="pricing-foot"><span data-home-ar="الأسعار استرشادية بالجنيه المصري وليست عرضًا ملزمًا. الدومين والاستضافة والخدمات الخارجية تُحسب منفصلة ما لم تُذكر ضمن النطاق." data-home-en="Indicative EGP prices, not a binding quote. Domain, hosting and third-party services are separate unless included in scope.">الأسعار استرشادية بالجنيه المصري وليست عرضًا ملزمًا. الدومين والاستضافة والخدمات الخارجية تُحسب منفصلة ما لم تُذكر ضمن النطاق.</span><a href="{{ route('pricing', ['locale' => 'ar']) }}" data-home-route="pricing" data-home-ar="كل الباقات وطريقة الحساب ↗" data-home-en="All packages and estimates ↗">كل الباقات وطريقة الحساب ↗</a></div>
    </div>
</section>

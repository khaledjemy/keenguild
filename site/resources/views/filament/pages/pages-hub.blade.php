<x-filament-panels::page>
    <div class="kg-pages" dir="rtl">
        <div class="kg-pages-intro">
            <div>
                <span class="kg-pages-kicker">إدارة الموقع</span>
                <h2>كل صفحات الموقع، في مكان واحد</h2>
                <p>الصفحات الحالية تحتفظ بتصميمها. يمكنك تعديل محتواها، أو إنشاء صفحة جديدة بقالب مستقل.</p>
            </div>
            <a class="kg-pages-primary" href="{{ $createPageUrl }}">+ إضافة صفحة جديدة</a>
        </div>

        <section class="kg-pages-section" aria-labelledby="kg-home-heading">
            <div class="kg-pages-section-head"><div><span class="kg-pages-step">01 / الرئيسية</span><h3 id="kg-home-heading">الصفحة الرئيسية</h3><p>نصوص الأقسام، المقدمة والفيديو؛ كل جزء له محرره الخاص.</p></div><div class="kg-pages-actions"><a class="kg-pages-preview" href="{{ route('home') }}" target="_blank" rel="noopener noreferrer">عرض AR ↗</a><a class="kg-pages-preview" href="{{ route('home.en') }}" target="_blank" rel="noopener noreferrer">عرض EN ↗</a></div></div>
            <div class="kg-pages-rows">
                @foreach($homeParts as $part)
                    <div class="kg-pages-row"><div><strong>{{ $part['label'] }}</strong><small>{{ $part['status'] }}</small></div><a href="{{ $part['edit'] }}">{{ $part['status'] === 'التصميم الحالي' || $part['status'] === 'الفيديو الحالي' ? 'تخصيص' : 'تعديل' }} ←</a></div>
                @endforeach
            </div>
        </section>

        <section class="kg-pages-section" aria-labelledby="kg-standard-heading">
            <div class="kg-pages-section-head"><div><span class="kg-pages-step">02 / صفحات أساسية</span><h3 id="kg-standard-heading">الصفحات الأساسية والقانونية</h3><p>التحرير يغيّر النصوص المحددة أو الإعداد، ولا يغيّر تصميم الصفحة.</p></div></div>
            <div class="kg-pages-rows">
                @foreach($standardPages as $page)
                    <div class="kg-pages-row">
                        <div><strong>{{ $page['label'] }}</strong><small>{{ $page['status'] }}</small></div>
                        <div class="kg-pages-actions"><a href="{{ $page['edit'] }}">{{ $page['editLabel'] }}</a>@if($page['previewAr'])<a class="kg-pages-preview" href="{{ $page['previewAr'] }}" target="_blank" rel="noopener noreferrer">AR ↗</a><a class="kg-pages-preview" href="{{ $page['previewEn'] }}" target="_blank" rel="noopener noreferrer">EN ↗</a>@endif</div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="kg-pages-section" aria-labelledby="kg-custom-heading">
            <div class="kg-pages-section-head"><div><span class="kg-pages-step">03 / صفحات جديدة</span><h3 id="kg-custom-heading">صفحات أضفتها بنفسك</h3><p>الصفحة الجديدة تبدأ مسودة، وتظهر على الموقع فقط بعد نشرها.</p></div><a class="kg-pages-preview" href="{{ $createPageUrl }}">إضافة صفحة +</a></div>
            <div class="kg-pages-rows">
                @forelse($customPages as $page)
                    <div class="kg-pages-row"><div><strong>{{ $page->title_ar }}</strong><small>/pages/{{ $page->slug }} · {{ in_array($page->id, $visibleCustomIds, true) ? 'منشورة ومكتملة' : ($page->published ? 'غير مكتملة' : 'مسودة') }}</small></div><div class="kg-pages-actions"><a href="{{ \App\Filament\Resources\CustomPages\CustomPageResource::getUrl('edit', ['record' => $page]) }}">تعديل</a><a class="kg-pages-preview" href="{{ route('preview.custom-page', ['locale' => 'ar', 'slug' => $page->slug]) }}" target="_blank" rel="noopener noreferrer">AR ↗</a><a class="kg-pages-preview" href="{{ route('preview.custom-page', ['locale' => 'en', 'slug' => $page->slug]) }}" target="_blank" rel="noopener noreferrer">EN ↗</a></div></div>
                @empty
                    <div class="kg-pages-empty">لم تُضف صفحات بعد. استخدم «إضافة صفحة جديدة» لإنشاء أول صفحة.</div>
                @endforelse
            </div>
        </section>

        @if($detailPages->isNotEmpty())
            <details class="kg-pages-section kg-pages-details"><summary>04 / صفحات يولّدها المحتوى <span>{{ $detailPages->count() }} صفحة: خدمات ومشاريع ومقالات وتصنيفات وتجارب</span></summary><div class="kg-pages-rows">
                @foreach($detailPages as $page)
                    <div class="kg-pages-row"><div><strong>{{ $page['title'] }}</strong><small>{{ $page['kind'] }} · {{ $page['status'] }}</small></div><div class="kg-pages-actions">@if($page['edit'])<a href="{{ $page['edit'] }}">تعديل المصدر</a>@endif @if($page['previewAr'])<a class="kg-pages-preview" href="{{ $page['previewAr'] }}" target="_blank" rel="noopener noreferrer">AR ↗</a><a class="kg-pages-preview" href="{{ $page['previewEn'] }}" target="_blank" rel="noopener noreferrer">EN ↗</a>@endif</div></div>
                @endforeach
            </div></details>
        @endif
    </div>
    <style>
        .kg-pages{max-width:1100px;margin:auto;display:grid;gap:18px;color:#123047;font-family:inherit}
        .kg-pages-intro{display:flex;align-items:center;justify-content:space-between;gap:24px;padding:28px 32px;border-radius:20px;background:linear-gradient(120deg,#0b2d42,#105e71);color:white}
        .kg-pages-kicker,.kg-pages-step{display:block;color:#12a9cc;font-size:12px;font-weight:800;letter-spacing:.02em}
        .kg-pages-intro .kg-pages-kicker{color:#9cecf4}
        .kg-pages-intro h2{font-size:25px;line-height:1.35;font-weight:800;margin:5px 0 7px}
        .kg-pages-intro p{font-size:14px;color:#d2e8ed;line-height:1.65;margin:0}
        .kg-pages-primary{flex:none;background:#12c4ea;color:#073045;padding:11px 17px;border-radius:10px;font-size:13px;font-weight:800;text-decoration:none}
        .kg-pages-section{overflow:hidden;background:#fff;border:1px solid #dce6ea;border-radius:18px;box-shadow:0 3px 16px rgba(11,45,66,.035)}
        .kg-pages-section-head{display:flex;align-items:center;justify-content:space-between;gap:15px;padding:20px 24px;border-bottom:1px solid #e7eff2}
        .kg-pages-section-head h3{font-size:18px;font-weight:800;margin:3px 0;color:#123047}
        .kg-pages-section-head p{color:#64798a;font-size:13px;margin:0}
        .kg-pages-rows{display:grid}
        .kg-pages-row{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:13px 24px;border-bottom:1px solid #edf2f4;min-height:69px}
        .kg-pages-row:last-child{border-bottom:0}
        .kg-pages-row strong{display:block;font-size:14px;font-weight:750;color:#123047}
        .kg-pages-row small{display:block;margin-top:3px;color:#718795;font-size:12px}
        .kg-pages-row a,.kg-pages-section-head>a{color:#0785a3;font-size:13px;font-weight:750;text-decoration:none;white-space:nowrap}
        .kg-pages-row a:hover,.kg-pages-section-head>a:hover{text-decoration:underline}
        .kg-pages-actions{display:flex;align-items:center;gap:18px;flex-wrap:wrap}
        .kg-pages-preview{color:#698394!important}
        .kg-pages-empty{padding:26px 24px;color:#718795;font-size:14px}
        .kg-pages-details summary{padding:18px 24px;cursor:pointer;font-size:15px;font-weight:800;list-style:none}
        .kg-pages-details summary::-webkit-details-marker{display:none}
        .kg-pages-details summary span{float:left;color:#718795;font-size:12px;font-weight:500}
        .kg-pages-details[open] summary{border-bottom:1px solid #e7eff2}
        .dark .kg-pages-section{background:#172633;border-color:#304a59}.dark .kg-pages-section-head,.dark .kg-pages-row,.dark .kg-pages-details[open] summary{border-color:#304a59}.dark .kg-pages-section-head h3,.dark .kg-pages-row strong,.dark .kg-pages-details summary{color:#eef7fa}
        @media(max-width:700px){.kg-pages-intro{align-items:flex-start;flex-direction:column;padding:22px}.kg-pages-intro h2{font-size:21px}.kg-pages-section-head,.kg-pages-row{padding-inline:16px}.kg-pages-row{align-items:flex-start;flex-direction:column;gap:8px}.kg-pages-actions{gap:14px}}
    </style>
</x-filament-panels::page>

@php($locale = $locale ?? 'ar')
@php($home = $home ?? false)
<style>
    .kg-external-demos{padding:28px 20px 42px;background:#f0f8fa;color:#10243a}
    .kg-external-wrap{max-width:1200px;margin:auto}
    .kg-external-head{max-width:1050px;margin-bottom:16px}
    .kg-external-head small{color:#008eaf;font-weight:800;letter-spacing:.04em}
    .kg-external-head h2{font-size:clamp(27px,2.7vw,36px);line-height:1.15;margin:5px 0}
    .kg-external-head p{color:#567184;font-size:13px;line-height:1.5;margin:0}
    .kg-external-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}
    .kg-external-card{overflow:hidden;border:1px solid #d4e6eb;border-radius:16px;background:#fff;box-shadow:0 10px 28px rgba(15,42,62,.06)}
    .kg-external-preview{height:112px;background:#102a43}
    .kg-external-preview img{display:block;width:100%;height:100%;object-fit:cover;object-position:center}
    .kg-external-copy{padding:13px 16px 15px}
    .kg-external-copy small{display:block;color:#548094;font-size:11px;font-weight:700}
    .kg-external-copy h3{font-size:18px;line-height:1.3;margin:5px 0}
    .kg-external-copy p{color:#567184;font-size:13px;line-height:1.5;min-height:39px;margin:0 0 10px}
    .kg-external-actions{display:flex;gap:12px;align-items:center;flex-wrap:wrap}.kg-external-actions a{font-weight:800;font-size:12px;color:#087f9d;text-decoration:underline;text-underline-offset:4px}
    .kg-external-actions a:first-child{padding:7px 11px;border-radius:8px;color:#fff;background:#0b8eaf;text-decoration:none}
    .kg-external-more{display:inline-block;margin-top:18px;color:#087f9d;font-size:14px;font-weight:800;text-decoration:underline;text-underline-offset:5px}
    @media(max-width:850px){.kg-external-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.kg-external-copy p{min-height:0}}
    @media(max-width:580px){.kg-external-demos{padding:24px 16px 36px}.kg-external-grid{grid-template-columns:1fr}.kg-external-head h2{font-size:26px}.kg-external-preview{height:140px}}
</style>
<section class="kg-external-demos" id="external-demos" aria-label="{{ $locale === 'ar' ? 'نماذج خارجية' : 'External demos' }}">
    <div class="kg-external-wrap">
        <div class="kg-external-head">
            <small data-home-ar="أفكار مشاريع عملية" data-home-en="Practical project ideas">{{ $locale === 'ar' ? 'أفكار مشاريع عملية' : 'Practical project ideas' }}</small>
            <h2 data-home-ar="نماذج قوية ومتنوعة تقدر تجربها" data-home-en="Explore a wider range of live projects">{{ $locale === 'ar' ? 'نماذج قوية ومتنوعة تقدر تجربها' : 'Explore a wider range of live projects' }}</h2>
            <p data-home-ar="متاجر، حجوزات، عقارات، تعليم وتحليلات: أفكار لمشاريع تجارية شائعة. هذه أمثلة خارجية من أصحابها، وليست أعمالًا نفذتها KeenGuild. بعض الوظائف قد تحتاج حسابًا لدى صاحب الديمو." data-home-en="Commerce, booking, real estate, learning and analytics: common business project ideas. These are third-party examples, not projects built by KeenGuild. Some features may require an account with the demo owner.">{{ $locale === 'ar' ? 'متاجر، حجوزات، عقارات، تعليم وتحليلات: أفكار لمشاريع تجارية شائعة. هذه أمثلة خارجية من أصحابها، وليست أعمالًا نفذتها KeenGuild. بعض الوظائف قد تحتاج حسابًا لدى صاحب الديمو.' : 'Commerce, booking, real estate, learning and analytics: common business project ideas. These are third-party examples, not projects built by KeenGuild. Some features may require an account with the demo owner.' }}</p>
        </div>
        <div class="kg-external-grid">
            @foreach($home ? array_slice(\App\Support\ExternalDemoExamples::all(), 0, 3) : \App\Support\ExternalDemoExamples::all() as $demo)
                <article class="kg-external-card" data-kind="{{ $demo['kind'] }}">
                    <div class="kg-external-preview"><img src="{{ asset($demo['image']) }}" alt="{{ $locale === 'ar' ? 'صورة توضيحية لفكرة '.$demo['title_ar'] : 'Illustration of '.$demo['title_en'].' concept' }}" width="720" height="400" loading="lazy" decoding="async"></div>
                    <div class="kg-external-copy">
                        <small>{{ $demo['author'] }} · {{ $locale === 'ar' ? 'مشروع خارجي' : 'Third-party project' }}</small>
                        <h3 data-home-ar="{{ $demo['title_ar'] }}" data-home-en="{{ $demo['title_en'] }}">{{ $locale === 'ar' ? $demo['title_ar'] : $demo['title_en'] }}</h3>
                        <p data-home-ar="{{ $demo['description_ar'] }}" data-home-en="{{ $demo['description_en'] }}">{{ $locale === 'ar' ? $demo['description_ar'] : $demo['description_en'] }}</p>
                        <div class="kg-external-actions">
                            <a href="{{ $demo['demo_url'] }}" target="_blank" rel="noopener noreferrer" data-home-ar="افتح الديمو ↗" data-home-en="Open demo ↗">{{ $locale === 'ar' ? 'افتح الديمو ↗' : 'Open demo ↗' }}</a>
                            <a href="{{ $demo['source_url'] }}" target="_blank" rel="noopener noreferrer" data-home-ar="المصدر والكود" data-home-en="Source code">{{ $locale === 'ar' ? 'المصدر والكود' : 'Source code' }}</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        @if($home)
            <a class="kg-external-more" href="{{ route('work', ['locale' => 'ar']) }}" data-home-route="work" data-home-ar="شاهد كل النماذج ←" data-home-en="Explore all demos →">{{ $locale === 'ar' ? 'شاهد كل النماذج ←' : 'Explore all demos →' }}</a>
        @endif
    </div>
</section>

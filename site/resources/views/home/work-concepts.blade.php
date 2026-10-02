<style>
  #work.work-concepts { background:#f6f8f7; }
  #work.work-concepts .work-concepts-head { display:flex; align-items:end; justify-content:space-between; gap:20px; margin-bottom:22px; }
  #work.work-concepts .work-concepts-head p { max-width:680px; color:#526b7c; line-height:1.6; }
  #work.work-concepts .work-concepts-all { display:inline-flex; align-items:center; justify-content:center; white-space:nowrap; padding:11px 16px; border-radius:999px; background:#10243a; color:#fff; font-size:.8rem; font-weight:800; }
  #work.work-concepts .work-concepts-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; }
  #work.work-concepts .work-concept { min-height:300px !important; padding:16px 16px 58px !important; border:1px solid rgba(16,36,58,.09); border-radius:22px; overflow:hidden; position:relative; box-shadow:0 14px 30px rgba(16,36,58,.07); }
  #work.work-concepts .work-concept:nth-child(1) { background:#d9e4ff !important; }
  #work.work-concepts .work-concept:nth-child(2) { background:#c3f0f5 !important; }
  #work.work-concepts .work-concept:nth-child(3) { background:#e5dcfb !important; }
  #work.work-concepts .work-concept-label { display:flex; justify-content:space-between; gap:8px; align-items:center; font-size:.68rem; font-weight:800; }
  #work.work-concepts .work-concept-label span { padding:5px 9px; border-radius:999px; background:rgba(255,255,255,.75); }
  #work.work-concepts .work-concept h3 { margin:13px 0 3px; color:#10243a; font-size:1.14rem; font-weight:900; }
  #work.work-concepts .work-concept p { margin:0; color:#36566a; font-size:.78rem; line-height:1.55; }
  #work.work-concepts .work-concept-art { height:119px; margin:14px 0 0; padding:10px; border:1px solid rgba(16,36,58,.12); border-radius:13px; background:#fff; box-shadow:0 12px 19px rgba(16,36,58,.1); display:flex; gap:7px; }
  #work.work-concepts .work-concept-art i { display:block; flex:1; border-radius:7px; background:#e9f2f6; }
  #work.work-concepts .work-concept-art.board i:nth-child(2) { background:#dceaff; }
  #work.work-concepts .work-concept-art.board i:nth-child(3) { background:#cff6ef; }
  #work.work-concepts .work-concept-art.store i:nth-child(1) { background:#ffd9c5; }
  #work.work-concepts .work-concept-art.store i:nth-child(2) { background:#d5ddff; }
  #work.work-concepts .work-concept-art.store i:nth-child(3) { background:#d9f3ed; }
  #work.work-concepts .work-concept-art.analytics { align-items:end; }
  #work.work-concepts .work-concept-art.analytics i { background:#18bdd8; }
  #work.work-concepts .work-concept-art.analytics i:nth-child(1) { height:45%; }
  #work.work-concepts .work-concept-art.analytics i:nth-child(2) { height:78%; }
  #work.work-concepts .work-concept-art.analytics i:nth-child(3) { height:59%; }
  #work.work-concepts .work-concept-art.analytics i:nth-child(4) { height:91%; }
  #work.work-concepts .work-concept-link { position:absolute; inset:auto 14px 13px; display:flex; justify-content:space-between; align-items:center; color:#10243a; font-size:.79rem; font-weight:900; text-decoration:underline; text-underline-offset:4px; }
  #work.work-concepts .work-concept-link:focus-visible,#work.work-concepts .work-concepts-all:focus-visible { outline:3px solid #08aeca; outline-offset:3px; }
  @media(max-width:820px) { #work.work-concepts .work-concepts-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } #work.work-concepts .work-concept:last-child { grid-column:1/-1; } }
  @media(max-width:580px) { #work.work-concepts .work-concepts-head { display:block; } #work.work-concepts .work-concepts-all { margin-top:14px; } #work.work-concepts .work-concepts-grid { grid-template-columns:1fr; } #work.work-concepts .work-concept:last-child { grid-column:auto; } #work.work-concepts .work-concept { min-height:270px !important; } }
</style>
<section id="work" class="work-concepts px-5 md:px-10 py-28">
  <div class="max-w-7xl mx-auto">
    <div class="work-concepts-head reveal">
      <div>
        <span class="text-sm font-black text-lime-700" data-home-ar="03 / تصورات تفاعلية" data-home-en="03 / Interactive concepts">{{ $locale === 'ar' ? '03 / تصورات تفاعلية' : '03 / Interactive concepts' }}</span>
        <h2 class="text-4xl md:text-6xl font-black tracking-tight mt-3" data-home-ar="جرّب الفكرة بنفسك." data-home-en="Try the idea yourself.">{{ $locale === 'ar' ? 'جرّب الفكرة بنفسك.' : 'Try the idea yourself.' }}</h2>
        <p class="mt-3 text-sm" data-home-ar="تجارب تصميمية ببيانات افتراضية توضح طريقة التفكير والتفاعل؛ ليست مشاريع عملاء منفذة." data-home-en="Interactive design studies with fictional data, not completed client projects.">{{ $locale === 'ar' ? 'تجارب تصميمية ببيانات افتراضية توضح طريقة التفكير والتفاعل؛ ليست مشاريع عملاء منفذة.' : 'Interactive design studies with fictional data, not completed client projects.' }}</p>
      </div>
      <a class="work-concepts-all" href="{{ route('work', ['locale' => $locale]) }}" data-home-route="work" data-home-ar="كل الأعمال والنماذج ←" data-home-en="All work and examples →">{{ $locale === 'ar' ? 'كل الأعمال والنماذج ←' : 'All work and examples →' }}</a>
    </div>
    <div class="work-concepts-grid">
      @if($concepts === [])<p data-home-ar="لا توجد نماذج تفاعلية منشورة حاليًا." data-home-en="No interactive concepts are published right now.">{{ $locale === 'ar' ? 'لا توجد نماذج تفاعلية منشورة حاليًا.' : 'No interactive concepts are published right now.' }}</p>@endif
      @foreach($concepts as $slug => $concept)
        <article class="work-concept reveal">
          <div class="work-concept-label"><span data-home-ar="نموذج تفاعلي" data-home-en="Interactive concept">{{ $locale === 'ar' ? 'نموذج تفاعلي' : 'Interactive concept' }}</span><span>{{ strtoupper($slug) }}</span></div>
          <div class="work-concept-art {{ $concept['type'] }}" aria-hidden="true"><i></i><i></i><i></i>@if($concept['type'] === 'analytics')<i></i>@endif</div>
          <h3 data-home-ar="{{ $concept['name_ar'] }}" data-home-en="{{ $concept['name_en'] }}">{{ $locale === 'ar' ? $concept['name_ar'] : $concept['name_en'] }}</h3>
          <p data-home-ar="{{ $concept['copy_ar'] }}" data-home-en="{{ $concept['copy_en'] }}">{{ $locale === 'ar' ? $concept['copy_ar'] : $concept['copy_en'] }}</p>
          <a class="work-concept-link" href="{{ route('concept.demo', ['locale' => $locale, 'slug' => $slug]) }}"><span data-home-ar="افتح التجربة" data-home-en="Open the demo">{{ $locale === 'ar' ? 'افتح التجربة' : 'Open the demo' }}</span><span aria-hidden="true">↗</span></a>
        </article>
      @endforeach
    </div>
  </div>
</section>

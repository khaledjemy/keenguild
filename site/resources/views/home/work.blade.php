<style>
    #work.home-managed article > .home-cover{margin-top:8px!important;padding:6px!important;flex:none!important}
    #work.home-managed article > .home-cover img,#work.home-managed article > .home-cover .home-cover-fallback{height:104px!important}
    #work.home-managed article > h3{font-size:1.15rem!important;line-height:1.3!important;margin-top:8px!important;overflow:hidden;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:1}
    #work.home-managed article > p{font-size:.75rem!important;line-height:1.5!important;margin-top:4px!important;overflow:hidden;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2}
    #work.home-managed article > .home-actions{font-size:.76rem!important;margin-top:8px!important}
</style>
<section id="work" class="home-managed px-5 md:px-10 py-28">
    <div class="max-w-7xl mx-auto">
        <div class="mb-14 reveal">
            <span class="text-sm font-black text-lime-700">03 / مختارات</span>
            <h2 class="text-4xl md:text-6xl font-black tracking-tight mt-4"><span data-home-ar="أعمال وأفكار" data-home-en="Work and ideas">أعمال وأفكار</span><br><span data-home-ar="تستحق التجربة." data-home-en="worth exploring.">تستحق التجربة.</span></h2>
            <p class="mt-3 text-sm text-black/50" data-home-ar="نميز بوضوح بين أعمال العملاء والتصورات والأمثلة الخارجية من أصحابها." data-home-en="Client work, design concepts and third-party examples are clearly distinguished.">نميز بوضوح بين أعمال العملاء والتصورات والأمثلة الخارجية من أصحابها.</p>
            <a href="{{ route('work', ['locale' => 'ar']) }}" data-home-route="work" class="inline-block mt-4 font-bold underline underline-offset-8" data-home-ar="تصفح كل الأعمال ↗" data-home-en="Explore all work ↗">تصفح كل الأعمال ↗</a>
        </div>
        <div class="grid md:grid-cols-12 gap-5">
            @foreach($projects as $project)
                <article class="{{ $projects->count() === 1 ? 'md:col-span-12 bg-[#d5ddff]' : ($loop->first ? 'md:col-span-7 bg-[#d5ddff]' : 'md:col-span-5 bg-lime') }} reveal rounded-3xl p-5 min-h-[420px] overflow-hidden flex flex-col">
                    <div class="flex justify-between gap-3 text-sm font-bold"><span data-home-ar="{{ $project->project_type === 'client' ? 'مشروع عميل' : ($project->project_type === 'external' ? 'مثال خارجي — ليس من أعمالنا' : 'نموذج تصميمي') }}" data-home-en="{{ $project->project_type === 'client' ? 'Client project' : ($project->project_type === 'external' ? 'Third-party example — not our work' : 'Design concept') }}">{{ $project->project_type === 'client' ? 'مشروع عميل' : ($project->project_type === 'external' ? 'مثال خارجي — ليس من أعمالنا' : 'نموذج تصميمي') }}</span>@if($project->hasLiveDemo())<span data-home-ar="ديمو متاح" data-home-en="Demo available">ديمو متاح</span>@endif</div>
                    <div class="home-cover bg-white rounded-2xl shadow-xl">
                        @if($project->coverUrl())
                            <img src="{{ $project->coverUrl() }}" alt="" loading="lazy" style="width:100%;object-fit:cover;border-radius:13px">
                        @else
                            <div class="home-cover-fallback" aria-hidden="true" style="display:flex;align-items:center;justify-content:center;background:#eef8fa;border-radius:13px"><img src="/assets/brand/favicon-new2.png" alt="" style="width:58px!important;height:58px!important;object-fit:contain"></div>
                        @endif
                    </div>
                    <h3 class="text-2xl font-black mt-5" data-home-ar="{{ $project->title_ar }}" data-home-en="{{ $project->title_en }}">{{ $project->title_ar }}</h3>
                    <p class="text-black/65 leading-7 mt-2" data-home-ar="{{ $project->summary_ar }}" data-home-en="{{ $project->summary_en }}">{{ $project->summary_ar }}</p>
                    <div class="home-actions flex flex-wrap gap-4 font-bold">
                        <a href="{{ route('project', ['locale' => 'ar', 'slug' => $project->slug]) }}" data-home-route="project" data-home-ar="تفاصيل المشروع ←" data-home-en="View project →">تفاصيل المشروع ←</a>
                        @if($project->hasLiveDemo())<a href="{{ $project->demo_url }}" target="_blank" rel="noopener noreferrer" data-home-ar="افتح الديمو ↗" data-home-en="Open demo ↗">افتح الديمو ↗</a>@endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
